<?php 
namespace PheroTest\DatabaseTest\Unit;
use PheroTest\DatabaseTest\Traits\Truncate;
use Phero\Database\DbUnit;
/**
 * @Author: lerko
 * @Date:   2017-05-31 11:54:57
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-14 15:05:54
 */
class MotherInfo extends DbUnit
{
	use Truncate;
	/**
	 * @Primary
	 * @Field[type=int]
	 * @Primary
	 * @var [type]
	 */
	public $mid;
	/**
	 * @Field
	 * @var [type]
	 */
	public $email;
}