<?php

namespace application\index\controller;

use framework\Controller;
use application\index\model\IndexModel;
use framework\Db;

class IndexController extends Controller{

    public function indexAction(){
        // $model=new IndexModel();
        // dump($model->where(['id'=>1])->fetch());
        dump(Db::connect()->table('`index`')->where(['id'=>1])->field('`index`,id')->fetch());

        $this->assign('title','Welcome to yjphp !');
        $this->display();
    }

}