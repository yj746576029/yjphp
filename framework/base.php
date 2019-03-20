<?php

namespace framework;

class Base{

    public function run(){
        $this -> loadConfig();  //加载配置
        $this -> registerAutoLoad(); //注册自动加载
        $this -> registerError(); //异常处理
        $this -> getRequestParams(); //获取请求参数
        $this -> dispatch();  //请求分发
    }

    private function loadConfig(){
        $GLOBALS['config']=require __ROOT__.'/config.php';
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
    private function getRequestParams()
    {
        $config = $GLOBALS['config'];
        if($config['url']['pathinfo']){

        }else{
            //当前模块
            $defaultModule = $GLOBALS['config']['app']['default_module'];
            $m = isset($_GET['m'])?$_GET['m']:$defaultModule;
            define('__MODULE__', $m);
            //当前控制器
            $defaultController = $GLOBALS['config']['app']['default_controller'];
            $c = isset($_GET['c'])?$_GET['c']:$defaultController;
            define('__CONTROLLER__', $c);
            //当前方法
            $defaultAction = $GLOBALS['config']['app']['default_action'];
            $a = isset($_GET['a'])?$_GET['a']:$defaultAction;
            define('__ACTION__', $a);
        }
    }

    private function dispatch(){
        //实例化控制器
        $controllerName = ucfirst(__CONTROLLER__.'Controller');
        $class='\application\\'.__MODULE__.'\\controller\\'.$controllerName;
        $controller = new $class();
        //调用当前方法
        $actionName = __ACTION__.'Action';
        $controller -> $actionName();
    }

}