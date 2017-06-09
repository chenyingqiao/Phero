<?php 

namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Parents;
use Phero\Cache\CacheOperationByConfig;
use Phero\Database\Enum\Cache;
use Phero\Database\Model;
/**
 * @Author: lerko
 * @Date:   2017-06-08 16:45:36
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-09 10:42:34
 */
class SelectCacheTest extends BaseTest
{
	/**
	 * 插入一百万条记录之后进行查询测试
	 * @Author   Lerko
	 * @DateTime 2017-06-08T18:00:58+0800
	 * @return   [type]                   [description]
	 */
	public function testCache(){
		echo "begin";
		$this->timer();
		// Parents::Inc()->truncate();
		// $UnitsParent=[];
		//insert one million data
		// for ($i=0; $i < 10000000; $i++) {
		// 	$parentsName="parent{$i}";
		// 	$UnitsParent[]=new Parents(["name"=>$parentsName]);
		// 	if($i%1000==0&&$i>=1000){
		// 		(new Model)->insert($UnitsParent);
		// 		echo "=======插入{$i}条数据===========\n";
		// 		unset($UnitsParent);
		// 		$UnitsParent=[];
		// 	}
		// }
		$sql="";
		Parents::Inc()->whereLike("name","parent1__");
		Parents::lastInc()->fetchSql($sql);
		$data=Parents::lastInc()->select(new Cache(10));
		// $this->TablePrint($data);
		// $this->TablePrint(CacheOperationByConfig::read(md5($sql)));
		echo $sql;
		// Parents::Inc()->truncate();
		$this->timer(false,"耗时:");
	}
}