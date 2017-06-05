<?php 

namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Mother;
use PheroTest\DatabaseTest\Unit\Parents;
/**
 * @Author: ‘chenyingqiao’
 * @Date:   2017-06-04 13:26:40
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-06-04 17:00:00
 */
class SelectRelationTest extends BaseTest
{
	public function testRelationSelect(){
		$data=Mother::Inc()->limit(1)->select();
		var_dump($data);
		$this->TablePrint([
				Mother::lastInc()->sql(),
				Mother::lastInc()->error(),
			]);
	}

	public function testInc(){
		Mother::Inc()->select();
		Parents::Inc()->select();
		$this->assertNotEquals(Parents::lastInc(),Mother::lastInc());
	}
}