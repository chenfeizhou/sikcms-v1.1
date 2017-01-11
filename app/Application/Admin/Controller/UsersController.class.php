<?php

// +----------------------------------------------------------------------
// | 思科cms 后台用户模块
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Common\Controller\AdminBase;
use Admin\Service\User;

class UsersController extends AdminBase {

    /**
     * 用户列表
     */
    public function user() {
        $page = I('get.page', 1);
        $where = array();
        $rows = C('LISTROWS');
        //角色列表
        $roleList = M('Role')->getField('id,name,status', true);
        $combox = array();
        foreach ($roleList as $info) {
            array_push($combox, array(
                'value' => $info['id'],
                'text' => $info['name'],
            ));
        }
        $this->assign('combox', $combox);
        $user_db = D('user');
        $count = $user_db->where($where)->count();
        $user = $user_db->where($where)->page($page, $rows)->order(array('userid' => 'DESC'))->select();
        foreach ($user as $k => $val) {
            $user[$k]['roleName'] = isset($roleList[$val['roleid']]) ? ($roleList[$val['roleid']]['status'] ? $roleList[$val['roleid']]['name'] : '<font color="grey">' . $roleList[$val['roleid']]['name'] . '[冻结]</font>') : '<font color="red">未设置角色</font>';
        }
        $page = (new CommonController())->getPage($count, $rows);
        $this->assign('userList', $user);
        $this->assign('page', $page);
        $this->display('user');
    }

    /**
     * 用户添加
     */
    public function userAdd() {
        if (IS_POST) {
            $user_db = D('user');
            $data = I('post.info');
            if ($user_db->where(array('username' => $data['username']))->count()) {
                $this->error('用户名称已经存在');
            }
            //邮件模版
            $email_db = M('email');
            $email = $email_db->field(array('subject', 'content'))->where(array('code' => 'user.useradd'))->find();
            if ($email) {
                $email = array_merge($email, array(
                    'email' => $data['email'],
                    'content' => str_replace(array('{username}', '{password}', '{site}'), array($data['username'], $data['password'], C('SITE_URL')), htmlspecialchars_decode($email['content']))
                ));
            }
            //密码加密
            $data['password'] = md5($data['password'] . md5($data['verify']));
            $data['encrypt'] = 'md5';
            $id = $user_db->add($data);
            if ($id) {
                if ($email)
                    sendEmail($email['email'], $email['subject'], $email['content'], array('isHtml' => true, 'charset' => 'UTF-8'));
                $this->success('添加成功');
            }else {
                $this->error('添加失败');
            }
        } else {
            $role_db = D('Role');
            $rolelist = $role_db->where(array('status' => 1))->order('listorder asc')->getField('id,name', true);
            $this->assign('rolelist', $rolelist);
            IS_AJAX ? $this->success($rolelist) : $this->display('user_add');
        }
    }

    /**
     * 用户编辑
     */
    public function userEdit() {
        $user_db = D('User');
        if (IS_POST) {
            $data = I('post.info');
            if ($data['userid'] == '1' && $data['roleid'] != '1')
                $this->error('默认用户角色不能被修改');
            $res = $user_db->save($data);
            $res ? $this->success('修改成功') : $this->error('修改失败');
        }else {
            $id = I('get.id');
            $info = $user_db->where(array('userid' => $id))->find();
            $this->assign('info', $info);
            $role_db = D('Role');
            $rolelist = $role_db->where(array('status' => '1'))->order('listorder asc')->getField('id,name', true);
            $this->assign('rolelist', $rolelist);
            IS_AJAX ? $this->success(array('rolelist' => $rolelist, 'info' => $info)) : $this->display('user_edit');
        }
    }

    /**
     * 修改密码
     */
    public function changePassword() {
        $user_db = D('User');
        if (!$userid = User::getInstance()->isLogin()) {
                $this->error('请登录');
         }
        if (IS_POST) {
            $data = I('post.info');
            if (empty($data['oldpwd'])) {
                $this->error('请输入原始密码!');
            }
            if ($data['newpwd'] != $data['renewpwd']) {
                $this->error('两次输入的密码不相同!');
            }
            if (D('Admin/User')->changePassword($userid, $data['newpwd'], $data['oldpwd'])) {
              //退出登陆
                User::getInstance()->logout();
                $this->success('密码已修改,请重新登陆!', U("Admin/Public/login"));
            } else {
                $error = D('Admin/User')->getError();
                $this->error($error ? $error : '密码更新失败!');
            }
            $res = $user_db->save($data);
            $res ? $this->success('修改成功') : $this->error('修改失败');
        } else {
            $info = $user_db->where(array('userid' => $userid))->find();
            $this->assign('info', $info);
            $this->display('change_password');
        }
    }

    /**
     * 用户删除
     */
    public function userDelete() {
        $user_db = D('User');
        $userid = I('get.id');
        $res = $user_db->where(array('userid' => $userid))->delete();
        $res ? $this->success('删除成功') : $this->error('删除失败');
    }

}
