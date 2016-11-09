#Phero数据库查询

##开始
>现在我们有几张表（都要引入DbUnit）

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
   1. @Table    表别名
   2. @Field    列的类型,列的别名

## 查询

### 查询所有列video_cat表中的列

```php
$video_cat=new video_cat();
$value=$video_user->select();//value就是video_cat查询出来的结果

相当于:
> select  cd.uid,cd.username,cd.password from video_user
```

### 查询表中的部分列

```php
$video_cat=new video_cat(['id','name']);  //===>可以输入要的列
$value=$video_user->select();

相当于:
> select id,name from video_user;
```


### 条件查询(where)

* 简单的where
```php
    $video_user=new test\video_user("*");
    $video_user->order('uid',database\Enum\OrderType::desc);
    $video_user->whereOrEqGroupStart("uid", 4)->whereInGroupEnd("uid", [2, 3, 1]);
    $video_user->group("password");
    $video_user->having(["password","many_test"]);
    $video_user->select();

相当于:
    > select  cd.uid,cd.username from video_user as cd where  (cd.uid = 4 or  cd.uid in (2, 3, 1)) having  cd.password = 'many_test'  order by cd.uid desc;
```

* 条件查询的组合
    1.  where\[Eq,Neq,In,Not_in,Between,Like,Lt,Lr,Gt,Ge\]([列],[值])
    2. whereAnd\[Eq,Neq,In,Not_in,Between,Like,Lt,Lr,Gt,Ge\]([列],[值])
    3. whereOr\[Eq,Neq,In,Not_in,Between,Like,Lt,Lr,Gt,Ge\]([列],[值])
    
    
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
$video_cat->join(new video_course(['course_id','name','video_path']),"$.id=#.cat_id");
$value=$video_cat->select();
```


## 插入
。。。。。
## 更新
。。。。。
## 删除
。。。。。