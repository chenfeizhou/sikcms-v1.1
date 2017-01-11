<?php

// +----------------------------------------------------------------------
// | 思科cms 后台公共调用模块
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Common\Controller\AdminBase;
use Think\Page;

class CommonController extends AdminBase {

    public function _empty() {
        header('location:' . C('ERROR_PAGE'));
        exit;
    }
   //获取分页
    public function getPage($count, $pagesize, $rewrite = '', $param_url = '') {
        $Page = new \Think\Page($count, $pagesize, '', $param_url);
        $Page->setConfig('header', '');
        $Page->setConfig('prev', "<i class='glyph-icon icon-chevron-left'></i>");
        $Page->setConfig('next', "<i class='glyph-icon icon-chevron-right'></i>");
        $Page->setConfig('first', '首页');
        $Page->setConfig('last', '末页');
        $Page->setConfig("%nowPage%/%totalPage% 页 %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end% %totalRow%");
        $page = $Page->showChouCms();
        return $page;
    }

}
