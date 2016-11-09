<?php
namespace Phero\Database\Realize\Constraint;

use Phero\Database\Enum as enum;
use Phero\Database\Interfaces as interfaces;
use Phero\Database\Realize as realize;
use Phero\Database\Traint as traint;
use Phero\Map\Note as note;

/**
 *where约束累
 */
class WhereConstraint implements interfaces\IConstraint, interfaces\IBindData {
	use traint\TConstraintTableDependent;

	protected $where;

	private $bindData=[];
	private $enableAlias = true;
	private $Entiy;
	/**
	 * 构造函数
	 * @param [type]  $Entiy       [数据库实体]
	 * @param boolean $enableAlias [是否启用别名]
	 */
	function __construct($Entiy, $enableAlias = true) {
		$this->Entiy = $Entiy;
		$this->enableAlias = $enableAlias;
		$this->userSetWhere($Entiy);
		if (count($Entiy->getJoin()) > 0) {
			foreach ($Entiy->getJoin() as $key => $value) {
				$this->userSetWhere($value[0]);
			}
		}
	}

	/**
	 * 返回语句约束的类型
	 * @Overried
	 * @return [type] [description]
	 */
	public function getType() {
		return realize\MysqlConstraintBuild::Where;
	}
	/**
	 * 获取这个约束组装完成的sql语句片段
	 * @return [type] [description]
	 */
	public function getSqlFragment() {
		if (!empty($this->where)) {
			$this->where = " where " . $this->where;
		}
		return $this->where;
	}

	protected function getBuildData($Entiy){
	    return $Entiy->getWhere();
    }

	/**
	 * 用户手动设置的查询条件
	 * @param  [type] $Entiy [数组方式的where的各种条件]
	 * @return [type]        [description]
	 */
	public function userSetWhere($Entiy, $group = 0) {
		$where = $this->getBuildData($Entiy);
		// var_dump($where);
		$i = 0;
		foreach ($where as $key => &$value) {

			if (isset($value['from'])) {
				$from = $value['from'];
			} else {
				//取得默认的表名或者是表的别名
				$from = $this->getName($Entiy);
			}
			//这里设置表的默认where链接方式
			$contype = ($i == count($where) - 1) ? "" : $value[3];
			$compare = isset($value[2]) ? $value[2] : enum\Where::eq_;
			$bindValue = $this->setBindDataAndGetBindKey($value[0], $value[1], $from, $compare);
            $group=null;
            if(!empty($value['group'])){
                $group=$value["group"];
            }
			$this->setWhere($from, $value[0], $bindValue, $compare, $contype, $group);
			$i++;
		}
	}

	/**
	 * 通过普通参数添加where
	 * @param [type] $from    [数据源]
	 * @param [type] $key     [数据库数据键]
	 * @param [type] $value   [数据]
	 * @param [type] $compare [比较方法]
	 * @param [type] $conType [连接方式]
	 */
	public function setWhere($from, $key, $value, $compare = enum\Where::eq_, $conType = "", $group = 0) {
		if ($group == 0) {
			$group1 = "";
			$group2 = "";
		} else if ($group == 1) {
			$group1 = "(";
			$group2 = "";
		} else if ($group == 2) {
			$group1 = "";
			$group2 = ")";
		}

		if (!$this->enableAlias) {$from = "";} else { $from .= ".";}

		$this->where .= " " . $group1 . $from . $key . $compare . $value . $group2 . $conType;
	}

	public function getBindData() {
		return $this->bindData;
	}

	/**
	 * [getBindDataType 获取绑定数据的]
	 * @param  [type] $field   [字段名称]
	 * @return [type]          [description]
	 */
	public function getBindDataType($field) {
		$type = $this->getTablePropertyNode($this->Entiy, $field, new note\Field());
		return note\Field::typeTrunPdoType($type->type);
	}

	/**
	 * 添加绑定数据的数据列  返回相应的value
	 * @param [type] $key     [description]
	 * @param [type] $values  [description]
	 * @param [type] $from    [description]
	 * @param [type] $compare [description]
	 */
	public function setBindDataAndGetBindKey($key, $values, $from, $compare) {
		if ($compare == enum\Where::between) {
			$key1 = ":" . $from . "_" . $key . "_" . rand();
			$key2 = ":" . $from . "_" . $key . "_" . rand();
			$this->bindData[] = [$key1, $values[0], $this->getBindDataType($key)];
			$this->bindData[] = [$key2, $values[1], $this->getBindDataType($key)];
			return $key1 . " AND " . $key2;
		} else if ($compare == enum\Where::in_) {
			$in_betweenBindKey = "(";
			$bindType = $this->getBindDataType($key);
			$i = 0;
			foreach ($values as $key => $value) {
				$bindKey = ":" . $from . "_" . $key . "_" . rand();
				$in_betweenBindKey .= $bindKey;
				if ($i != count($values) - 1) {
					$in_betweenBindKey .= ",";
				}
				$this->bindData[] = [$bindKey, $value, $bindType];
				$i++;
			}
			return $in_betweenBindKey . ")";
		} else {
			$bindKey = ":" . $from . "_" . $key . "_" . rand();
			$this->bindData[] = [$bindKey, $values, $this->getBindDataType($key)];
			return $bindKey;
		}
	}
}