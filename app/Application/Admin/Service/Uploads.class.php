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

class Uploads {

    private $config = array(
        'mimes' => array(), //允许上传的文件MiMe类型
        'maxSize' => 0, //上传的文件大小限制 (0-不做限制)
        'exts' => array('jpg', 'png', 'gif', 'jpeg'), //允许上传的文件后缀
        'autoSub' => true, //自动子目录保存文件
        'subName' => array('date', 'Ymd'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => UPLOADS_PATH, //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt' => 'jpg', //文件保存后缀，空则使用原后缀
        'replace' => true, //存在同名是否覆盖
        'hash' => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调，如果存在返回文件信息数组
        'driver' => 'Local', // 文件上传驱动
        'driverConfig' => array(), // 上传驱动配置
        'thumb_width' => 360, //缩略图的宽度
        'thumb_height' => 360  //缩略图的高度
    );

    //初始化config
    private function init() {
        //获取系统设置值
        if (configs('imgtype', 'value')) {
            $this->config['exts'] = explode('|', configs('imgtype', 'value'));
        }
        if (configs('thumb_width', 'value')) {
            $this->config['thumb_width'] = configs('thumb_width', 'value');
        }
        if (configs('thumb_height', 'value')) {
            $this->config['thumb_height'] = configs('thumb_height', 'value');
        }
    }

    //上传图片
    public function _upload() {
        $this->init();
        $upload = new Upload($this->config);
        //图片地址
        $path = UPLOADS_PATH;
        $pics = $upload->upload();
        foreach ($pics as $k => $thumb) {
            //原图地址
            $pic[$k]['original'] = UPLOADS_PATH . $thumb['savepath'] . $thumb['savename'];
            $file_thumb = UPLOADS_PATH . 'thumb/' . $thumb['savepath'] . $thumb['savename'];
            //生成缩略图  
            $this->_mkdir(UPLOADS_PATH . 'thumb/' . $thumb['savepath']);
            $this->_thumb($pic[$k]['original'], $file_thumb);
            $pic[$k]['thumb'] = $file_thumb;
        }
        if (!$pics) {
            return array('status' => 0, 'info' => $upload->getError());
        } else {
            return array('status' => 1, 'path' => $pic);
        }
    }

    /**
     * 缩略图生成
     * @param type $file_path 原图文件
     * @param type $thumb_path 缩略图文件
     */
    public function _thumb($file_path, $thumb_path) {
        $image = new \Think\Image();
        $image->open($file_path);
        $image->thumb($this->config['thumb_width'], $this->config['thumb_height'], \Think\Image::IMAGE_THUMB_FILLED)->save($thumb_path);
    }

    /**
     * 图片水印
     * @param type $pic_path 原图地址
     * @param type $thumb_path 图片水印地址
     * @param type $type  水印类型(img：图片 txt:文字)
     */
    public function _water($file_path, $thumb_path, $type = 'img') {
        $image = new \Think\Image();
        // 在图片左上角添加水印（水印文件位于./logo.png） 并保存为water.jpg
        if ($type == 'img') {
            $image->open($file_path)->water('./logo.png', \Think\Image::IMAGE_WATER_NORTHWEST)->save($thumb_path);
        } elseif ($type == 'txt') {
            $image->open($file_path)->text('sikcms', './1.ttf', 20, '#000000', \Think\Image::IMAGE_WATER_SOUTHEAST)->save($thumb_path);
        }
    }

    /**
     * 创建目录
     * @param type $path 目录地址
     * @return boolean
     */
    public function _mkdir($path) {
        if (!file_exists($path)) {
            if (!mkdir($path, 0777, TRUE)) {
                return false;
            }
        }
        return true;
    }

}
