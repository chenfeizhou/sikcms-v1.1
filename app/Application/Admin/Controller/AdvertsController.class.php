<?php

// +----------------------------------------------------------------------
// | 思科cms 后台广告模块
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Common\Controller\AdminBase;

class AdvertsController extends AdminBase {

    //页码
    public $page = 1;

    //初始化
    public function _initialize() {
        $this->page = I('get.page', 1);
    }

    /**
     * 添加广告位
     */
    public function addAdvert() {
        $advert_db = D('Adverts');
        $result = $advert_db->getAdvertsList($this->page);
        $this->assign('list', $result['list']);
        $this->display('advert_add');
    }

    /**
     * 广告位列表
     * @param int $page 页码
     */
    public function adverts() {
        $advert_db = D('Adverts');
        $result = $advert_db->getAdvertsList($this->page);
        $this->assign('list', $result['list']);
        $this->assign('page', $result['page']);
        $this->display('adverts');
    }

    /**
     * 广告位编辑
     */
    public function editAdvert() {
        $advert_db = D('Adverts');
        if (IS_POST) {
            $data = I('post.info');
            $res = $advert_db->save($data);
            $res ? $this->success('修改成功') : $this->error('修改失败');
        } else {
            $advert_id = I('get.advert_id');
            $info = $advert_db->where(array('advert_id' => $advert_id))->find();   
            $this->assign('info', $info);
            IS_AJAX ? $this->success($info) : $this->display('advert_edit');
        }
    }

    /**
     * 广告位删除
     * @param int $advert_id 广告位ID
     */
    public function deleteAdvert() {
        $advert_id = I('post.advert_id', 0);
        //该广告位否包含广告
        $adlist_db = D('adverts_list');
        if ($adlist_db->where(array('advert_id' => $advert_id))->count() > 0) {
            $this->error('请删除该广告位下的广告');
        }
        $advert_db = D('Adverts');
        $res = $advert_db->where(array('advert_id' => $advert_id))->delete();
        $res ? $this->success('删除成功') : $this->error('删除失败');
    }

    /**
     * 广告列表
     * @param int $page  页码
     */
    public function adlist() {
        $advert_db = D('Adverts');
        $result = $advert_db->getAdList($this->page);
        $this->assign('list', $result['list']);
        $this->assign('page', $result['page']);
        $this->display('adlist');
    }

    /**
     * 添加广告
     */
    public function addList() {
        if (IS_POST) {
            $adlist_db = M('adverts_list');
            $data = I('post.info');
            $data['ctime'] = time();
            $res = $adlist_db->add($data);
            $res ? $this->success('添加成功') : $this->error('添加失败');
        } else {
            $adv_list = D('Adverts')->getSelectTree();
            $this->assign('adv_list', $adv_list);
            $this->display('adlist_add');
        }
    }

    /**
     * 编辑广告
     */
    public function editList() {
        $adlist_db = M('adverts_list');
        if (IS_POST) {
            $data = I('post.info');
            $res = $adlist_db->save($data);
            $res ? $this->success('修改成功') : $this->error('修改失败');
        } else {
            $id = I('get.id');
            //广告位列表
            $adv_list = D('Adverts')->getSelectTree();
            $info = $adlist_db->where(array('id' => $id))->find();
            $this->assign('info', $info);
            $this->assign('adv_list', $adv_list);
            IS_AJAX ? $this->success($info) : $this->display('adlist_edit');
        }
    }

    /**
     * 删除广告
     */
    public function deleteList() {
        $id = I('post.ids');
        $where['id'] = array('in', $id);
        $adlist_db = M('adverts_list');
        $res = $adlist_db->where($where)->delete();
        $res ? $this->success('删除成功') : $this->error('删除失败');
    }

}
