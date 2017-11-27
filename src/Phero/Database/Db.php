<?php

namespace Phero\Database;

use Phero\Database\Model;
use Phero\Database\Realize\MysqlDbHelp;
use Phero\System\DI;

/**
 * @Author: lerko
 * @Date:   2017-06-26 14:07:22
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-11-27 22:45:57
 */
class Db {
	private $model_fun = ["insert", "update", "delete", "select", "getError", "getSql"];
	private $dbHelp_fun = ["exec", "queryResultArray", "query", "error"];

	private $dbhelp;
	private $model;
	private $db_connect;

	private function __construct($db_connect = "database") {
		$this->db_connect = $db_connect;
	}

	/**
	 * 保持多个Db实例并且不同链接
	 * @var array
	 */
	private static $dbs = [];

	public static function getInctance($db_connect = "database") {
		if (!isset(self::$dbs[$db_connect]) && empty(self::$dbs[$db_connect])) {
			self::$dbs[$db_connect] = new Db($db_connect);
		}
		echo spl_object_hash(self::$dbs[$db_connect])."\n";
		echo $db_connect."\n";
		return self::$dbs[$db_connect];
	}

	public static function __callStatic($name, $argument) {
		$name = explode("_", $name);
		if (count($name) != 2) {
			$db = Db::getInctance();
			return $db->call($name,$argument);
		} else {
			$db = Db::getInctance($name[0]);
			return $db->call($name[1],$argument);
		}
	}

	public function __call($name, $argument){
		return $this->call($name,$argument);
	}

	/**
	 * 调用dbhelp的方法
	 * @Author   Lerko
	 * @DateTime 2017-11-27T22:28:40+0800
	 * @param    [type]                   $name     方法名称
	 * @param    [type]                   $argument [description]
	 * @return   [type]                             [description]
	 */
	private function call($name, $argument) {
		if (empty($this->model)) {
			$this->model = new Model($this->db_connect);
		}
		if (in_array($name, $this->model_fun)) {
			return call_user_func_array([$this->model, $name], $argument);
		}
		if (in_array($name, $this->dbHelp_fun)) {
			return call_user_func_array([$this->model->getHelp(), $name], $argument);
		}
	}

	public static function getModel() {
		if (empty($this->model)) {
			$this->model = new Model($this->db_connect);
		}
		return $this->model;
	}
}
