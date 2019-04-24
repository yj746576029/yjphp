<?php

namespace framework\library;

class View
{
    private $viewPath = ''; //模板路径
    private $data = []; //模板变量

    public function __construct()
    {
        $this->viewPath = ROOT . '\\application\\' . Request::instance()->module() . '\\view\\';
    }

    /**
     * 设置模板变量
     * @param $key string | array
     * @param $value
     */
    public function assign($key, $value)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } elseif (is_string($key)) {
            $this->data[$key] = $value;
        }
    }

    /**
     * 解析模板
     * @param string $content
     * @return string
     */
    public function parse($content = '')
    {
        // 解析模板变量
        $newContent = $content;
        preg_match_all('/{(.*)}/U', $content, $matches);
        foreach ($matches[0] as $k => $match) {
            $flag = substr($match, 1, 1);
            switch ($flag) {
                case '$':
                    $str = '<?php echo ' . $matches[1][$k] . ' ; ?>';
                    break;
                case 'l':
                    $arr = explode(' ', $matches[1][$k]);
                    $arrLength = count($arr);
                    if ($arrLength == 3) {
                        $str = '<?php foreach( ' . $arr[1] . ' as ' . $arr[2] . ' ){?>';
                    }
                    if ($arrLength == 4) {
                        $str = '<?php foreach( ' . $arr[1] . ' as ' . $arr[2] . '=>' . $arr[2] . ' ){?>';
                    }
                    break;
                case 'i':
                    $str = '<?php ' . $matches[1][$k] . ' {?>';
                    break;
                case 'e':
                    $str = '<?php } ' . $matches[1][$k] . ' {?>';
                    break;
                case '/':
                    $str = '<?php } ?>';
                    break;
                default:
                    break;
            }
            $newContent = str_replace($match, $str, $newContent);
        }
        return $newContent;
    }

    /**
     * 编译文件
     * @param string $compilerFile
     * @param string $content
     * @return string
     */
    public function compiler($compilerFile, $content)
    {
        if (!is_dir(ROOT . '/' . 'runtime/data/')) {
            mkdir(ROOT . '/' . 'runtime/data/', 0777); // 使用最大权限0777创建文件
        }
        file_put_contents($compilerFile, $content);
        return $compilerFile;
    }

    /**
     * 检查编译缓存是否有效
     * 如果无效则需要重新编译
     * @param string $templateFile
     * @param string $compilerFile
     * @return bool
     */
    public function checkFile($templateFile, $compilerFile)
    {
        if (file_exists($compilerFile) && filemtime($templateFile) > filemtime($compilerFile)) {
            //说明模板文件做了修改，需要重新生成缓存
            return true;
        } else {
            return true;
        }
    }

    /**
     * 渲染模板
     * @param $template
     * @return string
     */
    public function display($template = '')
    {
        $templateFile = ''; //模板文件
        $arr = explode('/', $template);
        $length = count($arr);
        if ($length >= 3) {
            throw new \Exception('无法解析模板参数' . $template);
        } elseif ($length == 2) {
            $templateFile = $this->viewPath . $arr[0] . '\\' . $arr[1] . '.html';
        } elseif ($length == 1) {
            $templateFile = $this->viewPath . Request::instance()->controller() . '\\' . Request::instance()->action() . '.html';
        }
        extract($this->data);
        $content = file_get_contents($templateFile);
        $content = $this->parse($content);
        $compilerFile = ROOT . '/' . 'runtime/data/' . md5($templateFile) . '.php';
        if ($this->checkFile($templateFile, $compilerFile)) {
            $compilerFile = $this->compiler($compilerFile, $content);
        }
        ob_start();
        include $compilerFile;
        echo ob_get_clean();
    }
}
