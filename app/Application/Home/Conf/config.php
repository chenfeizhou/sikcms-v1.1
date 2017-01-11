<?php

//引入主体配置
$mysql = require (PROJECT_PATH."Common/Conf/mysql.inc.php");
$redis = require(PROJECT_PATH."Common/Conf/redis.inc.php");
$version=require(PROJECT_PATH.'Common/Conf/version.inc.php');
$data = require(PROJECT_PATH.'Common/Conf/dataconfig.php');
//基础前台配置项
$array = array(   
    'URL_MODEL'        => '0', //URL模式 普通模式 0 PATHINF O 模式 1REWRITE模式	2 兼容模式 	3
    'show_page_trace' => TRUE,
    //默认错误跳转对应的模板文件
    'TMPL_ACTION_ERROR' => 'Public:error',
    //默认成功跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => 'Public:success',
    'TMPL_DETECT_THEME' => TRUE, //自动检测模板主题
    'THEME_LIST' => 'theme', //模板主题列表
    'DEFAULT_THEME' => 'theme', // 默认模板主题名称
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' =>STATIC_PATH.'/home', //静态资源地址
    ),
);
return array_merge($mysql, $redis,$version,$data, $array);
