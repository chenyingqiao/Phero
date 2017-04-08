<?php
namespace Phero\Database\Interfaces;

use Phero\Database\Interfaces\IConsTrait;

interface IConstraitBuild {
	/**
	 * 添加约束
	 * @param IConsTrait $consTrait [description]
	 * @param [type]      $type       [description]
	 */
	public function addItem(IConsTrait $consTrait);
	/**
	 * 取得约束列表
	 * @return [type] [description]
	 */
	public function getConsTraits();
	/**
	 * 取得select 语句 通过固定的顺序拼装语句
	 * @return [type] [description]
	 */
	public function buildSelectSql($Entity);
	public function buildInsertSql($Entity);
	public function buildUpdataSql($Entity);
	public function buildDeleteSql($Entity);
}