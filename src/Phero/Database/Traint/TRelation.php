<?php
namespace Phero\Database\Traint;

/**
 * @Author: CYQ19931115
 * @Date:   2017-01-26 11:50:08
 * @Last Modified by:   CYQ19931115
 * @Last Modified time: 2017-01-30 22:51:28
 */

use Phero\Database\Enum\OrderType;
use Phero\Database\Enum\RelType;
use Phero\Map\NodeReflectionClass;
use Phero\Map\Note\Entiy;
use Phero\Map\Note\Foreign;
use Phero\Map\Note\Relation;

/**
 * summary
 */
trait TRelation {

	/**
	 * 获取外键列表
	 * @param  [type] $properties [description]
	 * @return [type]             [返回 关联key=> 本身的key]
	 */
	private function getForeignKey($properties) {
		$foreign = [];
		foreach ($properties as $key => $value) {
			$foreign_resolve = $value->resolve(new Foreign());
			if (!$foreign_resolve) {continue;}
			$foreign[$foreign_resolve->rel] = $value->getName();
		}
		return $foreign;
	}

	/**
	 * 解析关系 获取关系数据  关联数据有些问题
	 * @param  [type] $entiy [description]
	 * @return [type]        [description]
	 */
	private function getRelation($entiy) {
		$nodeReflectionClass = new NodeReflectionClass($entiy);
		$relation = $nodeReflectionClass->getProperties();
		$foreigns = $this->getForeignKey($relation);
		$properties = [];
		foreach ($relation as $key => $value) {
			$resolve = $value->resolve(new Relation());
			if ($resolve) {
				$property_name = $value->getName();
				if ($entiy->$property_name) {
					$properties[$property_name] = $entiy->$property_name;
				} else {
					$properties[$property_name]['relation'] = $resolve;
					$entiy_resolve = $value->resolve(new Entiy());
					$properties[$property_name]['entiy'] = $entiy_resolve;
					if (array_key_exists($property_name, $foreigns)) {
						$properties[$property_name]['foreign'] = $foreigns[$property_name];
					}
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
			if (!is_object($value)) {
				$relation_node = $value['relation'];
				$entiy_node = $value['entiy'];
				$foreign_key = $value['foreign'];
				if (!isset($foreign_key)) {
					continue;
				}
				$entiy = $this->buildEntiyByNode($entiy, $value);
				if (empty($entiy)) {
					continue;
				}
				if ($relation_node->type == Relation::OO) {
					$data[$key] = $entiy->find();
				} else {
					$data[$key] = $entiy->select();
				}
			} else {
				$data[$key] = $value->select();
			}
		}
		return $data;
	}
	/**
	 * 关联插入  是否需要进行事务处理取决于是否有Transaction这个note
	 * @param  [type] $entiy [description]
	 * @return [type]        [description]
	 */
	public function relation_insert($entiy) {
		$relation = $this->getRelation($entiy);
		$effect = false;
		foreach ($relation as $key => $value) {
			if (!is_object($value)) {
				$relation_node = $value['relation'];
				$entiy_node = $value['entiy'];
				$foreign_key = $value['foreign'];
				if (!isset($foreign_key)) {
					continue;
				}
				$entiy = $this->buildEntiyByNode($entiy, $value);
				if (empty($entiy)) {
					continue;
				}
				$effect = $entiy->insert();
			} else {
				$effect = $value->insert();
			}
		}
		return $effect > 0 ? true : false;
	}
	public function relation_update($entiy) {
		$relation = $this->getRelation($entiy);
	}
	public function relation_delete($entiy) {
		$relation = $this->getRelation($entiy);
	}
	/**
	 * 通过数据填充成对象
	 * @param  [type] $entiy [description]
	 * @param  [type] $data  [description]
	 * @return [type]        [description]
	 */
	protected function fillEntiy($entiy, $data) {
		$classname = get_class($entiy);
		$entiy = new $classname();
		foreach ($data as $key => $value) {
			$entiy->$key = $value;
		}
		return $entiy;
	}

	/**
	 * 通过注解来构建实体
	 * @param  [type] $data [
	 *                      select:关联key对应的值
	 *                      insert:包含数据的entiy
	 *                      update:更新的数据
	 *                      delete:和select相同
	 *               ]
	 * @param  [type] $relation_node [Relation这个注解]
	 * @param  [type] $entiy_node    [Entiy这个注解]
	 * @param  [type] $type                     [构建什么类型的entiy]
	 * @return [type]                           [description]
	 */
	private function buildEntiyByNode($data, $nodes, $type = RelType::select) {
		$relation_node = $nodes['relation'];
		$entiy_node = $nodes['entiy'];
		$rel = $nodes['foreign'];
		$entiyClass = $relation_node->class;
		$relation_key = $relation_node->key;
		$relation_type = $relation_node->type;
		if (!isset($entiyClass) && !isset($relation_key) && !isset($relation_type)) {
			return null;
		}
		switch ($type) {
		case RelType::select:
			{
				if (isset($entiy_node->field)) {
					if (is_string($entiy_node->field)) {
						$entiy_node->field = [$entiy_node->field];
					}
					$entiy = new $entiyClass($entiy_node->field);
				} else {
					$entiy = new $entiyClass();
				}

				//设置查询方式
				$entiy->whereEq($relation_key, $data->$rel);

				//设置排序
				if ($entiy_node) {
					$orderKey = $entiy_node->key;
					$orderType = $entiy_node->type;
					if ($orderKey && $orderType) {
						$orderType = $orderType == 'asc' ? OrderType::asc : OrderType::desc;
						$entiy->order($orderKey, $orderType);
					}
				}
			}
			break;
		case RelType::insert:{
				$entiy = $data->$rel;
				$entiy->$relation_key = $data->$relation;
			}
			break;
		case RelType::update:
			{
				$entiy = $data->$rel;
				$entiy->whereEq($relation_key, $entiy->$relation_key);
			}
			break;
		case RelType::delete:
			{
				$entiy = new $entiyClass();
				//设置查询方式
				$entiy->whereEq($relation_key, $entiy->$relation_key);
			}
			break;
		default:
			# code...
			break;
		}

		return $entiy;
	}
}