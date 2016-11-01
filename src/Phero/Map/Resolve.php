<?php
namespace Phero\Map;

/**
 *
 */
trait Resolve {
	/**
	 * 解析相应的注解 如果没有本注解就返回空
	 * 并且吧注解映射到实体类中
	 * @param  [type] $NodeClass [需要获取的注解名称]
	 * @return [type]            [返回注解实例化类]
	 */
	public function resolve($NodeClass) {
		if ($NodeClass instanceof \ReflectionClass) {
			$NodeReflection = $NodeClass;
		} else {
			$NodeReflection = new \ReflectionClass($NodeClass);
		}
		$NodeName = $NodeReflection->getName();
		$NodeShortName = $NodeReflection->getShortName();

		/**
		 * 这里可以通过缓存获取注解
		 *
		 * 缓存通过类名称（包括命名空间）作为key
		 */

		$str = $this->getDocComment();
		$result = preg_match_all("/@([\w]+)\[([\S]+)\]/", $str, $match);

		//没有找到注解
		if (empty($result)) {
			return null;
		}

		$ResolveNodeName = $match[1][0];
		$ResolveNodeParam = $match[2][0];
		$ResolveNodeParam = explode(',', $ResolveNodeParam);
		$ParamMap = [];
		foreach ($ResolveNodeParam as $key => $value) {
			$param = explode('=', $value);
			$ParamMap[$param[0]] = $param[1];
		}
		$ReflectionPropertys = $NodeReflection->getProperties(\ReflectionProperty::IS_PUBLIC);
		$ParamMapKeys = array_keys($ParamMap); //参数的key

		$Node = $NodeReflection->newInstance();
		foreach ($ReflectionPropertys as $key => $value) {
			$PropertyName = $value->getName();
			if (in_array($PropertyName, $ParamMapKeys)) {
				//如果在属性列表中
				$Node->$PropertyName = $ParamMap[$PropertyName];
			}
		}

		/**
		 * 这里可以缓存注解
		 */

		return $Node;
	}

	/**
	 * 取得node
	 * @param  [type] $node [description]
	 * @return [type]       [description]
	 */
	public function getNode($node) {
		$nowNode = $this->resolve($node);
		return empty($nowNode) ? $node : $nowNode;
	}
}