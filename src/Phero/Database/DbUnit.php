<?php
namespace Phero\Database;

use Phero\Database\Enum as enum;
use Phero\Database\Traint\DbUnitBase;

/**
 * 实体化数据的载入载体
 */
trait DbUnit {
	use DbUnitBase;
	public function __set($key, $value) {
		$this->$key = $value;
	}

	//只查询一条
	public function find() {
		$this->limit(1);
		return $this->select()[0];
	}

	public function polymerization($field, $keyword = "COUNT") {
		if (is_array($field)) {
			$temp_arr = [];
			foreach ($field as $key => $value) {
				$temp_arr[$value] = $keyword . "(?) as " . $keyword;
			}
			$this->fieldTemp($temp_arr);
		} else if (is_string($field)) {
			$this->fieldTemp([$field => $keyword . "(?) as " . $keyword]);
		}
	}
	/**
	 * 直接返回数量
	 * @param [type] $field [description]
	 */
	public function COUNT($field) {
		$this->allFalse();
		$this->$field = true;
		$this->polymerization($field, "COUNT");
		$data = $this->find();
		var_dump($data);
		$this->initField($this->values_cache, $this->inifalse);
		if ($this->getModel()->getFetchMode() == Model::fetch_arr_number) {
			return $data[0];
		} else {
			return $data["COUNT"];
		}
	}
	public function SUM($field) {
		$this->polymerization($field, "SUM");
		return $this;
	}
	public function MAX($field) {
		$this->polymerization($field, "MAX");
		return $this;
	}
	public function MIN($field) {
		$this->polymerization($field, "MIN");
		return $this;
	}

	public function AVG($field) {
		$this->polymerization($field, "AVG");
		return $this;
	}
	public function GROUP_CONCAT($field) {
		$this->polymerization($field, "GROUP_CONCAT");
		return $this;
	}
	public function BIN($field) {
		$this->polymerization($field, "BIN");
		return $this;
	}

	public function ABS($field) {
		$this->polymerization($field, "ABS");
		return $this;
	}
	public function CEILING($field) {
		$this->polymerization($field, "CEILING");
		return $this;
	}
	public function EXP($field) {
		$this->polymerization($field, "EXP");
		return $this;
	}
	public function FLOOR($field) {
		$this->polymerization($field, "FLOOR");
		return $this;
	}
	public function LN($field) {
		$this->polymerization($field, "LN");
		return $this;
	}
	public function SIGN($field) {
		$this->polymerization($field, "SIGN");
		return $this;
	}
	public function SQRT($field) {
		$this->polymerization($field, "SQRT");
		return $this;
	}

	/**
	 * where扩展函数
	 * @param  [type] $function_name [description]
	 * @param  [type] $argument      [description]
	 * @return [type]                [description]
	 */
	public function __call($function_name, $argument) {
		if (strstr($function_name, "whereOr")) {
			if (strstr($function_name, "Group")) {
				$reg = '/whereOr(\w+)Group(\w+)/';
				$matched = preg_match($reg, $function_name, $matcher);
				if ($matcher[2] == "Start") {$this->whereGroup = 1;} else if ($matcher[2] == "End") {$this->whereGroup = 2;}
			} else {
				$reg = '/whereOr(\w+)/';
				$matched = preg_match($reg, $function_name, $matcher);
				$this->whereGroup = 0;
			}
			if ($matched) {
				if (!empty($matcher[1])) {
					$compser = strtolower($matcher[1]);
					if ($compser == "eq" || $compser == 'in') {$compser .= "_";}
					$this->where([$argument[0], $argument[1], enum\Where::get($compser), enum\WhereCon::or_]);
				} else {
					$this->where([$argument[0], $argument[1], $argument[2], enum\WhereCon::or_]);
				}
			}
		} else if (strstr($function_name, "And")) {
			if (strstr($function_name, "Group")) {
				$reg = '/whereAnd(\w+)Group(\w+)/';
				$matched = preg_match($reg, $function_name, $matcher);
				if ($matcher[2] == "Start") {$this->whereGroup = 1;} else if ($matcher[2] == "End") {$this->whereGroup = 2;}
			} else {
				$reg = '/whereAnd(\w+)/';
				$matched = preg_match($reg, $function_name, $matcher);
				$this->whereGroup = 0;
			}
			if ($matched) {
				if (!empty($matcher[1])) {
					$compser = strtolower($matcher[1]);
					if ($compser == "eq" || $compser == 'in') {$compser .= "_";}
					$this->where([$argument[0], $argument[1], enum\Where::get($compser), enum\WhereCon::and_]);
				} else {
					$this->where([$argument[0], $argument[1], $argument[2], enum\WhereCon::and_]);
				}
			}
		} else if (!strstr($function_name, "And") && !strstr($function_name, "Or") && substr($function_name, 0, 5) == "where") {
			if (strstr($function_name, "Group")) {
				$reg = '/where(\w+)Group(\w+)/';
				$matched = preg_match($reg, $function_name, $matcher);
				if ($matcher[2] == "Start") {$this->whereGroup = 1;} else if ($matcher[2] == "End") {$this->whereGroup = 2;}
			} else {
				$reg = '/where(\w+)/';
				$matched = preg_match($reg, $function_name, $matcher);
				$this->whereGroup = 0;
			}
			if ($matched) {
				if (!empty($matcher[1])) {
					$compser = strtolower($matcher[1]);
					if ($compser == "eq" || $compser == 'in') {$compser .= "_";}
					$this->where([$argument[0], $argument[1], enum\Where::get($compser)]);
				}
			}
		}
		return $this;
	}
}
