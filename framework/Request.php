<?php

namespace framework;

class Request{
    /**
     * @var object 对象实例
     */
    protected static $instance;

    protected $method;
    protected $pathinfo;

    /**
     * @var array 当前调度信息
     */
    protected $module;
    protected $controller;
    protected $action;

    /**
     * @var array 请求参数
     */
    protected $get     = [];
    protected $post    = [];
    protected $request = [];

    /**
     * 构造函数(防止外部实例化)
     * @access private
     */
    private function __construct(){}

    /**
     * 禁止外部克隆该实例
     */
    private function __clone(){}

    /**
     * 初始化
     * @access public
     * @param array $options 参数
     * @return \think\Request
     */
    public static function instance(){
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 当前的请求类型
     * @access public
     * @param bool $method true 获取原始请求类型
     * @return string
     */
    public function method(){
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $this->method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
        } else {
            $this->method = $this->server('REQUEST_METHOD') ?: 'GET';
        }
        return $this->method;
    }

    /**
     * 是否为GET请求
     * @access public
     * @return bool
     */
    public function isGet(){
        return $this->method() == 'GET';
    }

    /**
     * 是否为POST请求
     * @access public
     * @return bool
     */
    public function isPost(){
        return $this->method() == 'POST';
    }

    /**
     * 当前是否Ajax请求
     * @access public
     * @return bool
     */
    public function isAjax(){
        $value  = $this->server('HTTP_X_REQUESTED_WITH', '', 'strtolower');
        $result = ('xmlhttprequest' == $value) ? true : false;
        return $result;
    }

    /**
     * 设置获取GET参数
     * @access public
     * @param string|array $name    变量名
     * @param mixed        $default 默认值
     * @return mixed
     */
    public function get($name = '', $default = null){
        if (empty($this->get)) {
            $this->get = $_GET;
        }
        if (is_array($name)) {
            return $this->get = array_merge($this->get, $name);
        }
        return $this->input($this->get, $name, $default);
    }

    /**
     * 设置获取POST参数
     * @access public
     * @param string|array       $name    变量名
     * @param mixed        $default 默认值
     * @return mixed
     */
    public function post($name = '', $default = null){
        if (empty($this->post)) {
            $content = $this->input;
            if (empty($_POST) && false !== strpos($this->contentType(), 'application/json')) {
                $this->post = (array) json_decode($content, true);
            } else {
                $this->post = $_POST;
            }
        }
        if (is_array($name)) {
            return $this->post = array_merge($this->post, $name);
        }
        return $this->input($this->post, $name, $default);
    }

    /**
     * 获取request变量
     * @param string|array $name    数据名称
     * @param string       $default 默认值
     * @return mixed
     */
    public function request($name = '', $default = null){
        if (empty($this->request)) {
            $this->request = $_REQUEST;
        }
        if (is_array($name)) {
            return $this->request = array_merge($this->request, $name);
        }
        return $this->input($this->request, $name, $default);
    }

    /**
     * 获取变量 支持过滤和默认值
     * @param array        $data    数据源
     * @param string|false $name    字段名
     * @param mixed        $default 默认值
     * @return mixed
     */
    public function input($data = [], $name = '', $default = null){
        if (false === $name) {
            // 获取原始数据
            return $data;
        }
        $name = (string) $name;
        if ('' != $name) {
            // 解析name
            if (strpos($name, '/')) {
                list($name, $type) = explode('/', $name);
            } else {
                $type = 's';
            }
            // 按.拆分成多维数组进行判断
            foreach (explode('.', $name) as $val) {
                if (isset($data[$val])) {
                    $data = $data[$val];
                } else {
                    // 无输入数据，返回默认值
                    return $default;
                }
            }
            if (is_object($data)) {
                return $data;
            }
        }
        return $data;
    }

    /**
     * 设置或者获取当前的模块名
     * @access public
     * @param string $module 模块名
     * @return string|Request
     */
    public function module($module = null){
        if (!is_null($module)) {
            $this->module = $module;
            return $this;
        } else {
            return $this->module ?: '';
        }
    }

    /**
     * 设置或者获取当前的控制器名
     * @access public
     * @param string $controller 控制器名
     * @return string|Request
     */
    public function controller($controller = null){
        if (!is_null($controller)) {
            $this->controller = $controller;
            return $this;
        } else {
            return $this->controller ?: '';
        }
    }

    /**
     * 设置或者获取当前的操作名
     * @access public
     * @param string $action 操作名
     * @return string|Request
     */
    public function action($action = null){
        if (!is_null($action) && !is_bool($action)) {
            $this->action = $action;
            return $this;
        } else {
            $name = $this->action ?: '';
            return true === $action ? $name : strtolower($name);
        }
    }

    // /**
    //  * 获取当前请求URL的pathinfo信息（含URL后缀）
    //  * @access public
    //  * @return string
    //  */
    // public function parsePathinfo()
    // {
    //     if (is_null($this->pathinfo)) {
    //         // 分析PATHINFO信息
    //         if (!isset($_SERVER['PATH_INFO'])) {
    //             foreach (['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'] as $type) {
    //                 if (!empty($_SERVER[$type])) {
    //                     $_SERVER['PATH_INFO'] = (0 === strpos($_SERVER[$type], $_SERVER['SCRIPT_NAME'])) ?
    //                     substr($_SERVER[$type], strlen($_SERVER['SCRIPT_NAME'])) : $_SERVER[$type];
    //                     break;
    //                 }
    //             }
    //         }
    //         $this->pathinfo = empty($_SERVER['PATH_INFO']) ? '/' : ltrim($_SERVER['PATH_INFO'], '/');
    //     }
    //     $arr = explode('/',$this->pathinfo);
    //     return ['m'=>$arr[0],'c'=>$arr[1],'a'=>$arr[2]];
    // }

}
