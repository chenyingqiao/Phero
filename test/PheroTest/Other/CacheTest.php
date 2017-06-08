<?php

namespace PheroTest\Other;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Mother;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\Cache\Simple\RedisCache;
/**
 * @Author: lerko
 * @Date:   2017-06-08 09:35:38
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-08 12:12:51
 */
class CacheTest extends BaseTest
{
    /**
     * 内存共享区域的文件存储
     * @Author   Lerko
     * @DateTime 2017-06-08T09:36:56+0800
     * @return   [type]                   [description]
     */
    public function saveFileSystemCache(){
    	$this->timer(true);
        $cache= new FilesystemCache('', 0,"/dev/shm/cache/");
        for ($i=0; $i < 100000; $i++) { 
	    	$value="{$i}";
	    	$cache->set("test.one{$i}",$value);
        }
    	$this->assertEquals($cache->get("test.one0"), 0);
    	$this->timer(false,"saveFileSystemCache耗时：");
    }

    /**
     * 普通磁盘文件存储
     * @Author   Lerko
     * @DateTime 2017-06-08T10:00:31+0800
     * @return   [type]                   [description]
     */
    public function saveFileSystemCacheInDisk(){
    	$this->timer(true);
        $cache= new FilesystemCache();
        for ($i=0; $i < 100000; $i++) {
	    	$value="{$i}";
	    	$cache->set("test.one{$i}",$value);
        }
    	$this->assertEquals($cache->get("test.one0"), 0);
    	$this->timer(false,"saveFileSystemCacheInDisk耗时：");
    }

    /**
     * @Author   Lerko
     * @DateTime 2017-06-08T10:40:34+0800
     * @return   [type]                   [description]
     */
    public function saveMemcacheCache(){
    	$this->timer(true);
    	$cache=AbstractAdapter::createConnection('memcached://127.0.0.1');
        for ($i=0; $i < 100000; $i++) {
	    	$value="{$i}";
	    	$cache->set("test.one{$i}",$value);
        }
    	$this->assertEquals($cache->get("test.one0"), 0);
    	$this->timer(false,"saveMemcacheCache耗时：");
    }

    /**
     * @Author   Lerko
     * @DateTime 2017-06-08T10:40:43+0800
     * @return   [type]                   [description]
     */
    public function saveRedisCache(){
    	$this->timer(true);
    	$cache=RedisCache::createConnection('redis://127.0.0.1');
        for ($i=0; $i < 100000; $i++) {
	    	$value="{$i}";
	    	$cache->set("test.one{$i}",$value);
        }
    	$this->assertEquals($cache->get("test.one0"), 0);
    	$this->timer(false,"saveRedisCache耗时：");
    }

    /**
     * @test
     * @Author   Lerko
     * @DateTime 2017-06-08T12:09:13+0800
     * @return   [type]                   [description]
     */
    public function saveFileSystemCacheObj(){
    	$cache= new FilesystemCache();
    	$cache->set("aaa",Mother::Inc());
    	var_dump($cache->get("aaa"));
    	$this->assertEquals($cache->get("aaa"),Mother::lastInc());
    }
}