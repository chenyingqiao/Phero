<?php
namespace Phero\Database\Realize\Constrait;

use Phero\Database\Interfaces as interfaces;
use Phero\Database\Realize as realize;
use Phero\Database\Traits as Traits;
use Phero\Map as M;
use Phero\Map\Note as note;

/**
 * 列约束
 */
class FieldConstrait implements interfaces\IConstrait {
	use Traits\TConstraitTableDependent;
	/**
	 * [table=>[name,as,temp]]
	 * table:field所属的表名
	 * name:field名称
	 * as:filed 别名
	 * temp:filed的函数  采用？代码field的
	 * @var array
	 */
	private $fieldList = array();

	private $entiy;
	/**
	 * 初始化的时候要进行表名指定
	 * @param [type] $Entiy [description]
	 */
	public function __construct($Entiy = null) {
		$this->entiy=$Entiy;
		$this->setFieldByEntiy($Entiy);
		$this->joinRecursion($Entiy);
	}

	private function joinRecursion($Entiy) {
		$joinList = $Entiy->getJoin();
		if (count($joinList) > 0) {
			foreach ($Entiy->getJoin() as $key => $value) {
				$this->setFieldByEntiy($value[0]);
				$this->joinRecursion($value[0]);
			}
		}
	}

	/**
	 * 通过实体类设置field
	 * 实体的字段为全等false的时候是属于不加入field的
	 * @param [type] $Entiy [description]
	 */
	public function setFieldByEntiy($Entiy) {
		$tableAlias = $this->getNameByCleverWay($Entiy);
		$property = $this->getTableProperty($Entiy);
		$this->userSetField($Entiy);
		foreach ($property as $key => $value) {
			$fieldName = $value->getName();
			$FieldNode=$value->getNode(new note\Field());
			if ($Entiy->$fieldName === false||empty($FieldNode)) {
				//有Field注解的才能添加到
				continue;
			}
			$as = $FieldNode->alias;
			if(!empty($FieldNode->name)){
				$fieldName=$FieldNode->name;
			}
			$this->setField($fieldName, $tableAlias, $as);
		}
	}
	/**
	 * 设置用户主动设置field
	 * @param  [type] $Entiy [description]
	 * @return [type]        [description]
	 */
	private function userSetField($Entiy) {
		$field = $Entiy->getField();
		$tableAlias = $this->getNameByCleverWay($Entiy);
		if (count($field) > 0) {
			foreach ($field as $key => $value) {
				$this->setField($value, $tableAlias, null);
			}
		}
	}

	/**
	 * 设置字段
	 * @param [type] $name  [字段名]
	 * @param [type] $table [表名]
	 * @param [type] $as    [别名]
	 * @param [type] $temp  [字段使用函数]
	 */
	public function setField($name, $table = null, $as = null) {
		if (is_array($name)) {
			return $this;
		}
		$index = count($this->fieldList);
		if (empty($table)) {
			$this->fieldList[$index][$index] = [$name,$as];
		} else {
			$this->fieldList[$index][$table] = [$name,$as];
		}
		return $this;
	}
	/**
	 * 获取字段的sql片段
	 * @return [type] [description]
	 */
	public function getSqlFragment() {
		$sql = "";
		$i = 0;
		// var_dump($this->fieldList);
		foreach ($this->fieldList as $key => $value) {
			//取内层的数据
			foreach ($value as $key1 => $value1) {
				$key = $key1;
				$value = $value1;
			}

			$i == count($this->fieldList) - 1 ? $split = " " : $split = ",";
			$table = is_numeric($key)? "" : $key;
			$name=$value[0];
			$as = !empty($value[1]) ? " as " . $value[1] : "";
			if(!strstr($name,'.')){
				$name = !empty($value[0]) ? "`".$value[0]."`" : "";
				$fieldSql = "`".$table ."`.". $name;
			}
			else
				$fieldSql = $name;
			$sql .= $fieldSql . $as . $split;
			$i++;
		}
		return $sql;
	}

	public function getType() {
		return realize\MysqlConstraitBuild::Field;
	}
}