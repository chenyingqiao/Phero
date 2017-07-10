<?php 

namespace Phero\System;

use Phero\Database\DbUnit;
use Phero\Map\NodeReflectionClass;
use Phero\System\Interfaces\ISection;
/**
 * @Author: lerko
 * @Date:   2017-06-07 17:55:17
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-07 19:41:52
 */
class Section implements ISection
{
	private static $sections=[];
	/**
	 * 执行切面
	 * @Author   Lerko
	 * @DateTime 2017-06-07T18:12:44+0800
	 * @param    [type]                   $realize [实现类]
	 * @param    [type]                   $fun     [调用类中函数的名称]
	 * @param    [type]                   $args    [参数]
	 */
	public static function run($realize,$args)
	{
		$reflection=new NodeReflectionClass($realize);
		$interface=$reflection->getInterfaceNames()[0];
		$method=self::$sections[$interface];
		$method=$reflection->getMethod($method);
		$method->invokeArgs($realize,$args);
	}

	/**
	 * 绑定切面
	 * @Author   Lerko
	 * @DateTime 2017-06-07T19:30:01+0800
	 * @param    [type]                   $interface [接口名称]
	 * @param    [type]                   $evn_class [运行的类环境]
	 * @return   [type]                              [description]
	 */
	public static function hook($interface,$evn_class=null)
	{
		$reflectionInterface=new NodeReflectionClass($interface);
		$firstMethodName=$reflectionInterface->getMethods()[0];//存下来第一个函数，默认调用也是第一个函数
		if($evn_class!==null)
			$classname=get_class($evn_class);
		else
			$classname="";
		self::$sections[$interface]=[$firstMethodName,$classname];
	}
}