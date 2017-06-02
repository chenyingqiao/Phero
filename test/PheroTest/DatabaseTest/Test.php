<?php
namespace PheroTest;

use PheroTest\DatabaseTest\BaseTest;
use PheroTest\DatabaseTest\Unit as unit;

class Test extends BaseTest {
    public function testEmpty()
    {
        $stack = [];
        $this->assertEmpty($stack);
        echo "testEmpty";
        return $stack;
    }

    /**
     * @depends testEmpty
     */
    public function testPush(array $stack)
    {
        array_push($stack, 'foo');
        $this->assertEquals('foo', $stack[count($stack)-1]);
        $this->assertNotEmpty($stack);
        echo "testPush";
        return $stack;
    }

    /**
     * @depends testPush
     * @test
     */
    public function Pop(array $stack)
    {
        $this->assertEquals('foo', array_pop($stack));
        $this->assertEmpty($stack);
        echo "Pop";
    }

    /**
     * @Author   Lerko
     * @DateTime 2017-06-02T09:39:41+0800
     * @after
     */
    public function tearDownPop(){
        echo "{asdf}";
    }

    /**
     * @Author   Lerko
     * @DateTime 2017-06-02T09:39:41+0800
     * @after
     */
    public static function tearDowntestPush(){
        echo "{asdf2}";
    }
}