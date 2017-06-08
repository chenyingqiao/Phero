<?php 

namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Mother;
use Phero\Database\Enum\FetchType;
/**
 * @Author: lerko
 * @Date:   2017-06-06 10:12:49
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-07 14:09:46
 */
class UpdateTest extends BaseTest
{
	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-06-07T09:14:06+0800
	 */
	public function DefaultUpdate(){
		$Mother=Mother::Inc();
		$Mother->name="这个是更新之后的name";
		$sql="";
		Mother::lastInc()->whereEq("id",1)->fetchSql($sql,FetchType::update);
		$data=Mother::Inc()->select();
		$this->TablePrint($sql);
		$this->TablePrint($data);
		$this->assertEquals($sql, "update `Mother` set `name`='这个是更新之后的name' where `Mother`.`id` = 1;");
	}
}