<?php 

namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Mother;
use PheroTest\DatabaseTest\Unit\Parents;
use Phero\Map\Note\RelationEnable;
/**
 * @Author: ‘chenyingqiao’
 * @Date:   2017-06-04 13:26:40
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-06 12:02:32
 */
class SelectRelationTest extends BaseTest
{
	/**
	 * @Author   Lerko
	 * @DateTime 2017-06-06T11:58:40+0800
	 * @return   [type]                   [description]
	 */
	public function RelationSelect(){
		$data=Mother::Inc()->find();
		$this->TablePrint([
				Mother::lastInc()->sql(),
				Mother::lastInc()->error(),
			]);
	}

	public function testRelationMapNodeSet(){
		$data=Mother::Inc()->map(new RelationEnable,null)->find();
		var_dump($data);
		$this->assertArrayNotHasKey("info", $data);
	}

	public function Inc(){
		Mother::Inc()->select();
		Parents::Inc()->select();
		$this->assertNotEquals(Parents::lastInc(),Mother::lastInc());
	}
}