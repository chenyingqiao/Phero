<?php
namespace Phero\Map;

use Phero\Cache as cache;
use Phero\Cache\CacheOperationByConfig;
use Phero\Cache\LocalFileCache;
use Phero\System as sys;
use Phero\System\Config;

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
		if(isset($this->entiy)){
			$Node=$this->entiy->getMap($NodeName);
			if($Node!==false){
				return $Node;
			}
		}
		$debug=Config::config("debug");
		$Node=CacheOperationByConfig::read($this->getCacheKey($NodeName));
		if(!empty($Node)&&empty($debug)){
			return $Node;
		}

		$match=$this->_getDocCommentMatch($NodeReflection);
		if(empty($match)){
			return null;
		}
		$paramData=$this->_getDocNodeData($match);
		$Node=$this->_checkNodePropertiseAndAssign($NodeReflection,$paramData);
		// if(empty($debug))
		CacheOperationByConfig::save($this->getCacheKey($NodeName),$Node);

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

	private function _getDocCommentMatch($NodeReflection){
		$str = $this->getDocComment();
		$NodeName = $NodeReflection->getName();
		$NodeName = explode('\\', $NodeName);
		$NodeName = $NodeName[count($NodeName) - 1];
		$result = preg_match_all("/@{$NodeName}[\s]*(\[([\S]+){0,}\]){0,}/", $str, $match);

		//没有找到注解
		if (empty($result)) {
			return null;
		}
		return $match;
	}

	private function _getDocNodeData($match){
		$ResolveNodeName = $match[1][0];
		$ResolveNodeParam = $match[2][0];
		$ResolveNodeParam = explode(',', $ResolveNodeParam);
		$ParamMap = [];
		foreach ($ResolveNodeParam as $key => $value) {
			$param = explode('=', $value);
			if (isset($param[1])) {
				if (strstr($param[1], '|')) {
					//v|v|v  或者 v:k|v:k|v:k
					$param_v = explode('|', $param[1]);
					$param_kv = [];
					foreach ($param_v as $key => $value) {
						if (strstr($value, ':')) {
							$kv = explode(':', $value);
							$param_kv[$kv[0]] = $kv[1];
							unset($param_v[$key]);
						}
					}
					$param[1] = array_merge($param_v, $param_kv);
				}
				$ParamMap[$param[0]] = $param[1];
			}
		}
		return $ParamMap;
	}

	private function _checkNodePropertiseAndAssign($NodeReflection,$ParamMap){
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
		return $Node;
	}

	private function getCacheKey($nodeName){
		$parent_class_name = get_parent_class();
		if ($parent_class_name == "ReflectionClass") {
			$NodeKey = $this->getName();
		} else {
			$NodeKey = $this->getDeclaringClass()->getName() .':'.$nodeName. ":" . $this->getName();
		}
		return base64_encode($NodeKey);
	}
}
