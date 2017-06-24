<?php 

namespace Phero\System\Interfaces;
/**
 * @Author: lerko
 * @Date:   2017-06-07 17:27:01
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-07 17:56:26
 */
interface ISection
{
	/**
	 * 执行切面
	 * @Author   Lerko
	 * @DateTime 2017-06-07T17:41:49+0800
	 * @param    [type]                   $realize [description]
	 * @return   [type]                            [description]
	 */
	public static function run($realize);

	/**
	 * 绑定切面
	 * @Author   Lerko
	 * @DateTime 2017-06-07T17:42:23+0800
	 * @param    [type]                   $interface [description]
	 * @return   [type]                              [description]
	 */
	public static function hook($interface);
}