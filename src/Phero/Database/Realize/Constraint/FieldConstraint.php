<?php
namespace Phero\Database\Realize\Constraint;

use Phero\Database\Interfaces as interfaces;
use Phero\Database\Realize as realize;
use Phero\Database\Traint as traint;
use Phero\Map as M;
use Phero\Map\Note as note;

/**
 * 列约束
 */
class FieldConstraint implements interfaces\IConstraint {
	use traint\TConstraintTableDependent;
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
	 * @param [type] $Entiy [description]
	 */
	public function setFieldByEntiy($Entiy) {
		$tableAlias = $this->getName($Entiy);
		$property = $this->getTableProperty($Entiy);
		$fieldTemp = $Entiy->getFieldTemp();
		$this->userSetField($Entiy);
		foreach ($property as $key => $value) {
			$fieldName = $value->getName();
			$temp = null;
			if (!empty($fieldTemp[$fieldName])) {
				$temp = $fieldTemp[$fieldName];
				$Entiy->$fieldName = true;
			}

			if ($Entiy->$fieldName === false) {
				//属性值未true的才添加到field
				continue;
			}
			$FieldNode=$value->getNode(new note\Field());
			if ($Entiy->have_as) {
				$as = $FieldNode->alias;
			} else {
				$as = null;
			}
			if(!empty($FieldNode->name)){
				$fieldName=$FieldNode->name;
			}
			$this->setField($fieldName, $tableAlias, $as, $temp);
		}
	}
	/**
	 * 设置用户主动设置field
	 * @param  [type] $Entiy [description]
	 * @return [type]        [description]
	 */
	private function userSetField($Entiy) {
		$field = $Entiy->getField();
		$tableAlias = $this->getName($Entiy);
		if (count($field) > 0) {
			foreach ($field as $key => $value) {
				$this->setField($value, $tableAlias, null, null);
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
	public function setField($name, $table = null, $as = null, $temp = null) {
		if (is_array($name)) {
			return $this;
		}
		$index = count($this->fieldList);
		if (empty($table)) {
			$this->fieldList[$index][$index] = [$name];
			$this->fieldList[$index][$index][] = $as;
			$this->fieldList[$index][$index][] = $temp;
		} else {
			$this->fieldList[$index][$table] = [$name];
			$this->fieldList[$index][$table][] = $as;
			$this->fieldList[$index][$table][] = $temp;
		}
		return $this;
	}
	/**
	 * 批量设置
	 * @param Array $arr ["table"=>[name,as,temp]]
	 */
	public function setFieldByList(Array $arr) {
		foreach ($arr as $key => $value) {
			if (!is_array($value)) {
				$value = [$value];
			}
			$this->fieldList[][$key] = $value;
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

			$table = is_numeric($key)&&$this->isEntiyField($value[0]) ? "" : $key . ".";
			$name = !empty($value[0]) ? "`".$value[0]."`" : "";
			$as = !empty($value[1]) ? " as " . $value[1] : "";
			$temp = !empty($value[2]) ? $value[2] : "";

			$fieldSql = $table . $name;

			if (empty($temp)) {
				$sql .= $fieldSql . $as . $split;
			} else {
				$sql .= str_replace("?", $fieldSql, $temp) . $as . $split; //替换函数
			}
			$i++;
		}
		return $sql;
	}

	private function isEntiyField($value)
	{
		$propertys=$this->getTablePropertyNames($this->entiy);
		var_dump($propertys);
		return in_array($value);
	}

	public function getType() {
		return realize\MysqlConstraintBuild::Field;
	}
}