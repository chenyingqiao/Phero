## 介绍

>phero是一个数据库查询的orm类库，注解形式的model以及方便快速的数据库操作方法

<h1>这是一个兴趣使然的ORM（手动斜眼笑）</h1>

支持下列特性:

- [swoole task数据库连接池](https://github.com/chenyingqiao/Phero/blob/master/doc/4%E8%BF%9E%E6%8E%A5%E6%B1%A0.md)
- 数据库链接断线自动重连
- [注解形式的Unit](https://github.com/chenyingqiao/Phero/blob/master/doc/3%E5%AE%9E%E4%BD%93.md)
- [读写分离配置](https://github.com/chenyingqiao/Phero/blob/master/doc/2%E9%85%8D%E7%BD%AE.md)
- [流畅的orm,支持复杂sql](https://github.com/chenyingqiao/Phero/blob/master/doc/5CURD.md)
- 注解形式的模型关联
- [命令行模型生成](https://github.com/chenyingqiao/Phero/blob/master/doc/3%E5%AE%9E%E4%BD%93.md)
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

## 进行一些基础的查询

```php
$parent=new Parent("name");# 你也可以这样获取Unit的实例  $parent=Parent::Inc();
$parent->whereEq("id",2)->select(Cache::time(10));//Cache::time(10)表示缓存10秒

select `mother`.`name` from `Mother` as `mother`;
```

```php
$Parents=new Parents();
$Marry=new Marry();
//#是会自动替换成使用本实体为子查询的父实体
//或者使用Marry::FF("{字段名称}");也可以生成对应Unit的字段名String
$Marry->whereEq("pid","#.`id`");
$Parents->whereEq("id",1)
    ->whereOrExists($Marry)->select();
```

关键字                         | 对应sql符号
------------------------------|------------
where{and/or}Eq               | =
where{and/or}Neq              | \<\> (!=)
where{and/or}In               | in
where{and/or}Not_in           | not in
where{and/or}Between          | between
where{and/or}Like             | like
where{and/or}Not_like         | not like
where{and/or}Lt               | <
where{and/or}Lr               | <=
where{and/or}Gt               | >
where{and/or}Ge               | >=
where{and/or}Regexp           | regexp
where{and/or}Isnotnull        | is not null
where{and/or}Isnull           | is null
where{and/or}Exists           | exists
where{and/or}Not_exists       | not exists
where{Lt/Lr/Gt/Ge}{and/or}All | all
where{Lt/Lr/Gt/Ge}{and/or}Any | any

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