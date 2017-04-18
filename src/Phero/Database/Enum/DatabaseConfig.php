<?php
namespace Phero\Database\Enum;

/**
 *
 */
class DatabaseConfig {
	CONST DatabaseConnect = "DatabaseConnect"; //是否开启注解缓存
	CONST pdo_instance = "pdo_instance";
	CONST pdo_warehouse = "pdo_warehouse"; //pdo仓库
	CONST pdo_hit = "pad_hit"; //mysql 从服务器选择规则
	CONST all_config_path = "all_config_path";
}