<?php

namespace framework;

class Controller
{

    //模板实例
    private $template;

    public function __construct()
    {
        $this->template = new View();
    }

    /**
     * 渲染内容输出
     */
    protected function display($template = '')
    {
        $this->template->display($template);
    }

    /**
     * 模板变量赋值
     * @access protected
     * @param  mixed $name  要显示的模板变量
     * @param  mixed $value 变量的值
     * @return $this
     */
    protected function assign($name, $value = '')
    {
        $this->template->assign($name, $value);
    }
}
