<?php 

namespace Phero\Database\Traits\UnitTrait;
/**
 * @Author: lerko
 * @Date:   2017-06-02 16:59:28
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-02 17:15:14
 */

trait FieldUnitTrait{
	/**
	 * 用户主动设置的列
	 * @var array
	 */
	protected $field = [];
		//行列模板
	protected $fieldTemp = [];
	public function getFieldTemp() {return $this->fieldTemp;}
	public function getField() {return $this->field;}

	public function field($field, $temp = "") {
		$temp_arr = [];
		$has_temp = false;
		if (!empty($temp)) {
			$has_temp = true;
		}
		if (is_array($field)) {
			if (count($field) != count($temp) && is_array($temp_arr)) {
				throw new \Exception("field and temp length not equle");
			}
			foreach ($field as $key => $value) {
				if (!$has_temp) {
					$this->field[] = $value;
				} else {
					$temp_arr[$value] = $temp[$key];
				}

			}
		} else {
			if (!$has_temp) {
				$this->field[] = $field;
			} else {
				$temp_arr[$field] = $temp;
			}

		}
		$this->fieldTemp($temp_arr);
		return $this;
	}

	/**
	 * 设置字段的函数  字段用？标示
	 * 如  count(?)
	 * @param  [type] $temp [description]
	 * @return [type]       [description]
	 */
	public function fieldTemp($temp = []) {
		$this->fieldTemp = $temp;
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