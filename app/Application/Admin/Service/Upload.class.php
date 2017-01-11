<?php

/*
 * 思科cms 后台上传类
 * @author zhijian.chen
 * @email 1114526565@qq.com
 * @date 2016/9/23
 *
 */

namespace Admin\Service;

use Think\Upload;
use Vendor\ThinkImage\ThinkImage;

class upload {

    private $config = array(
        'mimes' => array(), //允许上传的文件MiMe类型
        'maxSize' => 0, //上传的文件大小限制 (0-不做限制)
        'exts' => array('jpg', 'png', 'gif'), //允许上传的文件后缀
        'autoSub' => false, //自动子目录保存文件
        'subName' => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Avatar/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('get_avatar_name', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt' => 'jpg', //文件保存后缀，空则使用原后缀
        'replace' => true, //存在同名是否覆盖
        'hash' => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调，如果存在返回文件信息数组
        'driver' => 'Local', // 文件上传驱动
        'driverConfig' => array(), // 上传驱动配置
    );

//上传图片
    public function _upload() {
        $upload = new Upload($this->config);
        //图片地址
        $path = './uploads/';
        if (!$upload->upload()) {
            $this->error($upload->getErrorMsg());
        } else {
            $temp_size = getimagesize($path . 'temp.jpg');
            if ($temp_size[0] < 100 || $temp_size[1] < 100) {//判断宽和高是否符合头像要求
                $this->error('图片宽或高不得小于100px');
            }
            //__ROOT__ '/uploads/' . 'temp.jpg'
            $this->success('上传成功');
        }
    }

    //裁剪并保存图片
    public function _crop() {
        //图片裁剪数据
        $param = I('post.');
        if (!isset($params) && empty($params)) {
            $this->error('参数错误！');
        }
        //图片目录地址
        $path = './uploads/';
        //要保存的图片
        $real_path = $path . 'avatar.jpg';
        //临时图片地址
        $pic_path = $path . 'temp.jpg';
        //实例化裁剪类
        $Think_img = new ThinkImage(THINKIMAGE_GD);
        //裁剪原图得到选中区域
        $Think_img->open($pic_path)->crop($params['w'], $params['h'], $params['x'], $params['y'])->save($real_path);
        //生成缩略图
        $Think_img->open($real_path)->thumb(100, 100, 1)->save($path . 'avatar_100.jpg');
        $Think_img->open($real_path)->thumb(60, 60, 1)->save($path . 'avatar_60.jpg');
        $Think_img->open($real_path)->thumb(30, 30, 1)->save($path . 'avatar_30.jpg');
        $this->success('裁剪并上传图片成功');
    }

}
