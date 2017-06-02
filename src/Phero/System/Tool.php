<?php 

namespace Phero\System;

use Phero\Database\Model;
/**
 * @Author: ‘chenyingqiao’
 * @Date:   2017-04-23 10:50:45
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-02 17:40:27
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

	/**
	 * 通过pdo的绑定还原原本的sql
	 * @Author   Lerko
	 * @DateTime 2017-05-31T15:22:13+0800
	 * @param    [type]                   $query  [description]
	 * @param    [type]                   $params [description]
	 * @return   [type]                           [description]
	 */
	public function showQuery($query, $params)
    {
        $keys = array();
        $values = array();
        
        # build a regular expression for each parameter
        foreach ($params as $key=>$value)
        {
            if (is_string($value[0]))
            {
                $keys[] = '/'.$value[0].'/';
            }
            else
            {
                $keys[] = '/[?]/';
            }
            
            if(is_numeric($value[1]))
            {
                $values[] = intval($value[1]);
            }
            else
            {
                $values[] = "'".$value[1] ."'";
            }
        }
        
        $query = preg_replace($keys, $values, $query, 1, $count);
        return $query;
    }

    /**
     * where替换$
     * @Author   Lerko
     * @DateTime 2017-06-02T14:45:06+0800
     * @param    [type]                   &$where        [description]
     * @param    [type]                   $relationTable [description]
     */
    public function setWhereRelation(&$where,$relationTablename,$selfTablename){
    	foreach ($where as $key => &$value) {
            if(!is_string($value[1])){
                continue;
            }
    		if(strstr($value[1],'#')){
    			$value[1]=str_replace("#", "`{$relationTablename}`", $value[1]);
                $value["sql_fregment"]=true;
    		}
    		if(strstr($value[0],'$')){
    			$value[0]=str_replace("$", "`{$relationTablename}`", $value[0]);
    		}
    	}
    }
}