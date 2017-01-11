<?php
// +----------------------------------------------------------------------
// | 思科cms 后台用户模型
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----
namespace Admin\Model;

use Common\Model\Model;

class LoginlogModel extends Model {

    //array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
        array('logintime', 'time', 1, 'function'),
        array('loginip', 'get_client_ip', 3, 'function'),
    );

    /**
     * 删除一个月前的日志
     * @return boolean
     */
    public function deleteAMonthago() {
        $status = $this->where(array("logintime" => array("lt", time() - (86400 * 30))))->delete();
        return $status !== false ? true : false;
    }

    /**
     * 添加登录日志
     * @param array $data
     * @return boolean
     */
    public function addLoginLogs($data) {
        $this->create($data);
        return $this->add() !== false ? true : false;
    }

}
