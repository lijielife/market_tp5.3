<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 15-5-4
 * Time: 上午9:42
 */
namespace Home\Controller;
use Think\Controller;
class CheckController extends CommonController {
    public function login($phone,$password){
        //$phone=I('phone');
        //$password=I('password');
        $user=M('UserMessage');
        //$pass=md5($password);
        $where['mobilephone']=$phone;
        $where['password']=$password;
        $userlist=$user->where($where)->find();

        if($userlist){
            S('username'.$userlist['id'],$userlist['username'],300);
            S('uid'.$userlist['id'],300);


            $ckUid=$userlist['id'];//用户编号
            $rand = md5(time().mt_rand(0,1000));//32位随机数
            $status=1;
            $userMessage['status']=$status;
            $userMessage['token']=$rand;
            if($user->where('id='.$ckUid)->save($userMessage)){
                $arr=array(
                    'ckUid'     =>  $ckUid,//用户id
                    'token'     =>  $rand,//32位令牌
                    'status'    =>  $status,//用户状态
                );
                $userMs['last_login_ip']=get_client_ip();
                $userMs['last_login_time']=time();
                $user->where('id='.$ckUid)->save($userMs);
                $user->where('id='.$ckUid)->setInc('login_count',1);
                echo json_encode($arr);
            }else{
                $arr=array(
                    'status'  =>  0,
                    'info'   =>  '登陆失败 未知错误 请重试',
                    'data'  =>  '',
                );
                echo json_encode($arr);
                exit;
            }
        }else{
           $arr=array(
               'state'  =>  0,
               'info'   =>  '用户名或密码错误',
               'data'  =>  '',
           );
           echo json_encode($arr);
           exit;
        }
    }
    public function logout(){
        $ckUid=I('ckUid');
        $token=I('token');
        $user=M('UserMessage');
        $where['id']=$ckUid;
        $where['token']=$token;
        $userlist=$user->where($where)->find();
        if($userlist){
            $userMessage['status']=0;
            $userMessage['token']='';
            if($user->where('id='.$ckUid)->save($userMessage)){
                $arr=array(
                    'status'  =>  1,
                    'info'   =>  '退出成功',
                    'data'  =>  '',
                );
                echo json_encode($arr);
                exit;
            }else{
                $arr=array(
                    'status'  =>  0,
                    'info'   =>  '退出失败 未知错误 请重试',
                    'data'  =>  '',
                );
                echo json_encode($arr);
                exit;
            }
        }else{
            $arr=array(
                'status'  =>  0,
                'info'   =>  'id与令牌不符',
                'data'  =>  '',
            );
            echo json_encode($arr);
            exit;
        }

    }
    /******************************注册********************************/
    public function register(){
        $user=M('UserMessage');
        $phone=I('phone');
        $password=md5(I('password'));
        $verify=I('verify');
        if($this->checkVerify($verify,$phone)){
            $userList=$user->where('mobilephone='.$phone)->find();
            if($userList){
                $arr=array(
                    'status'  =>  0,
                    'info'   =>  '已经注册过',
                    'data'  =>  '',
                );
                echo json_encode($arr);
                exit;
            }else{
                $number=$this->userNumber()+1000;
                $username=substr($phone,0,3).'****'.substr($phone,-4);
                $message['mobilephone']=$phone;
                $message['last_login_ip']=get_client_ip();
                $message['create_ip']=get_client_ip();
                $message['last_login_time']=time();
                $message['create_time']=time();
                $message['password']=$password;
                $message['username']=$username;
                $message['simulate_id']='226'.$number;
                $message['level']='网友';
                if($user->add($message)){
                    $this->login($phone,$password);
                }
            }
        }else{
            $arr=array(
                'status'  =>  0,
                'info'   =>  '验证码错误',
                'data'  =>  '',
            );
            echo json_encode($arr);
            exit;
        }

    }
    /**************************检测验证码*****************************/
    public function checkVerify($verify,$phone){
        //$verify=I('verify');
        //$phone=I('phone');
        $smsModel=M('Msg');
        $msn['msg_reg']=$verify;
        $msn['phone']=$phone;
        $smsList=$smsModel->where($msn)->find();
        if($smsList){
            //验证码符合
            return true;
        }else{
            return false;
        }
    }
    /*******************************直播间*******************************/
    public function liveUser(){
        $user=M('UserMessage');
        $id=I('ckUid');
        $token=I('token');
        $live['id']=$id;
        $live['token']=$token;
        $liveList=$user->where($live)->find();
        if($liveList){
            $arr=array(
                'liveUser'  =>  $liveList['username'],
                'liveImage' =>  'http://192.168.150.241/Upload'.$liveList['image'],
            );
            echo json_encode($arr);
        }else{
            $arr=array(
                'status'    =>  0,
                'info'      =>  '用户不存在',
                'data'      =>  '',
            );
            echo json_encode($arr);
        }
    }
    /*******************************用户******************************/
    public function userMessage(){
        $uid=I('uid');
        $model=M('UserMessage');
        $map['id'] = array('in',$uid);
        $list=$model->where( $map )->select();
        if(!$list){
            echo 'false';
            exit;
        }
        foreach($list as $key=>$val){
            $data[$key]['image']='http://192.168.150.241/Upload'.$val['image'];
            $data[$key]['username']=$val['username'];
            $data[$key]['uid']=$val['id'];
        }
        echo json_encode($data);
    }
}