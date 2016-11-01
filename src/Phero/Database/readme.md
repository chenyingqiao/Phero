# 说明


##数据库工具类
```php
<?php
namespace SimplePhp\Database;
/**
 * DbTable:映射的表
* @map[DbTable=user]
*/
class DbUnit
{
    /**
     * @map[DbKey=uid,DbType=int|class|string(length)|bool...,Dbrelation=1_m|1_1]
     * @var integer
     */
    public $id=0;
}
```

>通过数据单元 然后解析注解  之后通过注解形成的映射信息来进行查询