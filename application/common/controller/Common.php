<?php

namespace app\desktop\controller;
use think\Controller;
class Common extends Controller{
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
        $ip=get_client_ip();

        $ModelIP=M('IpAllow');
        $ListIP=$ModelIP->where(array('ip'=>$ip))->find();
        if(!$ListIP){
            echo '非法操作';
            exit;
        }
        $positionsFlow=M('PositionsFlow'); //资金流水
        $detailModel=M('PositionsDetail'); //持仓明细
        $capitalModel=M('UserCapital');    //用户资金
        $where['freeze_uccupy']=array('EXP','IS NULL');
        $detailList=$detailModel->where($where)->order('id desc')->select();//持仓明细
        $detailCount=$detailModel->where($where)->count();//计算负荷条件的仓数
        //$overnight=$maichu_mak*15*$number*0.0002*$day;//过夜费 卖出行情*15*手数*0.0002*天数
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
                $positionsFlow->add($flow)&&$capitalModel->where('uid='.$uid)->save($capital);
//                if($positionsFlow->add($flow)&&$capitalModel->where('uid='.$uid)->save($capital)){
//                    return true;
//                }else{
//                    return false;
//                }
            }
        }
    }
    /**********************************统一指价超时********************************************/
    public function overTime(){
        $ip=get_client_ip();

        $ModelIP=M('IpAllow');
        $ListIP=$ModelIP->where(array('ip'=>$ip))->find();
        if(!$ListIP){
            echo '非法操作';
            exit;
        }

        $capitalModel=M('UserCapital');    //用户资金
        $detailModel=M('PositionsDetail'); //持仓明细
        $todayPrice=M('TodayPrice');       //当日指价
        $historyPrice=M('HistoryPrice');   //历史指价

        $where['uccupy']=array('EXP','IS NULL');
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
            $todayPrice1['status']='超时';//修改状态
            /*******************用户资金变化****************/
            $capital['usable_margin']=$usable;//可用保证金
            $capital['freeze_margin']=$freezeMargin;//冻结资金
            /*******************写入数据库******************/
            if($todayPrice->where('positions_number='.$positionsNumber)->save($todayPrice1)&&$historyPrice->where('positions_number='.$positionsNumber)->save($history)&&$detailModel->where('positions_number='.$positionsNumber)->delete()&&$capitalModel->where('uid='.$uid)->save($capital)){
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

        return $mairu;
    }
    public function maichu(){
        $Model =  D("Market");
        $a = $Model->order('quotetime desc')->find();
        $maichu=$a['sellprice'];

        return $maichu;
    }
    /*************************************模拟盘开户**************************************/
    public function userCreate(){
        $uid=I('uid');
        $time=I('time');
        $echostr=I('echostr');
        $signature=I('signature');

        $kaihu=createSignature($uid,$time,$echostr,$signature);

        if(!$kaihu){
            $arr=array(
                'status'  =>  0,
                'info'   =>  '开户失败！非法操作',
                'data'  =>  ''
            );
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            exit;
        }

        $userCapital=M('UserCapital');
        $capital['uid']=$uid;
        if($userCapital->where(array('uid'=>$uid))->find()){
            $arr=array(
                'status'  =>  0,
                'info'   =>  '用户已存在无需再次开户',
                'data'  =>  ''
            );
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            exit;
        }
        if($userCapital->add($capital)){
            $arr=array(
                'status'  =>  1,
                'info'   =>  '开户成功',
                'data'  =>  ''
            );
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            exit;
        }else{
            $arr=array(
                'status'  =>  0,
                'info'   =>  '开户失败',
                'data'  =>  ''
            );
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            exit;
        }

    }
    /***********************************删除模拟盘**************************************/
    public function delMarket(){
        $uid=I('uid');

        $time=I('time');
        $echostr=I('echostr');
        $signature=I('signature');

        $kaihu=createSignature($uid,$time,$echostr,$signature);


        if(!$kaihu){
            $arr=array(
                'status'  =>  0,
                'info'   =>  '开户失败！非法操作',
                'data'  =>  ''
            );
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            exit;
        }

        $userModel=M('userCapital');
        $userList=$userModel->where(array('uid'=>$uid))->find();
        if($userList){
            $userDel=$userModel->where(array('uid'=>$uid))->delete();
            if($userDel){

                // 删除持仓信息
                M('positions_detail')->where(array('uid' => $uid))->delete();
                // 删除指价建仓
                M('today_price')->where(array('uid' => $uid))->delete();


                $arr=array(
                    'status'  =>  1,
                    'info'   =>  '删除成功',
                    'data'  =>  ''
                );
                echo json_encode($arr,JSON_UNESCAPED_UNICODE);
                exit;
            }else{
                $arr=array(
                    'status'  =>  0,
                    'info'   =>  '删除失败',
                    'data'  =>  ''
                );
                echo json_encode($arr,JSON_UNESCAPED_UNICODE);
                exit;
            }
        }else{
            $arr=array(
                'status'  =>  0,
                'info'   =>  '用户不存在',
                'data'  =>  ''
            );
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            exit;
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
    /**
     *
     * 输出内容到文本文件
     *
     */
     function _logs($data,$file){
        $filename = './logs/'.$file.'.txt';

        $filesize = @filesize($filename);

        if ($filesize > 10485760){
            $handle = fopen($filename, "w");
        }else{
            $handle = fopen($filename, "a");
        }


        fwrite($handle,"数据提交时间：".date('Y-m-d H:i:s')."\r\n");

        fwrite($handle,json_encode($data)."\r\n");

        fclose($handle);

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

