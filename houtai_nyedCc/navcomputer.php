<?php
if (!defined('copyright') && copyright != 'YINHE'){
   echo "<script language='javascript'>window.location.href='http://baidu.com';</script>";
   exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1"><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="viewport" content="target-densitydpi=device-dpi, width=1370, user-scalable=yes" /><title>
	线路选择-<?php echo $msql->f('webname'); ?>导航-会员&代理
</title><link href="./guides/jquery.cover.css" rel="stylesheet" type="text/css" /><link href="./guides/master.css" rel="stylesheet" type="text/css" /><link href="./guides/layout.css" rel="stylesheet" type="text/css" /><link href="./guides/additional.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="./guides/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="./guides/jquery.cover.js"></script>
    <script type="text/javascript" src="./guides/jquery.form.min.js"></script>
    <link href="./guides/style.css" rel="stylesheet" />
   
</head>
<body>
    
    
    <div class="box5 box" id="doMain">
        <div class="partTwoTop">
            <div class="inCenter">
                <div class="indexTwobg_1">
                    <dd><span id="title"><?php echo $msql->f('webname'); ?></span>线路选择</dd>
                    <dt><a href="javascript:;" class="logout">回到首页</a></dt>
                    <dt><a href="javascript:;" onclick="window.location.reload()">重新测速</a></dt>
                </div>
            </div>
        </div>
        <div style="min-height: 500px" class="main">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="mtable">
                <tr class="tt">
                    <td width="20%" class="tc">登陆端口
                    </td>
                    <td width="60%">测速网址（点击网址进入)
                    </td>
                    <td>速度（越小表示越快）
                    </td>
                </tr>
<tr><td class="tc">会员</td><td><a  target='_blank' href="?line=1&type=<?php echo 'U3678'.base64_encode('uuuuu88888')?>">线路1</a></td><td><span class="time"><input id="status1" readonly="" value="<?php echo rand(10,50);?>ms" /></span></td></tr>
<tr><td class="tc">会员</td><td><a  target='_blank' href="?line=2&type=<?php echo 'U3233'.base64_encode('uuuuu88888')?>">线路2</a></td><td><span class="time"><input id="status1" readonly="" value="<?php echo rand(10,50);?>ms" /></span></td></tr>
<tr><td class="tc">会员</td><td><a  target='_blank' href="?line=3&type=<?php echo 'U3454'.base64_encode('uuuuu88888')?>">线路3</a></td><td><span class="time"><input id="status1" readonly="" value="<?php echo rand(10,50);?>ms" /></span></td></tr>
<tr><td class="tc">会员</td><td><a  target='_blank' href="?line=4&type=<?php echo 'U3977'.base64_encode('uuuuu88888')?>">线路4</a></td><td><span class="time"><input id="status1" readonly="" value="<?php echo rand(10,50);?>ms" /></span></td></tr>

<tr><td class="tc">代理</td><td><a  target='_blank' href="?line=1&type=<?php echo 'U3678'.base64_encode('aaaaa88888')?>">线路1</a></td><td><span class="time"><input id="status1" readonly="" value="<?php echo rand(10,50);?>ms" /></span></td></tr>
<tr><td class="tc">代理</td><td><a  target='_blank' href="?line=2&type=<?php echo 'U3233'.base64_encode('aaaaa88888')?>">线路2</a></td><td><span class="time"><input id="status1" readonly="" value="<?php echo rand(10,50);?>ms" /></span></td></tr>
<tr><td class="tc">代理</td><td><a  target='_blank' href="?line=3&type=<?php echo 'U3454'.base64_encode('aaaaa88888')?>">线路3</a></td><td><span class="time"><input id="status1" readonly="" value="<?php echo rand(10,50);?>ms" /></span></td></tr>
<tr><td class="tc">代理</td><td><a  target='_blank' href="?line=4&type=<?php echo 'U3977'.base64_encode('aaaaa88888')?>">线路4</a></td><td><span class="time"><input id="status1" readonly="" value="<?php echo rand(10,50);?>ms" /></span></td></tr>

              
            </table>
        </div>
        <script type="text/javascript">
            var timecount = 1;
            var timerstart0;
            var bTimer = false;
            var timer;
            var Ri = '0|1|2|3|4|5|6|7|8|';
            function autotime(h) {
                if (timecount > 150) {
                    var obj;
                    for (b = 0; b <= h; b++) {
                        obj = $("#status" + b);
                        if (obj && obj.val() == '测速中...') {
                            obj.val("站点的连接超时");
                        }
                    }
                    clearInterval(timer);
                }
                else {
                    timecount++;
                }
            }

            function CountTime(i, timespace) {
                if (timespace > 100) {
                    $('#status' + i).val("站点的连接超时");
                }
                else {
                    if (timespace < 1) {
                        $('#status' + i).val("反应极快");
                    }
                    else {
                        var timestr = "" + timespace / 100 * 1000 + "ms";
                        $('#status' + i).val(timestr);
                    }
                }
            }

            function testspeed(url) {
                var R_array = Ri.split("|");
                timerstart0 = timecount;
                for (var i = 0; i < R_array.length - 1; i++) {
                    if (R_array[i]) {
                        var Id = R_array[i];
                        var links = $('#L' + Id).attr("href");
                        if (links.indexOf('http') >= 0) {
                            var j = i + 1;
                            $('#status' + j).val("测速中...");
                            document.write("<img src='" + links + "' width=1 height=1 onerror='CountTime(" + j + ",timecount-timerstart0);'>");
                        }
                    }
                }
            }
            timer = setInterval("autotime(2)", 100);
            testspeed();
            function speed() {
                timer = setInterval("autotime(2)", 100);
                testspeed();
            }
            $(document).ready(function () {
                return;
                $('.logout').click(function () {
                    $.ajax({
                        type: "post",
                        dataType: "html",
                        url: "tools/admin_ajax.ashx?action=login_out&clienttt=" + Math.random(),
                        error: function (XmlHttpRequest, textStatus, errorThrown) { alert(XmlHttpRequest.responseText); },
                        success: function (data) {
                            switch (data) {
                                case '0':
                                    window.location.reload();
                                    break;
                            }
                        }
                    });
                });
            });
        </script>
        
<div style="display: none"></div>
</body>
</html>
