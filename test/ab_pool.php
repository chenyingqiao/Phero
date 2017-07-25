<?php 
require "../vendor/autoload.php";
use Phero\Database\Realize\SwooleMysqlDbHelp;
use Phero\System\DI;
/**
 * @Author: lerko
 * @Date:   2017-07-24 10:29:09
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-07-24 17:21:13
 */
error_reporting(E_ALL ^ E_NOTICE);
DI::inj(DI::config,"/home/lerko/Desktop/config.php");
// DI::inj(DI::dbhelp,new SwooleMysqlDbHelp());
$SwooleMysqlDbHelp=new SwooleMysqlDbHelp();
$data=$SwooleMysqlDbHelp->queryResultArray("show tables");
var_dump($data);