<?php

namespace Common\Controller;

//定义后台
define('IN_ADMIN', true);

class AdminBase extends SikCMS {

    //初始化
    protected function _initialize() {
        //初始化菜单
        $this->getMenu();
        //权限验证
       C(array(
            "USER_AUTH_ON" => true, //是否开启权限认证
            "USER_AUTH_TYPE" => 1, //默认认证类型 1 登录认证 2 实时认证
            "REQUIRE_AUTH_MODULE" => "", //需要认证模块
            "NOT_AUTH_MODULE" => "Public", //无需认证模块
            "USER_AUTH_GATEWAY" => U("Admin/Public/login"), //登录地址
        ));
        
        //是否有权限
       if (false == \Admin\Service\Access::AccessDecision(MODULE_NAME)) {
            //检查是否登录
            if (false === \Admin\Service\Access::checkLogin()) {
                //跳转到登录界面
                redirect(C('USER_AUTH_GATEWAY'));
            }
            //没有操作权限
            $this->error('您没有操作此项的权限！');
        }
    }

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param string $message 提示信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    final public function success($message = '', $jumpUrl = '', $ajax = false) {
        D('Admin/Operationlog')->record($message, 1);
        parent::success($message, $jumpUrl, $ajax);
    }

    /**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param string $message 错误信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    final public function error($message = '', $jumpUrl = '', $ajax = false) {
       D('Admin/Operationlog')->record($message, 0);
        parent::error($message, $jumpUrl, $ajax);
    }

    /**
     * 获取菜单项
     */
    public function getMenu() {
        $menuid = $_GET['menuid'] ? (int) $_GET['menuid'] : 0;
        $menu_db = D('Admin/Menu')->getMenu();
        $data = $this->public_left($menuid);
        $this->assign('curpos', D('Admin/Menu')->currentPos($menuid));
        $this->assign('menuList', $menu_db);
        $this->assign('menuitem', $data);
    }

    /**
     * 
     * 左侧对应子菜单
     */
    public function public_left($menuid = 0) {
        if (IS_GET) {
            $menu_db = D('Admin/Menu');
            $data = array();
            $data = $menu_db->getTree($menuid);
        }
        return $data;
    }

}
