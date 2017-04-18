<?php
namespace PheroTest\DatabaseTest\Erp;
use Phero\Database\DbUnit;

/**
 * @Table[alias=uwi]
 */
class erp_user_warehouse_info extends DbUnit {
/**
 * @Field
 * @var [type]
 */
	public $id;
/**
 * @Field
 * @var [type]
 */
	public $user_id;
/**
 * @Field
 * @var [type]
 */
	public $root_id;
/**
 * @Field
 * @var [type]
 */
	public $name;
/**
 * @Field
 * @var [type]
 */
	public $type;
/**
 * @Field
 * @var [type]
 */
	public $tel;
/**
 * @Field
 * @var [type]
 */
	public $mobile;
/**
 * @Field
 * @var [type]
 */
	public $mail;
/**
 * @Field
 * @var [type]
 */
	public $zipcode;
/**
 * @Field
 * @var [type]
 */
	public $country_id;
/**
 * @Field
 * @var [type]
 */
	public $province_id;
/**
 * @Field
 * @var [type]
 */
	public $city_id;
/**
 * @Field
 * @var [type]
 */
	public $region_id;
/**
 * @Field
 * @var [type]
 */
	public $addr;
/**
 * @Field
 * @var [type]
 */
	public $prepare_day;
/**
 * @Field
 * @var [type]
 */
	public $status;
/**
 * @Field
 * @var [type]
 */
	public $account_name;
/**
 * @Field
 * @var [type]
 */
	public $account_passwd;
/**
 * @Field
 * @var [type]
 */
	public $shop_id;
/**
 * @Field
 * @var [type]
 */
	public $warehouse_sites;
/**
 * @Field
 * @var [type]
 */
	public $create_time;
/**
 * @Field
 * @var [type]
 */
	public $update_user_id;
/**
 * @Field
 * @var [type]
 */
	public $update_time;
}