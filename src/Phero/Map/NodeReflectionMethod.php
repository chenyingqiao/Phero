<?php
namespace Phero\Map;
use Phero\Map\Interfaces\INode;

/**
 * 自定义函数反射实例
 */
class NodeReflectionMethod extends \ReflectionMethod implements INode {
	use Resolve;
}