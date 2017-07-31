<?php 

namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Mother;
use PheroTest\DatabaseTest\Unit\Parents;
use Phero\Map\Note\RelationEnable;
/**
 * @Author: ‘chenyingqiao’
 * @Date:   2017-06-04 13:26:40
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-07-31 09:12:38
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
		$this->assertArrayNotHasKey("info", $data);
	}

	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-06-27T15:55:20+0800
	 */
	public function RelationJoinChange(){
		$data=Mother::Inc()->relSelect();
		// var_dump($data);
		if(!empty($data))
			$this->assertArrayHasKey("info", $data[0]);
	}

	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-06-27T15:54:31+0800
	 */
	public function Inc(){
		Mother::Inc()->select();
		Parents::Inc()->select();
		$this->assertNotEquals(Parents::lastInc(),Mother::lastInc());
	}
}