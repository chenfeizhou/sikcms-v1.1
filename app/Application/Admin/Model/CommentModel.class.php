<?php

namespace Admin\Model;

use Think\Model;

class CommentModel extends Model {

 
    /**
     * 评论列表
     */
    public function getCommentList($page=1,$where=null){
          $rows = C('LISTROWS');
          $list = $this->where($where)->page($page,$rows)->order('ctime desc')->select();
          return $list?$list:false;
    }
    /**
     * 评论总数
     */
    public function getCount($where){
        $count = $this->where($where)->count();
        return $count;
    }

}
