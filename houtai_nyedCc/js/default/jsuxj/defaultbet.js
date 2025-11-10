/*$(function() {	
    $( "body" ).on( "click", ".ba", function(k) {
        var f = LIBS.cookie("settingChecked");
        $(".arrow_box").remove();
        if (f == 1) {
            var n = (parseInt($(this).parent().css("width"), 10) - 110) / 2;
            var g = "left:" + n + "px;";
            var c = $("#bet_panel").height();
            var a = c / 2;
            var m = $("#main").height();
            if (m - c > 150) {
                c = m;
                a = c / 2 + 40
            }
            if (a > 500) {
                a = a / 2
            }
            var b = k.pageY - 100;
            var h = "top:32px;";
            var j = "arrow_box arrowUp";
            if (c - b < a && c > 150) {
                h = "bottom:32px;";
                j = "arrow_box arrowDown"
            }
            var l = LIBS.cookie("defaultSetting");
            if (!l) {
                return
            }
            var d = l.split(",");
            var o = "<div class='" + j + "' style='" + g + h + "'>";
            for (i = 0; i < d.length; i++) {
                o += "<button class='db' rel='" + d[i] + "'>下注" + d[i] + "元</button>"
            }
            o += "<button class='dbclose'>停用</button></div>";
            if (d.length > 0) {
                $(this).parent().append(o)
            }
        }
    });
    $(".ba").keyup(function() {
        closeDefaultBet()
    });
    $(".showbet input").click(function() {
        var a = "";
        $(".showbet input:checked").each(function() {
            a += $(this).attr("title");
            $(".betdetails").html(a)
        })
    });
    $(".showbetchip input").click(function() {
        var a = "";
        $(".betdetails").html(a);
        $(".showbetchip input:checked").each(function() {
            a += "<div class='b" + $(this).val() + "'>" + $(this).val() + "</div>";
            $(".betdetails").html(a)
        })
    });
    $(document).mouseup(function(b) {
        var a = $(".arrow_box");
        if (!a.is(b.target) && a.has(b.target).length === 0) {
            closeDefaultBet()
        }
    })
});*/
$(function() {
    var r = LIBS.cookie("defaultSetting"),
    u = LIBS.cookie("settingChecked"),
    n,
    t;
    if (r && u == 1) for ($(".chip-amount").empty(), n = r.split(","), i = 0; i < n.length; i++) $(".chip-amount").append('<a href="javascript:void(0)" onclick="setAmount(' + n[i] + ');">' + n[i] + "<\/a>");
    else for ($(".chip-amount").empty(), t = [5, 10, 20, 50, 100, 500], i = 0; i < t.length; i++) $(".chip-amount").append('<a href="javascript:void(0)" onclick="setAmount(' + t[i] + ');">' + t[i] + "<\/a>")
});
/*$(document).on("click", ".db",
function() {
    var b = $(this).attr("rel");
    var a = $(this).parent().parent().find(".ba");
    a.val(b);
    closeDefaultBet();
    a.focus()
});
$(document).on("click", ".dbclose",
function() {
    setTimeout(function() {
        parent.showMsg("停用后，如需启用需至快选金额设定视窗点选启用！")
    },
    500);
    var n = {path:'/'};
    LIBS.cookie("settingChecked", "0",n);
    closeDefaultBet()
});
function closeDefaultBet() {
    $(".arrow_box").remove();
    $("td").removeClass("hover");
    $("th").removeClass("hover")
};*/