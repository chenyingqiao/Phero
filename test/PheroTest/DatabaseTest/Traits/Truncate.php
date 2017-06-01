<?php 

namespace PheroTest\DatabaseTest\Traits;

use Phero\Database\Realize\MysqlDbHelp;
/**
 * @Author: lerko
 * @Date:   2017-05-31 17:20:03
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-05-31 17:43:49
 */
trait Truncate{
	public function truncate(){
		$help=new MysqlDbHelp();
		$tablename=$this->getTableName($this);
		$this->dumpSql="truncate table {$tablename}";
		$help->exec($this->dumpSql);
	}
}