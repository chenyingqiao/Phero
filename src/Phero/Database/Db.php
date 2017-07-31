<?php

namespace Phero\Database;

use Phero\Database\Model;
use Phero\Database\Realize\MysqlDbHelp;
use Phero\System\DI;

/**
 * @Author: lerko
 * @Date:   2017-06-26 14:07:22
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-07-31 08:30:24
 */
class Db
{
	private static $model_fun=["insert","update","delete","select","getError","getSql"];
	private static $dbHelp_fun=["exec","queryResultArray","query","error"];

	private static $dbhelp;
	private static $model;
	public static function __callStatic($name,$argument){
		if(empty(self::$dbhelp)){
			if(!empty(DI::get("dbhelp"))){
				self::$dbhelp=DI::get("dbhelp");
			}else{
				self::$dbhelp=new MysqlDbHelp;
			}
		}
		if(empty(self::$model)){
			self::$model=new Model();
		}
		if(in_array($name,self::$model_fun)){
			return call_user_func_array([self::$model,$name],$argument);
		}
		if(in_array($name,self::$dbHelp_fun)){
			return call_user_func_array([self::$dbhelp,$name],$argument);
		}
	}

	public static function getModel()
	{
		if(empty(self::$model)){
			self::$model=new Model();
		}
		return self::$model;
	}
}
