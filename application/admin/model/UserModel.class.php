<?php
namespace Admin\Model;
use Think\Model;
class UserModel extends Model{

	/*function login($name)
	{
		echo $this->fields['username'];
		$res=$this->query("select * from ajax_user where username='$name'");
		return $res;
	}*/

	/*protected $_validate = array(
	//每个字段的详细验证内容
	array("user","require","用户名不能为空"),
	array("user","checkLength","用户名长度不符合要求",0,'callback'),
	array("pwd","require","密码不能为空"),
	array("pwd","checkLength","密码长度的要求是5~15位之间",0,'callback'),
	array("pwd","repwd","两次密码输入不一致",0,'confirm'),
	array("qq","require","qq必须填写"),
	//array("cdate","require","时间不能为空",callback),
	);

	//自定义验证方法，来验证用户名的长度是否合法
	//$date形参  可以写成任意如 $AA  $bb
	function checkLength($data){
		//$data里存放的就是要验证的用户输入的字符串
		if(strlen($data)<5||strlen($data)>15){
			return false;
		}else{
			return true;
		}
	}
	//返回访问者的IP地址
	function getIp(){
		return $_SERVER['REMOTE_ADDR'];
	}
	function time(){
		return date("Y-m-d H:i:s");
	}

	protected $_auto=array(

	array("pwd","md5",3,'function'),
	array("time","time",3,'callback'),
	array("creatip","getIp",3,'callback'),

	);*/

}