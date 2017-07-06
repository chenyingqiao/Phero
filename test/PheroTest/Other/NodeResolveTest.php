<?php 

namespace PheroTest\Other;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Mother;
use Phero\Database\Traits\TConstraitTableDependent;
use Phero\Map\NodeReflectionClass;
use Phero\Map\Note\Table;
use Phero\System\Config;
/**
 * @Author: lerko
 * @Date:   2017-06-08 14:18:21
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-29 10:07:05
 */
class NodeResolveTest extends BaseTest
{	
	use TConstraitTableDependent;
	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-06-08T14:24:24+0800
	 * @return   [type]                   [description]
	 */
	public function resolveDebugEnable(){
		$this->timer();
		for ($i=0; $i < 100; $i++) { 
			$reflection=new NodeReflectionClass(new Mother);
			$table=$reflection->resolve(new Table);
		}
		$this->timer(false,"没有开启缓存耗时:");
	}

	/**
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-06-08T14:24:30+0800
	 * @return   [type]                   [description]
	 */
	public function resolveDebugDisable(){
		$this->timer();
		//关闭debug
		Config::config("debug",false);
		for ($i=0; $i < 100; $i++) { 
			$reflection=new NodeReflectionClass(new Mother);
			$table=$reflection->resolve(new Table);
		}
		$this->timer(false,"开启缓存耗时:");
	}

	/**
	 * 测试获取Property的name
	 * @test
	 * @Author   Lerko
	 * @DateTime 2017-06-28T11:56:48+0800
	 * @return   [type]                   [description]
	 */
	public function resolvePropertyName(){
		$name=$this->getPropertyNameClever(Mother::Inc(),"name");
		$this->assertEquals("name", $name);
	}
}