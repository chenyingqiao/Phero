<?php
namespace Phero\Database\Realize;

use Phero\Database\Enum\DatabaseConfig;
use Phero\Database\PDO;
use Phero\System\Config;
use Phero\System\DI;
use Phero\System\Traits\TInject;
use Phero\Database\Realize\Hit\RandomSlaveHit;

/**
 *pdo链接仓库
 *pdo链接从这里获取
 *这里会对多台mysql机器进行分配
 */
class PdoWarehouse {
	use TInject;

	/**
	 * @Inject[di=pdo_hit]
	 * @var [type]
	 */
	protected $pdo_hit;
	private static $already_init = false;

	/**
	 * @Inject[di=pdo_instance]
	 * @var [type]
	 */
	protected $pdo;

	private static $PdoWarehouse=null;

	CONST read = 0;
	CONST write = 1;

	private function __construct() {}
	public static function getInstance() {
		if(!isset(self::$PdoWarehouse))
			self::$PdoWarehouse=new PdoWarehouse();
		return self::$PdoWarehouse;
	}

	//根据数据
	public function getPdo() {
		$database_config = Config::config("database");
		//注入后解析
		$this->inject();
		if (empty($this->pdo_hit)) {
			$hit_classname = new RandomSlaveHit();
		}
		$this->init($database_config);
		$pdo=$this->pdo;
		$this->setPdo($pdo);
		return $pdo;
	}

	private function setPdo(&$pdo){
		if(!is_array($pdo)){
			$this->setPdoItem($pdo);
			return;
		}
		//设置master
		if(is_array($pdo["master"])){
			foreach ($pdo['master'] as $key => &$value) {
					$this->setPdoItem($value);
			}
		}else{
			$this->setPdoItem($pdo["master"]);
		}
		//设置slave
		if(isset($pdo['slave'])){
			foreach($pdo['slave'] as $key=>$value){
					$this->setPdoItem($value);
			}
		}
	}

	/**
	 * 设置初始化pdo链接
	 * @method setPdoItem
	 * @param  [type]     $pdo [description]
	 */
	public function setPdoItem(&$pdo)
	{
		$charset = Config::config('charset');
		$charset = empty($charset) ? "utf8" : $charset;
		$pdo->exec("set names $charset");
		$pdo->exec("set character_set_client=$charset");
		$pdo->exec("set character_set_results=$charset");
		//PDO::ATTR_STRINGIFY_FETCHES 提取的时候将数值转换为字符串。
		//PDO::ATTR_EMULATE_PREPARES 启用或禁用预处理语句的模拟。
		$pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
		$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	}

	private function init($config) {
		if(!$config&&empty($this->pdo)){
			throw new \Exception("Do not specify a configuration file", 1);
		}
		$pdo_config=[];
		if(isset($config["database"]["attr"])){
			$pdo_config=$config["database"]["attr"];
		}
		$pdo_di = DI::get(DI::pdo_instance);
		if (!empty($pdo_di)) {
			$this->pdo=$pdo_di;
			return;
		}
		if (array_key_exists("dsn", $config)) {
			$this->pdo= new PDO($config['dsn'], $config['user'], $config['password'],$pdo_config);
		} elseif (array_key_exists('master', $config)) {
			$master = $config['master'];
			$slave_pdo = [];
			if (!empty($config['slave'])){
				$slave = $config['slave'];
				foreach ($slave as $value) {
					$slave_pdo[] = new PDO($value['dsn'], $value['user'], $value['password'],$pdo_config);
				}
			}
			$master_pdo=[];
			if(!empty($config['master'])&&is_array($config['master'])&&!array_key_exists("dsn",$config['master'])){
				foreach ($config['master'] as $key => $value) {
					$master_pdo[]=new PDO($value['dsn'], $value['user'], $value['password'],$pdo_config);
				}
			}else if(!empty($config['master'])){
				$value=$config['master'];
				$master_pdo=new PDO($value['dsn'], $value['user'], $value['password'],$pdo_config);
			}
			$this->pdo = [
				"master" => $master_pdo,
				"slave" => $slave_pdo,
			];
		}
	}
}
