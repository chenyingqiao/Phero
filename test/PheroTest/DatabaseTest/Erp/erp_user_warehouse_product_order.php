<?php
namespace PheroTest\DatabaseTest\Erp;
use Phero\Database\DbUnit;

/**
 * @Table[alias=uwpo]
 */
class erp_user_warehouse_product_order extends DbUnit {
	/**
	 * @Field
	 */
	public $id;
	/**
	 * @Field
	 */
	public $user_id;
	/**
	 * @Field
	 */
	public $root_id;
	/**
	 * @Field
	 */
	public $product_id;
	/**
	 * @Field
	 */
	public $order_sn;
	/**
	 * @Field
	 */
	public $out_warehouse_id;
	/**
	 * @Field
	 */
	public $in_warehouse_id;
	/**
	 * @Field
	 */
	public $in_shelf_id;
	/**
	 * @Field
	 */
	public $remark;
	/**
	 * @Field
	 */
	public $sku_number;
	/**
	 * @Field
	 */
	public $is_storage;
	/**
	 * @Field
	 */
	public $add_time;
	/**
	 * @Field
	 */
	public $edit_time;
}