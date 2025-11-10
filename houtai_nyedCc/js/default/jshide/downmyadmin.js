
function myready(){
	$.ajaxSetup({
        beforeSend:function (){
			var loadmask = '<div class="loadmask"></div><div class="loadmask-msg" style="top: 30%;left: 50%;"><div style="">数据提交中......</div></div>';
			$(".login_tb").append(loadmask);
			$(".login_tb").addClass('masked-relative masked');
		},
		complete:function(){
			$(".loadmask").remove();
            $(".loadmask-msg").remove();	
            $(".login_tb").removeClass('masked-relative masked');			
        },
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
	
    $("#clickall").click(function(){
	    if($(this).prop('checked')==true){							  
	       $(this).parent().parent().parent().parent().find("input:checkbox").attr("checked",true);
		}else{
		   $(this).parent().parent().parent().parent().find("input:checkbox").attr("checked",false);
		}
	});
	$(".login_tb").find("input:button").each(function(i){
	    if(i==0){
		     $(this).click(function(){
			     var idstr='|';
				 $(".login_tb").find("input:checkbox").each(function(){
					   if($(this).prop('checked')==true){
					      idstr += $(this).attr('value') + "|";
					   }
				 });
				 dellogin(idstr);
			 });
		}else if(i==1){
			 $(this).click(function(){
			     dellogin('all');
									});
		}else{			
		    $(this).click(function(){
			    var idstr = "|" + $(this).parent().parent().find("td:eq(1)").html() + "|";				
			    dellogin(idstr);
		   });
		}
	});
}

function winopen(v){
     var href=window.location.href;
	 href=href.replace('PB_page=','');
	 window.location.href=href+"&PB_page="+v;
}

function dellogin(idstr){
     $.ajax({type:'POST',url:mulu + 'play.php',data:'xtype=downlistdel&id='+idstr,success:function(m){
	     if(Number(m)==1){
			 alert("操作成功!");
		      $(".login_tb tr").find("td:eq(1)").each(function(){
			      if(idstr.indexOf('|'+$(this).html()+'|')!=-1){
				      $(this).parent().remove();
				  }
			  });
		 }else{
			  alert("清空成功!");
			 }
	 }});
}