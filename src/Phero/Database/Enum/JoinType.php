<?php
namespace Phero\Database\Enum;

/**
 *
 */
class JoinType {
	CONST inner_join = "inner join"; //内连接
	CONST left_join = "left join"; //左连接
	CONST rigth_join = "right join"; //右链接
	CONST rigth_outer_join = "right outer join"; //右外链接
	CONST left_outer_join = "left outer join"; //左链接
	CONST cross_join = "cross join"; //笛卡尔积
}