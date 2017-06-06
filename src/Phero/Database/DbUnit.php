<?php
namespace Phero\Database;

use Phero\Database\DbUnitBase;
use Phero\Database\Enum as enum;
use Phero\Database\Enum\Where;
use Phero\Database\Realize\MysqlDbHelp;

/**
 * 实体化数据的载入载体
 */
class DbUnit extends DbUnitBase {
	//只查询一条
	public function find($field=null) {
		$this->limit(1);
		$data = $this->select();
		if (isset($data[0])) {
			if(isset($field)){
				return $data[0][$field];
			}
			return $data[0];
		}
		return [];
	}
	/**
	 * 统一设置聚合函数
	 * @Author   Lerko
	 * @DateTime 2017-05-31T18:14:06+0800
	 * @param    [type]                   $field    [description]
	 * @param    string                   $keyword  [description]
	 * @param    boolean                  $distanct [description]
	 * @return   [type]                             [description]
	 */
	public function polymerization($field, $keyword = "COUNT", $distanct = false) {
		if ($distanct) {
			$split = "(distanct ?) as ";
		} else {
			$split = "(?) as ";
		}
		if (is_array($field)) {
			$temp_arr = [];
			foreach ($field as $key => $value) {
				$temp_arr[$value] = $keyword . $split . $keyword;
			}
			$this->fieldTemp($temp_arr);
		} else if (is_string($field)) {
			$this->fieldTemp([$field => $keyword . $split . $keyword]);
		}
	}
	/**
	 * 直接返回数量
	 * @param [type] $field [description]
	 */
	public function count($field=null, $distanct = false) {
		$help=new MysqlDbHelp();
		$tablename=$this->getTableName($this);
		$id=$help->query("select count(*) as count from {$tablename};");
		foreach ($id as $key => $value) {
			return $value['count'];
		}
	}
	public function sum($field) {
		$this->polymerization($field, "SUM");
		return $this;
	}
	public function max($field) {
		$this->polymerization($field, "MAX");
		return $this;
	}
	public function min($field) {
		$this->polymerization($field, "MIN");
		return $this;
	}

	public function avg($field) {
		$this->polymerization($field, "AVG");
		return $this;
	}
	public function group_concat($field) {
		$this->polymerization($field, "GROUP_CONCAT");
		return $this;
	}
	public function bin($field) {
		$this->polymerization($field, "BIN");
		return $this;
	}

	public function abs($field) {
		$this->polymerization($field, "ABS");
		return $this;
	}
	public function ceiling($field) {
		$this->polymerization($field, "CEILING");
		return $this;
	}
	public function exp($field) {
		$this->polymerization($field, "EXP");
		return $this;
	}
	public function floor($field) {
		$this->polymerization($field, "FLOOR");
		return $this;
	}
	public function ln($field) {
		$this->polymerization($field, "LN");
		return $this;
	}
	public function sign($field) {
		$this->polymerization($field, "SIGN");
		return $this;
	}
	public function sqrt($field) {
		$this->polymerization($field, "SQRT");
		return $this;
	}

	/**
	 * where扩展函数
	 * 1个参数 数据和比较符号
	 * 2个参数 字段和比较符号
	 * 3个参数 字段比较符号字段模板
	 * @param  [type] $function_name [调用的函数名称]
	 * @param  [type] $argument      [调用where的参数 每个调用都不一样]
	 * @return [type]                [description]
	 */
	public function __call($function_name, $argument) {
		$result=$this->_callWhereAndHavingCatch($function_name,$argument);
		if($result)return $this;
	}

	private function _callWhereAndHavingCatch($function_name, $argument){
		//判断是having还是where
		if(strstr($function_name,'where')) $call="where";
		else $call="having";
		//
		if(strstr($function_name,"And")){$Connect="And";$Con=enum\WhereCon::and_;}
		elseif(strstr($function_name,"Or")){$Connect="Or";$Con=enum\WhereCon::or_;}
		else {$Connect="";$Con=null;};

		$reg = "/{$call}{$Connect}(\w+)/";
		$matched = preg_match($reg, $function_name, $matcher);
		if ($matched) {
			if (!empty($matcher[1])) {
				$compser = strtolower($matcher[1]);
				if ($compser == "eq" || $compser == 'in') {$compser .= "_";}
				if(count($argument)==1&&is_object($argument[0])){
					$this->$call(["", $argument[0], enum\Where::get($compser), $Con]);
				}else if(count($argument)==2){
					$this->$call([$argument[0], $argument[1], enum\Where::get($compser),$Con]);
				}else if(count($argument)==3){
					$this->$call([$argument[0], $argument[1], enum\Where::get($compser), $Con], null, false, $argument[2]);
				}else{
					return false;
				}
				return true;
			}
		}
	}
}
