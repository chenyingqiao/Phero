<?php 

namespace Phero\Database\Interfaces;
/**
 * @Author: ‘chenyingqiao’
 * @Date:   2017-06-04 14:25:44
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-06 11:18:10
 */

interface INodeMap {
	/**
	 * 设置node的map
	 * @Author   Lerko
	 * @DateTime 2017-06-06T10:32:20+0800
	 * @param    [type]                   $noteName [description]
	 * @param    [type]                   $value    [description]
	 * @return   [type]                             [返回本身]
	 */
	public function map($note,$value=false);
	/**
	 * 获取map节点
	 * @Author   Lerko
	 * @DateTime 2017-06-06T10:32:38+0800
	 * @param    [type]                   $nodeName [description]
	 * @return   [type]                             [如果没有找到返回false]
	 */
	public function getMap($nodeName);
}