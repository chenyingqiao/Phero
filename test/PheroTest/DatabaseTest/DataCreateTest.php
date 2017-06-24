<?php 

namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Children;
use PheroTest\DatabaseTest\Unit\Marry;
use PheroTest\DatabaseTest\Unit\Mother;
use PheroTest\DatabaseTest\Unit\MotherInfo;
use PheroTest\DatabaseTest\Unit\ParentInfo;
use PheroTest\DatabaseTest\Unit\Parents;
use Phero\Database\Model;
/**
 * @Author: lerko
 * @Date:   2017-06-02 12:12:52
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-15 14:47:00
 */
class DataCreateTest extends BaseTest
{
	/**
	 * @Author   Lerko
	 * @DateTime 2017-06-02T12:02:59+0800
	 * @return   [type]                   [description]
	 */
	public function testCreateData(){
		(new Parents)->truncate();
		(new Mother)->truncate();
		(new Marry)->truncate();
		(new ParentInfo)->truncate();
		(new MotherInfo)->truncate();
		(new Children)->truncate();
		for ($i=0; $i < 10; $i++) {
			$parentsName="parent{$i}";
			$motherName="mother{$i}";
			$UnitsParent[]=new Parents(["name"=>$parentsName]);
			$UnitsMother[]=new Mother(["name"=>$motherName]);
			$UnitsMarry[]=new Marry(["pid"=>$i+1,"mid"=>$i+1]);
			$UnitsParentInfo[]=new ParentInfo(["pid"=>$i+1,"phone"=>"1506013{$i}03"]);
			$UnitsMotherInfo[]=new MotherInfo(["mid"=>$i+1,"email"=>"6143257{$i}@qq.com"]);
			$UnitsChildren[]=new Children(['name'=>"小明{$i}","marry_id"=>$i+1]);
			$UnitsChildren[]=new Children(['name'=>"小黄{$i}","marry_id"=>$i+1]);
		}
		$Model=new Model();
		$Model->insert($UnitsParent);
		$Model->insert($UnitsMother);
		$Model->insert($UnitsMarry);
		$Model->insert($UnitsParentInfo);
		$this->TablePrint([
				$Model->getSql(),
				$Model->getError()
			]);
		$Model->insert($UnitsMotherInfo);
		$this->TablePrint([
				$Model->getSql(),
				$Model->getError()
			]);
		$Model->insert($UnitsChildren);
		$this->TablePrint([
				$Model->getSql(),
				$Model->getError()
			]);

		echo "=============";
	}
}