<?php

namespace app\mobile\controller;
use think\Controller;
class Common extends Controller{
    /**
     * 接口地址
     */
//    public $importNews="http://192.168.150.241:8090/index.php/api/Mobile/important";//重要消息
//    public $market="http://192.168.150.241:8090/index.php/api/Mobile/market";//行情策略
//    public $calender="http://192.168.150.241:8090/index.php/api/Mobile/ecalenda";//财经日历
//    public $teacher="http://192.168.150.241:8090/index.php/api/mobile/teacher";//专家解盘分析师列表
//    public $retroaction="http://192.168.150.241:8090/index.php/api/mobile/retroaction";//建议与反馈
//    public $gain="http://192.168.150.241:8090/index.php/api/message/gain";//互动大厅
    // public $realMarket="http://www.jyd226.net/market/index/index.html";//实时行情
//    public $userMessage="http://192.168.150.241/Home/Check/userMessage";//获取用户信息
//    public $image="http://192.168.150.241/Upload/images/default/default.jpg";//默认头像地址
//    public $send="http://192.168.150.241:8090/index.php/api/message/send";//互动大厅发送消息
//    public $explain="http://192.168.150.241:8090/index.php/api/message/explain";//专家解盘消息

    public function loss($positions,$number,$baysell,$mairu_mak,$maichu_mak){//$positions持仓价 $number持仓数 $baysell买入/卖出
        //$baysell  0为买1为卖

        if($baysell==0){
            $loss=($maichu_mak-$positions)*15*$number;
        }else{
            $loss=($positions-$mairu_mak)*15*$number;
        }
        return $loss;
    }


    /**************************************资金流水*******************************************/
    public function flow($uid,$price,$name,$positions_number,$total){
        header("Content-type: application/json; charset=utf-8");
        $positions_flow=M('PositionsFlow'); //资金流水
        $flow['uid']=$uid;//关联编号
        $flow['flow_number']=$this->flowNumber($uid);//流水号
        $flow['flow_name']=$name;//业务名称
        $flow['flow_time']=time();//发生时间
        $flow['chang_capital']=$price;//变动资金
        $flow['flow_capital']=$total;//变后资金
        $flow['positions_number']=$positions_number;//关联单号
        if($positions_flow->add($flow)){
            return true;
        }else{
            return false;
        }
    }
    /************************************模拟交易id*******************************************/
    public function userNumber(){
        header("Content-type: application/json; charset=utf-8");
        $userNumber=M('UserNumber');
        $date['time']=array('EXP','IS NULL');
        $number=$userNumber->where($date)->find();
        $rand['rand']=time();
        $userNumber->add($rand);

        $user=$number['user_number'];
        $time['time']=time();
        $userNumber->where('user_number='.$user)->save($time);
        return $user;
    }
    /**********************************订单编号*********************************************/
    public function positionsNumber($uid){
        header("Content-type: application/json; charset=utf-8");
           $positionsNumber=M('PositionsNumber');
           $where['uid']=array('EXP','IS NULL');
           $list=$positionsNumber->where($where)->find();
           $number=$list['positions_number'];
           $time['time']=time();
           $id['uid']=$uid;
           if($positionsNumber->where('positions_number='.$number)->save($id)&&$positionsNumber->add($time)){
               return '226'.($number+10000);
           }else{
               return false;
           }
       }
    /**********************************流水编号******************************************/
    public function flowNumber($uid){
        header("Content-type: application/json; charset=utf-8");
        $flowNumber=M('FlowNumber');
        $where['uid']=array('EXP','IS NULL');
        $list=$flowNumber->where($where)->find();
        $number=$list['positions_number'];
        $time['time']=time();
        $id['uid']=$uid;
        if($flowNumber->where('positions_number='.$number)->save($id)&&$flowNumber->add($time)){
            return '226'.($number+10000);
        }else{
            return false;
        }
    }
    /**********************************检测用户********************************************/
    public function check($ckUid,$token){
        $url=C('SERVER_NAME')."/Desktop/Auth/checkAuth";
        $post_data['ckUid']=$ckUid;
        $post_data['token']=$token;
        $res=$this->request_post($url,$post_data);
        $array=json_decode($res,true);
        $status=$array['status'];

        if($status==1){
            return 226200;
        }else{
            return 226400;
        }
    }
    /***********************************买入卖出点数***************************************/
    public function mairu(){
        $Model =  D("Market");
        $a = $Model->order('quotetime desc')->find();
        $mairu=$a['buyprice'];
        //echo $mairu;

        return $mairu;
    }
    public function maichu(){
        $Model =  D("Market");
        $a = $Model->order('quotetime desc')->find();
        $maichu=$a['sellprice'];
        //echo $maichu;

        return $maichu;
    }
    /*************************************模拟盘开户**************************************/
    public function userCreate(){
        header("Content-type: application/json; charset=utf-8");
        $info=MI('post.body');
        $ckUid=$info['ckUid'];
        $token=$info['token'];
        $ck=$this->check($ckUid,$token);
        if($ck==226400){
            $arr=array(
                'body'  =>array(
                    'data'  => '',
                ),
                'header' =>array(
                    'status'    =>  '0',
                    'info'      =>  '非法操作',
                    'code'      =>  '226400',
                ),
            );
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            exit;
        }
        $userCapital=M('UserCapital');
        $capital['uid']=$ckUid;
        if($userCapital->where('uid='.$ckUid)->find()){
            $arr=array(
                'body'  =>array(
                    'data'  => '',
                ),
                'header' =>array(
                    'status'    =>  '0',
                    'info'      =>  '用户已存在无需再次开户',
                    'code'      =>  '226405',
                ),
            );
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            exit;
        }
        if($userCapital->add($capital)){
            $arr=array(
                'body'  =>array(
                    'data'  => '',
                ),
                'header' =>array(
                    'status'    =>  '1',
                    'info'      =>  '开户成功',
                    'code'      =>  '226202',
                ),
            );
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            exit;
        }else{
            $arr=array(
                'body'  =>array(
                    'data'  => '',
                ),
                'header' =>array(
                    'status'    =>  '0',
                    'info'      =>  '开户失败',
                    'code'      =>  '226406',
                ),
            );
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            exit;
        }

    }
    /**********************************自助加金********************************************/
    public function addMargin(){
        header("Content-type: application/json; charset=utf-8");
        $info=MI('post.body');
        $ckUid=$info['ckUid'];
        $token=$info['token'];
        $ck=$this->check($ckUid,$token);
        if($ck==226400){
            $arr=array(
                'body'  =>array(
                    'data'  => '',
                ),
                'header' =>array(
                    'status'    =>  '0',
                    'info'      =>  '非法操作',
                    'code'      =>  '226400',
                ),
            );
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            exit;
        }
        $returnArr = array(
            'status' => 0,
            'info' => '',
        );
        $capitalModel=M('UserCapital');    //用户资金
        $capitalList=$capitalModel->where('uid='.$ckUid)->find();
        if($capitalList['initial_capital']>3000000){
            $returnArr['status'] = 0;
            $returnArr['info'] = '总初始资产大于300W';
            $returnArr['data'] = '';
            echo json_encode($returnArr);
            exit;
        }
        $time=$capitalList['time'];
        if(date('Y.m.d',$time)==date('Y.m.d',time())){
            $returnArr['status'] = 0;
            $returnArr['info'] = '一天只有一次加金机会';
            $returnArr['data'] = '';
            echo json_encode($returnArr);
            exit;
        }
        $money['initial_capital']=$capitalList['initial_capital']+100000.00;
        $money['total_capital']=$capitalList['total_capital']+100000.00;
        $money['usable_margin']=$capitalList['usable_margin']+100000.00;
        $money['time']=time();
        if($capitalModel->where('uid='.$ckUid)->save($money)){
            $arr=array(
                'body'  =>array(
                    'data'  => '',
                ),
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
                    'data'  => '',
                ),
                'header' =>array(
                    'status'    =>  '0',
                    'info'      =>  '操作失败',
                    'code'      =>  '226402',
                ),
            );
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
        }
    }

    /*********************************用户活跃度***************************************/
    public function active($ckUid){
        $timeStary=mktime(0,null,null,date('n'),date('j'),date('Y'));
        $timeEnd=mktime(24,null,null,date('n'),date('j'),date('Y'));

        $countModel=M('UserCount');
        $count['uid']=$ckUid;
        $count['time']=array(array('egt',$timeStary),array('elt',$timeEnd));
        $countList=$countModel->where($count)->order('id desc')->find();
        if($countList){
            $id=$countList['id'];
            $countModel->where('id='.$id)->setInc('count',1);
        }else{
            $countModel->time=time();
            $countModel->uid=$ckUid;
            $countModel->count=1;
            $countModel->add();
        }
    }
    /**
     * 模拟post进行url请求
     * @param string $url
     * @param array $post_data
     */
    function request_post($url = '', $post_data = array()){
        if (empty($url) || empty($post_data)){
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
    /*
     *接口验证
     */
    /**
     * @param $accountID    硬件ID
     * @param $signature    验证字符串
     * @param $reqTime      请求时间
     * @param $serviceName  地址
     * @param $version      版本号
     * @param $echostr      随机字符串
     * @return bool
     */
    public function checkSignature($accountID,$signature,$reqTime,$serviceName,$version,$echostr){
//        $accountID=I('accountID');//硬件ID
//        $signature=I('digitalSign');//验证字符串
//        $reqTime=I('reqTime');//请求时间
//        $serviceName=I('serviceName');//地址
//        $version=I('version');//版本号
//        $echostr=I('echoStr');//随机字符串
        $appkey_model=M('MobileAppkey');
        $appkey_list=$appkey_model->where(array('accountID'=>$accountID))->find();

        if($appkey_list){
            $appkey=$appkey_list['appKey'];
        }else{
            $arr=array(
                'body'  =>array(
                    'data' => '',
                ),
                'header' =>array(
                    'status'    =>  '0',
                    'info'      =>  '验证失败,非法设备',
                    'code'      =>  '226400',
                ),
            );
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            exit;
        }

        $tmpArr = array($appkey, $reqTime, $serviceName,$version,$echostr);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            echo 'true';
            return true;
        }else{
            echo 'false';
            return false;
        }
    }

}

