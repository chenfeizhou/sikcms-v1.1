<?php

/*
 * 系统函数库
 * @author zhijian.chen
 * @email 1114526565@qq.com
 * @date 2016/3/7
 */

/*
 * 系统缓存管理
 * @param mixed $name 缓存名称
 * @param mixed $value 缓存值
 * @param mixed $options 缓存参数
 * @return mixed
 */

function cache($name, $value = '', $options) {
    static $cache = '';
    if (!empty($cache)) {
        
    }
}

//产生一个制定长度随机字符串
function randString($len = 6) {
    $chars = array(
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
        "3", "4", "5", "6", "7", "8", "9"
    );
    $charsLen = count($chars) - 1;
    // 将数组打乱 
    shuffle($chars);
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}

/**
 * 系统标签读取字段处理函数
 * 参数：$content 模板内容 $list 将要输出的数据，格式为二维数组
 * 返回值:$str 处理后的字符串数据
 * 用法:<sikcms:article catid="2" limit="20" order="id desc" >
 *    <li>[field:id]<a href="{:U('Article/info',array('id'=>[field:id]))}">[field:title]</a></li>
 *  </sikcms:article>
 */
function field_list($content, $list) {
    preg_match_all('/\[field:(.*?)\]/', $content, $arry); //读取标签
    $tag = $arry[0]; //匹配标签
    $key = $arry[1]; //标签字段
    $str = '';
    for ($i = 0; $i < count($list); $i++) {
        $c = $content; //读取模板内容
        //替换标签
        foreach ($tag as $k => $v) {
            //如果有函数则执行函数后输出
            $arr = explode('|', $key[$k]);
            $th = $list[$i][$arr[0]];
            if ($arr[1]) {
                $arr[1] = str_replace('###', $list[$i][$arr[0]], $arr[1]);
                $a = '$th' . "=$arr[1]";
                eval($a . ";");
            }
            $c = str_replace($v, $th, $c);
        }
        $str .=$c;
    }
    return $str;
}

/**
 * 内容标签处理函数
 * $key 处理的值
 * $fun 函数
 * 用法：<articleinfo name="content" field="" />
 */
function field_info($key, $fun) {
    //有输出值则输出
    if ($key) {
        //如果使用函数
        if ($fun) {
            $return = str_replace('###', $key, $fun);
        } else {
            $return = $key;
        }
        //如果值不为空
        if ($return) {
            $str = '<?php ';
            $str .= 'echo ' . $return . ';';
            $str .= ' ?>';
        }  
    }
    return $str;
}
