<?php
/**********************************
 * Created by PhpStorm
 * file_name: Index.php
 * User: funny
 * Date: 2016/3/22
 * Time: 9:35
 */
namespace app\test\controller;

class Index {
    public function index(){
        return 'hello mf';
    }

    public function hello(){
        $Test=new \my\Test();
        return $Test->sayhello();
    }
    //test api
    public function testapi(){
        $data= ['name'=>'thinkphp','url'=>'thinkphp.cn'];
        \think\Response::tramsform('data_to_xml');
        return ['data'=>$data,'code'=>1,'message'=>'操作完成'];
    }
}
 