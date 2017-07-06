<?php 

namespace Phero\System\Interfaces\Section;
/**
 * @Author: lerko
 * @Date:   2017-06-07 17:43:05
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-07 17:49:17
 */
interface ISectionCacheSave{
	public function save($key,$data);
}