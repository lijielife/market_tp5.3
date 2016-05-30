<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 15-4-23
 * Time: 下午2:49
 */
namespace app\desktop\controller;
use think\Controller;
use app\desktop\libs\Response;
class Capital extends Common {
    public function index(){
        $ckuid=DI('ckuid');
        $token=DI('token');

        $ck=$this->check($ckuid,$token);

        // file_put_contents('logs.txt', '===========>'. date('Y-m-d H:i:s') .PHP_EOL, FILE_APPEND);
        // file_put_contents('logs.txt', $token.PHP_EOL, FILE_APPEND);
        // file_put_contents('logs.txt', $ckUid.PHP_EOL, FILE_APPEND);
        // file_put_contents('logs.txt', $ck.PHP_EOL, FILE_APPEND);

        if($ck==226400){
            Response::json(0,'非法操作');
        }
//        $mairuMak=$this->mairu();          //行情买入点数
//        $maichuMak=$this->maichu();        //行情卖出点数
        $capitalModel=M('UserCapital');    //用户资金
//        $detailModel=M('PositionsDetail'); //持仓明细

        $uid=$ckuid;
        $capitalList=$capitalModel->where('uid='.$uid)->find();//用户资金
        $where['uid']=$uid;
        $where['type']='市价';
//        $detailList=$detailModel->where($where)->select();//持仓明细
//        $detailCount=$detailModel->where($where)->count();//计算负荷条件的仓数

        $initialCapital=$capitalList['initial_capital'];      //初始资产
        $totalCapital=$capitalList['total_capital'];          //总资产
        $usableMargin=$capitalList['usable_margin'];           //可用保证金
        $uccupyMargin=$capitalList['uccupy_margin'];           //占用保证金
        $freezeMargin=$capitalList['freeze_margin'] ? $capitalList['freeze_margin'] : '';           //冻结保证金
        $totalLoss=$capitalList['total_profit_loss'];          //总盈亏
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

        $arr=array(
            'initial_capital'   =>  $initialCapital,       //初始资产
            'total_capital'     =>  $totalCapital,         //总资产
            'total_loss'        =>  $totalLoss,            //总盈亏
            'scale'             =>  '',                 //盈亏比例
            'danger'            =>  '',                //风险率
            'usable_margin'     =>  $usableMargin,         //可用保证金
            'uccupy_margin'     =>  $uccupyMargin,         //占用保证金
            'freeze_margin'     =>  $freezeMargin,         //冻结保证金

        );

        Response::json(1,'操作成功',$arr);
    }

    /***************************************持仓明细**************************************************/
    public function positionsDetail(){
        $ckuid=DI('ckuid');
        $token=DI('token');

//        $ckUid=22600008;
//        $token='a578abec4ffd19b4f11e9d38520524c8';


        $ck=$this->check($ckuid,$token);
        if($ck==226400){
            Response::json(0,'非法操作');
        }

//        $mairuMak=$this->mairu();                 //行情买入点数
//        $maichuMak=$this->maichu();               //行情卖出点数

        $detailModel=M('PositionsDetail'); //持仓明细
        $uid=$ckuid;
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
        Response::json(1,'操作成功',$detailList);

//        file_put_contents('12321.txt',json_encode($arr).'---'.$ckUid.'----'.$token);
    }
    /***************************************持仓汇总**************************************************/
    public function totalPositions(){
        $ckuid=DI('ckuid');
        $token=DI('token');
        $ck=$this->check($ckuid,$token);
        if($ck==226400){
            Response::json(0,'非法操作');
        }
//        $mairuMak=$this->mairu();                 //行情买入点数
//        $maichuMak=$this->maichu();               //行情卖出点数

        $detailModel=M('PositionsDetail'); //持仓明细
        $uid=$ckuid;
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
//        return 322;
        $avePrice1=round($price1/$totalNumber1,0);
//        return 121212;
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


       $arr= array(
               array(
                   'good_name'         =>  '现货白银',
                   'direction'         =>  '卖',
                   'positions_price'   =>  (string) $avePrice1,//平均持仓价
                   'number'            =>  (string) $totalNumber1,//数量
                   'today_loss'        =>  '',//当日浮动盈亏
                   'uccupy'            =>  (string) $uccupy1,//占用保证金
               ),
               array(
                   'good_name'         =>  '现货白银',
                   'direction'         =>  '买',
                   'positions_price'   =>  (string) $avePrice0,//平均持仓价
                   'number'            =>  (string) $totalNumber0,//数量
                   'today_loss'        =>  '',//当日浮动盈亏
                   'uccupy'            =>  (string) $uccupy0,//占用保证金
               ),
           );
        Response::json(1,'操作成功',$arr);
    }
    /***************************************当日指价**************************************************/
    public function todayPrice(){
        $ckuid=DI('ckuid');
        $token=DI('token');
        $ck=$this->check($ckuid,$token);
        if($ck==226400){
            Response::json(0,'非法操作');
        }
        $todayPrice=M('TodayPrice');       //当日指价
        $timeStary=mktime(0,null,null,date('n'),date('j'),date('Y'));
        $timeEnd=mktime(24,null,null,date('n'),date('j'),date('Y'));
        $uid=$ckuid;
        $today['time']=array(array('egt',$timeStary),array('elt',$timeEnd));
        $today['uid']=$uid;
        $priceList=$todayPrice->where($today)->order('id desc')->select();
        Response::json(1,'操作成功',$priceList);
    }
    /***************************************当日成交**************************************************/
    public function todayBargain(){
        $ckUid=DI('ckUid');
        $token=DI('token');
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


        Response::json(1,'操作成功',$bargainList);
    }
    /***************************************历史建仓单**************************************************/
    public function historyCreate(){
        $timeStary=DI('timeStary');
        $timeEnd=DI('timeEnd');
        $ckUid=DI('ckUid');
        $token=DI('token');
        $ck=$this->check($ckUid,$token);
        if($ck==226400){
            Response::json(0,'非法操作');
        }

        $historCreate=M('HistoryCreate'); //历史建仓

        $uid=$ckUid;

        $history['uid']=$uid;
        $history['time']=array(array('egt',$timeStary),array('elt',$timeEnd));
        $creatList=$historCreate->where($history)->order('id desc')->select();
        Response::json(1,'操作成功',$creatList);
    }
    /***************************************历史平仓单**************************************************/
    public function historyDetail(){
        $timeStary=DI('timeStary');
        $timeEnd=DI('timeEnd');
        $ckUid=DI('ckUid');
        $token=DI('token');
        $ck=$this->check($ckUid,$token);
        if($ck==226400){
            Response::json(0,'非法操作');
        }

        $historyDetail=M('HistoryDetail'); //历史平仓


        $uid=$ckUid;

        $history['uid']=$uid;
        $history['time']=array(array('egt',$timeStary),array('elt',$timeEnd));
        $detailList=$historyDetail->where($history)->order('id desc')->select();
        Response::json(1,'操作成功',$detailList);
    }
    /***************************************历史指价单**************************************************/
    public function historyPrice(){
        $timeStary=DI('timeStary');
        $timeEnd=DI('timeEnd');
        $ckUid=DI('ckUid');
        $token=DI('token');
        $ck=$this->check($ckUid,$token);
        if($ck==226400){
            Response::json(0,'非法操作');
        }

        $historyPrice=M('HistoryPrice');   //历史指价

        $uid=$ckUid;

        $history['uid']=$uid;
        $history['time']=array(array('egt',$timeStary),array('elt',$timeEnd));
        $priceList=$historyPrice->where($history)->order('id desc')->select();
        Response::json(1,'操作成功',$priceList);

    }
    /***************************************资金流水**************************************************/
    public function positionsFlow(){
        $ckUid=DI('ckUid');
        $token=DI('token');
        $timeStary=DI('timeStary');
        $timeEnd=DI('timeEnd');
        $ck=$this->check($ckUid,$token);
        if($ck==226400){
            Response::json(0,'非法操作');
        }

        $positionsFlow=M('PositionsFlow'); //资金流水
        $uid=$ckUid;

        $flow['uid']=$uid;
        $flow['time']=array(array('egt',$timeStary),array('elt',$timeEnd));
        $flowList=$positionsFlow->where($flow)->order('id desc')->select();
        Response::json(1,'操作成功',$flowList);
    }
}
