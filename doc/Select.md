# 查询

接下来的查询的实体类实例都在Phero/test/PheroTest/DatabaseTest/Unit文件夹中

## 普通查询

### sql语句查询

```php
$data=Db::exec("insert into Mother(name) values (:name);",["name"=>"exec_text"]);
var_dump(Db::error());

$data2=Db::queryResultArray("select * from Mother where id=:id",["id"=>1]);
var_dump(Db::error());

$mother=new Mother(["id"=>11,"name"=>"test".rand(1,100)]);
Db::update($mother);

$mother=new Mother(["id"=>11]);
Db::delete($mother);
```

db的方法表

### 查询全部数据
```php

```
