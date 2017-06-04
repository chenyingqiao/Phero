<?php 
namespace PheroTest\DatabaseTest\Unit;
use PheroTest\DatabaseTest\Traits\Truncate;
use Phero\Database\DbUnit;
/**
 * @Author: lerko
 * @Date:   2017-05-31 11:54:57
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-06-04 15:53:14
 */
class MotherInfo extends DbUnit
{
	use Truncate;
	/**
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