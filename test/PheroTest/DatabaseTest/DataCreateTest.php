<?php 

namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit\Marry;
use PheroTest\DatabaseTest\Unit\Mother;
use PheroTest\DatabaseTest\Unit\Parents;
use Phero\Database\Model;
/**
 * @Author: lerko
 * @Date:   2017-06-02 12:12:52
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-02 13:03:01
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
		$UnitsParent=[];
		$UnitsMother=[];
		$UnitsMarry=[];
		for ($i=0; $i < 100; $i++) {
			$parentsName="parent{$i}";
			$motherName="mother{$i}";
			$UnitsParent[]=new Parents(["name"=>$parentsName]);
			$UnitsMother[]=new Mother(["name"=>$motherName]);
			$UnitsMarry[]=new Marry(["pid"=>$i+1,"mid"=>$i+1]);
		}
		(new Model())->insert($UnitsParent);
		(new Model())->insert($UnitsMother);
		(new Model())->insert($UnitsMarry);

		$this->TablePrint($UnitsParent[0]->select());
		$this->TablePrint($UnitsMother[0]->select());
		$this->TablePrint($UnitsMarry[0]->select());
	}
}