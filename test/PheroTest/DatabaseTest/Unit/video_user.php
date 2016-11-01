<?php
namespace PheroTest\DatabaseTest\Unit;

use Phero\Database\DbUnit;

/**
 * @Table[alias=cd]
 */
class video_user {
	use DbUnit;
	/**
	 * @Primary
	 * @DbType[type=int]
	 * @var [type]
	 */
	public $uid;
	/**
	 * @DbType[type=string]
	 * @var [type]
	 */
	public $username;
	/**
	 * @DbType[type=string]
	 * @var [type]
	 */
	public $password;
}