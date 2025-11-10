/* 
 *@名称: function.js
 *@功能: 函数方法
 *@作者: Kish
 *@版本: v2.0
 *@时间: 2013-12-14
 */
//后台首页中间自适应高度
function midAutoH() {
    var middleContent = document.getElementById("middle-content"), content = document.getElementById("content");
    middleContent.style.height = (document.body.clientHeight - 130) + "px";  // 获取中间高度
    content.style.height = (document.body.clientHeight - 130) + "px";

    //var mid = document.getElementById("mid"), thing = document.getElementById("welcome");
    //welcome.style.height = (document.body.clientHeight - 130) + "px";
}

//********
$(document).ready(function () {
    navImgShowHide();
    $('.toggle').find('span').eq(0).unbind("click").click(function () {
        //alert('show first');
        //判断display属性
        var ishide = $('.sider-left').css('display');
        if (ishide == "block") {
            //当显示的时候，执行收缩功能
            $(".toggle").css({ "border-color": "#3d96c9" });
            $(".sider-toggle").addClass("sider-toggle-focus");
            $(".main-con").css({ "margin-left": "-15px" });
            $(".main-con-in").css({ "margin-left": "15px" });
            $(".toggle").find("span").addClass("fold").attr("title", "展开");
            $(".sider-left").hide(100);
        } else {
            $(".toggle").css({ "border-color": "#fff" });
            $(".sider-toggle").removeClass("sider-toggle-focus");
            $(".main-con").css({ "margin-left": "-211px" });
            $(".main-con-in").css({ "margin-left": "210px" });
            $(".toggle").find("span").removeClass("fold").attr("title", "收缩");
            $(".sider-left").show(100);
        }
    });
    $(window).resize(function () {
        navImgShowHide();
    });
});

function navImgShowHide() {
    var _width = $(window).width();
    var _imgWidth = 0;
    _imgWidth = $(".top-menu img").size() * 185;
    if (_imgWidth < _width)
        $(".top-nav").hide();
    else {
        $(".top-nav").show();
    }
}

