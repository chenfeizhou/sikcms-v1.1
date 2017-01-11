<?php


// +----------------------------------------------------------------------
// | 思科cms 公共模型
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Common\Model;

class Model extends \Think\Model{
    
    //是否新增
    private $isNewRecord = false;
    
    /**
     * 新增数据
     * @access public
     * @param mixed $data 数据
     * @param array $options 表达式
     * @param  boolean $replace 是否replace
     * @reutrn mixed
     */
    public function add($data='',$options = array(),$replace =false){
        $this->isNewRecord = true;
        return parent::add($data,$options,$replace);
    }
    public  function addAll($dataList, $options = array(), $replace = false) {
       return  parent::addAll($dataList, $options, $replace);
    }
    /**
     * 对保存到数据库的数据进行处理
     * 注意:由于建表不规范，可能一些字段设置不允许NULL，然后又没有设置默认值
     *      所以对_facade进行了复写，支持这类操作
     * @access protected
     * @param mixed $data 要操作的数据
     * @return boolean
     */
    protected function _facade($data) {
        // 检查数据字段合法性
        if(!empty($this->fields)) {
            if(!empty($this->options['field'])) {
                $fields =   $this->options['field'];
                unset($this->options['field']);
                if(is_string($fields)) {
                    $fields =   explode(',',$fields);
                }    
            }else{
                $fields =   $this->fields;
            }        
            foreach ($data as $key=>$val){
                if(!in_array($key,$fields,true)){
                    if(!empty($this->options['strict'])){
                        E(L('_DATA_TYPE_INVALID_').':['.$key.'=>'.$val.']');
                    }
                    unset($data[$key]);
                }elseif(is_scalar($val)) {
                    // 字段类型检查 和 强制转换
                    $this->_parseType($data,$key);
                }
            }
        }
       
        // 安全过滤
        if(!empty($this->options['filter'])) {
            $data = array_map($this->options['filter'],$data);
            unset($this->options['filter']);
        }
        $this->_before_write($data);
        return $data;
    }
    
    /**
     * 删除表（慎重）
     * @param string $tablename 不带表前缀的表面
     * @return type
     */
    public function delete_table($tablename){
        $tablename = C("DB_PREFIX").$tablename;
        return $this->query("DROP TABLE {$tablename}");
    }
    /**
     * 读取全部表明
     * @return type
     */
    public function list_tables(){
        $tables = array();
        $data = $this->query("SHOW TABLES");
        foreach ($data as $k=>$v){
            $tables[]=$v['tables_in_'.C('DB_NAME')];
        }
        return $tables;
    }
    /**
     * 检查表是否存在
     * $table 不带表前缀
     */
    public function table_exists($table){
        $tables = $this->list_tables();
        return in_array(C('DB_PREFIX').$table, $tables);
    }
    /**
     * 获取表字段
     * $table 不代表前缀
     */
    public function gt_fields($table){
        $fields = array();
        $table = C('DB_PREFIX').$table;
        $data = $this->query("SHOW COLUMNS FORM $table");
        foreach($data as $v){
            $fields[$v['field']]=$v['type'];
        }
        return $fields;
    }
    /**
     * 检查字段是否存在
     * $table 不带表前缀
     */
    public function field_exists($table, $field) {
        $fields = $this->get_fields($table);
        return array_key_exists($field, $fields);
    }
    
}