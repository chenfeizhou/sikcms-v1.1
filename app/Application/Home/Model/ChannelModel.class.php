<?php

// +----------------------------------------------------------------------
// | 思科cms  栏目模型
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Home\Model;

use Common\Model\Model;

class ChannelModel extends \Think\Model {
   

    /**
     * 获取栏目
     */
    public function getChannel($where = array()) {
        $order = "`sort` asc,`ctime` desc";
        $where['parentid'] =0;
        $result = $this->where($where)->order($order)->select();
        return $result;
   
    }
      /**
     * 获取父类下的子栏目
     */
    public function getChannelByParentid($parentid = array()) {
        $order = "`sort` asc,`ctime` desc";
        $where['parentid'] =$parentid;
        $result = $this->where($where)->order($order)->select();
        return $result;
   
    }

    
    
    /**
     * 栏目下拉
     * @param int $parentid 父类ID
     * @param int $mode_id 模型ID
     */
    public function getChannelTree($parentid = 0, $page = 1,$rows = 20,$mode_id = null) {
        $order = "`sort` asc,`ctime` desc";
        $where = array('parentid' => $parentid,'ispart'=>array('neq',1));
        if($mode_id){
            $where['mode_id']=$mode_id;
        }
        $result = $this->where($where)->order($order)->page($page, $rows)->select();
        if (is_array($result)) {
            foreach ($result as &$arr) {
                $arr['children'] = $this->getChannelTree($arr['id']);
            }
        } else {
            $result = array();
        }
        return $result;
   
    }


   

    /**
     * 栏目总数
     */
    public function getCount($where =  array()) {
        $where['parentid']=0;
        return $this->where($where)->count();
    }

}
