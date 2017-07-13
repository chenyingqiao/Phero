<?php

namespace PheroTest\DatabaseTest;
/**
 * @Author: lerko
 * @Date:   2017-07-11 19:39:04
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-07-13 10:34:11
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

	/**
	 * 多重嵌套
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-07-13T10:11:50+0800
	 * @param    string                   $value [description]
	 * @return   [type]                          [description]
	 */
	public function multipleTransaction()
	{
		$mother_name1="transaction".rand();
		$mother_name2="transaction".rand();
		$Mother1=new Mother(["name"=>$mother_name1]);

		$Mother1->start()->insert();
			$Mother1->name=$mother_name2;
			$Mother1->start()->insert();//这里外层事务自动被commit
			$Mother1->commit();
		$Mother1->commit();

		$data=$Mother1->select();
		var_dump($data);
	}
}
