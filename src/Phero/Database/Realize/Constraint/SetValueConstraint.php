<?php
namespace Phero\Database\Realize\Constraint;

use Phero\Database\Interfaces as interfaces;
use Phero\Database\Realize as realize;
use Phero\Database\Traint as traint;
use Phero\Map\Note as note;

/**
 *生成 field和数据的sql片段
 *如 username=:username
 */
class SetValueConstraint implements interfaces\IConstraint, interfaces\IBindData {
	use traint\TConstraintTableDependent;
	private $bindData = [];
	private $sql = "";
	public function __construct($Entiy) {
		if (is_array($Entiy)) {
			foreach ($Entiy as $key => $value) {
				$this->resovleEntiy($value, $key);
			}
		} else {
			$this->resovleEntiy($Entiy, 0);
		}
	}
	/**
	 * 返回语句约束的类型
	 * @Overried
	 * @return [type] [description]
	 */
	public function getType() {
		return realize\MysqlConstraintBuild::Set;
	}
	/**
	 * 获取这个约束组装完成的sql语句片段
	 * @return [type] [description]
	 */
	public function getSqlFragment() {
		return $this->sql;
	}

	/**
	 * 解析entiy
	 * @param  [type] $Entiy [description]
	 * @param  [type] $index [description]
	 * @return [type]        [description]
	 */
	public function resovleEntiy($Entiy, $index) {
		$propertys = $this->getTableProperty($Entiy);
		$propertys = $this->getCountHasValue($Entiy, $propertys);
		$index = 0;
		foreach ($propertys as $key => $value) {
			$value_ = $value->getValue($Entiy);
			if (isset($value_) && $value_ !== false) {
				$field = $this->getTablePropertyNodeOver1($value, new note\Field());

				$bind_key = ":" . $value->getName() . "_" . $index . "_set";
				$split = $index != count($propertys) - 1 ? "," : "";
				$this->sql .= $value->getName() . "=" . $bind_key . $split;

				if ($field != null) {
					$this->bindData[] = [$bind_key, $value_, note\Field::typeTrunPdoType($field->type)];
				} else {
					$this->bindData[] = [$bind_key, $value_, \PDO::PARAM_STR];
				}
				$index++;
			}
		}
	}

/**
 * [getCountHasValue description]
 * @param  [type] $Entiy     [description]
 * @param  Array  $propertys [description]
 * @return [type]            [description]
 */
	private function getCountHasValue($Entiy, Array $propertys) {
		$propertys_result = [];
		foreach ($propertys as $key => $value) {
			if (!empty($value->getValue($Entiy))) {
				$propertys_result[] = $value;
			}
		}
		return $propertys_result;
	}

	/**
	 * 获取生成的绑定数据
	 * @return [type] [description]
	 */
	public function getBindData() {
		return $this->bindData;
	}

}