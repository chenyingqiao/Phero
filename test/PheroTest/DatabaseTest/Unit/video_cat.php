<?php
namespace PheroTest\DatabaseTest\Unit;
use Phero\Database\DbUnit;

/**
 * @Table[alias=cat]
 */
class video_cat {
	use DbUnit;
	/**
	 * [$id description]
	 * @var [type]
	 * @Field[alias=cat_id]
	 */
	public $id;
	/**
	 * [$name description]
	 * @var [type]
	 * @Field[alias=cat_name]
	 */
	public $name;
}