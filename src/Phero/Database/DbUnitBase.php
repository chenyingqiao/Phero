<?php

namespace Phero\Database;

use Phero\Database\Enum as enum;
use Phero\Database\Enum\FetchType;
use Phero\Database\Enum\JoinType;
use Phero\Database\Model;
use Phero\Database\Realize\MysqlDbHelp;
use Phero\Database\Traits\ArrayAccessTrait;
use Phero\Database\Traits\TConstraitTableDependent;
use Phero\Database\Traits\UnitTrait\DataSourceUnitTrait;
use Phero\Database\Traits\UnitTrait\FieldUnitTrait;
use Phero\Database\Traits\UnitTrait\HavingUnitTrait;
use Phero\Database\Traits\UnitTrait\JoinUnitTrait;
use Phero\Database\Traits\UnitTrait\OtherUnitTrait;
use Phero\Database\Traits\UnitTrait\WhereUnitTrait;
use Phero\System\Tool;

/**
 * 用来设置数据库实体类的一些携带数据
 * 以及基础功能
 */
class DbUnitBase implements \ArrayAccess {
	use TConstraitTableDependent,
		ArrayAccessTrait,
		WhereUnitTrait,
		HavingUnitTrait,
		JoinUnitTrait,
		FieldUnitTrait,
		DataSourceUnitTrait,
		OtherUnitTrait;

	CONST GroupStart=1,GroupEnd=2,GroupDisbale=0;


	/**
	 * 初始化实体类中的数据
	 * 可以是属性名和数据
	 * ['id'=>1,'username'=>'asdf']
	 * 也可以传(设置这些字段未查询字段)
	 * ['id','username']
	 * @param [type]  $values   [
	 *                          array:标示启用的列  【带有费数值key的就会进行赋值】
	 *                          false :禁用原本所有的数据
	 *                          null :不填
	 * ]
	 * @param boolean $IniFalse [反向设置false false表示的是这个列不出现在select列表中]
	 */
	public function __construct($values = null) {
		$this->model = new Model();
		$this->values_cache = $values;
		if($values!==null)
			$this->allFalse();
		$this->initField($values);
	}

	private $dumpSql;
	//ORM
	public function select($yield = false) {
		$result = $this->model->select($this, $yield);
		$this->dumpSql = $this->model->getSql();
		$this->unit_new();
		return $result;
	}
	/**
	 * 通过本实体类更新数据
	 * @param  boolean $transaction_type [更新时是否开启一个事务]
	 * @return [type]                    [description]
	 */
	public function update($transaction_type = false) {
		if ($transaction_type) {
			$this->model->transaction(Model::begin_transaction);
		}
		$result = $this->model->update($this);
		$this->dumpSql = $this->model->getSql();
		$this->unit_new();
		return $result;
	}
	/**
	 * [通过本实体类删除数据]
	 * @param  boolean $transaction_type [更新时是否开启一个事务]
	 * @return [type]                    [description]
	 */
	public function delete($transaction_type = false) {
		if ($transaction_type) {
			$this->model->transaction(Model::begin_transaction);
		}
		$result = $this->model->delete($this);
		$this->dumpSql = $this->model->getSql();
		$this->unit_new();
		return $result;
	}
	/**
	 * [通过本实体类插入数据]
	 * @param  boolean $transaction_type [更新时是否开启一个事务]
	 * @return [type]                    [description]
	 */
	public function insert($transaction_type = false) {
		if ($transaction_type) {
			$this->model->transaction(Model::begin_transaction);
		}
		$result = $this->model->insert($this);
		$this->dumpSql = $this->model->getSql();
		$this->unit_new();
		return $result;
	}

	public function replace($transaction_type = false) {
		if ($this->model->getPdoDriverType() != enum\PdoDriverType::PDO_MYSQL) {
			throw new \Exception("mysql驱动才支持replace", 1);
		}
		if ($transaction_type) {
			$this->model->transaction(Model::begin_transaction);
		}
		$result = $this->model->insert($this, true);
		$this->dumpSql = $this->model->getSql();
		$this->unit_new();
		return $result;
	}

	/**
	 * 取得插入或者修改的最后一个id
	 * @Author   Lerko
	 * @DateTime 2017-04-08T15:53:55+0800
	 * @return   [type]                   [description]
	 */
	public function getLastId(){
		$help=new MysqlDbHelp();
		$id=$help->query("select last_insert_id() as id;");
		foreach ($id as $key => $value) {
			return $value['id'];
		}
	}

	/**
	 * 从Unit中解析成接口
	 * @return array 绑定的value数组
	 */
	public function fetchSql($type=FetchType::select) {
		$bindValues = $this->model->fetchSql($this,$type);
		$this->dumpSql = $this->model->getSql();
		$this->unit_new();
		return $bindValues;
	}

	public function start() {
		$this->model->transaction(Model::begin_transaction);
	}

	public function rollback() {
		$this->model->transaction(Model::rollback_transaction);
	}
	public function commit() {
		$this->model->transaction(Model::commit_transaction);
	}

	public function getModel() {
		return $this->model;
	}

	public function dumpSql() {
		var_dump($this->dumpSql);
	}

	public function sql() {
		return $this->dumpSql;
	}

	private $errormsg;
	public function error()
	{
		return $this->errormsg;
	}
}