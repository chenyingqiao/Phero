<?php
namespace Phero\SwoolePool;

use League\CLImate\CLImate;
use Phero\Database\Realize\MysqlDbHelp;
use Phero\Database\Realize\SwooleMysqlDbHelp;
use Phero\System\Config;
use Phero\System\DI;
/**
 *
 */
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
        $this->_swoole_server->on('WorkerStart',function($serv, $worker_id){
            $climate=new CLImate;
            $climate->backgroundBlue()->out('已经开启swoole线程'.$worker_id);
        });
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
            echo "Task {$task_id}:链接数据库 链接hash".spl_object_hash($db_help)."\n";
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

    private function exec($serv,&$db_help,$seriData)
    {
        
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
        if($result){
            $serv->finish(serialize($result));
        }else{
            $serv->finish($db_help->error());
        }
    }


    public function _finish($serv, $data)
    {
        echo "AsyncTask Finish:Connect.PID=" . posix_getpid() . PHP_EOL;
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
        if(empty($swoole_config))
            $swoole_server = new \swoole_server("127.0.0.1",54288);
        else
            $swoole_server =  new \swoole_server($swoole_config['ip'],$swoole_config['port']);
        $swoole_server->set([
            'worker_num' => $swoole_config["worker_num"]?$swoole_config["worker_num"]:100,
            'task_worker_num' => $swoole_config["pool_num"]?$swoole_config["pool_num"]:10,
        ]);
        return $swoole_server;
    }
}
