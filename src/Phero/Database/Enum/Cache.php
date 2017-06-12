<?php 

namespace Phero\Database\Enum;
/**
 * @Author: lerko
 * @Date:   2017-06-08 17:24:28
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-12 10:27:55
 */
class Cache{
	public $liveTime=null;
	public function __construct($liveTime=null){
		$this->liveTime=$liveTime;
	}

	public static function time($time){
		return new Cache($time);
	}
}