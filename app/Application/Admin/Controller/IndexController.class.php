<?php

// +----------------------------------------------------------------------
// | 思科cms 后台首页
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Common\Controller\AdminBase;
use Admin\Service\User;

class IndexController extends AdminBase {

    //后台首页
    public function index() {
        $this->assign('userInfo', User::getInstance()->getInfo());
        $this->assign("role_name", D('Admin/Role')->getRoleIdName(User::getInstance()->role_id));
        $this->display();
    }

    public function main() {
        //服务器信息
        $info = array(
            '操作系统' => PHP_OS,
            '运行环境' => $_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式' => php_sapi_name(),
            'MYSQL版本' => mysql_get_server_info(),
            '产品名称' => '<font color="#FF0000">' . C('SYSTEM_NAME') . '</font>' . "&nbsp;&nbsp;&nbsp; [<a href='http://www.sikcms.cn' target='_blank'>访问官网</a>]",
            '用户类型' => '<font color="#FF0000" id="server_license">获取中...</font>',
            '产品版本' => '<font color="#FF0000">' . C('SIKCMS_VERSION') . '</font>，最新版本：<font id="server_version">获取中...</font>',
            '产品流水号' => '<font color="#FF0000">' . C('SIKCMS_BUILD') . '</font>，最新流水号：<font id="server_build">获取中...</font>',
            '上传附件限制' => ini_get('upload_max_filesize'),
            '执行时间限制' => ini_get('max_execution_time') . "秒",
            '剩余空间' => round((@disk_free_space(".") / (1024 * 1024)), 2) . 'M',
        );
        $this->assign('server_info',$info);
        $this->display('Common/main');
    }
    
    //系统设置
    public function setting(){
        if(IS_POST){
            $set = I();
            foreach($set as $k=>$v){
                M('config')->where(array('valuename'=>$k))->setField('value',$v);
            }
            $this->success('修改成功', U('Index/setting'));
        }
        $conf_db = D('Config');
        $set_list = $conf_db->getSetList();
        $this->assign('set_list',$set_list);
        $this->display('setting');
    }
   
    //获取版本
    public function version(){
        //获取官网版本
        $http ="http://www.sikcms.cn/admin.php?m=Admin&c=Index&a=version";
        //当前版本
        $version = floatval(I('get.version'));
        //官网版本
        $online_version = C('');
        
        
    }

}
