<?php 

namespace Phero\Database\Traits\UnitTrait;
/**
 * @Author: lerko
 * @Date:   2017-06-02 16:24:50
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-02 16:34:05
 */
trait HavingUnitTrait{
	protected $having = [];
	public function getHaving() {return $this->having;}
	/**
	 * 设置having
	 * @Author   Lerko
	 * @DateTime 2017-03-20T16:24:23+0800
	 * @param    [type]                   $having [description]
	 * @param    [type]                   $from   [description]
	 * @param    boolean                  $group  [description]
	 * @return   [type]                           [description]
	 */
	public function having($having, $from = null, $group = false) {
		if (!isset($having) || count($having) < 2) {
			return;
		}
		if (isset($from)) {
			$having['from'] = $from;
		}
		$group = $this->whereGroup;
		if ($group !== false) {
			$having['group'] = $group;
		}
		$this->having[] = $having;
		return $this;
	}
}