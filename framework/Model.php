<?php

namespace framework;

class Model{

    protected $db = null;  //数据库连接对象

    public $tableName; //表名

    public function __construct(){
        // get_called_class() 也可用static::class
        // 把子类名转化成表名
       $str=strrchr(get_called_class(),'\\');
       $str=str_replace('\\','',$str);
       $str=str_replace('Model','',$str);
       $this->tableName=strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $str));
       //连接数据库
       $this->db = Db::connect();
    }

    // //获取全部数据
    // public function getAll(){
    //     $sql = "SELECT * FROM student";
    //     return $this->db->querySql($sql);
    // }

}    