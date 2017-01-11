<?php

namespace Home\Controller;

use Think\Controller;

class CommentController extends CommonController {

    //页码
    public $page = 1;
    //显示页数
    public $rows = 5;

    //初始化
    public function _initialize() {
        $this->page = I('get.page', 1);
    }

    /**
     * 根据文档ID获取评论列表
     * @param int $relation_id 关联文档ID
     * @param int $channel_id 关联类目ID
     */
    public function getlistById($relation_id, $channel_id, $page = null) {
        if (!$relation_id || !$channel_id) {
            return array();
        }
        $comment_db = D('Comment');
        $where['relation_id'] = $relation_id;
        $where['channel_id'] = $channel_id;
        $where['is_audit'] = '1';
        $data = $comment_db->getList($page ? $page : $this->page, $where, "*", 'ctime desc', $this->rows);
        return $data;
    }

    /**
     * 根据文档id获取评论总数
     */
    public function getCommentCount($relation_id, $channel_id) {
        if (!$relation_id || !$channel_id) {
            return 0;
        }
        $comment_db = D('Comment');
        return $comment_db->getCount($relation_id, $channel_id);
    }

    /**
     * 评论添加
     * 文章评论数
     */
    public function addComment() {
        if (!parent::userInfo()) {
            $this->error('请先登录');
        }
        $member_db = D('Member');
        $comment_db = M('comment');
        $data['channel_id'] = I('post.channel_id');
        $data['relation_id'] = I('post.relation_id');
        $data['content'] = htmlspecialchars(I('post.content'));
        $data['content'] = preg_replace('/@(.*)(:)/i', '', $data['content']);
        $data['reply_userid'] = I('post.reply_userid', '');
        $data['pid'] = I('post.pid');
        $data['ip'] = get_client_ip();
        $data['nickname'] = session('nickname');
        $data['username'] = session('username');
        $data['userid'] = session('user_id');
        $data['ctime'] = time();
        $data['is_audit'] = '1';
        if ($data['reply_userid'] == session('user_id')) {
            $this->error('不能跟自己评论');
        }
        $user = $member_db->getInfo(session('user_id'));
        if (!$user) {
            $this->error('用户异常');
        }
        if ($data['reply_userid']) {
            $ruser = $member_db->getInfo($data['reply_userid']);
            $data['reply_username'] = $ruser['nickname'];
        }
        $data['user_header'] = $user['header'];
        if (!$data['channel_id'] || !$data['relation_id']) {
            $this->error('post data is not exit');
        }
        if (!$data['content']) {
            $this->error('评论不能为空');
        }
        if (!strlen($data['content']) > 50) {
            $this->error('评论内容最多为50字');
        }
        //1小时内最多评论5次
        $time = time();
        $map['userid'] = session('user_id');
        $map['ctime'] = array('gt', $time - 60);
        $map['relation_id'] = $data['relation_id'];
        $min_count = $comment_db->where($map)->count();
        if ($min_count >= 1) {
            $this->error('评论一分钟只能发布一次哦');
        }
        if ($res = $comment_db->add($data)) {
            if ($data['pid']) {
                //回复数增加
                $comment_db->where(array('id' => $data['pid']))->setInc('reply_nums', 1);
            }
            $this->success('评论成功,刷新查看');
        } else {
            $this->error('评论失败');
        }
    }
 //无刷新加载评论列表
    public function ajaxComment($relation_id, $channel_id,$page) {
          $relation_id=I('post.relation_id');
          $channel_id=I('post.channel_id');
          $page=I('post.page');
          $comment_list =  $this->getlistById($relation_id, $channel_id, $page);   
          if(empty($comment_list)){
              $this->error('没有更多了');
          }
          $this->assign('page',$page);
          $this->assign('comment_list',$comment_list);
          $data =$this->fetch('Public/sub_comment');
          $this->success($data, array('page' => $page+1));
    }

}
