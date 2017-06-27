<?php 

namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use Phero\Database\Db;
use Phero\Database\Realize\MysqlDbHelp;
/**
 * @Author: lerko
 * @Date:   2017-06-26 11:40:36
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-27 14:25:19
 */
class ModelTest extends BaseTest
{
	public function testSql(){
		$data=Db::queryResultArray("show tables");
		$this->assertEquals(count($data), 6);
	}
}