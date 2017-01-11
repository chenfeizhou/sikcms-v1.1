<?php

// +----------------------------------------------------------------------
// | 快乐筹cms 后台商品模块
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.klchoucms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Common\Controller\AdminBase;
use Admin\Service\User;

class GoodsController extends AdminBase {

    //页码
    public $page = 1;
    //每页数
    public $rows = 10;
    //软件状态码
    public $status = array();
    //模型标识
    public $nid ='goods'; 
    //模型ID
    public $mode_id=7;
    //是否为管理员操作标识
    public $isadmin = 1;

    //初始化
    public function _initialize() {
        parent::_initialize();
        $this->page = I('get.page', 1);
        $this->rows = C('LISTROWS');
        //商品上下架状态对应商品表marketable字段
        $this->status = array(
            0 => array('text' => '上架', 'value' => 'true'),
            1 => array('text' => '下架', 'value' => 'false')
            );
    }

    /**
     * 列表
     */
    public function index() {
        $status = I('post.status', '');
        $goods_name = I('post.goods_name');
        $channel_id = I('request.channel_id', '');
        $where = "";
        if (!empty($status) && $status != 'all') {
            $where['marketable'] = $status;
        }
        if(!empty($goods_name)){
            $where['goods_name']=array('like',"%$goods_name%");
        }
        if (!empty($channel_id) && $channel_id != 'all') {
            $where['channel_id'] = array('eq', $channel_id);
        }
        $goods_db = D('Goods');
        $module_db = D('Module');
        //获取操作方法
        $action = $module_db->getInfoBynid($this->nid);
        $list = $goods_db->getGoodsList($this->page, $where);
        $page = (new CommonController())->getPage($goods_db->getCount($where),$this->rows);
        $this->assign('action',$action);
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('status', $this->status);
        $this->display('list');
    }

    /**
     * 添加
     */
    public function add() {
        if (IS_POST) {
            $user = User::getInstance()->getInfo();
            $goods_db = D('Goods');
            $post = I('post.info');
            $data = array(
                'channel_id' => $post['channel_id'],
                'goods_name' => $post['goods_name'],
                'marketable' => $post['marketable'],
                'market_price'=>$post['market_price'],
                'goods_price'=>$post['goods_price'],
                'goods_pic' => implode('|',$post['goods_pic']),
                'thumb_pic' => implode('|',$post['thumb_pic']),
                'goods_no' => $post['goods_no'],
                'updatetime'=>time(),
                'inputtime' => time(),
                'goods_body' => html_entity_decode($post['goods_body']),
                'operater_id'=>$user['userid'],
                'author'=>$user['username'],
                'isadmin'=>$this->isadmin,
            );
            $res = $goods_db->add($data);
            $res ? $this->success('添加成功', U('Goods/index')) : $this->error('添加失败');
        }
        $channel_db = D('Channel');
        $channel = $channel_db->getChannelTree(0, 1, 1000,$this->mode_id);
        $this->assign('channel', $channel);
        $this->assign('status', $this->status);
        $this->display();
    }

    /**
     * 编辑
     */
    public function edit() {
         $goods_db = D('Goods');
        if (IS_POST) {
            $user = User::getInstance()->getInfo();
            $post = I('post.info');
            $data = array(
                'channel_id' => $post['channel_id'],
                'goods_name' => $post['goods_name'],
                'marketable' => $post['marketable'],
                'market_price'=>$post['market_price'],
                'goods_price'=>$post['goods_price'],
                'goods_pic' => implode('|',$post['goods_pic']),
                'thumb_pic' => implode('|',$post['thumb_pic']),
                'goods_no' => $post['goods_no'],
                'updatetime'=>time(),
                'goods_body' => html_entity_decode($post['goods_body']),
                'operater_id'=>$user['userid'],
                'author'=>$user['username'],
                'isadmin'=>$this->isadmin,
            ); 
            $res = $goods_db->where('goods_id=' . $post['goods_id'])->save($data);
            if (!$res) {
                $this->error('修改失败');
            }
            $this->success('修改成功', U('Goods/index'));
        }
        $id = I('get.id');
        $info = $goods_db->getInfo($id);
        //商品图集
        $goods_pic=explode('|', $info['goods_pic']);
        $thumb_pic=explode('|', $info['thumb_pic']);
        for($i=0;$i<count($goods_pic);$i++){
          $pics[$i]['goods_pic']=$goods_pic[$i];
          $pics[$i]['thumb_pic']=$thumb_pic[$i];
        }
        $channel_db = D('Channel');
        $channel = $channel_db->getChannelTree(0, 1, 1000,$this->mode_id);
        $this->assign('channel', $channel);
        $this->assign('pics',$pics);
        $this->assign('id', $id);
        $this->assign('info', $info);
        $this->assign('status', $this->status);
        $this->display();
    }

    /**
     * 删除
     */
    public function delete() {
        $ids = I('post.ids', 0);
        $goods_db = D('goods');
        $res = $goods_db->where(array('goods_id' => array('in', $ids)))->delete();
        $res ? $this->success('删除成功') : $this->error('删除失败');
    }

}
