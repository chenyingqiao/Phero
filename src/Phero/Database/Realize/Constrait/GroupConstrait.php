<?php
namespace Phero\Database\Realize\Constrait;

use Phero\Database\Interfaces as interfaces;
use Phero\Database\Realize as realize;
use Phero\Database\Traits as Traits;

/**
 *Group by field
 */
class GroupConstrait implements interfaces\IConstrait {
	use Traits\TConstraitTableDependent;
	protected $group_field;
	protected $Entiy;
	public function __construct($Entiy) {
		$this->group_field = $Entiy->getGroup();
		$this->Entiy = $Entiy;
	}

	/**
	 * 返回语句约束的类型
	 * @return [type] [description]
	 */
	public function gettype() {
		return realize\MysqlConstraitBuild::Grouping;
	}
	/**
	 * 获取这个约束组装完成的sql语句片段
	 * @return [type] [description]
	 */
	public function getsqlfragment() {
		$sql = " group by ";
		if (empty($this->group_field)) {
			return "";
		} else {
			$alias = $this->getTableAlias($this->Entiy);
			if (empty($alias)) {
				$sql .= $this->group_field;
			} else {
				if(strstr($this->group_field,".")){
					$sql.=$this->group_field;
				}else{
					$sql .= $alias . "." . $this->group_field;
				}
			}
		}
		return $sql;
	}
}