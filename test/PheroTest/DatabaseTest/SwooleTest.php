<?php
namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\BuildUnit\Mother;
use Phero\Database\Realize\SwooleMysqlDbHelp;
use Phero\System\Config;
use Phero\System\DI;
/**
 *
 */
class SwooleTest extends BaseTest
{
    /**
     * @test
     * @method swooleSelect
     * @return [type]       [description]
     */
    public function swooleSelect()
    {
        $SwooleMysqlDbHelp=new SwooleMysqlDbHelp();
        $data=$SwooleMysqlDbHelp->queryResultArray("show tables");
        $this->assertNotEmpty($data);
    }

    /**
     * @test
     * @Author   Lerko
     * @DateTime 2017-07-27T16:44:22+0800
     * @return   [type]                   [description]
     */
    public function swooleSelectUnit(){
        DI::inj("dbhelp",new SwooleMysqlDbHelp);
        $data=Mother::Inc()->select();
        $this->TablePrint($data);
        $this->assertNotEmpty($data);
    }

    /**
     * @test
     * @Author   Lerko
     * @DateTime 2017-07-27T16:52:42+0800
     * @return   [type]                   [description]
     */
    public function swooleExec(){
        DI::inj("dbhelp",new SwooleMysqlDbHelp);
        $effect=Mother::Inc(['id'=>1])->delete();
        $data=Mother::Inc()->select();
        $this->TablePrint($data);
        $effect=Mother::Inc(['name'=>"ying89"])->insert();
        $data=Mother::Inc()->select();
        $this->TablePrint($data);
        $effect=Mother::Inc(["id"=>2,'name'=>"ying8923"])->update();
        $data=Mother::Inc()->select();
        $this->TablePrint($data);
    }
}
