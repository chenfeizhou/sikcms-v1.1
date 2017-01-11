<?php

// +----------------------------------------------------------------------
// | 快乐筹cms  栏目模型
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.klchoucms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Model;

use Common\Model\Model;
use Admin\Model\ModuleModel;

class ChannelModel extends \Think\Model {


    //自定验证
    protected $_validate = array(
        array('name', 'require', '栏目名称不能为空!'),
        array('template_id', 'require', '缺少模板id参数!'),
        array('index_template', 'require', '封面模板不能为空!'),
        array('list_template', 'require', '列表模板不能为空!'),
        array('article_template', 'require', '文章模板不能为空!'),
        array('ishidden', array(0, 1), '值的范围不正确！', 0, 'in'), // 判断是否在一个范围内
        array('ispart', array(0, 1, 2), "值的范围不正确！", 0, 'in')
    );
    //array(填充字段,填充内容,[填充条件,附加规则]) 
    protected $_auto = array(
        array('ctime', 'time', 1, 'function'),
    );



    /**
     * 栏目下拉
     * @param int $parentid 父类ID
     * @param int $mode_id 模型ID
     */
    public function getChannelTree($parentid = 0, $page = 1, $rows = 20, $mode_id = null) {
        $order = "`sort` asc,`ctime` desc";
        $where = array('parentid' => $parentid);
        if ($mode_id) {
            $where['mode_id'] = $mode_id;
        }
        $result = $this->where($where)->order($order)->page($page, $rows)->select();
        if (is_array($result)) {
            foreach ($result as &$arr) {
                //各栏目下的文档数
                $arr['count'] = $this->getModelInstance($arr['mode_id'])->getCountByChannel($arr['id']);
                //各栏目下对应模型的修改添加列表方法
                $arr['action'] = (new ModuleModel())->getInfo($arr['mode_id']);
                $arr['children'] = $this->getChannelTree($arr['id']);
            }
        } else {
            $result = array();
        }
        return $result;
    }

    /**
     * 获取栏目对应的模型实例
     * @param int $mode_id 
     * @return object
     */
    public function getModelInstance($mode_id = 5) {
        $mode =(new ModuleModel())->getInfo($mode_id);
        switch ($mode['nid']) {
            //文章模型
            case 'article':
                return (new ArticlesModel());
                break;
            //软件模型
            case 'soft':
                return (new SoftModel());
                break;
            //商品模型
            case 'goods':
                return (new GoodsModel());
                break;
        }
    }

    /**
     * 按父类ID查找子栏目
     * @param int $parentid
     */
    public function getChannel($parentid = 0, $with_self = 0) {
        $rows = C('LISTROWS');
        $parentid = intval($parentid);
        $result = $this->where(array('parentid' => $parentid))->order('sort asc')->limit(1000)->select();
        if (!is_array($result))
            $result = array();
        if ($with_self) {
            $result2[0] = $this->where(array('id' => $parentid))->find();
            $result = array_merge($result2, $result);
        }
        return $result;
    }

    /**
     * 栏目总数
     */
    public function getCount($parentid = 0) {
        return $this->where(array('parentid' => $parentid))->count();
    }

}
