<?php
namespace Phero\SwoolePool;

use Phero\System\DI;
use Phero\System\Config;
use Phero\Database\Realize\MysqlDbHelp;
use League\CLImate\CLImate;
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
            $climate->backgroundBlue()->out('已经开启swoole线程池'.$worker_id);
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

    public function _task($serv, $task_id, $from_id, $sql)
    {
        static $db_help;
        if($db_help==null){
            $db_help=new MysqlDbHelp();
        }
        $data=unserialize($sql);
        $sql=$data[0];
        $bindData=$data[1];
        $result=$db_help->queryResultArray($sql,$bindData);
        if($result){
            $serv->finish(serialize($result));
        }else{
            $serv->finish($db_help->error());
        }
    }
    public function _finish($value='')
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
