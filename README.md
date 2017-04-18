#Phero数据库查询

数据类型支持  DataTime
table 名称直接实例化支持
直接补货post以及get的变量

## 特点

1. 分布式数据库关联查询（表在不同的数据库机器上）
2. 分布式分割数据库查询 (水平分割和垂直分割)
3. 自动关联查询
4. 存储过程的支持
5. 快速的数据库调试
6. 分布式存储过程支持
7. 乐观锁支持
8. 数据库查询钩子 事件触发


$entity->select();//直接赋值到entity里面
update 表达式类    定义列和update表达式

> update where function
```php
Set(function($this){
    
})
```

## 开发笔记

需要增加注解去标示这个entiy是不是垂直分割的还是关联查询的
@


## 安装

> git安装 `git clone https://github.com/chenyingqiao/Phero.git`

> composer 安装 `composer require lerko/p-hero`



## 开始

### 在入口脚本注入PDO

> 方法1 

```php
use Phero\System\DI;
use Phero\Database as database;

$config[]="mysql:host=localhost;dbname=video;charset=utf8";//链接字符串
$config[]="root";//用户名
$config[]="Cyq19931115";//密码
DI::inj(database\Enum\DatabaseConfig::DatabaseConnect,$config);//注入
```

> 方法2

```php
use Phero\System\DI;
use Phero\Database as database;

$dns = "mysql:host=localhost;dbname=video;charset=utf8";
DI::inj(database\Enum\DatabaseConfig::pdo_instance, new Phero\Database\PDO($dns, 'root', 'Cyq19931115'));
```

> 接下来就可以建立实体类 然后愉快的使用了

### 直接执行sql语句

*	exec(sql,bindData) 插入数据
*	query(sql,bindData) 读取数据

```php
$help=new database\Realize\MysqlDbHelp();
$data=$help->query("select * from video_cat where id=:id ",["id",1]);
$effect=$help->exec("update video_cat set id=:id where name=:name",[
    ['id',1],
    ['name','视频']
]);
```


### 新建实体类
> 现在我们有几张表（都要`use DbUnit`）

`video_cat`表  `(视频种类表)`


| Field  | Type              | Null   | Key   | Default  | Extra             |
|------- |:--------------:|:------:|:-----:|:---------:|----------------:|
| id       | int(11)            | NO    | PRI  | NULL     | auto_increment|
| name   | varchar(255) | YES   |         | NULL     |                       |


> 实体

```php
namespace PheroTest\DatabaseTest\Unit;
use Phero\Database\DbUnit;

/**
 * @Table[alias=cat]
 */
class video_cat {
	use DbUnit;
	/**
	 * [$id description]
	 * @var [type]
	 * @Field[alias=cat_id]
	 */
	public $id;
	/**
	 * [$name description]
	 * @var [type]
	 * @Field[alias=cat_name]
	 */
	public $name;
}
```


`video_user`表   `(用户表)`

| Field       | Type             | Null   |   Key | Default | Extra          |
|---------- |:--------------:|:------:|:-----: |:---------:|----------------:|
| uid          | int(11)            | NO   | PRI   | NULL      | auto_increment|
| username | varchar(255) | YES  |          | NULL      |                        |
| password | varchar(255) | YES  |          | NULL      |                        |

> 实体

```php
namespace PheroTest\DatabaseTest\Unit;

use Phero\Database\DbUnit;

/**
 * @Table[alias=cd]
 */
class video_user {
	use DbUnit;
	/**
	 * @Primary
	 * @DbType[type=int]
	 * @var [type]
	 */
	public $uid;
	/**
	 * @DbType[type=string]
	 * @var [type]
	 */
	public $username;
	/**
	 * @DbType[type=string]
	 * @var [type]
	 */
	public $password;
}
```

`video_course`表  `(视频课程表)`

| Field         | Type         | Null | Key | Default | Extra          |
|---------------|:--------------:|:------:|:-----:|:---------:|----------------:|
| course_id     | int(11)      | NO   | PRI | NULL    | auto_increment |
| name          | varchar(255) | NO   |     | NULL    |                |
| anthor        | varchar(255) | NO   |     | NULL    |                |
| cat_id        | int(11)      | NO   |     | NULL    |                |
| direction_id  | int(11)      | NO   |     | NULL    |                |
| difficulty_id | int(11)      | NO   |     | NULL    |                |
| intreduce     | text         | NO   |     | NULL    |                |
| video_path    | varchar(255) | NO   |     | NULL    |                |
| cover         | varchar(255) | NO   |     | NULL    |                |
| create_time   | int(11)      | YES  |     | NULL    |                |
| update_time   | int(11)      | YES  |     | NULL    |                |

>实体


```php
namespace PheroTest\DatabaseTest\Unit;
use Phero\Database\DbUnit;

/**
 * @Table[alias=course]
 */
class video_course {
	use DbUnit;

	public $course_id;
	public $name;
	public $anthor;
	public $cat_id;
	public $direction_id;
	public $difficulty_id;
	public $intreduce;
	public $video_path;
	public $cover;
	public $create_time;
	public $update_time;
}
```

### 注解
   1. @Table[alias=###]    表别名
   2. @Field[alias=###,type=[string|int]]    列的别名,列的类型

## 查询

### 查询所有列video_cat表中的列

```php
$video_cat=new video_cat();
$video_user->select();//value就是video_cat查询出来的结果
```

```sql
相当于:
  select
  	cat.uid,
  	cat.username,
  	cat.password
  from
  	video_user as cat
```

### 查询取值方式

```php
//select可以使用函数进行数据的遍历
$user_list = $video_user->select(function($item){
        var_dump($item);
});
```

### 查询表中的部分列

```php
$video_cat=new video_cat(['id','name']);  //===>可以输入要的列
$value=$video_user->select();
```

```sql
相当于:
select
	cd.id,
	cd.name
from
	video_user as cd;
```

### 


### 条件查询(where)

> 简单的where(GroupStart GroupEnd标示一个where组)
```php
    $video_user=new test\video_user(["uid","username"]);
    $video_user->order('uid',database\Enum\OrderType::desc);
    $video_user->whereOrEqGroupStart("uid", 4)->whereInGroupEnd("uid", [2, 3, 1]);
    $video_user->group("password");
    $video_user->having(["password","many_test"]);
    $video_user->select();
```

```sql
相当于:
select
	cd.uid,
	cd.username
from
	video_user as cd
where
	(
		cd.uid = 4 or cd.uid in(2,3,1)
	)
having
	cd.password = 'many_test'
order by
	cd.uid desc;
```

### where函数模板

> where 列使用函数模板

```php
$video_user = new unit\video_user();
$user_list = $video_user->whereN("uid", null, "ASCII(?)")->select();
var_dump($user_list);
```

```sql
SELECT
    cd.uid,
    cd.username AS um,
    cd.password AS pwd
FROM
    video_user AS cd
WHERE
    ASCII(cd.uid);
```

> 条件查询的组合{N或者随意字符串标示不使用比较符号:使用函数模板的时候使用}

    *使用函数模板 第二个参数一定要传 *

    1. where[Eq,Neq,In,Not_in,Between,Like,Lt,Lr,Gt,Ge,N或者随意字符串]{GroupStart|GroupEnd}([列],[值],[函数模板])
    2. whereAnd[Eq,Neq,In,Not_in,Between,Like,Lt,Lr,Gt,Ge,N或者随意字符串]{GroupStart|GroupEnd}([列],[值],[函数模板])
    3. whereOr[Eq,Neq,In,Not_in,Between,Like,Lt,Lr,Gt,Ge,N或者随意字符串]{GroupStart|GroupEnd}([列],[值],[函数模板])
    
    
### 关联查询

```php
$video_cat=new video_cat();
/**
 * $.id=#.cat_id
 * 这个表达式标示两个表的联系
 * 其中
 *         $:表示 video_cat表
 *         #:表示 video_path表
 *          join可以嵌套使用
            如 A B C 三表
            A->join(B);
            B->join(C);
            A->select(); //完成3表链接
 * @type {[type]}
 */
//===========3表链接============
$video_cat->join(new video_course(['course_id','name','video_path']),"$.id=#.cat_id");
$value=$video_cat->select();

//===========3表链接============
$video_cat = new unit\video_cat(['id', 'name']); //===>可以输入要的列
$video_course = new unit\video_course(['course_id', 'name', 'video_path', "difficulty_id"]);
$video_cat->join($video_course, "$.id=#.cat_id");
$video_course->join(new unit\video_difficulty(['id', 'name']), "$.difficulty_id=#.id");
$video_cat_join_course = $video_cat->select();
var_dump($video_cat_join_course);
//打印sql
var_dump($video_cat->sql());
//打印错误信息
var_dump($video_cat->getModel()->getError());
```


## 插入

### 普通插入

```php
//这是一种赋值方式
$video_user = new unit\video_user(["username" => "asdfs" . rand(), "password" => "1234" . rand()]);
//或者通过字段直接赋值
$video_user->username="fuck";
$video_user->password="123455";
$insert = $video_user->insert();
```

### 批量插入

```php
$video_user = new unit\video_user(["username" => "asdfs" . rand(), "password" => "1234" . rand()]);
 $video_user2 = new unit\video_user(["username" => "asdfs" . rand(), "password" => "1234" . rand()]);
//批量插入
$entiy = [$video_user, $video_user2];
$model = new Model();
$model->insert($entiy);
```

### 事务插入

```php
//这是一种赋值方式
$video_user = new unit\video_user(["username" => "asdfs" . rand(), "password" => "1234" . rand()]);
//或者通过字段直接赋值
$video_user->username="this is fuck";
$video_user->password="123455";
//true表示开启事务
$insert = $video_user->insert(true);
//提交事务
$video_user->commit();
```

## 更新

### 普通更新

```php
$video_user = new unit\video_user(["username" => "asdfs", "password" => "1234"]);
$video_user->where(['uid', 4]);
$update = $video_user->update();
```

```sql
相当于
update
	video_user as cd
set
	username = asdfs,
	password = 1234
where
	cd.uid = 4;
```


# 删除

### 普通删除
```php
$video_user = new unit\video_user();
$video_user->whereEq("uid",4);
$video_user->delete();
```

```sql
相当于
delete from video_user where uid = 4;
```



# 实例测试文件

```php
<?php
require_once "vendor/autoload.php";

use PheroTest\DatabaseTest\Unit as unit;
use Phero\System\DI;
use Phero\Database as database;

/**
 * 注入pdo实例【没有注入将无法使用】
 * @var string
 */
$dns = "mysql:host=localhost;dbname=video;charset=utf8";
$config[]=$dns;
$config[]="root";
$config[]="Cyq19931115";
DI::inj(database\Enum\DatabaseConfig::DatabaseConnect,$config);

//DI::inj(database\Enum\DatabaseConfig::pdo_instance, new Phero\Database\PDO($dns, 'root', 'Cyq19931115'));


/******************************************
 * 测试普通单个查询
 * @var video_user
 *****************************************/
$video_user = new unit\video_user();
//$video_user->group("uid");
//$video_user->limit(2, 5);
$video_user->order('uid',database\Enum\OrderType::desc);
$video_user->group("password");
$video_user->having(["password","many_test"]);
//$video_user->fieldTemp(['username' => "SUBSTRING(? FROM 2) as subUsername", 'password' => "SUBSTRING(? FROM 2) as subPassword"]);
//// 设置列模板之后原来的列就会消失-----要如下手动添加
//$video_user->field(["username", "password"]);
//$video_user->field("count(case when username='ying' then username end) as 'asdf'");
//$video_user->BIN("password");
$video_user->whereOrEqGroupStart("uid", 4)->whereInGroupEnd("uid", [2, 3, 1]);
$user = $video_user->select();
//$user = $video_user->find();
var_dump($user);
$video_user->dumpSql();

/******************************************
 * 测试内外链接
 * @var video_user
 *****************************************/
$video_cat = new unit\video_cat(['id', 'name']); //===>可以输入要的列
$video_course = new unit\video_course(['course_id', 'name', 'video_path', "difficulty_id"]);
$video_cat->join($video_course, "$.id=#.cat_id");
$video_course->join(new unit\video_difficulty(['id', 'name']), "$.difficulty_id=#.id");
$video_cat_join_course = $video_cat->select();
var_dump($video_cat_join_course);
$video_cat->dumpSql();
var_dump($video_cat->getModel()->getError());

/******************************************
 * 测试删除
 * @var video_user
 *****************************************/
$video_user = new video_user();
$video_user->where(['uid', 8]);
var_dump($video_user->delete());

/******************************************
 * 测试插入
 * @var video_user
 *****************************************/
$video_user = new unit\video_user(["username" => "asdfs" . rand(), "password" => "1234" . rand()]);
$video_user->username="this is fuck";
$video_user->password="123455";
//true表示开启事务
$insert = $video_user->insert();
 //嵌套事务
$insert = $video_user->insert(true);
$video_user->commit();
 //嵌套事务
$video_user->commit();
var_dump($video_user->getError());
$video_user->dumpSql();
var_dump($insert);

/******************************************
 * 测试数据更新
 * @var video_user
 *****************************************/
$video_user = new unit\video_user(["username" => "asdfs" . rand(), "password" => "1234" . rand()]);
$video_user->where(['uid', 4]);
$update = $video_user->update();
var_dump($update);
var_dump($video_user->getModel()->getError());

/******************************************
 *测试数据替换
 * @var video_user
 *****************************************/
 $video_user = new unit\video_user(["username" => "asdfs" . rand(), "password" => "1234" . rand()]);
 $video_user2 = new unit\video_user(["username" => "asdfs" . rand(), "password" => "1234" . rand()]);
//批量插入
 $entiy = [$video_user, $video_user2];
 $model = new Model();
 $model->insert($entiy);
 var_dump($model->getError());
//ORM 插入
 $result = $video_user->replace();
 var_dump($result);

/******************************************
 *依赖注入测试
 * @var video_user
 *****************************************/
 $injectTest = new InjectTest();
 var_dump($injectTest);

/******************************************
 *手动设置数据源测试
 * @var video_user
 *****************************************/
$video_cat = new unit\video_cat(['id', 'name']); //===>可以输入要的列
$video_cat->field("course.id");
$video_cat->datasourse("(select * from video_course)", "course", "#.cat_id=$.id");
$data = $video_cat->select();
var_dump($data);
$video_cat->dumpSql();
echo $video_cat->fetchSql();

```
