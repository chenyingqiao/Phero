<?php
namespace Phero\Database\Traint;

use Phero\Map\NodeReflectionClass;
use Phero\Map\Note\Relation;

/**
 * summary
 */
trait TRelation {
	private function getRelation($entiy) {
		$nodeReflectionClass = new NodeReflectionClass($entiy);
		$relation = $nodeReflectionClass->getProperties();
		$properties = [];
		foreach ($relation as $key => $value) {
			$resolve = $value->resolve(new Relation());
			if ($resolve) {
				$property_name = $value->getName();
				if ($entiy->$property_name) {
					$properties[$property_name] = $entiy->$property_name;
				}
			}
		}
		return $properties;
	}

	/**
	 * 级联查询
	 * @param  [type] $entiy [这个是数据实体类  需要包含对应数据]
	 * @return [type]        [description]
	 */
	public function relation_select($entiy) {
		$relation = $this->getRelation($entiy);
		$data = [];
		foreach ($relation as $key => $value) {
			$data[$key] = $value->select();
		}
		return $data;
	}
	public function relation_insert($entiy) {
		$relation = $this->getRelation($entiy);
	}
	public function relation_update($entiy) {
		$relation = $this->getRelation($entiy);
	}
	public function relation_delete($entiy) {
		# code...
	}
	protected function fillEntiy($entiy, $data) {
		foreach ($data as $key => $value) {
			$entiy->$key = $value;
		}
		return $entiy;
	}
}