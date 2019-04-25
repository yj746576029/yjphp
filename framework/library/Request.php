<?php

namespace framework\library;

class Request
{
    /**
     * 对象实例
     */
    protected static $instance;

    /**
     * 当前调度信息
     */
    protected $module;
    protected $controller;
    protected $action;

    /**
     * 请求参数
     */
    protected $get     = [];
    protected $post    = [];
    protected $request = [];
    protected $method;
    protected $pathinfo;

    /**
     * 构造函数(防止外部实例化)
     */
    private function __construct()
    { }

    /**
     * 禁止外部克隆该实例
     */
    private function __clone()
    { }

    /**
     * 初始化
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 当前的请求类型
     */
    public function method()
    {
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $this->method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
        } else {
            $this->method = $this->server('REQUEST_METHOD') ?: 'GET';
        }
        return $this->method;
    }

    /**
     * 是否为GET请求
     */
    public function isGet()
    {
        return $this->method() == 'GET';
    }

    /**
     * 是否为POST请求
     */
    public function isPost()
    {
        return $this->method() == 'POST';
    }

    /**
     * 当前是否Ajax请求
     */
    public function isAjax()
    {
        $value  = $this->server('HTTP_X_REQUESTED_WITH', '', 'strtolower');
        $result = ('xmlhttprequest' == $value) ? true : false;
        return $result;
    }

    /**
     * 设置或者获取GET参数
     * @param string|array $name    变量名
     */
    public function get($name = '')
    {
        if (empty($this->get)) {
            $this->get = $_GET;
        }
        if (is_array($name)) {
            return $this->get = array_merge($this->get, $name);
        }
        return $this->get;
    }

    /**
     * 设置或者获取POST参数
     * @param string|array $name    变量名
     */
    public function post($name = '')
    {
        if (empty($this->post)) {
            $content = $this->input;
            if (empty($_POST) && false !== strpos($this->contentType(), 'application/json')) {
                $this->post = (array)json_decode($content, true);
            } else {
                $this->post = $_POST;
            }
        }
        if (is_array($name)) {
            return $this->post = array_merge($this->post, $name);
        }
        return $this->post;
    }

    /**
     * 设置或者获取request变量
     * @param string|array $name    数据名称
     */
    public function request($name = '')
    {
        if (empty($this->request)) {
            $this->request = $_REQUEST;
        }
        if (is_array($name)) {
            return $this->request = array_merge($this->request, $name);
        }
        return $this->request;
    }


    /**
     * 设置或者获取当前的模块名
     * @param string $module 模块名
     */
    public function module($module = null)
    {
        if (!is_null($module)) {
            $this->module = $module;
            return $this;
        } else {
            return $this->module ?: '';
        }
    }

    /**
     * 设置或者获取当前的控制器名
     * @param string $controller 控制器名
     */
    public function controller($controller = null)
    {
        if (!is_null($controller)) {
            $this->controller = $controller;
            return $this;
        } else {
            return $this->controller ?: '';
        }
    }

    /**
     * 设置或者获取当前的操作名
     * @param string $action 操作名
     */
    public function action($action = null)
    {
        if (!is_null($action) && !is_bool($action)) {
            $this->action = $action;
            return $this;
        } else {
            $name = $this->action ?: '';
            return true === $action ? $name : strtolower($name);
        }
    }

    /**
     * 解析当前请求的URL
     */
    public function parse()
    {
        if (Config::get('url.pathinfo')) {
            if (is_null($this->pathinfo)) {
                // 分析PATHINFO信息
                if (!isset($_SERVER['PATH_INFO'])) {
                    foreach (['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'] as $type) {
                        if (!empty($_SERVER[$type])) {
                            $_SERVER['PATH_INFO'] = (0 === strpos($_SERVER[$type], $_SERVER['SCRIPT_NAME'])) ?
                                substr($_SERVER[$type], strlen($_SERVER['SCRIPT_NAME'])) : $_SERVER[$type];
                            break;
                        }
                    }
                }
                $this->pathinfo = empty($_SERVER['PATH_INFO']) ? '/' : ltrim($_SERVER['PATH_INFO'], '/');
            }
            $arr = explode('/', $this->pathinfo);
            //当前模块
            $m = isset($arr[0]) && !empty($arr[0]) ? $arr[0] : Config::get('app.default_module');
            //当前控制器
            $c = isset($arr[1]) && !empty($arr[1]) ? $arr[1] : Config::get('app.default_controller');
            //当前方法
            $a = isset($arr[2]) && !empty($arr[2]) ? $arr[2] : Config::get('app.default_action');
            //解析pathinfo里面的参数
            unset($arr[0]);
            unset($arr[1]);
            unset($arr[2]);
            $arr = array_merge($arr);
            foreach ($arr as $k => $v) {
                if ($k % 2 == 0) {
                    $_GET[$v] = isset($arr[$k + 1]) ? $arr[$k + 1] : null;
                    $_REQUEST[$v] = $_GET[$v];
                }
            }
        } else {
            //当前模块
            $m = isset($_GET['m']) && !empty($_GET['m']) ? $_GET['m'] : Config::get('app.default_module');
            //当前控制器
            $c = isset($_GET['c']) && !empty($_GET['c']) ? $_GET['c'] : Config::get('app.default_controller');
            //当前方法
            $a = isset($_GET['a']) && !empty($_GET['a']) ? $_GET['a'] : Config::get('app.default_action');
        }
        $this->module($m); //设置当前模块
        $this->controller($c); //设置当前控制器
        $this->action($a); //设置当前方法
    }
}
