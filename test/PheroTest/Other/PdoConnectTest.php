 <?php
namespace PheroTest\Other;

use PHPUnit\Framework\TestCase;

/**
 * @Author: lerko
 * @Date:   2017-07-21 18:22:14
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-07-24 09:42:27
 */
class PdoConnectTest extends TestCase
{
	/**
	 * 
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-07-21T18:23:33+0800l
	 * @param    string                   $value [description]
	 * @return   [type]                          [description]
	 */
	public function n()
	{
		$pdo=new \PDO("mysql:dbname=phero","root","lerko",[\PDO::ATTR_PERSISTENT=>TRUE]);
	}
	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-07-21T18:23:40+0800
	 * @param    string                   $value [description]
	 * @return   [type]                          [description]
	 */
	public function l()
	{
		$pdo=new \PDO("mysql:dbname=phero","root","lerko");
		
	}
}