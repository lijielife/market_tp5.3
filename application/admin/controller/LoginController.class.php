<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller{
    public function index(){
        if(isset($_SESSION['username'])) {
            //跳转到首页
            $this->redirect("Capital/index",array());
        }else{
            //跳转到登录页面
            $this->display('Login/login');
        }
    }
    public function login(){
        $time=I('year');
        if(!isset($_POST['username']))
            //展示登陆页面
            $this->display('Login/login');
        else{
            //获取参数
            $name=$_POST['username'];
            $password=md5($_POST['password']);
            $model=M('AdminUser');
            $user['username'] = $name;
            $user['password']=$password;
            $login=$model->where($user)->find();
            $verify = new \Think\Verify();
            if (!$verify->check(I('verify'))){
                     $this->error("验证码错误",U('index'));
            }else{
                //执行登录
                if($login){
                    if($time=='year'){
                        $pwd=$this->encrypt($password,'E',C('ckKey'));
                        cookie('username',$name,360000);
                        cookie('password',$pwd,360000);
//                        echo cookie('username')."<br>".cookie('password');
                    }
                    $data['ip']=get_client_ip();
                    $data['time']=time();
                    $model->where($user)->setInc("count");
                    $model->where($user)->save($data);

                    $_SESSION['username']=$name;
                    $this->success('登陆成功！',U('Capital/index'));
                }else{
                    $this->error('登录失败！');
                }
            }
        }
    }

    public function logout(){
        session('username', null);
        cookie('username',null);
        cookie('password',null);
        $this->success('登出成功！',U('Login/login'));
    }
    public function verify(){
        $Verify = new \Think\Verify();
        $Verify->fontSize = 20; //验证码尺寸
        $Verify->length   = 4;  //验证码位数
        $Verify->imageW=135;   //验证码宽度
        $Verify->imageH=40;   //验证码高度
        $Verify->useNoise = false;
        $Verify->entry();
    }
    /**
     * 加密解密函数
     *
     * @param  [type] $string    [要加密的字符串]
     * @param  [type] $operation [判断是加密还是解密，E表示加密，D表示解密]
     * @param  string $key       [密匙]
     * @return [type]            [description]
     */
    function encrypt($string,$operation,$key=''){

        $key=md5($key);

        $key_length=strlen($key);

        $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;

        $string_length=strlen($string);

        $rndkey=$box=array();

        $result='';

        for($i=0;$i<=255;$i++){

            $rndkey[$i]=ord($key[$i%$key_length]);

            $box[$i]=$i;

        }

        for($j=$i=0;$i<256;$i++){

            $j=($j+$box[$i]+$rndkey[$i])%256;

            $tmp=$box[$i];

            $box[$i]=$box[$j];

            $box[$j]=$tmp;

        }

        for($a=$j=$i=0;$i<$string_length;$i++){

            $a=($a+1)%256;

            $j=($j+$box[$a])%256;

            $tmp=$box[$a];

            $box[$a]=$box[$j];

            $box[$j]=$tmp;

            $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));

        }

        if($operation=='D'){

            if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8)){

                return substr($result,8);

            }else{

                return'';

            }

        }else{

            return str_replace('=','',base64_encode($result));

        }

    }
}