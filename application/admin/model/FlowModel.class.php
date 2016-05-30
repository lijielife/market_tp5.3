<?php
/**
 * Created by PhpStorm.
 * User: xiaoyu
 * Date: 2015/10/19
 * Time: 12:00
 */

namespace Admin\Model;
use Think\Model;
class FlowModel extends Model{

    protected $tableName        =   'positions_flow';


    /**
     * [getLists 获取数据库数据]
     * @param  [type]  $map      [查询条件]
     * @param  integer $pageSize [每页显示条数]
     * @return [type]            [description]
     */
    public function getLists($map,$pageSize = 15){
        $count = $this->where($map)->count();
        $page = new \Think\Page($count,$pageSize);
        $page->setConfig('next','下一页');
        $page->setConfig('prev','上一页');
        $page->setConfig('header','条记录');
        $page->setConfig('first','首页');
        $page->setConfig('last','末页');
        $page->setConfig('theme','<span class="rows">共 %TOTAL_ROW% 条记录</span> %FIRST% %UP_PAGE% %LINK_PAGE% %END% %DOWN_PAGE%');
        $show = $page->show();
        $limit = $page->firstRow.','.$page->listRows;
        $lists = $this->where($map)->limit($limit)->order('id DESC')->select();
        return array(
            'lists' => $lists,
            'page' => $show,
        );
    }
}