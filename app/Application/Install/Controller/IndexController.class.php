<?php

/*
 * 快乐筹cms
 * @author zhijian.chen
 * @email 1114526565@qq.com
 * @date 2016/3/11
 *
 */

namespace Install\Controller;

use Libs\Util\Dir;
use Think\Controller;

class IndexController extends Controller {

    //初始化
    public function _initialize() {
        header('Content-Type:text/html;charset=utf-8;');
        if (!defined('INSTALL')) {
            exit('禁止访问本模块');
        }
        //检查是否已经安装
        if (is_file(MODULE_PATH . 'install.lock')) {
            exit('你已安装过该系统，如需再次安装请删除' . MODEL_PATH . '站点下的install.lock文件');
        }
        $this->assign('Title', C('SYSTEM_NAME'))
                ->assign('Version', C('SIKCMS_VERSION'));
    }

    //安装第一步首页
    public function index() {
        $this->assign('step','step1');
        $this->display();
    }

    //第二步
    public function step2() {
        $error = 0;
        //mysql检测
        if (function_exists('mysql_connect')) {
            $mysql = '<span>&radic;</span>已安装';
        } else {
            $mysql = '<span>&radic;</span>出现错误';
            $error++;
        }
        //上传检测
        if (ini_get('file_uploads')) {
            $uploadFileSize = '<span>&radic;</span>' . ini_get('upload_max_filesize');
        } else {
            $uploadFileSize = '<span>&radic;</span>禁止上传';
            $error++;
        }
        //session 检测
        if (function_exists('session_start')) {
            $session = '<span>&radic;</span>支持';
        } else {
            $session = '<span>&radic;</span>不支持';
            $error++;
        }
        //目录权限检测
        $folder = array(
            '/',
            '/app/Application/Install/',
            '/app/Common/Conf/'
        );
        $dir = new Dir();
        $folderInfo = array();
        foreach ($folder as $dir) {
            $result = array('dir' => $dir);
            $path = SITE_PATH . $dir;
            //是否可读
            if (is_readable($path)) {
                $result['is_readable'] = '<span>&radic;</span>可读';
            } else {
                $result['is_readable'] = '<span>&radic;</span>不可读';
                $error++;
            }
            //是否可写
            if (is_writable($path)) {
                $result['is_writeable'] = '<span>&radic;</span>可写';
            } else {
                $result['is_writeable'] = '<span>&radic;</span>不可写';
                $error++;
            }
            $folderInfo[] = $result;
        }
        //php内置函数检测
        $function = array(
            'mb_strlen' => function_exists('mb_strlen'),
            'curl_init' => function_exists('curl_init'),
        );
        foreach ($function as $rs) {
            if ($rs == false) {
                $error++;
            }
        }
        $this->assign('os', PHP_OS)
                ->assign('step','step2')
                ->assign('function', $function)
                ->assign('error', $error)
                ->assign('mysql', $mysql)
                ->assign('uploadSize', $uploadFileSize)
                ->assign('session', $session)
                ->assign('folderInfo', $folderInfo)
                ->assign('php_version', @phpversion());
        $this->display('index');
    }

    //第三步
    public function step3() {
  
        //地址
        $scriptName = !empty($_SERVER["REQUEST_URI"]) ? $scriptName = $_SERVER["REQUEST_URI"] : $scriptName = $_SERVER['PHP_SELF'];
        $rootpath = @preg_replace("/\/(I|i)nstall\/index\.php(.*)$/", "/", $scriptName);
        $sysPort = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] ? 'https://' : 'http';
        $domain = empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
        if ((int) $sysPort != 80) {
            $domain .=":" . $_SERVER['SERVER_PORT'];
        }
        $domain = $sysPort . $domain . $rootpath;
        $parse_url = parse_url($domain);
        $parse_url['path'] = str_replace('install.php', '', $parse_url['path']);
        $this->assign('parse_url', $parse_url);
        $this->assign('step','step3');
        $this->display('index');
    }

    //第四步
    public function step4() {
        $this->assign('data', json_encode($_POST));
        $this->assign('step','step4');
        $this->display('index');
    }

    //安装完成
    public function step5() {
        @unlink(RUNTIME_PATH . APP_MODE . '~runtime.php');
        @touch(MODULE_PATH . 'install.lock');
        $this->assign('step','step5');
        $this->display('index');
    }

    //数据库安装
    public function mysql() {
        $n = intval($_GET['n']);
        $arr = array();
        $dbHost = trim($_POST['dbhost']);
        $dbPort = trim($_POST['dbport']);
        $dbName = trim($_POST['dbname']);
        $dbHost = empty($dbPort) || $dbPort == 3306 ? $dbHost : $dbHost . ":" . $dbPort;
        $dbUser = trim($_POST['dbuser']);
        $dbPwd = trim($_POST['dbpwd']);
        $dbPrefix = empty($_POST['dbprefix']) ? 'think_' : trim($_POST['dbprefix']);
        $username = trim($_POST['manager']);
        $password = trim($_POST['manager_pwd']);
        //网站名称
        $site_name = addslashes(trim($_POST['sitename']));
        //网站域名
        $site_url = trim($_POST['siteurl']);
        $_site_url = parse_url($site_url);
        //附件地址
        $sitefileurl = $_site_url['path'] . "d/file/";
        //描述
        $seofileurl = trim($_POST['siteinfo']);
        //关键词
        $seo_keywords = trim($_POST['sitekeywords']);
        //测试数据
        $testdata = (int) $_POST['testdata'];
        //邮箱地址
        $siteemail = trim($_POST['manager_email']);
        $conn = @mysql_connect($dbHost, $dbUser, $dbPwd);
        if (!$conn) {
            $arr['msg'] = '连接数据库失败!';
            echo json_encode($arr);exit;
        }
        mysql_query("SET NAMES 'utf8'");
        $version = mysql_get_server_info($conn);
        if ($version < 5.0) {
            $arr['msg'] = '数据库版本太低!';
            echo json_encode($arr);exit;
        }
        if (!mysql_select_db($dbName, $conn)) {
            if (!mysql_query("CREATE DATABASE IF NOT EXISTS `" . $dbName . "` DEFAULT CHARACTER SET utf8;", $conn)) {
                $arr['msg'] = '数据库 ' . $dbName . ' 不存在，也没权限创建新的数据库！';
                 echo json_encode($arr);exit;
            }
            if (empty($n)) {
                $arr['n'] = 1;
                $arr['msg'] = "<h4>成功创建数据库:{$dbName}</h4><br>";
                $this->ajaxReturn($arr);
                exit;
            }
            mysql_select_db($dbName, $conn);
        }
        //读取数据文件
        $sqldata = file_get_contents(MODULE_PATH . 'Data/sikcms.sql');
        //读取测试数据
        if ($testdata) {
            $sqldataDemo = file_get_contents(MODULE_PATH . 'Data/sikcms_demo.sql');
            $sqldata = $sqldata . "\r\n" . $sqldataDemo;
        } else {
            //不加测试数据的时候，删除d目录的文件
            try {
                $Dir = new Dir();
                $Dir->delDir(SITE_PATH . 'd/file/contents/');
            } catch (Exception $exc) {
                
            }
        }
        $sqlFormat = sql_split($sqldata, $dbPrefix);
        //执行sql语句
        $counts = count($sqlFormat);
        for ($i = $n; $i < $counts; $i++) {
            $sql = trim($sqlFormat[$i]);
            if (strstr($sql, 'CREATE TABLE')) {
                preg_match('/CREATE TABLE `([^ ]*)`/', $sql, $matches);
                mysql_query("DROP TABLE IF EXISTS `$matches[1]");
                $sql = str_replace('chou_', $dbPrefix, $sql);
                $ret = mysql_query($sql);
                if ($ret) {
                    $message = '<p class="infobox success-bg"><span class="correct_span">&radic;</span>创建数据表' . $matches[1] . '，完成</p> ';
                } else {
                    $message = '<p class="infobox error-bg"><span class="correct_span error_span">&radic;</span>创建数据表' . $matches[1] . '，失败</p>';
                }
                $i++;
                $arr = array('n' => $i, 'msg' => $message,'sql'=>$sql);
                echo json_encode($arr);
                exit;
            } else {
                $sql = str_replace('chou_', $dbPrefix, $sql);
                $ret = mysql_query($sql);
                $message = '';
                $arr = array('n' => $i, 'msg' => $message);
            }
        }
        if ($i === 999999)
            exit;
        
        //更新配置信息
        mysql_query("UPDATE `{$dbPrefix}config` SET  `value` = '$site_name' WHERE valuename='sitename'");
        mysql_query("UPDATE `{$dbPrefix}config` SET  `value` = '$site_url' WHERE valuename='siteurl' ");
        mysql_query("UPDATE `{$dbPrefix}config` SET  `value` = '$sitefileurl' WHERE valuename='sitefileurl' ");
        mysql_query("UPDATE `{$dbPrefix}config` SET  `value` = '$seo_description' WHERE valuename='siteinfo'");
        mysql_query("UPDATE `{$dbPrefix}config` SET  `value` = '$seo_keywords' WHERE valuename='sitekeywords'");
        mysql_query("UPDATE `{$dbPrefix}config` SET  `value` = '$siteemail' WHERE valuename='siteemail'");
        //读取配置文件，并替换真实配置数据 
        $strConfig = file_get_contents(MODULE_PATH . 'Data/mysql.inc.php');
        $strConfig = str_replace('{DB_HOST}', $dbHost, $strConfig);
        $strConfig = str_replace('{DB_NAME}', $dbName, $strConfig);
        $strConfig = str_replace('{DB_USER}', $dbUser, $strConfig);
        $strConfig = str_replace('{DB_PWD}', $dbPwd, $strConfig);
        $strConfig = str_replace('{DB_PORT}', $dbPort, $strConfig);
        $strConfig = str_replace('{DB_PREFIX}', $dbPrefix, $strConfig);
        $strConfig = str_replace('{AUTHCODE}', randString(18), $strConfig);
        $strConfig = str_replace('{COOKIE_PREFIX}', randString(3) . "_", $strConfig);
        $strConfig = str_replace('{DATA_CACHE_PREFIX}', randString(3) . "_", $strConfig);
        @file_put_contents(COMMON_PATH.'Conf/mysql.inc.php', $strConfig);
        //插入管理员
        //生成随机认证码
        $verify = randString(6);
        $time = time();
        $ip = get_client_ip();
        $password = md5($password . md5($verify));
        $query = "INSERT INTO `{$dbPrefix}user` VALUES('1','{$username}','{$password}','1','md5','{$ip}','{$time}','{$siteemail}','NULL','NULL','{$verify}')";
        mysql_query($query);
        $message = "<h4>成功添加管理员</h4><br/><h4>成功写入配置文件</h4>";
        $arr = array('n' => 999999, 'msg' => $message);
        echo json_encode($arr);
        exit;
    }

    //测试连接数据库
    public function condb() {
        $dbHost = $_POST['dbHost'] . ":" . $_POST['dbPort'];
        $conn = @mysql_connect($dbHost, $_POST['dbUser'], $_POST['dbPwd']);
        if ($conn) {
            exit("1");
        } else {
            exit("");
        }
    }

}
