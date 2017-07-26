<?php 
require "../vendor/autoload.php";
use PheroTest\DatabaseTest\BuildUnit\Mother;
use Phero\Database\Realize\MysqlDbHelp;
use Phero\System\DI;
/**
 * @Author: lerko
 * @Date:   2017-07-24 10:29:09
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-07-26 17:50:36
 */
error_reporting(E_ALL ^ E_NOTICE);
DI::inj("config","/home/lerko/Desktop/config.php");
$data=Mother::Inc()->select();
$error=Mother::lastInc()->error();

// $SwooleMysqlDbHelp=new MysqlDbHelp();
// $data=$SwooleMysqlDbHelp->queryResultArray("show tables");
var_dump($data);
var_dump($error);
