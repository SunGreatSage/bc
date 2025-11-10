$(window).bind('resize', function (e) {
    var h = $(window).height();
    var pageheight = h - 160;
    $(".page_content_body").css("height", pageheight + "px");
    $(".page_content_body").mCustomScrollbar("update");
});

var h = $(window).height();
var pageheight = h - 160;
$(".page_content_body").css("height", pageheight + "px");


$(".itm ul").hover(
  function () {
      $(this).find(".linet").css("display", "block");
      $(this).find("li").css("display", "block");
  },
  function () {
      $(this).find(".linet").css("display", "none");
      $(this).find("li").css("display", "none");

  }
);

$(document).ready(function () {

    //Table字母搜索
    $('.content_header2 div').click(function () {
        var currentABC = $(this).text();
        var objTable = $(this).parents('.page_content').find('.row');
        objTable.find('.itmtxt a').each(function () {
            if (currentABC != "全部显示") {
                if ($(this).attr('Tag').toUpperCase() != currentABC) {
                    $(this).parents('.itm').find('ul').css("display", "none");
                } else {
                    $(this).parents('.itm').find('ul').css("display", "block");
                }
            } else {
                $(this).parents('.itm').find('ul').css("display", "block");
            }
        });
    });

    $('.content_header3 i').click(function () {
        var textValue = $(this).parents('.content_header3').find('.searchbox').val();
        if (textValue == "") {
            alert('请输入您要搜索的内容');
            return false;
        }
        var objTable = $(this).parents('.page_content').find('.row');
        objTable.find('.itmtxt a').each(function () {
            if ($(this).text().indexOf(textValue) >= 0) {
                $(this).parents('.itm').find('ul').css("display", "block");
            } else {
                $(this).parents('.itm').find('ul').css("display", "none");
            }
        });
    });

    $('.bu_reg').click(function () {
        var username = $('#userName').val();
        var userpwd = $('#userPwd').val();
        var userpwd1 = $('#userPwd1').val();
        var usermail = $('#userMail').val();
        if (username == "") {
            alert('用户名不能为空！'); return false;
        } else if (username.length > 20 || username.length < 3) {
            alert('用户名长度为3-20个字符！'); return false;
        }
        if (userpwd == "") {
            alert('用户密码不能为空！'); return false;
        }
        if (userpwd.length < 6) {
            alert('用户密码6位数以上！'); return false;
        }
        if (userpwd != userpwd1) {
            alert('两次密码不一致，请确认！'); return false;
        }
        var reg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
        if (usermail == "") {
            alert('用户邮箱不能为空！'); return false;
        }
        if (!reg.test(usermail)) {
            alert('邮箱格式不对！'); return false;
        }
        $('.reg_msg').text('正在提交数据，请稍候...');
        $.ajax({
            type: "post",
            dataType: "json",
            url: "tools/admin_ajax.ashx?action=user_register&clienttt=" + Math.random(),
            data: "userName=" + encodeURIComponent(username) + "&userPwd=" + encodeURIComponent(userpwd) + "&email=" + encodeURIComponent(usermail) + "&time=" + (new Date().getTime()),
            error: function (XmlHttpRequest, textStatus, errorThrown) { alert(XmlHttpRequest.responseText); },
            success: function (data) {
                switch (data.status) {
                    case 0:
                        alert(data.msg);
                        break;
                    case 1:
                        $('.reg_msg').text('注册成功');
                        alert('注册成功！');
                        top.location.href = data.url;
                        break;
                }
            }
        });
    });

    $('.bu_login').click(function () {
        var username1 = $('#txtUserName').val();
        var userpwd1 = $('#txtUserPwd').val();
        if (username1 == "") {
            alert('用户名不能为空！'); return false;
        }
        if (userpwd1 == "") {
            alert('用户密码不能为空！'); return false;
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "tools/admin_ajax.ashx?action=user_login&clienttt=" + Math.random(),
            data: "userName=" + encodeURIComponent(username1) + "&userPwd=" + encodeURIComponent(userpwd1) + "&time=" + (new Date().getTime()),
            error: function (XmlHttpRequest, textStatus, errorThrown) { alert(XmlHttpRequest.responseText); },
            success: function (data) {
                switch (data.status) {
                    case 0:
                        alert(data.msg);
                        break;
                    case 1:
                        top.location.href = data.url;
                        break;
                }
            }
        });
    });

/*    $('.searchTop_btn').click(function () {
        var searchValue = $('.searchTop_txt').val();
        if (searchValue == "") {
            alert('请输入您要搜索的内容');
            return false;
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "tools/admin_ajax.ashx?action=login_search&clienttt=" + Math.random(),
            data: "searchValue=" + encodeURIComponent(searchValue) + "&time=" + (new Date().getTime()),
            error: function (XmlHttpRequest, textStatus, errorThrown) { alert(XmlHttpRequest.responseText); },
            success: function (data) {
                switch (data.status) {
                    case 0:
                        window.location.reload();
                        break;
                    case 1:
                        window.location.href = data.url;
                        break;
                }
            }
        });
    });*/

    $(".searchTop_txt").keyup(function () {
        if (event.keyCode == 13) {
            $('.searchTop_btn').click();
        }
    })

    //添加第一屏的广告
    var adHtml1 = "";
    adHtml1 += '<a href="javascript:;"><img src="images/advert1.png" /></a>';
    adHtml1 += '<a href="javascript:;"><img src="images/advert2.png" /></a>';
    adHtml1 += '<a href="javascript:;"><img src="images/advert3.png" /></a>';
    adHtml1 += '<a href="javascript:;"><img src="images/advert4.png" /></a>';
    adHtml1 += '<a href="javascript:;"><img src="images/advert5.png" /></a>';
    //$('#ad1').html(adHtml1);
});

(function ($) {
    $(document).ready(function () {
        $(".myBackgroundImage").cover({
            backgroundPosition: "bottom",
            checkWindowResize: true,
            loadHidden: true
        });

        $(".myBackgroundImage2").cover({
            backgroundPosition: "center",
            checkWindowResize: true,
            loadHidden: true
        });
    });

    $(window).load(function () {
        $(".page_content_body").mCustomScrollbar({
            axis: "y",
            theme: "3d-light"
        });
    });


})(jQuery);