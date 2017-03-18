<?php
namespace Phero\Database\Enum;

/**
 * 查询语句条件
 */
class Where {
	CONST eq_ = " = ";
	CONST neq = " <> ";
	CONST in_ = " in ";
	CONST not_in = " not in ";
	CONST between = " between ";
	CONST like = " like ";
	CONST not_like = " not like ";
	CONST lt = " < ";
	CONST lr = " <= ";
	CONST gt = " > ";
	CONST ge = " >= ";
	CONST regexp = " regexp ";
	CONST is = " is ";

	CONST exists =" exists ";
	CONST not_exists =" not exists ";
	CONST all =" all ";
	CONST any =" any ";

	public static function get($key) {
		if ($key == "eq_") {
			return Where::eq_;
		} else if ($key == "neq") {
			return Where::neq;
		} else if ($key == "in_") {
			return Where::in_;
		} else if ($key == "not_in") {
			return Where::not_in;
		} else if ($key == "between") {
			return Where::between;
		} else if ($key == "like") {
			return Where::like;
		} else if ($key == "lt") {
			return Where::lt;
		} else if ($key == "lr") {
			return Where::lr;
		} else if ($key == "gt") {
			return Where::gt;
		} else if ($key == "ge") {
			return Where::ge;
		} else if ($key == 'regexp') {
			return Where::regexp;
		} else if ($key == 'is') {
			return Where::is;
		}else if($key=='exists'){
			return Where::exists;
		}else if($key=='not_exists'){
			return Where::not_exists;
		}else if($key=='all'){
			return Where::all;
		}else if($key=='any'){
			return Where::any;
		} else {
			return "";
		}
	}
}