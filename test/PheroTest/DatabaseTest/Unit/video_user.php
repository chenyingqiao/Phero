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
	 * @Field[type=int]
	 * @var [type]
	 */
	public $uid;
	/**
	 * @Field[alias=um,type=string]
	 * @var [type]
	 */
	public $username;
	/**
	 * @Field[alias=pwd,type=string]
	 * @var [type]
	 */
	public $password;
}