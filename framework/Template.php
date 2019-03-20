<?php
namespace framework;
class Template {
    private $templatePath=''; //模板路径
    private $data=[];//模板变量

    public function __construct(){
        $this->templatePath=__ROOT__.'\\application\\'.__MODULE__.'\\template\\'.__ACTION__.'\\';
    }

    /**
     * 设置模板变量
     * @param $key string | array
     * @param $value
     */
    public function assign($key, $value) {
        if(is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } elseif(is_string($key)) {
            $this->data[$key] = $value;
        }
    }

    /**
     * 渲染模板
     * @param $template
     * @return string
     */
    public function display($template) {
        extract($this->data);
        ob_start();
        include $this->templatePath . ($template!=''?:__ACTION__).'.php';      
        $res = ob_get_contents();
        ob_end_clean();
        echo $res;
    }

}