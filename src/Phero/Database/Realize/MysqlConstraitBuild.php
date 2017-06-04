<?php
namespace Phero\Database\Realize;

use Phero\Database\Interfaces as interfaces;
use Phero\Database\Realize as realize;
use Phero\Database\Realize\Constrait as Constraits;
use Phero\Database\Traits as Traits;

/**
 * 约束建造者
 *
 * Constaint吧sql类比成约束
 * 不同的结构就是不同的约束
 * 其实sql本省就是一个约束的描述
 * 比如从是数据库里面筛选什么样的数据用select 描述
 */
class MysqlConstraitBuild implements interfaces\IConstraitBuild, interfaces\IBindData {
	use Traits\TConstraitTableDependent;

	CONST DataSource = 'datasourse'; //数据源  包括筛选语句 如where
	CONST Field = 'field'; //查询的数据
	CONST Grouping = 'grouping'; //分组
	CONST Value = 'value'; //数据 包括insert中values 后边
	CONST Order = 'order'; //排序语句
	CONST Set = 'set'; //key=value格式的sql语句
	CONST Where = 'where';
	CONST Limit = 'limit';
	CONST Having = 'having';

	private $Constraits = array();

	/**
	 * 添加sql语句的约束
	 * 最后添加的会覆盖之前添加的同类型的约束
	 * @param IConstrait $Constrait [description]
	 */
	public function addItem(interfaces\IConstrait $Constrait) {
		$this->Constraits[$Constrait->getType()] = $Constrait;
	}
	public function clearItem(){
		$this->Constraits=[];
	}
	public function getConstraits() {
		return $this->Constraits;
	}
	/**
	 * 快速构建查询
	 * @param  [type] $Entiy [可传也可以忽略 忽略的时候用的是使用Constraits数组中的拼接对象 ]
	 * @return [type]        [description]
	 */
	public function buildSelectSql($Entiy = null) {
		if (empty($this->Constraits) && !empty($Entiy)) {
			$this->addItem(new Constraits\FieldConstrait($Entiy));
			$this->addItem(new Constraits\DataSourceConstrait($Entiy));
			$this->addItem(new Constraits\WhereConstrait($Entiy));
			$this->addItem(new Constraits\GroupConstrait($Entiy));
			$this->addItem(new Constraits\LimitConstrait($Entiy));
			$this->addItem(new Constraits\OrderConstrait($Entiy));
			$this->addItem(new Constraits\HavingConstrait($Entiy));
		}

		$distanct = "";
		if ($Entiy->getDistinct()) {$distanct = " distinct ";}

		$sql = "select " . $distanct . $this->fragment(MysqlConstraitBuild::Field)
		. $this->fragment(MysqlConstraitBuild::DataSource)
		. $this->fragment(MysqlConstraitBuild::Where)
		. $this->fragment(MysqlConstraitBuild::Grouping)
		. $this->fragment(MysqlConstraitBuild::Having)
		. $this->fragment(MysqlConstraitBuild::Order)
		. $this->fragment(MysqlConstraitBuild::Limit) . ';';
		$bindData1 = $this->Constraits[MysqlConstraitBuild::Where]->getBindData();
		$bindData2 = $this->Constraits[MysqlConstraitBuild::Having]->getBindData();
		$this->bindData = array_merge($bindData1, $bindData2);
		$this->clearItem();
		return $sql;
	}
	/**
	 * 这里要处理成也可以支持多个值的形式
	 * @param  [type]  $Entiy      [实体类对象 传入数组就是更新多个]
	 * @param  boolean $is_replace [是否是replace]
	 * @return [type]              [description]
	 */
	public function buildInsertSql($Entiy = null, $is_replace = false) {
		if (empty($this->Constraits) && !empty($Entiy)) {
			$this->addItem(new Constraits\InsertFieldConstrait($Entiy));
			$this->addItem(new Constraits\InsertValueConstrait($Entiy));
		}
		if (is_array($Entiy)) {
			$Entiy = $Entiy[0];
		}
		if ($is_replace) {
			$sql_head = "replace";
		} else {
			$sql_head = "insert";
		}
		$sql = $sql_head . " into " . $this->getTableName($Entiy) . " (" . $this->fragment(MysqlConstraitBuild::Field) . ") values " . $this->fragment(MysqlConstraitBuild::Value) . ";";
		$this->bindData = $this->Constraits[MysqlConstraitBuild::Value]->getBindData();
		$this->clearItem();
		return $sql;
	}

	/**
	 * 更新
	 * @param  [type] $Entiy [description]
	 * @return [type]        [description]
	 */
	public function buildUpdataSql($Entiy = null) {
		if (empty($this->Constraits) && !empty($Entiy)) {
			$this->addItem(new Constraits\SetValueConstrait($Entiy));
			$this->addItem(new Constraits\WhereConstrait($Entiy));
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
		$sql = "update `" . $this->getTableName($Entiy)."`" . $as . " set " . $this->fragment(MysqlConstraitBuild::Set) . $this->fragment(MysqlConstraitBuild::Where) . ";";
		$bindData1 = $this->Constraits[MysqlConstraitBuild::Where]->getBindData();
		$bindData2 = $this->Constraits[MysqlConstraitBuild::Set]->getBindData();
		$this->bindData = array_merge($bindData1, $bindData2);
		$this->clearItem();
		return $sql;
	}

	/**
	 * 构建删除的sql语句
	 * @param  [type] $Entity [description]
	 * @return [type]         [description]
	 */
	public function buildDeleteSql($Entiy) {
		if (empty($this->Constraits) && !empty($Entiy)) {
			$this->addItem(new Constraits\WhereConstrait($Entiy, false));
		}
		$sql = "delete from `" . $this->getTableName($Entiy) ."`". $this->fragment(MysqlConstraitBuild::Where) . ";";
		$bindData1 = $this->Constraits[MysqlConstraitBuild::Where]->getBindData();
		$this->bindData = $bindData1;
		$this->clearItem();
		return $sql;
	}

	/**
	 * 取得代码片段
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	private function fragment($key) {
		if (empty($this->Constraits[$key])) {
			return "";
		}
		return $this->Constraits[$key]->getSqlFragment();
	}

	private $bindData = [];
	//bindValue使用的数据数组
	public function getBindData() {
		return $this->bindData;
	}
}