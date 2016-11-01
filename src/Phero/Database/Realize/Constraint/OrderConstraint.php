<?php
namespace Phero\Database\Realize\Constraint;

use Phero\Database\Enum as enum;
use Phero\Database\Interfaces as interfaces;
use Phero\Database\Realize as realize;
use Phero\Database\Traint as traint;

/**
 *
 */
class OrderConstraint implements interfaces\IConstraint {
	use traint\TConstraintTableDependent;

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
		return realize\MysqlConstraintBuild::Order;
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
			$alias = $this->getTableAlias($this->Entiy);
			if (empty($alias)) {
				$alias = "";
			}
			$sql .= $alias . "." . $this->order[0] . " ";
		}
		return $sql . $this->order[1];
	}
}