<?php
namespace Phero\Database\Realize;

use Phero\Database\Enum\RelType;
use Phero\Database\Interfaces\IDbHelp;
use Phero\Database\Realize\MysqlDbHelp;
use Phero\System\Config;
/**
 *
 */
class SwooleMysqlDbHelp implements IDbHelp
{
    CONST Select=1;
    CONST Exec=2;
    private $error="";

    public function exec($sql, $data=[],$type=RelType::insert)
    {
        $client = $this->_get_swoole_client();
        $client->send(serialize([self::Exec,$sql,$data]));
        $recv=$client->recv();
        $data=unserialize($recv);
        if($data===false){
            $this->error=$recv;
            return 0;
        }
        $client->close();
        return $data;
    }

    /**
     * @hit
     */
	public function queryResultArray($sql, $data=[]){
        $client = $this->_get_swoole_client();
        $client->send(serialize([self::Select,$sql,$data]));
        $recv=$client->recv();
        $data=unserialize($recv);
        if($data===false){
            $this->error=$recv;
            return 0;
        }
        $client->close();
        return $data;
    }

    public function query($sql, $data=[])
    {
        return $this->queryResultArray($sql,$data);
    }

    /**
     * 获取一个根据配置的swoole客户端
     * @method _get_swoole_client
     * @param  string             $value [description]
     * @return [type]                    [description]
     */
    private function _get_swoole_client()
    {
        $swoole_config=Config::config("swoole");
        $swoole_client = new \swoole_client(SWOOLE_SOCK_TCP);
        if(!empty($swoole_config)&&isset($swoole_config['ip'])&&isset($swoole_config['port']))
            $connect=$swoole_client->connect($swoole_config['ip'],$swoole_config['port'],-1);
        else{
            $connect=$swoole_client->connect('127.0.0.1',54288,-1);
        }
        if(!$connect){
            throw new \Exception("Swoole connection exception! Check your swoole mysql connect pool!", 1);
        }
        return $swoole_client;
    }

    public function setEntiy(&$entiy)
    {
        return $this;
    }

    public function getDbConn()
    {
        return $this;
    }


    public function error()
    {
        return $this->error;
    }

    public function setFetchMode($mode, $classname = null)
    {
        return $this;
    }

    public function transaction($type)
    {
    }

    public function disconnect(&$pdo)
    {
        
    }
}
