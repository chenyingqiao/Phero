<?php

use Symfony\Component\Cache\Simple\RedisCache;
/**
 * @Author: lerko
 * @Date:   2017-05-27 18:12:52
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-08 13:10:18
 */
return [
	"database" => [
		"master" => [
			"dsn" => "mysql:dbname=phero;host=127.0.0.1",
			"user" => "root",
			"password" => "lerko",
		],
		"persistent"=>true,
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
	"cache"=>RedisCache::createConnection('redis://127.0.0.1'),
	"debug"=>true
];
