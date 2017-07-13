<?php
namespace Phero\Database\Realize;

use Phero\Database as database;
use Phero\Database\Enum\RelType;
use Phero\Database\Interfaces as interfaces;
use Phero\Database\Interfaces\IRelation;
use Phero\Database\Realize\PdoWarehouse;
use Phero\Database\Traits\TRelation;
use Phero\System\Config;
use Phero\System\Tool;

/**
 * 数据库
 */
class MysqlDbHelp implements interfaces\IDbHelp {
	use TRelation;

	const begin_transaction = 1;
	const rollback_transaction = 2;
	const commit_transaction = 3;

	protected $pdo;

	private $mode, $classname;

	private $error=false;

	private $entiy;

    private $enableRelation=false;

	public function __construct() {
		$this->pdo = PdoWarehouse::getInstance()->getPdo(PdoWarehouse::write);
		$fetch_mode=Config::config("fetch_mode");
		$this->mode = Tool::getInstance()->getConfigMode($fetch_mode);
	}

	/**
	 * 返回影响的行数
	 * @param  [type] $sql  [PDOStatement对象或者是sql语句]
	 * @param  array  $data [绑定的数据]
	 * @return [type]       [返回影响的行数 0 就是没有修改或者插入成功]
	 */
	public function exec($sql, $data = [],$type=RelType::insert) {
        $this->enableRelation=$this->getRelationIsEnable($this->entiy);
        if(!isset($this->pdo))//避免一个事务出现多个pdo这样造成事务不连续
	        $this->pdo = PdoWarehouse::getInstance()->getPdo(PdoWarehouse::write);
		$data = $data == null ? [] : $data;
		if (is_string($sql)) {
			try {
				$sql = $this->pdo->prepare($sql);
			} catch (\PDOException $e) {
				$this->error=$e->getMessage();
				return 0;
			}
			if(empty($sql)){
				$this->error="sql prepare 失败 请检查表名或者字段名称或者语句结构是否错误！";
				return 0;
			}
		}
		$this->sql_bind_execute($sql, $data);
		$result = $sql->rowCount();

		$is_realtion = false;
		if ($result&&$this->enableRelation) {
			$realtion_effect = $this->exec_relation($this->entiy,$type);
			if (isset($relation_data) && $relation_data > 0) {
				return $result;
			} else {
				$this->error="关联表数据写入失败";
				return 0;
			}
		}
		return $result;
	}

	/**
	 * 返回结果集
	 * @param  [type] $sql  [PDOStatement对象或者是sql语句]
	 * @param  array  $data [绑定的数据]
	 * @return array [返回结果集]
	 */
	public function queryResultArray($sql, $data = []) {
        $this->enableRelation=$this->getRelationIsEnable($this->entiy);
        $this->pdo = PdoWarehouse::getInstance()->getPdo(PdoWarehouse::read);
		$data = $data == null ? [] : $data;
		if (is_string($sql)) {
			try {
				$sql = $this->pdo->prepare($sql);
			} catch (\PDOException $e) {
				$this->error=$e->getMessage();
				return 0;
			}
			if(empty($sql)){
				$this->error="sql prepare 失败 请检查表明或者字段名称是否错误！";
				return 0;
			}
		}
		$this->sql_bind_execute($sql, $data);
		$result_data = [];
		$result_data=$sql->fetchAll($this->mode);
		if($this->enableRelation)
			$this->relation_select($result_data,$this->entiy);
		return $result_data;
	}

	/**
	 * 返回结果集 不支持关联查询
	 * @param  [type] $sql  [PDOStatement对象或者是sql语句]
	 * @param  array  $data [绑定的数据]
	 * @return array [返回结果集]
	 */
	public function query($sql, $data = []) {
        $this->enableRelation=$this->getRelationIsEnable($this->entiy);
		$this->pdo = PdoWarehouse::getInstance()->getPdo(PdoWarehouse::read);
		$data = $data == null ? [] : $data;
		if (is_string($sql)) {
			try {
				$sql = $this->pdo->prepare($sql);
			} catch (\PDOException $e) {
				$this->error=$e->getMessage();
			}
			if(empty($sql)){
				$this->error="sql prepare 失败 请检查表明或者字段名称是否错误！";
				yield 0;
			}
		}
		$this->sql_bind_execute($sql, $data);
		while ($result = $sql->fetch($this->mode)) {
                yield $result;
		}
		yield null;
	}

	private function bindData(&$sql, $data = []) {
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$sql->bindValue($value[0], $value[1], $value[2]);
			} else {
				if (isset($data[2])) {
					$sql->bindValue($data[0], $data[1], $data[2]);
				} else {
					$sql->bindValue($data[0], $data[1]);
				}
				return;
			}
		}
	}

	public function PDOStatementFactory(&$PDOStatement) {
		if ($this->mode != database\Model::fetch_obj) {
			$PDOStatement->setFetchMode($this->mode);
		}
		if (!empty($this->mode) && !empty($this->classname) && $this->mode == database\Model::fetch_obj) {
			$PDOStatement->setFetchMode($this->mode, $this->classname,array());
		}
		// $this->mode = empty($this->mode)?database\Model::fetch_arr_key:$this->mode;
		// $this->classname =empty($this->classname)?null:$this->;
	}

	/**
	 * 设置遍历模式
	 * @param [type] $mode      [description]
	 * @param [type] $classname [指定FETCH_CLASS遍历模型对应的生成类]
	 */
	public function setFetchMode($mode, $classname = null) {
		$this->mode = $mode;
		$this->classname = $classname;
		return $this;
	}
	public function error() {
		return $this->error;
	}
	private function errorMessage($state) {
		$info = $state->errorInfo();
		$this->error = "[error:code]:" . $state->errorCode() . "[error:info]:";
		foreach ($info as $key => $value) {
			$this->error .= $value . "	";
		}
	}
	public function setEntiy($entiy) {
		$this->entiy = $entiy;
	}

	public function getDbConn() {
		return $this->pdo;
	}



	/**
	 * 更新 插入 删除   本身不使用事务进行包裹
	 * @Author   Lerko
	 * @DateTime 2017-06-14T11:34:01+0800
	 * @param    [type]                   $entiy [需要关联写入的实体]
	 * @param    [type]                   $type  [关联写入的类型]
	 * @return   [type]                          [返回影响的行数]
	 */
	private function exec_relation($entiy,$type) {
		if ($entiy instanceof IRelation) {
			$entiy->rel($type, $entiy);
		}
		switch ($type) {
			case RelType::update:{
					return $this->relation_update($entiy);
				};
			case RelType::insert:{
					return $this->relation_insert($entiy);
				};
			case RelType::delete:{
					return $this->relation_delete($entiy);
			};
		}
	}

	/**
	 * 绑定sql数据并且执行sql
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	private function sql_bind_execute(&$sql, $data) {
		$this->PDOStatementFactory($sql);
		$this->bindData($sql, $data);
		$sql->execute();
		$this->errorMessage($sql);
	}

	public function transaction($type)
	{
		if ($type == self::begin_transaction) {
			if ($this->pdo->inTransaction()) {
				if (!(get_class($this->pdo) == "Phero\Database\PDO")) {
					throw new \Exception("原生pdo类不支持事务嵌套", 1);
				}
			}
			$this->pdo->beginTransaction();
		} elseif ($type == self::rollback_transaction) {
			$this->pdo->rollBack();
		} elseif ($type == self::commit_transaction) {
			$this->pdo->commit();
		}
	}
}