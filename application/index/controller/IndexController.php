<?php
/**
 * Created by PhpStorm.
 * User: yangjie
 * Date: 2019/1/31
 * Time: 16:08
 */

namespace application\index\controller;


use framework\Db;
use framework\Controller;

class IndexController extends Controller{

    public function indexAction(){
        // $db=Db::connect();
        // $data=[
        //     'name' => '杨杰aa',
        //     'email' => '杨杰@php.cn'
        // ];
        // $res=$db->table('student')->where(['id'=>1])->find();
        $this->assign('title','Welcome to yjphp !');
        $this->display();
    }

}