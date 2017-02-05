<?php
namespace Phero\Database\Realize;

use Phero\Database\Enum\DatabaseConfig;
use Phero\Database\PDO;
use Phero\System\Config;
use Phero\System\DI;
use Phero\System\Traint\TInject;

/**
 *pdo链接仓库
 *pdo链接从这里获取
 *这里会对多台mysql机器进行分配
 */
class PdoWarehouse {
	use TInject;

	protected $pdo_hit;
	private static $already_init = false;

	/**
	 * @Inject[di=pdo_instance]
	 * @var [type]
	 */
	protected $pdo;

	CONST read = 0;
	CONST write = 1;

	private function __construct() {}
	public static function getInstance() {
		return new PdoWarehouse();
	}

	//根据数据
	public function getPdo($pattern) {
		$database_config = Config::config("database");
		$hit_classname = Config::config('hit_rule');
		$this->pdo_hit = new $hit_classname;
		$this->init($database_config);
		//注入后解析
		$this->inject();
		if (is_array($this->pdo)) {
			if ($pattern == 0) {
				$pdo = $this->pdo_hit->hit($this->pdo['servlet']);

			} else {
				$pdo = $this->pdo['master'];
			}
		} else {
			$pdo = $this->pdo;
		}
		$charset =Config::config('hit_rule');
		$charset=isset($charset)?"utf8":$charset;
		$pdo->exec("set names $charset");
		$pdo->exec("set character_set_client=$charset");
		$pdo->exec("set character_set_results=$charset");
		return $pdo;
	}
	private function init($config) {
		$pdo_di = DI::get(DatabaseConfig::pdo_instance);
		if ($pdo_di) {
			return;
		}
		if (array_key_exists("dsn", $config)) {
			DI::inj(DatabaseConfig::pdo_instance, new PDO($config['dsn'], $config['user'], $config['password']));
		} elseif (array_key_exists('master', $config)) {
			$master = $config['master'];
			$servlet = $config['servlet'];
			$servlet_pdo = [];
			foreach ($servlet as $value) {
				$servlet_pdo[] = new PDO($value['dsn'], $value['user'], $value['password']);
			}
			$pdo = [
				"master" => new PDO($master['dsn'], $master['user'], $master['password']),
				"servlet" => $servlet_pdo,
			];
			DI::inj(DatabaseConfig::pdo_instance, $pdo);
		}
	}
}
