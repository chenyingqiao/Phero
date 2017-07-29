# 查询

接下来的查询的实体类实例都在Phero/test/PheroTest/DatabaseTest/Unit文件夹中

## 普通查询

### sql语句查询以及Db工具类

```php
$data=Db::exec("insert into Mother(name) values (:name);",["name"=>"exec_text"]);
var_dump(Db::error());

$data2=Db::queryResultArray("select * from Mother where id=:id",["id"=>1]);
var_dump(Db::error());

$mother=new Mother(["id"=>11,"name"=>"test".rand(1,100)]);
Db::update($mother);//自动识别id=11的数据进行更新(只有主键会被自动识别@Primary注解)

$mother=new Mother(["id"=>11]);
Db::delete($mother);//自动识别id=11的数据进行删除(只有主键会被自动识别@Primary注解)
echo Db::getSql();//获取运行的sql

$mother=new Mother();
$data_select=Db::select($mother);
```


### 查询全部数据（Obj）

```php
$data=Mother::Inc(["name"])->select();
```

```sql
select `mother`.`name` from `Mother` as `mother`;
```

### 条件查询

- 比较符号查询(Lt Lr Gt Ge Eq Neq 用法相同 )

```php
Mother::Inc()->whereLt("id",4)->select();
```

```sql
SELECT
    `mother`.`id`, `mother`.`name`
FROM
    `Mother` AS `mother`
WHERE
    `mother`.`id` < 4;
```

- 范围查询
```
Mother::Inc()->whereBetween("id",[1,10])->select();
```
```sql
SELECT
    `mother`.`id`, `mother`.`name`
FROM
    `Mother` AS `mother`
WHERE
    `mother`.`id` BETWEEN 1 AND 10;
```

- in查询
```php
# 第二个参数也可传入对象 但是对象查询出来的列必须为一列
Mother::Inc()->whereIn("id",[1,10])->select();
```
```sql
SELECT
    `mother`.`id`, `mother`.`name`
FROM
    `Mother` AS `mother`
WHERE
    `mother`.`id` IN (1 , 10);
```

- like查询(not like同理)
```php
Mother::Inc()->whereLike("name","test%")->select();
```

```sql
SELECT
    `mother`.`id`, `mother`.`name`
FROM
    `Mother` AS `mother`
WHERE
    `mother`.`name` LIKE 'test%';
```

- is null

```php
$Parents->whereEq("id",1)
			->whereOrIsnull("id")->select();
```

```sql
SELECT
    `parent`.`id`, `parent`.`name`
FROM
    `Parent` AS `parent`
WHERE
    `parent`.`id` = 1
        OR `parent`.`id` IS NULL;
```

- exists查询

```php
$Parents=new Parents();
$Marry=new Marry();
//#是会自动替换成使用本实体为子查询的父实体
//或者使用Marry::FF("{字段名称}");也可以生成对应Unit的字段名String
$Marry->whereEq("pid","#.`id`");
$Parents->whereEq("id",1)
	->whereOrExists($Marry)
```

```sql
SELECT
    `parent`.`id`, `parent`.`name`
FROM
    `Parent` AS `parent`
WHERE
    `parent`.`id` = 1
        OR EXISTS( SELECT
            `Marry`.`id`, `Marry`.`pid`, `Marry`.`mid`
        FROM
            `Marry`
        WHERE
            `Marry`.`pid` = `parent`.`id`);
```

- all 和any

```php
$Parents=new Parents();
$Marry=new Marry();
$Marry->whereEq("pid","#.`id`");//#是会自动替换成使用本实体为子查询的父实体
$Parents->whereEq("id",1)
	->whereOrLtAll("id",$Marry)->select();
```

```sql
SELECT
    `parent`.`id`, `parent`.`name`
FROM
    `Parent` AS `parent`
WHERE
    `parent`.`id` = 1
        OR `parent`.`id` < ALL (SELECT
            `Marry`.`id`, `Marry`.`pid`, `Marry`.`mid`
        FROM
            `Marry`
        WHERE
            `Marry`.`pid` = `parent`.`id`);
```

> where支持多种查询条件，只要在后面添加相应的关键字就行  and 和or这个条件连接词可以加中间

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


### 条件查询(未包装的where)

```php
$result=$Parents
	->where(["id",10,Where::eq_],null,1,"Fun(?)")
	->where(["name","%test%",Where::like,WhereCon::and_],null,2,"Fun2(?)")
    ->select();
```

```sql
SELECT
    `parent`.`id`, `parent`.`name`
FROM
    `Parent` AS `parent`
WHERE
    (FUN(`parent`.`id`) = 10
        AND FUN2(`parent`.`name`) LIKE '%test%');
```

### where分组

```php
Mother::Inc()->Set(function(){
	$this->whereEq("id",1)->whereOrLike("name","sss_");
	return $this;
},WhereCon::or_)->Set(function(){
	$this->whereEq("id",2)->whereOrLike("name","ddd_");
	return $this;
})->select()
```

```sql
SELECT
    `mother`.`id`, `mother`.`name`
FROM
    `Mother` AS `mother`
WHERE
    (`mother`.`id` = 1
        OR `mother`.`name` LIKE 'sss_')
    OR (`mother`.`id` = 2
        OR `mother`.`name` LIKE 'ddd_');
```

### 列选取以及聚合查询

- 聚合函数



- 带函数的field

```php
$id=Mother::FF("id");
$marry->field("ThisIsMyFuckingFun($id)","fuckFun")->field("Fun2($id)","fcun2")->fetchSql($sql);
```

```sql
SELECT
    THISISMYFUCKINGFUN(`mother`.`id`) AS fuckFun,
    FUN2(`mother`.`id`) AS fcun2,
    `Marry`.`id`,
    `Marry`.`pid`,
    `Marry`.`mid`
FROM
    `Marry`;
```

### 排序

### 分组以及分组having

###

### join查询

```php
$Parents=new Parents();
$Marry=new Marry();
$Mother=new Mother();
$Marry->join($Parents,"$.`pid`=#.`id`");//$ 代表主动join的实体  # 代表被join的实体
$Marry->join($Mother,"$.`mid`=#.`id`")->select();
```

```sql
SELECT
    `Marry`.`id`,
    `Marry`.`pid`,
    `Marry`.`mid`,
    `parent`.`id`,
    `parent`.`name`,
    `mother`.`id`,
    `mother`.`name`
FROM
    `Marry`
        INNER JOIN
    `Parent` AS `parent` ON `Marry`.`pid` = `parent`.`id`
        INNER JOIN
    `Mother` AS `mother` ON `Marry`.`mid` = `mother`.`id`;
```

### 分组查询
