<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 15-4-23
 * Time: 下午2:49
 */
namespace app\mobile\controller;
use \app\mobile\libs\Response;
use \app\mobile\controller\request_post;
class Capital extends Common {
    public function index(){
        $info=MI('post.body');
        $ckuid=$info['ckuid'];
        $token=$info['token'];
        $ck=$this->check($ckuid,$token);

        header("Content-type: application/json; charset=utf-8");

        if($ck==226400){
            Response::json(0,'非法操作');
        }
//        $mairuMak=$this->mairu();          //行情买入点数
//        $maichuMak=$this->maichu();        //行情卖出点数
        $capitalModel=M('UserCapital');    //用户资金
//        $detailModel=M('PositionsDetail'); //持仓明细

        $uid=$ckuid;
        $capitalList=$capitalModel->where('uid='.$uid)->select();//用户资金
        $where['uid']=$uid;
        $where['type']='市价';
//        $detailList=$detailModel->where($where)->select();//持仓明细
//        $detailCount=$detailModel->where($where)->count();//计算负荷条件的仓数

        $initialCapital=$capitalList[0]['initial_capital'];      //初始资产
        $totalCapital=$capitalList[0]['total_capital'];          //总资产
        $usableMargin=$capitalList[0]['usable_margin'];           //可用保证金
        $uccupyMargin=$capitalList[0]['uccupy_margin'];           //占用保证金
        $freezeMargin=$capitalList[0]['freeze_margin'];           //冻结保证金
        $totalLoss=$capitalList[0]['total_profit_loss'];          //总盈亏
        /*************************计算总点数盈亏****************************/
//        $loss=0;
//        for($count=0;$count<$detailCount;$count++){
//            if($detailList[$count]['direction']=='买'){
//                $baysell=0;
//            }else{
//                $baysell=1;
//            }
//            $loss1=$this->loss($detailList[$count]['positions_price'],$detailList[$count]['number'],$baysell,$mairuMak,$maichuMak);
//            $loss+=$loss1;
//            //loss($positions,$number,$baysell){//$positions持仓价 $number持仓数 $baysell买入/卖出
//        }
//        $totalLoss=$totalLoss+$loss;
//        $scale=round(($totalLoss/$initialCapital)*100,2);//盈亏比例 总盈亏/初始资产
//        $danger=round(($totalCapital/$uccupyMargin)*100,2);//风险率 总资产/占用保证金
//        $usableMargin=$usableMargin+$totalLoss;//可用保证金 原可用保证金+总盈亏
//        $danger               //风险率
//        $scale                //盈亏比例


        $data=array(
            'initial_capital' =>  number_format($initialCapital, 2, '.', ''),       //初始资产
            'total_capital'     =>  number_format($totalCapital, 2, '.', ''),         //总资产
            'total_loss'        =>  number_format($totalLoss, 2, '.', ''),            //总盈亏
            'scale'             =>  '',                 //盈亏比例
            'danger'            =>  '',                //风险率
            'usable_margin'     =>  number_format($usableMargin, 2, '.', ''),         //可用保证金
            'uccupy_margin'     =>  number_format($uccupyMargin, 2, '.', ''),         //占用保证金
            'freeze_margin'     =>  number_format($freezeMargin, 2, '.', ''),         //冻结保证金
        );
        Response::json(1,'操作成功', $data);
    }

    /***************************************持仓明细**************************************************/
    public function positionsDetail(){
        header("Content-type: application/json; charset=utf-8");
        $info=MI('post.body');
        $ckUid=$info['ckUid'];
        $token=$info['token'];

        $ck=$this->check($ckUid,$token);
        if($ck==226400){
            Response::json(0,'非法操作');
        }

//        $mairuMak=$this->mairu();                 //行情买入点数
//        $maichuMak=$this->maichu();               //行情卖出点数

        $detailModel=M('PositionsDetail'); //持仓明细
        $uid=$ckUid;
        $where['uid']=$uid;
        $where['freeze_uccupy']=array('EXP','IS NULL');
        $detailList=$detailModel->where($where)->order('id desc')->select();//持仓明细
//        $detailCount=$detailModel->where($where)->order('id desc')->count();//计算负荷条件的仓数
//
//        for($count=0;$count<$detailCount;$count++){
//            if($detailList[$count]['direction']=='买'){
//                $baysell=0;
//            }else{
//                $baysell=1;
//            }
//            $detailList[$count]['total_loss']=$this->loss($detailList[$count]['positions_price'],$detailList[$count]['number'],$baysell,$mairuMak,$maichuMak);
//            //loss($positions,$number,$baysell){//$positions持仓价 $number持仓数 $baysell买入/卖出
//        }

        $data=array(
            'detailList'=>$detailList,
        );
        Response::json(1,'操作成功', $data);
    }
    /***************************************持仓汇总**************************************************/
    public function totalPositions(){
        header("Content-type: application/json; charset=utf-8");
        $info=MI('post.body');
        $ckUid=$info['ckUid'];
        $token=$info['token'];
        $ck=$this->check($ckUid,$token);
        if($ck==226400){
            Response::json(0,'非法操作');
        }
//        $mairuMak=$this->mairu();                 //行情买入点数
//        $maichuMak=$this->maichu();               //行情卖出点数

        $detailModel=M('PositionsDetail'); //持仓明细
        $uid=$ckUid;

        /*         卖         */
        $sell['direction']='卖';
        $sell['uid']=$uid;
        $sell['freeze_uccupy']=array('EXP','IS NULL');
        $detailList1=$detailModel->where($sell)->order('id desc')->select();//持仓明细
        $detailCount1=$detailModel->where($sell)->order('id desc')->count();//计算负荷条件的仓数

//        $todayLoss1=0;//当日浮动盈亏
        $totalNumber1=0;//数量
        $uccupy1=0;//占用保证金
        $price1=0;//总持仓价
        for($count=0;$count<$detailCount1;$count++){
//            $loss1=$this->loss($detailList1[$count]['positions_price'],$detailList1[$count]['number'],1,$mairuMak,$maichuMak);
//            $todayLoss1+=$loss1;
            //loss($positions,$number,$baysell){//$positions持仓价 $number持仓数 $baysell买入/卖出
            $totalNumber1+=$detailList1[$count]['number'];
            $uccupy1+=$detailList1[$count]['uccupy']*$detailList1[$count]['number'];
            $price1+=$detailList1[$count]['positions_price']*$detailList1[$count]['number'];
        }
        $avePrice1=round($price1/$totalNumber1,0);
        /*         买         */
        $bay['direction']='买';
        $bay['uid']=$uid;
        $bay['freeze_uccupy']=array('EXP','IS NULL');
        $detailList0=$detailModel->where($bay)->order('id desc')->select();//持仓明细
        $detailCount0=$detailModel->where($bay)->order('id desc')->count();//计算负荷条件的仓数

//        $todayLoss0=0;//当日浮动盈亏
        $totalNumber0=0;//数量
        $uccupy0=0;//占用保证金
        $price0=0;//总持仓价
        for($count=0;$count<$detailCount0;$count++){
//            $loss1=$this->loss($detailList0[$count]['positions_price'],$detailList0[$count]['number'],0,$mairuMak,$maichuMak);
//            $todayLoss0+=$loss1;
            //loss($positions,$number,$baysell){//$positions持仓价 $number持仓数 $baysell买入/卖出
            $totalNumber0+=$detailList0[$count]['number'];
            $uccupy0+=$detailList0[$count]['uccupy']*$detailList0[$count]['number'];
            $price0+=$detailList0[$count]['positions_price']*$detailList0[$count]['number'];
        }
        $avePrice0=round($price0/$totalNumber0,0);
//        $todayLoss1,//当日浮动盈亏
//        $todayLoss0,//当日浮动盈亏

        $data=array(
                'capitalList'   =>  array(
                    array(
                        'good_name'         =>  '现货白银',
                        'direction'         =>  '卖',
                        'positions_price'   =>  number_format($avePrice1, 2, '.', ''),//平均持仓价
                        'number'            =>  $totalNumber1,//数量
                        'today_loss'        =>  '',//当日浮动盈亏
                        'uccupy'            =>  number_format($uccupy1, 2, '.', ''),//占用保证金
                    ),
                    array(
                        'good_name'         =>  '现货白银',
                        'direction'         =>  '买',
                        'positions_price'   =>  number_format($avePrice0, 2, '.', ''),//平均持仓价
                        'number'            =>  $totalNumber0,//数量
                        'today_loss'        =>  '',//当日浮动盈亏
                        'uccupy'            =>  number_format($uccupy0, 2, '.', ''),//占用保证金
                    ),
                ),
            );
        Response::json(1,'操作成功', $data);
    }
    /***************************************当日指价**************************************************/
    public function todayPrice(){
        header("Content-type: application/json; charset=utf-8");
        $info=MI('post.body');
        $ckUid=$info['ckUid'];
        $token=$info['token'];
        $ck=$this->check($ckUid,$token);
        if($ck==226400){
            Response::json(0,'非法操作');
        }

        $todayPrice=M('TodayPrice');       //当日指价

        $timeStary=mktime(0,null,null,date(n),date(j),date(Y));
        $timeEnd=mktime(24,null,null,date(n),date(j),date(Y));
        $uid=$ckUid;

        $today['time']=array(array('egt',$timeStary),array('elt',$timeEnd));
        $today['uid']=$uid;
        $priceList=$todayPrice->where($today)->order('id desc')->select();
        $data=array(
                'priceList'  => $priceList,
            );
        Response::json(1,'操作成功', $data);
    }
    /***************************************当日成交**************************************************/
    public function todayBargain(){
        header("Content-type: application/json; charset=utf-8");
        $info=MI('post.body');
        $ckUid=$info['ckUid'];
        $token=$info['token'];
        $ck=$this->check($ckUid,$token);
        if($ck==226400){
            Response::json(0,'非法操作');
        }



        $todayBargain=M('TodayBargain');   //当日成交

        $timeStary=mktime(0,null,null,date(n),date(j),date(Y));
        $timeEnd=mktime(24,null,null,date(n),date(j),date(Y));

        $uid=$ckUid;

        $today['uid']=$uid;
        $today['time']=array(array('egt',$timeStary),array('elt',$timeEnd));
        $bargainList=$todayBargain->where($today)->order('id desc')->select();
        $data=array(
                'bargainList'  => $bargainList,
            );
        Response::json(1,'操作成功', $data);
    }
    /***************************************历史建仓单**************************************************/
    public function historyCreate(){
        header("Content-type: application/json; charset=utf-8");
        $info=MI('post.body');
        $timeStary=$info['timeStary'];
        $timeEnd=$info['timeEnd'];
        $ckUid=$info['ckUid'];
        $token=$info['token'];
        $ck=$this->check($ckUid,$token);
        if($ck==226400){
            Response::json(0,'非法操作');
        }

        $historCreate=M('HistoryCreate'); //历史建仓

        $uid=$ckUid;

        $history['uid']=$uid;
        $history['time']=array(array('egt',$timeStary),array('elt',$timeEnd));
        $creatList=$historCreate->where($history)->order('id desc')->select();
        $data=array(
                'creatList'  => $creatList,
            );
        Response::json(1,'操作成功', $data);
    }
    /***************************************历史平仓单**************************************************/
    public function historyDetail(){
        header("Content-type: application/json; charset=utf-8");
        $info=MI('post.body');
        $timeStary=$info['timeStary'];
        $timeEnd=$info['timeEnd'];
        $ckUid=$info['ckUid'];
        $token=$info['token'];
        $ck=$this->check($ckUid,$token);
        if($ck==226400){
            Response::json(0,'非法操作');
        }

        $historyDetail=M('HistoryDetail'); //历史平仓


        $uid=$ckUid;

        $history['uid']=$uid;
        $history['time']=array(array('egt',$timeStary),array('elt',$timeEnd));
        $detailList=$historyDetail->where($history)->order('id desc')->select();
        $data=array(
                'detailList'  => $detailList,
            );
        Response::json(1,'操作成功', $data);
    }
    /***************************************历史指价单**************************************************/
    public function historyPrice(){
        header("Content-type: application/json; charset=utf-8");
        $info=MI('post.body');
        $timeStary=$info['timeStary'];
        $timeEnd=$info['timeEnd'];
        $ckUid=$info['ckUid'];
        $token=$info['token'];
        $ck=$this->check($ckUid,$token);
        if($ck==226400){
            Response::json(0,'非法操作');
        }

        $historyPrice=M('HistoryPrice');   //历史指价

        $uid=$ckUid;

        $history['uid']=$uid;
        $history['time']=array(array('egt',$timeStary),array('elt',$timeEnd));
        $priceList=$historyPrice->where($history)->order('id desc')->select();
        $data=array(
                'priceList'  => $priceList,
            );
        Response::json(1,'操作成功', $data);
    }
    /***************************************资金流水**************************************************/
    public function positionsFlow(){
        header("Content-type: application/json; charset=utf-8");
        $info=MI('post.body');
        $ckUid=$info['ckUid'];
        $token=$info['token'];
        $timeStary=$info['timeStary'];
        $timeEnd=$info['timeEnd'];
        $ck=$this->check($ckUid,$token);
        if($ck==226400){
            Response::json(0,'非法操作');
        }

        $positionsFlow=M('PositionsFlow'); //资金流水
        $uid=$ckUid;

        $flow['uid']=$uid;
        $flow['time']=array(array('egt',$timeStary),array('elt',$timeEnd));
        $flowList=$positionsFlow->where($flow)->order('id desc')->select();
        $data=array(
                'flowList'  => $flowList,
            );
        Response::json(1,'操作成功', $data);
    }
}
