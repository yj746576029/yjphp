<?php

namespace framework;

class Base{

    public function run(){
        $this -> registerAutoLoad(); //注册自动加载
        $this -> loadConfig();  //加载配置
        $this -> registerError(); //异常处理
        $this -> getRequestParams(); //获取请求参数
        $this -> dispatch();  //请求分发
    }

    private function loadConfig(){
        Config::load();//加载配置文件
    }

    private function registerAutoLoad(){
        spl_autoload_register([$this, 'autoload']);
    }
    private function registerError(){
        error_reporting(E_ALL);// 报告所有错误
        set_error_handler([$this, 'appError']);//在trigger_error()的时候触发
        set_exception_handler([$this, 'appException']);//在throw new \Exception()的时候触发
        register_shutdown_function([$this, 'appShutdown']);//程序终止时触发，如发生致命错误、die()、exit()
    }

    private function autoload($className){
        require __ROOT__.'/'.$className.'.php';
    }

    public function appError($errno, $errstr, $errfile, $errline){
        echo "<pre>";
        echo "<b>Custom error:</b> [$errno] $errstr<br>";
        echo " Error on line $errline in $errfile<br>";
    }

    public function appException($exception){
        echo '<pre>';
        echo $exception;
    }

    public function appShutdown(){
        //一般记录错误到log
    }

    /**
     * 获取请求参数
     */
    private function getRequestParams(){    
        if(Config::get('url.pathinfo')){

        }else{
            //当前模块
            $m = isset($_GET['m'])?$_GET['m']:Config::get('app.default_module');
            //当前控制器
            $c = isset($_GET['c'])?$_GET['c']:Config::get('app.default_controller');
            //当前方法
            $a = isset($_GET['a'])?$_GET['a']:Config::get('app.default_action');
        }
        $request=Request::instance();//获取请求类实例
        $request->module($m);//设置当前模块
        $request->controller($c);//设置当前控制器
        $request->action($a);//设置当前方法
    }

    private function dispatch(){
        //实例化控制器
        $controllerName = ucfirst(Request::instance()->controller().'Controller');
        $class='\application\\'.Request::instance()->module().'\\controller\\'.$controllerName;
        $controller = new $class();
        //调用当前方法
        $actionName = Request::instance()->action().'Action';
        $controller -> $actionName();
    }

}