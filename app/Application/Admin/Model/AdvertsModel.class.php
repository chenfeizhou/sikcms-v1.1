<?php

// +----------------------------------------------------------------------
// | 思科cms 广告管理模型
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Model;

use Common\Model\Model;
use \Admin\Controller\CommonController;

class AdvertsModel extends \Think\Model {
   //array(填充字段,填充内容,[填充条件,附加规则]) 
    protected $_auto = array(
         array('ctime', 'time', 1, 'function'),
        );
    /**
     * 广告位列表
     */
    public function getAdvertsList($page = 1) {
        $rows = C('LISTROWS');
        $data = $this->order('ctime desc')->page($page, $rows)->select();
        $page = (new CommonController())->getPage($this->count(), $rows);
        return array('list' => $data, 'page' => $page);
    }

    /**
     * 广告列表
     */
    public function getAdList($page = 1) {
        $rows = C('LISTROWS');
        $db_prefix = C('DB_PREFIX');
        $field = array('list.*', 'adverts.title', 'adverts.type');
        $adlist_db = M('adverts_list as list');
        $data = $adlist_db
                        ->join("LEFT JOIN {$db_prefix}adverts as adverts on adverts.advert_id=list.advert_id")->field($field)->order("list.ctime desc")->page($page, $rows)->select();
        if (empty($data)) {
            return false;
        }
        $page = (new CommonController())->getPage($adlist_db->count(), $rows);
        return array('list' => $data, 'page' => $page);
    }

    /*
     * 广告位下拉列表
     */

    public function getSelectTree() {
        $field = array('type', '`title`');
        $order = '`ctime` desc';
        $data = $this->field()->order($order)->select();
        if (empty($data)) {
            return array();
        }
        return $data;
    }

}
