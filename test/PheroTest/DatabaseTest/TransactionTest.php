<?php

namespace PheroTest\DatabaseTest;
/**
 * @Author: lerko
 * @Date:   2017-07-11 19:39:04
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-07-15 08:35:12
 */

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Mother;
class Test extends BaseTest {
	/**
	 * @Author   Lerko
	 * @DateTime 2017-07-11T19:41:11+0800
	 * @param    string                   $value [description]
	 * @return   [type]                          [description]
	 */
	public function transactionCommit()
	{
		Mother::Inc(["name"=>"kkk_transaction_commit"])->start()->insert();
		Mother::lastInc()->commit();
		$data=Mother::Inc()->whereEq("name","kkk_transaction_commit")->find();
		$this->assertNotEmpty($data);
	}

	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-07-11T19:43:23+0800
	 * @return   [type]                   [description]
	 */
	public function transactionRollback(){
		Mother::Inc(["name"=>"kkk_transaction_rollback"])->start()->insert();
		Mother::lastInc()->rollback();
		$data=Mother::Inc()->whereEq("name","kkk_transaction_rollback")->find();
		$this->assertEmpty($data);
	}
}
