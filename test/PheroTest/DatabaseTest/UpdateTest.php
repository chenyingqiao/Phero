<?php 

namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Mother;
use Phero\Database\Enum\FetchType;
/**
 * @Author: lerko
 * @Date:   2017-06-06 10:12:49
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-07-31 08:17:59
 */
class UpdateTest extends BaseTest
{
	/**
	 * 正常更新测试
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-06-07T09:14:06+0800
	 */
	public function DefaultUpdate(){
		echo "updateTest\n";
		$Mother=Mother::Inc();
		$Mother->name="这个是更新之后的name";
		$sql="";
		Mother::lastInc()->whereEq("id",1)->fetchSql($sql,FetchType::update);
		$data=Mother::Inc()->select();
		$this->TablePrint($sql);
		$this->assertEquals($sql, "update `Mother` as mother set `name`='这个是更新之后的name' where `mother`.`id` = 1;");
	}

	/**
	 * 测试自动吧主键设置成更新或者删除的where
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-06-14T14:49:20+0800
	 */
	public function PrimaryKeyAutoWhere(){
		$Mother=Mother::Inc();
		$Mother->id=1;
		$Mother->name="这个是更新之后的name";
		$sql="";
		$data=Mother::lastInc()->fetchSql($sql,FetchType::update);
		$this->TablePrint($sql);
		$this->assertEquals($sql, "update `Mother` as mother set `id`=1,`name`='这个是更新之后的name' where `mother`.`id` = 1;");
	}
}