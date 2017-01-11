<?php

// +----------------------------------------------------------------------
// | 快乐筹cms 后台栏目模块
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.klchoucms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Common\Controller\AdminBase;
use Admin\Model\ChannelModel;
use Admin\Model\ModuleModel;
use Admin\Model\ArticlesModel;

class ChannelController extends AdminBase {

    //页码
    public $page = 1;
    //每页数
    public $rows = 10;

    //初始化
    public function _initialize() {
        parent::_initialize();
        $this->page = I('get.page', 1);
        $this->rows = C('LISTROWS');
    }

    /**
     * 栏目列表
     */
    public function index() {
        $channel_db = new ChannelModel();
        $module_db = new ModuleModel();
        $list = $channel_db->getChannelTree(0, $this->page, $this->rows);
        $page = (new CommonController())->getPage($channel_db->getCount(0), $this->rows);
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->display('list');
    }

    /**
     * 栏目添加
     */
    public function add() {
        $channel_db = D('Channel');
        $module_db = D('Module');
        if (IS_POST) {
            $data = I('post.info');
            $data['updatetime']=time();
            $data['content']= html_entity_decode($data['content']);
            if (!$channel_db->create($data)) {
                $this->error($channel_db->getError());
            }
            $res = $channel_db->add();
            $res ? $this->success('添加成功', U('Channel/index')) : $this->error('添加失败');
        }
        $cid = I('get.cid', '');
        $module = $module_db->getList();
        $info = $channel_db->where(array('id' => $cid))->find();
        $this->assign('module', $module);
        $this->assign('info', $info);
        $this->assign('cid', $cid);
        $this->display('add');
    }

    /**
     * 获取模型对应模板
     */
    public function getTemplate() {
        $mode_id = (int) $_GET['id'];
        $mode_db = new ModuleModel();
        $list = $mode_db->getTemplate($mode_id);
        $this->ajaxReturn($list);
    }

    /**
     * 栏目编辑
     */
    public function edit() {
        $channel_db = D('Channel');
        $module_db = D('Module');
        if (IS_POST) {
            $data = I('post.info');
            $data['updatetime']=time();
            $data['content']= html_entity_decode($data['content']);
             if (!$channel_db->create($data)) {
                $this->error($channel_db->getError());
            }
            $res = $channel_db->save($data);
            $res ? $this->success('修改成功', U('Channel/index')) : $this->error('修改失败');
        }
        $id = I('get.id');
        $module = $module_db->getList();
        $info = $channel_db->where(array('id' => $id))->find();
        $this->assign('module',$module);
        $this->assign('info', $info);
        $this->display();
    }

    /**
     * 栏目删除
     */
    public function delete() {
        $ids = I('post.ids', 0);
        $articles_db = D('articles');
        //栏目下是否有文章列表
        if ($articles_db->where(array('channel_id' => array('in', $ids)))->count() >= count($ids)) {
            $this->error('栏目下包含文章，请优先删除');
        }
        $channel_db = D('Channel');
        $res = $channel_db->where(array('id' => array('in', $ids)))->delete();
        $res ? $this->success('删除成功') : $this->error('删除失败');
    }

}
