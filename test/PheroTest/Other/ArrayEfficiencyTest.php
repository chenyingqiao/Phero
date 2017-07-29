<?php 

namespace PheroTest\Other;

use PheroTest\DatabaseTest\BaseTest;
use Phero\Database\Enum\Where;
/**
 * @Author: lerko
 * @Date:   2017-06-19 18:05:05
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-07-29 22:12:24
 */
class ArrayEffciencyTest extends BaseTest
{
	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-07-29T22:05:59+0800
	 */
	public function WhereEmun()
	{
		echo Where::get("ltall");
		echo Where::$eq_;
	}
}