<?php 

use PheroTest\DatabaseTest\BaseTest;
use Phero\Cache\CacheOperationByConfig;
use Phero\Database\Enum\Cache;
use Phero\System\Config;
use Symfony\Component\Cache\Simple\RedisCache;
/**
 * @Author: lerko
 * @Date:   2017-06-08 14:33:17
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-27 15:29:03
 */
class ConfigTest extends BaseTest
{
	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-06-08T14:34:01+0800
	 * @param    string                   $value [description]
	 * @return   [type]                          [description]
	 */
	public function configSet()
	{
		Config::config("debug",false);
		$this->assertEquals(Config::config("debug"), false);
	}

	public function testArrCache(){
		$data=["aaa"=>2,"232"=>time()];
		CacheOperationByConfig::save("ying",$data,10);
	}

	public function testArrayCacheOnRedisCache(){
		$data=["aaa"=>2,"232"=>time()];
		$redis=RedisCache::createConnection('redis://127.0.0.1');
		$redis->set("RedisArrayTest",$data);
	}
}