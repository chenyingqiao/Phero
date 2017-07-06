<?php
namespace Phero\System;

use Phero\System\DI;

/**
 *这是一个配置读取的类
 */
class Config {
	protected static $config_path;
	protected static $config;
	public static function config($key,$value=null) {
		self::$config_path = DI::get("config");
		if (self::$config_path) {
			if (!self::$config) {
				self::$config = require_once self::$config_path;
			}
			if($value!==null){
				self::$config[$key]=$value;
				return $value;
			}
			return isset(self::$config[$key]) ? self::$config[$key] : null;
		}
		return null;
	}
}