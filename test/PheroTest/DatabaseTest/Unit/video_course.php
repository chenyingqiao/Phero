<?php
namespace PheroTest\DatabaseTest\Unit;
use Phero\Database\DbUnit;
use Phero\Database\Interfaces\IRelation;

/**
 * @Table[alias=course]
 */
class video_course extends DbUnit implements IRelation {
	/**
	 * @Field
	 * @var [type]
	 */
	public $course_id;
	/**
	 * @Field
	 * @var [type]
	 */
	public $name;
	/**
	 * @Field
	 * @var [type]
	 */
	public $anthor;
	/**
	 * @Field
	 * @var [type]
	 */
	public $cat_id;
	/**
	 * @Field
	 * @var [type]
	 */
	public $direction_id;
	/**
	 * @Field
	 * @var [type]
	 */
	public $difficulty_id;
	/**
	 * @Field
	 * @var [type]
	 */
	public $intreduce;
	/**
	 * @Field
	 * @var [type]
	 */
	public $video_path;
	/**
	 * @Field
	 * @var [type]
	 */
	public $cover;
	/**
	 * @Field
	 * @var [type]
	 */
	public $create_time;
	/**
	 * @Field
	 * @var [type]
	 */
	public $update_time;
	/**
	 * @Relation
	 * @var [type]
	 */
	public $cat;

	// public function __construct() {
	// 	parent::__construct();
	// 	$this->cat = (new video_cat())->whereEq("id", $this->cat_id);
	// }

	private $video_cat;
	public function rel() {
		$this->video_cat = new video_cat();
		$this->video_cat->whereEq("id", 1);
		$this->cat = $this->video_cat;
	}
	public function sql() {
		$this->video_cat->dumpSql();
	}
}