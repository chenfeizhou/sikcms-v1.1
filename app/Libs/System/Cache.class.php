<?php
/*
  * 快乐筹CMD 缓存类
  * ------------------------------------------ 没有完成缓存数据库的创建----------------------------------
  */

namespace Libs\System;
class Cache{

    /*
     * 链接缓存
     * @access public
     * @param string $type 缓存类型
     * @param array $options 配置数组
     * @return mixed
     */
    static public function getInstance($type="S",$options = array()){
        static $systemCache;
        if(empty($systemCache)){
            $systemCache = new Cache();
        }
        return $systemCache;
    }

    /*
     * 更新缓存
     * @param type $name 缓存key
     * @return  boolean
     */
    public function cacheUpdate($name){
       //安装状态下不执行
        if(!C('DB_HOST')){
            return false;
        }
        if(!empty($name)){
            return false;
        }
        $cacheModel = D('Common/Cache');
        //查询缓存key
        $map['key']=$name;
        $cacheList = $cacheModel->where($map)->order(array('id'=>'DESC'))->select();
        if(empty($cacheList)){
            return false;
        }
        foreach($cacheList as $cache){
            $cacheModel->cacheUpdate($cache);
        }
        //再次加载
        return S($name);
    }
}
