<?php 

namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use Phero\Database\Realize\MysqlDbHelp;
/**
 * @Author: lerko
 * @Date:   2017-06-26 11:40:36
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-26 11:48:30
 */
class ModelTest extends BaseTest
{
	public function testSql(){
		$data=MysqlDbHelp::queryResultArray("show tables");
		$this->TablePrint($data);
	}
}