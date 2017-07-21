<?php
namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
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
        Config::config("persistent",true);
        for ($i=0; $i < 1; $i++) { 
            $data=$SwooleMysqlDbHelp->queryResultArray("show tables");
            // $this->TablePrint($data);
            $this->assertNotEmpty($data);
        }
    }

    /**
     * 测试持久化链接
     * @Author   Lerko
     * @DateTime 2017-07-21T18:11:39+0800
     * @return   [type]                   [description]
     */
    public function pdoPersistent(){

    }

    /**
     * 测试普通pdo
     * @Author   Lerko
     * @DateTime 2017-07-21T18:12:33+0800
     * @return   [type]                   [description]
     */
    public function pdoNormal()
    {
        # code...
    }
}
