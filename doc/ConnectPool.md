# 连接池

## 运行连接池

到包根目录 运行 `php DbPool`
连接池就开始运行了

> 这里要注意的是config文件指定的ip和端口要和client端一样

然后我们在index.php或者是任意一个全局访问的入口加入下面这个代码
```php
DI::inj(DI::dbhelp,new SwooleMysqlDbHelp());
```

现在我们就可以使用数据库连接池了

## 配置文件

```php
"swoole"=>[
    "ip"=>"127.0.0.1",//ip
    "port"=>54288,//端口
	"worker_num"=>16,//线程池的worker进程的数量 cpu核心数的2倍
	"pool_num"=>100,//链接池的max数量
],
```
