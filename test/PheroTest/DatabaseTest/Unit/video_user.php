<?php
namespace PheroTest\DatabaseTest\Unit;

use Phero\Database\DbUnit;

/**
 * @Table[alias=cd]
 */
class video_user extends DbUnit {

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
	 * @Field
	 * @var [type]
	 */
	public $password;
}