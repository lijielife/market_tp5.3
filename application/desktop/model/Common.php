<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 15-5-28
 * Time: 下午5:12
 */
namespace app\desktop\model;
use think\Model;
Class Common extends Model{
    protected $connection = array(
        //数据库配置信息
        'deploy' => 1, // 数据库部署方式 0 集中式（单一服务器） 1 分布式（主从服务器）
        'type'   => 'mongo', // 数据库类型
        'hostname'   => '192.168.150.132', // 服务器地址
        'database'   => 'test', // 数据库名
        'username'   => 'phpServer', // 用户名
        'password'    => '123456', // 密码
        'hostport'   => '30000,30000', // 端口
        'charset' => 'utf8',
    );
    protected $dbName='test';//如果配置了全局配置,mongodb数据库和mysql数据库名称不一样的话,必须配置此项

    protected $trueTableName = 'tmp_AG1000';


}