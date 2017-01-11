<?php

// +----------------------------------------------------------------------
// | 思科cms 后台用户角色模块
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Common\Controller\AdminBase;
use Admin\Model\RoleModel;
use Admin\Controller\CommonController;

class RoleController extends AdminBase {

    /**
     * 角色管理
     */
    public function role() {
        $page = I('get.page', 1);
        $rows = C('LISTROWS');
        $role_db = new RoleModel();
        $role_list = $role_db->getTreeArray($page, $rows);
        $page = (new CommonController())->getPage($role_db->count(), $rows);
        $this->assign('list', $role_list);
        $this->assign('page', $page);
        $this->display();
    }

    /**
     * 角色添加
     */
    public function roleAdd() {
        if (IS_POST) {
            $role_db = D('Role');
            $data = I('post.info');
            if ($role_db->where(array('name' => $data['name']))->count()) {
                $this->error('角色名称已存在');
            }
            $res = $role_db->add($data);
            $res ? $this->success('添加成功') : $this->error('添加失败');
        } else {
            $this->display('role_add');
        }
    }

    /**
     * 角色编辑
     */
    public function roleEdit() {
        $role_db = D('Role');
        if (IS_POST) {
            $data = I('post.info');
            if ($data['id'] == '1' && $data['status'] != '1')
                $this->error('系统默认角色不能被禁用');
            $res = $role_db->save($data);
            $res ? $this->success('修改成功') : $this->error('修改失败');
        } else {
            $roleid = I('get.id');
            $info = $role_db->where(array('id' => $roleid))->find();
            $this->assign('info', $info);
            IS_AJAX ? $this->success($info) : $this->display('role_edit');
        }
    }

    /**
     * 角色删除
     */
    public function roleDelete() {
        $role_db = D('Role');
        $roleid = I('get.id');
        $res = $role_db->where(array('id' => $roleid))->delete();
        $res !== false ? $this->success('删除成功') : $this->error('删除失败');
    }

    /**
     * 角色权限
     */
    public function roleAccess($id = 0) {
        $menu_db = D('Menu');
        if (IS_POST) {
            $access_db = M('access');
            $ids = array_unique(I('post.ids'));
            $access_db->where(array('role_id' => $id))->delete(); //清除旧数据
            //添加新数据
            if (!empty($ids)) {
                $menuList = $menu_db->where(array('id' => array('in', $ids)))->getField('id,controller,action,level', true);
                foreach ($ids as $i) {
                    $access_db->add(array(
                                'role_id'=>$id,
                                'menu_id'=>$menuList[$i]['id'],
                                'level'=>$menuList[$i]['level']
                            ) );
                }
            }
            $this->success('权限设置成功');
        }else{
            $data = $menu_db->getRoleTree(0,$id);
            $this->assign('roleid',$id);
            $this->assign('data',$data);
            $this->display("role_access");
        }
    }
    
    

    /**
     * 检测角色名称是否存在
     */
    public function checkRole() {
    
    }

}
