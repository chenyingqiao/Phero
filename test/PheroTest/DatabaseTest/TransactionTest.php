<?php

namespace PheroTest\DatabaseTest;
/**
 * @Author: lerko
 * @Date:   2017-07-11 19:39:04
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-07-31 09:12:44
 */

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Mother;
class Test extends BaseTest {
	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-07-11T19:41:11+0800
	 * @param    string                   $value [description]
	 * @return   [type]                          [description]
	 */
	public function transactionCommit()
	{
		Mother::Inc(["name"=>"kkk_transaction_commit"])->start()->insert();
		Mother::lastInc()->commit();
		// $mother=new Mother(["name"=>"kkk_transaction_commit"]);
		// $mother->start()->insert();
		// $mother->commit();
		$data=Mother::Inc()->whereEq("name","kkk_transaction_commit")->find();
		$this->TablePrint($data);
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
		$tabledata=Mother::Inc()->select();
		$this->TablePrint($tabledata);
		// var_dump($data);
		$this->assertEmpty($data);
	}

	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-07-29T19:07:52+0800
	 * @return   [type]                   [description]
	 */
	public function emptyT(){
		$this->assertEmpty([]);
	}
}
