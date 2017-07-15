<?php
namespace PheroTest\DatabaseTest;

use PheroTest\DatabaseTest\BaseTest;
use Phero\System\DI;
use Phero\Database\Realize\SwooleMysqlDbHelp;
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
}
