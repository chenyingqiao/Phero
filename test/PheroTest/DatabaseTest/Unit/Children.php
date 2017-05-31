<?php 

namespace PheroTest\DatabaseTest\Unit;

use Phero\Database\DbUnit;
/**
 * @Author: lerko
 * @Date:   2017-05-31 11:54:17
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-05-31 13:47:10
 *
 * @Table[alias=children]
 */
class Children extends DbUnit
{
	/**
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
	 * @Field
	 * @var [type]
	 */
	public $pid;
}