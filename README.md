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
│  ├─library            底层类库
│  │  ├─Base.php        基础类
│  │  ├─Config.php      配置类
│  │  ├─Controller.php  控制器类
│  │  ├─Db.php          数据库类
│  │  ├─Model.php       模型类
│  │  ├─Request.php     请求类
│  │  └─View.php        视图类
│  │
│  ├─start.php          引导文件
│  └─helper.php         助手函数
│
├─statics               静态文件目录（css，js，image）
├─runtime               应用的运行时目录（可写，可定制）
├─vendor                第三方类库目录（Composer依赖库）
├─config.php            配置文件
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

*   数据表和字段采用小写加下划线方式命名，并注意字段名不要以下划线开头，例如 `user` 表和 `user_name`字段，不建议使用驼峰和中文作为数据表字段命名。


## 使用说明

`yjphp`使用说明如下：

### 配置文件

*   根目录下config.php
*   配置如下：
```php
return [
    // +----------------------------------------------------------------------
    // | 数据库设置
    // +----------------------------------------------------------------------
    'database' => [
        // 数据库类型
        'type'      => 'mysql',
        // 服务器地址
        'host'      => '127.0.0.1',
        // 数据库名
        'dbname'    => 'yjphp',
        // 用户名
        'username'  => 'root',
        // 密码
        'password'  => 'root',
        // 端口
        'port'      => '3306',
        // 数据库编码默认采用utf8
        'charset'   => 'utf8',
        // 数据库表前缀
        'prefix'    => ''
    ],
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------
    'app' => [
        //默认模块
        'default_module'        => 'index', 
        //默认控制器
        'default_controller'    => 'index', 
        //默认方法
        'default_action'        => 'index', 
    ],
    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------
    'url' => [
        'pathinfo' => true,        //开启pathinfo
    ]
];
```

### 路由

*   pathinfo开启的时候
*   路由规则为：http://serverName/index.php/模块名/控制器名/方法名/参数名1/参数值1/参数名2/参数值2...
*   pathinfo关闭的时候
*   路由规则为：http://serverName/index.php?m=模块名&c=控制器名&a=方法名&参数名1=参数值1&参数名2=参数值2...

### 控制器

*   以模块名为index，控制器名为index举例：
*   控制器在application\index\controller\IndexController.php
*   可以无需继承任何的基础类，也可以继承\framework\library\Controller类或者其他的控制器类。
*   典型的控制器类定义如下：
```php
    namespace application\index\controller;

    class Index 
    {
        public function indexAction()
        {
            return 'index';
        }
    }
```

### 请求

*   如果要获取当前的请求信息，可以使用\framework\library\Request类，
*   使用方法：
```php
//获取实例
$request = Request::instance();
//获取当前的请求类型
$request->method();
//是否为GET请求
$request->isGet();
//是否为POST请求
$request->isPost();
//是否为AJAX请求
$request->isAjax();
//设置或者获取GET参数
$request->get();
//设置或者获取POST参数
$request->post();
//设置或者获取request变量
$request->request();
//设置或者获取当前的模块名
$request->module();
//设置或者获取当前的控制器名
$request->controller();
//设置或者获取当前的操作名
$request->action();
```

### 数据库

*   Db操作数据库
```php
//获取连接数据库的实例
$db=Db::connect();
//基本查询；fetch()获取一条，fetchAll()获取多条
$db->table('table')->fetch();
//where();普通用法
$db->table('table')->where(['id'=>1])->fetch();
//where();like用法
$db->table('table')->where(['name'=>['like'=>'%张三%']])->fetchAll();
//where();in用法
$db->table('table')->where(['id'=>['in'=>'1,2,3']])->fetchAll();
//where();between用法
$db->table('table')->where(['id'=>['between'=>'1,3']])->fetchAll();
//join();
$db->table('table_a a')->join('table_b b','a.id,b.pid','left')->where(['a.id'=>1])->fetch();
//field();field()放置的位置不做要求，但必须放在fetch()或fetchAll()之前调用；
$db->table('table_a a')->join('table_b b','a.id,b.pid','left')->field('a.*')->where(['a.id'=>1])->fetch();
//orderBy();
$db->table('table')->where(['id'=>1])->orderBy('sort DESC,id ASC')->fetch();
//insert();
$db->table('table')->insert(['name'=>'张三','age'=>30,'sex'=>'男']);
//insert();
$db->table('table')->insert(['name'=>'张三','age'=>30,'sex'=>'男']);
//update();需要和where()一起用
$db->table('table')->where(['id'=>1])->update(['name'=>'张三','age'=>30,'sex'=>'男']);
//delete();需要和where()一起用
$db->table('table')->where(['id'=>1])->delete();
```

### 模型

*   以demo模型举例：
*   模型在application\index\model\DemoModel.php
*   可以无需继承任何的基础类，也可以继承\framework\library\Model类或者其他的模型类。
*   典型的控制器类定义如下：
```php
    namespace application\index\model;

    use framework\library\Model;

    class DemoModel extends Model
    {
    }
```
*   在控制器中调用模型如下：
```php
    namespace application\index\controller;

    use application\index\model\DemoModel;

    class Index 
    {
        public function indexAction()
        {
            $model=new DemoModel();
            $res=$model->where(['id' =>1])->fetch();
            return 'index';
        }
    }
```
*   模型同样可以使用Db的链式查询，但模型实例无法调用table()，模型自动采用模型类名作为表名

### 视图模板

*   以模块名为index，控制器名为index举例：
*   控制器在application\index\controller\IndexController.php
*   用法如下：
```php
    namespace application\index\controller;

    use framework\library\Controller;

    class IndexController extends Controller
    {

        public function indexAction()
        {
            $this->view();
        }
    }
```
*   此时模板为application\index\view\index\index.html
```html
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>yjphp</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <h1>Welcome to yjphp</h1>
    </body>
    </html>
```
*   当然我们还可以指定模板：
```php
    namespace application\index\controller;

    use framework\library\Controller;

    class IndexController extends Controller
    {

        public function indexAction()
        {
            $this->view('demo/index');
        }
    }
```
*   此时视模板径为application\index\view\demo\index.html

### 模板标签

*   模板中我们还可以使用简单的模板标签：
```php
    namespace application\index\controller;

    use framework\library\Controller;

    class IndexController extends Controller
    {

        public function indexAction()
        {
            $this->assign('title','Welcome to yjphp');
            // $this->assign(['title' => 'Welcome to yjphp']);
            $this->view();
        }
    }
```
```html
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>yjphp</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <h1>{$title}</h1>
    </body>
    </html>
```
*   if标签：{if ($a==1)} {elseif ($a==2)} {else} {/if}
*   loop标签：{loop $list $k $v} {/loop}
*   标签中直接使用php函数：{strlen($a)}