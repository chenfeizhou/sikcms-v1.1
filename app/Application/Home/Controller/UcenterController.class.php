<?php

namespace Home\Controller;

use Think\Controller;

/**
 * 个人中心控制器
 */
class UcenterController extends CommonController {

    //userid
    protected $userid = '';
    //指定可显示的文章栏目ID(bug反馈,闲话多说)
    protected $show_channel = array(1,2,3,4,5,6,7);
    //头像
    protected $heads = array('head1.png', 'head2.png', 'head3.png', 'head4.png', 'head5.png', 'head6.png');

    protected function _initialize() {
        parent::_initialize();
        if (!parent::userInfo()) {
            $this->error('请登录!', U('Member/login'));
        }
        $session = parent::userInfo();
        $this->userid = $session['userid'];
    }

    //个人中心主页
    public function index() {
        $member_db = D('Member');
        $user = $member_db->getInfo($this->userid);
        $this->assign('user', $user);
        $this->assign('heads', $this->heads);
        $this->display();
    }

    //文章
    public function articles() {
        $page = I('get.page', 1);
        $channel_id = I('get.channel_id',3);
        $rows = C('LISTROWS');
        $article_db = D('Articles');
        $channel_db = D('Channel');
        $where['userid'] = $this->userid;
        $where['channel_id'] = $channel_id;
        $channel_ids = array();
        if($items = $channel_db->getChannelByParentid($channel_id)){
             array_push($channel_ids, $channel_id);
          foreach($items as $k=>$v){
                array_push($channel_ids, $v['id']);
          }
          $where['channel_id'] =array('in', implode(',',$channel_ids));
        }
        $where['status']=2;
        $list = $article_db->getArticleList($page, $where);
        $count = $article_db->getCount($where);
        $page = (new CommonController())->getPage($count, $rows);
        $map['id'] = array('in', $this->show_channel);
        $channel = $channel_db->getChannel($map);
        $this->assign('channel', $channel);
        $this->assign('channel_id', $channel_id);
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->display('myarticle');
    }

    //修改
    public function updateUser() {
        $data = array(
            'header' => I('post.head_ico'),
            'nickname' => I('post.nickname'),
        );
        $m = M('member');
        if ($m->where(array('user_id' => $this->userid))->save($data) !== false) {
            $this->success('更新成功');
        } else {
            $this->error('更新失败');
        }
    }

    public function updatePassword() {
        $old_password = I('post.old_password');
        $password = I('post.password');
        $repassword = I('post.repassword');
        if (empty($old_password) || empty($password) || empty($repassword)) {
            $this->error('请填写必填项');
        }
        if ($password != $repassword) {
            $this->error('新密码和确认密码不相同');
        }
        $data = array('password' => md5($password));
        $m = M('member');
        if ($m->where(array('user_id' => $this->userid))->save($data)) {
            $this->success('更新成功');
        } else {
            $this->error('更新失败');
        }
    }

    //写文章
    public function add() {
        if (IS_POST) {
            $m = M('articles');
            $m2 = M('article_data');
            $channel_id = I('post.channel_id');
            $title = I('post.title');
            $content = I('post.content');
            $time = time();
            if (!in_array($channel_id, $this->show_channel)) {
                $this->error('该类目暂停发布信息');
            }
            $data = array(
                'channel_id' => $channel_id,
                'title' => $title,
                'username' => session('username'),
                'userid' => $this->userid,
                'inputtime' => $time,
                'updatetime' => $time,
                'status' => 2,
                'isadmin' => 0
            );
            $m->startTrans();
            if ($res = $m->add($data)) {
                $data2 = array(
                    'articles_id' => $res,
                    'content' => $content
                );
                if (!$m2->add($data2)) {
                    $this->error('内容添加失败');
                    $m->rollback();
                }
                $this->success('添加成功',U('Ucenter/articles'));
                $m->commit();
            } else {
                $this->error('添加信息失败');
                $m->rollback();
            }
        }
        $channel_db = D('Channel');
        $map['id'] = array('in', $this->show_channel);
        $channels = $channel_db->getChannelTree();
        $channel = array();
         //屏蔽不显示ID
        foreach($channels as $k=>$v){
            if(in_array($v['id'], $this->show_channel)){
                $channel[]=$v;
            }
        }
        $this->assign('channel', $channel);
        $this->display();
    }
    public function edit(){
         $m=D('Articles');
        if(IS_POST){
          $m2 = M('article_data');
          $channel_id = I('post.channel_id');
          $title = I('post.title');
          $id = I('post.id');
          $content = I('post.content');
          $time = time();
          if (!in_array($channel_id, $this->show_channel)) {
                $this->error('该类目暂停发布信息');
          }
           $data = array(
                'channel_id' => $channel_id,
                'title' => $title,
                'updatetime' => $time
           );
           $m->startTrans();
           $res = $m->where(array('id'=>$id,'userid'=>$this->userid))->save($data);
            if ($res!==false){ 
                $r = $m2->where(array('articles_id'=>$id))->setField('content',$content);
                if (!$r) {
                    $this->error('内容修改失败');
                    $m->rollback();
                }
                $this->success('修改成功',U('Ucenter/articles'));
                $m->commit();
            }else {
                $this->error('修改信息失败');
                $m->rollback();
            }
          
        }
        $id = I('get.id');
        $info = $m->getInfo($id);
        $channel_db = D('Channel');
        $channels = $channel_db->getChannelTree();
        $channel = array();
          //屏蔽不显示ID
        foreach($channels as $k=>$v){
            if(in_array($v['id'], $this->show_channel)){
                $channel[]=$v;
            }
        }
        $this->assign('info',$info);
        $this->assign('channel', $channel);
        $this->display();
    }
    
    public function delete(){
        $id = I('get.id');
        $m=D('Articles'); 
        $res = $m->where(array('id'=>$id,'userid'=>$this->userid))->delete();
        if($res){
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }

}
