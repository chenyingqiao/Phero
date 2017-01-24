<?php
namespace Phero\Database;

use Phero\Database\Interfaces as interfaces;
use Phero\Database\Realize as realize;

/**
 *
 */
class Model implements interfaces\IModel {

	const fetch_arr_number = \PDO::FETCH_NUM; //只有数值键
	const fetch_arr_key = \PDO::FETCH_ASSOC; //只有文本键
	const fetch_arr_numberAkey = \PDO::FETCH_BOTH; //返回的是数值键和文本键都有
	const fetch_obj = \PDO::FETCH_CLASS; //返回结果是类集合

	const begin_transaction = 1;
	const rollback_transaction = 2;
	const commit_transaction = 3;

	private $mode = self::fetch_arr_key, $classname = "Phero\\Database\\DbUnit";

	protected $help;

	protected $IConstraintBuild;

	private $sql, $error;

	public function __construct($dns = null, $username = null, $password = null) {
		$this->help = new realize\MysqlDbHelp();
		$this->IConstraintBuild = new realize\MysqlConstraintBuild($dns, $username, $password);
	}

	public function insert($Entiy, $is_replace = false) {
		$sql = $this->IConstraintBuild->buildInsertSql($Entiy, $is_replace);
		$this->sql = $sql;
		$return = $this->help->exec($sql, $this->IConstraintBuild->getBindData());
		return $return;
	}
	/**
	 * 聚合语句count sun avg等
	 * group by
	 * order by
	 * 函数使用
	 * having
	 * 子查询
	 * 表链接
	 * @param  [type] $entiy [description]
	 * @param  [type] $yield [description]
	 * @return [type]        [description]
	 */
	public function select($Entiy, $yield = false) {
		$sql = $this->IConstraintBuild->buildSelectSql($Entiy);
		$this->sql = $sql;
		$this->help->setEntiy($Entiy);
		if ($yield) {
			$data = $this->help->setFetchMode($this->mode, $this->classname)->query($sql, $this->IConstraintBuild->getBindData());
		} else {
			$data = $this->help->setFetchMode($this->mode, $this->classname)->queryResultArray($sql, $this->IConstraintBuild->getBindData());
		}
		return $data;
	}
	public function update($Entiy) {
		$sql = $this->IConstraintBuild->buildUpdataSql($Entiy);
		// var_dump($sql);
		$this->sql = $sql;
		$return = $this->help->exec($sql, $this->IConstraintBuild->getBindData());
		return $return;
	}
	public function delete($Entiy) {
		$sql = $this->IConstraintBuild->buildDeleteSql($Entiy);
		// var_dump($sql);
		$this->sql = $sql;
		$effect_rows_num = $this->help->exec($sql, $this->IConstraintBuild->getBindData());
		return $effect_rows_num;
	}

	/**
	 * 遍历数据的模式
	 * @param [type] $mode      [description]
	 * @param [type] $classname [description]
	 */
	public function setFetchMode($mode, $classname = null) {
		$this->mode = $mode;
		$this->classname = empty($classname) ? $this->classname : $classname;
		return $this;
	}

	public function getFetchMode() {
		return $this->mode;
	}

	public function getSql() {
		return $this->sql;
	}

	/**
	 * 不同的事务类型
	 * @param  [type] $type [description]
	 * @return [type]       [description]
	 */
	public function transaction($type) {
		$pdo = $this->help->getDbConn();
		if ($type == self::begin_transaction) {
			if ($pdo->inTransaction()) {
				if (!(get_class($pdo) == "Phero\Database\PDO")) {
					throw new \Exception("原生pdo类不支持事务嵌套", 1);
				}
			}
			$pdo->beginTransaction();
		} elseif ($type == self::rollback_transaction) {
			$pdo->rollBack();
		} elseif ($type == self::commit_transaction) {
			$pdo->commit();
		}
	}
	/**
	 * 取得pdo
	 * @return [type] [description]
	 */
	public function getPdo() {
		return $this->help->getDbConn();
	}

	public function getPdoDriverType() {
		$pdo = $this->help->getDbConn();
		return $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
	}
	public function getError() {
		return $this->help->error();
	}

	/**
	 * @param $Entiy 实体类
	 * @return array 返回sql对应的bindValue数据
	 */
	public function fetchSql($Entiy) {
		// TODO: Implement fetchSql() method.
		$sql = $this->IConstraintBuild->buildSelectSql($Entiy);
		$this->sql = $sql;
		return $this->IConstraintBuild->getBindData();
	}
}