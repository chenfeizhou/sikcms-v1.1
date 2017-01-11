<?php 
/*
 * 
 * 思科cms前台框架主入口
 * @author zhijian.chen
 * @email 1114526565@qq.com
 * @date 2016/3/7
 * 
 */
//调试模式
define('APP_DEBUG', true);
define('NO_CACHE_RUNTIME', true);

//当前目录路径
define('SITE_PATH', getcwd() . '/');
//项目路径
define('PROJECT_PATH', SITE_PATH . 'app/');

// 应用公共目录
define('COMMON_PATH',   PROJECT_PATH.'Common/');

//静态资源目录
define('STATIC_PATH',"./statics");

// 定义应用目录
define('APP_PATH',PROJECT_PATH.'Application/');
//上传目录
define('UPLOADS_PATH', './upload_dir/');

//缓存目录
define("RUNTIME_PATH", SITE_PATH . "#runtime/");

//引入框架
require PROJECT_PATH.'Core3.2.3/ThinkPHP/ThinkPHP.php';