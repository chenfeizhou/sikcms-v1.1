<?php

namespace Home\Controller;

use Think\Controller;

class MemberController extends CommonController {

    /**
     * 注册
     */
    public function register() {
        if (IS_POST) {
            $m = M('member');
            $username = I('post.username');
            $password = md5(I('post.password'));
            $code = I('post.verify');
            $nickname = I('post.nickname');
            $islogin = I('post.islogin');
            if ($m->where(array('username' => $username))->find()) {
                $this->error('该用户名已经存在');
            }
       
            $verify = new \Think\Verify();
            if ($verify->check($code)===false) {
                $this->error('验证码错误');
            }
            $data = array(
                'username' => $username,
                'password' => $password,
                'ip' => get_client_ip(),
                'ctime' => time(),
                'nickname'=>$nickname,
                'header'=>'head'.rand(1,6).'.png'
                );
            if($res=$m->add($data)){
                if($islogin==1){
                    session('username',$username);
                    session('user_id',$res);
                    session('nickname',$nickname);
                }
                $this->success('注册成功',U('Member/login'),3);
            }
        }
   
        $title = '注册';
        $this->assign('title', $title);
        $this->display();
    }

    public function verify() {
        $verify = new \Think\Verify();
        $verify->useNoise = false;
        $verify->useCurve=false;
        $verify->entry();
    }

    /**
     * 登录
     */
    public function login() {
        if(IS_POST){
            $m = M('member');
            $username = I('post.username');
            $password = md5(I('post.password'));
            if(empty($username) || empty($password)){
                $this->error('用户名或密码不能为空');
            }
            if($info=$m->where(array('useranme'=>$username,'password'=>$password))->find()){
                session('username',$username);
                session('user_id',$info['user_id']);
                session('nickname',$info['nickname']);
                $this->success('登录成功',U('Index/index'));
            }else{
                $this->error('用户名或密码错误请联系管理员');
            }
        }
        $title = '登录';
        $this->assign('title', $title);
        $this->display();
    }
   
    
    public function logout(){
        session(null);
         $this->success('登出成功');
    }
    
    
    

}
