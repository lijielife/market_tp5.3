<?php
/**
 * 移动端输出接口封装
 */
namespace app\mobile\libs;
class Response{

	/**
	 * [json json格式输出数据]
	 * @param  [type] $code    [状态码]
	 * @param  string $message [提示信息]
	 * @param  array  $data    [数据]
	 * @param string $error_code [错误代码]
	 * @return [type]          [description]
	 */
	public static function json($code, $message = '', $data = array(), $error_code = '226200'){

		$result=array(
			'body'  =>$data,
			'header' =>array(
				'status'    =>  $code,
				'info'      =>  $message,
				'code'      =>  $error_code,
			),
		);

		$tmp_str = json_encode($result, JSON_UNESCAPED_UNICODE);

		die($tmp_str);
	}

}