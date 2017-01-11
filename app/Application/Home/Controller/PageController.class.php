<?php
namespace Home\Controller;

use Think\Controller;
class PageController extends CommonController {
    
    function zhaop(){
        $this->display('invite');
    }
}