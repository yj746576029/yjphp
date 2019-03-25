<?php

namespace framework;

class Db{

    //单例模式,本类的实例
    private static $instance = null;

    //数据库的连接
    private $con = null;

    /**
     * Db 构造方法
     * 私有化以防止外部实例化
     */
    private function __construct(){
        $config=$GLOBALS['config']['database'];
        try{
            //配置数据源DSN
            $dsn = "{$config['type']}:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
            //创建PDO对象
            $this->con = new \PDO($dsn,$config['username'],$config['password'],[]);
            //设置客户端的默认字符集
            $this->con->query("SET NAMES {$config['charset']}");
        }catch(\PDOException $e){
            die('数据库连接失败'.$e->getMessage());
        }
    }

    /**
     * 禁止外部克隆该实例
     */
    private function __clone(){

    }

    /**
     * 数据库初始化，并取得数据库类实例(单例)
     */
    public static function connect(){
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 执行sql
     */
    public function query($sql, $bind = []){
        $statement = $this->con->prepare($sql);//采用预处理，防止sql注入
        $statement->execute($bind);
        return $statement;
    }
    
    /**
     * 获取最后插入的id
     */
    public function lastInsertId(){
        return $this->con->lastInsertId();
    }

}