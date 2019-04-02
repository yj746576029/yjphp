yjphp
===============

## 目录结构

初始的目录结构如下：

~~~
www  WEB部署目录（或者子目录）
├─application           应用目录
│  └─module_name        模块目录
│     ├─controller      控制器目录
│     ├─model           模型目录
│     └─view            视图目录
│
├─framework             框架系统目录
│  ├─Base.php           基础类
│  ├─Config.php         配置类
│  ├─Controller.php     控制器类
│  ├─Db.php             数据库类
│  ├─Model.php          模型类
│  ├─Request.php        请求类
│  └─View.php           视图类
│
├─statics               静态文件目录（css，js，image）
├─runtime               应用的运行时目录（可写，可定制）
├─vendor                第三方类库目录（Composer依赖库）
├─config.php            配置文件
├─function.php          函数文件
├─index.php             入口文件
├─composer.json         composer 定义文件
├─README.md             README 文件
~~~


## 命名规范

`yjphp`注意如下规范：

### 目录和文件

*   目录不强制规范，驼峰和小写+下划线模式均支持；
*   类库、函数文件统一以`.php`为后缀；
*   类的文件名均以命名空间定义，并且命名空间的路径和类库文件所在路径一致；
*   类名和类文件名保持一致，统一采用驼峰法命名（首字母大写）；

### 函数和类、属性命名

*   类的命名采用驼峰法，并且首字母大写，例如 `User`、`UserType`；
*   函数的命名使用小写字母和下划线（小写字母开头）的方式，例如 `get_client_ip`；
*   方法的命名使用驼峰法，并且首字母小写，例如 `getUserName`；
*   属性的命名使用驼峰法，并且首字母小写，例如 `tableName`、`instance`；
*   以双下划线“__”打头的函数或方法作为魔法方法，例如 `__call` 和 `__autoload`；

### 常量和配置

*   常量以大写字母和下划线命名，例如 `ROOT`；
*   配置参数以小写字母和下划线命名，例如 `url_route_on` 和`url_convert`；

### 数据表和字段

*   数据表和字段采用小写加下划线方式命名，并注意字段名不要以下划线开头，例如 `think_user` 表和 `user_name`字段，不建议使用驼峰和中文作为数据表字段命名。
