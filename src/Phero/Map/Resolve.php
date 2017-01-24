<?php
namespace Phero\Map;

use Phero\Cache as cache;
use Phero\Cache\LocalFileCache;
use Phero\System as sys;

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
		// $SelfReflectionClass = new \ReflectionClass(get_parent_class);
		// $NodeName = get_parent_class();

		/**
		 * 这里可以通过缓存获取注解
		 *
		 * 缓存通过类名称（包括命名空间）作为key
		 */
		if (sys\DI::get(cache\Enum\CacheConfig::injectCache)) {
			//有注入缓存注解才会进行注解缓存
			$parent_class_name = get_parent_class();
			if ($parent_class_name == "ReflectionClass") {
				$NodeKey = $this->getName();
			} else {
				$NodeKey = $this->getDeclaringClass()->getName() . ":" . $this->getName();
			}
			$NodeKey = md5($NodeKey);
			$cache = LocalFileCache::read($NodeKey);
			if (!empty($cache)) {return $cache;}
		}

		$str = $this->getDocComment();
		$NodeName = $NodeReflection->getName();
		$NodeName = explode('\\', $NodeName);
		$NodeName = $NodeName[count($NodeName) - 1];
		$result = preg_match_all("/@$NodeName(\[([\S]+){0,}\]){0,}/", $str, $match);

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
		if (sys\DI::get(cache\Enum\CacheConfig::injectCache)) {
			LocalFileCache::save($NodeKey, $Node);
		}

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