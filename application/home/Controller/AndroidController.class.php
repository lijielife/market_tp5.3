<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 15-5-19
 * Time: 下午1:50
 */
namespace Home\Controller;
use Think\Controller;
class AndroidController extends CommonController {
    public function index(){
        $clientIp=I('clientIp');//登陆IP
        $deviceId=I('deviceId');//硬件号
        $extend=I('extend');//安卓版本
        $refId=I('refId');//应用市场ID
        $versionNumber=I('versionNumber');//应用版本
        $versionType=I('versionType');//手机型号
        $phone=I('phone');//手机号码
        $mtime=time();//获取时间

        $androidModel=M('AndroidMessage');
        $android['client_ip']=$clientIp;
        $android['device_id']=$deviceId;
        $android['extend']=$extend;
        $android['ref_id']=$refId;
        $android['version_number']=$versionNumber;
        $android['version_type']=$versionType;
        $android['phone']=$phone;
        $android['mtime']=$mtime;

        $data['device_id']=$deviceId;
        $androidList=$androidModel->where($data)->select();
        if($androidList){
            if($androidModel->where($data)->save($android)){
                $arr=array(
                    'status'  =>  1,
                    'info'   =>  '操作成功',
                    'data'  =>  '',
                );
                echo json_encode($arr);
                exit;
            }else{
                $arr=array(
                    'status'  =>  0,
                    'info'   =>  '操作失败',
                    'data'  =>  '',
                );
                echo json_encode($arr);
                exit;
            }
        }else{
            if($androidModel->add($android)){
                $arr=array(
                    'status'  =>  1,
                    'info'   =>  '操作成功',
                    'data'  =>  '',
                );
                echo json_encode($arr);
                exit;
            }else{
                $arr=array(
                    'status'  =>  0,
                    'info'   =>  '操作失败',
                    'data'  =>  '',
                );
                echo json_encode($arr);
                exit;
            }
        }
    }
    public function contact(){
        /*$arr=array(
            'owner_mobilephone'=>18912480000,//所有者电话
            'owner_name'=>'张三',//所有者姓名
            'contacts'=>array(//联系人通讯录
                array(
                    'contact_name'          =>  '李一',//联系人姓名
                    'contact_mobilephone'   =>  18888888888,//联系人手机号码
                    'contact_telephone'     =>  87777777,//联系人座机号
                ),
                array(
                    'contact_name'          =>  '李二',
                    'contact_mobilephone'   =>  18888888888,
                    'contact_telephone'     =>  87777777,
                ),
                array(
                    'contact_name'          =>  '李三',
                    'contact_mobilephone'   =>  18888888888,
                    'contact_telephone'     =>  87777777,
                ),
            ),
        );
        echo p(json_encode($arr,true));*/
        /*$contact=array(
            array(
                'contact_name'          =>  '李一',//联系人姓名
                'contact_mobilephone'   =>  18888888888,//联系人手机号码
                'contact_telephone'     =>  87777777,//联系人座机号
            ),
            array(
                'contact_name'          =>  '李二',
                'contact_mobilephone'   =>  18888888888,
                'contact_telephone'     =>  87777777,
            ),
            array(
                'contact_name'          =>  '李三',
                'contact_mobilephone'   =>  18888888888,
                'contact_telephone'     =>  87777777,
            ),
        );
        $ownerName='张三';//所有者姓名
        $ownerMobilephone=18912480003;//所有者手机号*/
        $contactModel=M('AndroidContacts');//联系人
        $ownerModel=M('AndroidOwner');//所有者
        $ownerName=CI('owner_name');//所有者姓名
        $ownerMobilephone=CI('owner_mobilephone');//所有者手机号
        $contact=CI('contacts');//通讯录
        $ownerGetTime=time();//所有者更新时间
        $ownerCreateTime=time();//所有者创建时间
        $contactGetTime=time();//联系人更新时间
        $contactCreateTime=time();//联系人创建时间

        if(!$ownerMobilephone){
            $arr=array(
                'status'  =>  0,
                'info'   =>  '没有数据',
                'data'  =>  '',
            );
            echo json_encode($arr);
            exit;
        }
        $ownerCond['owner_mobilephone']=$ownerMobilephone;
        $ownerList=$ownerModel->where($ownerCond)->find();
        if($ownerList&$ownerList['owner_status']==0){//客户存在但没上传过通讯录
            if($contact){//通讯录有内容
                foreach($contact as $val){
                    foreach($val as $k=>$v){
                        $contactDetail[$k]=$v;
                    }
                    $contactDetail['contact_create_time']=$contactCreateTime;
                    $contactDetail['contact_get_time']=$contactGetTime;
                    $contactDetail['owner_mobilephone']=$ownerMobilephone;
                    $contactResult=$contactModel->add($contactDetail);
                }
                $ownerDetail['owner_status']=1;//通讯录保存状态 1为保存过 0为没保存过
                $ownerDetail['owner_get_time']=$ownerGetTime;
                $ownerResult=$ownerModel->where($ownerCond)->save($ownerDetail);
                if($ownerResult){//操作成功
                    $arr=array(
                        'status'  =>  1,
                        'info'   =>  '操作成功',
                        'data'  =>  '',
                    );
                    echo json_encode($arr);
                    exit;
                }else{//操作失败
                    $arr=array(
                        'status'  =>  0,
                        'info'   =>  '操作失败',
                        'data'  =>  '',
                    );
                    echo json_encode($arr);
                    exit;
                }
            }else{//没有通讯录
                $arr=array(
                    'status'  =>  1,
                    'info'   =>  '操作成功',
                    'data'  =>  '',
                );
                echo json_encode($arr);
                exit;
            }
        }else if($ownerList&$ownerList['owner_status']==1){//客户存在也上传过通讯录
            $arr=array(
                'status'  =>  1,
                'info'   =>  '操作成功',
                'data'  =>  '',
            );
            echo json_encode($arr);
            exit;
        }else{//客户是新用户
            if($contact){//通讯录有内容
                foreach($contact as $val){
                    foreach($val as $k=>$v){
                        $contactDetail[$k]=$v;
                    }
                    $contactDetail['contact_create_time']=$contactCreateTime;
                    $contactDetail['contact_get_time']=$contactGetTime;
                    $contactDetail['owner_mobilephone']=$ownerMobilephone;
                    $contactResult=$contactModel->add($contactDetail);
                }
                $ownerDetail['owner_status']=1;//通讯录保存状态 1为保存过 0为没保存过
                $ownerDetail['owner_mobilephone']=$ownerMobilephone;
                $ownerDetail['owner_name']=$ownerName;
                $ownerDetail['owner_get_time']=$ownerGetTime;
                $ownerDetail['owner_create_time']=$ownerCreateTime;
                $ownerResult=$ownerModel->add($ownerDetail);
                if($ownerResult&$contactResult){//操作成功
                    $arr=array(
                        'status'  =>  1,
                        'info'   =>  '操作成功',
                        'data'  =>  '',
                    );
                    echo json_encode($arr);
                    exit;
                }else{//操作失败
                    $arr=array(
                        'status'  =>  0,
                        'info'   =>  '操作失败',
                        'data'  =>  '',
                    );
                    echo json_encode($arr);
                    exit;
                }
            }else{//没有通讯录
                $ownerDetail['owner_status']=0;//通讯录保存状态 1为保存过 0为没保存过
                $ownerDetail['owner_mobilephone']=$ownerMobilephone;
                $ownerDetail['owner_name']=$ownerName;
                $ownerDetail['owner_get_time']=$ownerGetTime;
                $ownerDetail['owner_create_time']=$ownerCreateTime;
                $ownerResult=$ownerModel->add($ownerDetail);
                if($ownerResult){//操作成功
                    $arr=array(
                        'status'  =>  1,
                        'info'   =>  '操作成功',
                        'data'  =>  '',
                    );
                    echo json_encode($arr);
                    exit;
                }else{//操作失败
                    $arr=array(
                        'status'  =>  0,
                        'info'   =>  '操作失败',
                        'data'  =>  '',
                    );
                    echo json_encode($arr);
                    exit;
                }
            }
        }

        /*$contactName=CI('contact_name');//通讯录姓名
        $contactMobilephone=CI('contact_mobilephone');//通讯录手机号
        $contactTelephone=CI('contact_telephone');//通讯录座机号*/


    }
}