<?php 

namespace Phero\Database;

use Phero\Database\Model;
use Phero\Database\Realize\MysqlDbHelp;
/**
 * @Author: lerko
 * @Date:   2017-06-26 14:07:22
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-27 14:26:28
 */
class Db
{
	private static $model_fun=["insert","update","delete","select"];
	private static $dbHelp_fun=["exec","queryResultArray","query"];
	public static function __callStatic($name,$argument){
		if(in_array($name,self::$model_fun)){
			return call_user_func_array([new Model,$name],$argument);
		}
		if(in_array($name,self::$dbHelp_fun)){
			return call_user_func_array([new MysqlDbHelp,$name],$argument);
		}
	}
}