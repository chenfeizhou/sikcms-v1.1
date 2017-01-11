<?php

// +----------------------------------------------------------------------
// | 快乐筹cms 后台软件模块
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.klchoucms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Common\Controller\AdminBase;
use Admin\Service\User;

class SoftController extends AdminBase {

    //页码
    public $page = 1;
    //每页数
    public $rows = 10;
    //软件状态码
    public $status = array();
    //模型标识
    public $nid ='soft'; 
    //模型ID
    public $mode_id=6;
    //是否为管理员操作标识
    public $isadmin = 1;

    //初始化
    public function _initialize() {
        parent::_initialize();
        $this->page = I('get.page', 1);
        $this->rows = C('LISTROWS');
        //文章状态对应文章表status字段
        $this->status = array(
            0 => array('text' => '草稿', 'color' => 'font-gray-dark'),
            1 => array('text' => '审核中', 'color' => 'font-yellow'),
            2 => array('text' => '审核通过', 'color' => 'font-green'),
            3 => array('text' => '回收站', 'color' => 'font-gray'));
    }

    /**
     * 列表
     */
    public function index() {
        $status = I('post.status', '');
        $channel_id = I('post.channel_id', '');
        $where = "";
        if (!empty($status) && $status != 'all') {
            $where['status'] = $status;
        }
        if (!empty($channel_id) && $channel_id != 'all') {
            $where['channel_id'] = array('eq', $channel_id);
        }
        $soft_db = D('Soft');
        $module_db = D('Module');
        //获取操作方法
        $action = $module_db->getInfoBynid($this->nid);
        $list = $soft_db->getSoftList($this->page, $where);
        $page = (new CommonController())->getPage($soft_db->getCount($where),$this->rows);
        $this->assign('action',$action);
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('status', $this->status);
        $this->display('list');
    }

    /**
     * 添加
     */
    public function add() {
        if (IS_POST) {
            $user = User::getInstance()->getInfo();
            $soft_db = D('Soft');
            $post = I('post.info');
            $data = array(
                'channel_id' => $post['channel_id'],
                'soft_name' => $post['soft_name'],
                'status' => $post['status'],
                'listorder' => $post['sort'],
                'download'=>$post['download'],
                'demo_url'=>$post['demo_url'],
                'thumb' => $post['thumb'],
                'introduce' => html_entity_decode($post['introduce']),
                'inputtime' => time(),
                'soft_size'=>$post['soft_size'],
                'operater_id'=>$user['userid'],
                'author'=>$user['username'],
                'isadmin'=>$this->isadmin,
                'updatetime' => time()
            );
            $res = $soft_db->add($data);
            $res ? $this->success('添加成功', U('Soft/index')) : $this->error('添加失败');
        }
        $channel_db = D('Channel');
        $channel = $channel_db->getChannelTree(0, 1, 1000,$this->mode_id);
        $this->assign('channel', $channel);
        $this->assign('flag', $this->flag);
        $this->assign('status', $this->status);
        $this->display();
    }

    /**
     * 编辑
     */
    public function edit() {
        $soft_db = D('soft');
        if (IS_POST) {
            $user = User::getInstance()->getInfo();
            $post = I('post.info');
            $data = array(
                'channel_id' => $post['channel_id'],
                'title' => $post['title'],
                'status' => $post['status'],
                'listorder' => $post['listorder'],
                'thumb' => $post['thumb'],
                'flag' => implode(',', $post['flag']),
                'introduce' => html_entity_decode($post['introduce']),
                'inputtime' => time(),
                'soft_size'=>$post['soft_size'],
                'operater_id'=>$user['userid'],
                'author'=>$user['username'],
                'isadmin'=>$this->isadmin,
                'updatetime' => time()
            );
            $res = $soft_db->where('soft_id=' . $post['id'])->save($data);
            if (!$res) {
                $this->error('修改失败');
            }
            $this->success('修改成功', U('Soft/index'));
        }
        $id = I('get.id');
        $info = $soft_db->getInfo($id);
        $channel_db = D('Channel');
        $channel = $channel_db->getChannelTree(0, 1, 1000,$this->mode_id);
        $this->assign('channel', $channel);
        $this->assign('id', $id);
        $this->assign('info', $info);
        $this->assign('status', $this->status);
        $this->display();
    }

    /**
     * 删除
     */
    public function delete() {
        $ids = I('post.ids', 0);
        $soft_db = D('soft');
        $res = $soft_db->where(array('soft_id' => array('in', $ids)))->delete();
        $res ? $this->success('删除成功') : $this->error('删除失败');
    }

}
