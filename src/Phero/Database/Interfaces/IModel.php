<?php
namespace Phero\Database\Interfaces;

/**
 * 数据库统一操作接口
 */
interface IModel {
	public function insert($Entiy, $is_replace);
	public function select($Entiy);
	public function update($Entiy);
	public function delete($Entiy);
	public function getSql();
	public function transaction($type);
	public function getPdo();
	public function getPdoDriverType();
	public function getError();
	public function fetchSql($Entiy);
	public function setFetchMode($mode, $classname = null);
	public function getFetchMode();
	public function getHelp();
}