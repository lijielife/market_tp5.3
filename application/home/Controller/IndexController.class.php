<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommonController {
    public function index(){
        $this->show("<h1>你想干嘛？</h1>");

    }
    /***********************************市价建仓*********************************************/
    public function create(){
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
        $number=I('number');
        $pip=I('pip');
        $baysell=I('baysell');                    //0为买1为卖
        $positionsPrice=I('positionsPrice');
        $mairuMak=$this->mairu();                 //行情买入点数
        $maichuMak=$this->maichu();               //行情卖出点数

        $capitalModel=M('UserCapital');    //用户资金
        $detailModel=M('PositionsDetail'); //持仓明细
        $todayBargain=M('TodayBargain');   //当日成交
        $historyCreate=M('HistoryCreate'); //历史建仓

        $uid=$ckUid;
        $capitalList=$capitalModel->where('uid='.$uid)->select();         //用户资金

        $totalCapital=$capitalList[0]['total_capital'];                   //总资产
        $totalProfitLoss=$capitalList[0]['total_profit_loss'];           //总盈亏
        $usableMargin=$capitalList[0]['usable_margin'];                   //可用保证金
        $uccupyMargin=$capitalList[0]['uccupy_margin'];                   //占用保证金
        $freezeMargin=$capitalList[0]['freeze_margin'];                   //冻结保证金
        /******************计算*******************/

        if($totalCapital>=200000){//计算手续费
            $commission=0.05;
        }else{
            $commission=0.08;
        }
        if($baysell==0){
            if(abs($positionsPrice-$mairuMak)<=$pip){
                $charge=$mairuMak*15*0.0008*$number;                                       //买入手续费 买入点数*15*0.0008*手数
                $uccupy=$mairuMak*15*$commission*$number;                                  //这一单占用保证金 买入点数*15*0.08*手数
                $usableMargin1=$usableMargin-$charge-$uccupy;  //可用保证金 可用保证金-手续费-占用保证金+（卖出行情-买入点数）*15*手数
                $totalCapital1=$uccupy+$usableMargin1+$uccupyMargin+$freezeMargin;        //总资产 这一单占用保证金+可用保证金+历史占用保证金+历史冻结保证金
            }else{
                $arr=array(
                    'status'  =>  0,
                    'info'   =>  '点差超过设定值',
                    'data'  =>  ''
                );
                echo json_encode($arr);
                exit;
            }
        }else{
            if(abs($positionsPrice-$maichuMak)<=$pip){
                $charge=$maichuMak*15*0.0008*$number;                                       //卖出手续费 卖出点数*15*0.0008*手数
                $uccupy=$maichuMak*15*$commission*$number;                                  //这一单占用保证金 卖出点数*15*0.08*手数
                $usableMargin1=$usableMargin-$charge-$uccupy;  //可用保证金 可用保证金-手续费-占用保证金+（买入点数-卖出行情）*15*手数
                $totalCapital1=$uccupy+$usableMargin1+$uccupyMargin+$freezeMargin;         //总资产 这一单占用保证金+可用保证金+历史占用保证金+历史冻结保证金
            }else{
                $arr=array(
                    'status'  =>  0,
                    'info'   =>  '点差超过设定值',
                    'data'  =>  ''
                );
                echo json_encode($arr);
                exit;
            }
        }
        $positionsNumber=$this->positionsNumber($uid);
        $greatNumber=$this->positionsNumber($uid);
        /******************创建新单*******************/
        $positions['uccupy']=$uccupy;                  //占用保证金
        $positions['uid']=$uid;                        //关联id
        $positions['time']=time();
        $positions['positions_number']=$positionsNumber;         //持仓单号
        $positions['type']='市价';                   //单据类型
        $positions['good_name']='现货白银';              //商品名称
        $positions['commission']=$commission;          //手续费率
        if($baysell==0){
            $positions['direction']='买';                   //方向
            $positions['positions_price']=$mairuMak;       //持仓价
            $positions['pip']=abs($positionsPrice-$mairuMak);       //点差
        }else{
            $positions['direction']='卖';
            $positions['positions_price']=$maichuMak;
            $positions['pip']=abs($positionsPrice-$maichuMak);       //点差
        }

        $positions['number']=$number;                   //数量
        /*******************当日成交表*******************/
        $bargain['uid']=$uid;
        $bargain['time']=time();//成交时间
        $bargain['good_name']='现货白银';//商品名称
        if($baysell==0){
            $bargain['direction']='买';//方向
            $bargain['bargain_price']=$mairuMak;//成交价格

        }else{
            $bargain['direction']='卖';//方向
            $bargain['bargain_price']=$maichuMak;//成交价格
        }
        $bargain['instruct']='建仓';//指令
        $bargain['number']=$number;//数量
        $bargain['type']='市价';//类型
        $bargain['uccupy']=$uccupy;//占用保证金
        $bargain['commission']=-$charge;//手续费
        $bargain['overnight']='';//延期费
        $bargain['loss']='';//单笔盈亏
        $bargain['positions_number']=$positionsNumber;//持仓单号
        $bargain['positions_bargain']=$greatNumber;//成交编号
        /********************历史建仓单*********************/
        $create['uid']=$uid;//关联编号
        $create['time']=time();//委托时间
        $create['good_name']='现货白银';//商品名称
        if($baysell==0){
            $create['direction']='买';//方向
            $create['price']=$mairuMak;//成交价
        }else{
            $create['direction']='卖';//方向
            $create['price']=$maichuMak;//成交价
        }
        $create['number']=$number;//数量
        $create['positions_number']=$greatNumber;//成交单号
        /*******************资金流水********************/
        //flow($uid,$price,$name,$positionsNumber,$total) $uid关联id $price变动资金 $name业务名称 $positionsNumber关联单号 $total变后资金
        $price=0-$charge;
        $flow1=$this->flow($uid,$price,'手续费',$positionsNumber,$totalCapital1);
        /*******************用户资金变化****************/
        $capital['total_capital']=$totalCapital1;//总资产
        $capital['total_profit_loss']=$totalProfitLoss-$charge;//总盈亏
        $capital['usable_margin']=$usableMargin1;//可用保证金
        $capital['uccupy_margin']=$uccupyMargin+$uccupy;//占用保证金
        /*******************写入数据库******************/
        if($historyCreate->add($create)&&$flow1&&$detailModel->add($positions)&&$capitalModel->where('uid='.$uid)->save($capital)&&$todayBargain->add($bargain)){
            $this->active($ckUid);//用户活跃度+1
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

    /****************************************指价建仓*************************************************/
    public function createPrice(){
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

        $baysell=I('baysell');             //0为买1为卖
        $lossPrice=I('lossPrice');         //止损价
        $profitPrice=I('profitPrice');     //止盈价
        $number=I('number');
        $positionsPrice=I('positionsPrice');//指价点数

        //$mairuMak=$this->mairu();                 //行情买入点数
        //$maichuMak=$this->maichu();               //行情卖出点数

        $capitalModel=M('UserCapital');    //用户资金
        $detailModel=M('PositionsDetail'); //持仓明细
        $todayPrice=M('TodayPrice');       //当日指价
        $historyPrice=M('HistoryPrice');   //历史指价

        $uid=$ckUid;
        $capitalList=$capitalModel->where('uid='.$uid)->select();         //用户资金

        $totalCapital=$capitalList[0]['total_capital'];                   //总资产
        $usableMargin=$capitalList[0]['usable_margin'];                   //可用保证金
        $freezeMargin=$capitalList[0]['freeze_margin'];                   //冻结保证金
        /******************计算*******************/

        if($totalCapital>=200000){//计算手续费
            $commission=0.05;
        }else{
            $commission=0.08;
        }

            $charge=$positionsPrice*15*0.0008*$number;//买入冻结手续费 买入点数*15*0.0008*手数
            $uccupy=$positionsPrice*15*$commission*$number;//这一单冻结保证金 买入点数*15*0.08*手数
            $freeze=$charge+$uccupy;//冻结资金  冻结手续费+冻结保证金
            $usableMargin1=$usableMargin-$freeze;  //可用保证金 可用保证金-冻结资金

        $positionsNumber=$this->positionsNumber($uid);
        /******************创建新单*******************/
        $positions['freeze_uccupy']=$uccupy;           //冻结保证金
        $positions['uid']=$uid;                        //关联id
        $positions['time']=time();
        $positions['positions_number']=$positionsNumber;         //持仓单号
        $positions['type']='指价';                   //单据类型
        $positions['good_name']='现货白银';              //商品名称
        $positions['commission']=$commission;          //手续费率
        if($baysell==0){
            $positions['direction']='买';                   //方向
            $positions['positions_price']=$positionsPrice;     //持仓价
        }else{
            $positions['direction']='卖';
            $positions['positions_price']=$positionsPrice;
        }
        $positions['number']=$number;                   //数量
        $positions['pip']='指价建仓无点差';       //点差
        $positions['loss']=$lossPrice;         //止损价
        $positions['profit']=$profitPrice;     //止盈价
        /********************当日指价******************/
        $price['uid']=$uid;
        $price['time']=time();//申报时间
        $price['good_name']='现货白银';//商品名称
        if($baysell==0){
            $price['direction']='买';//方向
            $price['price']=$positionsPrice;//委托价
        }else{
            $price['direction']='卖';//方向
            $price['price']=$positionsPrice;//委托价
        }
        $price['number']=$number;//数量
        $price['status']='委托';//状态
        $price['type']='建仓';//指令
        $price['freeze_uccupy']=$uccupy;//冻结资金
        $price['loss']=$lossPrice;//止损价
        $price['profit']=$profitPrice;//止盈价
        $price['positions_number']=$positionsNumber;//委托单号
        /*******************历史指价**********************/
        $htPrice['uid']=$uid;//关联编号
        $htPrice['time']=time();//委托时间
        $htPrice['good_name']='现货白银';//商品名称
        if($baysell==0){
            $htPrice['direction']='买';//方向
            $htPrice['price']=$positionsPrice;//委托价
        }else{
            $htPrice['direction']='卖';//方向
            $htPrice['price']=$positionsPrice;//委托价
        }
        $htPrice['number']=$number;//数量
        $htPrice['status']='委托';//状态
        $htPrice['loss']=$lossPrice;//止损价
        $htPrice['profit']=$profitPrice;//止盈价
        $htPrice['positions_number']=$positionsNumber;//委托单号
        /*******************用户资金变化****************/
        $capital['usable_margin']=$usableMargin1;//可用保证金
        $capital['freeze_margin']=$freeze+$freezeMargin;//冻结资金
        /*******************写入数据库******************/
        if($historyPrice->add($htPrice)&&$todayPrice->add($price)&&$detailModel->add($positions)&&$capitalModel->where('uid='.$uid)->save($capital)){
            $this->active($ckUid);//用户活跃度+1
            $arr=array(
                'status'  =>  1,
                'info'   =>  '操作成功',
                'data'  =>  ''
            );
            echo json_encode($arr);
        }else{
            $arr=array(
                'status'  =>  0,
                'info'   =>  '操作失败',
                'data'  =>  ''
            );
            echo json_encode($arr);
        }

    }
    /******************************************指价变市价**********************************************/
    public function priceCreate(){
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
        $positionsNumber=I('positions_number');

        $capitalModel=M('UserCapital');    //用户资金
        $detailModel=M('PositionsDetail'); //持仓明细
        $todayBargain=M('TodayBargain');   //当日成交
        $todayPrice=M('TodayPrice');       //当日指价
        $historyPrice=M('HistoryPrice');   //历史指价
        $historyCreate=M('HistoryCreate'); //历史建仓

        $uid=$ckUid;
        $where['positions_number']=$positionsNumber;
        $where['uccupy']=array('EXP','IS NULL');
        $detailList=$detailModel->where($where)->select();//持仓明细
        if(!$detailList){
            $arr=array(
                'status'  =>  0,
                'info'   =>  '没有指价单',
                'data'  =>  ''
            );
            echo json_encode($arr);
            exit;
        }
        $capitalList=$capitalModel->where('uid='.$uid)->select();         //用户资金

        $bay=$detailList[0]['direction'];                           //买或卖
        if($bay=='买'){
            $baysell=0;
        }else{
            $baysell=1;
        }
        $commission=$detailList[0]['commission'];                          //手续费率
        $number=$detailList[0]['number'];                                  //手数
        $positionsPrice=$detailList[0]['positions_price'];
        $totalCapital=$capitalList[0]['total_capital'];                   //总资产
        $totalProfitLoss=$capitalList[0]['total_profit_loss'];           //总盈亏
        $usableMargin=$capitalList[0]['usable_margin'];                   //可用保证金
        $uccupyMargin=$capitalList[0]['uccupy_margin'];                   //占用保证金
        $freezeMargin=$capitalList[0]['freeze_margin'];                   //冻结保证金

        $greatNumber=$this->positionsNumber($uid);
        /******************计算*******************/

        $charge=$positionsPrice*15*0.0008*$number;                                       //买入手续费 买入点数*15*0.0008*手数
        $uccupy=$positionsPrice*15*$commission*$number;                                  //这一单保证金 买入点数*15*0.08*手数
        //$loss=($maichuSell-$mairuSell)*15*$commission*$number-$charge;//这单盈亏 （卖出-买入）*15*手数-这单手续费
        $usable=$usableMargin;//可用保证金 总可用保证金

        $totalMargin=$totalCapital-$charge;//总资产 总资产-手续费
        $totalLoss=$totalProfitLoss-$charge;//总盈亏 历史总盈亏-手续费
        $uccupyMargin1=$uccupyMargin+$uccupy;//占用保证金 历史占用保证金+这一单保证金
        $freeze=$freezeMargin-$uccupy-$charge;//剩余冻结金 总冻结金-这单保证金-这单手续费

        /******************修改指价单*******************/
        $positions['uccupy']=$uccupy;            //占用保证金
        $positions['freeze_uccupy']=null;          //冻结保证金
        $positions['time']=time();
        $positions['type']='指价';                   //单据类型
        /******************修改历史指价单******************/
        $history['status']='成交';//状态
        /*******************修改资金流水******************/
        //flow($uid,$price,$name,$positionsNumber,$total) $uid关联id $price变动资金 $name业务名称 $positionsNumber关联单号 $total变后资金
        $price=0-$charge;
        $flow1=$this->flow($uid,$price,'手续费',$positionsNumber,$totalMargin);
        /*******************修改当日指价表****************/
        $todayPrice1['status']='成交';//修改状态
        /*******************新建当日成交****************/
        $bargain['uid']=$uid;
        $bargain['time']=time();//成交时间
        $bargain['good_name']='现货白银';//商品名称
        if($baysell==0){
            $bargain['direction']='买';//方向
            $bargain['bargain_price']=$positionsPrice;//成交价格

        }else{
            $bargain['direction']='卖';//方向
            $bargain['bargain_price']=$positionsPrice;//成交价格
        }
        $bargain['instruct']='建仓';//指令
        $bargain['number']=$number;//数量
        $bargain['type']='指价';//类型
        $bargain['uccupy']=$uccupy;//占用保证金
        $bargain['commission']=-$charge;//手续费
        $bargain['overnight']='';//延期费
        $bargain['loss']='';//单笔盈亏
        $bargain['positions_number']=$positionsNumber;//持仓单号
        $bargain['positions_bargain']=$greatNumber;//成交编号
        /********************历史建仓单*********************/
        $create['uid']=$uid;//关联编号
        $create['time']=time();//委托时间
        $create['good_name']='现货白银';//商品名称
        if($baysell==0){
            $create['direction']='买';//方向
            $create['price']=$positionsPrice;//成交价
        }else{
            $create['direction']='卖';//方向
            $create['price']=$positionsPrice;//成交价
        }
        $create['number']=$number;//数量
        $create['positions_number']=$positionsNumber;//成交单号
        /*******************用户资金变化****************/
        $capital['total_capital']=$totalMargin;//总资产
        $capital['total_profit_loss']=$totalLoss;//总盈亏
        $capital['usable_margin']=$usable;//可用保证金
        $capital['uccupy_margin']=$uccupyMargin1;//占用保证金
        $capital['freeze_margin']=$freeze;//冻结资金
        /*******************写入数据库******************/
        if($todayBargain->add($bargain)&&$historyCreate->add($create)&&$todayPrice->where('positions_number='.$positionsNumber)->save($todayPrice1)&&$flow1&&$historyPrice->where('positions_number='.$positionsNumber)->save($history)&&$detailModel->where('positions_number='.$positionsNumber)->save($positions)&&$capitalModel->where('uid='.$uid)->save($capital)){
            $arr=array(
                'status'  =>  1,
                'info'   =>  '操作成功',
                'data'  =>  ''
            );
            echo json_encode($arr);
        }else{
            $arr=array(
                'status'  =>  0,
                'info'   =>  '操作失败',
                'data'  =>  ''
            );
            echo json_encode($arr);
        }

    }
    /**************************************指价撤单*********************************************/
    public function priceTimeOut(){
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
        $positionsNumber=I('positions_number');

        $capitalModel=M('UserCapital');    //用户资金
        $detailModel=M('PositionsDetail'); //持仓明细
        $todayPrice=M('TodayPrice');       //当日指价
        $historyPrice=M('HistoryPrice');   //历史指价

        $uid=$ckUid;
        $detailList=$detailModel->where('positions_number='.$positionsNumber)->select();//持仓明细
        $capitalList=$capitalModel->where('uid='.$uid)->select();         //用户资金

        $commission=$detailList[0]['commission'];                          //手续费率
        $number=$detailList[0]['number'];                                  //手数
        $positionsPrice=$detailList[0]['positions_price'];
        $usableMargin=$capitalList[0]['usable_margin'];                   //可用保证金
        $freezeMargin=$capitalList[0]['freeze_margin'];                   //冻结保证金

        /******************计算*******************/

            $charge=$positionsPrice*15*0.0008*$number;                                       //买入手续费 买入点数*15*0.0008*手数
            $uccupy=$positionsPrice*15*$commission*$number;                                  //这一单保证金 买入点数*15*0.08*手数

        $freeze=$charge+$uccupy;//这一单冻结资金  冻结手续费+冻结保证金
        $usable=$usableMargin+$freeze;//可用保证金 历史可用保证金+这一单冻结保证金
        $freezeMargin=$freezeMargin-$freeze;//冻结保证金 历史冻结保证金-这一单冻结保证金
        /******************修改历史指价单******************/
        $history['status']='撤单';//状态
        /*******************修改当日指价表****************/
        $todayPrice1['status']='撤单';//修改状态
        /*******************用户资金变化****************/
        $capital['usable_margin']=$usable;//可用保证金
        $capital['freeze_margin']=$freezeMargin;//冻结资金
        /*******************写入数据库******************///
        if($detailModel->where('positions_number='.$positionsNumber)->delete()&&$todayPrice->where('positions_number='.$positionsNumber)->save($todayPrice1)&&$historyPrice->where('positions_number='.$positionsNumber)->save($history)&&$capitalModel->where('uid='.$uid)->save($capital)){
            $this->active($ckUid);//用户活跃度+1
            $arr=array(
                'status'  =>  1,
                'info'   =>  '操作成功',
                'data'  =>  ''
            );
            echo json_encode($arr);
        }else{
            $arr=array(
                'status'  =>  0,
                'info'   =>  '操作失败',
                'data'  =>  ''
            );
            echo json_encode($arr);
        }
    }
    /*******************************************平仓*************************************************/
    public function sell(){
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
        $positionsNumber=I('positions_number');
        $mairuMak=$this->mairu();                 //行情买入点数
        $maichuMak=$this->maichu();               //行情卖出点数

        $capitalModel=M('UserCapital');    //用户资金
        $detailModel=M('PositionsDetail'); //持仓明细
        $historyModel=M('HistoryDetail');  //历史记录
        $todayBargain=M('TodayBargain');   //当日成交

        $uid=$ckUid;
        $detailList=$detailModel->where('positions_number='.$positionsNumber)->select();           //持仓明细
        $capitalList=$capitalModel->where('uid='.$uid)->select();         //用户资金

        $bay=$detailList[0]['direction'];                           //买或卖
        if($bay=='买'){
            $baysell=1;
        }else{
            $baysell=0;
        }

        $uccupy=$detailList[0]['uccupy'];                                  //这单占用保证金
        $number=$detailList[0]['number'];                                  //手数
        $positionsPrice=$detailList[0]['positions_price'];                  //持仓价
        $totalCapital=$capitalList[0]['total_capital'];                   //总资产
        $totalProfitLoss=$capitalList[0]['total_profit_loss'];           //总盈亏
        $usableMargin=$capitalList[0]['usable_margin'];                   //可用保证金
        $uccupyMargin=$capitalList[0]['uccupy_margin'];                   //占用保证金
        /******************计算*******************/


        if($baysell==0){
            //$overnight=$maichuMak*15*$number*0.0002*$day;//过夜费 卖出行情*15*手数*0.0002*天数
            $charge=$mairuMak*15*0.0008*$number;//手续费 卖出行情*15*0.0008*手数
            $loss1=($mairuMak-$positionsPrice)*15*$number;//点数盈亏 （卖出行情-买入行情）*15*手数

        }else{
            //$overnight=$mairuMak*15*$number*0.0002*$day;//过夜费 卖出行情*15*手数*0.0002*天数
            $charge=$maichuMak*15*0.0008*$number;//手续费 卖出行情*15*0.0008*手数
            $loss1=($maichuMak-$positionsPrice)*15*$number;//点数盈亏 （卖出行情-买入行情）*15*手数
        }
        $loss=$loss1-$charge;//这单盈亏 点数盈亏-手续费
        $totalLoss=$totalProfitLoss+$loss;//总盈亏 当前盈亏+这单盈亏
        $uccupyMargin=$uccupyMargin-$uccupy;//总占用保证金 原总占用保证金-这一单占用保证金
        $totalCapital=$totalCapital+$loss;//总资产 原总资产+这单盈亏+这单占用保证金
        $usableMargin=$usableMargin+$loss+$uccupy;//可用保证金 原可用保证金+这单盈亏+这单占用保证金

        $greatNumber=$this->positionsNumber($uid);
        /******************历史平仓单*******************/
        $history['uid']=$uid;                        //关联id
        $history['positions_number']=$detailList[0]['positions_number'];         //持仓单号
        $history['type']=$detailList[0]['type'];                   //单据类型
        $history['good_name']=$detailList[0]['good_name'];              //商品名称
        if($detailList[0]['direction']=='买'){
            $history['direction']='卖';                   //方向
            $history['positions_price']=$maichuMak;     //平仓价
        }else{
            $history['direction']='买';
            $history['positions_price']=$mairuMak;
        }

        $history['number']=$detailList[0]['number'];                   //数量
        //$history['commission']=0-$charge;   //手续费
        $history['instruct']='平仓';                  //指令
        $history['loss']=$loss1;                   //单笔盈亏
        $history['charge']=0-$charge;                 //手续费
        $history['turnover_number']=$greatNumber;             //成交编号
        $history['time']=time();               //成交时间
        //$history['uccupy']='';               //占用保证金
        /*******************当日成交*******************/
        $bargain['uid']=$uid;
        $bargain['time']=time();//成交时间
        $bargain['good_name']='现货白银';//商品名称
        if($baysell==0){
            $bargain['direction']='买';//方向
            $bargain['bargain_price']=$mairuMak;//成交价格
        }else{
            $bargain['direction']='卖';//方向
            $bargain['bargain_price']=$maichuMak;//成交价格
        }
        $bargain['instruct']='平仓';//指令
        $bargain['number']=$number;//数量
        $bargain['type']=$detailList[0]['type'];//类型
        $bargain['uccupy']=$uccupy;//占用保证金
        $bargain['commission']=-$charge;//手续费
        $bargain['loss']=$loss1;//单笔盈亏
        $bargain['positions_number']=$detailList[0]['positions_number'];//持仓单号
        $bargain['positions_bargain']=$greatNumber;//成交编号
        /*******************资金流水*******************/
        //flow($uid,$price,$name,$positionsNumber,$total) $uid关联id $price变动资金 $name业务名称 $positionsNumber关联单号 $total变后资金
        $price1=0-$charge;$totalCapital1=$totalCapital-$loss1;
        $price2=$loss1;$totalCapital_2=$totalCapital;
        $flow1=$this->flow($uid,$price1,'手续费',$positionsNumber,$totalCapital1);
        $flow2=$this->flow($uid,$price2,'平仓盈亏',$positionsNumber,$totalCapital_2);
        /*******************用户资金变化****************/
        $capital['total_capital']=$totalCapital;//总资产
        $capital['total_profit_loss']=$totalLoss;//总盈亏
        $capital['usable_margin']=$usableMargin;//可用保证金
        $capital['uccupy_margin']=$uccupyMargin;//占用保证金
        /*******************写入数据库******************/
        if($flow1&&$flow2&&$todayBargain->add($bargain)&&$historyModel->add($history)&&$detailModel->where('positions_number='.$positionsNumber)->delete()&&$capitalModel->where('uid='.$uid)->save($capital)){
            $this->active($ckUid);//用户活跃度+1
            $arr=array(
                'status'  =>  1,
                'info'   =>  '操作成功',
                'data'  =>  ''
            );
            echo json_encode($arr);
        }else{
            $arr=array(
                'status'  =>  0,
                'info'   =>  '操作失败',
                'data'  =>  ''
            );
            echo json_encode($arr);
        }
    }

    /*************************************统一平仓**********************************************/
    public function totalSell(){
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
        $baysell=I('baysell');                         //0为买1为卖
        $mairuMak=$this->mairu();                 //行情买入点数
        $maichuMak=$this->maichu();               //行情卖出点数

        $capitalModel=M('UserCapital');    //用户资金
        $detailModel=M('PositionsDetail'); //持仓明细
        $historyModel=M('HistoryDetail');  //历史记录
        $todayBargain=M('TodayBargain');   //当日成交

        if($baysell==0){
            $direction='卖';
        }else{
            $direction='买';
        }
        $where['direction']=$direction;
        $where['uid']=$ckUid;
        $where['freeze_uccupy']=array('EXP','IS NULL');
        $detailCount=$detailModel->where($where)->count();//计算负荷条件的仓数
        $detailList=$detailModel->where($where)->select();//持仓明细
        $uid=$ckUid;
        for($count=0;$count<$detailCount;$count++){

            $positionsNumber=$detailList[$count]['positions_number'];
            //$detailList=$detailModel->where('positions_number='.$positionsNumber)->select();           //持仓明细
            $capitalList=$capitalModel->where('uid='.$uid)->select();         //用户资金

            $uccupy=$detailList[$count]['uccupy'];                                  //这单占用保证金
            $number=$detailList[$count]['number'];                                  //手数
            $positionsPrice=$detailList[$count]['positions_price'];                  //持仓价
            $totalCapital=$capitalList[$count]['total_capital'];                   //总资产
            $totalProfitLoss=$capitalList[$count]['total_profit_loss'];           //总盈亏
            $usableMargin=$capitalList[$count]['usable_margin'];                   //可用保证金
            $uccupyMargin=$capitalList[$count]['uccupy_margin'];                   //占用保证金
            /******************计算*******************/


            if($baysell==0){
                //$overnight=$maichuMak*15*$number*0.0002*$day;//过夜费 卖出行情*15*手数*0.0002*天数
                $charge=$mairuMak*15*0.0008*$number;//手续费 卖出行情*15*0.0008*手数
                $loss1=($mairuMak-$positionsPrice)*15*$number;//点数盈亏 （卖出行情-买入行情）*15*手数

            }else{
                //$overnight=$mairuMak*15*$number*0.0002*$day;//过夜费 卖出行情*15*手数*0.0002*天数
                $charge=$maichuMak*15*0.0008*$number;//手续费 卖出行情*15*0.0008*手数
                $loss1=($maichuMak-$positionsPrice)*15*$number;//点数盈亏 （卖出行情-买入行情）*15*手数
            }
            $loss=$loss1-$charge;//这单盈亏 点数盈亏-手续费
            $totalLoss=$totalProfitLoss+$loss;//总盈亏 当前盈亏+这单盈亏
            $uccupyMargin=$uccupyMargin-$uccupy;//总占用保证金 原总占用保证金-这一单占用保证金
            $totalCapital=$totalCapital+$loss;//总资产 原总资产+这单盈亏+这单占用保证金
            $usableMargin=$usableMargin+$loss+$uccupy;//可用保证金 原可用保证金+这单盈亏+这单占用保证金

            $greatNumber=$this->positionsNumber($uid);
            /******************历史平仓单*******************/
            $history['uid']=$uid;                        //关联id
            $history['positions_number']=$detailList[$count]['positions_number'];         //持仓单号
            $history['type']=$detailList[$count]['type'];                   //单据类型
            $history['good_name']=$detailList[$count]['good_name'];              //商品名称
            if($detailList[$count]['direction']=='买'){
                $history['direction']='卖';                   //方向
                $history['positions_price']=$maichuMak;     //平仓价
            }else{
                $history['direction']='买';
                $history['positions_price']=$mairuMak;
            }

            $history['number']=$detailList[$count]['number'];                   //数量
            //$history['commission']=0-$charge;   //手续费
            $history['instruct']='平仓';                  //指令
            $history['loss']=$loss1;                   //单笔盈亏
            $history['charge']=0-$charge;                 //手续费
            $history['turnover_number']=$greatNumber;             //成交编号
            $history['time']=time();               //成交时间
            //$history['uccupy']='';               //占用保证金
            /*******************当日成交*******************/
            $bargain['uid']=$uid;
            $bargain['time']=time();//成交时间
            $bargain['good_name']='现货白银';//商品名称
            if($baysell==0){
                $bargain['direction']='买';//方向
                $bargain['bargain_price']=$mairuMak;//成交价格
            }else{
                $bargain['direction']='卖';//方向
                $bargain['bargain_price']=$maichuMak;//成交价格
            }
            $bargain['instruct']='平仓';//指令
            $bargain['number']=$number;//数量
            $bargain['type']=$detailList[0]['type'];//类型
            $bargain['uccupy']=$uccupy;//占用保证金
            $bargain['commission']=-$charge;//手续费
            $bargain['loss']=$loss1;//单笔盈亏
            $bargain['positions_number']=$detailList[$count]['positions_number'];//持仓单号
            $bargain['positions_bargain']=$greatNumber;//成交编号
            /*******************资金流水*******************/
            //flow($uid,$price,$name,$positionsNumber,$total) $uid关联id $price变动资金 $name业务名称 $positionsNumber关联单号 $total变后资金
            $price1=0-$charge;$totalCapital1=$totalCapital-$loss1;
            $price2=$loss1;$totalCapital_2=$totalCapital;
            $flow1=$this->flow($uid,$price1,'手续费',$positionsNumber,$totalCapital1);
            $flow2=$this->flow($uid,$price2,'平仓盈亏',$positionsNumber,$totalCapital_2);
            /*******************用户资金变化****************/
            $capital['total_capital']=$totalCapital;//总资产
            $capital['total_profit_loss']=$totalLoss;//总盈亏
            $capital['usable_margin']=$usableMargin;//可用保证金
            $capital['uccupy_margin']=$uccupyMargin;//占用保证金
            /*******************写入数据库******************/
            if($flow1&&$flow2&&$todayBargain->add($bargain)&&$historyModel->add($history)&&$detailModel->where('positions_number='.$positionsNumber)->delete()&&$capitalModel->where('uid='.$uid)->save($capital)){
                $this->active($ckUid);//用户活跃度+1
                if($count==($detailCount-1)){
                    $arr=array(
                        'status'  =>  1,
                        'info'   =>  '操作成功',
                        'data'  =>  ''
                    );
                    echo json_encode($arr);
                }
            }else{
                $arr=array(
                    'status'  =>  0,
                    'info'   =>  '操作失败',
                    'data'  =>  ''
                );
                echo json_encode($arr);
            }


        }



    }
}