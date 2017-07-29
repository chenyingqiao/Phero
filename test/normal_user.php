<?php 
require "../vendor/autoload.php";
use PheroTest\DatabaseTest\BuildUnit\Mother;
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
 * @Last Modified time: 2017-07-29 18:01:14
 */
error_reporting(E_ALL ^ E_NOTICE);
DI::inj(DI::config,"/home/lerko/Desktop/config.php");
$mother=new Mother(["name"=>"kkk_transaction_commit"]);
$mother->start()->insert();
$mother->commit();
// CacheOperationByConfig::save("objectTest","asdfasdf");
// $cache=AbstractAdapter::createConnection("redis://127.0.0.1");
// $test=new RandomSlaveHit();
// $cache->set("teset",serialize($test));
