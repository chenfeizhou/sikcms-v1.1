<?php

// +----------------------------------------------------------------------
// | 思科cms 导航模型
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Home\Model;

use Common\Model\Model;

class NavModel extends \Think\Model {

    protected $db;

    public function __construct() {
        $this->db = M('Channel');
    }

    /**
     * 前台导航
     * @param  int $pid 栏目父类ID
     * @reutrn array
     */
    public function navList($pid = 0) {
        $order = "`sort` asc,`ctime` desc";
        $field = "id,name,sort,level";
        $where = array('parentid' => $pid, 'ishidden' => 0);
        $result = $this->db->field($field)->where($where)->order($order)->page($page, $rows)->select();
        if (is_array($result)) {
            foreach ($result as &$arr) {
                $arr['sub_item'] = $this->navList($arr['id']);
            }
        } else {
            $result = array();
        }
        return $result;
    }

    /**
     * 当前位置
     * @param $id 栏目ID
     */
    public function currentPos($id) {
        $str = '<li><a href="/" class="icon-home">首页</a></li>';
        if (!$id && ACTION_NAME=='lists') {
            return $str . '<li><a>全部列表</a></li>';
        }
        if (ACTION_NAME == 'info') {
            return $str . '<li><a>正文</a></li>';
        }
        $r = $this->db->where(array('id' => $id))->find(array('id', 'name', 'parentid'));
        if ($r['parentid']) {
            $str = $this->currentPos($r['parentid']);
        }
        return $str . '<li><a>' . $r['name'] . '</a></li>';
    }

}
