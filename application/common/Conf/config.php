<?php
return array(
    //'配置项'=>'配置值'
    'DEFAULT_MODULE'        =>  'Home',  // 默认模块
    'MODULE_ALLOW_LIST'     =>  array('Home', 'Admin','Desktop','Mobile'),    // 允许访问的模块列表
    'URL_MODEL' => '2',//URL模式
    //数据库配置信息
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => 'localhost', // 服务器地址
    'DB_NAME'   => 'market', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'root', // 密码
    'DB_PORT'   => 3306, // 端口
    'DB_PREFIX' => 'market_', // 数据库表前缀
    'DB_CHARSET'=> 'utf8', // 字符集

    'SERVER_NAME' => 'http://192.168.150.132:8080',
    'SERVER_IP'   => '192.168.150.132',

    'TMPL_EXCEPTION_FILE'   =>  APP_PATH.'Common/Tpl/think_exception.tpl',// 异常页面的模板文件
);