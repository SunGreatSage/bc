function myready() {
	
	
	changeh(document.documentElement.scrollHeight+500);
	
	$(".data_table tr").mouseover(function(){
		$(this).addClass("hover");
	}).mouseout(function(){
		$(this).removeClass("hover");
	});
	$("input:radio").click(function() {
		var name = $(this).attr('class');
		var gid = $(this).parent().parent().parent().find(".gid").html();
		var obj = $(this);
	  // console.log(gid);
		$.ajax({
			type: 'POST',
			url: mulu + 'game.php',
			data: 'xtype=change&name=' + name + "&gid=" + gid,
			success: function(m) {
				if (Number(m) == 1) {
                    alertMessage("设定成功!");
                }else{
					 alertMessage("设定成功!");
                }
			}
		})
	});
	$(".tsset").click(function() {
		var obj = $(this);
		var thisqishu = obj.prev().val();
		var gid = $(this).parent().parent().find(".gid").html();
		 var   str = '&thisqishu='+thisqishu+ "&gid=" + gid;
		// console.log(str); return;
		    $.ajax({
			type: 'POST',
			url: mulu + 'game.php',
			data: 'xtype=editgame' + str,
			success: function(m) {
				$("#test").html(m);
				if (Number(m) == 1) {
					alertMessage("修改成功!");
					//window.location.href = window.location.href
				}
			}
		})
	});
	$(".tlset").click(function() {
		var obj = $(this);
		var thisbml = obj.prev().val();
		var gid = $(this).parent().parent().find(".gid").html();
		 var   str = '&thisbml='+thisbml+ "&gid=" + gid;
		// console.log(str); return;
		    $.ajax({
			type: 'POST',
			url: mulu + 'game.php',
			data: 'xtype=editgame' + str,
			success: function(m) {
				$("#test").html(m);
				if (Number(m) == 1) {
					alertMessage("修改成功!");
					//window.location.href = window.location.href
				}
			}
		})
	});
	$(".oeset").click(function() {
		var obj = $(this);
		var otherclosetime = obj.prev().val();
		var gid = $(this).parent().parent().find(".gid").html();
		 var   str = '&otherclosetime='+otherclosetime+ "&gid=" + gid;
		// console.log(str); return;
		    $.ajax({
			type: 'POST',
			url: mulu + 'game.php',
			data: 'xtype=editgame' + str,
			success: function(m) {
				$("#test").html(m);
				if (Number(m) == 1) {
					alertMessage("设置成功!");
					//window.location.href = window.location.href
				}
			}
		})
	});
	$(".ueset").click(function() {
		var obj = $(this);
		var userclosetime = obj.prev().val();
		var gid = $(this).parent().parent().find(".gid").html();
		 var   str = '&userclosetime='+userclosetime+ "&gid=" + gid;
		// console.log(str); return;
		    $.ajax({
			type: 'POST',
			url: mulu + 'game.php',
			data: 'xtype=editgame' + str,
			success: function(m) {
				$("#test").html(m);
				if (Number(m) == 1) {
					alertMessage("设置成功!");
				//	window.location.href = window.location.href
				}
			}
		})
	});
	$(".ojset").click(function() {
		var obj = $(this);
		var opentimekj = obj.prev().val();
		var gid = $(this).parent().parent().find(".gid").html();
		 var   str = '&opentimekj='+opentimekj+ "&gid=" + gid;
		// console.log(str); return;
		    $.ajax({
			type: 'POST',
			url: mulu + 'game.php',
			data: 'xtype=editgame' + str,
			success: function(m) {
				$("#test").html(m);
				if (Number(m) == 1) {
					alertMessage("设置开盘时间成功!");
					//window.location.href = window.location.href
				}
			}
		})
	});
	$(".cjset").click(function() {
		var obj = $(this);
		var closetimekj = obj.prev().val();
		var gid = $(this).parent().parent().find(".gid").html();
		 var   str = '&closetimekj='+closetimekj+ "&gid=" + gid;
		// console.log(str); return;
		    $.ajax({
			type: 'POST',
			url: mulu + 'game.php',
			data: 'xtype=editgame' + str,
			success: function(m) {
				$("#test").html(m);
				if (Number(m) == 1) {
					alertMessage("设置关盘成功!");
					//window.location.href = window.location.href
				}
			}
		})
	});


}
function strlen(sString) {
	var sStr, iCount, i, strTemp;
	iCount = 0;
	sStr = sString.split("");
	for (i = 0; i < sStr.length; i++) {
		strTemp = escape(sStr[i]);
		if (strTemp.indexOf("%u", 0) == -1) {
			iCount = iCount + 1
		} else {
			iCount = iCount + 2
		}
	}
	return iCount
}