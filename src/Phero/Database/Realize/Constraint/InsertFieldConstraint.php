<?php
namespace Phero\Database\Realize\Constraint;

use Phero\Database\Interfaces as interfaces;
use Phero\Database\Realize as realize;
use Phero\Database\Traint as traint;
use Phero\Map as M;

/**
 * 列约束
 */
class InsertFieldConstraint implements interfaces\IConstraint {
	use traint\TConstraintTableDependent;
	/**
	 * [table=>[name,as,temp]]
	 * table:field所属的表名
	 * name:field名称
	 * as:filed 别名
	 * temp:filed的函数  采用？代码field的
	 * @var array
	 */
	private $fieldList = array();
	/**
	 * 初始化的时候要进行表名指定
	 * @param [type] $Entiy [description]
	 */
	public function __construct($Entiy) {
		if (is_array($Entiy)) {
			$Entiy = $Entiy[0];
		}
		$propertys = $this->getTableProperty($Entiy);
		foreach ($propertys as $key => $value) {
			$value_ = $value->getValue($Entiy);
			if (isset($value_) && $value_ !== false) {
				$this->fieldList[] = $value->getName();
			}
		}
	}

	/**
	 * 获取字段的sql片段
	 * @return [type] [description]
	 */
	public function getSqlFragment() {
		$sql = " ";
		$i = 0;
		foreach ($this->fieldList as $key => $value) {
			if ($i < count($this->fieldList) - 1) {
				$split = ",";
			} else {
				$split = "";
			}
			$sql .= $value . $split;
			$i++;
		}
		return $sql;
	}

	public function getType() {
		return realize\MysqlConstraintBuild::Field;
	}
}