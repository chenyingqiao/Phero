<?php
namespace Phero\Database\Interfaces;

use Phero\Database\Enum\RelType;

/**
 * 数据操作基本的实现接口
 * 通过这个借口扩展不同数据库的操作
 */
interface IDbHelp {
	public function setEntiy(&$entiy);
	public function getDbConn();
	public function exec($sql, $data=[],$type=RelType::insert);
	public function query($sql, $data=[]);
	public function queryResultArray($sql, $data=[]);
	public function error();
	public function setFetchMode($mode, $classname = null);
	public function transaction($type);
	public function disconnect(&$pdo);
}