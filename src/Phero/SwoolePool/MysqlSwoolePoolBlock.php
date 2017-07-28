<?php
/**
 * 这个是task阻塞版本
 * @Author: lerko
 * @Date:   2017-07-27 15:07:27
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-07-28 12:14:45
 */
namespace Phero\SwoolePool;

use League\CLImate\CLImate;
use Phero\Database\Realize\MysqlDbHelp;
use Phero\Database\Realize\SwooleMysqlDbHelp;
use Phero\System\Config;
use Phero\System\DI;

class MysqlSwoolePoolBlock
{
    private $_swoole_server;
    public function __construct($config_path)
    {
        DI::inj("config",$config_path);
        $this->_swoole_server=$this->_get_swoole_server_by_config();
    }

    public function start()
    {
        $this->_swoole_server->on("Receive",[$this,"_receive"]);
        $this->_swoole_server->on("Task",[$this,"_task"]);
        $this->_swoole_server->on("Finish",[$this,"_finish"]);
        $this->_swoole_server->start();
    }

    public function _receive($serv, $fd, $from_id, $data)
    {
        $result=$serv->taskwait($data);
        if($result!==false){
            $serv->send($fd,$result);
        }else{
            $serv->send($fd,"error");
        }
    }

    public function _task($serv, $task_id, $from_id, $seriData)
    {
        static $db_help;
        if($db_help==null){
            $db_help=new MysqlDbHelp();
            if(Config::config("debug"))
                echo "Task {$task_id}:链接数据库 链接hash".spl_object_hash($db_help->getDbConn())."\n";
        }else{
            if(Config::config("debug"))
                echo "命中Task{$task_id} 链接".spl_object_hash($db_help->getDbConn())."\n";
        }
        $seriData=unserialize($seriData);
        switch ($seriData[0]) {
            case SwooleMysqlDbHelp::Select:
                    $this->arraySelect($serv,$db_help,$seriData);
                break;
            case SwooleMysqlDbHelp::Exec:
                    $this->exec($serv,$db_help,$seriData);
                break;
        }
    }

    /**
     * 连接词执行
     * @Author   Lerko
     * @DateTime 2017-07-26T16:57:10+0800
     * @param    [type]                   $serv     [description]
     * @param    [type]                   &$db_help [description]
     * @param    [type]                   $seriData [description]
     * @return   [type]                             [description]
     */
    private function exec($serv,&$db_help,$seriData)
    {
        $sql=$seriData[1];
        $bindData=$seriData[2];
        $result=$db_help->exec($sql,$bindData);
        if($result){
            if(Config::config("debug"))
                var_dump($result);
            $serv->finish(serialize($result));
        }else{
            if(Config::config("debug"))
                var_dump($db_help->error());
            $serv->finish($db_help->error());
        }
    }

    /**
     * 连接池查询
     * @Author   Lerko
     * @DateTime 2017-07-19T15:15:23+0800
     * @param    [type]                   $serv     [description]
     * @param    [type]                   &$db_help [description]
     * @param    [type]                   $seriData [description]
     * @return   [type]                             [description]
     */
    private function arraySelect($serv,&$db_help,$seriData)
    {
        $sql=$seriData[1];
        $bindData=$seriData[2];
        $result=$db_help->queryResultArray($sql,$bindData);
        if($result!==0){
            if(Config::config("debug"))
                var_dump($result);
            $serv->finish(serialize($result));
        }else{
            if(Config::config("debug"))
                var_dump($db_help->error());
            $serv->finish($db_help->error());
        }
    }


    public function _finish($serv, $data)
    {
        if(Config::config("debug"))
            echo "AsyncTask Finish:Connect.PID=" . posix_getpid() . PHP_EOL;
    }
    /**
     * {
     *  ip:"ip"
     *  port:"端口"
     *  worker_num_block:"工作线程数量"//task阻塞版本
     *  pool_num_block:"连接池数量"//task阻塞版本
     * }
     * @method _get_swoole_server_by_config
     * @return [type]                       [description]
     */
    private function _get_swoole_server_by_config()
    {
        $swoole_config=Config::config("swoole");
        $ip=isset($swoole_config["ip"])?$swoole_config["ip"]:"127.0.0.1";
        $port=isset($swoole_config["port"])?$swoole_config["port"]:54288;
        $swoole_server =  new \swoole_server($ip,$port);
        $swoole_server->set([
            'worker_num' => isset($swoole_config["worker_num_block"])?$swoole_config["worker_num_block"]:100,
            'task_worker_num' => isset($swoole_config["pool_num_block"])?$swoole_config["pool_num_block"]:10,
        ]);
        return $swoole_server;
    }
}
