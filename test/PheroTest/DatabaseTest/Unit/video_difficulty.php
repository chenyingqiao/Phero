<?php
namespace PheroTest\DatabaseTest\Unit;
use Phero\Database\DbUnit;

/**
 *
 */
class video_difficulty {
	use DbUnit;
	/**
	 * @Field[alias=diff_id]
	 * @var [type]
	 */
	public $id;
	/**
	 * @Field[alias=diff_name]
	 * @var [type]
	 */
	public $name;
}