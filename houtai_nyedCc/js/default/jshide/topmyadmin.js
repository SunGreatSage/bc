var settime0;
var settime1;
var time0 = 0;
var time1 = 0;
var gntime;
var gid;
var upl;
var lottery = "",
    lotterys, controlMenu = null,
    resetTimer;
var cxflytime;
var cztxtime;
//后台首页中间自适应高度
function midAutoH() {
    var middleContent = document.getElementById("middle-content"), content = document.getElementById("content");
    middleContent.style.height = (document.body.clientHeight - 130) + "px";  // 获取中间高度
    content.style.height = (document.body.clientHeight - 130) + "px";
}
/*自适应高度*/
window.onload = midAutoH;
window.onresize = midAutoH;
function myready() {
	  $('[id^="dialogNotice_"]').dialog({
		autoOpen: !0,
        modal: !0,
        width: 450,
        height: 300,
        title: "最新公告",
        buttons: {
                                "确定": function() {
                                    $(this).dialog("close")
                                }
                            }
    })
    if(top.location.href.indexOf("top.php")==-1 && ismobi()){
        //top.location.href="top.php?xtype=this";
        //return;
    }
    $(".menus").html($(".games a.xz").attr('gname'));
    var posi = $(".menus").position();
    $(".menus").mouseover(function () {
        $(".games").show();
    });

    $(".games").mouseleave(function () {
        $(this).hide();
    });

   $(".games a").click(function () {
        $(".games a").removeClass('xz');
        $(this).addClass('xz');
        $(".menus").html($(".games a.xz").attr('gname'));
        changegid($(this).attr('gid'));
        ngid = Number($(this).attr('gid'));
        fenlei = Number($(this).attr('fenlei'));
    });
	
	$("#lotterys a").click(function () {
        ngid = Number($(this).attr('gid'));
        fenlei = Number($(this).attr('fenlei'));
        $("#lotterys a").removeClass("selected");
        $(this).addClass("selected");
        changegid(ngid);
		h = $(this).text();
		$("#lotteryInfo").text(h);
		$("#lotterys").hide();
    });

  $("#lotterys a.g" + ngid).addClass("selected");
   $("#lotteryInfo").text($("#lotterys .selected").html());
    if (/MSIE (6|7)/.test(navigator.userAgent)) {
        var b = function () {
            $("#frame").height($("#contents").height())
        };
        b();
        $("#contents").resize(b)
    }
	
	$(".dropdown-menu-sub-indicator").hover(function() {
        $(".dropdown-menu-sub-indicator").addClass("dropdown-menu-hover");
		$(".dropdown-menu-shadow").show();
		$(".dropdown-menu-shadow").css("visibility","visible");
        clearTimeout(i)
    }, function() {
        i = setTimeout(function() {
            $(".dropdown-menu-sub-indicator").removeClass("dropdown-menu-hover");
			$(".dropdown-menu-shadow").hide();
			$(".dropdown-menu-shadow").css("visibility","hidden");
        }, 100)
    });
	 $("#lotterys").hide();
	 i = null;
    $("#lotterys,#lotteryInfo").hover(function() {
        $("#lotterys").show();
        clearTimeout(i)
    }, function() {
        i = setTimeout(function() {
            $("#lotterys").hide()
        }, 100)
    });
    $(".submenudiv").hide();
    $(".header .top_menu  a").click(function () {
        $(this).parent().find('a').removeClass("c_curr");
        var a = $(this);
        a = a.html();
       if("现金管理" == a) return false;
        if ("系统设置" == a || "后台管理" == a || "现金管理" == a || "用户管理" == a || "报表查询" == a || "开奖结果" == a || "即时注单" == a || "规则说明" == a || "注单预警" == a|| "个人管理" == a || "赔率设置" == a || "在线统计" == a) {
            var i = Number($(this).attr('i'));
			 $(".submenudiv").hide();
            $(".submenudiv:eq(" + i + ")").show();
		    $(".submenudiv:eq(" + i + ")").find("a").removeClass('c_curr');
		 if ("用户管理" == a) {$("#drownMenu ").children().not(":first").remove();$(".accessible").html("选择用户级别<span class='dropdown-menu-sub-indicator'>»</span>")}
		 if ("即时注单" == a) {} else {$(".submenudiv:eq(" + i + ")").find("a:eq(0)").addClass('c_curr');}

        }
        if ("退出系统" == a) {
            window.location.href = "/Home/Logout";
        }

    });

    $(".header .nav a").click(function () {
        return;
        $(this).parent().find('a').removeClass("selected");
        var a = $(this);
        a.addClass("selected");
        changegid(a.attr('gid'));
    });
$("  .submenudiv li a").click(function () {
      if(!$(this).parent().parent().hasClass("dropdown-menu")){
		$(".submenudiv").find("a").removeClass('c_curr');
        $(this).addClass("c_curr");
        var u = $(this).attr('u');
        var type = $(this).attr('type');
        if($(this).hasClass("usermenu")){
            //alert(frame.window.location.href)
            if(frame.window.location.href.indexOf("editson")!=-1){
                $(this).parent().children().removeClass("c_curr");
                $(this).parent().find("a:eq(0)").addClass("c_curr");
                frame.window.location.href = mulu + "suser.php?xtype=show";
            }
            return false;
        }
        frame.window.location.href = mulu+u + ".php?xtype=" + type;
		  } else {
			  if($(this).hasClass("one")){frame.window.location.href = mulu + "suser.php?xtype=show";}
			 $(this).parent().nextAll().remove();
			  return false;
			  }
    });

    $(".top_menu  a").click(function () {
		 $(this).parent().find('a').removeClass("hover");
        $(this).addClass("hover");
        var url = $(this).attr('x');
        if (url == 'suser') {
            frame.window.location.href = mulu+url + ".php?xtype=show&layer=" + $(this).attr('u')
        } else if (url == 'top') {
            frame.window.location.href = mulu+url + ".php?logout=yes"
        } else if (url == 'libset') {
            frame.window.location.href = mulu+url + ".php?xtype=warn"
        } else if (url == 'zshui') {
            frame.window.location.href = mulu+url + ".php?xtype=ptype"
        }else if (url == 'admins') {
            frame.window.location.href = mulu+url + ".php?xtype=list"
        } else if (url == 'class') {
            frame.window.location.href = mulu+url + ".php?xtype=bigclass"
        } else if (url == 'setatt') {
            frame.bottom.window.location.href = mulu+"zshui.php?xtype=setattshow"
        } else if (url == 'money') {
            frame.window.location.href = mulu+url + ".php?xtype=chongzhi"
        } else if (url == 'logout'){
        } else {
            frame.window.location.href = mulu+url + ".php?xtype=show"
        }

        $(".panstatus").show();
        $(".otherstatus").hide();
        return false
    });

    $("label").css("color", "white");

    kj();
    upl = setTimeout(updatel, 3000);
    $(".qzclose").click(function () {
        $.ajax({
            type: 'POST',
            url: mulu + 'top.php',
            data: 'xtype=qzclose',
            success: function (m) {
                if (Number(m) == 1) {
                    window.location.href = window.location.href
                }
            }
        })
    });

    getnews();
    $(".more").click(function () {
        frame.window.location.href = mulu+"new.php";
    })
    $("a#notices").click(function () {
        frame.window.location.href = mulu+"new.php";
    });
    $(".online").click(function () {
        frame.window.location.href = mulu+"online.php?xtype=show";
    });
    cztxtime = setTimeout(cztx, 5000);
    cxflytime = setTimeout(cxfly, 3000);
    $(".clqq").click(function () {
        $("a.xjgl").click();
        $("#dialog").dialog("close");
    });

}

function changegid(gid) {
    $.ajax({
        type: 'POST',
        url: mulu + 'top.php',
        data: 'xtype=setgame&gid=' + gid,
        success: function (m) {
            if (Number(m) == 1) {

                frame.window.location.href = frame.window.location.href;
            }
        }
    })
}

function cztx() {
    clearTimeout(cztxtime);
    if (frame.window.location.href.indexOf('money.php') == -1) {
        $.ajax({
            type: 'POST',
            url: mulu + 'money.php',
            data: 'xtype=getcztx',
            success: function (m) {
                if (Number(m) == 1) {
                    $("#dialog").dialog();
                    playVoice('/js/sound/cash.wav', 'cash-voice');
                }
            }
        });
    }
    cztxtime = setTimeout(cztx, 10000);
}

function cxfly() {
   clearTimeout(cxflytime);
        $.ajax({
            type: 'POST',
            url: mulu + 'fly.php',
            data: 'xtype=cxfly',
            cache:false,
            success: function (m) {
                if (Number(m) == 1) {
                    playVoice('/js/sound/unonline.wav', 'cash-voice1');
                }else if (Number(m) == 2) {

                    playVoice('/js/sound/moneyerr.wav', 'cash-voice2');
                }
            }
        });

    cxflytime = setTimeout(cxfly, 10000);
}


function playVoice(src, domId) {
    var $dom = $('#' + domId)
    if ($.browser.msie) {
        // IE用bgsound标签处理声音

        if ($dom.length) {
            $dom[0].src = src;
        } else {
            $('<bgsound>', {src: src, id: domId}).appendTo('body');
        }
    } else {
        // IE以外的其它浏览器用HTML5处理声音
        if ($dom.length) {
            $dom[0].play();
        } else {
            $('<audio>', {src: src, id: domId}).appendTo('body')[0].play();
        }
    }
}

function kj() {
    if (ngid == 161 | ngid == 162) {
        $(".upkj").html($(".upqishu").attr('m'));
        return false
    }
    var upkj = $(".upqishu").attr('m').split(',');
    var ul = upkj.length;
    var str = '';
    for (i = 0; i < ul; i++) {
        if (fenlei == 100 & i == 6) str += "<span>T</span>";
        str += qiu(upkj[i]);
    }
    $(".upkj").html(str);
    $(".upkj").attr("class", "T" + fenlei);
    $(".T" + fenlei).addClass("upkj");

}

function updatel() {
    clearTimeout(upl);
    var mm = $(".upqishu").attr("m").split(',');
    $.ajax({
        type: 'POST',
        url: mulu + 'top.php',
        dataType: 'json',
        cache: false,
        data: "xtype=upl&qishu=" + $(".upqishu").html() + "&m1=" + mm[0],
        success: function (m) {
            if (m[0] != 'A') {
                $(".upqishu").html(m[0]);
                $(".upqishu").attr("m", m[1]);
                kj();
                m[1] = m[1].split(',');
                /*if (m[1][0] != '') {
                    bofang("kaijiang");
                }*/
            }
            var cobj = $("#frame").contents().find("#bresult");
            cobj.html(m[2]);
            $(".online").html(m[3]);
        }
    });
    upl = setTimeout(updatel, 3000);
}

function getResult(num, n) {
    return Math.round(num * Math.pow(10, n)) / Math.pow(10, n)
}

function getnews() {
    $.ajax({
        type: 'POST',
        url: mulu + 'top.php',
        dataType: 'json',
        data: 'xtype=getnews',
        cache: false,
        success: function (m) {
            var mlength = m.length;
            if (mlength == 0) return false;
            var str = '';
            for (i = 0; i < mlength; i++) {
                str += '' + m[i]['content'] + '<label>[' + m[i]['time'] + ']</label> '
            }
            $("#notices").html(str);
            setTimeout(getnews, 30000);
            if (m[0]['mc'] != '' & m[0]['mc'] != undefined & m[0]['mc'] != 'undefined') {
                alert(m[0]['mc']);
            }
            m = null;
            str = null
        }
    })
}

function qiu(n, bname) {
    if (n == '') return '';
    if (fenlei == 107) n = Number(n);
    return "<span><b class='b" + n + "'>" + n + "</b></span>";
}

function in_array(v, a) {
    for (key in a) {
        if (a[key] == v) return true
    }
    return false
}

function ismobi(){
      var sUserAgent = navigator.userAgent.toLowerCase();
      var bIsIpad = sUserAgent.match(/ipad/i) == 'ipad';
      var bIsIphone = sUserAgent.match(/iphone os/i) == 'iphone os';
      var bIsMidp = sUserAgent.match(/midp/i) == 'midp';
      var bIsUc7 = sUserAgent.match(/rv:1.2.3.4/i) == 'rv:1.2.3.4';
      var bIsUc = sUserAgent.match(/ucweb/i) == 'web';
      var bIsCE = sUserAgent.match(/windows ce/i) == 'windows ce';
      var bIsWM = sUserAgent.match(/windows mobile/i) == 'windows mobile';
      var bIsAndroid = sUserAgent.match(/android/i) == 'android';
 
      if(bIsIpad || bIsIphone || bIsMidp || bIsUc7 || bIsUc || bIsCE || bIsWM || bIsAndroid ){
          return true;
      }
      return false;
}