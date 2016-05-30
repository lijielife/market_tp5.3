<?php
/**
 * 桌面端输出接口封装
 */
namespace app\desktop\libs;
class Response{

	/**
	 * [json json格式输出数据]
	 * @param  [type] $code    [状态码]
	 * @param  string $message [提示信息]
	 * @param  array  $data    [数据]
	 * @return [type]          [description]
	 */
	public static function json($code, $message = '', $data = array()){

		$result = array(
			'status' => $code,
			'info' => $message,
			'data' => $data,
			);

		$tmp_str = json_encode($result,JSON_UNESCAPED_UNICODE);
		header( "content-length:".strlen($tmp_str) );
		header( "uncompress-content-length:".strlen($tmp_str) );

		die($tmp_str);
	}

}