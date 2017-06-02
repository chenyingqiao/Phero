<?php 
namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Marry;
use PheroTest\DatabaseTest\Unit\Mother;
use PheroTest\DatabaseTest\Unit\Parents;
use Phero\Database\Model;
/**
 * @Author: lerko
 * @Date:   2017-06-02 12:00:16
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-02 12:13:52
 */
class ClearTest extends BaseTest
{
	/**
	 * @Author   Lerko
	 * @DateTime 2017-06-02T12:03:19+0800
	 * @return   [type]                   [description]
	 */
	public function testClearAll(){
		(new Parents)->truncate();
		(new Mother)->truncate();
		(new Marry)->truncate();
	}
}