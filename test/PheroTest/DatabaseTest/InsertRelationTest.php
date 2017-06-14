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
 * @Last Modified time: 2017-06-14 14:10:23
 */
class InsertRelationTest extends BaseTest
{
	/**
	 * 目前只支持单个的插入关联
	 * @Author   Lerko
	 * @DateTime 2017-06-14T14:05:58+0800
	 * @return   [type]                   [description]
	 */
	public function testInsertRelation(){
		$Mother=new Mother;
		$Mother->id=12;
		$Mother->name="relation_test关联插入测试";
		$Mother->info=new MotherInfo([
				"email"=>"00000000@qq.com"
			]);
		$Mother->insert();
		$data=$Mother->select();
	}
}