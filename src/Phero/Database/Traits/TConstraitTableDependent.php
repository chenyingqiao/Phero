<?php
namespace Phero\Database\Traits;

use Phero\Map\NodeReflectionClass;
use Phero\Map\NodeReflectionProperty;
use Phero\Map\Note\Field;
use Phero\Map\Note\Primary;
use Phero\Map\Note\Table;

trait TConstraitTableDependent {
	/**
	 * 获取实体对应的表的名称
	 * @param  [type] $Entiy [description]
	 * @return [type]        [description]
	 */
	public final function getTableName($Entiy) {
		$Reflection = new NodeReflectionClass($Entiy);
		$TableNode=$Reflection->resolve(new Table());
		if(empty($TableNode)||empty($TableNode->name)){
			return $Reflection->getShortName();
		}else{
			return $TableNode->name;
		}
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
	public final function getNameByCleverWay($Entiy) {
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

	/**
	 * 获取表的属性名称
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public final function getTablePropertyNames($Entiy) {
		$NodeReflectionClass = new NodeReflectionClass($Entiy);
		$property = $NodeReflectionClass->getProperties();
		$propertys = [];
		foreach ($property as $value) {
			if ($Field=$value->resolve(new Field())) {
				if(!empty($Field->name)){
					$propertys[] = $Field->name;
				}else{
					$propertys[] = $value->getName();
				}
			}
		}
		return $propertys;
	}

	public final function getTablePropertySingle($Entiy, $propertyName) {
		$propertys = $this->getTableProperty($Entiy);
		$name = [];
		$feild_node_name=[];
		$feild_node_relation_name=[];
		foreach ($propertys as $key => $value) {
			$name[] = $value->getName();
			$node_name_item=$value->getNode(new Field())->name;
			$feild_node_name[]=$node_name_item;
			$feild_node_relation_name[$node_name_item]=$value->getName();
		}
		if (!in_array($propertyName, $name)&&!in_array($propertyName,$feild_node_name)) {
			return false;
		}
		//这里如果是node的name就要对对propertyName进行指定
		if(in_array($propertyName,$feild_node_name)&&!in_array($propertyName, $name)){
			$propertyName=$feild_node_relation_name[$propertyName];
		}
		$NodeReflectionClass = new NodeReflectionClass($Entiy);
		return $NodeReflectionClass->getProperty($propertyName);
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
			throw new \Exception(get_class($Entiy)." entiy not exist ".$propertyName);
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
		$nameOfEntiy = $this->getNameByCleverWay($Entiy);
		if (is_string($JoinEntiy)) {
			$nameOfJoinEntiy = $JoinEntiy;
		} else {
			$nameOfJoinEntiy = $this->getNameByCleverWay($JoinEntiy);
		}
		$on = str_replace(["$", "#"], ["`{$nameOfEntiy}`", "`{$nameOfJoinEntiy}`"], $on);
		return $on;
	}
	/**
	 * 取得实体类的类型是不是string
	 * @return [type] [description]
	 */
	public final function getTablePropertyNodeOver1(NodeReflectionProperty $property, $nodeClass) {
		return $property->resolve($nodeClass);
	}

	/**
	 * 获取表标记的主键
	 * @Author   Lerko
	 * @DateTime 2017-04-15T15:50:25+0800
	 * @param    [type]                   $Entiy [description]
	 * @return   [type]                          [description]
	 */
	protected final function getPrimaryKey($Entity){
		$NodeReflectionClass = new NodeReflectionClass($Entity);
		$propertys = $NodeReflectionClass->getProperties();
		foreach ($propertys as $key => $value) {
			$primary=$value->getNode(new Primary());
			if(isset($primary)){
				$Field= $value->getNode(new Field());
				if(isset($Field->name)){
					return $Field->name;
				}
				return $value->getName();
			}
		}
		return null;
	}
}