思科cms后台内容管理系统结构说明

1.使用thinkphp3.2.3版本作为PHP框架

2.目录结构

/index.php              前端入口文件

/admin.php              后端入口文件

/api.php                api入口文件

/install.php            安装入口文件(需安装时使用)


/app				     项目主目录

/app/Application         主应用程序目录

/app/Application/Admin   后台主应用程序

/app/Application/Api     api主应用程序

/app/Application/Home    前台主应用程序

/app/Application/Install 安装向导主应用程序

/app/Core3.2.3	        核心框架主目录

/Common                 公共项目

/Common/conf	        项目主配置访目录，与其它子项目共享使用

/statics				sikcms静态资源目录

/statics/admin			sikcms后台静态资源目录

/statics/home		    sikcms前台静态资源目录

/statics/install		sikcms安装向导静态资源目录

/Public                 公共资源调用目录

/upload_dir             后台文件上传目录

/#runtime               应用缓存文件

3.安装指导

本地部署

http://localhost/sikcms/install.php 进入安装


若有疑问加QQ群：498476759

邮箱：1114526565@qq.com




