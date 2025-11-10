function myready(){
	$.ajaxSetup({
        beforeSend:function (){
			 $("#con_six").mask("正在执行...");
		},
		complete:function(){
			$("#con_six").unmask("正在执行...");		
        },
      });
	  
	  $("#quickbtn").click(function(){		
		   var v1 = $('#txtSetValue').val();
				if (!v1 || isNaN(v1)) return false;
				if (parseFloat(v1) < 0) {
					$('#txtSetValue').val('')
					return false;
				}
				var v2 = $('#selectsetting').val();
				var obj = $('.'+v2);
				obj.each(function(i,n) {
					n.value = v1;
				});
		   });
		   
	//changeh(document.documentElement.scrollHeight + 500);
	$(".page-tabs a.g"+gid).addClass("hover");

	$(".page-tabs a").click(function(){		
        window.location.href = mulu + "libset.php?xtype=warn&gid="+$(this).attr('gid');
	});
	
	$(".table_ball td").mouseover(function(){
	    $(this).parent().addClass("hover");
	}).mouseout(function(){
	    $(this).parent().removeClass("hover");
	});
	
	$(".send").click(function(){
		
		var str = '{';
		var i=0;
		var err = false;
		$(".nr").each(function(){
			  var je = $(this).find(".je").val();
			  var ks = $(this).find(".ks").val();
			  var ftype = $(this).attr('f');
			  if(i>0) str += ',';
			  str += '"'+i+'":{"je":"'+je+'","ks":"'+ks+'","ftype":"'+ftype+'"}';
			  if(je%1!=0) err=true;
			  if(ks%1!=0) err=true;
			  
			  i++;
		});
		str += '}';
	    if(err){
		     alertMessage("输入的金额不是有效数值！");
			return false;
		}
		str = '&str='+str+'&gid='+gid;
		if(!confirm("是否保存货量预警金额设置"))	return false;
	    $.ajax({type:'POST',
		   url:mulu + 'libset.php',
		   data:'xtype=setwarn'+str,
		   success:function(m){
			   if(Number(m)==1){
			      alertMessage("数据保存成功！");
				  //window.location.href=window.location.href;
			   }
		  }
		});
	});
			$(".yiwotongbu").click(function(){
			if(!confirm("是否同步货量预警到其他彩"))	return false;
        $.ajax({
            type: 'POST',
            url: mulu + 'libset.php',
            data: 'xtype=yiwotongbuwarn&gid='+gid,
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
		    $("tr").find("input:checkbox:eq(0)").attr("checked",true);
		}else{
		    $("tr").find("input:checkbox:eq(0)").attr("checked",false);
		}
	});
	
	$("input.pantb").click(function(){
	    $(this).parent().parent().find("input:text").each(function(i){
			 var val = $(this).val();
		     $("tr.nr").each(function(){
			     if($(this).find("input:checkbox:eq(0)").prop("checked")){
			         $(this).find("input:text:eq("+i+")").val(val);
				 }
			 });
		});

	});
}
