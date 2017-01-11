<?php
namespace Home\Controller;
use Think\Controller;
use Home\Controller\CommonController;
class IndexController extends CommonController {
    
    public function index(){
        $this->display();
    }
    
}