<?php
namespace Phero\Database\Interfaces;

use Phero\Database\Interfaces\IConstraint;

interface IConstraintBuild {
	/**
	 * 添加约束
	 * @param IConstraint $constraint [description]
	 * @param [type]      $type       [description]
	 */
	public function addItem(IConstraint $constraint);
	/**
	 * 取得约束列表
	 * @return [type] [description]
	 */
	public function getConstraints();
	/**
	 * 取得select 语句 通过固定的顺序拼装语句
	 * @return [type] [description]
	 */
	public function buildSelectSql($Entity);
	public function buildInsertSql($Entity);
	public function buildUpdataSql($Entity);
	public function buildDeleteSql($Entity);
}