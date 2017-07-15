<?php
namespace PheroTest\Other;

use PheroTest\DatabaseTest\BaseTest;
/**
 *
 */
class swooleMysqlTest extends BaseTest
{
    /**
     * @test
     * @method mysqlReflection
     * @return [type]          [description]
     */
    public function mysqlReflection()
    {
        $refletion = new \ReflectionClass("swoole_mysql");
        $mathod=$refletion->getMethods();
    }

    /**
     * @test
     * @method yieldfunc
     * @param  string    $value [description]
     * @return [type]           [description]
     */
    public function yieldfunc($value='')
    {
        $yfun=$this->promist();
        $yfun->send(function(){echo "this is fun\n";});
        $yfun->send(function(){echo "this is fun2\n";});
        $yfun->send(function(){echo "this is fun3";});
    }
    
    private function promist(){
        $fun=yield;
        $fun2=yield;
        $fun3=yield;
        $fun();
        $fun2();
        $fun3();
    }
}
