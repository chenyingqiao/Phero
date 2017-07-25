<?php
namespace Phero\Database\Interfaces;
/**
 * Created by PhpStorm.
 * User: chen
 * Date: 16-12-16
 * Time: 上午10:44
 * 在多个从数据库中选取一个来进行读取
 * 这里写入的是读取规则
 */

interface IDbSlaveHit {
	public function hit(&$slave);
}