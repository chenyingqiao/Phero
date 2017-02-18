<?php
namespace PheroTest\DatabaseTest\Unit;
use Phero\Database\DbUnit;
use Phero\Database\Enum\RelType;
use Phero\Database\Interfaces\IRelation;

/**
 * @Table[alias=course]
 * @RelationEnable
 */
class video_course extends DbUnit implements IRelation {
	/**
	 * @Field
	 */
	public $course_id;
	/**
	 * @Field
	 */
	public $name;
	/**
	 * @Field
	 */
	public $anthor;
	/**
	 * @Field
	 * @Foreign[rel=cat]
	 */
	public $cat_id;
	/**
	 * @Field
	 */
	public $direction_id;
	/**
	 * @Field
	 */
	public $difficulty_id;
	/**
	 * @Field
	 */
	public $intreduce;
	/**
	 * @Field
	 */
	public $video_path;
	/**
	 * @Field
	 */
	public $cover;
	/**
	 * @Field
	 */
	public $create_time;
	/**
	 * @Field
	 */
	public $update_time;
	/**
	 * @Relation[type=oo,class=PheroTest\DatabaseTest\Unit\video_cat,key=id]
	 * @Entiy[field=name]
	 */
	public $cat;

	/**
	 * 关联
	 */
	public function rel($type, $entiy) {
		switch ($type) {
		case RelType::select:
			echo "  +++++++++++++++++++++++++++select+++++++++++++++++++++++++++ ";
			break;

		default:
			# code...
			break;
		}
	}
}