<?php

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

if (!function_exists('dump')) {
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

if (!function_exists('hasStr')) {
	/**
	 * 判断字符串中是否含有某个字符串
	 */
	function hasStr($haystack, $needle)
	{
		if (strpos($haystack, $needle) === false) {
			return false;
		} else {
			return true;
		}
	}
}
