## 介绍

>phero是一个数据库查询的orm类库，注解形式的model以及方便快速的数据库操作方法

<h1>这是一个兴趣使然的ORM（手动斜眼笑）</h1>

支持下列特性:

- swoole task数据库连接池
- 数据库链接断线自动重连
- 注解形式的Unit
- 读写分离配置
- 流畅的orm,支持复杂sql
- 注解形式的模型关联
- 命令行模型生成
- 查询即时缓存（redis，mamcache）

> 文档位于doc
> 可以查看test中单元测试的例子

## 安装
 
- composer 
```
composer require lerko/p-hero
```

- git clone
```
git clone https://github.com/chenyingqiao/Phero.git;
```

## 第一个ORM

### 创建一个Unit

```php
<?php 
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

### 简单的查询

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
