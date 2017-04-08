<?php
namespace Phero\Database\Realize\ConsTrait;

use Phero\Database\Interfaces as interfaces;
use Phero\Database\Realize as realize;
use Phero\Database\Traits as Traits;

class HavingConsTrait extends realize\ConsTraits\WhereConsTrait{
    /**
     * 返回语句约束的类型
     * @Overried
     * @return [type] [description]
     */
    public function getType() {
        return realize\MysqlConsTraitBuild::Having;
    }
    /**
     * 获取这个约束组装完成的sql语句片段
     * @return [type] [description]
     */
    public function getsqlfragment(){
        if (!empty($this->where)) {
            $this->where = " having " . $this->where;
        }
        return $this->where;
    }

    protected function getBuildData($Entiy){
        return $Entiy->getHaving();
    }
}