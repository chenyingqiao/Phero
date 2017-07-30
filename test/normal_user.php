<?php 
require "../vendor/autoload.php";
use PheroTest\DatabaseTest\BuildUnit\Mother;
use PheroTest\DatabaseTest\Unit\Marry;
use PheroTest\DatabaseTest\Unit\Parents;
use Phero\Cache\CacheOperationByConfig;
use Phero\Database\Realize\Hit\RandomSlaveHit;
use Phero\Database\Realize\SwooleMysqlDbHelp;
use Phero\System\DI;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Simple\RedisCache;
/**
 * @Author: lerko
 * @Date:   2017-07-24 10:29:09
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-07-30 16:13:23
 */
error_reporting(E_ALL ^ E_NOTICE);
DI::inj(DI::config,"/home/lerko/Desktop/config.php");
// $data=Marry::Inc()->relSelect();
// var_dump($data);
// $result=Marry::Inc([
// 	"id"=>1,
// 	"mid"=>1,
// 	"pid"=>1
// ])->relDelete();
// var_dump($result);
// var_dump(Mother::Inc()->select());
// var_dump(Parents::Inc()->select());
// $mother=new Mother(["name"=>"kkk_transaction_commit"]);
// $mother->start()->insert();
// $mother->commit();
// CacheOperationByConfig::save("objectTest","asdfasdf");
// $cache=AbstractAdapter::createConnection("redis://127.0.0.1");
// $test=new RandomSlaveHit();
// $cache->set("teset",serialize($test));
