<?php 
/**
 * @Author: lerko
 * @Date:   2017-05-27 18:12:52
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-05-27 18:13:05
 */
return [
	"database" => [
		"master" => [
			"dsn" => "mysql:dbname=blog;host=localhost",
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
];