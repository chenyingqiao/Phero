# 配置

## 配置文件实例

```php
<?php
/**
 * @Author: lerko
 * @Date:   2017-05-27 18:12:52
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-07-28 13:25:20
 */
return [
	"database" => [
        # 主数据库
		"master" => [
			"dsn" => "mysql:dbname=phero;host=127.0.0.1",
			"user" => "root",
			"password" => "lerko",
		],
        #从数据库
		"slave" => [
			[
				"dsn" => "mysql:dbname=kn_erp_db;host=172.17.0.3",
				"user" => "admin",
				"password" => "password",
			],
			[
				"dsn" => "mysql:dbname=kn_erp_db;host=172.17.0.4",
				"user" => "admin",
				"password" => "password",
			],
		],
        "attr"=>[],//pdo初始化的时候会进行赋值  比如长链接的配置
	],
	"swoole"=>[
		"worker_num"=>16,//线程池的worker进程的数量 cpu核心数的2倍
		"pool_num"=>100,//链接池的max数量
		// "worker_num_block"=>100,
		// "pool_num_block"=>20
	],
	"cache"=>'redis://127.0.0.1',//缓存数据的介质
	"debug"=>true,//是否开启debug (注解会重复解析不进行缓存，线程池会打印调试数据)
];
```

## 如何指定配置文件

> 需要放到DI容器中

```php
DI::inj("config",$you_config_file_path);#指定文件的地址 可以放置在任意php可以写的目录
```
