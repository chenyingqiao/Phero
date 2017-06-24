<?php 

namespace Phero\Database\Traits\UnitTrait;
/**
 * @Author: lerko
 * @Date:   2017-06-02 16:24:50
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-06 16:39:34
 */
trait HavingUnitTrait{
	private $having=[];
	public function getHaving(){
		return $this->having;
	}
	/**
	 * 收集having的信息
	 * @Author   Lerko
	 * @DateTime 2017-06-06T16:14:29+0800
	 * @param    [type]                   $having     [description]
	 * @param    [type]                   $from       [description]
	 * @param    boolean                  $group      [description]
	 * @param    string                   $havingTemp [description]
	 * @return   [type]                               [description]
	 */
	public function having($having, $from = null, $group = false, $havingTemp = "") {
		if (isset($from)) {
			$having['from'] = $from;
		}
		if($this->whereGroup!==false)
			$group = $this->whereGroup;
		//这里的wheregroup是通过where进行添加的
		if ($group !== false) {
			$having['group'] = $group;
		}
		if (!empty($whereTemp)) {
			$having['temp'] = $havingTemp;
		}
		$this->having[] = $having;
		return $this;
	}
}