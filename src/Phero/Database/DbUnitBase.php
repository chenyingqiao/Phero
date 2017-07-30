<?php

namespace Phero\Database;

use Phero\Cache\CacheOperationByConfig;
use Phero\Database\Enum as enum;
use Phero\Database\Enum\Cache;
use Phero\Database\Enum\FetchType;
use Phero\Database\Enum\JoinType;
use Phero\Database\Interfaces\INodeMap;
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
class DbUnitBase implements \ArrayAccess,INodeMap {
	use TConstraitTableDependent,
		ArrayAccessTrait,
		WhereUnitTrait,
		HavingUnitTrait,
		JoinUnitTrait,
		FieldUnitTrait,
		DataSourceUnitTrait,
		OtherUnitTrait;

	CONST GroupStart=1,GroupEnd=2,GroupDisbale=0;
	private $dumpSql;
	
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
	 *                          ======================================================================可以考虑可以添加不存在的函数string==========
	 * ]
	 * @param boolean $IniFalse [反向设置false false表示的是这个列不出现在select列表中]
	 */
	public function __construct($values = null) {
		// $this->model = new Model();
		$this->values_cache = $values;
		if($values!==null)
			$this->allFalse();
		$this->initField($values);
	}

	//ORM
	/**
	 * 查询方法
	 * @Author   Lerko
	 * @DateTime 2017-06-08T17:53:58+0800
	 * @param    boolean|Cache                  $yield [是否进行一条一条数据的取出，传入cache的时候是进行数据缓存]
	 * @return   [type]                          [description]
	 */
	public function select($yield = false) {
		$cache=false;
		$sql="";
		$this->fetchSql($sql);
		if($yield instanceof Cache){
			$cacheObj=$yield;
			$cache=true;
			$yield=false;
			$data=CacheOperationByConfig::read(md5($sql));
			if(!empty($data)){
				return $data;
			}
		}
		$result = $this->getModel()->select($this, $yield);
		$this->dumpSql = $sql;
		$this->unit_new();
		if($cache){
			CacheOperationByConfig::save(md5($sql),$result,$cacheObj->liveTime);
		}
		return $result;
	}
	/**
	 * 通过本实体类更新数据
	 * @param  boolean $transaction_type [更新时是否开启一个事务]
	 * @return [type]                    [description]
	 */
	public function update() {
		$result = $this->getModel()->update($this);
		$this->dumpSql = $this->getModel()->getSql();
		$this->unit_new(false);
		return $result;
	}
	/**
	 * [通过本实体类删除数据]
	 * @param  boolean $transaction_type [更新时是否开启一个事务]
	 * @return [type]                    [description]
	 */
	public function delete() {
		$result = $this->getModel()->delete($this);
		$this->dumpSql = $this->getModel()->getSql();
		$this->unit_new(false);
		return $result;
	}
	/**
	 * [通过本实体类插入数据]
	 * @param  boolean $transaction_type [更新时是否开启一个事务]
	 * @return [type]                    [description]
	 */
	public function insert() {
		$result = $this->getModel()->insert($this);
		$this->dumpSql = $this->getModel()->getSql();
		$this->unit_new(false);
		return $result;
	}

	public function replace() {
		// if(!$this->checkSaveForUpdateOrDelete()){
		// 	throw new \Exception("You are using safe delete mode and you tried to delete a table without a WHERE that uses a KEY column To disable safe mode");
		// }
		if ($this->getModel()->getPdoDriverType() != enum\PdoDriverType::PDO_MYSQL) {
			throw new \Exception("mysql驱动才支持replace", 1);
		}
		$result = $this->getModel()->insert($this, true);
		$this->dumpSql = $this->getModel()->getSql();
		$this->unit_new(false);
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
	public function fetchSql(&$sql="",$type=FetchType::select) {
		$this->checkSaveForUpdateOrDelete();
		$bindValues = $this->getModel()->fetchSql($this,$type);
		$this->dumpSql = $this->getModel()->getSql();
		$sql=$this->dumpSql;
		return $bindValues;
	}

	public function start() {
		$this->getModel()->transaction(MysqlDbHelp::begin_transaction);
		return $this;
	}

	public function rollback() {
		$this->getModel()->transaction(MysqlDbHelp::rollback_transaction);
	}
	public function commit() {
		$this->getModel()->transaction(MysqlDbHelp::commit_transaction);
	}

	public function sql() {
		return $this->dumpSql;
	}

	public function setSql($value='')
	{
		$this->dumpSql=$value;
	}

	private $errormsg=false;
	public function error()
	{
		return $this->errormsg;
	}

	/**
	 * 获取当前类的实例化
	 * @Author   Lerko
	 * @DateTime 2017-06-04T13:22:26+0800
	 */
	private static $LastInc=[];
	public static function Inc($data=null){
		$classname=get_called_class();
		self::$LastInc[$classname] = new $classname($data);
		return self::$LastInc[$classname];
	}

	/**
	 * 获取一个单独实例，这个实例是Inc创建,
	 * 如果Inc没有创建那么就会自己创建一个实例
	 * @Author   Lerko
	 * @DateTime 2017-06-04T13:45:16+0800
	 * @return   [type]                   [description]
	 */
	public static function lastInc($data=null){
		$classname=get_called_class();
		if(!isset(self::$LastInc[$classname])){
			self::$LastInc[$classname]=new $classname($data);
		}
		return self::$LastInc[$classname];
	}

	private $map=[];
	/**
	 * 存储map 用来外部设置注解数据
	 * @Author   Lerko
	 * @DateTime 2017-06-04T14:35:25+0800
	 * @param    [type]                   $noteName [相关node的类名]
	 * @param    [type]                   $value    [如果前面noteName是string的话，写入的值就是$value]
	 * @return   [type]                             [description]
	 */
	public function map($note,$value=false)
	{
		if($value===false){
			$value=$note;
		}
		if(is_object($note)){
			$NodeReflection = new \ReflectionClass($note);
			$NodeName = $NodeReflection->getName();
		}else{
			$NodeName=$note;
		}
		$this->map[$NodeName]=$value;
		return $this;
	}

	/**
	 * 获取map中设置的节点
	 * @Author   Lerko
	 * @DateTime 2017-06-06T10:50:31+0800
	 * @param    [type]                   $nodeName [description]
	 * @return   [type]                             [description]
	 */
	public function getMap($nodeName)
	{
		if(array_key_exists($nodeName,$this->map)){
			return $this->map[$nodeName];
		}else{
			return false;
		}
	}

	/**
	 * 快速获取列明和表明的拼接
	 * @Author   Lerko
	 * @DateTime 2017-06-06T14:38:07+0800
	 * @param    [type]                   $Field [description]
	 */
	public static function FF($Field){
		//这里调用了Inc使得实例池中的数据会被清空
		return "`".self::lastInc()->getNameByCleverWay(self::lastInc())."`.`$Field`";
	}
}