<?php
namespace Phero\Database\Interfaces;

/**
 * 数据操作基本的实现接口
 * 通过这个借口扩展不同数据库的操作
 */
interface IDbHelp {
	public function getDbConn();
	public function exec($sql, $data);
	public function query($sql, $data);
	public function error();
	public function setFetchMode($mode, $classname = null);
}