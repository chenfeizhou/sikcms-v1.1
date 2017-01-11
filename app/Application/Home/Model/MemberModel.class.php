<?php

// +----------------------------------------------------------------------
// | 思科cms 会员模型
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.sikcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhijian.chen <1114526565@qq.com>
// +----------------------------------------------------------------------

namespace Home\Model;

use Common\Model\Model;
class MemberModel extends Model{
    
    public function getInfo($userid){
      return   $this->where(array('user_id'=>$userid))->find();
    }
}
