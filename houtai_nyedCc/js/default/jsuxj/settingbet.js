$(function () {
    $("#settingbet").dialog({
        autoOpen: false,
        modal: true,
        width: 270,
        title: "快选金额",
        show: {
            effect: "fade",
            duration: 1000
        },
        hide: {
            effect: "fade",
            duration: 1000
        }
    })
});

function showsetting() {
    var c = LIBS.cookie("defaultSetting");
    if (c) {
        var a = c.split(",");
        for (i = 0; i < a.length; i++) {
            $(".ds").eq(i).val(a[i])
        }
    }
    var b = LIBS.cookie("settingChecked");
    if (!b) {
        b = 1
    }
    $("input[name='settingbet'][value=" + b + "]").prop("checked", true);
    $("#settingbet").dialog("open")
}

function submitsetting() {
    var b = new Array();
    for (i = 0; i < 8; i++) {
        var a = $(".ds").eq(i).val();
        if (a) {
            b.push(a)
        }
    }
    var c = $("input[name=settingbet]:checked").val();
    if (c == 0) {
        setTimeout(function () {
            parent.showMsg("停用后，如需启用需至快选金额设定视窗点选启用！")
        }, 500)
    }
    var n = {path:'/'};
    LIBS.cookie("defaultSetting", b,n);
    LIBS.cookie("settingChecked", c,n);
    $("#settingbet").dialog("close")
	loadChipAmount()
};

function showMsg(b, c) {
    var a = $("#messageBox");
    if (a.length == 0) {
        a = $('<div id="messageBox">').appendTo("body").dialog({
            autoOpen: false,
            resizable: false,
            modal: true,
            icon: true,
            minHeight: 0,
            width: 400,
            title: "用户提示",
        }).on("dialogclose",
        function(f) {
            var d = $(this).data("cb");
            if ($.isFunction(d)) {
                d($(this).data("ok"))
            }
        })
    }
    a.text(b).dialog("open").data({
        ok: false,
        cb: c
    });
    if (c) {
        a.dialog("widget").find(".ui-dialog-buttonset button:eq(1)").show()
    } else {
        a.dialog("widget").find(".ui-dialog-buttonset button:eq(1)").hide()
    }
}
function loadChipAmount() {
	var child = $("#frame").contents();
    var r = LIBS.cookie("defaultSetting"),
    u = LIBS.cookie("settingChecked"),
    n,
    t;
    if (r && u == 1) for (child.find(".chip-amount").empty(), n = r.split(","), i = 0; i < n.length; i++) child.find(".chip-amount").append('<a href="javascript:void(0)" onclick="setAmount(' + n[i] + ');">' + n[i] + "<\/a>");
    else for (child.find(".chip-amount").empty(), t = [5, 10, 20, 50, 100, 500], i = 0; i < t.length; i++) child.find(".chip-amount").append('<a href="javascript:void(0)" onclick="setAmount(' + t[i] + ');">' + t[i] + "<\/a>")

//find("a span").html()
//console.log(child.find(".chip-amount").html());
}