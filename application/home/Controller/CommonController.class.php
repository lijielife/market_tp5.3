<?php

namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller{
    public function loss($positions,$number,$baysell,$mairu_mak,$maichu_mak){//$positions持仓价 $number持仓数 $baysell买入/卖出
        //$baysell  0为买1为卖

        if($baysell==0){
            $loss=($maichu_mak-$positions)*15*$number;
        }else{
            $loss=($positions-$mairu_mak)*15*$number;
        }
        return $loss;
    }
    /**
     * 打印输出
     * @param  [type] $arr [description]
     * @return [type]      [description]
     */
    /************************************统一收延期费*******************************************/
    public function overnight(){
        $positionsFlow=M('PositionsFlow'); //资金流水
        $detailModel=M('PositionsDetail'); //持仓明细
        $capitalModel=M('UserCapital');    //用户资金
        $where['freeze_uccupy']=array('EXP','IS NULL');
        $detailList=$detailModel->where($where)->order('id desc')->select();//持仓明细
        $detailCount=$detailModel->where($where)->count();//计算负荷条件的仓数
        // $overnight=$maichu_mak*15*$number*0.0002*$day;//过夜费 卖出行情*15*手数*0.0002*天数
        //$timeStary=mktime(4,null,null,date(n,$time),date(j,$time),date(Y,$time));
        $timeEnd=mktime(4,null,null,date(n),date(j),date(Y));

        for($count=0;$count<$detailCount;$count++){
            $timeStary=$detailList[$count]['time'];//持仓时间
            $positionsPrice=$detailList[$count]['positions_price'];//持仓价
            $number=$detailList[$count]['number'];//数量
            $uid=$detailList[$count]['uid'];
            $price=$positionsPrice*15*$number*0.0002;//延期费
            $positionsNumber=$detailList[$count]['positions_number'];

            $capitalList=$capitalModel->where('uid='.$uid)->select();//用户资金
            $totalCapital=$capitalList[0]['total_capital'];//总资产
            $totalLoss=$capitalList[0]['total_profit_loss'];//总盈亏
            $uccupy=$capitalList[0]['usable_margin']-$price;//可用保证金
            $total=$totalCapital-$price;
            if($timeStary<$timeEnd){
                $capital['total_capital']=$total;//总资产
                $capital['total_profit_loss']=$totalLoss-$price;//总盈亏
                $capital['usable_margin']=$uccupy;//可用保证金
                $flow['uid']=$uid;//关联编号
                $flow['flow_number']=$this->flowNumber($uid);//流水号
                $flow['flow_name']='延期费';//业务名称
                $flow['flow_time']=time();//发生时间
                $flow['chang_capital']=0-$price;//变动资金
                $flow['flow_capital']=$total;//变后资金
                $flow['positions_number']=$positionsNumber;//关联单号
                if($positionsFlow->add($flow)&&$capitalModel->where('uid='.$uid)->save($capital)){
                    return true;
                }else{
                    return false;
                }
            }
        }
    }
    /**********************************统一指价超时********************************************/
    public function overTime(){

        $capitalModel=M('UserCapital');    //用户资金
        $detailModel=M('PositionsDetail'); //持仓明细
        $todayPrice=M('TodayPrice');       //当日指价
        $historyPrice=M('HistoryPrice');   //历史指价

        $where['uccupy']='';
        $detailList=$detailModel->where($where)->select();//持仓明细
        $detailCount=$detailModel->where($where)->count();//计算负荷条件的仓数

        for($count=0;$count<$detailCount;$count++){
            $uid=$detailList[$count]['uid'];
            $positionsNumber=$detailList[$count]['positions_number'];
            $capitalList=$capitalModel->where('uid='.$uid)->select();//用户资金

            $commission=$detailList[$count]['commission'];                          //手续费率
            $number=$detailList[$count]['number'];                                  //手数
            $positionsPrice=$detailList[$count]['$positions_price'];
            $usableMargin=$capitalList[$count]['usable_margin'];                   //可用保证金
            $freezeMargin=$capitalList[$count]['freeze_margin'];                   //冻结保证金

            /******************计算*******************/

            $charge=$positionsPrice*15*0.0008*$number;                                       //买入手续费 买入点数*15*0.0008*手数
            $uccupy=$positionsPrice*15*$commission*$number;                                  //这一单保证金 买入点数*15*0.08*手数

            $freeze=$charge+$uccupy;//这一单冻结资金  冻结手续费+冻结保证金
            $usable=$usableMargin+$freeze;//可用保证金 历史可用保证金+这一单冻结保证金
            $freezeMargin=$freezeMargin-$freeze;//冻结保证金 历史冻结保证金-这一单冻结保证金
            /******************修改历史指价单******************/
            $history['status']='超时';//状态
            /*******************修改当日指价表****************/
            $todayPrice['status']='超时';//修改状态
            /*******************用户资金变化****************/
            $capital['usable_margin']=$usable;//可用保证金
            $capital['freeze_margin']=$freezeMargin;//冻结资金
            /*******************写入数据库******************/
            if($todayPrice->where('positions_number='.$positionsNumber)->save($todayPrice)&&$historyPrice->where('positions_number='.$positionsNumber)->save($history)&&$detailModel->where('positions_number='.$positionsNumber)->delete()&&$capitalModel->where('uid='.$uid)->save($capital)){
                if($count==($detailCount-1)){
                    $arr=array(
                        'status'  =>  1,
                        'info'   =>  '操作成功',
                        'data'  =>  '',
                    );
                    echo json_encode($arr);
                }
            }else{
                $arr=array(
                    'status'  =>  0,
                    'info'   =>  '操作失败',
                    'data'  =>  '',
                );
                echo json_encode($arr);
            }

        }


    }

    /**************************************资金流水*******************************************/
    public function flow($uid,$price,$name,$positions_number,$total){
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
        $user=M('UserMessage');
        $where['id']=$ckUid;
        $where['token']=$token;
        $userlist=$user->where($where)->find();
        if($userlist){
            return 226200;
        }else{
            return 226400;
        }
    }
    public function totalCheck(){


        $returnArr = array(
            'status' => 0,
            'info' => '',
        );

        $ckUid=I('ckUid');
        $token=I('token');
        $user=M('UserMessage');
        $where['id']=$ckUid;
        $where['token']=$token;
        $userlist=$user->where($where)->find();

        if($userlist){
            $returnArr['status'] = 1;
            $returnArr['info']  = '验证成功';
            $returnArr['data'] = '';
        }else{
            $returnArr['status'] = 0;
            $returnArr['info']  = 'id与令牌不符';
            $returnArr['data'] = '';
        }

        echo json_encode($returnArr);
    }
    /***********************************买入卖出点数***************************************/
    public function mairu(){
        /*$url='http://www.jyd226.net/market/index/index.html';
        $html = file_get_contents($url);
        $arrayBay=json_decode($html);
        $arr=get_object_vars($arrayBay[0]);
        $mairu=$arr['Buy'];*/
        $Model =  D("market");
        $a = $Model->order('quotetime desc')->find();
        $mairu=$a['buyprice'];
        //echo $mairu;

        return $mairu;
    }
    public function maichu(){
        /*$url='http://www.jyd226.net/market/index/index.html';
        $html = file_get_contents($url);
        $arrayBay=json_decode($html);
        $arr=get_object_vars($arrayBay[0]);
        $maichu=$arr['Sell'];*/
        $Model =  D("market");
        $a = $Model->order('quotetime desc')->find();
        $maichu=$a['sellprice'];
        //echo $maichu;

        return $maichu;
    }
    /*************************************模拟盘开户**************************************/
    public function userCreate(){
        $ckUid=I('ckUid');
        $token=I('token');
        $ck=$this->check($ckUid,$token);
        if($ck==226400){
            $arr=array(
                'status'  =>  0,
                'info'   =>  '非法操作',
                'data'  =>  ''
            );
            echo json_encode($arr);
            exit;
        }
        $userCapital=M('UserCapital');
        $capital['uid']=$ckUid;
        if($userCapital->where('uid='.$ckUid)->find()){
            $arr=array(
                'status'  =>  0,
                'info'   =>  '用户已存在无需再次开户',
                'data'  =>  ''
            );
            echo json_encode($arr);
            exit;
        }
        if($userCapital->add($capital)){
            $arr=array(
                'status'  =>  1,
                'info'   =>  '开户成功',
                'data'  =>  ''
            );
            echo json_encode($arr);
            exit;
        }else{
            $arr=array(
                'status'  =>  0,
                'info'   =>  '开户失败',
                'data'  =>  ''
            );
            echo json_encode($arr);
            exit;
        }

    }
    /**********************************自助加金********************************************/
    public function addMargin(){
        $ckUid=I('ckUid');
        $token=I('token');
        $ck=$this->check($ckUid,$token);
        if($ck==226400){
            $arr=array(
                'status'  =>  0,
                'info'   =>  '非法操作',
                'data'  =>  ''
            );
            echo json_encode($arr);
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
                'status'  =>  1,
                'info'   =>  '操作成功',
                'data'  =>  '',
            );
            echo json_encode($arr);
        }else{
            $arr=array(
                'status'  =>  0,
                'info'   =>  '操作失败',
                'data'  =>  '',
            );
            echo json_encode($arr);
        }
    }
    /**************************************注册发送验证码********************************************/
    public function setMsg(){
        $phone=I('phone');
        $smsModel=M('Msg');

        $data['mobilephone']=$phone;
        $check=M('UserMessage')->where($data)->find();
        if($check){
            $arr=array(
                'status'  =>  0,
                'info'   =>  '已注册 请直接登录',
                'data'  =>  '',
            );
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            exit;
        }else{
            $smslist=$smsModel->where('phone='.$phone)->find();
            if($smslist){//已经发送过一次
                $time=$smslist['time_reg'];
                if((time()-$time)>900){
                    //再次发送验证码
                    $verify = mt_rand(100000, 999999);
                    $contents = '您的验证码：' . $verify . '，验证码15分钟内有效，请勿外泄。金裕道24小时热线:4000360226【金裕道贵金属】';
                    $res =$this->sendMessage($phone, $contents);
                    if($res==1){
                        $msg['phone']=$phone;
                        $msg['msg_reg']=$verify;
                        $msg['time_reg']=time();
                        if($smsModel->where('phone='.$phone)->save($msg)){
                            $arr=array(
                                'status'  =>  1,
                                'info'   =>  '操作成功',
                                'data'  =>  '',
                            );
                            echo json_encode($arr);
                        }else{
                            $arr=array(
                                'status'  =>  0,
                                'info'   =>  '发送失败请重试',
                                'data'  =>  '',
                            );
                            echo json_encode($arr);
                            exit;
                        }
                    }else{
                        $arr=array(
                            'status'  =>  0,
                            'info'   =>  '发送失败请重试',
                            'data'  =>  '',
                        );
                        echo json_encode($arr);
                        exit;
                    }
                }else{
                    $arr=array(
                        'status'  =>  0,
                        'info'   =>  '请注意查看手机，如还没收到请15分钟后重试',
                        'data'  =>  '',
                    );
                    echo json_encode($arr);
                    exit;
                }
            }else{
                //首次注册发送短信
                $verify = mt_rand(100000, 999999);
                $contents = '您的验证码：' . $verify . '，验证码15分钟内有效，请勿外泄。金裕道24小时热线:4000360226【金裕道贵金属】';
                $res =$this->sendMessage($phone, $contents);
                if($res==1){
                    $msg['phone']=$phone;
                    $msg['msg_reg']=$verify;
                    $msg['time_reg']=time();
                    if($smsModel->add($msg)){
                        $arr=array(
                            'status'  =>  1,
                            'info'   =>  '操作成功',
                            'data'  =>  '',
                        );
                        echo json_encode($arr);
                    }else{
                        $arr=array(
                            'status'  =>  0,
                            'info'   =>  '发送失败请重试',
                            'data'  =>  '',
                        );
                        echo json_encode($arr);
                    }
                }else{
                    $arr=array(
                        'status'  =>  0,
                        'info'   =>  '发送失败请重试',
                        'data'  =>  '',
                    );
                    echo json_encode($arr);
                    exit;
                }
            }
        }



    }
    /***********************************修改密码发送验证码*********************************************/
    public function revisePw(){
        $phone=I('phone');
        $smsModel=M('Msg');
        $smslist=$smsModel->where('phone='.$phone)->find();
        $message['phone']=$phone;
        $message['msg_revise']=array('EXP','IS NULL');
        $msList=$smsModel->where($message)->find();
        $time=$smslist['time_revise'];
        if($smslist){
        //已经注册过
            if($msList){
                //还没发送过验证码
                //发送验证码
                $verify = mt_rand(100000, 999999);
                $contents = '您的验证码：' . $verify . '，验证码15分钟内有效，请勿外泄。金裕道24小时热线:4000360226【金裕道贵金属】';
                $res =$this->sendMessage($phone, $contents);
                if($res==1){
                    $msg['msg_revise']=$verify;
                    $msg['time_revise']=time();
                    if($smsModel->where('phone='.$phone)->save($msg)){
                        $arr=array(
                            'status'  =>  1,
                            'info'   =>  '操作成功',
                            'data'  =>  '',
                        );
                        echo json_encode($arr);
                    }else{
                        $arr=array(
                            'status'  =>  0,
                            'info'   =>  '发送失败请重试',
                            'data'  =>  '',
                        );
                        echo json_encode($arr);
                        exit;
                    }
                }else{
                    $arr=array(
                        'status'  =>  0,
                        'info'   =>  '发送失败请重试',
                        'data'  =>  '',
                    );
                    echo json_encode($arr);
                    exit;
                }
            }else{
                //已经发送过
                if((time()-$time)>900){
                    //如果间隔大于15分钟
                    //发送验证码
                    $verify = mt_rand(100000, 999999);
                    $contents = '您的验证码：' . $verify . '，验证码15分钟内有效，请勿外泄。金裕道24小时热线:4000360226【金裕道贵金属】';
                    $res =$this->sendMessage($phone, $contents);
                    if($res==1){
                        $msg['msg_revise']=$verify;
                        $msg['time_revise']=time();
                        if($smsModel->where('phone='.$phone)->save($msg)){
                            $arr=array(
                                'status'  =>  1,
                                'info'   =>  '操作成功',
                                'data'  =>  '',
                            );
                            echo json_encode($arr);
                        }else{
                            $arr=array(
                                'status'  =>  0,
                                'info'   =>  '发送失败请重试',
                                'data'  =>  '',
                            );
                            echo json_encode($arr);
                            exit;
                        }
                    }else{
                        $arr=array(
                            'status'  =>  0,
                            'info'   =>  '发送失败请重试',
                            'data'  =>  '',
                        );
                        echo json_encode($arr);
                        exit;
                    }
                }else{
                    $arr=array(
                        'status'  =>  0,
                        'info'   =>  '请注意查看手机，如还没收到请15分钟后重试',
                        'data'  =>  '',
                    );
                    echo json_encode($arr);
                    exit;
                }
            }
        }else{
            //还未注册
            echo 226409;
            exit;
        }
    }
    /*********************************短信接口**************************************/
    function sendMessage($phones, $contents, $scode="", $setTime=""){
        $curlPost = "username="."金裕道贵金属"."&pwd="."bFhJHi#JKW!E"."&phones=". $phones ."&contents=".$contents."&scode=". $scode ."&setTime=" . $setTime;

        $ch = curl_init();//初始化curl
        curl_setopt($ch,CURLOPT_URL,'http://yyqd.shareagent.cn:888/sdk/service.asmx/sendMessage');//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $bodytag = curl_exec($ch);//运行curl
        curl_close($ch);
        // print_r($bodytag);//输出结果

        $dom = new \DOMDocument('1.0');
        $dom ->loadXML($bodytag);
        $xml = simplexml_import_dom($dom);
        $res= $xml;
        return $res;
    }
    /**********************************判断是否有中文************************************/
    public function isChinese($str){
        //if (preg_match("/^[".chr(0xa1)."-".chr(0xff)."]+$/", $str)) { //只能在GB2312情况下使用
        //if (preg_match("/^[\x7f-\xff]+$/", $str)) { //兼容gb2312,utf-8  //判断字符串是否全是中文
        if (preg_match("/[\x7f-\xff]/", $str)) {  //判断字符串中是否有中文
            return true;
        } else {
            return false;
        }
    }
    /*********************************用户活跃度***************************************/
    public function active($ckUid){
        $timeStary=mktime(0,null,null,date(n),date(j),date(Y));
        $timeEnd=mktime(24,null,null,date(n),date(j),date(Y));

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
}

