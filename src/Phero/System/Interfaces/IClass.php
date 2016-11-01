<?php
namespace Phero\System\Interfaces;

interface IClass {
	/**
	 * 通过反射获取class实例
	 * @return [type] [description]
	 */
	public static function getClass($className, $namespace);
}