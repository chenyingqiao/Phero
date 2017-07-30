<?php 

namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Marry;
use PheroTest\DatabaseTest\Unit\Parents;
use Phero\Cache\CacheOperationByConfig;
use Phero\Database\Db;
use Phero\Database\Enum\Cache;
use Phero\Database\Model;
use Phero\System\DI;
/**
 * @Author: lerko
 * @Date:   2017-06-08 16:45:36
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-07-30 17:10:05
 */
class SelectCacheTest extends BaseTest
{

	/**
	 * @beforeClass
	 * @Author   Lerko
	 * @DateTime 2017-07-30T17:00:06+0800
	 */
	public static function setUpConfig(){
		self::TablePrint("初始化数据库");
		DI::inj("config",dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");
	}
	public static function tearDownClearData(){}

	/**
	 * @test
	 * 插入一百万条记录之后进行查询测试
	 * @Author   Lerko
	 * @DateTime 2017-06-08T18:00:58+0800
	 * @return   [type]                   [description]
	 */
	public function Cache(){
		$this->timer();
		// $UnitsParent=[];
		// for ($i=0; $i < 100000; $i++) {
		// 	$parentsName="parent{$i}";
		// 	$UnitsParent[]=new Parents(["name"=>$parentsName]);
		// 	if($i%1000==0&&$i>=1000){
		// 		Db::insert($UnitsParent);
		// 		echo "=======插入{$i}条数据===========\n";
		// 		unset($UnitsParent);
		// 		$UnitsParent=[];
		// 	}
		// }
		$sql="";
		Parents::Inc()->whereLike("name","parent1_9");
		Parents::lastInc()->fetchSql($sql);
		// $data=Parents::lastInc()->select(new Cache(10));
		$data=Parents::lastInc()->select(Cache::time(10));
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
	public function JoinBigTable(){
		$this->timer();
		$MarryJoinField=Marry::FF("pid");
		$ParentsJoinField=Parents::FF("id");
		$dataBigTable=Marry::Inc()->join(Parents::Inc(),"{$MarryJoinField}={$ParentsJoinField}")
			->whereLike(Parents::FF("name"),"parent1__")->select();
		$this->TablePrint($dataBigTable);
		$this->timer(false,"小表join大表耗时：");
		$this->timer();
		$dataSmallTable=Parents::Inc()->join(Marry::Inc(),"{$MarryJoinField}={$ParentsJoinField}")
			->whereLike(Parents::FF("name"),"parent1__")->select();
		$this->TablePrint($dataSmallTable);		
		$this->timer(false,"大表join小表耗时：");
	}
}