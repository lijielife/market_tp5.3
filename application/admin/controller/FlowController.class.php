<?php
/**
 * Created by PhpStorm.
 * User: xiaoyu
 * Date: 2015/10/19
 * Time: 11:58
 */

namespace Admin\Controller;
class FlowController extends CommonController{

    public function index(){
        $model = D('Flow');
        $map = array();
        $datas = $model->getLists($map);

        $this->assign('lists',$datas['lists']);
        $this->assign('page',$datas['page']);

        $this->display();
    }

    public function view(){
        $model = D('Flow');
        $id = I('id', '', 'int');
        $data = $model->where(array('id' => $id))->find();
        $this->assign('data', $data);
        $this->display();
    }
}