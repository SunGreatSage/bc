function myready() {
	$(".tab_panel span.g"+gid).addClass("group_tap_focus");
	$(".tab_panel span").click(function(){		
        window.location.href = mulu + "data.php?xtype=show&gid="+$(this).attr('gid');
	});
    $.ajax({
    url: 'data.php',
    type: 'POST',
    data: { xtype: 'data', gid: gid },
    dataType: 'json',
    success: function(data) {
        // 将查询结果插入到表格中
        if (data.length > 0) {
            $.each(data, function(index, row) {
                $('#data tbody').append('<tr><td>' + row.username + '</td><td>' + row.name + '</td><td>' + row.kmoney + '</td><td style="color: red;">(' + row.bet_count + ')</td><td><a href="javascript:void(0);" userid=' + row.userid + ' username=' + row.username + '  class="detailed">查看明细</a></td></tr>');
            });
        } else {
            $('#data tbody').append('<tr><td height="28" colspan="5" align="center"><marquee align="middle" behavior="alternate" style="width: 100%;"><font class="font_r">查无任何资料</font></marquee></td></tr>');
        }
    },
    error: function() {
        alertMessage('数据加载失败，请重试。');
    }
});

$(".send").on("click", function () {
    var datatime = $(".datatime").val();
    var dataje = $(".dataje").val();
    var number = $(".number").val();

    $.ajax({
        url: 'data.php',
        type: 'POST',
        data: { xtype: 'update', gid: gid, datatime: datatime, dataje: dataje, number: number },
        success: function (response) {
            if (response == "1") {
                alertMessage("保存成功！");
            } else {
                alertMessage("保存失败！");
            }
        },
        error: function () {
            alertMessage("请求失败，请重试");
        }
    });
});

$("#data").on("click", ".detailed", function () {
	$(this).closest('tr').css('background-color', 'rgb(255, 255, 162)').siblings().css('background-color', '');
    var userid = $(this).attr('userid');
	var username = $(this).attr('username');
	$('#current').html('当前会员账号：' + username);
    $.ajax({
        url: 'data.php',
        type: 'POST',
        data: { xtype: 'details', gid: gid, userid: userid },
        dataType: 'json',
        success: function(data) {
            // 清空表格内容
            $('#zd tbody').empty();

            // 将查询结果插入到表格中
            if (data.length > 0) {
                $.each(data, function(index, row) {
					    var textDecoration = row.z == 7 ? 'line-through' : 'none';
						var buttonValue = row.z == 7 ? '恢复' : '注销';
                        var buttonClass = row.z == 7 ? 'btn7' : 'btn4';
                        $('#zd tbody').append('<tr style="text-decoration: ' + textDecoration + ';"><td><input type="checkbox" class="selectRow" /></td><td>' + row.gname + '</td><td>' + row.qishu + '期</td><td style="color:#2836f4" >' + row.cid + '-[' + row.pid + ']</td><td>' + row.je + '</td><td style="color:red;">' + row.peilv1 + '</td><td>' + row.points + '</td><td>' + row.time + '</td><td >' + row.zc + '</td><td><input name="button" type="button" style="width: 50px;"  tid=' + row.tid + ' qishu=' + row.qishu + ' userid=' + row.userid + '  z=' + row.z + ' class="out ' + buttonClass + '"  value="' + buttonValue + '"></td></tr>');
                });
            } else {
                $('#zd tbody').append('<tr><td colspan="6">无结果</td></tr>');
            }
        },
        error: function() {
            alertMessage('数据加载失败，请重试。');
        }
    });
});
$("#zd").on("click", ".out", function() {
    var row = $(this).closest('tr');
    var tid = $(this).attr('tid');
    var qishu = $(this).attr('qishu');
    var userid = $(this).attr('userid');
    var z = $(this).attr('z');
    var button = $(this); // 保存 this 的引用到 button 变量

    $.ajax({
        url: 'data.php',
        type: 'POST',
        data: { xtype: 'cancel', tid: tid, gid: gid, qishu: qishu, userid: userid, z: (z == 7 ? 9 : 7)  },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                if (z == 7) {
                    row.css('text-decoration', 'none'); // 移除注单行删除线
                    button.val('注销'); // 使用 button 变量
                    button.removeClass('btn7').addClass('btn4'); // 使用 button 变量
                    button.attr('z', 9); // 使用 button 变量
                    alertMessage('恢复成功');
                } else {
                    row.css('text-decoration', 'line-through'); // 给注单行添加删除线
                    button.val('恢复'); // 使用 button 变量
                    button.removeClass('btn4').addClass('btn7'); // 使用 button 变量
                    button.attr('z', 7); // 使用 button 变量
                    alertMessage('注销成功');
                }
            } else {
                alertMessage('操作失败，请重试。');
            }
        },
        error: function() {
            alertMessage('数据加载失败，请重试。');
        }
    });
});

$("#bulkCancel, #bulkRestore").on("click", function () {
    var selectedRows = $(".selectRow:checked").closest("tr");

    if (selectedRows.length === 0) {
        alertMessage("请选择要操作的行。");
        return;
    }

    var rowsData = [];

    selectedRows.each(function () {
        var row = $(this);
        var outButton = row.find(".out");
        var tid = outButton.attr("tid");
        var qishu = outButton.attr("qishu");
        var userid = outButton.attr("userid");
        var z = outButton.attr("z");
        var newZ = z == 7 ? 9 : 7;

        rowsData.push({ tid: tid, gid: gid, qishu: qishu, userid: userid, z: newZ });
    });

    $.ajax({
        url: "data.php",
        type: "POST",
        data: { xtype: "toggle", rowsData: rowsData },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                selectedRows.each(function () {
                    var row = $(this);
                    var outButton = row.find(".out");
                    var z = outButton.attr("z");

                    if (z == 7) {
                        row.css("text-decoration", "none");
                        outButton.val("注销");
                        outButton.removeClass("btn7").addClass("btn4");
                        outButton.attr("z", 9);
						 alertMessage('批量恢复成功');
                    } else {
                        row.css("text-decoration", "line-through");
                        outButton.val("恢复");
                        outButton.removeClass("btn4").addClass("btn7");
                        outButton.attr("z", 7);
						 alertMessage('批量注销成功');
                    }
                });
            } else {
                alertMessage("操作失败，请重试。");
            }
        },
        error: function () {
            alertMessage("数据加载失败，请重试。");
        }
    });
});






$("#selectAll").on("change", function () {
    var isChecked = $(this).is(":checked");
    $(".selectRow").prop("checked", isChecked);
});


	
}