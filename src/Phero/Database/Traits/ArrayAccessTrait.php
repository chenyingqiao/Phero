<?php 
/**
 * @Author: ‘chenyingqiao’
 * @Date:   2017-04-08 10:25:35
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-04-08 10:44:19
 */
namespace Phero\Database\Traits;

trait ArrayAccessTrait implements \ArrayAccess{
	public offsetExists ( $offset ){
		return isset($this->$offset);
	}
	public offsetGet ( $offset ){
		return $this->$offset;
	}
	public offsetSet ( $offset , $value ){
		$this->$offset=$value;
	}
	public offsetUnset ( $offset ){
		unset($this->$offset);
	}
}