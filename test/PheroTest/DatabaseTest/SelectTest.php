<?php 

namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Children;
use PheroTest\DatabaseTest\Unit\Marry;
use PheroTest\DatabaseTest\Unit\Mother;
use PheroTest\DatabaseTest\Unit\Parents;
use Phero\Database\DbUnit;
use Phero\Database\Enum\OrderType;
use Phero\Database\Enum\Where;
use Phero\Database\Enum\WhereCon;
use Phero\Database\Model;

/**
 * @Author: lerko
 * @Date:   2017-05-27 16:14:54
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-08 16:59:37
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
		$this->timer(false,__METHOD__);
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
			->where(["id",10,Where::eq_],null,1,"Fun(?)")
			->where(["name","%test%",Where::like,WhereCon::and_],null,2,"Fun2(?)")
			->fetchSql();
		$sql=$Parents->sql();
		//$this->TablePrint($result);
		$this->assertEquals($sql,
			"select `parent`.`id`,`parent`.`name` from `Parent` as `parent` where (Fun(`parent`.`id`) = 10  and Fun2(`parent`.`name`) like '%test%');");
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
		$this->assertEquals($Marry->sql(), "select `Marry`.`id`,`Marry`.`pid`,`Marry`.`mid`,`parent`.`id`,`parent`.`name`,`Mother`.`id`,`Mother`.`name` from `Marry` inner join `Parent` as `parent` on `Marry`.`pid`=`parent`.`id`  inner join `Mother` on `Marry`.`mid`=`Mother`.`id` ;");
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
		$this->assertEquals($Parents->sql(),"select `parent`.`id`,`parent`.`name` from `Parent` as `parent` where `parent`.`id` = 1  or  exists (select `Marry`.`id`,`Marry`.`pid`,`Marry`.`mid` from `Marry` where `Marry`.`pid` = `parent`.`id`  and `Marry`.`id` in (select `Mother`.`id` from `Mother` where `Mother`.`id` between 1 AND 10));");
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
		$this->assertEquals($sql,"select `Mother`.`name` from `Mother`;");
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
		$marry->field("ThisIsMyFuckingFun($id)")->fetchSql($sql);
		$this->assertEquals($sql,"select ThisIsMyFuckingFun(`Mother`.`id`),`Marry`.`id`,`Marry`.`pid`,`Marry`.`mid`,`parent`.`id`,`parent`.`name`,`Mother`.`id`,`Mother`.`name` from `Marry` inner join `Parent` as `parent` on `Marry`.`pid`=`parent`.`id`  inner join `Mother` on `Marry`.`mid`=`Mother`.`id` ;");
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
		$this->assertEquals($marry->sql(),"select `Marry`.`id`,`Marry`.`pid`,`Marry`.`mid`,`parent`.`id`,`parent`.`name`,`Mother`.`id`,`Mother`.`name` from `Marry` inner join `Parent` as `parent` on `Marry`.`pid`=`parent`.`id`  inner join `Mother` on `Marry`.`mid`=`Mother`.`id`  order by `Mother`.`id` desc;");
	}

	/**
	 * @depends clone testSelectJoin
	 * @Author   Lerko
	 * @DateTime 2017-06-06T16:00:15+0800
	 * @param    Marry                    $marry [description]
	 * @return   [type]                          [description]
	 */
	public function testGroupByAndHave(Marry $marry){
		$sql="";
		$marry->sum("id")->group(Mother::FF("id"))->havingEq(Mother::FF("id"),1)->fetchSql($sql);
		//$this->TablePrint($sql);
		$this->assertEquals($sql,"select `Marry`.`sum(id) as sum_id`,`Marry`.`id`,`Marry`.`pid`,`Marry`.`mid`,`parent`.`id`,`parent`.`name`,`Mother`.`id`,`Mother`.`name` from `Marry` inner join `Parent` as `parent` on `Marry`.`pid`=`parent`.`id`  inner join `Mother` on `Marry`.`mid`=`Mother`.`id`  group by `Mother`.`id` having  `Mother`.`id` = 1;");
	}

	/**
	 * @Author   Lerko
	 * @DateTime 2017-06-06T17:53:16+0800
	 * @return   [type]                   [description]
	 */
	public function testSimpleGroupByAndHaving(){
		$data=Children::Inc()->limit(10)->group(Children::FF("pid"))->select();
	}

}