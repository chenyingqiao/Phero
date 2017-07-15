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
        // DI::inj("dbhelp",new SwooleMysqlDbHelp());
        // Mother::Inc()->select();
        $SwooleMysqlDbHelp=new SwooleMysqlDbHelp();
        $data=$SwooleMysqlDbHelp->queryResultArray("show tables");
        var_dump($data);
    }
}
