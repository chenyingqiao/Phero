<?php
namespace Phero\Database\Traint;
use Phero\Map\NodeReflectionClass;
use Phero\Map\NodeReflectionProperty;
use Phero\Map\Note\Field;
use Phero\Map\Note\Table;

trait TConstraintTableDependent {
	/**
	 * 获取实体对应的表的名称
	 * @param  [type] $Entiy [description]
	 * @return [type]        [description]
	 */
	public final function getTableName($Entiy) {
		$Reflection = new NodeReflectionClass($Entiy);
		return $Reflection->getShortName();
	}

	/**
	 * 获取表的别名
	 * @param  [type] $Entiy [description]
	 * @return [type]        [description]
	 */
	public final function getTableAlias($Entiy) {
		$NodeReflectionClass = new NodeReflectionClass($Entiy);
		$Node = $NodeReflectionClass->resolve(new Table());
		if (empty($Node)) {
			return null;
		}
		return $Node->alias;
	}

	/**
	 * 判断是否有别名
	 * 有的话返回别名
	 * 没有就返回表名
	 * @param  [type] $Entiy [description]
	 * @return [type]        [description]
	 */
	public final function getName($Entiy) {
		$aliasEntiy = $this->getTableAlias($Entiy);
		$nameOfEntiy = isset($aliasEntiy) ? $aliasEntiy : $this->getTableName($Entiy);
		return $nameOfEntiy;
	}
	/**
	 * 获取实体类的属性
	 * @return [type] [description]
	 */
	public final function getTableProperty($Entiy) {
		$NodeReflectionClass = new NodeReflectionClass($Entiy);
		$property = $NodeReflectionClass->getProperties();
		$propertys = [];
		foreach ($property as $value) {
			if ($value->resolve(new Field())) {
				$propertys[] = $value;
			}
		}
		return $propertys;
	}

	public final function getTablePropertySingle($Entiy, $propertyName) {
		$propertys = $this->getTableProperty($Entiy);
		$nams = [];
		foreach ($propertys as $key => $value) {
			$nams[] = $value->getName();
		}
		if (!in_array($propertyName, $nams)) {
			return false;
		}
		$NodeReflectionClass = new NodeReflectionClass($Entiy);
		return $NodeReflectionClass->getProperty($propertyName);
	}

	public final function getTableRelation() {

	}

	/**
	 * 返回属性相应的注解
	 * @param  [type] $Entiy        [实体类]
	 * @param  [type] $propertyName [属性名称]
	 * @param  [type] $nodeClass    [注解名称]
	 * @return [type]               [description]
	 */
	public final function getTablePropertyNode($Entiy, $propertyName, $nodeClass) {
		$property = $this->getTablePropertySingle($Entiy, $propertyName);
		if ($property == false) {
			return false;
		}
		return $property->resolve($nodeClass);
	}

	/**
	 * 处理on链接的字符串 如"$.uid=#.id"
	 * @param  [type] $Entiy     [被关联的实体]
	 * @param  [type] $JoinEntiy [关联的实体]
	 * @return [type]            [description]
	 */
	public final function getTableOn($Entiy, $JoinEntiy, $on) {
		$nameOfEntiy = $this->getName($Entiy);
		if (is_string($JoinEntiy)) {
			$nameOfJoinEntiy = $JoinEntiy;
		} else {
			$nameOfJoinEntiy = $this->getName($JoinEntiy);
		}
		$on = str_replace(["$", "#"], [$nameOfEntiy, $nameOfJoinEntiy], $on);
		return $on;
	}
	/**
	 * 取得实体类的类型是不是string
	 * @return [type] [description]
	 */
	public final function getTablePropertyNodeOver1(NodeReflectionProperty $property, $nodeClass) {
		return $property->resolve($nodeClass);
	}
}