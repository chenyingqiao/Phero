<?php
namespace Phero\System\Traint;

use Phero\Map\NodeReflectionClass;
use Phero\Map\Note\Inject;
use Phero\System\DI;

/**
 * 属性注入trait
 */
trait TInject {
	public function __construct() {
		$this->inject();
	}
	/**
	 * 注入解析
	 * 并且执行默认注入
	 */
	public final function inject() {
		$NodeReflection = new NodeReflectionClass($this);
		$propertys = $NodeReflection->getProperties(\ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
		$this->autoInject($propertys);
	}

	/**
	 * 注入的优先级
	 * 最先是注入实现类->di注入
	 * di注入覆盖class默认实现
	 * @param  [type] $propertys [类的属性集合]
	 * @return [type]            [description]
	 */
	private function autoInject($propertys) {
		foreach ($propertys as $key => $value) {
			$inject = $value->resolve(new Inject());
			$property_ref = null;
			if (!empty($inject)) {
				$class = null;
				$property_ref = $value->getName();
				// if (!empty($inject->super)) {
				// 	if ($class instanceof $inject->super) {
				// 		throw new Exception("不是超类", 1);
				// 	}
				// }
				if (!empty($inject->di)) {
					$DI = new NodeReflectionClass(new DI());
//					$injs = $DI->getProperty("injs");
					//					$injs->setAccessible(true);
					//					$injs = $injs->getValue();
					if (DI::get($inject->di) != null) {
						$this->$property_ref = DI::get($inject->di);
					}
				}
				// if (!empty($inject->class)) {
				// 	$class = $inject->class; //实现类
				// 	$class = new $class();
				// 	$this->$property_ref = $class;
				// }
			}
		}
	}
}