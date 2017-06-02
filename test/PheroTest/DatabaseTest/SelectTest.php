<?php 

namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Marry;
use PheroTest\DatabaseTest\Unit\Mother;
use PheroTest\DatabaseTest\Unit\Parents;
use Phero\Database\Enum\Where;
use Phero\Database\Enum\WhereCon;
use Phero\Database\Model;

/**
 * @Author: lerko
 * @Date:   2017-05-27 16:14:54
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-02 17:53:38
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
		for ($i=0; $i < 100; $i++) { 
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
		$this->assertEquals($result, 100);
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
		$this->TablePrint($result);
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
		$Marry->join($Parents,"$.pid=#.id");
		$Marry->join($Mother,"$.mid=#.id");
		$result=$Marry->fetchSql();
		$this->TablePrint($result);
		$this->assertEquals($Marry->sql(), "select `Marry`.`id`,`Marry`.`pid`,`Marry`.`mid`,`parent`.`id`,`parent`.`name`,`Mother`.`id`,`Mother`.`name` from `Marry` inner join `Parent` as `parent` on `Marry`.pid=`parent`.id  inner join `Mother` on `Marry`.mid=`Mother`.id ;");
	}

	/**
	 * 车市exists
	 * @Author   Lerko
	 * @DateTime 2017-06-02T14:35:40+0800
	 * @return   [type]                   [description]
	 */
	public function testSelectExists(){
		$Parents=new Parents();
		$Marry=new Marry();
		$Mother=new Mother(["id"]);
		$Mother->whereBetween("id",[1,10]);
		$Marry->whereEq("pid","#.id");
		$Marry->whereAndIn("id",$Mother);
		$result=$Parents
			->whereEq("id",1)
			->whereOrExists($Marry)
			->fetchSql();
		$this->TablePrint($result);
		echo $Parents->sql();
		$this->assertEquals($Parents->sql(),"select `parent`.`id`,`parent`.`name` from `Parent` as `parent` where `parent`.`id` = 1  or  exists (select `Marry`.`id`,`Marry`.`pid`,`Marry`.`mid` from `Marry` where `Marry`.`pid` = `parent`.id  or `Marry`.`id` in (select `Mother`.`id` from `Mother` where `Mother`.`id` between 1 AND 10));");
	}

	public function testWhereFunction(){
	}
}