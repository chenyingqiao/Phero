<?php 

namespace Phero\System;

use Phero\Database\Model;
/**
 * @Author: ‘chenyingqiao’
 * @Date:   2017-04-23 10:50:45
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-04-23 11:05:58
 */

/**
* 
*/
class Tool
{
	private static $tool;
	private function __construct(){}
	public static function getInstance()
	{
		if(empty(self::$tool)){
			self::$tool=new Tool();
		}
		return self::$tool;
	}

	/**
	 * 获取配置项目对应的获取数据的类型
	 * @Author   Lerko
	 * @DateTime 2017-04-23T10:56:25+0800
	 * @param    [type]                   $configValue [description]
	 * @return   [type]                                [description]
	 */
	public function getConfigMode($configValue){
		switch ($configValue) {
			case "Object":
					return Model::fetch_obj;
			case "ArrAndNumber":
					return Model::fetch_arr_numberAkey;
			case "Number":
					return Model::fetch_arr_number;
			case "Key":
					return Model::fetch_arr_key;
			default:
					return Model::fetch_arr_key;
		}
	}
}