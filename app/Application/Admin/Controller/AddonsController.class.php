<?php

// +----------------------------------------------------------------------
// | 思科cms 后台插件模块
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Common\Controller\AdminBase;
class AddonsController extends AdminBase{
      //页码
    public $page = 1;

    //初始化
    public function _initialize() {
        parent::_initialize();
        $this->page = I('get.page', 1);
         $this->rows = C('LISTROWS');
    }
   /**
    * 插件列表
    * 2留言插件 addon_bookguest
    * 3表单插件 addon_form
    */
    public function index(){
        $addons_db = D('addons');
        $list = $addons_db->getList();
        $page = (new CommonController())->getPage($addons_db->getCount(), $this->rows);
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->display();
    }
    
    /**
     * 开启/关闭
     */
    public function setStatus(){
        $id = I('get.id');
        $status = I('get.status');
        if(empty($id)){
            $this->error('参数错误!');
        }
        $addons_db = M('addons');
        if($addons_db->where(array('id'=>$id))->setField('status',$status=='true'?'false':'true')){
            $this->success('设置成功');
        }else{
            $this->error('设置失败');
        }
    }
    
 
    
}