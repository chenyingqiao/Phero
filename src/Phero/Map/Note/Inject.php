<?php
namespace Phero\Map\Note;

/**
 *
 *通过类去注入
 *@Inject[]
 *
 */
class Inject {
	/**
	 * 实现类
	 * 不需要注入直接实例化
	 * @var [type]
	 */
	// public $class;

	/**
	 * 超类的限制
	 * @var [type]
	 */
	// public $super;

	/**
	 * di的key
	 * 优先级最高
	 * 会覆盖class
	 * @var [type]
	 */
	public $di;
}