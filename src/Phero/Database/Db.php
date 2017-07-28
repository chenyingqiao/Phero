<?php 

namespace Phero\Database;

use Phero\Database\Model;
use Phero\Database\Realize\MysqlDbHelp;

/**
 * @Author: lerko
 * @Date:   2017-06-26 14:07:22
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-07-27 18:15:08
 */
class Db
{
	private static $model_fun=["insert","update","delete","select"];
	private static $dbHelp_fun=["exec","queryResultArray","query"];

	private static $dbhelp;
	public static function __callStatic($name,$argument){
		self::injectStatic();
		if(!empty(DI::get("dbhelp"))){
			self::$dbhelp=DI::get("dbhelp");
		}else{
			self::$dbhelp=new MysqlDbHelp;
		}
		if(in_array($name,self::$model_fun)){
			return call_user_func_array([new Model,$name],$argument);
		}
		if(in_array($name,self::$dbHelp_fun)){
			return call_user_func_array([self::$dbhelp,$name],$argument);
		}
	}
}