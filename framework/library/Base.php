<?php

namespace framework\library;

class Base
{

    public function run()
    {
        $this->registerError(); //注册异常处理
        $this->registerAutoLoad(); //注册自动加载
        $this->loadConfig();  //加载配置
        $this->loadHelper(); //加载函数文件
        $this->loadComposer(); //加载composer包
        $this->requestParse(); //请求解析
        $this->dispatch();  //请求分发
    }

    private function loadConfig()
    {
        Config::load(); //加载配置文件
    }

    private function loadHelper()
    {
        require ROOT . '/framework/helper.php'; //加载函数文件
    }
    private function loadComposer()
    {
        $file = ROOT . '/vendor/autoload.php';
        if (file_exists($file)) {
            require $file; //加载composer包
        }
    }

    private function registerAutoLoad()
    {
        spl_autoload_register([$this, 'autoload']);
    }
    private function registerError()
    {
        error_reporting(E_ALL); // 报告所有错误
        set_error_handler([$this, 'appError']); //在trigger_error()的时候触发
        set_exception_handler([$this, 'appException']); //在throw new \Exception()的时候触发
        register_shutdown_function([$this, 'appShutdown']); //程序终止时触发，如发生致命错误、die()、exit()
    }

    private function autoload($className)
    {
        require ROOT . '/' . $className . '.php';
    }

    public function appError($errno, $errstr, $errfile, $errline)
    {
        echo "<pre>";
        echo "<b>Custom error:</b> [$errno] $errstr<br>";
        echo " Error on line $errline in $errfile<br>";
    }

    public function appException($exception)
    {
        echo '<pre>';
        echo $exception;
    }

    public function appShutdown()
    {
        //一般记录错误到log
    }

    /**
     * 请求解析
     */
    private function requestParse()
    {
        Request::instance()->parse();
    }

    private function dispatch()
    {
        $request = Request::instance();
        //实例化控制器
        $controllerName = ucfirst($request->controller() . 'Controller');
        $class = '\application\\' . $request->module() . '\\controller\\' . $controllerName;
        $controller = new $class();
        //调用当前方法
        $actionName = $request->action() . 'Action';
        $controller->$actionName();
    }
}
