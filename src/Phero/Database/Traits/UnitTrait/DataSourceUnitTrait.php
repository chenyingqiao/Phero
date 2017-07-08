<?php 

namespace Phero\Database\Traits\UnitTrait;
/**
 * @Author: lerko
 * @Date:   2017-06-02 17:17:00
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-02 17:19:16
 */

trait DataSourceUnitTrait{
	//数据源join方式
	protected $datasourseJoinType;
	public function getDatasourse() {return $this->datasourse;}
	/**
	 * 获取单独添加数据源时设置的Join类型
	 * @return [type] [description]
	 */
	public function getDatasourseJoinType() {
		return $this->datasourseJoinType;
	}

	/**
	 * 添加数据源
	 * @param  [type] $table [数据源  可以是子查询  也可以是一个表名]
	 * @param  [type] $as    [如果是子查询就必须要有别名]
	 * @param  [type] $on    [关联条件]
	 * @param  [type] $join  [join方式 默认内连接]
	 */
	public function datasourse($table, $as, $on, $join = null) {
		$this->datasourse[] = [$table, $as, $on, $join];
		return $this;
	}

	/**
	 * 设置单独添加数据源时设置的Join类型
	 * @param  [type] $JoinType [description]
	 * @return [type]           [description]
	 */
	public function datasourseJoinType($JoinType) {
		$this->datasourseJoinType = $JoinType;
		return $this;
	}
}