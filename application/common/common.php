<?php
/**
 * ��ӡ���
 * @param  [type] $arr [description]
 * @return [type]      [description]
 */
function p($arr) {
	echo '<pre>' . print_r($arr, true) . '</pre>';
}
/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list,$field, $sortby='desc') {
    if(is_array($list)){
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
            $refer[$i] = &$data[$field];
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc':// 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ( $refer as $key=> $val)
            $resultSet[] = &$list[$key];
        return $resultSet;
    }
    return false;
}
/**
 * [createSignature 生成签名]
 * @param  [type] $uid     [用户id]
 * @param  [type] $time    [时间戳]
 * @param  [type] $echostr [随机字符串]
 * @return [type] $signature  [签名字符串]
 */
function createSignature($uid, $time, $echostr,$signature){
    $token = C('MARKET_SERVER_KEY');
    $tmpArr = array($uid, $time, $echostr, $token);
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode( $tmpArr );
    $resignature = sha1( $tmpStr );
//    echo $resignature."<br>";
    if($resignature == $signature){
        return true;
    }else{
        return false;
    }

}

/**
 * 判断是否开市
 */
function is_market_opening(){

    $time = time(); // 当前时间戳

    $week = date('N', $time); // 当前是周几

    $hour = date('H', $time); // 当前是几点

    $type = true;

    switch($week){
        case 1: // 周一8点开市
            if ($hour < 8){
                $type = false;
            }
            break;
        case 2:
        case 3:
        case 4:
        case 5:
            if (4 < $hour && $hour < 7){
                $type = false;
            }
            break;
        case 6: // 周六四点休市
            if (4 < $hour){
                $type = false;
            }
            break;
        case 7: // 周日休市
            $type = false;
            break;
        default:
    }

    return $type;
}
