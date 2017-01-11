$(function () {
    //ȫѡ
    $(".check-all").click(function () {
        $('.ids').prop("checked", this.checked);
    });
    $(".ids").click(function () {
        var option = $(".ids");
        option.each(function (i) {
            if (!this.checked) {
                $(".check-all").prop("checked", false);
                return false;
            } else {
                $(".check-all").prop("checked", true);
            }
        });
    });

    //radio��ѡ��ѡ��
    $(".form-checkbox-radio").delegate('.radius-all-100', 'click', function () {
        if (!$(this).hasClass('checked')) {
            $(this).addClass('checked');
            $(this).find("input[type=radio]").attr("checked", true);
            var siblings = $(this).parent('.radio').siblings('.radio').find('.radius-all-100');
            siblings.removeClass('checked');
            siblings.find("input[type=radio]").attr("checked", false);
        }
    });
    
    //tab 
    $(".ui-state-default").click(function(){
        $(this).addClass('ui-tabs-active ui-state-active'); 
        $(this).siblings('.ui-state-default').removeClass('ui-tabs-active ui-state-active');
        var tab_con_show =  $(this).children('a').attr('href');
        var tab_arr_hide = $(this).siblings('.ui-state-default');
        $(tab_arr_hide).each(function(){
            var tab_con_hide = $(this).children('a').attr('href');
            $(tab_con_hide).hide();
        });
        $(tab_con_show).show();
    });

    
});
