$(function() {
    $("#signin-btn").click(function() {
        var c = $(this).find('.button-content');
        if (c.hasClass("loading")) {
            return false
        }
        var e = $("#signin-username");
        var a = $("#signin-password");
        if (!e.val()) {
            alert("请输入用户名！");
            return false
        }
        if (!a.val()) {
            alert("请输入密码！");
            return false
        }
        var b = {username: e.val(), password: a.val()};
        var url = $("#loginForm").attr('action');
        c.addClass("loading").text("登录中...");
        $("#loginForm").submit();
        return false
    });
    if ($.cookie("username")) {
        $("#signin-username").val($.cookie("username"))
    }
    if ($("#signin-username").val() == "") {
        $("#signin-username").focus()
    } else {
        $("#signin-password").focus()
    }
    $(".confirmtips").bind("click", function() {
        $("#cover").fadeOut();
        $(".showTips").fadeOut()
    });
    $(".loginforgetpsw").bind("click", function() {
        $("#cover").fadeIn();
        $(".showTips").fadeIn()
    })
});