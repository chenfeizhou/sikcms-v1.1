<?php

// +----------------------------------------------------------------------
// | 思科cms  频道模型
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Model;

use Common\Model\Model;

class ModuleModel extends Model {

    
    /**
     * 获取模型信息
     */
    public function getInfo($id){
        if(!$id){
            return null;
        }
        return $this->where("id=$id")->find();
    }
    
    
     /**
     * 获取模型信息
     */
    public function getInfoBynid($nid){
        if(!$nid){
            return null;
        }
        return $this->where("nid='$nid'")->find();
    }
    
    
    /**
     * 列表获取
     */
    public function getList($field = "*", $page = 1, $rows = 20) {
       return $this->field($field)->page($page, $rows)->select();
     
    }

    /**
     * 模型对应的模板
     * @apram int $module_id 模型ID
     */
    public function getTemplate($module_id) {
        if (!$module_id) {
            return null;
        }
        $template_db = M('template');
        return  $template_db->where("module_id=$module_id")->find();
    }

    /**
     * 总数
     */
    public function getCount() {
        return $this->count();
    }

}
