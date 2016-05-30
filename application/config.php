<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

return [
    'url_route_on' => true,
    'log'          => [
        'type' => 'trace', // 支持 socket trace file
    ],
//    'default_return_type'=>'json',
    'config_test' =>'配置测试',
    'URL_MODEL' => '2',//URL模式
    'SERVER_NAME' => 'http://192.168.150.132:8080',
    'SERVER_IP'   => '192.168.150.132',
    'default_return_type' =>'json',
    'URL_CASE_INSENSITIVE' => false,
    'TMPL_EXCEPTION_FILE'   =>  APP_PATH.'Common/Tpl/think_exception.tpl',// 异常页面的模板文件
    'lang_switch_on' => true,   // 开启语言包功能
    'lang_list'     => ['zh-cn'], // 支持的语言列表
];
