<?php 

namespace Phero\Database\Traits\UnitTrait;

use Phero\Database\Enum\JoinType;
/**
 * @Author: lerko
 * @Date:   2017-06-02 16:52:45
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-02 17:15:40
 */
trait JoinUnitTrait{
	//查询条件列表
	protected $join = [];
	public function getJoin() {return $this->join;}
	/**
	 * 表链接
	 * $on 通过这样的替换符号标示
	 * $：标示是被关联的Entiy
	 * #：标示的关联的Entiy
	 *   $Entiy->join(new XX(),"$.uid=#.id");
	 */
	public function join($Entiy, $on, $joinType = JoinType::inner_join) {
		$this->join[] = [$Entiy, $on, $joinType];
		return $this;
	}
}