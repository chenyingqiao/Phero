<?php
namespace Phero\Database\Traits;

use Phero\Database\Enum as enum;
use Phero\Database\Enum\JoinType;
use Phero\Database\Model;

/**
 * 用来设置数据库实体类的一些携带数据
 * 以及基础功能
 */
class DbUnitBase implements \ArrayAccess {
	use TConstraitTableDependent,ArrayAccessTrait;

	CONST GroupStart=1,GroupEnd=2,GroupDisbale=0;

	private $model;

	/**
	 * 列是否需要as
	 * @var [type]
	 */
	public $have_as = true;

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
	public function __construct($values = null, $IniFalse = true) {
		$this->model = new Model();
		$this->values_cache = $values;
		$this->inifalse = $IniFalse;
	}

	protected $values_cache, $inifalse;

    /**
     * 初始化实体
     */
	protected function unit_new() {
		$this->errormsg=$this->model->getError();
		$this->model = new Model();
		$this->where = [];
		$this->having = [];
		$this->join = [];
		$this->datasourseJoinType = null;
		$this->fieldTemp = [];
		$this->groupBy = null;
		$this->limit = null;
		$this->order = null;
		$this->whereGroup = false;
		$this->havingGroup = false;
		$this->field = [];
		$this->datasourse = [];
		$this->distinct = false;
		//不管是查询还是插入都会把字段值全部重置成null
		//select存储的field描述存储在一个values_cache变量中
		$this->allNull();
	}

	/**
	 * 初始化列
	 * @param  [type] $values [description]
	 * @return [type]         [description]
	 */
	protected function initField($values, $IniFalse) {
		//判断是否吧除了需要初始化的值之外的数据设置成false[就是不需要查询]
		// if ($IniFalse && count($values) > 0 || $values == false) {
		// 	$this->allFalse();
		// }
		if (is_array($values)) {
			$setFiled = false;
			$keys = array_keys($values);
			//判断是否是数值key的数组
			if (is_numeric($keys[0])) {
				$setFiled = true;
			}
			foreach ($values as $key => $value) {
				if ($setFiled) {
					$this->$value = true;
				} else {
					$this->$key = $value;
				}
			}
		}
	}

	/**
	 * 查询的时候反向取消所有的列
	 * @return [type] [description]
	 */
	protected function allFalse() {
		$propertys = $this->getTablePropertyNames($this);
		//初始化所有的值null
		foreach ($propertys as $key => $value) {
			$this->$value = false;
		}
	}
	/**
	 * 数据插入的时候初始化为null
	 * @return [type] [description]
	 */
	protected function allNull() {
		$propertys = $this->getTablePropertyNames($this);
		//初始化所有的值null
		foreach ($propertys as $key => $value) {
			$this->$value = null;
		}
	}
	//查询条件列表
	protected $where = [];
	protected $having = [];
	protected $join = [];
	//数据源join方式
	protected $datasourseJoinType;
	//行列模板
	protected $fieldTemp = [];
	//分组
	protected $groupBy;
	//范围
	protected $limit;
	//排序
	protected $order;

	protected $whereGroup = false;
	protected $havingGroup = false;

	/**
	 * 用户主动设置的列
	 * @var array
	 */
	protected $field = [];
	protected $datasourse = [];

	protected $distinct = false;

	public function getWhere() {return $this->where;}
	public function getJoin() {return $this->join;}
	public function getFieldTemp() {return $this->fieldTemp;}
	public function getField() {return $this->field;}
	public function getDatasourse() {return $this->datasourse;}
	public function getGroup() {return $this->groupBy;}
	public function getLimit() {return $this->limit;}
	public function getOrder() {return $this->order;}
	public function getHaving() {return $this->having;}
	public function getDistinct() {return $this->distinct;}
	/**
	 * 获取单独添加数据源时设置的Join类型
	 * @return [type] [description]
	 */
	public function getDatasourseJoinType() {
		return $this->datasourseJoinType;
	}

	/**
	 * [where description]
	 * 设置条件语句
	 * @param  [type] $where [需要的参数
	 *                       ×数据库字段---index:0
	 *                       ×value数据---index:1
	 *                       -可选
	 *                       		比较符号 可选---index:2(默认未等号)
	 *                       		下个字段连接符 可选---index:3(默认为空字符串)
	 * ]
	 * @param  [type] $from  [来自那个表  如果是多表链接的话]
	 * @param  boolean $group     [是否进行where分组]
	 * @param  string  $whereTemp [where字段模板]
	 * @return [type]             [description]
	 */
	public function where($where, $from = null, $group = false, $whereTemp = "") {
		// if (!isset($where) || count($where) < 2) {
		// 	return;
		// }
		if (isset($from)) {
			$where['from'] = $from;
		}
		$group = $this->whereGroup;
		//这里的wheregroup是通过where进行添加的
		if ($group !== false) {
			$where['group'] = $group;
		}
		if (!empty($whereTemp)) {
			$where['temp'] = $whereTemp;
		}
		$this->where[] = $where;
		return $this;
	}

	/**
	 * where 分组标示符号
	 * @Author   Lerko
	 * @DateTime 2017-03-20T15:51:57+0800
	 * @return   [type]                   [description]
	 */
	public function setGroup($type=self::GroupStart){
		if($type==self::GroupStart)
			$this->whereGroup=1;
		else if($type==self::GroupEnd)
			$this->whereGroup=2;
		else
			$this->whereGroup=0;
		if($type!=self::GroupDisbale)
			$this->where(null,null,true);
	}

	/**
	 * 设置having
	 * @Author   Lerko
	 * @DateTime 2017-03-20T16:24:23+0800
	 * @param    [type]                   $having [description]
	 * @param    [type]                   $from   [description]
	 * @param    boolean                  $group  [description]
	 * @return   [type]                           [description]
	 */
	public function having($having, $from = null, $group = false) {
		if (!isset($having) || count($having) < 2) {
			return;
		}
		if (isset($from)) {
			$having['from'] = $from;
		}
		$group = $this->whereGroup;
		if ($group !== false) {
			$having['group'] = $group;
		}
		$this->having[] = $having;
		return $this;
	}

	/**
	 * 表链接
	 * $on 通过这样的替换符号标示
	 * $：标示是被关联的Entiy
	 * #：标示的关联的Entiy
	 *   $Entiy->join(new XX(),"$.uid=#.id");
	 */
	public function join($Entiy, $on, $joinType = JoinType::inner_join) {
		$this->join[] = [$Entiy, $on, $joinType];
		return $this;
	}

	public function field($field, $temp = "") {
		$temp_arr = [];
		$has_temp = false;
		if (!empty($temp)) {
			$has_temp = true;
		}
		if (is_array($field)) {
			if (count($field) != count($temp) && is_array($temp_arr)) {
				throw new \Exception("field and temp length not equle");
			}
			foreach ($field as $key => $value) {
				if (!$has_temp) {
					$this->field[] = $value;
				} else {
					$temp_arr[$value] = $temp[$key];
				}

			}
		} else {
			if (!$has_temp) {
				$this->field[] = $field;
			} else {
				$temp_arr[$field] = $temp;
			}

		}
		$this->fieldTemp($temp_arr);
		return $this;
	}
	/**
	 * 添加数据源
	 * @param  [type] $table [数据源  可以是子查询  也可以是一个表名]
	 * @param  [type] $as    [如果是子查询就必须要有别名]
	 * @param  [type] $on    [关联条件]
	 * @param  [type] $join  [join方式 默认内连接]
	 */
	public function datasourse($table, $as, $on, $join = null) {
		$this->datasourse[] = [$table, $as, $on, $join];
		return $this;
	}
	/**
	 * 设置字段的函数  字段用？标示
	 * 如  count(?)
	 * @param  [type] $temp [description]
	 * @return [type]       [description]
	 */
	public function fieldTemp($temp = []) {
		$this->fieldTemp = $temp;
		return $this;
	}
	/**
	 * 设置单独添加数据源时设置的Join类型
	 * @param  [type] $JoinType [description]
	 * @return [type]           [description]
	 */
	public function datasourseJoinType($JoinType) {
		$this->datasourseJoinType = $JoinType;
		return $this;
	}

	/**
	 * 分组列
	 * @param  [type] $field [description]
	 * @return [type]        [description]
	 */
	public function group($field) {
		$this->groupBy = $field;
		return $this;
	}

	/**
	 * 查询范围
	 * @param  [type] $start [description]
	 * @param  [type] $end   [description]
	 * @return [type]        [description]
	 */
	public function limit($start, $end = null) {
		$this->limit = [$start, $end];
		return $this;
	}

	/**
	 * 设置排序
	 * @param  [type] $field      [description]
	 * @param  [type] $order_type [description]
	 * @return [type]             [description]
	 */
	public function order($field, $order_type = null) {
		$this->order = [$field, $order_type];
		return $this;
	}

	public function distinct() {
		$this->distinct = true;
	}

	private $dumpSql;
	//ORM
	public function select($yield = false) {
		if(!empty($this->values_cache)){
			$this->allFalse();
		}
		$this->initField($this->values_cache, $this->inifalse);
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
		if(!empty($this->values_cache)){
			$this->allFalse();
		}
		$this->initField($this->values_cache, $this->inifalse);
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
		if(!empty($this->values_cache)){
			$this->allFalse();
		}
		$this->initField($this->values_cache, $this->inifalse);
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
		if(!empty($this->values_cache)){
			$this->allFalse();
		}
		$this->initField($this->values_cache, $this->inifalse);
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
	 * 从Unit中解析成接口
	 * @return array 绑定的value数组
	 */
	public function fetchSql() {
		$bindValues = $this->model->fetchSql($this);
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