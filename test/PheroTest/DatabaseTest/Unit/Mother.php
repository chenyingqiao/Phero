<?php

namespace PheroTest\DatabaseTest\Unit;

use PheroTest\DatabaseTest\Traits\Truncate;
use Phero\Database\DbUnit;
/**
 * @Author: lerko
 * @Date:   2017-05-31 11:54:57
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-07-29 20:50:16
 * @Table[name=Mother,alias=mother]
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
	 * @Entity[field=mid,sort=desc,key=mid]
	 * @var [type]
	 */
	public $info;
}
