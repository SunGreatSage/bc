	 $.ajaxSetup({
        beforeSend:function (){
			var htmlValue = '<div class="loadmask-msg" style="top: 40%;left: 40%;" ><div>正在加载......</div></div>';
			$("html").append(htmlValue);
		},
		complete:function(){
			$(".loadmask").remove();
            $(".loadmask-msg").remove();		
        },
      });