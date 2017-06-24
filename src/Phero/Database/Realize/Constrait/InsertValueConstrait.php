<?php
namespace Phero\Database\Realize\Constrait;

use Phero\Database\Interfaces as interfaces;
use Phero\Database\Realize as realize;
use Phero\Database\Traits as Traits;
use Phero\Map\Note as note;
use Phero\System\Tool;

/**
 * 列约束
 */
class InsertValueConstrait implements interfaces\IConstrait, interfaces\IBindData {
	use Traits\TConstraitTableDependent;
	/**
	 * [table=>[name,as,temp]]
	 * table:field所属的表名
	 * name:field名称
	 * as:filed 别名
	 * temp:filed的函数  采用？代码field的
	 * @var array
	 */
	private $ValueList = array();

	private $sql = "";
	/**
	 * 初始化的时候要进行表名指定
	 * @param [type] $Entiy [需要支持数组形式的数据]
	 */
	public function __construct($Entiy) {
		if (is_array($Entiy)) {
			$index = 0;
			foreach ($Entiy as $key => $value) {
				$this->resovleEntiy($value, $key);
				if ($index < count($Entiy) - 1) {
					$this->sql .= ",";
				}
				$index++;
			}
		} else {
			$this->resovleEntiy($Entiy, 0);
		}
	}

	public function resovleEntiy($Entiy, $index) {
		$propertys = $this->getTableProperty($Entiy);
		foreach ($propertys as $key => $value) {
			$value_ = $value->getValue($Entiy);
			if (!empty($value_) && $value_ !== false) {
				$field = $this->getTablePropertyNodeOver1($value, new note\Field());

				$bind_key = ":" . $value->getName() . "_" . $index;
				$bind_key=Tool::clearSpecialSymbal($bind_key);
				$this->ValueList[] = $bind_key;
				if ($field != null) {
					$this->bindData[] = [$bind_key, $value_, note\Field::typeTrunPdoType($field->type)];
				} else {
					$this->bindData[] = [$bind_key, $value_, \PDO::PARAM_STR];
				}
			}
		}
		$this->SqlFragmentPiece();
	}

	private $bindData = [];
	public function getBindData() {
		return $this->bindData;
	}

	/**
	 * 获取字段的sql片段
	 * @return [type] [description]
	 */
	public function SqlFragmentPiece() {
		$this->sql .= "(";
		$i = 0;
		foreach ($this->ValueList as $key => $value) {
			if ($i < count($this->ValueList) - 1) {
				$split = ",";
			} else {
				$split = "";
			}
			$this->sql .= $value . $split;
			$i++;
		}
		$this->sql .= ")";
		$this->ValueList = [];
		return $this->sql;
	}
	public function getSqlFragment() {
		return $this->sql;
	}

	public function getType() {
		return realize\MysqlConstraitBuild::Value;
	}
}