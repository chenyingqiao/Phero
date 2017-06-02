<?php 

namespace PheroTest\DatabaseTest\Unit;

use PheroTest\DatabaseTest\Traits\Truncate;
use Phero\Database\DbUnit;
/**
 * @Author: lerko
 * @Date:   2017-05-31 11:54:57
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-02 12:17:03
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
	 * @var [type]
	 */
	public $pid;
	/**
	 * @Field
	 * @var [type]
	 */
	public $mid;
}