function myready() {
	$.ajaxSetup({
        beforeSend:function (){
			 $("#con_six").mask("正在执行...");
		},
		complete:function(){
			$("#con_six").unmask("正在执行...");		
        },
      });
	 
	 $('[id^="quick_"]').on('blur keyup',function() {
				var va = $(this).val();
				if (!va || isNaN(va)) return false;
				if (parseFloat(va) < 0) {
					$(this).val('');
					return false;
				}
				var v1 = $(this).attr('id').split('_')[1];
				var obj = '';
				if (v1 == 'a1') {
					var obj = $('[id^="minje"]'); 
				} else if (v1 == 'a2') {
					var obj = $('[id^="maxje"]');
				} else if (v1 == 'a3') {
					var obj = $('[id^="cmaxje"]');
				}
				if (!obj) return false;
				obj.each(function(i,n) {
					n.value = va;
				});
			});
			$("#quickbtn").click(function(){		
	       var va = $('#quick2').val();
		   if (!va || isNaN(va)) return false;
		   var v1 = parseFloat(va);
		   var v2 = $('#quick1').val(); //盘口
		   var v3 = v2 != '0' ? ''+v2 : '';
		   var obj = v3 ? $('[name^="points'+v3+'"]:enabled') : $('[name^="points"]:enabled');
		  obj.each(function(i,n) {
			  var val = parseFloat(n.value)+v1;
			  n.value = parseFloat(val.toFixed(4));
			  });
		   });
			
	//changeh(document.documentElement.scrollHeight+500);
	$(".tab_panel span.g"+gid).addClass("group_tap_focus");

	$(".tab_panel span").click(function(){		
        window.location.href = mulu + "zshui.php?xtype=show&gid="+$(this).attr('gid');
	});
	
	$(".table_ball td").mouseover(function(){
	    $(this).parent().addClass("hover");
	}).mouseout(function(){
	    $(this).parent().removeClass("hover");
	});
	
	$(".send").click(function() {
		var str = '';
		var flag = true;
		var flags = true;
		$("select").each(function() {
			str += '&' + $(this).attr('id') + "=" + $(this).val();
			if ((Number($(this).val()) * 1000) % 1 != 0) {
				flag = false
			}
		});
		$("input:text").each(function() {
			str += '&' + $(this).attr('id') + "=" + $(this).val();
			if ((Number($(this).val())*1000) % 1 != 0) {
				flags = false
			}
		});
		if (!flag) {}
		if (!flags) {
			//alert("请输入整数!");
			//return false
		}
		var downlineValue = $('input[name="downline"]:checked').val();
		if(!confirm("是否保存初始限额设置"))	return false;
		$.ajax({
			type: 'POST',
			url: mulu + 'zshui.php',
			  data: 'xtype=setpoints' + str + '&gid=' + gid + '&downline=' + downlineValue,
			success: function(m) {
				if(downlineValue == 1) {
        alertMessage("退水保存成功！");
      } else if(downlineValue == 2) {
        alertMessage("退水保存成功，并已全部同步到其他用户！");
      } else if(downlineValue == 3) {
        alertMessage("退水保存成功，并已等量增加到其他用户！");
      }
			}
		})
	});
	
	
		$(".yiwotongbu").click(function(){
		if(!confirm("是否同步初始限额到其他彩"))	return false;
        $.ajax({
            type: 'POST',
            url: mulu + 'zshui.php',
            data: 'xtype=yiwotongbuzshui&gid='+gid,
            success: function(m) {
                if (Number(m) == 1) {
                    alertMessage("同步成功！");
                    //window.location.href = window.location.href;
                }
            }
        });  
	});
	
	$("input.all").click(function(){
	    if($(this).prop("checked")){
		    $("input:checkbox").attr("checked",true);
		}else{
		    $("input:checkbox").attr("checked",false);
		}
	});
	
	$("input.pantb").click(function(){
	    $(this).parent().parent().find("input:text").each(function(i){
			 var val = $(this).val();
		     $("tr").each(function(){
			     if($(this).find("input:checkbox").prop("checked")){
			         $(this).find("input:text:eq("+i+")").val(val);
				 }
			 });
		});
		
	    $(this).parent().parent().find("select").each(function(i){
			 var val = $(this).val();
		     $("tr").each(function(){
			     if($(this).find("input:checkbox").prop("checked")){
			         $(this).find("select").val(val);
				 }
			 });
		});
	});
}