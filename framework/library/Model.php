<?php

namespace framework\library;

abstract class Model
{

    protected $db = null;  //数据库连接对象

    public $tableName = ''; //表名

    public $field = '*'; //查询字段

    public $join = ''; //表连接

    public $where = ''; //条件

    public $order = ''; //排序

    public $limit = ''; //记录限定

    public $bindValue = []; //参数绑定

    public $rowCount = null; //影响的行数

    public function __construct()
    {
        // static::class 也可用get_called_class()
        // 把子类名转化成表名
        $str = strrchr(static::class, '\\');
        $str = str_replace('\\', '', $str);
        $str = str_replace('Model', '', $str);
        $tableName = strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $str));
        $this->db = Db::connect(); //连接数据库
        $prefix = Config::get('database.prefix');
        $this->tableName = '`' . $prefix . $tableName . '`'; //自动设置表名
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
        $this->field = '';
        if (has_str($field, '.')) {
            $arr = explode(',', $field);
            $arrLength = count($arr);
            foreach ($arr as $k => $v) {
                $arrV = explode('.', $v);
                $this->field .= '`' . trim($arrV[0]) . '`.';
                if (has_str($arrV[1], ' as ') || has_str($arrV[1], ' AS ') || has_str($arrV[1], ' As ') || has_str($arrV[1], ' aS ')) {
                    $arrV1 = explode(' ', $arrV[1]);
                    $this->field .= '`' . $arrV1[0] . '` AS `' . $arrV1[2] . '`';
                } else {
                    $this->field .= trim($arrV[1]) == '*' ? trim($arrV[1]) : '`' . trim($arrV[1]) . '`';
                }
                if ($k + 1 < $arrLength) {
                    $this->field .= ',';
                }
            }
        } else {
            $this->field = $field;
        }
        return $this;
    }

    /**
     * 表连接
     */
    public function join($tableName, $on, $type = 'inner')
    {
        $prefix = Config::get('database.prefix');
        $t = strtoupper($type);
        $onArr = explode(',', $on);
        $onArr0 = explode('.',  $onArr[0]);
        $onArr1 = explode('.',  $onArr[1]);
        if (has_str($tableName, ' ')) {
            $tableNameArr = explode(' ', $tableName);
            $this->join .= ' ' . $t . ' JOIN `' . $prefix . $tableNameArr[0] . '` AS `' . $tableNameArr[1] . '` ON `' . $onArr0[0] . '`.`' . $onArr0[1] . '` = `' . $onArr1[0] . '`.`' . $onArr1[1] . '`';
        } else {
            $this->join .= ' ' . $t . ' JOIN `' . $prefix . $tableName . '` ON `' . $prefix . $onArr0[0] . '`.`' . $onArr0[1] . '` = `' . $prefix . $onArr1[0] . '`.`' . $onArr1[1] . '`';
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
        $this->clearPar();
        return $this->db->query($sql, $bind);
    }

    /**
     * 返回最后插入行的ID或序列值
     */
    public function lastInsertId(){
        return $this->db->lastInsertId();
    }


    /**
     * 清空查询构造数据
     */
    private function clearPar()
    {
        $this->field = '*'; //查询字段
        $this->join = ''; //表连接
        $this->where = ''; //条件
        $this->order = ''; //排序
        $this->limit = ''; //记录限定
        $this->bindValue = []; //参数绑定
    }
}
