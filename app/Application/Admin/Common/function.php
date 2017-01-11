<?php
/*
 * 后台系统函数库
 * @author zhijian.chen
 * @email 1114526565@qq.com
 * @date 2016/3/7
 */

/**
 * 发送邮件
 * @param string $to      收件人
 * @param string $subject 主题
 * @param string $body    内容
 * @param array $config
 * @return bool
 */
function sendEmail($to, $subject, $body, $config = array()){
        $email = new \Libs\Util\Email($config);
	$email->send($to, $subject, $body);
	return $email->result;
}

/**
 * 配置文件信息
 * @param string $key 字段键值
 * @param string $field 字段值
 * @reutn array  配置值
 */
function configs($key,$field){
    if(S('config_list')){
       $list = S('config_list');
    }else{
        $config_db = new Admin\Model\ConfigModel();
        $list = $config_db->getListByKey();
        S('config_list',$list);
   }
   if(empty($key)){
       return $list;
   }else{
       if(empty($field)){
         return $list[$key];  
       }else{
          return $list[$key][$field];  
       }
   }
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
	$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
	for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
	return round($size, 2) . $delimiter . $units[$i];
}
/**
 * 下载文件
 * @param string $fileurl 文件地址
 */
function downfile($fileurl,$filename='data.gz')
{
    $filename=$fileurl;
    $file   =   fopen($filename, "rb");
    Header( "Content-type:   application/octet-stream ");
    Header( "Accept-Ranges:   bytes ");
    Header( "Content-Disposition:   attachment;   filename=$filename");
    $contents = "";
    while (!feof($file)) {
      $contents .= fread($file, 8192);
    }
    echo $contents;
    fclose($file);
}
/**
 * 树状菜单空格
 */
 function level_space($level){
        //空格
       $space="";
       for($i=0;$i<=($level-1)*2;$i++){
        if($level!=1){
          $space .="|--";
        }
       } 
     return $space;
 }

