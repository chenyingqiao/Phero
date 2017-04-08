<?php
namespace Phero\Database\Interfaces;

/**
 * 约束的基本接口
 */
interface IConsTrait {
	/**
	 * 返回语句约束的类型
	 * @return [type] [description]
	 */
	public function gettype();
	/**
	 * 获取这个约束组装完成的sql语句片段
	 * @return [type] [description]
	 */
	public function getsqlfragment();
}