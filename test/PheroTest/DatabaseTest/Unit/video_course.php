<?php
namespace PheroTest\DatabaseTest\Unit;
use Phero\Database\DbUnit;

/**
 * @Table[alias=course]
 */
class video_course {
	use DbUnit;

	public $course_id;
	public $name;
	public $anthor;
	public $cat_id;
	public $direction_id;
	public $difficulty_id;
	public $intreduce;
	public $video_path;
	public $cover;
	public $create_time;
	public $update_time;
}