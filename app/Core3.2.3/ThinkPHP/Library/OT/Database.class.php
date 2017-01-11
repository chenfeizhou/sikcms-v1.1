<?php

/* ---------------------------------------------
 * @数据库备份还原控制器
 * @第一：本控制器依靠config中的配置运行，须在config中新增配置：
 * 		'DB_PATH_NAME'=> 'db',        //备份目录名称,主要是为了创建备份目录；
 * 		'DB_PATH'     => './db/',     //数据库备份路径必须以 / 结尾；
 * 		'DB_PART'     => '20971520',  //该值用于限制压缩后的分卷最大长度。单位：B；建议设置20M
 * 		'DB_COMPRESS' => '1',         //压缩备份文件需要PHP环境支持gzopen,gzwrite函数        0:不压缩 1:启用压缩
 * 		'DB_LEVEL'    => '9',         //压缩级别   1:普通   4:一般   9:最高
 * @第二：本控制器依赖ThinkPHP/Library/OT/Database.class.php
 * @第三：在Application/Common/function.php里面加个format_bytes()函数；
  /**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 *
  function format_bytes($size, $delimiter = '') {
  $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
  for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
  return round($size, 2) . $delimiter . $units[$i];
  }
 * @第四：模版文件，Home/View/Database下面的2个文件 export.html备份数据库  import.html还原数据库
 * @第五：模版文件中需jquery支持（模版中有ajax），并且要想实现模版中的多选需引用以下js：
  <script>
  ;$(function(){
  //全选的实现
  $(".check-all").click(function(){
  $(".ids").prop("checked", this.checked);
  });
  $(".ids").click(function(){
  var option = $(".ids");
  option.each(function(i){
  if(!this.checked){
  $(".check-all").prop("checked", false);
  return false;
  }else{
  $(".check-all").prop("checked", true);
  }
  });
  });

  });
  </script>
 * @第六：引用方法：<a href="{:U('Database/index',array('type'=>'export'))}">备份数据库</a>
 * 		          <a href="{:U('Database/index',array('type'=>'import'))}">还原数据库</a>
 * Author: 枫LT <957987132@qq.com>
 * ---------------------------------------------
 */

namespace OT;

use Think\Db;

//数据导出模型
class Database {

    /**
     * 文件指针
     * @var resource
     */
    private $fp;

    /**
     * 备份文件信息 part - 卷号，name - 文件名
     * @var array
     */
    private $file;

    /**
     * 当前打开文件大小
     * @var integer
     */
    private $size = 0;

    /**
     * 备份配置
     * @var integer
     */
    private $config;

    /**
     * 数据库备份构造方法
     * @param array  $file   备份或还原的文件信息
     * @param array  $config 备份配置信息
     * @param string $type   执行类型，export - 备份数据， import - 还原数据
     */
    public function __construct($file, $config, $type = 'export') {
        $this->file = $file;
        $this->config = $config;
    }

    /**
     * 打开一个卷，用于写入数据
     * @param  integer $size 写入数据的大小
     */
    private function open($size) {
        if ($this->fp) {
            $this->size += $size;
            if ($this->size > $this->config['part']) {
                $this->config['compress'] ? @gzclose($this->fp) : @fclose($this->fp);
                $this->fp = null;
                $this->file['part'] ++;
                session('backup_file', $this->file);
                $this->create();
            }
        } else {
            $backuppath = $this->config['path'];
            $filename = "{$backuppath}{$this->file['name']}-{$this->file['part']}.sql";
            if ($this->config['compress']) {
                $filename = "{$filename}.gz";
                $this->fp = @gzopen($filename, "a{$this->config['level']}");
            } else {
                $this->fp = @fopen($filename, 'a');
            }
            $this->size = filesize($filename) + $size;
        }
    }

    /**
     * 写入初始数据
     * @return boolean true - 写入成功，false - 写入失败
     */
    public function create() {
        $sql = "/* \n";
        $sql .= "Think MySQL Data Transfer \n";
        $sql .= "\n";
        $sql .= "Host     : " . C('DB_HOST') . "\n";
        $sql .= "Port     : " . C('DB_PORT') . "\n";
        $sql .= "Database : " . C('DB_NAME') . "\n";
        $sql .= "\n";
        $sql .= "Part : #{$this->file['part']}\n";
        $sql .= "Date : " . date("Y-m-d H:i:s") . "\n";
        $sql .= "*/ \n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
        return $this->write($sql);
    }

    /**
     * 写入SQL语句
     * @param  string $sql 要写入的SQL语句
     * @return boolean     true - 写入成功，false - 写入失败！
     */
    private function write($sql) {
        $size = strlen($sql);

        //由于压缩原因，无法计算出压缩后的长度，这里假设压缩率为50%，
        //一般情况压缩率都会高于50%；
        $size = $this->config['compress'] ? $size / 2 : $size;

        $this->open($size);
        return $this->config['compress'] ? @gzwrite($this->fp, $sql) : @fwrite($this->fp, $sql);
    }

    /**
     * 备份表结构
     * @param  string  $table 表名
     * @param  integer $start 起始行数
     * @return boolean        false - 备份失败
     */
    public function backup($table, $start) {
        //创建DB对象
        $db = Db::getInstance();
        //备份表结构
        if (0 == $start) {
            $result = $db->query("SHOW CREATE TABLE `{$table}`");
            $sql = "\n";
            $sql .= "-- -----------------------------\n";
            $sql .= "-- Table structure for `{$table}`\n";
            $sql .= "-- -----------------------------\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= trim($result[0]['create table']) . ";\n\n";
            if (false === $this->write($sql)) {
                return false;
            }
        }

        //数据总数
        $result = $db->query("SELECT COUNT(*) AS count FROM `{$table}`");
        $count = $result['0']['count'];

        //备份表数据
        if ($count) {
            //写入数据注释
            if (0 == $start) {
                $sql = "-- -----------------------------\n";
                $sql .= "-- Records of `{$table}`\n";
                $sql .= "-- -----------------------------\n";
                $this->write($sql);
            }

            //备份数据记录默认1000
            $result = $db->query("SELECT * FROM `{$table}` LIMIT {$start}, 1000");

            foreach ($result as $row) {
                $row = array_map('mysql_real_escape_string', $row);
                $sql = "INSERT INTO `{$table}` VALUES ('" . implode("', '", $row) . "');\n";
                if (false === $this->write($sql)) {
                    return false;
                }
            }

            //还有更多数据
            if ($count > $start + 1000) {
                return array($start + 1000, $count);
            }
        }

        //备份下一表
        return 0;
    }

    public function import($start) {
        //还原数据
        $db = Db::getInstance();
        if ($this->config['compress']) {
            $gz = gzopen($this->file[1], 'r');
            $size = 0;
        } else {
            $size = filesize($this->file[1]);
            $gz = fopen($this->file[1], 'r');
        }

        $sql = '';
        if ($start) {
            $this->config['compress'] ? gzseek($gz, $start) : fseek($gz, $start);
        }
        for ($i = 0; $i < 1000; $i++) {
            $sql .= $this->config['compress'] ? gzgets($gz) : fgets($gz);
            if (preg_match('/.*;$/', trim($sql))) {
                if (false !== $db->execute($sql)) {
                    $start += strlen($sql);
                } else {
                    return false;
                }
                $sql = '';
            } elseif ($this->config['compress'] ? gzeof($gz) : feof($gz)) {
                return 0;
            }
        }
        return array($start, $size);
    }

    /**
     * 析构方法，用于关闭文件资源
     */
    public function __destruct() {
        $this->config['compress'] ? @gzclose($this->fp) : @fclose($this->fp);
    }

}
