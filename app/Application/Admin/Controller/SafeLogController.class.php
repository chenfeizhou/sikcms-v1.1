<?php

// +----------------------------------------------------------------------
// | 思科cms 后台安全记录日志模块
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Common\Controller\AdminBase;

class SafeLogController extends AdminBase {

    //登录日志
    public function index() {
        
    }

    //操作日志
    public function operationLog() {
        $page = I('get.page', 1);
        $rows = C('LISTROWS');
        $log_db = M('operationlog');
        $log_list = $log_db->page($page, $rows)->select();
        $page = (new CommonController())->getPage($log_db->count(), $rows);
        $this->assign('page', $page);
        $this->assign('list', $log_list);
        $this->display('operation_log');
    }
    //删除一个月前记录
    public function operateDelete(){
       $log_db = D('Operationlog');
       $res = $log_db->deleteAMonthago();
       $res?$this->success('删除成功'):$this->error('删除失败，已删');
       
    }

}
