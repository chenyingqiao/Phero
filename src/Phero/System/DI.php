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

    /**
     * 获取di容器中的对象
     * @param $key
     * @return mixed
     */
	public static function get($key){
	    return array_key_exists($key,DI::$injs)?DI::$injs[$key]:null;
    }
}