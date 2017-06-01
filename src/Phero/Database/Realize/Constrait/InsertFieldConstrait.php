<?php
namespace Phero\Database\Realize\Constrait;

use Phero\Database\Interfaces as interfaces;
use Phero\Database\Realize as realize;
use Phero\Database\Traits as Traits;
use Phero\Map as M;
use Phero\Map\Note\Field;

/**
 * 列约束
 */
class InsertFieldConstrait implements interfaces\IConstrait {
	use Traits\TConstraitTableDependent;
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
			if (!empty($value_)) {
				$Node=$value->getNode(new Field());
				if(!empty($Node->name))
					$this->fieldList[] = $Node->name;
				else
					$this->fieldList[] = $value->getName();
			}
		}
	}

	/**
	 * 获取字段的sql片段
	 * @return [type] [description]
	 */
	public function getSqlFragment() {
		$sql = "";
		$i = 0;
		foreach ($this->fieldList as $key => $value) {
			if ($i < count($this->fieldList) - 1) {
				$split = ",";
			} else {
				$split = "";
			}
			$sql .= "`".$value."`" . $split;
			$i++;
		}
		return $sql;
	}

	public function getType() {
		return realize\MysqlConstraitBuild::Field;
	}
}