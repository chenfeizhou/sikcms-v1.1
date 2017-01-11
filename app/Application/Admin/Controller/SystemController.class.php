<?php
// +----------------------------------------------------------------------
// | 思科cms 后台系统模块
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Common\Controller\AdminBase;

class SystemController extends AdminBase{
    
    /**
     * 菜单管理
     */
    public function menu(){
        $this->dispay();
    }
}