<?php
namespace Phero\Database\Realize\Hit;

use Phero\Database\Interfaces as interf;

/**
 *随机选取servlet来进行读
 */
class RandomSlaveHit implements interf\IDbServletHit {
	public function hit($ServletArr) {
		$length = count($ServletArr);
		$random_number = rand(0, $length - 1);
		return $ServletArr[$random_number];
	}
}
