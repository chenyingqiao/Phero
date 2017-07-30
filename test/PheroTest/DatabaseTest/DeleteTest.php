<?php 

namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Mother;
/**
 * @Author: ‘chenyingqiao’
 * @Date:   2017-07-30 13:14:59
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-07-30 13:16:53
 */
class DeleteTest extends BaseTest
{
	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-07-30T13:16:28+0800
	 * @return   [type]                   [description]
	 */
	public function simpleDelete()
	{
		$data=Mother::Inc(["id"=>2])->delete();
		var_dump($data);
		$this->TablePrint(Mother::Inc()->select());
	}
}