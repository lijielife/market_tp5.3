<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 15-5-7
 * Time: 下午12:24
 */
namespace Home\Controller;
use Think\Controller;
class SetController extends CommonController{
    public function index(){
        $ckUid=I('ckUid');
        $token=I('token');
        if(!$ckUid||!$token){
            echo "<div style='color: red;'>请重新登录</div>";
            exit;
        }
        $ck=$this->check($ckUid,$token);
        if($ck==226400){
            echo "<div style='color: red;'>请重新登录</div>";
            exit;
        }
        $user=M('userMessage');
        $date['id']=$ckUid;
        $date['token']=$token;
        $userList=$user->where($date)->find();
        if(!$userList){
            $arr=array(
                'status'  =>  0,
                'info'   =>  '请重新登录',
                'data'  =>  ''
            );
            echo json_encode($arr);
            exit;
        }
        $this->assign('level',$userList['level']);
        $this->assign('simulate_id',$userList['simulate_id']);
        $this->assign('username',$userList['username']);
        $this->assign('email',$userList['email']);
        $this->assign('image',$userList['image']);
        $this->assign('ckUid',$ckUid);
        $this->assign('token',$token);
        $this->display('set/set');

    }
    public function save($ckUid,$token){
        if(!$ckUid||!$token){
            echo "<div style='color: red;'>请重新登录</div>";
            exit;
        }
        $ck=$this->check($ckUid,$token);
        if($ck==226400){
            echo "<div style='color: red;'>请重新登录</div>";
            exit;
        }
        $user=M('userMessage');
        $date['id']=$ckUid;
        $date['token']=$token;
        $userList=$user->where($date)->find();
        if(!$userList){
            $arr=array(
                'status'  =>  0,
                'info'   =>  '请重新登录',
                'data'  =>  ''
            );
            echo json_encode($arr);
            exit;
        }
        $this->assign('level',$userList['level']);
        $this->assign('simulate_id',$userList['simulate_id']);
        $this->assign('username',$userList['username']);
        $this->assign('email',$userList['email']);
        $this->assign('image',$userList['image']);
        $this->assign('ckUid',$ckUid);
        $this->assign('token',$token);
        $this->display('set/set');

    }

    public function upload(){
        $ckUid=I('ckUid');
        $token=I('token');
        $ck=$this->check($ckUid,$token);
        if($ck==226400){
            echo "<div style='color: red;'>请重新登录</div>";
            exit;
        }
        $user=M('userMessage');
        $upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','bmp','psd','tiff','swf','svg');// 设置附件上传类型
        $upload->rootPath  =     './Upload' ;
        $upload->savePath  =     '/images/'.$ckUid.'/'; // 设置附件上传目录
        $upload->subName   =     array('date','ymd');
        // 上传文件
        $info   =   $upload->upload();
        $image=$info['photo']['savepath'].$info['photo']['savename'];
        $user->image=$image;
        $user->where('id='.$ckUid)->save();

        $this->redirect('index',array('ckUid'=>$ckUid,'token'=>$token));


    }
    public function username(){
        $username=I('username');
        $ckUid=I('ckUid');
        $token=I('token');
        $ck=$this->check($ckUid,$token);
        if($ck==226400){
            echo "<div style='color: red;'>请重新登录</div>";
            exit;
        }
        if(!$username){
            echo "<div style='color: red;'>昵称不能为空</div>";
            exit;
        }
        $user=M('userMessage');
        $user->username=$username;
        $model=$user->where('id='.$ckUid)->save();
        if($model){
            echo "<div style='color: red;'>昵称修改成功</div>";
        }else{
            echo "<div style='color: red;'>昵称与原昵称相同！</div>";
        }

    }
    public function email(){
        $email=I('email');
        $ckUid=I('ckUid');
        $token=I('token');
        $ck=$this->check($ckUid,$token);
        if($ck==226400){
            echo "<div style='color: red;'>请重新登录</div>";
            exit;
        }
        if(!$email){
            echo "<div style='color: red;'>邮箱不能为空</div>";
            exit;
        }
        if(!$this->is_valid_email($email)){
            echo "<div style='color: red;'>邮箱格式不正确</div>";
            exit;
        }
        $user=M('userMessage');
        $user->email=$email;
        $model=$user->where('id='.$ckUid)->save();
        if($model){
            echo "<div style='color: red;'>邮箱修改成功</div>";
        }else{
            echo "<div style='color: red;'>邮箱与原邮箱相同！</div>";
        }
    }
//又一个PHP验证邮箱格式的代码，从代码看是基于正则表达式，本函数除了验证电子邮件地址外，还可以检查邮件域所属 DNS 中的 MX 记录，使邮件验证功能更加强大，当需要此项功能时，你需要将函数参数$test_mx 设置为true。
    function is_valid_email($email, $test_mx = false)
{
    if(eregi("^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email))
        if($test_mx)
        {
            list($username, $domain) = split("@", $email);
            return getmxrr($domain, $mxrecords);
        }
        else
            return true;
    else
        return false;
}

    public function password(){
        $pwd=I('pwd');
        $newpwd=I('newpwd');
        $renewpwd=I('renewpwd');

        $ckUid=I('ckUid');
        $token=I('token');
        $ck=$this->check($ckUid,$token);
        if($ck==226400){
            echo "<div style='color: red;'>请重新登录</div>";
            exit;
        }
        if($pwd==0){
            echo "<div style='color: red;'>请输入您的旧密码</div>";
            exit;
        }
        if($newpwd==1){
            echo "<div style='color: red;'>请输入您的新密码</div>";
            exit;
        }
        if($renewpwd==2){
            echo "<div style='color: red;'>请再次输入您的新密码</div>";
            exit;
        }
        if($pwd==$newpwd){
            echo "<div style='color: red;'>新旧密码相同，请更改</div>";
            exit;
        }
        if($newpwd!=$renewpwd){
            echo "<div style='color: red;'>新的密码2次输入不相同</div>";
            exit;
        }
        if($this->isChinese($newpwd)){
            echo "<div style='color: red;'>不能含有中文</div>";
            exit;
        }
        if(strlen($newpwd)<3){
            echo "<div style='color: red;'>长度不能小于3位</div>";
            exit;
        }

        $user=M('userMessage');
        $date['id']=$ckUid;
        $date['token']=$token;
        $userList=$user->where($date)->find();
        $password=$userList['password'];
        if(md5($pwd)!=$password){
            echo "<div style='color: red;'>原始密码错误</div>";
            exit;
        }
        $user->password=md5($newpwd);
        $model=$user->where('id='.$ckUid)->save();
        if($model){
            echo "<div style='color: red;'>修改成功</div>";
            exit;
        }else{
            echo "<div style='color: red;'>修改失败</div>";
            exit;
        }
    }

}