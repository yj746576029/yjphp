<?php
/**
 * Created by PhpStorm.
 * User: yangjie
 * Date: 2019/1/31
 * Time: 10:56
 */

namespace framework;

class Db{

    //单例模式,本类的实例
    private static $instance = null;

    //数据库的连接
    private $con = null;

    //新增主键id
    public $insertId = null;

    //受影响的记录数
    public $count = null;

    //表名
    public $tableName=null;

    //查询字段
    public $field='*';

    //表连接
    public $join='';

    //条件
    public $where='';

    //排序
    public $order='';

    //记录限定
    public $limit='';

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
            $this->con = new \PDO($dsn,$config['username'],$config['password']);
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

    //完成数据表的写操作:新增,更新,删除
    //返回受影响的记录，如果新增还返回新增主键id
    private function exec($sql){
        $num = $this->con->exec($sql);
        //如果有受影响的记录
        if($num > 0){
            //如果是新增操作，初始化新增主键id属性
            if(null !==$this->con->lastInsertId()){
                $this->insertId = $this->con->lastInsertId();
            }
            $this->count = $num;  //返回受影响的记录数量
            return true;
        }else {
            $error = $this->con->errorInfo(); //获取最后操作的错误信息的数组
            //[0]错误标识符[1]错误代码[2]错误信息
            echo '操作失败'.$error[0].':'.$error[1].':'.$error[2];
            return false;
        }
    }

    //完成数据表的读操作
    private function query($sql){
        return $this->con->query($sql);
    }

    public function table($tableName){
        $prefix=$GLOBALS['config']['database']['prefix'];
        if(strpos($tableName,',') !== false){
            $arr=explode(',',$tableName);
            $this->tableName=$prefix.$arr[0].' AS '.$arr[1];
        }else{
            $this->tableName=$prefix.$tableName;
        }
        return $this;
    }

    public function field($field){
        $this->field = $field;
        return $this;
    }

    public function join($tableName,$on,$type='inner'){
        $t=strtoupper($type);
        $onArr=explode(',',$on);
        if(strpos($tableName,',') !== false){
            $tableNameArr=explode(',',$tableName);
            $this->join.=' '.$t.' JOIN '.$tableNameArr[0].' AS '.$tableNameArr[1] .' ON '.$onArr[0].' = '.$onArr[1];
        }else{
            $this->join.=' '.$t.' JOIN '.$tableName .' ON '.$onArr[0].' = '.$onArr[1];
        }
        return $this;
    }

    public function where(array $where=[]){
        $this->where=count($where)>0?' WHERE':'';
        foreach ($where as $k=>$v){
            //判断是否为一维数组
            if(!is_array($v)){
                $this->where .= $k == 0 ? " {$k} = '{$v}'":" AND {$k} = '{$v}'";
            }else{
                reset($v);//	将数组的内部指针指向第一个元素
                $key=key($v);//从当前内部指针位置返回元素键名
                switch ($key){
                    case 'in':
                        $this->where .= $k == 0 ? " {$k} IN ({$v['in']})":" AND {$k} IN ({$v['in']})";
                        break;
                    case 'between':
                        $arr = explode(',',$v['between']);
                        $this->where .= $k == 0 ? " ( {$k} BETWEEN {$arr[0]} AND {$arr[1]} )":" AND ( {$k} BETWEEN {$arr[0]} AND {$arr[1]} )";
                        break;
                    case 'like':
                        $this->where .= $k == 0 ? " {$k} LIKE '{$v['like']}'":" AND {$k} LIKE '{$v['like']}'";
                        break;
                    default:
                        break;
                }
            }
        }
        return $this;
    }

    public function order($order){
        $this->order = 'ORDER DY '.$order;
        return $this;
    }

    public function find(){
        $this->limit = ' LIMIT 1 ';
        $sql = 'SELECT '.$this->field.' FROM '.$this->tableName.$this->join.$this->where.$this->order.$this->limit;
        return $this->querySql($sql)->fetch(\PDO::FETCH_ASSOC);
    }

    public function findAll(){
        $sql = 'SELECT '.$this->field.' FROM '.$this->tableName.$this->join.$this->where.$this->order;
        return $this->querySql($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function insert($data){
        $column = ' ( ';
        $value = ' ( ';
        $i=0;
        foreach($data as $k=>$v){
            $column.=$i==0?$k:','.$k;
            $value.=$i==0?'\''.$v.'\'':',\''.$v.'\'';
            ++$i;
        }
        $column .= ' ) ';
        $value .= ' ) ';
        $sql = 'INSERT INTO '.$this->tableName.$column.' VALUES'.$value;
        return $this->querySql($sql);
    }

    public function update($data){
        $str= ' ';
        $i=0;
        foreach($data as $k=>$v){
            $str.=$i==0?$k.' = \''.$v.'\'':','.$k.' = \''.$v.'\'';
            ++$i;
        }
        $sql = 'UPDATE '.$this->tableName.' SET' .$str.$this->where;
        return $this->querySql($sql);
    }

    public function delete(){
        $sql = 'DELETE FROM '.$this->tableName.$this->where;
        return $this->querySql($sql);
    }

    public function querySql($sql){
        //需要防止sql注入，待优化
        if(strpos($sql,'SELECT') !== false){
            return $this->query($sql);
        }else{
            return $this->exec($sql);
        }
    }
}