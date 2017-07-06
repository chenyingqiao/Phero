<?php
namespace Phero\Database\Realize\Hit;

use Phero\Database\Interfaces as interf;

/**
 *随机选取servlet来进行读
 */
class RandomslaveHit implements interf\IDbSlaveHit {
	public function hit($slave) {
		$length = count($ServletArr);
		$random_number = rand(0, $length - 1);
		return $ServletArr[$random_number];
	}
}