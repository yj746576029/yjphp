<?php

namespace framework\library;

abstract class Model
{

    protected $db = null;  //数据库连接对象
    
    public $rowCount = null; //影响的行数

    public $insertId = null; //插入数据的id

    public function __construct()
    {
        // static::class 也可用get_called_class()
        // 把子类名转化成表名
        $str = strrchr(static::class, '\\');
        $str = str_replace('\\', '', $str);
        $str = str_replace('Model', '', $str);
        $tableName = strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $str));
        $this->db = Db::connect(); //连接数据库
        $this->db->table($tableName); //自动设置表名
    }

    /**
     * 表名
     */
    public function table($tableName)
    {
        $this->db->table($tableName);
        return $this;
    }

    /**
     * where 条件构造
     */
    public function where(array $where = [])
    {
        $this->db->where($where);
        return $this;
    }

    /**
     * 字段筛选
     */
    public function field($field)
    {
        $this->db->field($field);
        return $this;
    }

    /**
     * 表连接
     */
    public function join($tableName, $on, $type = 'inner')
    {
        $this->db->join($tableName, $on, $type);
        return $this;
    }

    /**
     * 排序
     */
    public function orderBy($order)
    {
        $this->db->orderBy($order);
        return $this;
    }

    /**
     * 获取单条数据
     */
    public function fetch()
    {
        return $this->db->fetch();
    }

    /**
     * 获取多条数据
     */
    public function fetchAll()
    {
        return $this->db->fetchAll();
    }

    /**
     * 插入数据
     */
    public function insert($data)
    {
        $res=$this->db->insert($data);
        $this->rowCount = $this->db->rowCount;
        $this->insertId = $this->db->insertId;
        return $res;
    }

    /**
     * 更新数据
     */
    public function update($data)
    {
        $res=$this->db->update($data);
        $this->rowCount = $this->db->rowCount;
        return  $res;
    }

    /**
     * 删除数据
     */
    public function delete()
    {
        $res=$this->db->delete();
        $this->rowCount = $this->db->rowCount;
        return  $res;
    }
}
