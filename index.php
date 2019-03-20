<?php

function p ($p){
    echo "<pre>";
    print_r($p);die;
}

define('__ROOT__',__DIR__);
require __DIR__.'/framework/Base.php';
(new \framework\Base())->run();