<?php
/**
 * Created by PhpStorm.
 * User: chen
 * Date: 17-1-31
 * Time: 下午11:10
 */

namespace Phero\Map\Note;

/**
 * 标示这个实体类是在那个机器组上面
 * 一种是垂直分表(多表链接)
 * 一种是水平分表(hash数据 雪花主键 )
 *
 * 计算机group1,,group2
 */
class Service
{
    /**
     * 配置文件中数据库服务器的对应名称
     * @var
     */
    public $name;
}