# 依赖注入的配置项目

- config 配置文件位置或者数组注入
```php
DI::inj(DI::config,dirname(__FILE__)."/PheroTest/DatabaseTest/config.php");
```
- dbhelp 数据库操作类注入 实现IDbHelp接口
```
DI::inj(DI::dbhelp,new SwooleMysqlDbHelp());
```
- pdo_instance pdo实体或者是实体集合
- pdo_hit 主从数据库从库命中规律 实现IDbSlaveHit接口
