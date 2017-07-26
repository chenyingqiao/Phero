<?php 

namespace Phero\Cache;

use Phero\Cache\Interfaces\ICache;
use Phero\System\Config;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Simple\AbstractCache;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\Cache\Simple\RedisCache;
/**
 * @Author: lerko
 * @Date:   2017-06-08 11:43:47
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-07-26 15:29:37
 */
class CacheOperationByConfig implements ICache
{
	private static $cache;
	private static function getCache(){
		$cache=Config::config("cache");
		if(strstr($cache,"redis")||strstr($cache,"memcached")){
			self::$cache=AbstractAdapter::createConnection($cache);
		}else{
			self::$cache= new FilesystemCache();
		}
	}

	public static function save($key, $data,$lt=null)
	{
		if(empty($data))return;
		if(is_object($data)||is_array($data)){
			$data=serialize($data);
		}
		if(!isset(self::$cache))
			self::getCache();
		self::$cache->set($key,$data,$lt);
	}

	public static function read($key)
	{
		if(!isset(self::$cache))
			self::getCache();
		$data=self::$cache->get($key);
		if(empty($data))return;
		$serializeAble=unserialize($data);
		if($serializeAble){
			return $serializeAble;
		}
		return $data;
	}

	public static function delete($key)
	{
		if(!isset(self::$cache))
			self::getCache();
		self::$cache->deleteItem($key);
	}
}
