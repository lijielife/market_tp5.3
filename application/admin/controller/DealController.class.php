<?php
/**
 * Created by PhpStorm.
 * User: xiaoyu
 * Date: 2015/10/12
 * Time: 15:13
 */
namespace Admin\Controller;
class DealController extends CommonController{

    public function index(){
        $model = D('Deal');
        $map = array();
        $datas = $model->getLists($map);

        $this->assign('lists',$datas['lists']);
        $this->assign('page',$datas['page']);

        $this->display();
    }

    public function view(){
        $model = D('Deal');
        $id = I('id', '', 'int');
        $data = $model->where(array('id' => $id))->find();
        $this->assign('data', $data);
        $this->display();
    }

    public function del(){

    }

    public function delete(){

    }
}