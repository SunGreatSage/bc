function myready() {
	$.ajaxSetup({
        beforeSend:function (){
			 $("#con_six").mask("正在执行...");
		},
		complete:function(){
			$("#con_six").unmask("正在执行...");		
        },
      });
	  
	//changeh(document.documentElement.scrollHeight+500);
	$(".tab_panel span.g"+gid).addClass("group_tap_focus");

	$(".tab_panel span").click(function(){		
        window.location.href = mulu + "zshui.php?xtype=ptype&gid="+$(this).attr('gid');
	});
	
	$(".table_ball td").mouseover(function(){
	    $(this).parent().addClass("hover");
	}).mouseout(function(){
	    $(this).parent().removeClass("hover");
	});
	$(".add").click(function(){
		var id = Number($(".list tr:last td:eq(0)").html());
		isNaN(id) ? id=0 : id=id+1;
		$(".list tbody").append('<tr><td>'+id+'</td><td><input type="text" class="txt c" value="" /></td><td><input type="text" class="txt p" value="" /></td></tr>');
	});
	$(".del").click(function(){
		$(".list tbody tr:last").remove();
	});
	
$(".send").click(function() {
		var data=[];
        $(".list tbody tr").each(function(i){
        	var s = {};
        	s.id = $(this).find("th:eq(0)").html();
        	s.c = $(this).find(".c").val();
        	s.p = $(this).find(".p").val();
        	data[i] = s;
        });
        var gid = $(".tab_panel span.group_tap_focus").attr("gid");
		if(!confirm("是否保存默认赔率设置"))	return false;		
        var pass = prompt("请输入密码:","");
		$.ajax({
			type: 'POST',
			url: mulu + 'zshui.php',
			data: 'xtype=setptype&data='+JSON.stringify(data)+"&gid="+gid+"&pass="+pass,
			success: function(m) {
				console.log(m);
				if (Number(m) == 1) {
					alertMessage("数据保存成功！");
					//window.location.href = window.location.href
				}else if(Number(m)==2){
					alertMessage("密码不正确！");
				}
			}
		})
	});
	
	
		$(".yiwotongbu").click(function(){
		if(!confirm("是否同步默认赔率到其他彩"))	return false;
		var pass = prompt("请输入密码:","");
        $.ajax({
            type: 'POST',
            url: mulu + 'zshui.php',
            data: 'xtype=yiwotongbuptype&gid='+gid+"&pass="+pass,
            success: function(m) {
                if (Number(m) == 1) {
                    alertMessage("同步成功！");
                   // window.location.href = window.location.href;
                }else if(Number(m)==2){
					alertMessage("密码不正确！");
				}
            }
        });  
	});

}