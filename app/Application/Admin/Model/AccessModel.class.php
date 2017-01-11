<?php

// +----------------------------------------------------------------------
// | 思科cms 角色权限模型
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Model;

use Common\Model\Model;

class AccessModel extends Model {

    /**
     * 角色权限列表
     * @param int $role_id 角色ID
     * @return array
     */
    public function getAccessList($role_id) {
        if (empty($role_id))
            return false;

        //该角色下的所有权限
        $db_prefix=C('DB_PREFIX');
        $field = array('access.*','menu.model','menu.controller','menu.action');
        $data = $this->alias('as access')
                ->join("LEFT JOIN {$db_prefix}menu as menu on menu.id=access.menu_id")->field($field)->where(array('access.role_id' => $role_id))->select();
        if (empty($data)) {
            return false;
        }
        $accessList = array();
        foreach ($data as $info) {
            unset($info['status']);
            $accessList[] = $info;
        }
        return $accessList;
    }

    /**
     * 检查用户是否有对应权限
     * @param type $param 方法[模块/控制器/方法]，为空自动获取
     * @return boolean 
     */
    public function isPass($param = '') {
        if (\Admin\Service\User::getInstance()->isAdministrator()) {
            return true;
        }
        $role_id = \Admin\Service\User::getInstance()->role_id;
        if (!empty($param)) {
            $param = trim($param, '/');
            $param = explode('/', $param);
            if (empty($param)) {
                return false;
            }
        } else {
            $param = array(MODULE_NAME, CONTROLLER_NAME, ACTION_NAME);
        }
        if (count($param) >= 3) {
            list($model, $controller, $action) = $param;
        } elseif (count($param) == 1) {
            $model = MODULE_NAME;
            $controller = CONTROLLER_NAME;
            $action = $param[0];
        } elseif (count($param) == 2) {
            $model = MODULE_NAME;
            list($controller, $action) = $param;
        }
        $map = array('model' => $model, 'controller' => $controller, 'action' => $action);
        $menu_id = M('menu')->where($map)->getField('id');
        $count = $this->where(array('menu_id' => $menu_id,'role_id'=>$role_id))->count();
        return $count ? true : false;
    }

}
