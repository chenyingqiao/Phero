<?php
namespace Phero\Map;
use Phero\Map\Interfaces\INode;

/**
 *
 */
class NodeReflectionProperty extends \ReflectionProperty implements INode {
	use Resolve;
}