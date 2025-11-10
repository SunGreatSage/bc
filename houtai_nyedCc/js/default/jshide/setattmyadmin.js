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
					var obj = $('.peilvatt input:text'); 
				} else if (v1 == 'a2') {
					var obj = $('[id^="peilvatt_1"]');
				} else if (v1 == 'a3') {
					var obj = $('[id^="maxatt"]');
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
		   var v4 = $('.pc input:text');
		   var obj = v3 ? $('[name^="'+v3+'1_"]:enabled') : v4;
		  obj.each(function(i,n) {
			  var val = parseFloat(n.value)+v1;
			  n.value = parseFloat(val.toFixed(4));
			  });
		   });
			
	//changeh(document.documentElement.scrollHeight+500);
    $("input:text").addClass('txt2');
    $("input:text").addClass('num');
	$(".tab_panel span.g"+gid).addClass("group_tap_focus");

	$(".tab_panel span").click(function(){		
        window.location.href = mulu + "zshui.php?xtype=setattshow&gid="+$(this).attr('gid');
	});
	
	$(".table_ball td").mouseover(function(){
	    $(this).parent().addClass("hover");
	}).mouseout(function(){
	    $(this).parent().removeClass("hover");
	});
	
	
    $(".send").click(function() {
        var str = "&gid="+gid;
        var flag = true;
        $(".pan input:text").each(function() {
            str += '&' + $(this).attr('id') + "=" + $(this).val();
            if ((Number($(this).val()) * 10000) % 1 != 0) {
                flag = false;
            }
        });
        if (!flag) {
            alertMessage("输入的赔率值不对！");
            return false;
        }
		var attstr = '&';
        var flag = true;
		var err='';
        $(".setatt2 input:text").each(function() {
            attstr += '&' + $(this).attr('id') + "=" + $(this).val();
            if ((Number($(this).val()) * 10000) % 1 != 0) {
				err +=$(this).val()+$(this).attr('id');
                flag = false;
            }
        });
        if (!flag) {
        }
			if(!confirm("是否保存赔率参数"))	return false;
        $.ajax({
            type: 'POST',
            url: mulu + 'zshui.php',
            data: 'xtype=setatt' + str+attstr,
            success: function(m) {
			  // console.log(m); return;
                if (Number(m) == 1) {
                   alertMessage("保存成功!");
                }
            }
        });
    });

	$(".yiwotongbu").click(function(){
		if(!confirm("是否同步赔率参数到其他彩"))	return false;
        $.ajax({
            type: 'POST',
            url: mulu + 'zshui.php',
            data: 'xtype=yiwotongbuatt&gid='+gid,
            success: function(m) {

                if (Number(m) == 1) {
                    alertMessage("同步成功!");
                }
            }
        });  
	});
	
	$(".pan input.all").click(function(){
		
	    if($(this).prop("checked")){
		    $(".pan").find("input:checkbox").attr("checked",true);
		}else{
		    $(".pan").find("input:checkbox").attr("checked",false);
		}
	});
	
	$(".patt input.all").click(function(){
	    if($(this).prop("checked")){
		    $(".patt").find("input:checkbox").attr("checked",true);
		}else{
		    $(".patt").find("input:checkbox").attr("checked",false);
		}
	});
	
	$(".pan input.pantb").click(function(){
	    $(this).parent().parent().find("input:text").each(function(i){
			 var val = $(this).val();
		     $(".pan tr").each(function(){
			     if($(this).find("input:checkbox").prop("checked")){
			         $(this).find("input:text:eq("+i+")").val(val);
				 }
			 });
		});
	});
	
	$(".patt input.patttb").click(function(){
	    $(this).parent().parent().find("input:text").each(function(i){
			 var val = $(this).val();
		     $(".patt tr").each(function(){
			     if($(this).find("input:checkbox").prop("checked")){
			         $(this).find("input:text:eq("+i+")").val(val);
				 }
			 });
		});
	});
	
	$(".patt td.bcs").click(function(){
		var html = $(this).html();
		var check = $(this).parent().find("input:checkbox").prop("checked");
	    $(this).parent().parent().find("td.bcs").each(function(i){
			     if($(this).html()==html){
					 if(check){
			            $(this).parent().find("input:checkbox").prop("checked",false);
					 }else{
			            $(this).parent().find("input:checkbox").prop("checked",true);
					 }
				 }
			
		});
	});
	
	$(".modetb").click(function(){
	    var v = Number($(this).attr('v'));
		$(".patt input.patttb").each(function(){
		    var c = $(this).parent().parent().find("td:eq(1)").html();
			for(i=1;i<=5;i++){
			    if(i!=v){
				    $("#a"+i+'_'+c).val($("#a"+v+'_'+c).val());
					$("#b"+i+'_'+c).val($("#b"+v+'_'+c).val());
					$("#c"+i+'_'+c).val($("#c"+v+'_'+c).val());
					$("#d"+i+'_'+c).val($("#d"+v+'_'+c).val());
					$("#ab"+i+'_'+c).val($("#ab"+v+'_'+c).val());
					 
				}
			}
		});
	});
	
}