<?php 
namespace PheroTest\DatabaseTest\Unit;
use Phero\Database\DbUnit;
/**
 * @Author: lerko
 * @Date:   2017-05-31 11:54:57
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-01 13:42:07
 */
class MotherInfo extends DbUnit
{
	/**
	 * @Field[type=int]
	 * @Primary
	 * @var [type]
	 */
	public $pid;
	/**
	 * @Field
	 * @var [type]
	 */
	public $phone;
}