<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 15-5-27
 * Time: 下午3:31
 */
namespace Home\Model;
Class MarketModel extends CommonModel{


    protected $dbName='test';//如果配置了全局配置,mongodb数据库和mysql数据库名称不一样的话,必须配置此项

    protected $trueTableName = 'tmp_AG1000';

}