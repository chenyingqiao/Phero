<?php
namespace Phero\Database\Realize\Constrait;

use Phero\Database\Enum as enum;
use Phero\Database\Interfaces as interfaces;
use Phero\Database\Realize as realize;
use Phero\Database\Traits as Traits;
use Phero\Map\Note as note;
use Phero\System\Tool;

/**
 *where约束
 */
class WhereConstrait implements interfaces\IConstrait, interfaces\IBindData {
	use Traits\TConstraitTableDependent;

	protected $where;

	private $bindData = [];
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
		return realize\MysqlConstraitBuild::Where;
	}
	/**
	 * 获取这个约束组装完成的sql语句片段
	 * @return [type] [description]
	 */
	public function getSqlFragment() {
		if (!empty($this->where)) {
			$this->where = " where" . $this->where;
		}
		return $this->where;
	}

	protected function getBuildData($Entiy) {
		return $Entiy->getWhere();
	}

	/**
	 * 用户手动设置的查询条件
	 * @param  [type] $Entiy [要设置where的实体类,实体类中带where的数据]
	 * @param  [type] $group [where分组]
	 * @return [type]        [description]
	 */
	public function userSetWhere($Entiy, $group = 0) {
		$where = $this->getBuildData($Entiy);
		$i = 0;
		foreach ($where as $key => &$value) {
			//where 分组
			if(count($value)==1&&array_key_exists("group",$value)){
				$this->setWhere("","","","","",$value['group'],"");
				continue;
			}
			if(empty($value[0])&&empty($value[1])&&empty($value[2])&&!empty($value[3])){
				$this->setWhere("","","","",$value[3],"","");
				continue;
			}
			if (isset($value['from'])) {
				$from = $value['from'];
			} else {
				//取得默认的表名或者是表的别名
				$from = $this->getNameByCleverWay($Entiy);
			}

			$temp = isset($value['temp']) ? $value['temp'] : "";

			//这里设置表的默认where链接方式
			if (isset($value[3])) {
				$contype = $value[3];
			} else {
				$contype = "";
			}
			//设置默认的比较符号
			//			$compare = isset($value[2]) ? $value[2] : enum\Where::eq_;
			$compare = isset($value[2]) ? $value[2] : "";
			//支持查询对象是一个实体类 在这里会被解析成子查询
			if (is_object($value[1])) {
				$value[1]->setWhereRelation($this->getNameByCleverWay($Entiy));
				$bindValues = $value[1]->fetchSql();
				$this->bindData = array_merge($this->bindData, $bindValues);
				$bindValue = "(" . rtrim($value[1]->sql(), ";") . ")";
			}elseif(isset($value['sql_fregment'])){
				$bindValue=$value[1];
			} else {
				$bindValue = $this->setBindDataAndGetBindKey($value[0], $value[1], $from, $compare);
			}
			$group = null;
			if (!empty($value['group'])) {
				$group = $value["group"];
			}
			$this->setWhere($from, $value[0], $bindValue, $compare, $contype, $group, $temp);
			$i++;
		}
	}

	/**
	 * 通过普通参数添加where
	 * @param [type] $from    [数据源 表名或者是表的别名]
	 * @param [type] $key     [数据库数据键]
	 * @param [type] $value   [数据]
	 * @param [type] $compare [比较方法]
	 * @param [type] $conType [连接方式]
	 * @param string $whereTemp
	 */
	public function setWhere($from, $key, $value, $compare = enum\Where::eq_, $conType = "", $group = 0, $whereTemp = "") {
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

		//为字段添加表明前缀
		if (!$this->enableAlias||strstr($key,'.')) {$from = "";$dit="";} else { $from = "`".$from."`.";$dit="`";}
		$field ="$from$dit$key$dit";
		if (!empty($whereTemp)) {
			$field = str_replace("?", $field, $whereTemp);
		}
		//如果比较符号为空 值也清空 只留下field
		if (empty($compare)) {
			$value = "";
		}
		if (empty($key)) {
			$field = "";
		}
		$this->where .= " " .$conType .  $group1 . $field . $compare . $value . $group2;
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
		if ($type == false) {
			return 1;
		}
		return note\Field::typeTrunPdoType($type->type);
	}

	/**
	 * 添加绑定数据的数据列  返回相应的value  这个value会直接添加到sql中
	 * 需要绑定的就是：开头
	 * @param [type] $key     [bindvalue的key]
	 * @param [type] $values  [bindvalue的value]
	 * @param [type] $from    [表名]
	 * @param [type] $compare [比较符号]
	 */
	public function setBindDataAndGetBindKey($key, $values, $from, $compare) {
		if(empty($values)){
			return "";
		}
		$bindType = $this->getBindDataType($key);
		if ($compare == enum\Where::get("between")) {
			$key1 = Tool::clearSpecialSymbal(":" . $from . "_" . $key . "_" . rand());
			$key2 = ":" . $from . "_" . $key . "_" . rand();
			if (!empty($values)) {
				$this->bindData[] = [$key1, $values[0], $bindType];
				$this->bindData[] = [$key2, $values[1], $bindType];
			}
			return $key1 . " AND " . $key2;
		} else if ($compare == enum\Where::get("in_")) {
			$in_betweenBindKey = "(";
			$i = 0;
			foreach ($values as $key => $value) {
				$bindKey = Tool::clearSpecialSymbal(":" . $from . "_" . $key . "_" . rand());
				$in_betweenBindKey .= $bindKey;
				if ($i != count($values) - 1) {
					$in_betweenBindKey .= ",";
				}
				if (!empty($values)) {
					$this->bindData[] = [$bindKey, $value, $bindType];
				}
				$i++;
			}
			return $in_betweenBindKey . ")";
		} else {
			$bindKey = Tool::clearSpecialSymbal(":" . $from . "_" . $key . "_" . rand());
			if (!empty($values)) {
				$this->bindData[] = [$bindKey, $values, $bindType];
			}
			return $bindKey;
		}
	}
}