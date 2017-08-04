<?php
namespace Phero\Database\Realize;

use Phero\Database as database;
use Phero\Database\Enum\RelType;
use Phero\Database\Interfaces as interfaces;
use Phero\Database\Interfaces\IRelation;
use Phero\Database\Realize\PdoWarehouse;
use Phero\Database\Realize\Hit\RandomSlaveHit;
use Phero\Database\Traits\TRelation;
use Phero\System\Config;
use Phero\System\Tool;
use Phero\System\Traits\TInject;

/**
 * 数据库
 */
class MysqlDbHelp implements interfaces\IDbHelp {
	use TRelation;
	use TInject;

	const begin_transaction = 1;
	const rollback_transaction = 2;
	const commit_transaction = 3;

	protected $pdo;

	private $mode, $classname;

	private $error=false;

	private $entiy;

	private $enableRelation=false;

	private $pdoType=PdoWarehouse::read;

	/**
	 * @Inject[di=pdo_hit]
	 * @var [type]
	 */
	protected $pdo_hit;

	public function __construct() {
		$this->inject();
		$this->pdo = PdoWarehouse::getInstance()->getPdo();
		$fetch_mode=Config::config("fetch_mode");
		$this->mode = Tool::getInstance()->getConfigMode($fetch_mode);
		if(!isset($this->pdo_hit)){
			$this->pdo_hit=new RandomSlaveHit();
		}
	}

	/**
	 * 获取pdo实例
	 * @method getPdoByType
	 * @param  [type]       $type [description]
	 * @return [type]             [description]
	 */
	private function &getPdo($type){
		$this->pdoType=$type;
		if(is_object($this->pdo)){
			return $this->pdo;
		}
		if($type==PdoWarehouse::write||empty($this->pdo['slave'])){
			return $this->pdo['master'];
		}elseif($type==PdoWarehouse::read){
			$pdo_result=&$this->pdo_hit->hit($this->pdo['slave']);
			return $this->pdo['slave'][0];
		}
	}

	/**
	 * 返回影响的行数
	 * @param  [type] $sql  [PDOStatement对象或者是sql语句]
	 * @param  array  $data [绑定的数据]
	 * @return [type]       [返回影响的行数 0 就是没有修改或者插入成功]
	 */
	public function exec($sql, $data = [],$type=RelType::insert) {
	    $this->enableRelation=$this->getRelationIsEnable($this->entiy);
		$data = $data == null ? [] : $data;
		$Statement=$this->sqlPrepare($sql,PdoWarehouse::write);
		if(!$Statement)return 0;
		$this->sql_bind_execute($Statement, $data);
		$result = $Statement->rowCount();

		$is_realtion = false;
		if ($result&&$this->enableRelation) {
			$realtion_effect = $this->exec_relation($this->entiy,$type);
			if (isset($realtion_effect) && $realtion_effect > 0) {
				return $result;
			} else {
				$this->error="关联表数据操作失败";
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
		$data = $data == null ? [] : $data;
		$Statement=$this->sqlPrepare($sql,PdoWarehouse::read);
		if(!$Statement)return [];
		$this->sql_bind_execute($Statement, $data);
		$result_data = [];
		$result_data=$Statement->fetchAll($this->mode);
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
		$data = $data == null ? [] : $data;
		$Statement=$this->sqlPrepare($sql,PdoWarehouse::read);
		if(!$Statement)yield null;
		$this->sql_bind_execute($Statement, $data);
		while ($result = $Statement->fetch($this->mode)) {
            yield $result;
		}
		yield null;
	}

	private function bindData(&$sql, $data = []) {
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$sql->bindValue($value[0], $value[1], $value[2]);
			} else {
				if (is_array($value)&&isset($value[1])) {
					$sql->bindValue($key, $value[0], $value[1]);
				} else {
					$sql->bindValue($key, $value);
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
	public function setEntiy(&$entiy) {
		$this->entiy = $entiy;
	}

	public function getDbConn() {
		return $this->getPdo($this->pdoType);
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
	 * 对sql进行准备获取Statement
	 * @method sqlPrepare
	 * @param  [type]     $sql [description]
	 * @return [type]          [description]
	 */
	private function sqlPrepare($sql,$pdo_type){
		$pdo = &$this->getPdo($pdo_type);
		if (is_string($sql)) {
			try {
				$backup_sql=$sql;
				$sql = $pdo->prepare($sql);
				if(!$sql){
					$pdo_error_info=$pdo->errorInfo();
					$this->error=$pdo->errorCode()." | $pdo_error_info[0]:$pdo_error_info[1]---$pdo_error_info[2]";
					return 0;
				}
				$sql->sql=$backup_sql;//将字符串的sql存储起来
			} catch (\PDOException $e) {
				$this->error=$e->getMessage();
				return 0;
			}
			if(empty($sql)){
				$this->error="sql prepare 失败 请检查表明或者字段名称是否错误！";
				return 0;
			}
		}
		return $sql;
	}
	/**
	 * 绑定sql数据并且执行sql
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	private function sql_bind_execute(&$sql, $data) {
		$this->PDOStatementFactory($sql);
		$this->bindData($sql, $data);
		@$result=$sql->execute();
		//这里如果execute失败就检查是不是mysql断线了
		$errorCode=$sql->errorCode();
		$errorInfo=$sql->errorInfo();
		if($errorCode=="HY000"||$errorInfo=="MySQL server has gone away"){
			if(Config::config("debug"))
				echo "断线重连\n";
			$this->reConnect();
			if(isset($sql->sql))
				$sql=$this->sqlPrepare($sql->sql,$this->pdoType);
			$this->PDOStatementFactory($sql);
			$this->bindData($sql, $data);
			$result=$sql->execute();
		}
		$this->errorMessage($sql);
	}

	/**
	 * 断线重新链接
	 * @Author   Lerko
	 * @DateTime 2017-07-27T13:36:14+0800
	 * @return   [type]                   [description]
	 */
	private function reConnect(){
		$this->pdo = PdoWarehouse::getInstance()->getPdo();
	}

	public function transaction($type)
	{
		$pdo=&$this->getPdo(PdoWarehouse::write);
		if ($type == self::begin_transaction) {
			if ($pdo->inTransaction()) {
				if (!(get_class($pdo) == "Phero\Database\PDO")) {
					throw new \Exception("原生pdo类不支持事务嵌套", 1);
				}
			}
			$pdo->beginTransaction();
		} elseif ($type == self::rollback_transaction) {
			$pdo->rollBack();
		} elseif ($type == self::commit_transaction) {
			$pdo->commit();
		}
	}

	public function disconnect(&$pdo)
	{
		$pdo=null;
	}
}
