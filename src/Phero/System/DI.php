<?php
namespace Phero\System;

class DI {
	/**
	 * charset:数据库字符类型
	 * database [master slave]:数据库链接
	 * attr  pdo实例化的配置
	 * cache 缓存介质
	 */
	CONST config="config";//配置文件注入项目
	CONST hit_rule="hit_rule";//数据库从库的命中规律
	CONST dbhelp="dbhelp";//db的help
	CONST pdo_instance="pdo_instance";//pdo实例或者是主从实例数组
	CONST pdo_hit="pdo_hit";//pdo slave 命中规律

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
