<?php 
require "../vendor/autoload.php";
use PheroTest\DatabaseTest\BuildUnit\Mother;
use Phero\Database\Realize\MysqlDbHelp;
use Phero\Database\Realize\SwooleMysqlDbHelp;
use Phero\System\DI;
/**
 * @Author: lerko
 * @Date:   2017-07-24 10:29:09
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-07-29 16:09:48
 */
error_reporting(E_ALL ^ E_NOTICE);
DI::inj("config",dirname(__FILE__)."/PheroTest/DatabaseTest/config.php");
DI::inj(DI::dbhelp,new SwooleMysqlDbHelp());
$data=Mother::Inc()->select();
$error=Mother::lastInc()->error();

// $SwooleMysqlDbHelp=new MysqlDbHelp();
// $data=$SwooleMysqlDbHelp->queryResultArray("show tables");
var_dump($data);
var_dump($error);
