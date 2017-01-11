<?php

// +----------------------------------------------------------------------
// | 思科cms 后台公共模块
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Common\Controller\AdminBase;
use Admin\Service\User;

class PublicController extends AdminBase{
    
    //后台登录界面
    public function login(){
        if(User::getInstance()->userid){
            $this->redirect('Admin/Index/index');
        }
        $this->display();
    }
    
    //后台登录验证
    public function doLogin(){
     //记录失败者ID
        $ip = get_client_ip();
        $username = I("post.user_name","","trim");
        $password = I("post.password","","trim");
        if(empty($username) || empty($password)){
            $this->error('用户名和密码不能为空，请重新输入',U('Public/Login'));
        }
        if(User::getInstance()->login($username, $password)){
             $forward = cookie("forward");
            if (!$forward) {
                $forward = U("Admin/Index/index");
            } else {
                cookie("forward", NULL);
            }
           //增加用户登录行为
            $this->redirect('Index/index');
        }else{
            $this->error('用户名或密码错误，重新登录',U('Public/login'));
        }
    }
 
    //退出登录
    public function logout(){
       
        if(User::getInstance()->logout()){
            //手动登出，清空forward
            cookie("forward",NULL);
            $this->success("注销成功",U('Admin/Public/login'));
        }
    }
    
}
