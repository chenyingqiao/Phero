<?php 

namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Mother;
use Phero\Database\Enum\FetchType;
use Phero\Database\Model;
use Phero\System\DI;

/**
 * @Author: lerko
 * @Date:   2017-05-31 14:23:48
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-02 12:04:39
 */

class InsertTest extends BaseTest
{
	/**
	 * 测试普通单条插入
	 * @Author   Lerko
	 * @DateTime 2017-05-31T14:50:07+0800
	 * @return   [type]                   [description]
	 */
	public function testDefaultInsertOne(){
		$Unit=new Mother();
		$name="now time".rand();
		$Unit->name=$name;
		$result=$Unit->fetchSql(FetchType::insert);
		$sql[]=$Unit->sql();
		$this->assertEquals($sql[0],"insert into Mother (`name`) values ('{$name}');");
		$Unit->truncate();
		$this->TablePrint($sql);
		$this->TablePrint($result);
	}

	/**
	 * 多次插入  构建多个语句
	 * 插入后检查插入的数量是否正确
	 * @Author   Lerko
	 * @DateTime 2017-06-01T09:36:45+0800
	 * @return   [type]                   [description]
	 */
	public function testDefaultInsertMany(){
		$t1=microtime(true);
		$sql=[];
		for ($i=0; $i < 100; $i++) { 
			$Unit=new Mother(['name'=>"now{$i}"]);
			$result=$Unit->insert();
			$this->assertEquals(true,$result);
			$sql[]=$Unit->sql();
		}
		$result=$Unit->count();
		$this->assertEquals($result,100);
		$this->TablePrint($Unit->sql());
		$this->TablePrint($sql);
		$Unit->truncate();
		$t2=microtime(true);
		echo "\n耗时".round($t2-$t1,5)."秒\n";
	}

	/**
	 * 插入多个数据控制 使用一条sql语句
	 * @Author   Lerko
	 * @DateTime 2017-06-01T09:43:14+0800
	 * @return   [type]                   [description]
	 */
	public function testDefaultInsertManyInOneSql(){
		$t1=microtime(true);
		$Units=[];
		for ($i=0; $i < 100; $i++) { 
			$Unit=new Mother(['name'=>"now{$i}"]);
			$Units[]=$Unit;
		}
		$model=new Model();
		$result=$model->insert($Units);
		$this->assertEquals(true,$result);
		$count=$Units[0]->count();
		$this->assertEquals(100,$count);
		$sql=$model->getSql();
		$this->TablePrint($sql);
		//清除表数据
		$Units[0]->truncate();
		$t2=microtime(true);
		echo "\n耗时".round($t2-$t1,5)."秒\n";
	}

	/**
	 * @Author   Lerko
	 * @DateTime 2017-06-01T10:17:54+0800
	 * @return   [type]                   [description]
	 */
	public function InsertTransation(){
	}
}