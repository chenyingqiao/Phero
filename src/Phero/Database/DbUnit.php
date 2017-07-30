<?php
namespace Phero\Database;

use Phero\Database\DbUnitBase;
use Phero\Database\Enum as enum;
use Phero\Database\Enum\Where;
use Phero\Database\Realize\MysqlDbHelp;
use Phero\Map\Note\RelationEnable;

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

	public function relFind($field=null){
		$this->map(new RelationEnable);
		$this->find($field);
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

	private function _callPolymerization($function_name,$argument){
		isset($argument[0])?$field=$argument[0]:$field="";
		$in_polymerization=in_array($function_name,explode(",","sum,max,min,avg,group_concat,bin,abs,ceiling,exp,floor,ln,sign,sqrt"));
		if(!$in_polymerization){
			return ;
		}

		$field_name=self::FF($field);
		$as="{$function_name}_{$field}";
		$field_name="$function_name($field_name)";
		$this->field($field_name,$as);
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
		$this->_callWhereAndHavingCatch($function_name,$argument);
		$this->_callPolymerization($function_name,$argument);
		return $this;
	}

	public function relSelect($yield=false){
		$this->map(new RelationEnable);
		return parent::select($yield);
	}
	public function relUpdate($transaction_type=false){
		$this->map(new RelationEnable);
		return parent::update($transaction_type);
	}
	public function relInsert($transaction_type=false){
		$this->map(new RelationEnable);
		return parent::insert($transaction_type);
	}
	public function relDelete($transaction_type=false){
		$this->map(new RelationEnable);
		return parent::delete($transaction_type);
	}

	private function _callwhereandhavingcatch($function_name, $argument){
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
				}elseif(count($argument)==1&&strstr($compser,"is")){
					$this->$call([$argument[0],"", enum\Where::get($compser), $Con]);
				}
			}
		}
	}
}
