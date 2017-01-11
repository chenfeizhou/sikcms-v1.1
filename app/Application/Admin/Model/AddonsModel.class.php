<?php

// +----------------------------------------------------------------------
// | 思科cms 插件模型
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn  All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Model;

use Common\Model\Model;

class AddonsModel extends Model{
    
    /**
     * 插件列表
     */
    public function getList(){
       return  $this->limit(100)->select();
    }
    
    /**
     * 总数
     */
    public function getCount(){
        return $this->count();
    }
}