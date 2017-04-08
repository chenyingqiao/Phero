<?php
namespace Phero\Database\Realize\Constrait;

use Phero\Database\Interfaces as interfaces;
use Phero\Database\Realize as realize;

/**
 *
 */
class LimitConstrait implements interfaces\IConstrait {

	protected $limit;

	public function __construct($Entiy) {
		$this->limit = $Entiy->getLimit();
	}

	/**
	 * 返回语句约束的类型
	 * @return [type] [description]
	 */
	public function gettype() {
		return realize\MysqlConstraitBuild::Limit;
	}
	/**
	 * 获取这个约束组装完成的sql语句片段
	 * @return [type] [description]
	 */
	public function getsqlfragment() {
		$sql = " limit ";
		if (empty($this->limit)) {
			return "";
		} else {
			if (empty($this->limit[1])) {
				$sql .= $this->limit[0];
			} else {
				$sql .= $this->limit[0] . "," . $this->limit[1];
			}
		}
		return $sql;
	}
}