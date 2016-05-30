<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 15-5-19
 * Time: 下午5:40
 */
namespace Admin\Controller;
class AdminController extends CommonController {
    public function index(){
        $userModel=M('AdminUser');

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
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出

        //设置导航缓存
        S('admin','active',1);
        $this->assign('admin',S('admin'));

        $this->display('index');
    }
    public function del($id=''){
        $userModel=M('AdminUser');
        if($userModel->where('id='.$id)->delete()){
            $this->success('删除成功！');
        }else{
            $this->error('删除失败！');
        }
    }
    public function alldel(){
        $userModel=M('AdminUser');
        $id = I('id');
        //判断id是数组还是一个数值
        if(!$id){
            $this->error('没有选中数据！');
            exit;
        }
        if(is_array($id)){
            $where['id'] = array('in',$id);
        }else{
            $where['id'] = $id;
        }
        //dump($where);
        $list=$userModel->where($where)->delete();
        if($list!==false){
            $this->success("成功删除{$list}条！");
        }else{
            $this->error('删除失败！');
        }
    }
    public function addshow(){
        //设置导航缓存
        S('admin','active',1);
        $this->assign('admin',S('admin'));

        $this->display('add');
    }
    public function add(){
        if($_POST){
            $model=M('AdminUser');
            $password=I('password');
            if($model->create()){
                $data['username']=I('username');
                if(!$model->where($data)->select()){
                    $model->create_ip=get_client_ip();
                    $model->create_time=time();
                    $model->time=time();
                    $model->ip=get_client_ip();
                    $model->password=md5($password);
                    if($model->add()){
                        $this->success('添加成功！',U('index'));
                    }else{
                        $this->error('添加失败！');
                    }
                }else{
                    $this->error('用户名已存在！');
                }

            }else{
                $this->error('添加失败！');
            }
        }else{
            $this->error('非法操作！');
        }
    }
    public function edit($id=''){
        $userModel=M('AdminUser');
        $list=$userModel->where('id='.$id)->select();
        $this->assign('list',$list);

        //设置导航缓存
        S('admin','active',1);
        $this->assign('admin',S('admin'));

        $this->display('edit');
    }
    public function save($id=''){
        if($_POST){
            $userModel=M('AdminUser');
            if($userModel->create()){
                $oldPassword=I('oldPassword');
                $newPassword=I('newPassword');
                $username=I('username');
                $user['id']=$id;
                $user['password']=md5($oldPassword);
                $map['id']=array('not in',$id);
                $map['username']=$username;
                if($userModel->where($map)->find()){
                    $this->error('用户名已存在！');
                }else{
                    if($userModel->where($user)->find()){
                        $userModel->username=$username;
                        $userModel->edit_time=time();
                        if($userModel->where('id='.$id)->save()){
                            if($newPassword){
                                $data['password']=md5($newPassword);
                                if($userModel->where('id='.$id)->save($data)){
                                    $this->success('修改成功',U('index'));
                                }else{
                                    $this->error('修改失败！');
                                }
                            }else{
                                $this->success('用户名修改成功！',U('index'));
                            }
                        }else{
                            $this->error('修改失败！');
                        }
                    }else{
                        $this->error('密码错误请重新输入！');
                    }
                }

            }else{
                $this->error('修改失败！');
            }
        }else{
            $this->error('非法操作！');
        }
    }
}