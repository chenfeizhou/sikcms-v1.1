<?php

/*
 * 思科cms 后台权限相关
 * @author zhijian.chen
 * @email 1114526565@qq.com
 * @date 2016/6/28
 *
 */

namespace Admin\Service;

use Think\Db;

/**
  +------------------------------------------------------------------------------
 * 基于菜单的数据库方式验证类
 * role_prev 角色权限表
 *  int filed role_id  角色
 *  int  filed menu_id  菜单ID(可获取controller action)
 *  int  filed level    级别
  +------------------------------------------------------------------------------
 */
class Access {

    /**
     * 检查当前登录用户是否有权限 
     * @param type $map [模块/控制器/方法]，没有时自动获取当前进行判断
     * @return boolean
     */
    static public function authenticate($param = '') {
        if (self::checkLogin() == false) {
            return false;
        }
        //是否为管理员
        if (User::getInstance()->isAdministrator() === true) {
            return true;
        }
        //查询是否有权限
        return D('Admin/Access')->isPass($param);
    }

    //用于检测用户权限的方法,并保存到Session中
    static function saveAccessList($authId = null) {
        if (null === $authId)
            $authId = User::getInstance()->id;
        // 如果使用普通权限模式，保存当前用户的访问权限列表
        // 对管理员开发所有权限
        if (C('USER_AUTH_TYPE') != 2 && User::getInstance()->isAdministrator($authId) !== true) {
            session('__ACCESS_LIST', Access::getAccessList($authId));
        }
        return;
    }

//    // 取得模块的所属记录访问权限列表 返回有权限的记录ID数组
//    static function getRecordAccessList($authId = null, $module = '') {
//        if (null === $authId)
//            $authId = $_SESSION[C('USER_AUTH_KEY')];
//        if (empty($module))
//            $module = CONTROLLER_NAME;
//        //获取权限访问列表
//        $accessList = self::getModuleAccessList($authId, $mole);
//        return $accessList;
//    }

    //检查当前操作是否需要认证
    static function checkAccess() {
        //如果项目要求认证，并且当前模块需要认证，则进行权限认证
        if (C('USER_AUTH_ON')) {
            $_controller = array();
            $_action = array();
            if ("" != C('REQUIRE_AUTH_MODULE')) {
                //需要认证的模块
                $_controller['yes'] = explode(',', strtoupper(C('REQUIRE_AUTH_MODULE')));
            } else {
                //无需认证的模块
                $_controller['no'] = explode(',', strtoupper(C('NOT_AUTH_MODULE')));
            }
            //检查当前模块是否需要认证
            if ((!empty($_controller['no']) && !in_array(strtoupper(CONTROLLER_NAME), $_controller['no'])) || (!empty($_controller['yes']) && in_array(strtoupper(CONTROLLER_NAME), $_controller['yes']))) {
                if ("" != C('REQUIRE_AUTH_ACTION')) {
                    //需要认证的操作
                    $_action['yes'] = explode(',', strtoupper(C('REQUIRE_AUTH_ACTION')));
                } else {
                    //无需认证的操作
                    $_action['no'] = explode(',', strtoupper(C('NOT_AUTH_ACTION')));
                }
                //检查当前操作是否需要认证
                if ((!empty($_action['no']) && !in_array(strtoupper(ACTION_NAME), $_action['no'])) || (!empty($_action['yes']) && in_array(strtoupper(ACTION_NAME), $_action['yes']))) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        return false;
    }

    // 登录检查
    static public function checkLogin() {
        //检查当前操作是否需要认证
        if (Access::checkAccess()) {
            //检查认证识别号
            if (User::getInstance()->isLogin() == FALSE) {
                return false;
            }
        }
        return true;
    }

    //权限认证的过滤器方法
    static public function AccessDecision($appName = MODULE_NAME) {
        //检查是否需要认证
        if (self::checkAccess()) {
            //存在认证识别号，则进行进一步的访问决策
            $accessGuid = md5($appName . CONTROLLER_NAME . ACTION_NAME);
            //是否为管理员
            if (User::getInstance()->isAdministrator() !== true) {
                //认证方式 1 登录验证 2 实时认证
                if (C('USER_AUTH_TYPE') == 2) {
                    //加强验证和即时验证模式 更加安全 后台权限修改可以即时生效
                    //通过数据库进行访问检查
                    $accessList = self::getAccessList(User::getInstance()->userid);
                } else {
                    // 如果是管理员或者当前操作已经认证过，无需再次认证
                    if (session($accessGuid)) {
                        return true;
                    }
                    //登录验证模式，比较登录后保存的权限访问列表
                    $accessList = session('__ACCESS_LIST');
                }
                //判断是否为组件化模式，如果是，验证其全模块名
                 $controller = defined('P_CONTROLLER_NAME') ? P_CONTROLLER_NAME : CONTROLLER_NAME;
                if (!isset($accessList[strtoupper($appName)][strtoupper($controller)][strtoupper(ACTION_NAME)])) {
                    //验证登录
                    if (self::checkLogin()) {
                        //主题框架默认有权限
                         if ($appName == "Admin" && in_array(CONTROLLER_NAME, array("Index")) && in_array(ACTION_NAME, array("index","main"))) {
                            session($accessGuid, true);
                            return true;
                        }
                    }
                    session($accessGuid, false);
                    return false;
                } else {
                    session($accessGuid, true);
                }
            } else {
                //超级管理员直接验证通过，且检查是否登录
                if (self::checkLogin()) {
                    return true;
                }
                return false;
            }
        }
        return true;
    }

    /**
      +----------------------------------------------------------
     * 取得当前认证号的所有权限列表
      +----------------------------------------------------------
     * @param integer $authId 用户ID
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     */
    static public function getAccessList($authId) {
        //用户信息
        $userInfo = User::getInstance()->getInfo();
        if (empty($userInfo)) {
            return false;
        }
        //角色ID
        $role_id = $userInfo['roleid'];
        //检查角色
        $roleinfo = D('Admin/Role')->where(array('id' => $role_id))->find();
        if (empty($roleinfo) || empty($roleinfo['status'])) {
            return false;
        }
        //该角色所有权限
        $access = D('Admin/Access')->getAccessList($role_id);
        $accessList = array();
        foreach ($access as $list) {
            $model = strtoupper($list['model']);
            $controller = strtoupper($list['controller']);
            $action = strtoupper($list['action']);
            $accessList[$model][$controller][$action] = $action;
        }
        return $accessList;
    }

   

}
