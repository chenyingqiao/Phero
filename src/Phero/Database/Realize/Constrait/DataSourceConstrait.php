<?php
namespace Phero\Database\Realize\Constrait;

use Phero\Database\Enum as enum;
use Phero\Database\Interfaces as interfaces;
use Phero\Database\Realize as realize;
use Phero\Database\Traits as Traits;

/**
 * 数据源建造者
 * 构建数据源的sql片段
 */
class DataSourceConstrait implements interfaces\IConstrait {

	use Traits\TConstraitTableDependent;

	/**
	 * 当前记录的数据源列表
	 * [table:表名  as:别名 join:链接方式 ]
	 * @var [type]
	 */
	private $datasource = array();

	/**
	 * 当前的join方式(默认)
	 * @var [type]
	 */
	private $join = enum\JoinType::inner_join;

	/**
	 * 初始化数据源构造
	 * @param [type] $Entiy [description]
	 */
	public function __construct($Entiy) {
		$this->setDatasourse($this->getTableName($Entiy), $this->getTableAlias($Entiy), null);
		$this->userSetDataSourse($Entiy);

		$this->joinRecursion($Entiy);
	}

	/**
	 * 地柜获取$Entiy的join数据 然后放置到datasource中通过getSqlFragment拼接
	 * @Author   Lerko
	 * @DateTime 2017-06-06T14:16:00+0800
	 * @param    [type]                   $Entiy [description]
	 * @return   [type]                          [description]
	 */
	private function joinRecursion($Entiy) {
		$joinList = $Entiy->getJoin();
		if (count($joinList) > 0) {
			foreach ($Entiy->getJoin() as $key => $value) {
				$this->setJoinType($value[2]);
				$this->setDatasourse($this->getTableName($value[0]), $this->getTableAlias($value[0]), $this->getTableOn($Entiy, $value[0], $value[1]));
				$this->userSetDataSourse($Entiy);
				$this->joinRecursion($value[0]);
			}
		}
	}

	/**
	 * 用户手动设置数据源
	 * @param  [type] $Entiy [description]
	 * @return [type]        [description]
	 */
	public function userSetDataSourse($Entiy) {
		$datasource = $Entiy->getDatasourse();
		foreach ($datasource as $key => $value) {
			if (isset($value[3])) {
				$this->setJoinType($value[3]);
			}
			if (!empty($jointype = $Entiy->getDatasourseJoinType())) {
				$this->setJoinType($jointype);
			}
			$this->setDatasourse($value[0], $value[1], $this->getTableOn($Entiy, $value[1], $value[2]));
		}
	}

	/**
	 * 普通参数设置数据源
	 * @param [type] $tablename [description]
	 * @param [type] $as        [description]
	 * @param [type] $on        [description]
	 */
	public function setDatasourse($tablename, $as = null, $on = null) {
		$this->datasource[] = ["table" => $tablename, "as" => $as, "on" => $on, "join" => $this->join];
		$this->join = enum\JoinType::inner_join; //初始化默认的join方式
		return $this;
	}

	/**
	 * 设置join的方式
	 * @param [type] $join_type [description]
	 */
	public function setJoinType($join_type) {
		$this->join = $join_type;
	}
	/**
	 * [getSqlFragment description]
	 * @return [type] [description]
	 * @Override
	 */
	public function getSqlFragment() {
		$sql = "from ";
		$i = 0;
		foreach ($this->datasource as $key => $value) {
			if ($i == 0) {
				$value['on'] = null;
				$value['join'] = null;
			}
			$table = "`".$value['table']."`";
			$i == 0 ? $join = "" : $join = " " . $value['join'] . " ";
			empty($value['as']) ? $as = "" : $as = " as `" . $value['as']."`";
			$on = empty($value['on']) ? "" : " on " . $value['on'] . " ";
			$sql .= $join . $table . $as . $on;
			$i++;
		}
		return $sql;
	}

	public function getType() {
		return realize\MysqlConstraitBuild::DataSource;
	}
}
