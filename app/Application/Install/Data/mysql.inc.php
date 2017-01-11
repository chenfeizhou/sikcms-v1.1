<?php
//mysql配置
return $array = array(
   //本地 
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => '{DB_HOST}', // 服务器地址
    'DB_NAME' => '{DB_NAME}', // 数据库名
    'DB_USER' => '{DB_USER}', // 用户名
    'DB_PWD' => '{DB_PWD}', // 密码
    'DB_PORT' => '{DB_PORT}', // 端口
    'DB_PREFIX' => '{DB_PREFIX}', // 数据库表前缀
    'AUTHCODE'=>'{AUTHCODE}',
    'COOKIE_PREFIX'=>'{COOKIE_PREFIX}',
    'DATA_CACHE_PREFIX'=>'{DATA_CACHE_PREFIX}',
    'DB_FIELDS_CACHE' => true, // 启用字段缓存
    'DB_CHARSET' => 'utf8', // 数据库编码
    'DB_DEPLOY_TYPE' => 0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE' => false, // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM' => 1, // 读写分离后 主服务器数量
    'DB_SLAVE_NO' => '', // 指定从服务器序号
    'DB_DEBUG' => TRUE, // 数据库调试模式 开启后可以记录SQL日志
);
