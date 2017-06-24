<?php 

namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Marry;
use PheroTest\DatabaseTest\Unit\Parents;
use Phero\Cache\CacheOperationByConfig;
use Phero\Database\Enum\Cache;
use Phero\Database\Model;
/**
 * @Author: lerko
 * @Date:   2017-06-08 16:45:36
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-12 17:32:58
 */
class SelectCacheTest extends BaseTest
{
	/**
	 * 插入一百万条记录之后进行查询测试
	 * @Author   Lerko
	 * @DateTime 2017-06-08T18:00:58+0800
	 * @return   [type]                   [description]
	 */
	public function Cache(){
		$this->timer();
		$UnitsParent=[];
		for ($i=0; $i < 10000000; $i++) {
			$parentsName="parent{$i}";
			$UnitsParent[]=new Parents(["name"=>$parentsName]);
			if($i%1000==0&&$i>=1000){
				(new Model)->insert($UnitsParent);
				echo "=======插入{$i}条数据===========\n";
				unset($UnitsParent);
				$UnitsParent=[];
			}
		}
		$sql="";
		Parents::Inc()->whereLike("name","parent1__");
		Parents::lastInc()->fetchSql($sql);
		// $data=Parents::lastInc()->select(new Cache(10));
		$data=Parents::lastInc()->select(Cache::time(100));
		// $this->TablePrint($data);
		// $this->TablePrint(CacheOperationByConfig::read(md5($sql)));
		echo $sql;
		// Parents::Inc()->truncate();
		$this->timer(false,"耗时:");
	}

	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-06-12T11:05:29+0800
	 * @return   [type]                   [description]
	 */
	public function testJoinBigTable(){
		$this->timer();
		$MarryJoinField=Marry::FF("pid");
		$ParentsJoinField=Parents::FF("id");
		$dataBigTable=Marry::Inc()->join(Parents::Inc(),"{$MarryJoinField}={$ParentsJoinField}")
			->whereLike(Parents::FF("name"),"parent1__")->select();
		var_dump(Marry::lastInc()->sql());
		var_dump(Marry::lastInc()->error());
		$this->TablePrint($dataBigTable);
		$this->timer(false,"小表join大表耗时：");
		$this->timer();
		$dataSmallTable=Parents::Inc()->join(Marry::Inc(),"{$MarryJoinField}={$ParentsJoinField}")
			->whereLike(Parents::FF("name"),"parent1__")->select();
		var_dump(Parents::lastInc()->sql());
		var_dump(Parents::lastInc()->error());
		$this->TablePrint($dataSmallTable);		
		$this->timer(false,"大表join小表耗时：");
	}
}