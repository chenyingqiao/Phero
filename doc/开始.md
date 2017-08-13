# Phero快速入门

## 介绍

>phero是一个数据库查询的orm类库，注解形式的model以及方便快速的数据库操作方法

<h1>简单 性能</h1>

支持下列特性:

- 注解形式的Unit
- 读写分离配置
- 流畅的orm
- 注解形式的模型关联
- 命令行模型生成
- 查询即时缓存（redis，mamcache，filesystem等）
- 嵌套事务
- swoole task线程池(阻塞以及非阻塞)

## 首先建立配置文件

### 配置文件内容

```php
use Symfony\Component\Cache\Simple\RedisCache;
return [
    "database" => [
            "dsn" => "mysql:dbname=phero;host=127.0.0.1",#pdo链接字符串
            "user" => "{your username}",
            "password" => "{your password}",
    ]
];
```

## 设置配置文件的位置

```php
#配置文件可以放到任意php可读的位置
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