<?php

// +----------------------------------------------------------------------
// | 思科cms 文章内容模型
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Home\Model;

use Common\Model\Model;

class ArticlesModel extends Model {

    /**
     * 文章内容列表
     */
    public function getArticleList($page = 1, $where = null, $order = null,$limit=20) {
        $rows = $limit;
        $db_prefix = C('DB_PREFIX');
        $order = empty($order) ? "list.inputtime desc" : $order;
        $field = "list.id ,list.title,list.flag,channel.name as cate_name,data.content,list.username,list.inputtime,list.updatetime,list.status";
        $list = $this->alias('list')
                        ->join("LEFT JOIN {$db_prefix}article_data as data  on data.articles_id = list.id")
                        ->join("LEFT JOIN {$db_prefix}channel as channel on channel.id=list.channel_id")
                        ->field($field)->where($where)->order($order)->page($page, $rows)->select();
        if (empty($list)) {
            return false;
        }
        return $list;
    }

    /**
     * 文章总数
     */
    public function getCount($where = null) {
        return $this->where($where)->count();
    }

    /**
     * 文章内容
     */
    public function getInfo($id = null) {
        if (!$id)
            return null;
        $db_prefix = C('DB_PREFIX');
        return $this->alias('a')->join("inner join {$db_prefix}article_data as `data` on `data`.articles_id=a.id ")->where(array('a.id' => $id))->find();
    }

}
