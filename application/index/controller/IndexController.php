<?php

namespace application\index\controller;

use framework\Controller;
// use application\index\model\StudentModel;

class IndexController extends Controller{

    public function indexAction(){
        // $model=new StudentModel();
        // p($model->getAll()->fetch(\PDO::FETCH_ASSOC));
        $this->assign('title','Welcome to yjphp !');
        $this->display();
    }

}