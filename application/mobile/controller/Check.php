<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2015/8/13
 * Time: 14:18
 */
namespace app\mobile\controller;
class Check extends Common{
    public function secretKey(){
        $info=MI('post.body');
        $version_model=M('MobileVersion');
        $appkey_model=M('MobileAppkey');
        $accountID=$info['accountID'];
        $secretKey=$info['secretKey'];//获取secretKey
        $version_list=$version_model->where(array('secretKey'=>$secretKey))->find();
        if(!$version_list){
            $arr=array(
                'body'  =>array(
                    'data' => '',
                ),
                'header' =>array(
                    'status'    =>  '0',
                    'info'      =>  '操作失败,非法操作',
                    'code'      =>  '226400',
                ),
            );
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            exit;
        }
//        $accountID='123123dsfsd';
        $number=time().rand(1000,9999);
        $appKey=md5($number.$secretKey);
        $data['accountID']=$accountID;
        $data['appKey']=$appKey;

        $appkey_list=$appkey_model->where(array('accountID'=>$accountID))->find();
        if($appkey_list){
            $list=$appkey_model->where(array('accountID'=>$accountID))->save($data);
            if($list){
                $arr=array(
                    'body'  => $appKey,
                    'header' =>array(
                        'status'    =>  '1',
                        'info'      =>  '操作成功',
                        'code'      =>  '226200',
                    ),
                );
                echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            }else{
                $arr=array(
                    'body'  =>array(
                        'data' => '',
                    ),
                    'header' =>array(
                        'status'    =>  '0',
                        'info'      =>  '操作失败请重试',
                        'code'      =>  '226400',
                    ),
                );
                echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            }
        }else{
            $list=$appkey_model->where(array('accountID'=>$accountID))->add($data);
            if($list){
                $arr=array(
                    'body'  => $appKey,
                    'header' =>array(
                        'status'    =>  '1',
                        'info'      =>  '操作成功',
                        'code'      =>  '226200',
                    ),
                );
                echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            }else{
                $arr=array(
                    'body'  =>array(
                        'data' => '',
                    ),
                    'header' =>array(
                        'status'    =>  '0',
                        'info'      =>  '操作失败请重试',
                        'code'      =>  '226400',
                    ),
                );
                echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            }
        }

    }
}