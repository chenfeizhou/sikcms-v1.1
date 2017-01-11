<?php

namespace Home\Controller;
use Think\Controller;
//use Home\Controller\CommonController;
class ArticleController extends CommonController {

    /**
     * 频道列表
     */
    public function lists(){
        $cid = I('get.cid',0);
        $page = I('get.page',1);
        $rows = C('LISTROWS');
        $article_db = D('Articles');
        $where=null;
        if($cid){
          $where = "channel_id = $cid";
        }
        $list = $article_db->getArticleList($page,$where);
        $count = $article_db->getCount($where);
        $page =(new CommonController())->getPage($count, $rows);
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->display();
    }
    
    /**
     * 文章详情
     */
    public function info(){
       $id = I('get.id',0);
       $article_db = D('Articles');
       $info = $article_db->getInfo($id);
       $comment = new CommentController();
       $comment_list = $comment->getlistById($id, $info['channel_id']);
       $comment_count = $comment->getCommentCount($id,$info['channel_id']);
       $this->assign('comment_count',$comment_count);
       $this->assign('comment_list',$comment_list);
       $this->assign('info',$info);
       $this->display();
    }
    
    
    
   
 
}