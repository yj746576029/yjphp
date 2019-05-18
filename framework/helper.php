<?php
use framework\library\Config;

if (!function_exists('dump')) {
	/**
	 * 调试输出（无断点）
	 */
	function dump($p)
	{
		echo "<pre>";
		print_r($p);
		echo "</pre>";
	}
}

if (!function_exists('halt')) {
	/**
	 * 调试输出（有断点）
	 */
	function halt($p)
	{
		echo "<pre>";
		print_r($p);
		echo "</pre>";
		die;
	}
}

if (!function_exists('dump2txt')) {
	/**
	 * 将调试内容输出到文件中，在异步调用时用到
	 */
	function dump2txt($content)
	{
		$filename = ROOT . '/' . 'runtime/log/dump2txt.txt';
		if (!is_dir(ROOT . '/' . 'runtime/log/')) {
			mkdir(ROOT . '/' . 'runtime/log/', 0777); // 使用最大权限0777创建文件
		}
		$import_data = print_r($content, 1);
		$import_data = "================" . date('Y-m-d H:i:s') . "---" . md5(time() . mt_rand(1, 1000000)) . "================\r\n" . $import_data . "\r\n";
		file_put_contents($filename, $import_data, FILE_APPEND);
	}
}

if (!function_exists('json')) {
	/**
	 * 返回json对象
	 */
	function json($data = '')
	{
		header('Content-Type:application/json');
		exit(json_encode($data));
	}
}

if (!function_exists('iserialize')) {
	/**
	 * 序列化
	 */
	function iserialize($value)
	{
		return serialize($value);
	}
}

if (!function_exists('unserialize')) {
	/**
	 * 反序列化
	 */
	function iunserialize($value)
	{
		if (empty($value)) {
			return array();
		}
		if (!is_serialized($value)) {
			return $value;
		}
		$result = unserialize($value);
		if ($result === false) {
			$temp = preg_replace_callback('!s:(\d+):"(.*?)";!s', function ($matchs) {
				return 's:' . strlen($matchs[2]) . ':"' . $matchs[2] . '";';
			}, $value);
			return unserialize($temp);
		} else {
			return $result;
		}
	}
}

if (!function_exists('has_str')) {
	/**
	 * 判断字符串中是否含有某个字符串
	 */
	function has_str($haystack, $needle)
	{
		if (strpos($haystack, $needle) === false) {
			return false;
		} else {
			return true;
		}
	}
}

if (!function_exists('session')) {
	/**
	 * 判断字符串中是否含有某个字符串
	 */
	function session($name, $value='')
	{
		if(session_status() !== PHP_SESSION_ACTIVE){
			session_start();
		}
		if (is_array($name)) {
			// 数组
			foreach ($name as $k => $v) { 
				$_SESSION[$k]=$v;
			}
		} elseif ('' === $value) {
			// 获取
			return isset($_SESSION[$name])?$_SESSION[$name]:false;
		} elseif (is_null($value)) {
			// 删除
			unset($_SESSION[$name]);
		} else {
			// 设置
			$_SESSION[$name]=$value;
		}
	}
}

if (!function_exists('is_https')) {
	/**
     * PHP判断当前协议是否为HTTPS
     */
    function is_https() {
        if ( !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return true;
        } elseif ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
            return true;
        } elseif ( !empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return true;
        }
        return false;
    }
}

if (!function_exists('url')) {
	/**
     * 构建url
     */
    function url($param) {
		$arr=explode('/',$param);
		if(count($arr)!=3){
			throw new \Exception('url格式不正确,格式必须为：module_name/controller_name/action_name');
		}
		$rootUrl=(is_https()?'https://':'http://').$_SERVER['SERVER_NAME'].'/index.php';
		if(Config::get('url.pathinfo')){
			return $rootUrl.'/'.$arr[0].'/'.$arr[1].'/'.$arr[2];
		}else{
			return $rootUrl.'?m='.$arr[0].'&c='.$arr[1].'&a='.$arr[2];
		}	
    }
}