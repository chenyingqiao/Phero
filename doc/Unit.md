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
