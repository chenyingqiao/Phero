<?php
namespace Phero\Map;
use Phero\Database\Interfaces\INodeMap;
use Phero\Map\Interfaces\INode;

/**
 *
 */
class NodeReflectionClass extends \ReflectionClass implements INode {
	use Resolve;

	public function __construct($argument){
		parent::__construct($argument);
		if($argument instanceof INodeMap)
			$this->entiy=$argument;
	}
	
	/**
	 * 重载获取Properties的方法
	 * @param  [type] $filter [属性过滤]
	 * @return [type]         [description]
	 * @Override
	 */
	public function getProperties($filter = \ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED|\ReflectionProperty::IS_PRIVATE) {
		$propertys = parent::getProperties($filter);
		$NodePropertys = [];
		foreach ($propertys as $key => $value) {
			$NodePropertys[] = new NodeReflectionProperty($this->getName(), $value->getName());
		}
		return $NodePropertys;
	}

	/**
	 * 获取NodeReflecationProperty
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function getProperty($name) {
		$property = parent::getProperty($name);
		return new NodeReflectionProperty($this->getName(), $property->getName());
	}
	/**
	 * 获取自定义的函数反射类型
	 * @param  [type] $filter [description]
	 * @return [type]         [description]
	 */
	public function getMethods($filter = \ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED|\ReflectionProperty::IS_PRIVATE) {
		$methodList = parent::getMethods($filter);
		$methodNodeList = [];
		foreach ($methodList as $key => $value) {
			$methodNodeList[] = new NodeReflectionMethod($this->getName(), $value->getName());
		}
		return $methodNodeList;
	}

	/**
	 * 获取单个NodeMethod
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function getMethod($name) {
		return new NodeReflectionMethod($this->getName(), $method->name);
	}

	/**
	 * 获取属性的名称列表
	 * @param  [type] $filter [属性过滤]
	 * @return [type]         [description]
	 */
	public function getPropertieNames($filter = \ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED|\ReflectionProperty::IS_PRIVATE) {
		$propertys = parent::getProperties($filter);
		$propertynames = [];
		foreach ($propertys as $key => $value) {
			$propertynames[] = $value->getName();
		}
		return $propertynames;
	}

}