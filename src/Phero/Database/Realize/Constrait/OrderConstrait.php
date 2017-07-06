<?php
namespace Phero\Database\Realize\Constrait;

use Phero\Database\Enum as enum;
use Phero\Database\Interfaces as interfaces;
use Phero\Database\Realize as realize;
use Phero\Database\Traits as Traits;

/**
 *
 */
class OrderConstrait implements interfaces\IConstrait {
	use Traits\TConstraitTableDependent;

	protected $order;
	protected $Entiy;
	public function __construct($Entiy) {
		$this->Entiy = $Entiy;
		$this->order = $Entiy->getOrder();
		if (empty($this->order[1])) {
			$this->order[1] = enum\OrderType::asc;
		}
	}
	/**
	 * 返回语句约束的类型
	 * @return [type] [description]
	 */
	public function gettype() {
		return realize\MysqlConstraitBuild::Order;
	}
	/**
	 * 获取这个约束组装完成的sql语句片段
	 * @return [type] [description]
	 */
	public function getsqlfragment() {
		$sql = " order by ";
		if (empty($this->order[0])) {
			return "";
		} else {
			$alias = $this->getNameByCleverWay($this->Entiy).".";
			if (empty($alias)||strstr($this->order[0],'.')) {
				$alias = "";
			}
			$sql .= $alias.$this->order[0]." ";
		}
		return $sql . $this->order[1];
	}
}