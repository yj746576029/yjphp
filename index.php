<?php
/**
 * Created by PhpStorm.
 * User: yangjie
 * Date: 2019/1/31
 * Time: 10:26
 */
function p ($p){
    echo "<pre>";
    print_r($p);die;
}
define('__ROOT__',__DIR__);
require __DIR__.'/framework/base.php';
(new \framework\Base())->run();