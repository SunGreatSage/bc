var NS4 = (document.layers);
var IE4 = (document.all);
var ver4 = (NS4 || IE4);
var IE5 = (IE4 && navigator.appVersion.indexOf("5.") != -1);
var isMac = (navigator.appVersion.indexOf("Mac") != -1);
var isMenu = (NS4 || (IE4 && !isMac) || (IE5 && isMac));
var browerAgent = navigator.userAgent.toLowerCase();
$.browser = {};//jquery1.9以上不支持browser
$.browser.msie = /msie/.test(browerAgent);
$.browser.safari = /webkit/.test(browerAgent);
$.browser.opera = /opera/.test(browerAgent);
$.browser.mozilla = /mozilla/.test(browerAgent);
$.ajaxSetup({ cache: false });
String.prototype.format = function () {
    var g = arguments;
    return this.replace(/{(\d+)}/g,
        function (a, b) {
            return g[b]
        })
};
String.prototype.padleft = function (g, a) {
    if (this.length < g) {
        a || (a = " ");
        for (var b = "",
            c = 0,
            d = g - this.length; c < d; c++) b += a;
        return b + this
    }
    return this
};
String.prototype.replaceTemplate = function (data) {
    var template = this;
    var matchs = template.match(/\{[_a-zA-Z0-9]+\}/gi);
    var temp = template;
    for (var j = 0; j < matchs.length; j++) {
        var re_match = matchs[j].replace(/[\{\}]/gi, "");
        var value = data[re_match];
        if (value != undefined) {
            temp = temp.replace(matchs[j], value);
        }
    }
    return temp;
};

String.prototype.replaceTemplateArray = function (data) {
    var outPrint = "";
    var template = this;
    for (var i = 0; i < data.length; i++) {
        if (data[i] == undefined || data[i] == null) {
            continue;
        }
        var matchs = template.match(/\{[_a-zA-Z0-9]+\}/gi);
        var temp = template;
        for (var j = 0; j < matchs.length; j++) {
            var re_match = matchs[j].replace(/[\{\}]/gi, "");
            //if (dec && isFloat(data[i][re_match])) {
            //    temp = temp.replace(matchs[j], data[i][re_match].toFixed(dec));
            //}
            //else {
            var value = data[i][re_match];
            if (value != undefined) {
                temp = temp.replace(matchs[j], value);
            }
            //}
        }
        outPrint += temp;
    }
    return outPrint;
};
Date.prototype.Format = function (fmt) { //author: meizz   
    var o = {
        "M+": this.getMonth() + 1,                 //月份   
        "d+": this.getDate(),                    //日   
        "h+": this.getHours(),                   //小时   
        "m+": this.getMinutes(),                 //分   
        "s+": this.getSeconds(),                 //秒   
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度   
        "S": this.getMilliseconds()             //毫秒   
    };
    if (/(y+)/.test(fmt))
        fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt))
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}
//去除字符串尾部空格或指定字符  
String.prototype.trimEnd = function (c) {
    if (c == null || c == "") {
        var str = this;
        var rg = /s/;
        var i = str.length;
        while (rg.test(str.charAt(--i)));
        return str.slice(0, i + 1);
    }
    else {
        var str = this;
        var rg = new RegExp(c);
        var i = str.length;
        while (rg.test(str.charAt(--i)));
        return str.slice(0, i + 1);
    }
}

String.prototype.trimStart = function (trimStr) {
    if (!trimStr) { return this; }
    var temp = this;
    while (true) {
        if (temp.substr(0, trimStr.length) != trimStr) {
            break;
        }
        temp = temp.substr(trimStr.length);
    }
    return temp;
};
String.prototype.startWith = function (str) {
    if (str == null || str == "" || this.length == 0 || str.length > this.length)
        return false;
    if (this.substr(0, str.length) == str)
        return true;
    else
        return false;
    return true;
};
String.prototype.endWith = function (str) {
    if (str == null || str == "" || this.length == 0 || str.length > this.length)
        return false;
    if (this.substring(this.length - str.length) == str)
        return true;
    else
        return false;
    return true;
};
Array.prototype.contains = function (a) {
    if ("string" == typeof a || "number" == typeof a) {
        for (var b in this) {
            if (a == this[b]) {
                return !0;
            };
        }
    }
    return !1
};

//parse a date in yyyy-mm-dd format
function parseDate(input) {
    var parts = input.split('-');
    // new Date(year, month [, day [, hours[, minutes[, seconds[, ms]]]]])
    return new Date(parts[0] - 0, parts[1] - 1, parts[2].substr(0, 2) - 0, 0, 0, 0); // Note: months are 0-based
}
function getDaysInMonth(year, month) {
    month = parseInt(month, 10) + 1;
    var temp = new Date(year + "/" + month + "/01");
    return (new Date(temp.getTime() - 1000 * 60 * 60 * 24)).getDate();
}


function d2(d) {
    if (d < 10)
        return "0" + d;
    return d;
}

function msover() {
    var e = event.srcElement;
    while (e.tagName != "TR")
        e = e.parentElement;
    e.style.backgroundColor = "#FFFFCC";
}
function msout() {
    var e = event.srcElement;
    while (e.tagName != "TR")
        e = e.parentElement;
    e.style.backgroundColor = "";
}

function KeyNumber(event) {
    var e = event || window.event;
    var ele = e.target || e.srcElement;
    var key = e.which || e.keyCode
    if (key == 13 || key == 8 || key == 46 || key == 36 || key == 35
        || key == 37 || key == 39)
        return true;
    if ((key < 48 || key > 57) && (key > 95 || key < 106))
        return false;
}

function CheckAccountKey(event) {

    var e = event || window.event;
    var ele = e.target || e.srcElement;
    var key = e.which || e.keyCode
    if (key == 8 || key == 46 || key == 36 || key == 35 || key == 37
        || key == 39)
        return true;
    if ((key >= 97 && key <= 122) || (key >= 65 && key <= 90)
        || (key >= 48 && key <= 57))
        return true;
    return false;
}

// addy
function OnlyNumberKey(event) {
    var e = event || window.event;
    var ele = e.target || e.srcElement;
    var key = e.which || e.keyCode
    if (key == 13 || key == 8 || key == 46 || key == 36 || key == 35
        || key == 37 || key == 39)
        return true;
    if (key == 46)
        return true;
    if (key >= 48 && key <= 57)
        return true;
    return false;
}

function OnlyInt(event) {
    var e = event || window.event;
    var ele = e.target || e.srcElement;
    var key = e.which || e.keyCode;
    // var key = event.keyCode;
    if (key == 13 || key == 8 || key == 36 || key == 35
        || key == 37 || key == 39)
        return true;
    if (e.ctrlKey && key == 67 || key == 86)
        return true;
    if (key >= 48 && key <= 57)
        return true;
    return false;
}

function OnlyDecimal(event) {
    var e = event || window.event;
    var ele = e.target || e.srcElement;
    var key = e.which || e.keyCode;
    // var key = event.keyCode;
    if (key == 13 || key == 8 || key == 36 || key == 35 || key == 37 || key == 39 || key == 46 || key == 110)
        return true;
    if (e.ctrlKey && key == 67 || key == 86)
        return true;
    if (key >= 48 && key <= 57)
        return true;
    return false;
}

function OnlyNumber(obj) {
    //var v = $(obj).val()
    ////先把非数字的都替换掉，除了数字和.
    //v = v.replace(/[^\d\.]/g, '');
    ////必须保证第一个为数字而不是.
    //v = v.replace(/^\./g, '');
    ////保证只有出现一个.而没有多个.
    //v = v.replace(/\.{2,}/g, '.');
    ////保证.只出现一次，而不能出现两次以上
    //v = v.replace('.', '$#$').replace(/\./g, '').replace('$#$', '.');
    //if (v == "") {
    //    $(obj).val("");
    //} else {
    //    $(obj).val(v);
    //}
    //
    //只能输入数字和小数点
    $(obj).numeral();
}

$.fn.numeral = function () {
    //使用方式 $("#inputTXT").numeral();
    $(this).css("ime-mode", "disabled");
    this.bind("keypress", function (e) {
        var code = (e.keyCode ? e.keyCode : e.which);  //兼容火狐 IE   
        //if (!$.browser.msie && (e.keyCode == 0x8))  //火狐下 不能使用退格键  
        //{
        //    return;
        //}
        //if (e.ctrlKey && (code == 67 || code == 99 || code == 86 || code == 118))
        //    return true;
        //return code >= 48 && code <= 57 || code == 45 || code == 46 || code == 17;

        return true;

    });
    this.bind("blur", function () {

        if (this.value.lastIndexOf(".") == (this.value.length - 1)) {
            this.value = this.value.substr(0, this.value.length - 1);
        } else if (isNaN(this.value)) {
            this.value = " ";
        }
    });
    this.bind("paste", function (e) {
        //var s = clipboardData.getData('text');
        var s = (e.originalEvent || e).clipboardData.getData('text/plain')
        if (!/\D/.test(s))
            value = s.replace(/^0*/, '');

        //return false;
    });
    this.bind("dragenter", function () {
        return false;
    });
    this.bind("keyup", function () {
        this.value = this.value.replace(/[^\d.-]/g, "");
        //必须保证第一个为数字而不是.
        this.value = this.value.replace(/^\./g, "");
        //保证只有出现一个.而没有多个.
        this.value = this.value.replace(/\.{2,}/g, ".");
        //保证.只出现一次，而不能出现两次以上
        this.value = this.value.replace(".", "$#$").replace(/\./g, "").replace("$#$", ".");

        return true;
    });
};
// addy
function OnlySignInt(event) {
    var e = event || window.event;
    var ele = e.target || e.srcElement;
    var key = e.which || e.keyCode
    if (key == 13 || key == 8 || key == 36 || key == 35
        || key == 37 || key == 39)
        return true;
    if (key >= 48 && key <= 57)
        return true;
    if (key == 45)
        return true;
    return false;
}

function OnlyFloat(event) {
    var e = event || window.event;
    var ele = e.target || e.srcElement;
    var key = e.which || e.keyCode
    if (key == 13 || key == 8 || key == 46 || key == 36 || key == 35 || key == 37 || key == 39)
        return true;
    if (e.ctrlKey && key == 67 || key == 86)
        return true;
    if (key >= 48 && key <= 57)
        return true;
    return false;
}
//regex
function isNumber(value) {
    var reg = /^[0-9]+.?[0-9]*$/;
    value = value + '';
    if (value == "") {
        return false;
    }
    else if (reg.test(value)) {
        return true;
    }
    return false;
}

function GetCookie(name) {
    var arg = name + "=";
    var alen = arg.length;
    var clen = document.cookie.length;
    var i = 0;
    while (i < clen) {
        var j = i + alen;
        if (document.cookie.substring(i, j) == arg) {
            var endstr = document.cookie.indexOf(";", j);
            if (endstr == -1)
                endstr = document.cookie.length;
            return unescape(document.cookie.substring(j, endstr));
        }
        i = document.cookie.indexOf(" ", i) + 1;
        if (i == 0)
            break;
    }
    return null;
}

function SetCookie(name, value) {
    var argv = SetCookie.arguments;
    var argc = SetCookie.arguments.length;
    var expires = (argc > 2) ? argv[2] : null;
    var path = (argc > 3) ? argv[3] : null;
    var domain = (argc > 4) ? argv[4] : null;
    var secure = (argc > 5) ? argv[5] : false;
    document.cookie = name + "=" + escape(value)
        + ((expires == null) ? "" : ("; expires=" + expires.toGMTString()))
        + ((path == null) ? "" : ("; path=" + path))
        + ((domain == null) ? "" : ("; domain=" + domain))
        + ((secure == true) ? "; secure" : "");
}
// addy
function DeleteCookie(name) {
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    document.cookie = name + "=" + cval + "; expires=" + exp.toGMTString();
}

// addy
function printpage() {
    if (window.print) {
        window.print();
    } else {
        alert('No printer driver in your PC');
    }
}

function n2c(number) {
    if ((GetCookie("g_language") != "zh-tw")
        && (GetCookie("g_language") != "zh-cn"))
        return "";

    var buffer, len, time, m, n, foo, first
    buffer = "";
    if (number.length > 4) {
        len = number.length;
        time = (len % 4 != 0) ? parseInt(len / 4) + 1 : (len / 4);
        for (n = 1, m = 4; n <= time; n++) {
            if (n == time) {
                if (len % 4 > 0)
                    foo = n2c(number.substr(0, len % 4));
                else
                    foo = n2c(number.substr(len - m, 4));
            } else
                foo = n2c(number.substr(len - m, 4));
            if (foo)
                buffer = foo + sOrder[n] + buffer;
            m = m + 4;
        }
        var reg1 = new RegExp("^" + sChinese[0], "ig");
        var reg2 = new RegExp("(^[" + sChinese[0] + "]{1,})([^" + sChinese[0]
            + "]{0,})", "ig");
        if (reg1.test(buffer))
            buffer = buffer.replace(reg2, "$2");
        return buffer;
    } else {

        if (number.length == 0 || !(/[1-9]/ig).test(number))
            return "";
        first = number.substr(0, 1);

        buffer += sChinese[first];

        if (first != 0)
            buffer += sLength[number.length];

        if ((/^0/ig).test(number))
            buffer += n2c(number.replace(/(^0+)([1-9]*)/ig, "$2"));
        else
            buffer += n2c(number.substr(1));
        return buffer;
    }
}
function IsPC() {
    var userAgentInfo = navigator.userAgent;
    var Agents = ["Android", "iPhone",
        "SymbianOS", "Windows Phone",
        "iPad", "iPod"];
    var flag = true;
    for (var v = 0; v < Agents.length; v++) {
        if (userAgentInfo.indexOf(Agents[v]) > 0) {
            flag = false;
            break;
        }
    }
    return flag;
}
// addy
function colornumber(num) {
    var s = "" + num;
    if (s.length > 4) {
        s = "<font color=#FF5500>" + s.substr(0, s.length - 4) + "</font>"
            + s.substr(s.length - 4);
    }
    return s;
}

function trim(s) {
    return s.replace(/^[\s]+/g, "").replace(/[\s]+$/g, "");
}

// addy
function initarray(count, def) {
    for (var i = 0; i < count; i++)
        this[i] = def;
    return this;
}

// addy
function push(ary, value) {
    var i;
    for (i = ary.length - 1; i > 0; i--)
        ary[i] = ary[i - 1];
    ary[0] = value;
}

// addy
function popup(url, target, width, height) {
    if (!target)
        target = "_self";
    if (!width)
        width = 500;
    if (!height)
        height = 500;
    var w = window.open(url, target, "menu=0,width=" + width + ",height="
        + height + ",resizable=1,scrollbars=1");
    w.focus();
    return w;
}

/**
 * 一个用作js模板替换的代码 template格式和数组格式如下 var template = "<div>
 * <h1>{title}</h1>
 * <p>
 * {content}
 * </p>
 * </div>"; var data =
 * [{title:"a",content:"aaaa"},{title:"b",content:"bbb"},{title:"c",content:"ccc"}];
 * 只需要数据格式对应
 */
function isFloat(n) {
    return ((typeof n === 'number') && (n % 1 !== 0));
}
var dataTemplate = function (template, data, dec, fields) {
    var outPrint = "";
    for (var i = 0; i < data.length; i++) {
        if (data[i] == undefined || data[i] == null) {
            continue;
        }
        var matchs = template.match(/\{[a-zA-Z0-9]+\}/gi);
        var temp = "";
        for (var j = 0; j < matchs.length; j++) {
            if (temp == "")
                temp = template;
            var re_match = matchs[j].replace(/[\{\}]/gi, "");
            if (dec && fields && fields.indexOf(re_match) > -1 && data[i][re_match] && data[i][re_match].toFixed) {
                temp = temp.replace(matchs[j], data[i][re_match].toFixed(dec));
            }
            else {
                if (data[i][re_match] || data[i][re_match] == 0)
                    temp = temp.replace(matchs[j], data[i][re_match]);
                else {
                    temp = temp.replace(matchs[j], "");
                }
            }

        }
        outPrint += temp;
    }
    return outPrint;
}


var alertMessageCallbackFun = null;
function alertMessage(messa, callback) {
    alertMessageCallbackFun = callback;
    if ($("#dialogalert").length == 0) {
        $("body").append('<div id="dialogalert"></div>');
        $("#dialogalert").dialog({
            autoOpen: false,
            title: '系统消息框',
            model: true,
            width: 500,
            position: {
                my: "center top",
                at: "center top"
                //            collision: "none",
                //            of: window
            },
            // closeOnEscape:true,
            buttons: {
                '确定': function () {
                    $(this).dialog('close');
                    if (alertMessageCallbackFun)
                        alertMessageCallbackFun();
                },
                '取消': function () {
                    $(this).dialog('close');
                }
            },
            create: function () {
                $(this).closest(".ui-dialog").find(".ui-button") // the first
                    // button
                    .css({
                        'height': '28px'
                    });
            }
        });
    }
    $("#dialogalert").html("" + messa + "");
    $("#dialogalert").dialog('open');
}

function findByCode(code, jsonresl) {
    for (var e in jsonresl) {
        if (jsonresl[e].Code == code) {
            return jsonresl[e];
        }
    }
}
function findkeyByCode(code, jsonresl) {
    for (var e in jsonresl) {
        if (jsonresl[e].Code == code) {
            return e;
        }
    }
}

function nowdate() {
    now.setTime(now.getTime() + 1000);
    setTimeout("nowdate()", 1000);
    var hktime = $("#hktime");
    if (hktime.length > 0) {
        // hktime.empty();
        hktime.html("现在" + now.getDate() + "日 " + d2(now.getHours()) + " : "
            + d2(now.getMinutes()) + " : " + d2(now.getSeconds()) + "");
    }

}

function d2(value) {
    if ((value + '').length == 1) {
        value = "0" + value;
    }
    return value;
}


function stopCountdown() {
    if (countdownHandler > 0) {
        clearInterval(countdownHandler);
        countdownHandler = 0;
    }
}

function clearNoNum(event, obj) {
    //响应鼠标事件，允许左右方向键移动 
    event = window.event || event;
    if (event.keyCode == 37 | event.keyCode == 39) {
        return;
    }
    //先把非数字的都替换掉，除了数字和. 
    obj.value = obj.value.replace(/[^\d.]/g, "");
    //必须保证第一个为数字而不是. 
    obj.value = obj.value.replace(/^\./g, "");
    //保证只有出现一个.而没有多个. 
    obj.value = obj.value.replace(/\.{2,}/g, ".");
    //保证.只出现一次，而不能出现两次以上 
    obj.value = obj.value.replace(".", "$#$").replace(/\./g, "").replace("$#$", ".");
}
function checkNum(obj) {
    //为了去除最后一个. 
    obj.value = obj.value.replace(/\.$/g, "");
}

//获得上周的周一和周末
function getLastWeekDate1() {
    var nowTime = new Date();;
    //var week=new Array();
    var currentWeek = now.getDay();

    if (currentWeek == 0) {
        currentWeek = 7;
    }
    var monday = now.getTime() - (currentWeek + 6) * 24 * 60 * 60 * 1000; //星期一
    //var tuesday  = nowTime.getTime() - (currentWeek+5)*24*60*60*1000; //星期二
    //var wednesday = nowTime.getTime() - (currentWeek+4)*24*60*60*1000; //星期三
    //var thursday = nowTime.getTime() - (currentWeek+3)*24*60*60*1000; //星期四
    //var friday  = nowTime.getTime() - (currentWeek+2)*24*60*60*1000; //星期五
    //var saturday = nowTime.getTime() - (currentWeek+1)*24*60*60*1000; //星期六
    //var sunday  = nowTime.getTime() - (currentWeek)*24*60*60*1000;   //星期日


    //week=weektoday(monday,tuesday,wednesday,thursday,friday,saturday,sunday);
    nowTime.setTime(monday);
    return nowTime;
}

function setDifTime(date, difDate) {
    var df = now.getTime() + difDate * 24 * 60 * 60 * 1000;
    var nowTime = new Date();
    nowTime.setTime(df);
    return nowTime;
}

//获得上周的周一和周末
function getLastWeekDate7() {
    var nowTime = new Date();;
    //var week=new Array();
    var currentWeek = now.getDay();

    if (currentWeek == 0) {
        currentWeek = 7;
    }
    //var monday  = now.getTime() - (currentWeek+6)*24*60*60*1000; //星期一
    //var tuesday  = nowTime.getTime() - (currentWeek+5)*24*60*60*1000; //星期二
    //var wednesday = nowTime.getTime() - (currentWeek+4)*24*60*60*1000; //星期三
    //var thursday = nowTime.getTime() - (currentWeek+3)*24*60*60*1000; //星期四
    //var friday  = nowTime.getTime() - (currentWeek+2)*24*60*60*1000; //星期五
    //var saturday = nowTime.getTime() - (currentWeek+1)*24*60*60*1000; //星期六
    var sunday = now.getTime() - (currentWeek) * 24 * 60 * 60 * 1000;   //星期日


    //week=weektoday(monday,tuesday,wednesday,thursday,friday,saturday,sunday);
    nowTime.setTime(sunday);
    return nowTime;
}

//获得本周的周一和周末
function getThisWeekDate1() {
    var nowTime = new Date();
    //var week=new Array();
    var currentWeek = now.getDay();
    if (currentWeek == 0) {
        currentWeek = 7;
    }

    var monday = now.getTime() - (currentWeek - 1) * 24 * 60 * 60 * 1000;   //星期一
    //var tuesday  = now.getTime() - (currentWeek-2)*24*60*60*1000; //星期二
    //var wednesday = now.getTime() - (currentWeek-3)*24*60*60*1000; //星期三
    //var thursday = now.getTime() - (currentWeek-4)*24*60*60*1000; //星期四
    //var friday  = now.getTime() - (currentWeek-5)*24*60*60*1000; //星期五
    //var saturday = now.getTime() - (currentWeek-6)*24*60*60*1000; //星期六
    //var sunday = now.getTime() + (7-currentWeek)*24*60*60*1000;      //星期日

    nowTime.setTime(monday);
    return nowTime;

}

function getTwdx(tm) {
    var d = [5, 6, 7, 8, 9, 15, 16, 17, 18, 19, 25, 26, 27, 28, 29, 35, 36, 37, 38, 39, 45, 46, 47, 48];
    var x = [1, 2, 3, 4, 10, 20, 30, 40, 11, 12, 13, 14, 21, 22, 23, 24, 31, 32, 33, 34, 41, 42, 43, 44];
    tm = tm - 0;

    for (var index = 0; index < d.length; index++) {
        if (d[index] == tm) {
            return "大";
        }
    }
    for (var index = 0; index < x.length; index++) {
        if (x[index] == tm) {
            return "小";
        }
    }
    return "和";
}

function autoSize(id, offset) {
    var p = $("#" + id);
    var position = p.position();
    var top = position.top;
    var dis = offset == undefined ? 0 : offset;
    p.height($(window).height() - top - 105 - dis);
}

function html_encode(str) {
    var s = "";
    if (str.length == 0) return "";
    s = str.replace(/&/g, "&gt;");
    s = s.replace(/</g, "&lt;");
    s = s.replace(/>/g, "&gt;");
    s = s.replace(/ /g, "&nbsp;");
    s = s.replace(/\'/g, "&#39;");
    s = s.replace(/\"/g, "&quot;");
    s = s.replace(/\n/g, "<br>");
    return s;
}

function html_decode(str) {
    var s = "";
    if (str.length == 0) return "";
    s = str.replace(/&gt;/g, "&");
    s = s.replace(/&lt;/g, "<");
    s = s.replace(/&gt;/g, ">");
    s = s.replace(/&nbsp;/g, " ");
    s = s.replace(/&#39;/g, "\'");
    s = s.replace(/&quot;/g, "\"");
    s = s.replace(/<br>/g, "\n");
    return s;
}

var NumberChinese = ["一", "二", "三", "四", "五", "六", "七", "八", "九", "十", "十一", "十二", "十三", "十四", "十五", "十六", "十七", "十八", "十九", "二十", "二十一", "二十二", "二十三", "二十四", "二十五", "二十六", "二十七", "二十八", "二十九", "三十"];
function getLevelName(level, maxLevel, withAgent) {
    if (level == 1) {
        return "总公司";
    }
    else if (level == 2) {
        return "分公司";
    }
    else if (level == 3) {
        return "股东";
    }
    else if (level == 4) {
        return "总代";
    }
    else if (level > 0 && level <= maxLevel) {
        return NumberChinese[level - 5] + "级" + (withAgent ? "代理" : "");
    }
    else if (level == maxLevel + 1) {
        return "会员";
    } else if (level == 0) {
        return "外补";
    }
    else {
        return "后台管理员";
    }


    return "";
}

//函数名：CheckDate
//功能介绍：检查是否为日期
function CheckDate(str) {
    var reg = /^(\d+)-(\d{1,2})-(\d{1,2})$/;
    var r = str.match(reg);
    if (r == null) return false;
    r[2] = r[2] - 1;
    var d = new Date(r[1], r[2], r[3]);
    if (d.getFullYear() != r[1]) return false;
    if (d.getMonth() != r[2]) return false;
    if (d.getDate() != r[3]) return false;
    return true;
}

function bindingHover(parameters) {
    $("table.list tbody tr:not(.head)").hover(function () {
        $(this).addClass("hover");
    },
        function () {
            $(this).removeClass("hover");
        });
}

var __weekday = new Array(7);
__weekday[0] = "星期天";
__weekday[1] = "星期一";
__weekday[2] = "星期二";
__weekday[3] = "星期三";
__weekday[4] = "星期四";
__weekday[5] = "星期五";
__weekday[6] = "星期六";
function getWeekDay(date) {
    return __weekday[date.getDay()];
}

function bettingZcDetail(id, duplxType, topAgId,lot) {
    var a = {};
    if (duplxType && duplxType > 0) {
        a = $("#bettingFsDetail");
        0 == a.length && (a = $('<div id="bettingFsDetail">').addClass("popdiv"), a.dialog({
            autoOpen: !1,
            width: 780,
            maxHeight: 800,

            close: function (event, ui) {
            },
            modal: true,
            hide: "explode"
        }));
        a.dialog("option", {
            title: "复式明细"

        });
        a.empty();

        //var buttonPan = a.next().find(".ui-dialog-buttonset");
        //buttonPan.find("#betDetailFsPager").length == 0 &&
        // buttonPan.append('<div  style="text-align:center;margin:0 auto;display:inline" id="betDetailFsPager" class="pager"></div>');
        if (topAgId) {
            a.load(_root + "/OuterReportNew/BetDuplexDetailView?" + _urlParams + "&_" + new Date().getTime(), {
                Id: id, topAgentId: topAgId, lot: lot
            });
        }
        else {
            a.load(_root + "/ReportNew/BetDuplexDetailView?" + _urlParams + "&_" + new Date().getTime(), {
                Id: id, lot: lot
            });
        }



    } else {
        a = $("#bettingZcDetail");
        0 == a.length && (a = $('<div id="bettingZcDetail">').addClass("popdiv"), a.dialog({
            autoOpen: !1,
            width: 700,
            maxHeight: 600
        }));
        a.dialog("option", {
            title: "占成明细"
        });
        a.empty();
        if (topAgId) {
            a.append('<span class="loading">\u8f7d\u5165\u4e2d\u2026\u2026</span>').load(_root + "/OuterReportNew/BetZcView?" + _urlParams, {
                Id: id, topAgentId: topAgId, lot: lot
            });
        }
        else {
            a.append('<span class="loading">\u8f7d\u5165\u4e2d\u2026\u2026</span>').load(_root + "/ReportNew/BetZcView?" + _urlParams, {
                Id: id, lot: lot
            });
        }
      
        a.dialog("open");
    }

}
function bettingDuplexZcDetail(id, topAgId) {
    var a = $("#bettingZcDetail");
    0 == a.length && (a = $('<div id="bettingZcDetail">').addClass("popdiv"), a.dialog({
        autoOpen: !1,
        width: 700,
        maxHeight: 600
    }));
    a.dialog("option", {
        title: "占成明细"
    });
    a.empty();
    if (topAgId) {
        a.append('<span class="loading">\u8f7d\u5165\u4e2d\u2026\u2026</span>').load(_root + "/OuterReportNew/BetZcView?" + _urlParams, {
            Id: id, duplexType: 1, topAgentId: topAgId
        });
    }
    else {
        a.append('<span class="loading">\u8f7d\u5165\u4e2d\u2026\u2026</span>').load(_root + "/ReportNew/BetZcView?" + _urlParams, {
            Id: id, duplexType: 1
        });
    }
   
    a.dialog("open");


}
function back() {

    var b = LIBS.getUrlParam("back");
    b ? (-1 == b.indexOf("?") && (b += "?"), location.href = LIBS.url(b, {
        _: (new Date).getTime()
    })) : 0 == $("select#username").length ? history.back() : location.reload()
}
