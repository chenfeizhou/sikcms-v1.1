<?php

// +----------------------------------------------------------------------
// | 思科cms 评论模块
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Common\Controller\AdminBase;

class CommentController extends AdminBase {

    //页码
    public $page = 1;
    //每页数
    public $rows = 10;

    public function _initialize() {
        parent::_initialize();
        $this->page = I('get.page', 1);
        $this->rows = C('LISTROWS');
    }

    /**
     * 评论列表
     */
    public function index() {
        $keyword = I('get.keyword', '');
        $channel_id = I('get.channel_id', '');
        $where = '';
        if (isset($keyword) && !empty($keyword)) {
            $where = 'content like "%' . $keyword . '%"';
        }
        if (isset($channel_id) && !empty($channel_id)) {
            $where .="channel_id =$channel_id ";
        }
        $comment_db = D('comment');
        $list = $comment_db->getCommentList($this->page);
        $page = (new CommonController())->getPage($comment_db->getCount($where), $this->rows);
        $this->assign('list', $list);
        $this->assign('page',$page);
        $this->display();
    }
    /**
     * 审核评论
     */
    public function commentPass(){
        $ids = I('post.ids');
        if(empty($ids)){
            $this->error('暂无数据');
        }
        $comment_db = D('comment');
        $res = $comment_db->where(array('id' => array('in',$ids)))->setField('is_audit','1');
        $res ? $this->success('审核成功') : $this->error('审核失败');
    }
    /**
     * 不通过评论
     */
    public function commentUnPass(){
          $ids = I('post.ids');
        if(empty($ids)){
            $this->error('暂无数据');
        }
        $comment_db = D('comment');
        $res = $comment_db->where(array('id' => array('in',$ids)))->setField('is_audit','0');
        $res ? $this->success('审核成功') : $this->error('审核失败');
    }

}
