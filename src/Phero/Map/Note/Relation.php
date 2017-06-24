<?php
namespace Phero\Map\Note;

/**
 * 表关联注解
 */
class Relation {
	CONST OO = 'oo';// one to one
	CONST OM = 'om';// one to many

	/**
	 * 关联的类型  oo om
	 */
	public $type;

	/**
	 * 关联那个实体
	 */
	public $class;

	/**
	 * 关联的key parent 关联这个字段对应表的字段名
	 */
	public $key;
}