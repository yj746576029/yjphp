<?php

namespace application\index\controller;

use framework\Controller;

class IndexController extends Controller{

    public function indexAction(){
        dump2txt('111');
        $this->assign('title','Welcome to yjphp !');
        $this->display();
    }

}