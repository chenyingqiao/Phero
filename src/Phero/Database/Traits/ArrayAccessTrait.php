<?php 
/**
 * @Author: ‘chenyingqiao’
 * @Date:   2017-04-08 10:25:35
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-04-08 11:15:48
 */
namespace Phero\Database\Traits;

trait ArrayAccessTrait{
	public function offsetExists ( $offset ){
		return isset($this->$offset);
	}
	public function offsetGet ( $offset ){
		return $this->$offset;
	}
	public function offsetSet ( $offset , $value ){
		$this->$offset=$value;
	}
	public function offsetUnset ( $offset ){
		unset($this->$offset);
	}
}