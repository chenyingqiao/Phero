<?php 

namespace Phero\Database\Traits\UnitTrait;
/**
 * @Author: lerko
 * @Date:   2017-06-02 16:59:28
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-07 15:02:41
 */

trait FieldUnitTrait{
	/**
	 * 用户主动设置的列
	 * @var array
	 */
	protected $field = [];
	protected $values_cache, $inifalse;
	public function getField() {return $this->field;}

	public function field($field) {
		if (is_array($field)) {
			foreach ($field as $key => $value) {
					$this->field[] = $value;
			}
		} else {
				$this->field[] = $field;
		}
		return $this;
	}

	/**
	 * 初始化列
	 * @param  [type] $values [description]
	 * @return [type]         [description]
	 */
	protected function initField($values) {
		if (is_array($values)) {
			$setFiled = false;
			$keys = array_keys($values);
			//判断是否是数值key的数组 填充以及field选中
			if (is_numeric($keys[0])) {
				$setFiled = true;
			}
			foreach ($values as $key => $value) {
				if ($setFiled) {
					$this->$value = true;
				} else {
					$this->$key = $value;
				}
			}
		}
	}


	/**
	 * 查询的时候反向取消所有的列
	 * @return [type] [description]
	 */
	protected function allFalse() {
		$propertys = $this->getTablePropertyNames($this);
		//初始化所有的值null
		foreach ($propertys as $key => $value) {
			$this->$value = false;
		}
	}

	/**
	 * 数据插入的时候初始化为null
	 * 然后将数据缓存到value_cache
	 * @return [type] [description]
	 */
	protected function allNull() {
		$propertys = $this->getTableProperty($this);
		//初始化所有的值null
		foreach ($propertys as $key => $value) {
			$property_name=$value->getName();
			if($this->$property_name!=false)
				$this->values_cache[$value->getName()]=$this->$property_name;
			$this->$property_name = null;
		}
	}
}