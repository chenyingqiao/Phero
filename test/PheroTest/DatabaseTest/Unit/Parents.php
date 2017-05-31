<?php 
namespace PheroTest\DatabaseTest\Unit;
use Phero\Database\DbUnit;

/**
 * @Author: lerko
 * @Date:   2017-05-31 11:54:57
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-05-31 14:23:01
 * @Table[name=Parent,alias=parent]
 */
class Parents extends DbUnit
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
}