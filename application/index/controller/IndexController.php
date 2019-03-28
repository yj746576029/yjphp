<?php

namespace application\index\controller;

use framework\Controller;
use framework\Db;
use application\index\model\StudentModel;
use framework\Request;

class IndexController extends Controller{

    public function indexAction(){
        Request::instance()->get(['a'=>11]);
        p($_REQUEST);
        $model=new StudentModel();
        p($model->where(['id'=>1])->fetch());
        // p(Db::connect()->table('student')->where(['id'=>1])->fetch());
        $this->assign('title','Welcome to yjphp !');
        $this->display();
    }

}