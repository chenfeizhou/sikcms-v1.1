<?php

// +----------------------------------------------------------------------
// | 思科cms 商品模型
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Model;

use Common\Model\Model;

class GoodsModel extends Model {

    //array(填充字段,填充内容,[填充条件,附加规则]) 
    protected $_auto = array(
        array('inputtime', 'time', 1, 'function'),
        array('updatetime', 'time', 1, 'function')
    );

    /**
     * 产品内容列表
     */
    public function getGoodsList($page = 1, $where = null) {
        $rows = C('LISTROWS');
        $db_prefix = C('DB_PREFIX');
        $field = "channel.name as cate_name,list.*";
        $list = $this->alias('list')
                        ->join("LEFT JOIN {$db_prefix}channel as channel on channel.id=list.channel_id")
                        ->field($field)->where($where)->order("list.inputtime desc")->page($page, $rows)->select();
        if (empty($list)) {
            return false;
        }
        return $list;
    }

    /**
     * 根据类目获取总数数
     * @param int $channel_id 类目id
     */
    public function getCountByChannel($channel_id = 0) {
        $count = $this->where(array('channel_id' => $channel_id))->count();
        return $count ? $count : '';
    }

    /**
     * 总数
     */
    public function getCount($where = null) {
        return $this->where($where)->count();
    }

    /**
     * 内容
     */
    public function getInfo($id = null) {
        if (!$id)
            return null;
        return $this->where(array('goods_id' => $id))->find();
    }

}
