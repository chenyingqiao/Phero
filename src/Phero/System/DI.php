<?php
namespace Phero\System;

class DI {
	/**
	 * 注册di容器
	 * @var array
	 */
	private static $injs = [];
	/**
	 * 注册ioc容器
	 * @param  [type] $key      [ioc查找的key]
	 * @param  [type] $instance [ioc的实体]
	 * @return [type]           [description]
	 */
	public static function inj($key, $instance) {
		DI::$injs[$key] = $instance;
	}
}