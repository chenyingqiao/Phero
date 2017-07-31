<?php 
require "../vendor/autoload.php";
use PheroTest\DatabaseTest\Unit\Children;
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
 * @Last Modified time: 2017-07-31 18:37:58
 */
error_reporting(E_ALL ^ E_NOTICE);
DI::inj(DI::config,"/home/lerko/Desktop/config.php");
Mother::Inc(["name"=>"test"])->insert();
$data=Mother::Inc()->select();
var_dump($data);

