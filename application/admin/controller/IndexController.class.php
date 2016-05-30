<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2015/7/20
 * Time: 15:54
 */
namespace Admin\Controller;
class IndexController extends CommonController{
    public function index(){
        A('Capital')->index();
    }
}