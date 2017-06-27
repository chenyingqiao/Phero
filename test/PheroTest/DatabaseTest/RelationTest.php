<?php 

namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Mother;
use PheroTest\DatabaseTest\Unit\MotherInfo;
/**
 * 关联插入测试
 * @Author: ‘chenyingqiao’
 * @Date:   2017-06-04 17:00:10
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-27 15:00:52
 */
class RelationTest extends BaseTest
{
	/**
	 * 目前只支持单个的插入关联
	 * @Author   Lerko
	 * @DateTime 2017-06-14T14:05:58+0800
	 * @return   [type]                   [description]
	 */
	public function InsertRelation(){
		Mother::Inc()->whereEq("id",12)->delete();
		MotherInfo::Inc()->whereEq("id",12)->delete();
		$Mother=new Mother;
		$Mother->id=12;
		$Mother->name="relation_test关联插入测试";
		$Mother->info=new MotherInfo([
				"email"=>"00000000@qq.com"
			]);
		$Mother->insert();
		$motherInfo=MotherInfo::Inc()->whereEq("id",12)->find();
		$this->assertEmpty($motherInfo);
	}

	/**
	 * 更新
	 * @Author   Lerko
	 * @DateTime 2017-06-14T14:58:27+0800
	 * @param    Mother                   $Mother [description]
	 * @return   [type]                           [description]
	 */
	public function testUpdateRelation(){
		$Mother=new Mother;
		$Mother->id=12;
		$Mother->name="relation_test关联插入测试".rand();
		$Mother->info=new MotherInfo([
				"email"=>rand()."@qq.com"
			]);
		$Mother->update();
		$this->TablePrint($Mother->sql());
		$this->TablePrint($Mother->error());
		$this->TablePrint($Mother->info->sql());
	}

	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-06-14T16:16:25+0800
	 * @return   [type]                   [description]
	 */
	public function deleteRelation(){
		$Mother=new Mother;
		$Mother->id=12;
		$Mother->delete();
		$this->TablePrint($Mother->sql());
		$this->TablePrint($Mother->error());
	}
}