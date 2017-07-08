<?php 

namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\BuildUnit\Mother;
/**
 * @Author: lerko
 * @Date:   2017-06-29 10:16:52
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-29 10:20:03
 */
class BuilderUnitTest extends BaseTest
{
	
	public function testUnit($value='')
	{
		$data=Mother::Inc()->select();
		$this->assertEquals(count($data), 10);
	}
}