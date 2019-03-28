<?php

function p ($p){
    echo "<pre>";
    print_r($p);die;
}

define('__ROOT__',__DIR__);
require __DIR__.'/framework/Base.php';
// require __DIR__.'/vendor/autoload.php';//加载composer包
(new \framework\Base())->run();