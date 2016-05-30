<?php
return array(
	//'配置项'=>'配置值'
    'TMPL_L_DELIM'=>'<{',
    'TMPL_R_DELIM'=>'}>',
    //数据库配置信息
//    'DB_TYPE'   => 'mysql', // 数据库类型
//    'DB_HOST'   => 'localhost', // 服务器地址
//    'DB_NAME'   => 'market', // 数据库名
//    'DB_USER'   => 'root', // 用户名
//    'DB_PWD'    => '', // 密码
//    'DB_PORT'   => 3306, // 端口
//    'DB_PREFIX' => 'market_', // 数据库表前缀
//    'DB_CHARSET'=> 'utf8', // 字符集


    'TMPL_PARSE_STRING'     =>array(
        '__JS__' =>__ROOT__.'/Application/Admin/Common/assets/js',
        '__CSS__' => __ROOT__.'/Application/Admin/Common/assets/css',
        '__IMG__'=>__ROOT__.'/Application/Admin/Common/Image',
        //'__HTML__'=>'/admin/Application/Admin/View/Public',
        '__HTML__'=>'./Index',
    ),
    //'LAYOUT_ON'=>true,//模板布局
    //'LAYOUT_NAME'=>'layout',
    'URL_MODEL' => '2',
    'ckKey'=>'as5d4ASA!@#65AOSJ',
);