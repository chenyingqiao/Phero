# Phero快速入门

## 介绍

>phero是一个数据库查询的orm类库，注解形式的model以及方便快速的数据库操作方法

支持下列特性:

- 注解形式的Unit
- 读写分离配置
- 流畅的orm
- 注解形式的模型关联
- 命令行模型生成
- 查询即时缓存（redis，mamcache，filesystem等）
- 嵌套事务

## 首先建立配置文件

### 配置文件内容

```php
use Symfony\Component\Cache\Simple\RedisCache;
return [
    "database" => [
        # master为主从中的主  slave为从  目前只支持一主多从
        "master" => [
            "dsn" => "mysql:dbname=phero;host=127.0.0.1",#pdo链接字符串
            "user" => "{your username}",
            "password" => "{your password}",
        ],
        // "slave" => [
        //     [
        //         "dsn" => "mysql:dbname=kn_erp_db;host=172.17.0.3",
        //         "user" => "admin",
        //         "password" => "password",
        //     ],
        //     [
        //         "dsn" => "mysql:dbname=kn_erp_db;host=172.17.0.4",
        //         "user" => "admin",
        //         "password" => "password",
        //     ],
        // ],
    ],
    #缓存使用的是Symfony的缓存组件 这里可以对缓存组件进行创建 具体看Symfony文档
    "cache"=>RedisCache::createConnection('redis://127.0.0.1'),
    #是否开启debug{开启之后注解缓存将会每次都刷新}
    "debug"=>true
];
```

## 设置配置文件的位置

```php
DI::inj("config",dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");
```

## 执行第一条sql

```php
use Phero\Database\Db;
Db::queryResultArray("select * from phero.Mother");//直接返回所有的数据
Db::query("select * from phero.Mother");//返回一个生成器（yield） 直接遍历循环取出数据，对内存占用小
Db::exec("insert into phero.Mother (`name`) values (`test`)");//执行sql操作语句
```

## 第一个ORM

### 创建一个Unit

```php
<?php 
namespace PheroTest\DatabaseTest\Unit;
use PheroTest\DatabaseTest\Traits\Truncate;
use Phero\Database\DbUnit;

/**
 * #这里有其他的东西并不会干扰注解的工作
 * @Author: lerko
 * @Date:   2017-05-31 11:54:57
 * @Last Modified by:   lerko
 * @Last Modified time: 2017-06-02 10:10:52
 * @Table[name=Parent,alias=parent]  
 * # name表示真正的表名称，如果没有配置就是类名为表明
 * # alias为别名
 */
class Parents extends DbUnit
{
    use Truncate;
    /**
     * @Field[type=int] # 只有标示@Field的属性才会被作为查询列
     * @Primary #标示为主键
     * @var [type]
     */
    public $id;
    /**
     * @Field
     * @var [type]
     */
    public $name;
}
```

## 对应的表结构为

```
+-------+-------------+------+-----+---------+----------------+
| Field | Type        | Null | Key | Default | Extra          |
+-------+-------------+------+-----+---------+----------------+
| id    | int(11)     | NO   | PRI | NULL    | auto_increment |
| name  | varchar(45) | YES  |     | NULL    |                |
+-------+-------------+------+-----+---------+----------------+
```

### 查询

```php
$parent=new Parent("name");# 你也可以这样获取Unit的实例  $parent=Parent::Inc();
$parent->whereEq("id",2)->select(Cache::time(10));//Cache::time(10)表示缓存10秒
```

### 删除

```php
$parent=new Parent();
$parent->whereEq("id",2)->delete();
```

### 更新

```php
$parent=new Parent([
    "name"=>"this is change!"
]);
$parent->whereEq("id",2)->update();
```

### 插入

```php
#id是自增主键没有进行赋值
$parent=new Parent([
    "name"=>"插入"
]);
#不用构造函数赋值你也可以这样直接赋值
$parent->name="这个是直接赋值";
$parent->insert();
```