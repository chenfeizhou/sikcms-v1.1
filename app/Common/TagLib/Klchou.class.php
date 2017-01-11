<?php
/*
 * 快乐筹CMS标签解析库 (仅供参考)
 * @author zhijian.chen
 * @email 1114526565@qq.com
 * @date 2016/3/7
 */

namespace Common\TagLib;

use Think\Template\TagLib;
class Klchou extends TagLib {


//数据库表达式
protected  $comparisonKlchoucms = array(
     '{eq}'=>'=',
     '{neq}' => '<>',
     '{elt}' => '<=',
     '{egt}' => '>=',
     '{gt}' => '>',
     '{lt}' => '<',
   );
    //标签定义
  protected  $tags = array(
    //后台模板标签
    'admintemplate'=>array('attr'=>'file','close'=>0),
   //前台模板标签
    'template'=>array('attr'=>'file,theme','close'=>0),
   //Form标签
      'form'=>array('attr'=>'function,parameter','close'=>0),
   //导航标签
     'navigate' => array('attr'=>'cache,catid,space,blank','close'=>0),
   //评论标签
     'comment'=>array('attr'=>'action,cache,num,return,posid,catid,hot,date','level'=>3),
    //内容标签
    'content'=>array('attr'=>'action,cache,num,page,return,where,moreinfo,thumb,order,day,catid,output','level'=>3),
  );

/*
 * 加载前台模板
 * 格式 <template file="Content/footer.php" theme="主题"/>
 *@param type $attr file,theme
 * @param type $content
 * @return string|array 返回模板解析后的内容
 */
    public function _template($attr,$content){
       $config = cache('Config');
        $theme = $attr('theme')? :$config['theme'];
        $templateFile = $attr['file'];
        if(strpos($templateFile,C('TMPL_TEMPLATE_SUFFIX'))===false){
            $templateFile = $theme.'/'.$templateFile.C('TMPL_TEMPLATE_SUFFIX');
        }else{
            $templateFile = $theme.'/'.$templateFile;
        }
        //判断魔板是否存在
        if(!file_exists_case($templateFile)){
            $templateFile = str_replace($theme . '/', 'Default/', $templateFile);
            if (!file_exists_case($templateFile)) {
                return '';
            }
        }
        //读取内容
        $tmplContent = file_get_contents($templateFile);
        //解析模板
        $parseStr = $this->tpl->parse($tmplContent);
        return $parseStr;
    }


    /*
     * 加载后台模板
     * 格式 <admintemplate file="模块/控制器/模板名"/>
     *@param type $attr
     * @param type $content
     * @return string|array 返回魔板解析后的内容
     */
    public  function _admintemplte($attr,$content){
        $file = explode("/", $attr['file']);
        $counts = count($file);
        if($counts<2){
             return '';
        }else if($counts <3){
            $file_path="Admin/".C('DEFAULT_V_LAYER')."/{$attr['file']}";
        }else{
            $file_path ="$file[0]/".C('DEFAULT_V_LAYER')."/{$file['1']}/{$file[2]}";
        }
        //模板路径
        $TemplatePath = APP_PATH . $file_path . C("TMPL_TEMPLATE_SUFFIX");
        //判断模板是否存在
        if (file_exists_case($TemplatePath)) {
            //读取内容
            $tmplContent = file_get_contents($TemplatePath);
            //解析模板内容
            $parseStr = $this->tpl->parse($tmplContent);
            return $parseStr;
        }
        return '';
    }

    /*
     * 导航标签
     * 例：<navigate catid="$catid" space="$gt;"/>
     * 参数使用说明:
     *    @catid  栏目id,可参入数字，也可以传递变量 $catid
     *    @space 分隔符,支持html代码
     *    @blank  是否新窗口打开
     *    @cache   缓存时间
     * @param $content 表情内容
     * @return array|string
     */
    public function _navigate($tag,$content){
       $key = to_guid_string(array($tag,$content));
       $cache = (int) $tag['cache'];
       if($cache){
           $data = S($key);
           if($data){
               return $data;
           }
       }
        //分隔符，支持html代码
        $space  = !empty($tag['space']) ? $tag['space'] : '&gt;';
        $target = !empty($tag['blank'])?'target="_blank"':'';
        $catid  = $tag['catid'];
        $parsestr = '';
        if(is_numeric($catid)){
             $catid = (int) $catid;
            //分类可设置缓存---------暂无设置 getCategory函数获取分类

            //获取当前栏目的 父栏目列表
            $arrparentid = array_filter(explode(',', getCategory($catid, 'arrparentid') . ',' . $catid));
            foreach ($arrparentid as $cid) {
                $parsestr[] = '<a href="' . getCategory($cid, 'url') . '" ' . $target . '>' . getCategory($cid, 'catname') . '</a>';
            }
            $parsestr = implode($space, $parsestr);
        }else{
            $parsestr = '';
            $parsestr .= '<?php';
            $parsestr .='$arrparentid = array_filter(explode(\',\',getCategory('.$catid.',"arrparentid").\',\'.'.$catid.'));';
            $parsestr .= ' foreach ($arrparentid as $cid){';
            $parsestr .= '    $parsestr[] = \'<a href="\' . getCategory($cid,\'url\')  . \'" ' . $target . '>\' . getCategory($cid,\'catname\') . \'</a>\';';
            $parsestr .= ' }';
            $parsestr .= '?>';
        }
        if ($cache) {
            S($key, $parsestr, $cache);
        }
        return $parsestr;
    }

}