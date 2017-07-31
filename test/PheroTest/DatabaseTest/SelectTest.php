<?php

namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Children;
use PheroTest\DatabaseTest\Unit\Marry;
use PheroTest\DatabaseTest\Unit\Mother;
use PheroTest\DatabaseTest\Unit\MotherInfo;
use PheroTest\DatabaseTest\Unit\Parents;
use Phero\Database\Db;
use Phero\Database\DbUnit;
use Phero\Database\Enum\OrderType;
use Phero\Database\Enum\Where;
use Phero\Database\Enum\WhereCon;
use Phero\Database\Model;

/**
 * @Author: lerko
 * @Date:   2017-05-27 16:14:54
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-07-31 16:55:55
 */
class SelectTest extends BaseTest
{
	/**
	 * @Author   Lerko
	 * @DateTime 2017-06-02T10:36:59+0800
	 * @return   [type]                   [description]
	 */
	public function getParentTableData(){
		$data=[];
		for ($i=0; $i < 10; $i++) {
			$data[]=["id"=>$i+1,"name"=>"parent{$i}"];
		}
		return $data;
	}

	/**
	 * @Author   Lerko
	 * @DateTime 2017-06-01T13:52:39+0800
	 * @return   [type]                   [description]
	 */
	public function testCount(){
		$this->timer();
		$Parents=new Parents();
		$result=$Parents->count();
		$this->assertEquals($result, 10);
		// echo $Parents->sql();
		$this->timer(false,__METHOD__);
	}

	public function testIntField(){
		$data=Mother::Inc(["id"=>2,"name"])->select();
		$this->TablePrint(Mother::Inc()->select());
		var_dump($data);
		$this->assertArrayHasKey("name",$data[0]);
	}

	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-07-27T18:11:31+0800
	 * @return   [type]                   [description]
	 */
	public function query(){
		$data=Db::exec("insert into Mother(name) values (:name);",["name"=>"exec_text"]);
		// var_dump(Db::error());

		$data2=Db::queryResultArray("select * from Mother where id=:id",["id"=>1]);
		// var_dump(Db::error());
		// $this->TablePrint($data2);

		$mother=new Mother(["name"=>"test".rand(1,100)]);
		Db::insert($mother);

		$mother=new Mother(["id"=>11,"name"=>"test".rand(1,100)]);
		Db::update($mother);

		$mother=new Mother(["id"=>2]);
		Db::delete($mother);
		// var_dump(Db::getSql());

		$mother=new Mother();
		$data_select=Db::select($mother);
		// $this->TablePrint($data_select);

	}

	/**
	 * 单个查询
	 * @Author   Lerko
	 * @DateTime 2017-06-02T10:38:55+0800
	 * @return   [type]                   [description]
	 */
	public function testSelectOneTable(){
		$data=$this->getParentTableData();
		$Parents=new Parents();
		$result=$Parents->select();
		$this->assertEquals($result, $data);

		Mother::Inc()->select();
	}

	/**
	 * 测试where
	 * @Author   Lerko
	 * @DateTime 2017-06-02T11:27:19+0800
	 * @return   [type]                   [description]
	 */
	public function testSelectOneTableWhere(){
		$Parents=new Parents();
		$result=$Parents
			->where(["id",10,Where::$eq_],null,1,"Fun(?)")
			->where(["name","%test%",Where::$like,WhereCon::and_],null,2,"Fun2(?)")
			->fetchSql();
		$sql=$Parents->sql();
		//$this->TablePrint($result);
		$this->assertEquals($sql,
			"select `parent`.`id`,`parent`.`name` from `Parent` as `parent` where Fun(`parent`.`id`) = 10  and Fun2(`parent`.`name`) like '%test%';");
	}

	public function testSelectWhereClaver(){
		Mother::Inc()->Set(function(){
			$this->whereEq("id",1)->whereOrLike("name","sss_");
			return $this;
		},WhereCon::or_)->Set(function(){
			$this->whereEq("id",2)->whereOrLike("name","ddd_");
			return $this;
		})->fetchSql();
		// echo Mother::lastInc()->sql();
	}


	/**
	 * [testSelectJoin description]
	 * @Author   Lerko
	 * @DateTime 2017-06-02T11:43:25+0800
	 * @return   [type]                   [description]
	 */
	public function testSelectJoin(){
		$Parents=new Parents();
		$Marry=new Marry();
		$Mother=new Mother();
		$Marry->join($Parents,"$.`pid`=#.`id`");
		$Marry->join($Mother,"$.`mid`=#.`id`");
		$MarryClone=clone $Marry;
		$result=$Marry->fetchSql();
		//$this->TablePrint($result);
		$this->assertEquals($Marry->sql(), "select `Marry`.`id`,`Marry`.`pid`,`Marry`.`mid`,`parent`.`id`,`parent`.`name`,`mother`.`id`,`mother`.`name` from `Marry` inner join `Parent` as `parent` on `Marry`.`pid`=`parent`.`id`  inner join `Mother` as `mother` on `Marry`.`mid`=`mother`.`id` ;");
		return $MarryClone;
	}

	/**
	 * 测试exists
	 * @Author   Lerko
	 * @DateTime 2017-06-02T14:35:40+0800
	 * @return   [type]                   [description]
	 */
	public function testSelectExists(){
		$Parents=new Parents();
		$Marry=new Marry();
		$Mother=new Mother(["id"]);
		$Mother->whereBetween("id",[1,10]);
		$Marry->whereEq("pid","#.`id`");
		$Marry->whereAndIn("id",$Mother);
		$Parents->whereEq("id",1)
			->whereOrExists($Marry);
		$result=$Parents->fetchSql();
		//$this->TablePrint($result);
		$this->assertEquals($Parents->sql(),"select `parent`.`id`,`parent`.`name` from `Parent` as `parent` where `parent`.`id` = 1  or  exists (select `Marry`.`id`,`Marry`.`pid`,`Marry`.`mid` from `Marry` where `Marry`.`pid` = `parent`.`id`  and `Marry`.`id` in (select `mother`.`id` from `Mother` as `mother` where `mother`.`id` between 1 AND 10));");
	}

	/**
	 * 设置field
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-06-07T14:17:56+0800
	 * @return   [type]                   [description]
	 */
	public function selectField(){
		$sql="";
		$data=Mother::Inc(["name"])->fetchSql($sql);
		$this->assertEquals($sql,"select `mother`.`name` from `Mother` as `mother`;");
	}

	/**
	 * @depends clone testSelectJoin
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-06-07T14:28:29+0800
	 * @return   [type]                   [description]
	 */
	public function selectJoinField(Marry $marry){
		$sql="";
		$id=Mother::FF("id");
		$marry->field("ThisIsMyFuckingFun($id)","fuckFun")->field("Fun2($id)","fcun2")->fetchSql($sql);
		$this->assertEquals($sql,"select ThisIsMyFuckingFun(`mother`.`id`) as fuckFun,Fun2(`mother`.`id`) as fcun2,`Marry`.`id`,`Marry`.`pid`,`Marry`.`mid`,`parent`.`id`,`parent`.`name`,`mother`.`id`,`mother`.`name` from `Marry` inner join `Parent` as `parent` on `Marry`.`pid`=`parent`.`id`  inner join `Mother` as `mother` on `Marry`.`mid`=`mother`.`id` ;");
	}

	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-06-29T13:53:38+0800
	 * @return   [type]                   [description]
	 */
	public function selectObjectField(){
		$sql="";
		$id=Mother::FF("id");
		MotherInfo::Inc(false)->field("email")->field("Fun2($id)","f2")->whereEq(MotherInfo::FF("mid"),Mother::FF("id"));
		Mother::Inc()->field(MotherInfo::lastInc(),"email")->field("MFUnc($id)","mf2")->fetchSql($sql);
		$this->assertEquals($sql,"select (select `MotherInfo`.`email`,Fun2(`mother`.`id`) as f2 from `MotherInfo` where `MotherInfo`.`mid` = '`mother`.`id`') as email,MFUnc(`mother`.`id`) as mf2,`mother`.`id`,`mother`.`name` from `Mother` as `mother`;");
	}


	/**
	 * @depends clone testSelectJoin
	 * @Author   Lerko
	 * @DateTime 2017-06-06T13:49:09+0800
	 * @test
	 * @return   [type]                   [description]
	 */
	public function Order(Marry $marry){
		$marry->order(Mother::FF("id"),OrderType::desc);
		$sql=$marry->fetchSql();
		//$this->TablePrint($sql);
		$this->assertEquals($marry->sql(),"select `Marry`.`id`,`Marry`.`pid`,`Marry`.`mid`,`parent`.`id`,`parent`.`name`,`mother`.`id`,`mother`.`name` from `Marry` inner join `Parent` as `parent` on `Marry`.`pid`=`parent`.`id`  inner join `Mother` as `mother` on `Marry`.`mid`=`mother`.`id`  order by `mother`.`id` desc;");
	}

	/**
	 * @depends clone testSelectJoin
	 * @Author   Lerko
	 * @DateTime 2017-06-06T16:00:15+0800
	 * @param    Marry                    $marry [description]
	 * @return   [type]                          [description]
	 */
	public function testGroupByAndHaveing(Marry $marry){
		$sql="";
		$marry->sum("id")->group(Mother::FF("id"))->havingEq(Mother::FF("id"),1)->fetchSql($sql);
		//$this->TablePrint($sql);
		$this->assertEquals($sql,"select sum(`Marry`.`id`) as sum_id,`Marry`.`id`,`Marry`.`pid`,`Marry`.`mid`,`parent`.`id`,`parent`.`name`,`mother`.`id`,`mother`.`name` from `Marry` inner join `Parent` as `parent` on `Marry`.`pid`=`parent`.`id`  inner join `Mother` as `mother` on `Marry`.`mid`=`mother`.`id`  group by `mother`.`id` having  `mother`.`id` = 1;");
	}

	/**
	 * @Author   Lerko
	 * @DateTime 2017-06-06T17:53:16+0800
	 * @return   [type]                   [description]
	 */
	public function testSimpleGroupByAndHaving(){
		$data=Children::Inc()->whereIsNotNull("name")->limit(10)->group(Children::FF("pid"))->select();
		$sql= Children::lastInc()->sql();
		$this->assertEquals("select `children`.`id`,`children`.`name`,`children`.`pid` from `Children` as `children` where `children`.`name` is not null group by `children`.`pid` limit 10;",$sql);
	}

	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-07-30T09:14:12+0800
	 * @return   [type]                   [description]
	 */
	public function simplegrouphaving()
	{
		Mother::Inc()->sum("id")->group("name")->havingEq("name","test1")->fetchSql();
		// echo Mother::lastInc()->sql();
	}

	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-07-30T10:12:39+0800
	 * @return   [type]                   [description]
	 */
	public function simplesort()
	{
		Mother::Inc()->order("id",OrderType::asc)->fetchSql();
		echo Mother::lastInc()->sql();
	}
}
