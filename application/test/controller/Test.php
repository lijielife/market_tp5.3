<?php
/**********************************
 * Created by PhpStorm
 * file_name: Test.php
 * User: funny
 * Date: 2016/3/22
 * Time: 16:19
 */
namespace app\test\controller;
use think\Controller;
class Test extends Controller{
    public function _initialize(){
        echo 'init<br/>';
    }

    public function hello(){
        return 'hello';
    }
    public function data(){
        \think\Debug::remark('begin');
        echo 123456789*987654321;
        \think\Debug::remark('end');
        echo \think\Debug::getRangeTime('begin','end').'s';
    }
    public function log_test(){
        \think\Log::init(['type'=>'File','path'=>APP_PATH.'logs/']);
        \think\Log::record('日志谢谢');
    }
}
 