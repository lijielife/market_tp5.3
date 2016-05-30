<?php
/**
 * Created by PhpStorm.
 * User: xun
 * Date: 14-10-27
 * Time: 下午10:18
 */
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller{

    public $userUrl= "";//获取用户信息
    protected function _initialize(){
        $this->userUrl =  C("SERVER_NAME") ."/api/index/userInfo";
        if(!isset($_SESSION['username'])) {
            $isCookie=$this->isCookie();
            if($isCookie){
                $_SESSION['username']=cookie('username');
            }else{
                session('username', null);
                cookie('username',null);
                cookie('password',null);
                $this->error ( '您尚未登录！正在跳转登录页面', U ( 'Login/login' ) );
            }
        }
    }
    protected function isCookie(){
        $username=cookie('username');
        $password=cookie('password');
        if(!$username||!$password){
            return false;
        }else{
            $model=M('AdminUser');
            $user['username'] = $username;
            $login=$model->where($user)->find();
            $sessionPassword=$login['password'];
            $pwd=$this->encrypt($sessionPassword,'E',C('ckKey'));
            if($pwd==$password){
                return true;
            }else{
                return false;
            }
        }
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

    /**
     * 模拟post进行url请求
     * @param string $url
     * @param array $post_data
     */
    function request_post($url = '', $post_data = array()) {
        if (empty($url) || empty($post_data)) {
            return false;
        }

        $o = "";
        foreach ( $post_data as $k => $v )
        {
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $post_data = substr($o,0,-1);

        $postUrl = $url;
        $curlPost = $post_data;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);

        return $data;
    }

}