<?php 

namespace PheroTest\DatabaseTest\Unit;

use PheroTest\DatabaseTest\Traits\Truncate;
use Phero\Database\DbUnit;
/**
 * @Author: lerko
 * @Date:   2017-05-31 11:54:17
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-06-04 10:01:41
 *
 * @Table[alias=children]
 */
class Children extends DbUnit
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
	 * @var [type]
	 */
	public $name;
	/**
	 * @Field[name=pid]
	 * @var [type]
	 */
	public $marry_id;
}