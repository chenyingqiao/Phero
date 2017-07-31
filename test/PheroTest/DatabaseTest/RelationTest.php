<?php

namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Marry;
use PheroTest\DatabaseTest\Unit\Mother;
use PheroTest\DatabaseTest\Unit\MotherInfo;
use PheroTest\DatabaseTest\Unit\Parents;
use Phero\Database\Enum\RelType;
use Phero\Database\Traits\TRelation;
/**
 * 关联插入测试
 * @Author: ‘chenyingqiao’
 * @Date:   2017-06-04 17:00:10
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-07-31 12:13:54
 */
class RelationTest extends BaseTest
{
	use TRelation;
	/**
	 * @test
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
		$Mother->relInsert();
		$motherInfo=MotherInfo::Inc()->whereEq("email","00000000@qq.com")->find();
		// $this->TablePrint($motherInfo);
		// $this->TablePrint(Mother::Inc()->select());
		$this->assertNotEmpty($motherInfo);
		// var_dump($Mother->sql());
		// var_dump($Mother->info->sql());
	}

	/**
	 * 更新
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-06-14T14:58:27+0800
	 * @param    Mother                   $Mother [description]
	 * @return   [type]                           [description]
	 */
	public function UpdateRelation(){
		$Mother=new Mother;
		$Mother->id=12;
		$Mother->name="relation_test关联更新测试".rand();
		$Mother->info=new MotherInfo([
				"email"=>"relationupdate@qq.com"
			]);
		$Mother->relUpdate();
		$data=MotherInfo::Inc()->whereEq("email","relationupdate@qq.com")->find();
		// $this->TablePrint(MotherInfo::Inc()->select());
		$this->assertNotEmpty($data);
		// var_dump($Mother->sql());
		// var_dump($Mother->info->sql());
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
		$Mother->info=MotherInfo::Inc(["mid"=>12]);
		$Mother->relDelete();
		$data=MotherInfo::Inc()->whereEq("mid",12)->find();
		// $this->TablePrint($data);
		$this->assertEmpty($data);
		// var_dump($Mother->sql());
		// var_dump($Mother->info->sql());
	}

	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-07-04T14:23:19+0800
	 * @return   [type]                   [description]
	 */
	public function getRelationInfo(){
		// var_dump($this->getRelation(Mother::Inc()));
	}

	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-07-04T15:47:14+0800
	 * @param    string                   $value [description]
	 * @return   [type]                          [description]
	 */
	public function selectRelation($value='')
	{
		$motherinfo=Mother::Inc()->limit(1,3)->relSelect();
		// var_export($motherinfo);
		// var_dump(Mother::lastInc()->sql());
		// var_dump(Mother::lastInc()->info->sql());
		$this->assertNotEmpty(array_shift($motherinfo)['info']);
	}

	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-07-30T11:10:45+0800
	 * @return   [type]                   [description]
	 */
	public function selectRelationMatchRel()
	{
		$data=Marry::Inc()->relSelect();
		// var_dump($data);
		Marry::Inc([
			"id"=>1,
			"mid"=>1,
			"pid"=>1
		])->relDelete();
		$this->assertEmpty(Mother::Inc(["id"=>1])->select());
		$this->assertEmpty(Parents::Inc(["id"=>1])->select());
		$this->assertEmpty(Marry::Inc(["id"=>1])->select());
		$this->assertEmpty(MotherInfo::Inc(["id"=>1])->select());
		// $this->TablePrint(MotherInfo::Inc()->select());
		// $this->TablePrint(Parents::Inc()->select());
	}


	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-07-31T11:01:53+0800
	 * @return   [type]                   [description]
	 */
	public function relationUpdateMany(){
		$marry=new Marry;
		$marry->id=2;
		$marry->mid=3;
		$marry->pid=3;
		$marry->parent=Parents::Inc(["name"=>"this is update"]);
		$marry->mother=Mother::Inc(["name"=>"this is update"]);
		$marry->motherInfo=MotherInfo::Inc(["email"=>"this is update"]);
		$marry->relUpdate();
		$this->assertEquals(Mother::Inc(["id"=>3,"name"])->find("name"),"this is update");
		$this->assertEquals(Parents::Inc(["id"=>3,"name"])->find("name"),"this is update");
		$this->assertEquals(MotherInfo::Inc(["mid"=>3,"email"])->find("email"),"this is update");
	}
}
