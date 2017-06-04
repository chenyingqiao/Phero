<?php 

namespace PheroTest\DatabaseTest\Unit;

use PheroTest\DatabaseTest\Traits\Truncate;
use Phero\Database\DbUnit;
/**
 * @Author: lerko
 * @Date:   2017-05-31 11:54:57
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-06-04 16:25:38
 * @RelationEnable
 */
class Mother extends DbUnit
{
	use Truncate;
	/**
	 * @Primary
	 * @Foreign[rel=info]
	 * @Field[type=int]
	 * @var [type]
	 */
	public $id;
	/**
	 * @Field
	 * @var [type]
	 */
	public $name;

	/**
	 * @Relation[type=oo,class=PheroTest\DatabaseTest\Unit\MotherInfo,key=mid]
	 * @var [type]
	 */
	public $info;
}