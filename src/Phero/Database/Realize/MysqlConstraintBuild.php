<?php
namespace Phero\Database\Realize;

use Phero\Database\Interfaces as interfaces;
use Phero\Database\Realize as realize;
use Phero\Database\Realize\Constraint as constraint;
use Phero\Database\Traint as traint;

/**
 * 约束建造者
 *
 * Constaint吧sql类比成约束
 * 不同的结构就是不同的约束
 * 其实sql本省就是一个约束的描述
 * 比如从是数据库里面筛选什么样的数据用select 描述
 */
class MysqlConstraintBuild implements interfaces\IConstraintBuild, interfaces\IBindData {
	use traint\TConstraintTableDependent;

	CONST DataSource = 'datasourse'; //数据源  包括筛选语句 如where
	CONST Field = 'field'; //查询的数据
	CONST Grouping = 'grouping'; //分组
	CONST Value = 'value'; //数据 包括insert中values 后边
	CONST Order = 'order'; //排序语句
	CONST Set = 'set'; //key=value格式的sql语句
	CONST Where = 'where';
	CONST Limit = 'limit';

	private $constraints = array();

	/**
	 * 添加sql语句的约束
	 * 最后添加的会覆盖之前添加的同类型的约束
	 * @param IConstraint $constraint [description]
	 */
	public function addItem(interfaces\IConstraint $constraint) {
		$this->constraints[$constraint->getType()] = $constraint;
	}
	public function getConstraints() {
		return $this->constraints;
	}
	/**
	 * 快速构建查询
	 * @param  [type] $Entiy [可传也可以忽略 忽略的时候用的是使用constraints数组中的拼接对象 ]
	 * @return [type]        [description]
	 */
	public function buildSelectSql($Entiy = null) {
		if (empty($this->constraints) && !empty($Entiy)) {
			$this->addItem(new constraint\FieldConstraint($Entiy));
			$this->addItem(new constraint\DataSourceConstraint($Entiy));
			$this->addItem(new constraint\WhereConstraint($Entiy));
			$this->addItem(new constraint\GroupConstraint($Entiy));
			$this->addItem(new constraint\LimitConstraint($Entiy));
			$this->addItem(new constraint\OrderConstraint($Entiy));
		}

		$sql = "select " . $this->fragment(MysqlConstraintBuild::Field) . $this->fragment(MysqlConstraintBuild::DataSource) . $this->fragment(MysqlConstraintBuild::Where) . $this->fragment(MysqlConstraintBuild::Grouping) . $this->fragment(MysqlConstraintBuild::Order) . $this->fragment(MysqlConstraintBuild::Limit) . ';';
		$this->bindData = $this->constraints[MysqlConstraintBuild::Where]->getBindData();
		return $sql;
	}
	/**
	 * 这里要处理成也可以支持多个值的形式
	 * @param  [type]  $Entiy      [实体类对象 传入数组就是更新多个]
	 * @param  boolean $is_replace [是否是replace]
	 * @return [type]              [description]
	 */
	public function buildInsertSql($Entiy = null, $is_replace = false) {
		if (empty($this->constraints) && !empty($Entiy)) {
			$this->addItem(new constraint\InsertFieldConstraint($Entiy));
			$this->addItem(new constraint\InsertValueConstraint($Entiy));
		}
		if (is_array($Entiy)) {
			$Entiy = $Entiy[0];
		}
		if ($is_replace) {
			$sql_head = "replace";
		} else {
			$sql_head = "insert";
		}
		$sql = $sql_head . " into " . $this->getTableName($Entiy) . " (" . $this->fragment(MysqlConstraintBuild::Field) . ") values " . $this->fragment(MysqlConstraintBuild::Value) . ";";
		$this->bindData = $this->constraints[MysqlConstraintBuild::Value]->getBindData();
		return $sql;
	}

	/**
	 * 更新
	 * @param  [type] $Entiy [description]
	 * @return [type]        [description]
	 */
	public function buildUpdataSql($Entiy = null) {
		if (empty($this->constraints) && !empty($Entiy)) {
			$this->addItem(new constraint\SetValueConstraint($Entiy));
			$this->addItem(new constraint\WhereConstraint($Entiy));
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
		$sql = "update " . $this->getTableName($Entiy) . $as . " set " . $this->fragment(MysqlConstraintBuild::Set) . $this->fragment(MysqlConstraintBuild::Where) . ";";
		$bindData1 = $this->constraints[MysqlConstraintBuild::Where]->getBindData();
		$bindData2 = $this->constraints[MysqlConstraintBuild::Set]->getBindData();
		$this->bindData = array_merge($bindData1, $bindData2);
		return $sql;
	}

	/**
	 * 构建删除的sql语句
	 * @param  [type] $Entity [description]
	 * @return [type]         [description]
	 */
	public function buildDeleteSql($Entiy) {
		if (empty($this->constraints) && !empty($Entiy)) {
			$this->addItem(new constraint\WhereConstraint($Entiy, false));
		}
		$sql = "delete from " . $this->getTableName($Entiy) . $this->fragment(MysqlConstraintBuild::Where) . ";";
		$bindData1 = $this->constraints[MysqlConstraintBuild::Where]->getBindData();
		$this->bindData = $bindData1;
		var_dump($bindData1);
		return $sql;
	}

	/**
	 * 取得代码片段
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	private function fragment($key) {
		if (empty($this->constraints[$key])) {
			return "";
		}
		return $this->constraints[$key]->getSqlFragment();
	}

	private $bindData = [];
	//bindValue使用的数据数组
	public function getBindData() {
		return $this->bindData;
	}
}