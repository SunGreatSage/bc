function myready(){
	$.ajaxSetup({
        beforeSend:function (){
			 $("#con_six").mask("正在执行...");
		},
		complete:function(){
			$("#con_six").unmask("正在执行...");		
        },
      });
	  
	  $('#chkAll').click(function(){
    // 如果全选复选框被选中，则将所有 .ifok 复选框设置为选中状态
    if(this.checked){
      $('.ifok').prop('checked', true);
    } else {
      // 如果全选复选框未被选中，则将所有 .ifok 复选框设置为未选中状态
      $('.ifok').prop('checked', false);
    }});
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
    $("#quickjsff").change(function() {
        var v1 = $(this).val();
		if (v1==0) return false;
		if (v1 == 1) {
				$(".ifzc").val(0);
			} else{
				$(".ifzc").val(1);
					
			}
    });
	
	$(".page-tabs a.g"+gid).addClass("hover");

	$(".page-tabs a").click(function(){	
        window.location.href = mulu + "libset.php?xtype=auto&gid="+$(this).attr('gid');
	});
	
	$(".table_ball td").mouseover(function(){
	    $(this).parent().addClass("hover");
	}).mouseout(function(){
	    $(this).parent().removeClass("hover");
	});
	
	$(".send").click(function(){
		var str='{';
		var err1=false;
		var err2=false;
		var err3=false;
		var i=0;
		$(".nr").each(function(){
			  var ifok ;
			  if($(this).find(".ifok").prop("checked")==true){
			      ifok=1;
			  }else{
			      ifok=0;
			  }
			  var yj;
			  if($(this).find(".yj").prop("checked")==true){
			      yj=1;
			  }else{
			      yj=0;
			  }
			  var ifzc = $(this).find(".ifzc").val();
		
			  var qsnum = Number($(this).find(".qsnum").val());
			  var qspeilv = Number($(this).find(".qspeilv").val());
			  var startje = Number($(this).find(".startje").val());
			  var startpeilv = Number($(this).find(".startpeilv").val());
			  var addje = Number($(this).find(".addje").val());
			  var attpeilv = Number($(this).find(".attpeilv").val());
			  var lowpeilv = Number($(this).find('.lowpeilv').val());
			  var stopje = Number($(this).find('.stopje').val());
			  var ftype = $(this).attr('f');
			  
			  if(i>0) str += ',';
			  str += '"'+i+'":{"ifok":"'+ifok+'","ifzc":"'+ifzc+'","ftype":"'+ftype+'","startje":"'+startje+'","startpeilv":"'+startpeilv+'","addje":"'+addje+'","attpeilv":"'+attpeilv+'","lowpeilv":"'+lowpeilv+'","stopje":"'+stopje+'","yj":"'+yj+'","qsnum":"'+qsnum+'","qspeilv":"'+qspeilv+'"}';
			  if(startje%1!=0 | addje%1!=0 | stopje%1!=0 | qsnum%1!=0) err1=true;
			  if((startpeilv*10000)%1!=0  | (attpeilv*10000)%1!=0 | (lowpeilv*10000)%1!=0 | (qspeilv*10000)%1!=0) err2=true;
			  
			  i++;
		});
		str += '}';

		if(err1){
		     alertMessage("输入的金额不是有效数值！");
			 return false;
		}
		if(err2){
		     alertMessage("输入的赔率不是有效数值！");
			 return false;
		}	
		str = '&str='+str+'&gid='+gid;
		if(!confirm ("是否保存自降倍设置"))	return false;
	    $.ajax({type:'POST',
		   url:mulu + 'libset.php',
		   data:'xtype=setauto'+str,
		   success:function(m){			   
			   if(Number(m)==1){
			      alertMessage("数据保存成功！");
				  //window.location.href=window.location.href;
			   }
		  }
		});
	});
				$(".yiwotongbu").click(function(){
		if(!confirm("是否同步自动降倍到其他彩"))	return false;
        $.ajax({
            type: 'POST',
            url: mulu + 'libset.php',
            data: 'xtype=yiwotongbuauto&gid='+gid,
            success: function(m) {
				
                if (Number(m) == 1) {
                    alertMessage("同步成功！");
                //    window.location.href = window.location.href;
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
		
	    $(this).parent().parent().find("select").each(function(i){
			 var val = $(this).val();
		     $("tr.nr").each(function(){
			     if($(this).find("input:checkbox:eq(0)").prop("checked")){
			         $(this).find("select").val(val);
				 }
			 });
		});
	    $(this).parent().parent().find("input:checkbox").each(function(i){
			if(i>=1){
			 var val = $(this).prop("checked");
		     $("tr.nr").each(function(){
			     if($(this).find("input:checkbox:eq(0)").prop("checked")){
			        if(val){
					    $(this).find("input:checkbox:eq("+i+")").attr("checked",true);
					}else{
					    $(this).find("input:checkbox:eq("+i+")").attr("checked",false);
					}
				 }
			 });
			}
		});
		
	});
}
