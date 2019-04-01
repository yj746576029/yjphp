<?php

namespace framework;

class Config {
    /**
     * @var array 配置参数
     */
    private static $config = [];

    /**
     * 加载配置文件（PHP格式）
     * @access public
     * @return mixed
     */
    public static function load(){
        $file=ROOT.'/'.'config.php';
        if (is_file($file)) {
            return self::set(include $file);
        }
        return self::$config;
    }

    /**
     * 设置配置参数 name 为数组则为批量设置
     * @access public
     * @param  string|array $name  配置参数名（支持二级配置 . 号分割）
     * @param  mixed        $value 配置值
     * @return mixed
     */
    public static function set($name='', $value = null){

        // 字符串则表示单个配置设置
        if (is_string($name)) {
            if (!strpos($name, '.')) {
                self::$config[strtolower($name)] = $value;
            } else {
                // 二维数组
                $name = explode('.', $name, 2);
                self::$config[strtolower($name[0])][$name[1]] = $value;
            }

            return $value;
        }

        // 数组则表示批量设置
        if (is_array($name)) {
            if (!empty($value)) {
                self::$config[$value] = isset(self::$config[$value]) ?
                    array_merge(self::$config[$value], $name) :
                    $name;

                return self::$config[$value];
            }

            return self::$config = array_merge(
                self::$config, array_change_key_case($name)
            );
        }

        // 为空直接返回已有配置
        return self::$config;
    }



    /**
     * 获取配置参数 为空则获取所有配置
     * @access public
     * @param  string $name 配置参数名（支持二级配置 . 号分割）
     * @return mixed
     */
    public static function get($name = null)
    {

        // 无参数时获取所有
        if (empty($name)) {
            return self::$config;
        }

        // 非二级配置时直接返回
        if (!strpos($name, '.')) {
            $name = strtolower($name);
            return isset(self::$config[$name]) ? self::$config[$name] : null;
        }

        // 二维数组设置和获取支持
        $name    = explode('.', $name, 2);
        $name[0] = strtolower($name[0]);

        return isset(self::$config[$name[0]][$name[1]]) ?
            self::$config[$name[0]][$name[1]] :
            null;
    }

}
