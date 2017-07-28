<?php
/**
 * task非阻塞性 可以创建较多的task进程
 * @Author: lerko
 * @Date:   2017-07-27 16:00:35
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-07-28 13:27:41
 */

namespace Phero\SwoolePool;

use League\CLImate\CLImate;
use Phero\Database\Realize\MysqlDbHelp;
use Phero\Database\Realize\SwooleMysqlDbHelp;
use Phero\System\Config;
use Phero\System\DI;
use Phero\System\Tool;


class MysqlSwoolePool
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
        $data=unserialize($data);
        $data['fd']=$fd;
        $result=$serv->task($data);
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
        if($result>0){
            $exec_result=[
                    "data"=>$result,
                    md5("fd")=>$seriData['fd']
                ];
            $serv->finish($exec_result);
        }else{
            $exec_result=[
                    "data"=>$db_help->error(),
                    md5("fd")=>$seriData['fd']
                ];
            $serv->finish($exec_result);
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
            $data=[
                "data"=>$result,
                md5("fd")=>$seriData['fd']
            ];
            $serv->finish($data);
        }else{
            $data=[
                md5("fd")=>$seriData['fd'],
                "data"=>$db_help->error()
            ];
            $serv->finish($data);
        }
    }

    public function _finish($serv,$task_id, $data)
    {
        if(Config::config("debug"))
            echo "AsyncTask Finish:Connect.PID=" . posix_getpid() . PHP_EOL;
        $serv->send($data[md5("fd")],serialize($data['data']));
    }


    /**
     * {
     *  ip:"ip"
     *  port:"端口"
     *  worker_num:"工作线程数量"
     *  pool_num:"连接池数量"
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
            'worker_num' => isset($swoole_config["worker_num"])?$swoole_config["worker_num"]:2,
            'task_worker_num' => isset($swoole_config["pool_num"])?$swoole_config["pool_num"]:20,
        ]);
        return $swoole_server;
    }
}
