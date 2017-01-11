<?php

// +----------------------------------------------------------------------
// | 思科cms 后台菜单模块
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Common\Controller\AdminBase;

class MenuController extends AdminBase {

    /**
     * 后台菜单列表
     */
    public function menu() {
        $menu_db = D('Menu');
        if (IS_POST) {
            $id = I('post.id', 0);
        } else {
            $page = I('get.page', 1);
            $id = I('get.id', 0);
        }
        $where = array('pid' => $id);
        $rows = C('LISTROWS');
        $order = array('sort' => 'asc');
        $total = $menu_db->where($where)->count();
        $list = $menu_db->where($where)->order($order)->page($page, $rows)->select();
        $page = (new CommonController())->getPage($total, $rows);
        $this->assign('list', $list);
        $this->assign('page', $page);
        IS_AJAX ? $this->success($list) : $this->display();
    }

    /**
     * 后台子菜单加载
     */
    public function sub_menu() {
        $menu_db = D('Menu');
        $id = I('post.id', 0);
        $where = array('pid' => $id);
        $order = array('sort' => 'asc');
        $list = $menu_db->where($where)->order($order)->select();
        $this->assign('list', $list);
        !empty($list) ? $this->success($this->fetch('sub_menu')) : $this->error($this->fetch('sub_menu'));
    }

    /**
     * 菜单下拉框
     */
    public function public_menu_select() {
        $menu_db = D('Menu');
        $data = $menu_db->getSelectTree();
        $data = array(0 => array('id' => 0, 'name' => '作为一级菜单', 'children' => $data));
        $this->assign('menulist', $data);
        $this->success($this->fetch('public_menu_select'));
        //$this->ajaxReturn($data);
    }

    /**
     * 菜单添加
     */
    public function addMenu() {
        if (IS_POST) {
            $menu_db = D('Menu');
            $data = I('post.info');
            //菜单级别
            if ($data['pid'] > 0) {
                $level = $menu_db->where(array('id' => $data['pid']))->getField('level');
                $data['level'] = $level + 1;
            } else {
                $data['level'] = 1;
            }
            $res = $menu_db->add($data);
            $res ? $this->success('添加成功') : $this->error('添加失败');
        } else {
            $this->display('menu_add');
        }
    }

    /**
     * 菜单编辑
     */
    public function editMenu() {
        $menu_db = D('Menu');
        if (IS_POST) {
            $data = I('post.info');
            //菜单级别
            if ($data['pid'] > 0) {
                $level = $menu_db->where(array('id' => $data['pid']))->getField('level');
                $data['level'] = $level + 1;
            } else {
                $data['level'] = 1;
            }

            //上级菜单验证
            if (!$menu_db->checkParentId($data['id'], $data['pid'])) {
                $this->error('上级菜单设置失败');
            }
            $res = $menu_db->save($data);
            if ($res)
                $menu_db->setSonLevel($data['level'], $data['id']);

            $res ? $this->success('修改成功') : $this->error('修改失败');
        }
        $id = I('get.id');
        $info = $menu_db->where(array('id' => $id))->find();
        $this->assign('info', $info);
        IS_AJAX ? $this->success($info) : $this->display('menu_edit');
    }

    /**
     * 菜单删除
     */
    public function deleteMenu() {
        if (IS_POST) {
            $ids = I('post.ids');
            $menu_db = D('Menu');
            $res = $menu_db->where(array('id' => array('in', $ids)))->delete();
            if ($res)
                $menu_db->deleteSonMenu($ids);

            $res ? $this->success('删除成功') : $this->error('删除失败');
        }
    }

}
