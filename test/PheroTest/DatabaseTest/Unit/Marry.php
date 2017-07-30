<?php 

namespace PheroTest\DatabaseTest\Unit;

use PheroTest\DatabaseTest\Traits\Truncate;
use Phero\Database\DbUnit;
/**
 * @Author: lerko
 * @Date:   2017-05-31 11:54:57
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-07-30 16:23:20
 */
class Marry extends DbUnit
{
	use Truncate;
	/**
	 * @Field[type=int]
	 * @Primary
	 * @var [type]
	 */
	public $id;
	/**
	 * @Field
	 * @Foreign[rel=parent]
	 * @var [type]
	 */
	public $pid;
	/**
	 * @Field
	 * @Foreign[rel=mother|motherInfo]
	 * @var [type]
	 */
	public $mid;

	/**
	 * @Relation[type=oo,class=PheroTest\DatabaseTest\Unit\Parents,key=id]
	 * @var [type]
	 */
	public $parent;
	/**
	 * @Relation[type=oo,class=PheroTest\DatabaseTest\Unit\Mother,key=id]
	 * @var [type]
	 */
	public $mother;
	/**
	 * @Relation[type=oo,class=PheroTest\DatabaseTest\Unit\MotherInfo,key=mid]
	 * @var [type]
	 */
	public $motherInfo;
}