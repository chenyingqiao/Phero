<?php
/**
 * @Author: lerko
 * @Date:   2017-03-13 13:36:29
 * @Last Modified by:   ‘chenyingqiao’
 * @Last Modified time: 2017-07-30 16:20:23
 */

namespace Phero\Database\Traits;

use Phero\Database\Enum\OrderType;
use Phero\Database\Enum\RelType;
use Phero\Map\NodeReflectionClass;
use Phero\Map\Note\Entity;
use Phero\Map\Note\Foreign;
use Phero\Map\Note\Relation;
use Phero\Map\Note\RelationEnable;

/**
 * summary
 */
trait TRelation {

    /**
     * 是否开启了Relation 自动关联查询
     * @return bool 返回结果
     */
    private function getRelationIsEnable($Entiy){
    	if(is_array($Entiy)&&isset($Entiy[0]))$Entiy=$Entiy[0];
    	if(empty($Entiy)){
    		return false;
    	}
        $enable=(new NodeReflectionClass($Entiy))->resolve(new RelationEnable());
        if(empty($enable)){
            return false;
        }else{
            return true;
        }
    }

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
			if(is_array($foreign_resolve->rel)){
				foreach ($foreign_resolve->rel as $foreign_key => $foreign_value) {
					$foreign[$foreign_value] = $value->getName();
				}
			}else{
				$foreign[$foreign_resolve->rel] = $value->getName();
			}
		}
		return $foreign;
	}

	/**
	 * 解析关系 获取关系数据
	 * @param  [type] $entiy [description]
	 * @return [type]        [description]
	 */
	private function getRelation($entiy,$relType=RelType::select) {
		$nodeReflectionClass = new NodeReflectionClass($entiy);
		$relation = $nodeReflectionClass->getProperties();
		//取得关联的全部外键
		$foreigns = $this->getForeignKey($relation);
		$properties = [];
		foreach ($relation as $key => $value) {
			$resolve = $value->resolve(new Relation());
			if ($resolve) {
				$property_name = $value->getName();
				if ($relType!=RelType::select&&isset($entiy->$property_name)&&is_object($entiy->$property_name)) {
				    //关联插入需要对关联的数据自动赋值
				    $result_entiy=$entiy->$property_name;
				    if(empty($result_entiy)){
				    	continue;
				    }
                    $relation_key=$resolve->key;
                    if(empty($foreigns[$property_name])){
                        throw new \Exception("foreign key nonentity");
                    }
                    $parent_entiy_relation_key=$foreigns[$property_name];
                    $result_entiy->$relation_key=$entiy->$parent_entiy_relation_key;
                    //关联插入需要对关联的数据自动赋值
					$properties[$property_name] = $result_entiy;
					// $properties[$property_name]['foreign_rel'] = $property_name;
				}else {
					$properties[$property_name]['relation'] = $resolve;
					$entiy_resolve = $value->resolve(new Entity());
					$properties[$property_name]['entiy'] = $entiy_resolve;
					//判断这个外键是否有和关联查询的字段对应
					if (array_key_exists($property_name, $foreigns)) {
						$properties[$property_name]['foreign'] = $foreigns[$property_name];
						$properties[$property_name]['foreign_rel'] = $property_name;
					}
				}
			}
		}
		return $properties;
	}

	/**
	 * 级联查询
	 * @param  [type] $entiy [这个是数据实体类(father) 需要包含对应数据]
	 * @return [type]        [description]
	 */
	public function relation_select(&$result,&$entiy) {
		$relation = $this->getRelation($entiy);
		$data=[];
		foreach ($relation as $key => $value) {
			if (!is_object($value)) {
				$relation_node = $value['relation'];
				$entiy_node = $value['entiy'];
				if (!isset($value['foreign'])) {
					continue;
				}
				$foreign_key = $value['foreign'];
				$foreign_rel=$value['foreign_rel'];
				$entity_childer = $this->buildEntiyByNode($result, $value);
				if (empty($entity_childer)) {
					continue;
				}
				$data = $entity_childer->select();
				$entiy->$foreign_rel=$entity_childer;
				foreach ($result as $key => &$value_result) {
					$data_filter=array_filter($data,function($v)use($relation_node,$value_result,$foreign_key){
						$node_key=$relation_node->key;
						return $v[$node_key]==$value_result[$foreign_key];
					});
					if ($relation_node->type == Relation::OO) {
						$data_filter=array_shift($data_filter);
					}
					$value_result[$foreign_rel]=$data_filter;
				}
			}
		}
	}
	/**
	 * 关联插入  是否需要进行事务处理取决于是否有Transaction这个note
	 * @param  [type] $entiy [description]
	 * @return [type]        [返回的是插入的行数]
	 */
	public function relation_insert(&$entiy) {
		if(is_array($entiy)){
			$entiy=$entiy[0];
		}
		$relation = $this->getRelation($entiy,RelType::insert);
		$effect = true;
		foreach ($relation as $key => $value) {
			if (!is_object($value)) {
				$relation_node = $value['relation'];
				$entiy_node = $value['entiy'];
				if (!isset($value['foreign'])) {
					continue;
				}
				$foreign_key = $value['foreign'];
				$entity_childer = $this->buildEntiyByNode($entiy, $value,RelType::insert);
				//产生的实体类为空或者是对应的数据类是空的就直接进行下一个字段的检查
				if (empty($entity_childer)) {
					continue;
				}
				$effect = $entity_childer->insert();
			} else {
				$effect = $value->insert();
			}
		}
		return $effect;
	}
	/**
	 * 待测试
	 * @param  [type] $entiy [description]
	 * @return [type]        [description]
	 */
	public function relation_update($entiy) {
		$relation = $this->getRelation($entiy,RelType::update);
		$effect = true;
		foreach ($relation as $key => $value) {
			if (!is_object($value)) {
				$relation_node = $value['relation'];
				$entiy_node = $value['entiy'];
				if (!isset($value['foreign'])) {
					continue;
				}
				$foreign_key = $value['foreign'];
				$entity_childer = $this->buildEntiyByNode($entiy, $value,RelType::update);
				//产生的实体类为空或者是对应的数据类是空的就直接进行下一个字段的检查
				if (empty($entity_childer)) {
					continue;
				}
				$effect = $entity_childer->update();
			} else {
				$effect = $value->update();
			}
		}
		return $effect;
	}
	/**
	 * [relation_delete description]
	 * @param  [type] $entiy [description]
	 * @return [type]        [description]
	 */
	public function relation_delete($entiy) {
		$relation = $this->getRelation($entiy,RelType::delete);
		$effect = true;
		foreach ($relation as $key => $value) {
			if (!is_object($value)) {
				$relation_node = $value['relation'];
				$entiy_node = $value['entiy'];
				if (!isset($value['foreign'])) {
					continue;
				}
				$foreign_key = $value['foreign'];
				$entiy_delete = $this->buildEntiyByNode($entiy, $value,RelType::delete);
				//产生的实体类为空或者是对应的数据类是空的就直接进行下一个字段的检查
				if (empty($entiy_delete)) {
					continue;
				}
				$effect = $entiy_delete->delete();
			} else {
				$effect = $value->delete();
			}
		}
		return $effect;
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
		if (is_object($data)) {
			$entiy = $data;
		} else {
			foreach ($data as $key => $value) {
				$entiy->$key = $value;
			}
		}
		return $entiy;
	}

	/**
	 * 通过注解来构建实体用来进行更新插入以及删除
	 * @param  [type] $data [
	 *                      select:主表查询的数据
	 *                      insert:包含数据的entiy
	 *                      update:更新的数据
	 *                      delete:和select相同
	 *               ]
	 * @param  [type] $nodes    [Entiy这个注解]
	 * @param  [type] $type                     [构建什么类型的entiy]
	 * @return [type]                           [description]
	 */
	private function buildEntiyByNode(&$data, $nodes, $type = RelType::select) {
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
				$entiy->whereIn($relation_key, array_column($data, $rel));
				//设置排序
				if ($entiy_node) {
					$orderKey = $entiy_node->key;
					$orderType = $entiy_node->sort;
					if ($orderKey && $orderType) {
						$orderType = $orderType == 'asc' ? OrderType::asc : OrderType::desc;
						$entiy->order($orderKey, $orderType);
					}
				}
			}
			break;
		case RelType::insert:{
				$entiy = $data->$rel;
				if(empty($entiy)){
					return null;
				}
				//自动赋值关联字段到关联表中---如果
				if (isset($entiy->$relation_key)) {
					$entiy->$relation_key = $data->$relation;
				}
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
				$entiy->whereEq($relation_key, $data->$rel);
			}
			break;
		default:
			# code...
			break;
		}
		return $entiy;
	}
}
