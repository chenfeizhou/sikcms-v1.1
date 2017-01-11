<?php

// +----------------------------------------------------------------------
// | 思科cms 后台用户角色模型
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Model;

use Common\Model\Model;

class RoleModel extends Model {

    //array(验证字段，验证规则，错误提示,[验证条件，附加规则，验证时间])
    protected $_validate = array(
        array('name', 'require', '角色名称不能为空!'),
        array('name', '', '该名称已存在!', 0, 'unique', 3),
        array('status', 'require', '缺少状态！'),
        array('status', array(0, 1), '状态错误,状态只能是1或者0!', 2, 'in'),
    );
    //array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
        array('listorder', '0'),
    );

    /**
     * 获取该角色下的全部子角色
     * @param type $id
     * @return string
     */
    public function getArrchildid($id) {
        if (empty($this->rolelist)) {
            $this->roleList = $this->getTreeArray();
        }
        $arrchildid = $id;
        if (is_array($this->roleList)) {
            foreach ($this->roleList as $k => $cat) {
                if ($cat['parentid'] && $k != $id && $cat['parentid'] == $id) {
                    $arrchildid .= ',' . $this->getArrchildid($k);
                }
            }
        }
        return $arrchildid;
    }
    /**
     * 返回总数
     */
    public function getCount(){
        $count = $this->count();
        return  $count;  
    }

    /**
     * 返回Tree使用的数组
     * @return array
     */
    public function getTreeArray($page,$rows) {
        $roleList = array();
        $roleData = $this->order(array('listorder' => "asc", "id" => 'desc'))->page($page,$rows)->select();
        foreach ($roleData as $rs) {
            $roleList[$rs['id']] = $rs;
        }
        return $roleList;
    }

    /**
     * 根据角色Id获取角色名
     * @param int $roleId 角色id
     * @return string 返回角色名
     */
    public function getRoleIdName($roleId) {
        return $this->where(array('id' => $roleId))->getField('name');
    }

}
