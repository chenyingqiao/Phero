<?php 
require "../vendor/autoload.php";
use PheroTest\DatabaseTest\Unit\Marry;
use PheroTest\DatabaseTest\Unit\Mother;
use PheroTest\DatabaseTest\Unit\MotherInfo;
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
 * @Last Modified time: 2017-07-31 11:56:10
 */
error_reporting(E_ALL ^ E_NOTICE);
DI::inj(DI::config,"/home/lerko/Desktop/config.php");
$marry=new Marry;
$marry->id=2;
$marry->mid=2;
$marry->pid=2;
$marry->parent=Parents::Inc(["id"=>2,"name"=>"this is update"]);
$marry->mother=Mother::Inc(["id"=>2,"name"=>"this is update"]);
$marry->motherInfo=MotherInfo::Inc(["mid"=>2,"email"=>"this is update"]);
$marry->relUpdate();
