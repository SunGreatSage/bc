function myready() {
//	changeh(document.documentElement.scrollHeight+500);
	$("label").addClass('red');
	$("select.menu").change(function(){
	   window.location.href= $(this).val();
	});
	$(".send").click(function() {
		var str = '[';
		$(".ifok").each(function(i) {
			if(i>0) str += ',';
			str += '{"gid":'+'"'+$(this).val()+'",';
			if($(this).prop("checked")){
			   str += '"ifok":'+'"1"';
			}else{
			   str += '"ifok":'+'"0"';
			}
			if($(this).parent().parent().find(".ifopen").prop("checked")){
			   str += ',"ifopen":'+'"1"';
			}else{
			   str += ',"ifopen":'+'"0"';
			}
			str += ',"px":'+'"'+$(this).parent().parent().find("input:text").val()+'"}';
		});
		str += ']';

		$.ajax({
			type: 'POST',
			url: mulu + 'zshui.php',
			data: 'xtype=setgame&str=' + str,
			success: function(m) {
			 //alert(m);
				if (Number(m) == 1) {
					alertMessage("保存成功!");
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
		// alert(str);
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
	$(".kjset").click(function() {
		var obj = $(this);
		var kjurl = obj.prev().val();
		var gid = $(this).parent().parent().find(".gid").html();
		 var   str = '&kjurl='+kjurl+ "&gid=" + gid;
		// alert(str);
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
	$(".wfset").click(function() {
		var obj = $(this);
		var flname = obj.prev().val();
		var gid = $(this).parent().parent().find(".gid").html();
		 var   str = '&flname='+flname+ "&gid=" + gid;
		// alert(str);
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
}