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
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-07-26 15:25:05
 */
error_reporting(E_ALL ^ E_NOTICE);
DI::inj(DI::config,"/home/lerko/Desktop/config.php");
$data=Mother::Inc()->select();
var_dump($data);
// CacheOperationByConfig::save("objectTest","asdfasdf");
// $cache=AbstractAdapter::createConnection("redis://127.0.0.1");
// $test=new RandomSlaveHit();
// $cache->set("teset",serialize($test));
