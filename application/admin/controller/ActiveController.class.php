<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 15-5-20
 * Time: 上午11:53
 */
namespace Admin\Controller;
class ActiveController extends CommonController {
    public function index(){
        $userModel=M('UserCount');

        $timeStart=time()-1209600;
        $timeEnd=time();

        $time['time']=array('between',array($timeStart,$timeEnd));
        $list=$userModel->where($time)->select();

        $data = array();
        $arr = array();
        $active=array();
//        $userMessage=array('username','mobilephone','id','email','level','simulate_id','create_time');
        foreach($list as $v){//计算活跃度
            if($data[$v['uid']]){
                $data[$v['uid']]=$v['count']+$data[$v['uid']];
            }else{
                $data[$v['uid']]=$v['count'];
            }

        }
        foreach($data as $key=>$count){//生成新数组
//            $userData['id']=$key;
            $arr['id']=$key;
//            $userList=M('UserMessage')->where($userData)->find();
            $arr['totalCount']=$count;
//            foreach($userMessage as $uv){
//                $arr[$uv]=$userList[$uv];
//            }
            $active[]=$arr;
        }
        $active =list_sort_by($active,'totalCount');//数组排序

        $count = count($active);
        $p = new \Think\Page($count,25);
        $p->setConfig('next','下一页');
        $p->setConfig('prev','上一页');
        $p->setConfig('header','条记录');
        $p->setConfig('first','首页');
        $p->setConfig('last','末页');
        $p->setConfig('theme','<span class="rows">共 %TOTAL_ROW% 条记录</span> %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $lists = array_slice($active, $p->firstRow,$p->listRows);
        $page = $p->show();
        $this->assign('page',$page);
        $this->assign('list',$lists);

        //设置导航缓存
        S('active','active',1);
        $this->assign('active',S('active'));

        $this->display('index');
    }
    public function select(){
        $timeStart=I('start');
        $timeEnd=I('end');

        $timeStart=strtotime($timeStart);
        $timeEnd=strtotime($timeEnd);
        //echo $timeStart."<br>".$timeEnd;
        if(!$timeStart){
            $timeStart=time()-1209600;
        }
        if(!$timeEnd){
            $timeEnd=time();
        }
        $time['time']=array('between',array($timeStart,$timeEnd));

        $list =  M('UserCount')->where($time)->select();

        $data = array();
        $arr = array();
        $active=array();
//        $userMessage=array('username','mobilephone','id','email','level','simulate_id','create_time');
        foreach($list as $v){//计算活跃度
            if($data[$v['uid']]){
                $data[$v['uid']]=$v['count']+$data[$v['uid']];
            }else{
                $data[$v['uid']]=$v['count'];
            }

        }
        //var_dump($data);
        foreach($data as $key=>$count){//生成新数组
//            $userData['id']=$key;
//            $userList=M('UserMessage')->where($userData)->find();
            $arr['id']=$key;
            $arr['totalCount']=$count;
//            foreach($userMessage as $uv){
//                $arr[$uv]=$userList[$uv];
//            }
            $active[]=$arr;
        }
        $active = list_sort_by($active,'totalCount');//数组排序

        $count = count($active);
        $p = new \Think\Page($count,25);
        $p->setConfig('next','下一页');
        $p->setConfig('prev','上一页');
        $p->setConfig('header','条记录');
        $p->setConfig('first','首页');
        $p->setConfig('last','末页');
        $p->setConfig('theme','<span class="rows">共 %TOTAL_ROW% 条记录</span> %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $lists = array_slice($active, $p->firstRow,$p->listRows);
        $page = $p->show();
        $this->assign('page',$page);
        $this->assign('list',$lists);
        $this->assign('timeStart',$timeStart);
        $this->assign('timeEnd',$timeEnd);

        //设置导航缓存
        S('active','active',1);
        $this->assign('active',S('active'));

        $this->display('index');
    }
    public function excel(){
        $timeStart=I('timeStart');
        $timeEnd=I('timeEnd');
        //echo $timeStart."<br>".$timeEnd;
        if(!$timeStart){
            $timeStart=time()-1209600;
        }
        if(!$timeEnd){
            $timeEnd=time();
        }
        $time['time']=array('between',array($timeStart,$timeEnd));

        $info =  M('UserCount')->where($time)->select();

        //$list=$userModel->select();
        $data1 = array();
        $arr1 = array();
        $active=array();
//        $userMessage=array('username','mobilephone','id','email','level','simulate_id','create_time');
        foreach($info as $v){//计算活跃度
            if($data1[$v['uid']]){
                $data1[$v['uid']]=$v['count']+$data1[$v['uid']];
            }else{
                $data1[$v['uid']]=$v['count'];
            }
        }
        //var_dump($data);
        foreach($data1 as $key=>$count){//生成新数组
//            $userData['id']=$key;
//            $userList=M('UserMessage')->where($userData)->find();
            $arr1['id']=$key;
            $arr1['totalCount']=$count;
//            foreach($userMessage as $uv){
//                $arr1[$uv]=$userList[$uv];
//            }
            $active[]=$arr1;
        }
        $active = list_sort_by($active,'totalCount');//数组排序

        if(!empty($active)){
            $data = array();
            $arr = array();
            $key = array('id','totalCount');

            foreach($active as $val){
                foreach($key as $v){
                    $arr[$v] = $val[$v];
                }
                $data[] = $arr;
            }
            //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能import导入
            import("Org.Util.PHPExcel");
            import("Org.Util.PHPExcel.Writer.Excel5");
            import("Org.Util.PHPExcel.IOFactory.php");

            $filename="message_excel";
            $headArr=array('模拟ID','操作次数');
            $this->getExcel($filename,$headArr,$data);
//            var_dump($data);

        }else{
            $this->redirect('index');
        }
    }
    private function getExcel($fileName,$headArr,$data){
        //对数据进行检验
        if(empty($data) || !is_array($data)){
            die("data must be an array");
        }
        //检查文件名
        if(empty($fileName)){
            exit;
        }

        //$date = date("Y_m_d",time());
        $fileName .= "_".time().".xls";

        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel = new \PHPExcel();
        $objProps = $objPHPExcel->getProperties();

        //设置表头
        $key = ord("A");
        foreach($headArr as $v){
            $colum = chr($key);
            $zm = $colum.'1';
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $key += 1;
        }

        $objActSheet = $objPHPExcel->getActiveSheet();
        // 设置标题样式
        $thStyle = new \PHPExcel_Style();

        /*$thStyle->applyFromArray(array(
            'fill' 	=> array(
                'type'		=> \PHPExcel_Style_Fill::FILL_SOLID,
                'color'		=> array('argb' => '#66CD00')
            ),
            'borders' => array(
                'bottom'	=> array('style' => \PHPExcel_Style_Border::BORDER_THIN),
                'right'		=> array('style' => \PHPExcel_Style_Border::BORDER_THIN)
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        ));
        $objActSheet->setSharedStyle($thStyle, "A1:T1");*/

        //设置列宽
        $objActSheet->getColumnDimension('A')->setWidth(10);
        $objActSheet->getColumnDimension('B')->setWidth(20);

        //列高
        //  $objActSheet->getRowDimension('1')->setRowHeight(40);

        //行高
        $objActSheet->getDefaultRowDimension()->setRowHeight(20);

        // 设置默认字体和大小
        $objPHPExcel->getDefaultStyle()->getFont()->setName(iconv('utf-8', 'utf-8', '宋体'));
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(16);

        $k = ord('A');
        foreach($headArr as $v){
            $c = chr($k);
            $objActSheet->getStyle($c."1")->getFont()->setBold(true);
            $objActSheet->getStyle($c)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $k++;
        }
        /*$objActSheet->getStyle("A1")->getFont()->setBold(true);
        $objActSheet->getStyle("B1")->getFont()->setBold(true);
        $objActSheet->getStyle("C1")->getFont()->setBold(true);
        $objActSheet->getStyle("D1")->getFont()->setBold(true);*/


        $column = 2;
        //在数据是否有两个或以上的姓名
        /*$styleUser = array(
            'fill' 	=> array(
                'type'		=> \PHPExcel_Style_Fill::FILL_SOLID,
                'color'		=> array('argb' => '#D7D7D7')
            ),

        );
        $styleAllBorder = array(
            'borders' => array(
                'allborders' => array(
                    //'style' => PHPExcel_Style_Border::BORDER_THICK,//边框是粗的
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,//细边框
                    // 'color' => array('argb' => ''),
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );*/


//        $PeopleManager =   M('UserMessage');

        $sameUserColor = array();

        foreach($data as $key => $rows){ //行写入
            $span = ord("A");
            foreach($rows as $keyName=>$value){// 列写入
                $j = chr($span);
                //$objActSheet->setCellValue($j.$column," ".$value);
                //判断当前username 是否存在相同的username

                /*if($keyName == 'username' && ($PeopleManager->is_sameUser($value,$data))){

                    $sameUserColor[$column] =  $value;

                }*/
                $objActSheet->setCellValueExplicit($j.$column,$value, \PHPExcel_Cell_DataType::TYPE_STRING);
                //$objActSheet->getStyle($j.$column.':T'.$column)->applyFromArray($styleAllBorder);
                $span++;
            }
            $column++;
        }

        //处理名称相同的颜色
        /*$arr = array();
        $count = count($sameUserColor);
        foreach($sameUserColor as $key=>$val){

            $arr[$val][] = $key;

        }
        foreach($arr as $val){
            $color = '#'.dechex(rand(150,200)).dechex(rand(220,255)).dechex(rand(200,255));
            $styleUser = $PeopleManager->setColor($color);
            foreach($val as $v){
                $objActSheet->getStyle('A'.$v.':T'.$v)->applyFromArray($styleUser);
            }

        }*/

        $fileName = iconv("utf-8", "gb2312", $fileName);

        //重命名表
        // $objPHPExcel->getActiveSheet()->setTitle('test');
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;
    }
}