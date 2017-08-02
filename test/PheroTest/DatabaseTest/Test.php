<?php
namespace PheroTest;

use PHPUnit\Framework\TestCase;
use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit as unit;
use PheroTest\DatabaseTest\Unit\Children;
use PheroTest\DatabaseTest\Unit\Marry;
use PheroTest\DatabaseTest\Unit\Mother;
use PheroTest\DatabaseTest\Unit\MotherInfo;
use PheroTest\DatabaseTest\Unit\ParentInfo;
use PheroTest\DatabaseTest\Unit\Parents;
use Phero\Database\DbUnit;
use Phero\Database\Model;
use Phero\Map\NodeReflectionClass;
use Phero\System\Config;
use Phero\System\DI;

class Test extends TestCase {
    /**
     * @Author   Lerko
     * @DateTime 2017-07-26T15:41:32+0800
     * @return   [type]                   [description]
     */
    public function createTestData(){
        DI::inj(DI::config,"/home/lerko/Desktop/config.php");
        (new Parents)->truncate();
        (new Mother)->truncate();
        (new Marry)->truncate();
        (new ParentInfo)->truncate();
        (new MotherInfo)->truncate();
        (new Children)->truncate();
        for ($i=0; $i < 4; $i++) {
            $parentsName="parent{$i}";
            $motherName="mother{$i}";
            $UnitsParent[]=new Parents(["name"=>$parentsName]);
            $UnitsMother[]=new Mother(["name"=>$motherName]);
            $UnitsMarry[]=new Marry(["pid"=>$i+1,"mid"=>$i+1]);
            $UnitsParentInfo[]=new ParentInfo(["pid"=>$i+1,"phone"=>"1506013{$i}03"]);
            $UnitsMotherInfo[]=new MotherInfo(["mid"=>$i+1,"email"=>"6143257{$i}@qq.com"]);
            $UnitsChildren[]=new Children(['name'=>"小明{$i}","marry_id"=>$i+1]);
        }
        $Model=new Model();
        $Model->insert($UnitsParent);
        $Model->insert($UnitsMother);
        var_dump($Model->getError());
        var_dump($Model->getSql());
        $Model->insert($UnitsMarry);
        $Model->insert($UnitsParentInfo);
        $Model->insert($UnitsMotherInfo);
        $Model->insert($UnitsChildren);
    }

    /**
     * @test
     * @Author   Lerko
     * @DateTime 2017-07-31T18:42:01+0800
     */
    public function MasterSalveTest(){
        DI::inj(DI::config,"/home/lerko/Desktop/config.php");
        Mother::Inc(["name"=>"test"])->insert();
        $data=Mother::Inc()->select();
        var_dump($data);
    }
}