function myready() {
	$.ajaxSetup({
        beforeSend:function (){
			var loadmask = '<div class="loadmask"></div><div class="loadmask-msg" style="top: 30%;left: 50%;"><div style="">数据提交中......</div></div>';
			$(".nrtb").append(loadmask);
			$(".nrtb").addClass('masked-relative masked');
		},
		complete:function(){
			$(".loadmask").remove();
            $(".loadmask-msg").remove();	
            $(".nrtb").removeClass('masked-relative masked');			
        },
      });
	  
	changeh(document.documentElement.scrollHeight+500);
	$(".data_table tr").mouseover(function(){$(this).addClass('hover')}).mouseout(function(){$(this).removeClass('hover')});
	$(".deldate").click(function() {
		//WdatePicker();
	});
	$("input.date").click(function() {
		WdatePicker();
	});
	$("."+page).addClass('hover');
	$(".pages a").click(function() {
		if ($(this).attr('class') == 'show') {
			window.location.href = "history.php?xtype=show"
		} else if ($(this).attr('class') == 'useredit') {
			window.location.href = "history.php?xtype=useredit"
		} else if ($(this).attr('class') == 'adminedit') {
			window.location.href = "history.php?xtype=adminedit"
		} else if ($(this).attr('class') == 'agentpeilv') {
			window.location.href = "history.php?xtype=agentpeilv"
		} else if ($(this).attr('class') == 'adminpeilv') {
			window.location.href = "history.php?xtype=adminpeilv"
		} else if ($(this).attr('class') == 'downlist') {
			window.location.href = "play.php?xtype=downlist"
		}
		return true
	});

	$("a.page").click(function() {
		//window.location.href = "history.php?xtype=" + $(".pages a.selected").attr('page') + "&page=" + $(this).html()+ "&gid=" + $(".game").val();
	});
	$("select").change(function() {
		if($(this).hasClass('game')){
		  window.location.href = mulu + "history.php?xtype=" + $(".pages a.hover").attr('page') + "&PB_page=" + 1 + "&gid=" + $(".game").val();
		}else{
		  window.location.href = mulu + "history.php?xtype=" + $(".pages a.hover").attr('page') + "&PB_page=" + $(this).val()+ "&gid=" + $(".game").val();	
		}
	});
	
	$("#clickall").click(function() {
		if ($(this).prop('checked') == true) {
			$(this).parent().parent().parent().parent().find("input:checkbox").attr("checked", true)
		} else {
			$(this).parent().parent().parent().parent().find("input:checkbox").attr("checked", false)
		}
	});
	$("#delselect").click(function() {
		dellogin(1)
	});
	$(".delone").click(function() {
		$("input:checkbox").attr("checked", false);
		$(this).parent().parent().find("input:checkbox").attr("checked", true);
		dellogin(0)
	});
	$(".del").click(function() {
		dellogin('date')
	})
}
function getid() {
	var i = 0;
	var idarr = '[';
	$(".nrtb").find("input:checkbox").each(function() {
		if ($(this).prop('checked') == true && !isNaN($(this).val())){
			if (i > 0) idarr += ','
			idarr += '"' + $(this).val() + '"';
			i++
		}
	});
	idarr += ']';
	return idarr
}
function dellogin(val) {
	if (val == 'date') {
		var idarr = $(".deldate").val() + "&type=date"
	} else {
		var idarr = getid()
	}
	var pass = prompt("请输入密码:","");
	$.ajax({
		type: 'POST',
		url: mulu + 'history.php',
		data: 'xtype=del' + $(".pages a.hover").attr('page') + '&id=' + idarr+"&pass="+pass,
		success: function(m) {
			if (Number(m) == 1) {
				alert("清空成功!");
				window.location.href = "history.php?xtype=" +  $(".pages a.hover").attr('page') + "&gid=" + $(".game").val() + "&page=" + 1
			}
		}
	})
}