<?php

/**
 * 调试输出
 */
function dump($p){
    echo "<pre>";
    print_r($p);
    echo "</pre>";
    die;
}

/**
 * 将调试内容输出到文件中，在异步调用时用到
 */
function dump2txt($content) {
    $filename = ROOT.'/'.'runtime/log/dump2txt.txt';
    if (!is_dir( ROOT.'/'.'runtime/log/')) {
        mkdir( ROOT.'/'.'runtime/log/', 0777); // 使用最大权限0777创建文件
    }
    $import_data = print_r($content,1);
    $import_data = "================".date('Y-m-d H:i:s')."---".md5(time().mt_rand(1,1000000))."================\r\n".$import_data."\r\n";
    file_put_contents($filename, $import_data,FILE_APPEND);
}

/**
 * 返回json对象
 */
function json($data = '') {
    header('Content-Type:application/json');
    exit(json_encode($data));
}

/**
 * 序列化
 */
function serializer($value) {
	return serialize($value);
}

/**
 * 反序列化
 */
function unserializer($value) {
	if (empty($value)) {
		return array();
	}
	if (!is_serialized($value)) {
		return $value;
	}
	$result = unserialize($value);
	if ($result === false) {
		$temp = preg_replace_callback('!s:(\d+):"(.*?)";!s', function ($matchs){
			return 's:'.strlen($matchs[2]).':"'.$matchs[2].'";';
		}, $value);
		return unserialize($temp);
	} else {
		return $result;
	}
}