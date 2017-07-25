<?php 

use PheroTest\DatabaseTest\BaseTest;
use Phero\Cache\CacheOperationByConfig;
use Phero\Database\Enum\Cache;
use Phero\System\Config;
use Phero\System\DI;
use Symfony\Component\Cache\Simple\RedisCache;
/**
 * @Author: lerko
 * @Date:   2017-06-08 14:33:17
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-07-24 16:03:04
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

	/**
	 * 可变参数设置
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-07-04T13:59:54+0800
	 * @param    string                   $value [description]
	 * @return   [type]                          [description]
	 */
	public function variadicfuncCall($value='')
	{
		$this->Variadic("haha","woqu","你好");
		$arg=["1","2",3];
		$this->unpacking(...$arg);
	}

	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-07-24T16:01:44+0800
	 */
	public function Di()
	{
		echo DI::pdo_instance;
	}

	private function Variadic(...$arg){
		var_dump($arg);
	}

	private function unpacking($arg){
		var_dump($arg);
		var_dump(func_get_args());
	}
}
