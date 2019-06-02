<?php

namespace framework\library;

class Db
{

    private static $instance = null; //单例模式,本类的实例

    private $con = null; //数据库的连接

    public $tableName; //表名

    public $field = '*'; //查询字段

    public $join = ''; //表连接

    public $where = ''; //条件

    public $order = ''; //排序

    public $limit = ''; //记录限定

    public $bindValue = []; //参数绑定

    public $rowCount = null; //影响的行数

    public $insertId = null; //插入数据的id

    /**
     * Db 构造方法
     * 私有化以防止外部实例化
     */
    private function __construct()
    {
        $database = Config::get('database');
        try {
            //配置数据源DSN
            $dsn = "{$database['type']}:host={$database['host']};port={$database['port']};dbname={$database['dbname']};charset={$database['charset']}";
            //创建PDO对象
            $this->con = new \PDO($dsn, $database['username'], $database['password'], []);
            //设置客户端的默认字符集
            $this->con->query("SET NAMES {$database['charset']}");
        } catch (\PDOException $e) {
            die('数据库连接失败' . $e->getMessage());
        }
    }

    /**
     * 禁止外部克隆该实例
     */
    private function __clone()
    { }

    /**
     * 数据库初始化，并取得数据库类实例(单例)
     */
    public static function connect()
    {
        
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 表名
     */
    public function table($tableName)
    {
        $prefix = Config::get('database.prefix');
        if (has_str($tableName, ' ')) {
            $arr = explode(' ', $tableName);
            $this->tableName = '`' . $prefix . $arr[0] . '` AS `' . $arr[1] . '`';
        } else {
            $this->tableName = '`' . $prefix . $tableName . '`';
        }
        return $this;
    }

    /**
     * where 条件构造
     */
    public function where(array $where = [])
    {
        //where不能重复调用
        if (empty($this->where)) {
            $this->where = count($where) > 0 ? ' WHERE' : '';
            $i = 0;
            $condition = '';
            foreach ($where as $k => $v) {
                //判断是否为一维数组
                if (!is_array($v)) {
                    if (has_str($k, '.')) {
                        $arr = explode('.', $k);
                        $condition = " `{$arr[0]}`.`{$arr[1]}` = :{$arr[1]}";
                        $this->bindValue[':' . $arr[1]] = $v;
                    } else {
                        $condition = " `{$k}` = :{$k}";
                        $this->bindValue[':' . $k] = $v;
                    }
                } else {
                    reset($v); //	将数组的内部指针指向第一个元素
                    $key = key($v); //从当前内部指针位置返回元素键名
                    switch ($key) {
                        case 'in':
                            $iArr = explode(',', $v['in']);
                            if (has_str($k, '.')) {
                                $arr = explode('.', $k);
                                $condition = " `{$arr[0]}`.`{$arr[1]}` IN (";
                                foreach ($iArr as $ik => $iv) {
                                    $condition .= $ik == 0 ? ":" . $arr[1] . $ik : ",:" . $arr[1] . $ik;
                                    $this->bindValue[':' . $arr[1] . $ik] = $iv;
                                }
                                $condition .= ")";
                            } else {
                                $condition = " `{$k}` IN (";
                                foreach ($iArr as $ik => $iv) {
                                    $condition .= $ik == 0 ? ":" . $k . $ik : ",:" . $k . $ik;
                                    $this->bindValue[':' . $k . $ik] = $iv;
                                }
                                $condition .= ")";
                            }
                            break;
                        case 'between':
                            $bArr = explode(',', $v['between']);
                            if (has_str($k, '.')) {
                                $arr = explode('.', $k);
                                $condition .= " ( `{$arr[0]}`.`{$arr[1]}` BETWEEN :{$arr[1]}_start AND :{$arr[1]}_end )";
                                $this->bindValue[':' . $arr[1] . '_start'] = $bArr[0];
                                $this->bindValue[':' . $arr[1] . '_end'] = $bArr[1];
                            } else {
                                $condition .= " ( `{$k}` BETWEEN :{$k}_start AND :{$k}_end )";
                                $this->bindValue[':' . $k . '_start'] = $bArr[0];
                                $this->bindValue[':' . $k . '_end'] = $bArr[1];
                            }
                            break;
                        case 'like':
                            if (has_str($k, '.')) {
                                $arr = explode('.', $k);
                                $condition .= " `{$arr[0]}`.`{$arr[1]}` LIKE :{$arr[1]}";
                                $this->bindValue[':' . $arr[1]] = $v['like'];
                            } else {
                                $condition .= " `{$k}` LIKE :{$k}";
                                $this->bindValue[':' . $k] = $v['like'];
                            }

                            break;
                        default:
                            break;
                    }
                }
                $this->where .= $i == 0 ? $condition : " AND " . $condition;
                ++$i;
            }
        }
        return $this;
    }

    /**
     * 字段筛选
     */
    public function field($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * 表连接
     */
    public function join($tableName, $on, $type = 'inner')
    {
        $t = strtoupper($type);
        $onArr = explode(',', $on);
        if (has_str($tableName, ' ')) {
            $tableNameArr = explode(' ', $tableName);
            $this->join .= ' ' . $t . ' JOIN ' . $tableNameArr[0] . ' AS ' . $tableNameArr[1] . ' ON ' . $onArr[0] . ' = ' . $onArr[1];
        } else {
            $this->join .= ' ' . $t . ' JOIN ' . $tableName . ' ON ' . $onArr[0] . ' = ' . $onArr[1];
        }
        return $this;
    }

    /**
     * 排序
     */
    public function orderBy($order)
    {
        $this->order = 'ORDER DY ' . $order;
        return $this;
    }

    /**
     * 获取单条数据
     */
    public function fetch()
    {
        $this->limit = ' LIMIT 1 ';
        $sql = 'SELECT ' . $this->field . ' FROM ' . $this->tableName . $this->join . $this->where . $this->order . $this->limit;
        return $this->query($sql, $this->bindValue)->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * 获取多条数据
     */
    public function fetchAll()
    {
        $sql = 'SELECT ' . $this->field . ' FROM ' . $this->tableName . $this->join . $this->where . $this->order;
        return $this->query($sql, $this->bindValue)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * 插入数据
     */
    public function insert($data)
    {
        $column = ' ( ';
        $value = ' ( ';
        $i = 0;
        foreach ($data as $k => $v) {
            $column .= $i == 0 ? $k : ',' . $k;
            $value .= $i == 0 ? ':' . $k : ',:' . $k;
            $this->bindValue[':' . $k] = $v;
            ++$i;
        }
        $column .= ' ) ';
        $value .= ' ) ';
        $sql = 'INSERT INTO ' . $this->tableName . $column . ' VALUES' . $value;
        $this->rowCount = $this->query($sql, $this->bindValue)->rowCount();
        $this->insertId = $this->con->lastInsertId();
        return  $this->rowCount != null ? true : false;
    }

    /**
     * 更新数据
     */
    public function update($data)
    {
        $str = ' ';
        $i = 0;
        foreach ($data as $k => $v) {
            $str .= $i == 0 ? $k . ' = :' . $k : ',' . $k . ' = :' . $k;
            $this->bindValue[':' . $k] = $v;
            ++$i;
        }
        $sql = 'UPDATE ' . $this->tableName . ' SET' . $str . $this->where;
        $this->rowCount = $this->query($sql, $this->bindValue)->rowCount();
        return  $this->rowCount != null ? true : false;
    }

    /**
     * 删除数据
     */
    public function delete()
    {
        $sql = 'DELETE FROM ' . $this->tableName . $this->where;
        $this->rowCount = $this->query($sql, $this->bindValue)->rowCount();
        return  $this->rowCount != null ? true : false;
    }

    /**
     * 执行sql
     */
    public function query($sql, $bind = [])
    {
        $statement = $this->con->prepare($sql); //采用预处理，防止sql注入
        $statement->execute($bind);
        //清空查询构造数据
        $this->field = '*'; //查询字段
        $this->join = ''; //表连接
        $this->where = ''; //条件
        $this->order = ''; //排序
        $this->limit = ''; //记录限定
        $this->bindValue = []; //参数绑定
        $this->rowCount = null; //影响的行数
        $this->insertId = null; //插入数据的id
        return $statement;
    }
}
