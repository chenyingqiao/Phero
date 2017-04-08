<?php
namespace Phero\Database\Realize;

use Phero\Database\Interfaces as interfaces;
use Phero\Database\Realize as realize;
use Phero\Database\Realize\ConsTrait as consTraits;
use Phero\Database\Traits as Traits;

/**
 * 约束建造者
 *
 * Constaint吧sql类比成约束
 * 不同的结构就是不同的约束
 * 其实sql本省就是一个约束的描述
 * 比如从是数据库里面筛选什么样的数据用select 描述
 */
class MysqlConsTraitBuild implements interfaces\IConsTraitBuild, interfaces\IBindData {
	use Traits\TConsTraitTableDependent;

	CONST DataSource = 'datasourse'; //数据源  包括筛选语句 如where
	CONST Field = 'field'; //查询的数据
	CONST Grouping = 'grouping'; //分组
	CONST Value = 'value'; //数据 包括insert中values 后边
	CONST Order = 'order'; //排序语句
	CONST Set = 'set'; //key=value格式的sql语句
	CONST Where = 'where';
	CONST Limit = 'limit';
	CONST Having = 'having';

	private $consTraits = array();

	/**
	 * 添加sql语句的约束
	 * 最后添加的会覆盖之前添加的同类型的约束
	 * @param IConsTrait $consTrait [description]
	 */
	public function addItem(interfaces\IConsTrait $consTrait) {
		$this->consTraits[$consTrait->getType()] = $consTrait;
	}
	public function getConsTraits() {
		return $this->consTraits;
	}
	/**
	 * 快速构建查询
	 * @param  [type] $Entiy [可传也可以忽略 忽略的时候用的是使用consTraits数组中的拼接对象 ]
	 * @return [type]        [description]
	 */
	public function buildSelectSql($Entiy = null) {
		if (empty($this->consTraits) && !empty($Entiy)) {
			$this->addItem(new consTraits\FieldConsTrait($Entiy));
			$this->addItem(new consTraits\DataSourceConsTrait($Entiy));
			$this->addItem(new consTraits\WhereConsTrait($Entiy));
			$this->addItem(new consTraits\GroupConsTrait($Entiy));
			$this->addItem(new consTraits\LimitConsTrait($Entiy));
			$this->addItem(new consTraits\OrderConsTrait($Entiy));
			$this->addItem(new consTraits\HavingConsTrait($Entiy));
		}

		$distanct = "";
		if ($Entiy->getDistinct()) {$distanct = " distinct ";}

		$sql = "select " . $distanct . $this->fragment(MysqlConsTraitBuild::Field)
		. $this->fragment(MysqlConsTraitBuild::DataSource)
		. $this->fragment(MysqlConsTraitBuild::Where)
		. $this->fragment(MysqlConsTraitBuild::Grouping)
		. $this->fragment(MysqlConsTraitBuild::Having)
		. $this->fragment(MysqlConsTraitBuild::Order)
		. $this->fragment(MysqlConsTraitBuild::Limit) . ';';
		$bindData1 = $this->consTraits[MysqlConsTraitBuild::Where]->getBindData();
		$bindData2 = $this->consTraits[MysqlConsTraitBuild::Having]->getBindData();
		$this->bindData = array_merge($bindData1, $bindData2);
		return $sql;
	}
	/**
	 * 这里要处理成也可以支持多个值的形式
	 * @param  [type]  $Entiy      [实体类对象 传入数组就是更新多个]
	 * @param  boolean $is_replace [是否是replace]
	 * @return [type]              [description]
	 */
	public function buildInsertSql($Entiy = null, $is_replace = false) {
		if (empty($this->consTraits) && !empty($Entiy)) {
			$this->addItem(new consTraits\InsertFieldConsTrait($Entiy));
			$this->addItem(new consTraits\InsertValueConsTrait($Entiy));
		}
		if (is_array($Entiy)) {
			$Entiy = $Entiy[0];
		}
		if ($is_replace) {
			$sql_head = "replace";
		} else {
			$sql_head = "insert";
		}
		$sql = $sql_head . " into " . $this->getTableName($Entiy) . " (" . $this->fragment(MysqlConsTraitBuild::Field) . ") values " . $this->fragment(MysqlConsTraitBuild::Value) . ";";
		$this->bindData = $this->consTraits[MysqlConsTraitBuild::Value]->getBindData();
		return $sql;
	}

	/**
	 * 更新
	 * @param  [type] $Entiy [description]
	 * @return [type]        [description]
	 */
	public function buildUpdataSql($Entiy = null) {
		if (empty($this->consTraits) && !empty($Entiy)) {
			$this->addItem(new consTraits\SetValueConsTrait($Entiy));
			$this->addItem(new consTraits\WhereConsTrait($Entiy));
		}
		if (is_array($Entiy)) {
			$Entiy = $Entiy[0];
		}
		$as = $this->getTableAlias($Entiy);
		if ($as) {
			$as = " as " . $as;
		} else {
			$as = '';
		}
		$sql = "update " . $this->getTableName($Entiy) . $as . " set " . $this->fragment(MysqlConsTraitBuild::Set) . $this->fragment(MysqlConsTraitBuild::Where) . ";";
		$bindData1 = $this->consTraits[MysqlConsTraitBuild::Where]->getBindData();
		$bindData2 = $this->consTraits[MysqlConsTraitBuild::Set]->getBindData();
		$this->bindData = array_merge($bindData1, $bindData2);
		return $sql;
	}

	/**
	 * 构建删除的sql语句
	 * @param  [type] $Entity [description]
	 * @return [type]         [description]
	 */
	public function buildDeleteSql($Entiy) {
		if (empty($this->consTraits) && !empty($Entiy)) {
			$this->addItem(new consTraits\WhereConsTrait($Entiy, false));
		}
		$sql = "delete from " . $this->getTableName($Entiy) . $this->fragment(MysqlConsTraitBuild::Where) . ";";
		$bindData1 = $this->consTraits[MysqlConsTraitBuild::Where]->getBindData();
		$this->bindData = $bindData1;
		return $sql;
	}

	/**
	 * 取得代码片段
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	private function fragment($key) {
		if (empty($this->consTraits[$key])) {
			return "";
		}
		return $this->consTraits[$key]->getSqlFragment();
	}

	private $bindData = [];
	//bindValue使用的数据数组
	public function getBindData() {
		return $this->bindData;
	}
}