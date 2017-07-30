# 实体类


## 实体类的实例

```php
<?php
namespace PheroTest\DatabaseTest\Unit;

use PheroTest\DatabaseTest\Traits\Truncate;
use Phero\Database\DbUnit;
/**
 * @Table[name=Mother,alias=mother]
 */
class Mother extends DbUnit
{
	use Truncate;
	/**
	 * @Primary
	 * @Foreign[rel=info]
	 * @Field[name=id,alias=mother_id,type=int]
	 * @var [type]
	 */
	public $id;
	/**
	 * @Field
	 * @var [type]
	 */
	public $name;

	/**
	 * @Relation[type=oo,class=PheroTest\DatabaseTest\Unit\MotherInfo,key=mid]
	 * @var [type]
	 */
	public $info;
}
```

> 我们只要继承DbUnit类就行
>
> 并且 `use Truncate` 这个trait

## 几个注解

- @Table
    - name：表的真实名称
    - alias：表的别名 会出现在sql中的 as
- @Field
    - name:字段的真实名称
    - alias：字段的别名
    - type：字段的类型 {目前只有string和int的区别}
- @Primary 表示这个是表的主键
- @Foreign
    - rel：表示关联的是本表的那个关联字段
- @Relation
    - type：{oo：一对一  om：一对多}
    - class：关联的Unit的类名
    - key：关联的Unit的具体的字段
- @Entity
    - field: 需要查询出来包含的类
    - sort:排序
    - key：排序的键值

## 从数据库生成unit

我们只需要运行一个php文件就可以生成Unit文件到指定目录
cd到本包的根目录
运行 `php UnitBuilder`

就可以自动生成Unit到自定目录

```shell
lerko@lerko-PC:/var/www/html/Phero$ php UnitBuilder
-----------------------------------------------------------------------------------------
请输入生成文件的位置： /home/lerko/Desktop/Test/
输入统一的命名空间： Test

输入数据库名称： phero
输入数据库地址：(默认 localhost)
输入数据库用户名：(默认 root)
输入数据库密码：(默认为空) lerko
是否只生成某些表？(默认为全部，表名逗号隔开)
================================================================================> 100%
```


## Unit的实例化

- 方式1

```php
$mother=new Mother();
```

- 方式2

```php
$mother=Mother::Inc();
Mother::lastInc()==$mother;//lastInc会等于最近的那个Inc
```
