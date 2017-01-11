/* 
    页面无刷新上传空间
    参数说明:
    opt.frameName : iframe的name值;
    opt.url : 文件要提交到的地址;
    opt.fileName : file控件的name;
    opt.format : 文件格式，以数组的形式传递，如['jpg','png','gif','bmp'];
    opt.callBack : 上传成功后回调;
    */
function ajaxUpload(opt){
      var iName=opt.frameName;  
      var iframe,form,file,fileParent;
      //创建iframe和form表单
       iframe = $('<iframe name="'+iName+'" />');
       form = $('<form method="post" style="display:none;" target="'+iName+'" action="'+opt.url+'"  name="form_'+iName+'" enctype="multipart/form-data" />'); 
       file = $('#'+opt.id); //通过id获取flie控件
       fileParent = file.parent(); //存父级
       file.appendTo(form);
      //插入body
       $(document.body).append(iframe).append(form);
       //取得文件扩展名
       var fileExts=/\.[a-zA-Z]+$/.exec(file.val())[0].substring(1);
       if(opt.format.join('-').indexOf(fileExts)!=-1){
           form.submit();//格式通过验证后提交表单;
        }else{
            file.appendTo(fileParent); //将file控件放回到页面
            iframe.remove();
            form.remove();
            alert('文件格式错误，请重新选择！');
        }
         //文件提交完后
        iframe.load(function(){
            var data = $(this).contents().find('body').html();   
            file.appendTo(fileParent);
            iframe.remove();
            form.remove();
            opt.callBack(data);
        })
}

