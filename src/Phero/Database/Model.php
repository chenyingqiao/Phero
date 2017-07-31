<?php
namespace Phero\Database;

use Phero\Database\Enum\FetchType;
use Phero\Database\Enum\RelType;
use Phero\Database\Interfaces as interfaces;
use Phero\Database\Realize as realize;
use Phero\System\Tool;
use Phero\System\Traits\TInject;

/**
 *
 */
class Model implements interfaces\IModel {
	use TInject;

	const fetch_arr_number = \PDO::FETCH_NUM; //只有数值键
	const fetch_arr_key = \PDO::FETCH_ASSOC; //只有文本键
	const fetch_arr_numberAkey = \PDO::FETCH_BOTH; //返回的是数值键和文本键都有
	const fetch_obj = \PDO::FETCH_CLASS; //返回结果是类集合

	//obj无法使用
	private $mode = self::fetch_arr_key, $classname = "Phero\\Database\\DbUnit";

	/**
	 * @Inject[di=dbhelp]
	 * @var [type]
	 */
	protected $help;

	protected $IConstraitBuild;

	private $sql, $error=false;

	public function __construct() {
		$this->inject();//执行注入解析
		if(empty($this->help))
			$this->help = new realize\MysqlDbHelp();
		// $IConstraitBuild = new realize\MysqlConstraitBuild();
	}

	public function insert($Entiy, $is_replace = false) {
		$IConstraitBuild=new realize\MysqlConstraitBuild();
		$sql = $IConstraitBuild->buildInsertSql($Entiy, $is_replace);
		$this->help->setEntiy($Entiy);
		$bindData=$IConstraitBuild->getBindData();
		$return = $this->help->exec($sql, $bindData);
		$this->sql = Tool::getInstance()->showQuery($sql,$bindData);
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
		$IConstraitBuild=new realize\MysqlConstraitBuild();
		$sql = $IConstraitBuild->buildSelectSql($Entiy);
		$this->help->setEntiy($Entiy);
		$bindData=$IConstraitBuild->getBindData();
		if ($yield) {
			$data = $this->help->setFetchMode($this->mode, $this->classname)->query($sql, $bindData);
		} else {
			$data = $this->help->setFetchMode($this->mode, $this->classname)->queryResultArray($sql, $bindData);
		}
		$this->sql = Tool::getInstance()->showQuery($sql,$bindData);
		return $data;
	}
	public function update($Entiy) {
		if(!$Entiy->checkSaveForUpdateOrDelete()){
			throw new \Exception("You are using safe update mode and you tried to update a table without a WHERE that uses a KEY column To disable safe mode");
		}
		$IConstraitBuild=new realize\MysqlConstraitBuild();
		$sql = $IConstraitBuild->buildUpdataSql($Entiy);
		$this->help->setEntiy($Entiy);
		$bindData=$IConstraitBuild->getBindData();
		$return = $this->help->exec($sql, $bindData,RelType::update);
		$this->sql = Tool::getInstance()->showQuery($sql,$bindData);
		return $return;
	}
	public function delete($Entiy) {
		if(!$Entiy->checkSaveForUpdateOrDelete()){
			throw new \Exception("You are using safe update mode and you tried to update a table without a WHERE that uses a KEY column To disable safe mode");
		}
		$IConstraitBuild=new realize\MysqlConstraitBuild();
		$sql = $IConstraitBuild->buildDeleteSql($Entiy);
		$this->help->setEntiy($Entiy);
		$bindData=$IConstraitBuild->getBindData();
		$effect_rows_num = $this->help->exec($sql, $bindData ,RelType::delete);
		$this->sql = Tool::getInstance()->showQuery($sql,$bindData);
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
		$this->help->transaction($type);
	}
	/**
	 * 取得pdo
	 * @return [type] [description]
	 */
	public function getPdo() {
		return $this->help->getDbConn();
	}
	public function getHelp() {
		return $this->help;
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
	public function fetchSql($Entiy,$type=FetchType::select) {
		// TODO: Implement fetchSql() method.
		switch ($type) {
			case FetchType::select:
				$method="buildSelectSql";
				break;
			case FetchType::update:
				$method="buildUpdataSql";
				break;
			case FetchType::delete:
				$method="buildDeleteSql";
				break;
			case FetchType::insert:
				$method="buildInsertSql";
				break;
		}
		$IConstraitBuild=new realize\MysqlConstraitBuild();
		$sql = $IConstraitBuild->$method($Entiy);
		$bindData= $IConstraitBuild->getBindData();
		$this->sql = Tool::getInstance()->showQuery($sql,$bindData);
		return $bindData;
	}
}
