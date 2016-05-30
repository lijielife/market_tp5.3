<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 15-5-19
 * Time: 上午11:26
 */
namespace Admin\Controller;
class CapitalController extends CommonController {
    public function index(){
        $userModel=M('UserCapital');
//        $userMessage=M('UserMessage');

        $count      = $userModel->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $Page->setConfig('next','下一页');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('header','条记录');
        $Page->setConfig('first','首页');
        $Page->setConfig('last','末页');
        $Page->setConfig('theme','<span class="rows">共 %TOTAL_ROW% 条记录</span> %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $userModel->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

//        foreach($list as $key=>$uid){
//        }
//
//        //获取用户信息
//        $post_data['uid']=$uid;
//        $url=$this->userUrl;
//        $res=$this->request_post($url,$post_data);
//        $userMessage=json_decode($res,true);

        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集

        //设置导航缓存
        S('capital','active',1);
        $this->assign('capital',S('capital'));

        $this->display('Capital/index');
    }
    public function del($id=''){
        $userModel=M('UserCapital');
        if($userModel->where('uid='.$id)->delete()){
            $this->success('删除成功！');
        }else{
            $this->error('删除失败！');
        }
    }
    public function alldel(){
        $userModel=M('UserCapital');
        $id = I('id');
        //判断id是数组还是一个数值
        if(!$id){
            $this->error('没有选中数据！');
            exit;
        }
        if(is_array($id)){
            $where['uid'] = array('in',$id);
        }else{
            $where['uid'] = $id;
        }
        //dump($where);
        $list=$userModel->where($where)->delete();
        if($list!==false){
            $this->success("成功删除{$list}条！");
        }else{
            $this->error('删除失败！');
        }
    }
    public function edit($id=''){
        $userModel=M('UserCapital');
        $list=$userModel->where(array('uid'=>$id))->select();
        //获取用户信息
        $post_data['uid']=$id;
        $url=$this->userUrl;
        $res=$this->request_post($url,$post_data);
        $userMessage=json_decode($res,true);
        $list[0]['username']=$userMessage[0]['name'];

        $this->assign('list',$list);

        //设置导航缓存
        S('capital','active',1);
        $this->assign('capital',S('capital'));

        $this->display('edit');
    }
    public function addshow(){
        //设置导航缓存
        S('capital','active',1);
        $this->assign('capital',S('capital'));

        $this->display('add');
    }
    public function add(){
        $uid=I('uid');
        $userModel=M('UserCapital');

        //获取用户信息
        $post_data['uid']=$uid;
        $url=$this->userUrl;
        $res=$this->request_post($url,$post_data);
//        $userMessage=json_decode($res,true);
        if($res=='false'){
            $this->error('用户还未注册！请先注册');
        }else{
            if($userModel->where(array('uid'=>$uid))->find()){
                $this->error('已经开过户，无需再次开户！');
            }else{
                $data['uid']=$uid;
                if($userModel->add($data)){
                    $this->success('开户成功',U('Capital/index'));
                }
            }
        }
    }
    public function addMoney($id=''){
        $userModel=M('UserCapital');
        $userList=$userModel->where('uid='.$id)->find();
        $initial_capital=$userList['initial_capital'];
        $total_capital=$userList['total_capital'];
        $usable_margin=$userList['usable_margin'];
        $money['initial_capital']=$initial_capital+100000.00;
        $money['total_capital']=$total_capital+100000.00;
        $money['usable_margin']=$usable_margin+100000.00;
        if($userModel->where('uid='.$id)->save($money)){
            $this->success('入金成功！',U('Capital/index'));
        }else{
            $this->error('入金失败！');
        }
    }
    public function subtractMoney($id=''){
        $userModel=M('UserCapital');
        $userList=$userModel->where('uid='.$id)->find();
        $initial_capital=$userList['initial_capital'];
        $total_capital=$userList['total_capital'];
        $usable_margin=$userList['usable_margin'];
        $money['initial_capital']=$initial_capital-100000.00;
        $money['total_capital']=$total_capital-100000.00;
        $money['usable_margin']=$usable_margin-100000.00;
        if($userModel->where('uid='.$id)->save($money)){
            $this->success('减金成功！',U('Capital/index'));
        }else{
            $this->error('减金失败！');
        }
    }
}