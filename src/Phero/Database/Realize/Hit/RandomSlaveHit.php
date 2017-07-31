<?php
namespace Phero\Database\Realize\Hit;

use Phero\Database\Interfaces as interf;

/**
 *随机选取servlet来进行读
 */
class RandomSlaveHit implements interf\IDbSlaveHit {
	public function &hit(&$slave) {
		$length = count($slave);
		if($length==1){
			return $slave[0];
		}
		$random_number = rand(0, $length - 1);
		return $slave[$random_number];
	}
}
