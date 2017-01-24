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
	 * di的key
	 * 优先级最高
	 * 会覆盖class
	 * @var [type]
	 */
	public $di;
}