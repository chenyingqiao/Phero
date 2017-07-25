<?php

use Symfony\Component\Cache\Simple\RedisCache;
/**
 * @Author: lerko
 * @Date:   2017-05-27 18:12:52
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-07-24 16:08:02
 */
return [
	"database" => [
		"master" => [
			"dsn" => "mysql:dbname=phero;host=127.0.0.1",
			"user" => "root",
			"password" => "lerko",
		],
		// "slave" => [
		// 	[
		// 		"dsn" => "mysql:dbname=kn_erp_db;host=172.17.0.3",
		// 		"user" => "admin",
		// 		"password" => "password",
		// 	],
		// 	[
		// 		"dsn" => "mysql:dbname=kn_erp_db;host=172.17.0.4",
		// 		"user" => "admin",
		// 		"password" => "password",
		// 	],
		// ],
	],
	"cache"=>'redis://127.0.0.1',
	"debug"=>true
];
