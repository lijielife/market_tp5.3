<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 15-5-28
 * Time: 下午5:12
 */
namespace Mobile\Model;
use Think\Model\MongoModel;
Class CommonModel extends MongoModel{
    protected $connection = array(
        //数据库配置信息
        'DB_DEPLOY_TYPE' => 1, // 数据库部署方式 0 集中式（单一服务器） 1 分布式（主从服务器）
        'DB_TYPE'   => 'mongo', // 数据库类型
        'DB_HOST'   => '192.168.150.132,192.168.150.132', // 服务器地址
        'DB_NAME'   => 'test', // 数据库名
        'DB_USER'   => 'phpServer', // 用户名
        'DB_PWD'    => '123456', // 密码
        'DB_PORT'   => '30000,40000', // 端口
        'db_charset' => 'utf8',
    );
    //protected $dbName='admin';//如果配置了全局配置,mongodb数据库和mysql数据库名称不一样的话,必须配置此项

}