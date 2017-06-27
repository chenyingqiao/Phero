<?php 

namespace Phero\Database;

use Phero\Database\Model;
use Phero\Database\Realize\MysqlDbHelp;
/**
 * @Author: lerko
 * @Date:   2017-06-26 14:07:22
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-27 12:10:23
 */
class Db
{
	private $model_fun=["insert","update","delete","select"];
	private $dbHelp_fun=["exec","queryResultArray","query"];
	public static function __callStatic($name,$argument){
		if(in_array($name,$model_fun)){
			call_user_func_array([new Model,$name],$argument);
		}
		if(in_array($name,$dbHelp_fun)){
			call_user_func_array([new MysqlDbHelp,$name],$argument);
		}
	}
}