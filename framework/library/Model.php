<?php

namespace framework\library;

abstract class Model
{

    protected $db = null;  //数据库连接对象

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
        $sql = 'SELECT ' . $this->db->field . ' FROM ' . $this->db->tableName . $this->db->join . $this->db->where . $this->db->order . ' LIMIT 1';
        return $this->db->query($sql, $this->db->bindValue)->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * 获取多条数据
     */
    public function fetchAll()
    {
        $sql = 'SELECT ' . $this->db->field . ' FROM ' . $this->db->tableName . $this->db->join . $this->db->where . $this->db->order;
        return $this->db->query($sql, $this->db->bindValue)->fetchAll(\PDO::FETCH_ASSOC);
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
            $this->db->bindValue[':' . $k] = $v;
            ++$i;
        }
        $column .= ' ) ';
        $value .= ' ) ';
        $sql = 'INSERT INTO ' . $this->db->tableName . $column . ' VALUES' . $value;
        $this->rowCount = $this->db->query($sql, $this->db->bindValue)->rowCount();
        $this->insertId = $this->db->insertId;
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
            $this->db->bindValue[':' . $k] = $v;
            ++$i;
        }
        $sql = 'UPDATE ' . $this->db->tableName . ' SET' . $str . $this->db->where;
        $this->rowCount = $this->db->query($sql, $this->db->bindValue)->rowCount();
        return  $this->rowCount != null ? true : false;
    }

    /**
     * 删除数据
     */
    public function delete()
    {
        $sql = 'DELETE FROM ' . $this->db->tableName . $this->db->where;
        $this->rowCount = $this->db->query($sql, $this->db->bindValue)->rowCount();
        return  $this->rowCount != null ? true : false;
    }
}
