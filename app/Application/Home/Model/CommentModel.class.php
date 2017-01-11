<?php

// +----------------------------------------------------------------------
// | 思科cms  评论模型
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Home\Model;

use Common\Model\Model;

class CommentModel extends Model {

    /**
     * 获取评论
     */
    public function getList($page = 1, $where = null, $field = "*", $order = "ctime desc", $row = 10) {
        $map['pid'] = 0;
        $list = $this->field($field)->where($map)->where($where)->order($order)->page($page, $row)->select();
        if (empty($list)) {
            return false;
        }
        //评论回复列表
        foreach ($list as &$arr) {
            $arr['reply_list'] = $this->getReplyList($arr['id'], $field, $order);
        }
        return $list;
    }

    /**
     * 获取子评论回复
     */
    public function getReplyList($pid, $field = "*", $order = "ctime desc") {
        $where = array('pid' => $pid);
        $item = $this->field($field)->where($where)->order($order)->select();
        return $item ? $item : array();
    }
    /**
     * 评论总数
     */
    public function getCount($relation_id,$chanel_id){
       return $this->where(array('relation_id'=>$relation_id,'channel_id'=>$chanel_id,'is_audit'=>1))->count();
    }

}
