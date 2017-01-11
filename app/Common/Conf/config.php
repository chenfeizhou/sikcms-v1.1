<?php

return array(
    'TMPL_TEMPLATE_SUFFIX' => '.html', // 默认模板文件后缀
    'MODULE_DENY_LIST' => array('Common', 'Runtime'), // 设置禁止访问的模块列表
    'APP_GROUP_LIST' => 'Home,Api,Install,Admin', //项目分组设定
    'DEFAULT_GROUP'  => 'Home', //默认分组
    /* 命名空间 */
    'AUTOLOAD_NAMESPACE' => array(
        'Common' => COMMON_PATH,
        'Libs' => PROJECT_PATH . 'Libs',
    ),

);