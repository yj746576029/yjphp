<?php

namespace application\index\controller;

use framework\Controller;
use application\index\model\StudentModel;

class IndexController extends Controller{

    public function indexAction(){
        $model=new StudentModel();
        $data['name']='b';
        $data['email']='aaaa';
        p($model->insert($data));
        $this->assign('title','Welcome to yjphp !');
        $this->display();
    }

}