<?php

namespace Admin\Model;

use Think\Model;

class MenuModel extends Model {

    protected $tableName = 'menu';
    public $error;

    /**
     * 按父ID查找菜单子项
     * @param integer $parentid   父菜单ID  
     * @param integer $with_self  是否包括他自己
     */
    public function getMenu($pid = 0, $with_self = 0) {
        $pid = intval($pid);
        $roleid = session('roleid');
        $result = $this->where(array('pid' => $pid, 'status' => 1))->order('sort asc')->limit(1000)->select();
        if (!is_array($result))
            $result = array();
        if ($with_self) {
            $result2[0] = $this->where(array('id' => $pid))->find();
            $result = array_merge($result2, $result);
        }
        return $result;
    }

    /**
     * 当前位置
     * @param $id 菜单ID
     */
    public function currentPos($id) {
        if($id){
            $where =  array('id' => $id);
        }else{
            $where['controller'] = CONTROLLER_NAME;
            $where['action']=ACTION_NAME;
        }
        $r = $this->where($where)->find(array('id', 'name', 'pid'));
        $str = '';
        if ($r['pid']) {
            $str = $this->currentPos($r['pid']);
        }
        return $str . $r['name'] . ' &gt; ';
    }

    /**
     * 菜单列表
     * 
     */
    public function getTree($pid = 0) {
        $order = "`sort` asc,`id` desc";
        $data = $this->where(array('pid' => $pid,'status'=>1))->order($order)->select();
        if (is_array($data)) {
            foreach ($data as &$arr) {
                $name = BIND_MODULE;
                $param = "";
                if ($arr['params']) {
                    $param = "?" . $arr['params'];
                }
                $arr['url'] = U("{$name}/{$arr['controller']}/{$arr['action']}{$param}", array("menuid" => $arr['id']));
                $arr['children'] = $this->getTree($arr['id']);
            }
        } else {
            $data = array();
        }
        return $data;
    }

    /**
     * 权限管理列表
     */
    public function getRoleTree($pid = 0, $roleid = 0) {
        $field = array('id', '`name` as `text`', 'controller', 'action', 'level');
        $order = '`sort` ASC,`id` DESC';
        $data = $this->field($field)->where("`pid`='{$pid}'")->order($order)->select();
        if (is_array($data)) {
            $access_db = M('access');
            foreach ($data as $k => &$arr) {
                $arr['attributes']['parent'] = $this->getParentIds($arr['id']);
                $arr['children'] = $this->getRoleTree($arr['id'], $roleid);
                if (is_array($arr['children']) && !empty($arr['children'])) {
                    $arr['state'] = 'closed';
                } 
                    //勾选默认菜单
                $check = $access_db->where(array('menu_id' => $arr['id'], 'role_id' => $roleid))->count();
                if ($check)
                    $arr['checked'] = true;
                
            }
        }else {
            $data = array();
        }
        return $data;
    }

    /**
     * 获取菜单父级id
     */
    public function getParentIds($id, $result = null) {
        $pid = $this->where(array('id' => $id))->getField('pid');
        if ($pid) {
            $result .=$result ? ',' . $pid : $pid;
            $result = $this->getParentIds($pid, $result);
        }
        return $result;
    }

    /**
     * 检查菜单名称是否存在
     */
    public function checkName($name) {
        $name = trim($name);
        if ($this->where(array('name' => $name))->field('id')->find()) {
            return true;
        }
        return false;
    }

    /*
     * 菜单下拉列表
     */

    public function getSelectTree($pid = 0) {
        $field = array('id', '`name` as `text`');
        $order = '`sort` asc,`id` desc';
        $data = $this->field()->where(array('pid' => $pid))->order($order)->select();
        if (is_array($data)) {
            foreach ($data as &$arr) {
                $arr['children'] = $this->getSelectTree($arr['id']);
            }
        } else {
            $data = array();
        }
        return $data;
    }

    /**
     * 检查上级菜单设置是否正确
     */
    public function checkParentId($id, $pid) {
        if ($id == $pid)
            return false;  //上级菜单不能与本级菜单相同

        $data = $this->field(array('id'))->where(array('pid' => $id))->order('`sort` ASC,`id` DESC')->select();
        if (is_array($data)) {
            foreach ($data as &$arr) {
                if ($arr['id'] == $pid)
                    return false; //上级菜单不能与本级菜单子菜单
                return $this->checkParentId($arr['id'], $pid);
            }
        }else {
            return true;
        }
        return true;
    }

    /**
     * 设置子菜单级别
     * @param int $level
     * @param int $parentid
     * @return bool
     */
    public function setSonLevel($level, $pid) {
        $list = $this->field('id')->where(array('pid' => $pid))->select();

        if (is_array($list)) {
            $level = $level + 1;
            $this->where(array('pid' => $pid))->save(array('level' => $level));

            foreach ($list as $info) {
                $this->setSonLevel($level, $info['id']);
            }
        }
        return true;
    }

    /**
     * 删除子菜单
     * @param int $parentid
     * @return bool
     */
    public function deleteSonMenu($pid) {
        $list = $this->field('id')->where(array('pid' => array('in', $pid)))->select();
        if (is_array($list)) {
            $this->where(array('pid' => array('in', $pid)))->delete();
            foreach ($list as $info) {
                $this->deleteSonMenu($info['id']);
            }
        }
        return true;
    }

    /*
     * 清空菜单相关缓存
     */

    public function clearCatche() {
        S('system_menulist', null);
        S('system_public_menuselecttree', null);
    }

}
