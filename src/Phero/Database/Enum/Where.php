<?php
namespace Phero\Database\Enum;

/**
 * 查询语句条件
 */
class Where {
	public static $eq_ = " = ";
	public static $neq = " <> ";
	public static $in_ = " in ";
	public static $not_in = " not in ";
	public static $between = " between ";
	public static $like = " like ";
	public static $not_like = " not like ";
	public static $lt = " < ";
	public static $lr = " <= ";
	public static $gt = " > ";
	public static $ge = " >= ";
	public static $regexp = " regexp ";
	public static $isnotnull = " is not null";
	public static $isnull = " is null";
	public static $exists =" exists ";
	public static $not_exists =" not exists ";
	public static $all =" all ";
	public static $any =" any ";

	public static function get($key) {
		if(strstr($key,'all')||strstr($key,'any')){
			$comp=str_replace(["all","any"],["",""],$key);
			$all_any=substr($key,-3);
			return self::$$comp.self::$$all_any;
		}
		return self::$$key;
	}
}