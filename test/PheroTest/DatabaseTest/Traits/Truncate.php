<?php 

namespace PheroTest\DatabaseTest\Traits;

use Phero\Database\Db;
/**
 * @Author: lerko
 * @Date:   2017-05-31 17:20:03
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-07-31 08:13:30
 */
trait Truncate{
	public function truncate(){
		$tablename=$this->getTableName($this);
		$this->dumpSql="truncate table {$tablename}";
		Db::exec($this->dumpSql);
	}
}