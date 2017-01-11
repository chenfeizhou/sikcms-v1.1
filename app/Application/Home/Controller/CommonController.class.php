<?php

namespace Home\Controller;

use Think\Controller;
use Common\Controller\SikCMS;
use Home\Model\NavModel;

class CommonController extends SikCMS {

    public function _empty() {
        header('location:' . C('ERROR_PAGE'));
        exit;
    }

    protected function _initialize() {
        $userinfo = $this->userInfo();
        if ($userinfo && (ACTION_NAME == 'register' || ACTION_NAME == 'login') && ACTION_NAME != 'logout') {
            redirect(U('Index/index'));
        }
        $this->assign('userinfo', $userinfo);
        $this->getNav();
    }

    public function getNav() {
        $cid = $_GET['cid'] ? (int) $_GET['cid'] : 0;
        $nav_db = new NavModel;
        $nav = $nav_db->navList();
        $curpos = $nav_db->currentPos($cid);
        $this->assign('curpos', $curpos);
        $this->assign('nav', $nav);
    }

    public function userInfo() {
        if (session('user_id')) {
            return array('userid' => session('user_id'), 'username' => session('username'), 'nickname' => session('nickname'));
        } else {
            return false;
        }
    }

    //获取分页
    public function getPage($count, $pagesize, $rewrite = '', $param_url = '') {
        $Page = new \Think\Page($count, $pagesize, '', $param_url);
        $Page->setConfig('header', '');
        $Page->setConfig('prev', "«");
        $Page->setConfig('next', "»");
        $Page->setConfig('first', '首页');
        $Page->setConfig('last', '末页');
        $Page->setConfig("%nowPage%/%totalPage% 页 %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end% %totalRow%");
        $page = $Page->showPage();
        return $page;
    }

}
