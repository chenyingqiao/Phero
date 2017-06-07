<?php 

namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Mother;
/**
 * @Author: lerko
 * @Date:   2017-06-06 10:12:49
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-07 09:27:52
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
		Mother::lastInc()->whereEq("id",1)->update();
		$data=Mother::Inc()->select();
		$this->TablePrint($data);
	}
}