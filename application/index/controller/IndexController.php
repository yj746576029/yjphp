<?php

namespace application\index\controller;

use framework\library\Controller;
use framework\library\Db;
use application\index\model\DemoModel;

class IndexController extends Controller
{

    public function indexAction()
    {
        // dump(Db::connect()->table('demo')->where(['name'=>['like'=>'%张三%']])->fetchAll());
        // dump((new DemoModel())->where(['id' =>1])->fetch());
        $this->assign(['title' => 'Welcome to use yjphp']);
        $this->view();
    }
}
