<?php

// +----------------------------------------------------------------------
// | 思科cms 模型管理控制器
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +--------------

namespace Admin\Controller;

use Common\Controller\AdminBase;

class ModuleController extends AdminBase {

    //页码
    public $page = 1;
    //每页数
    public $rows = 10;

    //初始化
    public function _initialize() {
        parent::_initialize();
        $this->page = I('get.page', 1);
        $this->rows = C('LISTROWS');
    }

    /**
     * 模型列表
     */
    public function index() {
        $module_db = D('Module');
        $field = array("id", "typename", "nid", "relation_table", 'status', 'issystem');
        $list = $module_db->getList($field, $this->page, $this->rows);
        $page = (new CommonController())->getPage($module_db->getCount(), $this->rows);
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->display('list');
    }

    /**
     * 模型编辑
     */
    public function edit() {
        $id = I('get.id');
        if (IS_POST) {
            
        }
        $module_db = D('Module');
        $info = $module_db->getInfo($id);
        //字段列表
        $relation_table = $info['relation_table'];
        $table_arr = explode(',', $relation_table);
        for($i=0;$i<count($table_arr);$i++){
            $query = "show full fields from $table_arr[$i]";
            $fields[$i]['table']=$table_arr[$i];
            $fields[$i]['field']= M()->query($query);
        }
        $this->assign('tables',$relation_table);
        $this->assign('fields', $fields);
        $this->assign('info', $info);
        $this->assign('id', $id);
        $this->display('edit');
    }

    /**
     * 模型添加
     */
    public function add() {
       $this->error('暂未开放');  
    }

    /**
     * 模型对应模板
     */
    public function template() {
        $id = I('get.id');
        $module_db = D('Module');
        $template = $module_db->getTemplate($id);
        $this->assign('template',$template);
        $this->display('template');
    }

    /**
     * 模型删除
     */
    public function delete() {
        $id = I('get.id', 0);
        $module_db = D('Module');
        $info = $module_db->getInfo($id);
        if ($info['issystem'] == 1) {
            $this->error('该模型为系统模型');
        }
        $res = $module_db->where(array('id' => $id))->delete();
        $res ? $this->success('删除成功') : $this->error('删除失败');
    }

}
