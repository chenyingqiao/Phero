<?php
namespace Phero\Database\Realize;

use Phero\Database as database;
use Phero\Database\Interfaces as interfaces;
use Phero\System\Traint as sys_traint;

/**
 * 数据库
 */
class MysqlDbHelp implements interfaces\IDbHelp {
	use sys_traint\TInject;

	/**
	 * @Inject[di=pdo_instance]
	 * @var [type]
	 */
	protected $pdo;

	private $mode, $classname, $ctorargs;

	private $error;

	// public function __construct() {
	// $dns = "mysql:host=localhost;dbname=video;charset=utf8";
	//$dns="mysql:host=localhost;3306;dbname=video";
	// $this->pdo = new \PDO($dns, "root", "Cyq19931115");
	// }
	/**
	 * 返回影响的行数
	 * @param  [type] $sql  [PDOStatement对象或者是sql语句]
	 * @param  array  $data [绑定的数据]
	 * @return [type]       [返回影响的行数]
	 */
	public function exec($sql, $data = []) {
		$data = $data == null ? [] : $data;
		if (is_string($sql)) {
			$sql = $this->pdo->prepare($sql);
			foreach ($data as $key => $value) {
				$sql->bindValue($value[0], $value[1], $value[2]);
			}
			$sql->execute();
			$this->errorMessage($sql);
		} else {
			if ($sql instanceof \PDOStatement) {
				$this->PDOStatementFactory($sql);
				foreach ($data as $key => $value) {
					$sql->bindValue($value[0], $value[1], $value[2]);
				}
				$sql->execute();
				$this->errorMessage($sql);
			} else {
				return 0;
			}
		}
		return $sql->rowCount();
	}
	/**
	 * 返回结果集
	 * @param  [type] $sql  [PDOStatement对象或者是sql语句]
	 * @param  array  $data [绑定的数据]
	 * @return [type]       [返回结果集]
	 */
	public function query($sql, $data = []) {
		$data = $data == null ? [] : $data;
		if (is_string($sql)) {
			$sql = $this->pdo->prepare($sql);
			foreach ($data as $key => $value) {
				$sql->bindValue($value[0], $value[1], $value[2]);
			}
			$sql->execute();
			$this->errorMessage($sql);
		} else {
			if ($sql instanceof \PDOStatement) {
				$this->PDOStatementFactory($sql);
				foreach ($data as $key => $value) {
					$sql->bindValue($value[0], $value[1], $value[2]);
				}
				$sql->execute();
				$this->errorMessage($sql);
			} else {
				return array();
			}
		}
		return $sql;
	}
	public function PDOStatementFactory(&$PDOStatement) {
		if ($this->mode != database\Model::fetch_obj) {
			$PDOStatement->setFetchMode($this->mode);
		}
		if (!empty($this->mode) && !empty($this->classname) && $this->mode == database\Model::fetch_obj) {
			$PDOStatement->setFetchMode($this->mode, $this->classname);
		}
		$this->mode = null;
		$this->classname = null;
	}
	/**
	 * 设置遍历模式
	 * @param [type] $mode      [description]
	 * @param [type] $classname [指定FETCH_CLASS遍历模型对应的生成类]
	 */
	public function setFetchMode($mode, $classname = null) {
		$this->mode = $mode;
		$this->classname = $classname;
		return $this;
	}
	public function error() {
		return $this->error;
	}
	private function errorMessage($state) {
		$info = $state->errorInfo();
		$this->error = "[error:code]:" . $state->errorCode() . "[error:info]:";
		foreach ($info as $key => $value) {
			$this->error .= $value . "	";
		}
	}
	public function getDbConn() {
		return $this->pdo;
	}
}