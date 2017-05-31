<?php 

namespace PheroTest;

use PHPUnit\Framework\TestCase;
use PheroTest\DatabaseTest\Unit\Mother;
use Phero\System\DI;

/**
 * @Author: lerko
 * @Date:   2017-05-31 14:23:48
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-05-31 15:54:14
 */

class InsertTest extends TestCase
{

	/**
	 * 测试普通单条插入
	 * @Author   Lerko
	 * @DateTime 2017-05-31T14:50:07+0800
	 * @return   [type]                   [description]
	 */
	public function testDefaultInsertOne(){
		$Unit=new Mother();
		$Unit->name="now time".time();
		$result=$Unit->insert();
		$sql=$Unit->sql();
		echo "\n{$sql}";
		$this->assertTrue($result);
	}
}