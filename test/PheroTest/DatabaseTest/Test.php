<?php
namespace PheroTest;

use PheroTest\DatabaseTest\Unit as unit;

class Test extends \PHPUnit_Framework_TestCase {
	public function testSelect() {
		$video_user = new unit\video_user();
		$video_user_list = $video_user->select();
		$this->assertEmpty($video_user_list);
		// return $video_user_list;
	}
	public function testPushAndPop() {
		$stack = [];
		$this->assertEquals(0, count($stack));

		array_push($stack, 'foo');
		$this->assertEquals('foo', $stack[count($stack) - 1]);
		$this->assertEquals(1, count($stack));

		$this->assertEquals('foo', array_pop($stack));
		$this->assertEquals(0, count($stack));
	}

	/**
	 * @test
	 */
	public function Stringlen() {
		$str = 'abc';
		$this->assertEquals(3, strlen($str));
	}
}