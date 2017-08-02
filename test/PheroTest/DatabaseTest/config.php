<?php
/**
 * @Author: lerko
 * @Date:   2017-05-27 18:12:52
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-08-02 21:50:36
 */
return [
	"database" => [
		"master" => [
			"dsn" => "mysql:dbname=phero;host=127.0.0.1",
			"user" => "root",
			"password" => "lerko",
		],
		"attr"=>[],//pdo初始化的时候会进行赋值  比如长链接的配置
		"slave" => [
			[
				"dsn" => "mysql:dbname=phero;host=172.17.0.2",
				"user" => "root",
				"password" => "123456",
			],
			// [
			// 	"dsn" => "mysql:dbname=kn_erp_db;host=172.17.0.4",
			// 	"user" => "admin",
			// 	"password" => "password",
			// ],
		],
	],
	"swoole"=>[
		"worker_num"=>16,
		"pool_num"=>10,
		"worker_num_block"=>100,
		"pool_num_block"=>20
	],
	"cache"=>'redis://127.0.0.1',
	"debug"=>true
];
