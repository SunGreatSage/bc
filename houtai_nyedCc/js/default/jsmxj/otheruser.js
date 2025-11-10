function myready(){
    var userAgent = navigator.userAgent;
    if (userAgent.indexOf("Safari") != -1 && app==1) {
        $(".main-content").css("height",$(".main-content").height()-50);
        $(".menulist").css("bottom","50px");
    }
    $(".menu").click(function() {
        $(".zhao").removeClass("ivfTfC").addClass("OSUUp");
        $(".menulist.iJamhB").removeClass("iJamhB").addClass("efUsXr");
    });
    $(".zhao").click(function() {
        $(".zhao").removeClass("OSUUp").addClass("ivfTfC");
        $(".menulist.efUsXr").removeClass("efUsXr").addClass("iJamhB");
        $(".gamelist").removeClass("hTJmgb").addClass("iULZxB");
        $(".gamemenu div.jUzzot").removeClass("jUzzot").addClass("kIYJcB");
        $(".gamemenu div.gfEgAp").removeClass("gfEgAp").addClass("epExtR");
    });
    $(".primary").click(function(){
         $(".weui-mask--visible").hide();
    })
	  $(".setabcd").click(function(){
         window.location.href=window.location.href;
    })
    $(".menulist a").click(function() {
        var type = $(this).attr("type");
        if (type == 'home') {
           window.location.href = "/creditmobile/home";
            //window.location.href = "make.php?xtype=show";
        } else if (type == "logout") {
           window.location.href = mulu + "home.php?logout=yes";
        } else if (type == "/Mobile/Index") {
           window.location.href = "/Mobile/Index?xtype=show";
        } else if(type!="" && type!=undefined){
            window.location.href = type;
        }
    });
    if(type=='kj'){
	    $(".gamemenu").click(function(){
        $('.gamelist').slideToggle();

	    });
      $(".gamelist span").click(function(){
            window.location.href = "/Mobile/ResultHistory?gids="+$(this).attr("gid");
      });
    	$(".dates").change(function(){
            getkj();
    	});
    	getkj(ngid);
    }
    if(type=='wjs'){
    	getwjs();
    }
    if(type=='userinfo'){
        getuserinfo();
    }
	if(type=='nft'){
        getnft();
    }
    if(type=="changepass"){
      $(".setpass").click(function(){
           window.location.href="/";
      });
    	getchangepass();
    }
    if(type=="rule"){
    	$(".gamemenu span:eq(0)").html($(".gamelist span:eq(0) div").html());
    	$(".gamemenu span:eq(0)").attr("gid",$(".gamelist span:eq(0)").attr("gid"));
        $(".gamemenu").click(function() {
        $('.gamelist').slideToggle();
        });
        $(".gamelist span").click(function(){
            window.location.href = "/Mobile/GameRule?gids="+$(this).attr("gid");
       });
    	getrule();
    }
    if(type=='baoday'){
    	getbaoday();
    }
    if(type=='baoweek'){
    	$(".baoctrl li a").click(function(){
    		$(".baoctrl li a.active").removeClass("active");
    		$(this).addClass("active");
    		if($(".baoctrl li a:eq(0)").hasClass("active")){
    			$(".thisweek").show();
    			$(".lastweek").hide();
    		}else{
    			$(".thisweek").hide();
    			$(".lastweek").show();	
    		}
    	});
    	$(".lb_back").click(function(){
    		$(".lb_back").parent().hide();
    		$(".baozong").show();
			$(".detail").hide();
    	});
    	getbaoweek();
    }
}
function getbaoweek() {
  $.ajax({
    type: 'POST',
    url:mulu+'bao.php',
    cache: false,
    dataType:'json',
    data: 'xtype=show',
	beforeSend:function (){
			$.showLoading("页面加载中");
		},
		complete:function(){
            $.hideLoading();		
        },
    success: function(m) {
      //console.log(m);
      var obj = $(".thisweek .table-content");
      var data = m['bao'];
      for(i=0;i<7;i++){
        obj.find("a:eq("+i+")").attr("date",data[i]['dates']);
                obj.find("a:eq("+i+")").find("div:eq(0)").html(data[i]['dates']);
                obj.find("a:eq("+i+")").find("div:eq(1)").html(data[i]['zs']);
                obj.find("a:eq("+i+")").find("div:eq(2)").html(data[i]['zje']);
              //  obj.find("a:eq("+i+")").find("div:eq(3)").html(data[i]['zje']);
                obj.find("a:eq("+i+")").find("div:eq(3)").html(data[i]['points']);
                obj.find("a:eq("+i+")").find("div:eq(4) span").html(data[i]['rs']);
                if(Number(data[i]['rs'])>0){
                  obj.find("a:eq("+i+")").find("div:eq(4) span").attr("class","blue_color");
                }else if(Number(data[i]['rs'])<0){
                  obj.find("a:eq("+i+")").find("div:eq(4) span").attr("class","red_color");
                }else{
                    obj.find("a:eq("+i+")").find("div:eq(4) span").attr("class","");
                }
      }
      var obj = $(".thisweek .table-footer");
      obj.find("div:eq(1)").html(m['t']['zs']);
      obj.find("div:eq(2)").html(m['t']['zje']);
    //  obj.find("div:eq(3)").html(m['t']['zje']);
      obj.find("div:eq(3)").html(m['t']['points']);
      obj.find("div:eq(4) span").html(m['t']['rs']);
      if(Number(m['t']['rs'])>0){
        obj.find("div:eq(4) span").attr("class","blue_color");
      }else if(Number(m['t']['rs'])<0){
        obj.find("div:eq(4) span").attr("class","blue_color");
      }else{
        obj.find("div:eq(4) span").attr("class","");
      }
      var obj = $(".lastweek .table-content");
      var data = m['upbao'];
      for(i=0;i<7;i++){
        obj.find("a:eq("+i+")").attr("date",data[i]['dates']);
                obj.find("a:eq("+i+")").find("div:eq(0)").html(data[i]['dates']);
                obj.find("a:eq("+i+")").find("div:eq(1)").html(data[i]['zs']);
                obj.find("a:eq("+i+")").find("div:eq(2)").html(data[i]['zje']);
             //   obj.find("a:eq("+i+")").find("div:eq(3)").html(data[i]['zje']);
                obj.find("a:eq("+i+")").find("div:eq(3)").html(data[i]['points']);
                obj.find("a:eq("+i+")").find("div:eq(4) span").html(data[i]['rs']);
                if(Number(data[i]['rs'])>0){
                  obj.find("a:eq("+i+")").find("div:eq(4) span").attr("class","blue_color");
                }else if(Number(data[i]['rs'])<0){
                  obj.find("a:eq("+i+")").find("div:eq(4) span").attr("class","red_color");
                }else{
                    obj.find("a:eq("+i+")").find("div:eq(4) span").attr("class","");
                }
      }
      var obj = $(".lastweek .table-footer");
      obj.find("div:eq(1)").html(m['t']['uzs']);
      obj.find("div:eq(2)").html(m['t']['uzje']);
    //  obj.find("div:eq(3)").html(m['t']['uzje']);
      obj.find("div:eq(3)").html(m['t']['upoints']);
      obj.find("div:eq(4) span").html(m['t']['urs']);
      if(Number(m['t']['urs'])>0){
        obj.find("div:eq(4) span").attr("class","blue_color");
      }else if(Number(m['t']['urs'])<0){
        obj.find("div:eq(4) span").attr("class","blue_color");
      }else{
        obj.find("div:eq(4) span").attr("class","");
      }
      $(".thisweek a").click(function(){
        $(".baozong").hide();
        $(".detail").show();
        $(".lb_back").parent().show();
        $(".pager1").html(1);
        getbaoday($(this).attr("date"));
      });
      $(".lastweek a").click(function(){
        $(".baozong").hide();
        $(".detail").show();
        $(".lb_back").parent().show();
        $(".pager1").html(1);
        getbaoday($(this).attr("date"));
      });
    }
  })
} 
function getbaoday(date) {
	$.ajax({
		type: 'POST',
		url:mulu+'baoday.php',
		cache: false,
		dataType:'json',
		data: 'xtype=show&date=' + date + "&page=" + $(".pager1").html(),
	    beforeSend:function (){
			$.showLoading("页面加载中");
		},
		complete:function(){
            $.hideLoading();		
        },
		success: function(m) {
			if (m== null){var m=[];}
		   var ml= m.length;
           var str="";
           $(".wjslist").empty();
           if(ml==0){
               $(".wjslist").html('<div class="no_date"><i><img src="/images/no_data.png"></i><p>暂无数据</p></div>');
               return;
           }
           var zs=0;
           var zje=0;
           var rs =0;
           var pageCount_a=0;
           for(i=0;i<ml;i++){
               if(m[i]['z'] == 7) {
                   str = '<div class="table-row" style="text-decoration: line-through;">';
               } else {
                   str = '<div class="table-row">';
               }
               
               
               str += '<div class="col colw4"><span>'+m[i]['gid']+'</span><br><span>第 '+m[i]['qishu']+' 期</span><br><span class="green_color">'+m[i]['tid']+'</span><br><span>'+m[i]['date']+'&nbsp;'+m[i]['time']+'</span></div>';
               str += '<div class="col colw3"><span>'+m[i]['wf']+'</span><br><span class="red_color">@'+m[i]['peilv1'];
               if(Number(m[i]['peilv2'])>1) str += '/'+m[i]['peilv2']; 
               str += '</span></div>';
               str += '<div class="col colw2"><span>'+m[i]['je']+'</span></div>';
               if(Number(m[i]['rs'])>0){
                   str += '<div class="col colw1"><span class="blue_color">'+m[i]['rs']+'</span><br><span class="green_color">退水:'+m[i]['points']+'%</span></div></div>';
               }else{
               	   str += '<div class="col colw1"><span class="red_color">'+m[i]['rs']+'</span><br><span class="green_color">退水:'+m[i]['points']+'%</span></div></div>';
               }
               $(".wjslist").append(str);
               if(m[i]['z'] != 7) {
               zje += Number(m[i]['je']);
               rs += Number(m[i]['rs']);
               }
           }
           $(".pager2").html("/"+m[0]['pcount']+"");
           $(".pager1").html(m[0]['page']);
           $(".pager1").attr('v',m[0]['page']);
           $("#pageCount").html(ml);
		   $("#pageBettingAmount").html(getResult(zje,0));
		     if(getResult(rs,2)>0){
			   $("#pageResult").addClass("blue_f");
			     }else{
				$("#pageResult").addClass("red_f");
					 }
           $("#pageResult").html(getResult(rs,2)); ;
		   $("#totalCount").html(m[0]['total']['zs']);
           $("#totalBettingAmount").html(getResult(m[0]['total']['je'],0));
		   if(getResult(m[0]['total']['jg'],2)>0){
			   $("#totalResult").addClass("blue_f");
			     }else{
				$("#totalResult").addClass("red_f");
					 }
           $("#totalResult").html(getResult(m[0]['total']['jg'],2));
      
           if(m[0]['page']==1){
           	   $(".prev").addClass("disabled");
           }else{
           	   $(".prev").removeClass("disabled");
           }
           if(m[0]['pcount']==m[0]['page']){
           	   $(".next").addClass("disabled");
           }else{
           	   $(".next").removeClass("disabled");
           }
           $(".wjslist").attr("date",m[0]['dates']);
           $(".next").unbind("click");
           $(".prev").unbind("click"); 
           $(".next").click(function(){
           	   if($(this).hasClass("disabled")) return false;
           	   $(".pager1").html(Number($(".pager1").html())+1);
           	   getbaoday($(".wjslist").attr("date"))
           });
           $(".prev").click(function(){
           	   if($(this).hasClass("disabled")) return false;
           	   $(".pager1").html(Number($(".pager1").html())-1);
           	   getbaoday($(".wjslist").attr("date"))
           });
           str=null;
           m=null;
		}
	})
} 
function getrule(){
    var gg = $(".gamemenu span:eq(0)").attr("gid");
	$.ajax({
		type: 'POST',
		url:mulu+'rule.php',
		cache: false,
		async:false,
		data: 'xtype=show&gid=' + gg,
		success: function(m) {
		   $(".rulecon").html(m);

		}
	});
}
function getchangepass() {

	var ptest = /^.*(?=.{6,})(?=.*\d)(?=.*[A-Z])(?=.*[a-z]).*$/;
	$.ajax({
		type: 'POST',
		url:mulu+'changepass.php',
		cache: false,
		async: false,
		data: '',
		success: function(m) {
     //$(".menulist").hide();
      $(".passcon").html(m);
      $(".passcon button").attr("disabled", true);
      $(".passcon .rset").click(function() {
         $(".passcon input").val('');
         $(".passcon button").attr("disabled", true);
      });
      $(".passcon input:password").blur(function(){
           var cons=0;
           $(".passcon input:password").each(function(){
               if($(this).val()!=""){
                  cons=1;
               }
           });
           if(cons==1){
              $(".passcon button").attr("disabled", false);
           }else{
              $(".passcon button").attr("disabled", true);
           }
      });
			$(".passcon .blue_btn").click(function() {
				var pass0 = $("input.oldpassword").val();
				var pass1 = $("input.newpassword").val();
				var pass2 = $("input.confirmpassword").val();
				if (pass0 == '') {
					$(".weui-dialog__bd").html("请输入原始密码！");
                    $(".weui-mask--visible").show();
                    return false;
				}
		
				if (!ptest.test(pass1)) {
					$(".weui-dialog__bd").html("密码必须由6-20字符包含大小写字母和数字组合组成"+pass1);
                    $(".weui-mask--visible").show();
                    return false;
				}
				if (pass1 != pass2) {
					$(".weui-dialog__bd").html("新设密码 和 新设密码确认 不一样！");
                    $(".weui-mask--visible").show();
                    return false;
				}
				if (pass0 == pass1) {
					$(".weui-dialog__bd").html("新密码和旧密码不能一样!");
                    $(".weui-mask--visible").show();
                    return false;
				}
				if (!confirm("是否确定要修改密码？")) return false;
				pass0 = men_md5_password(pass0);
				pass1 = men_md5_password(pass1);
				pass2 = men_md5_password(pass2);
				var str = "&pass0=" + pass0 + "&pass1=" + pass1 + "&pass2=" + pass2;
				$.ajax({
					type: 'POST',
					url:mulu+'changepass.php',
					data: 'xtype=changepass' + str,
					success: function(m) {
						m = Number(m);
						if (m == 1) {
							 $(".weui-dialog__bd").html("原密码错误");
               $(".weui-mask--visible").show();
						} else if (m == 2) {
               $(".weui-dialog__bd").html("修改密码成功,将跳转到首页!");
               $(".weui-mask--visible").show();
			   $(".weui-dialog__btn").hide();
			   $(".setpass").show();
						/*	 top.window.location.href = "/";*/
						}
					}
				})
			});
			m = null;
		}
	});
}
function getuserinfo(){

	$.ajax({
		type: 'POST',
		url:mulu+'userinfo.php',
		cache: false,
		async:false,
		data: 'xtype=show',
		success: function(m) {
		   $(".userinfocon").html(m);
			$("button.qd").click(function(){
                $.ajax({
                    type:'POST',
                    url:mulu+'userinfo.php',
                    data:"xtype=setdefaultpan&pan="+$("#abcd").val(),
                    success:function(m){
                        if(Number(m)==1){
                          $(".weui-dialog__bd").html("设置成功,默认盘口:"+$("#abcd").val()+"");
						  $(".weui-dialog__title").html("提示");
						   $(".setabcd").show();
						   $(".primary").hide();
						   $(".weui-mask--visible").show();
						  // window.location.href=window.location.href;
                        }
                    }
                });
			});
		   $(".dropbtn").unbind("click");
		   $("#lotteryName").change(function(){
                var gg = $("#lotteryName").val();
                $(".conlist").hide();
                $(".g"+gg).show();
		   });
           m=null;
		}
	});
}
function getwjs(){
	$.ajax({
		type: 'POST',
		url:mulu+'lib.php',
		dataType: 'json',
		cache: false,
		data: "xtype=wjs&page=" + $(".pager1").html(),
		beforeSend:function (){
			$.showLoading("页面加载中");
		},
		complete:function(){
            $.hideLoading();		
        },
		success: function(m) {
		if (m== null){var m=[];}
           var ml= m.length;
           var str="";
           $(".wjslist").empty();
           if(ml==0){
               $(".wjslist").html('<div class="no_date"><i><img src="/images/no_data.png"></i><p>暂无数据</p></div>');
               return;
           }
           var zs=0;
           var ky=0;
           var zje=0;
           for(i=0;i<ml;i++){
               if(m[i]['z'] == 7) {
                   str = '<div class="table-row" style="text-decoration: line-through;">';
               } else {
                   str = '<div class="table-row">';
               }
               
               str += '<div class="col colw4"><span>'+m[i]['gid']+'</span><br><span>第 '+m[i]['qishu']+' 期</span><br><span class="green_color">'+m[i]['tid']+'</span><br><span>'+m[i]['date']+'</span>&nbsp;<span>'+m[i]['time']+'</span></div>';
               str += '<div class="col colw3"><span>'+m[i]['wf']+'</span><br><span class="red_color">@'+m[i]['peilv1'];
               if(Number(m[i]['peilv2'])>1) str += '/'+m[i]['peilv2']; 
               str += '</span></div>';
               str += '<div class="col colw2"><span>'+m[i]['je']+'</span></div><div class="col colw1"><span class="red_color">'+m[i]['ky']+'</span><br><span class="green_color">退水:'+m[i]['points']+'%</span></div></div>';
               $(".wjslist").append(str);
               if(m[i]['z'] != 7) {
               zje += Number(m[i]['je']);
               ky += Number(m[i]['ky']);
               }
           }
		   $(".pager2").html("/"+m[0]['pcount']+"");
           $(".pager1").html(m[0]['page']);
           $(".pager1").attr('v',m[0]['page']);
		   $("#pageCount").html(ml);
		   $("#pageBettingAmount").html(getResult(zje,0));
           $("#pageResult").html(getResult(ky,2)); 
           $("#totalCount").html(m[0]['total']['zs']);
           $("#totalBettingAmount").html(getResult(m[0]['total']['je'],0));
           $("#totalResult").html(getResult(m[0]['total']['ky'],2));
		   //分页
		   if(m[0]['page']==1){
           	   $(".prev").addClass("disabled");
           }else{
           	   $(".prev").removeClass("disabled");
           }
           if(m[0]['pcount']==m[0]['page']){
           	   $(".next").addClass("disabled");
           }else{
           	   $(".next").removeClass("disabled");
           }
		   $(".next").unbind("click");
           $(".prev").unbind("click"); 
		    $(".next").click(function(){
           	   if($(this).hasClass("disabled")) return false;
           	   $(".pager1").html(Number($(".pager1").html())+1);
			   getwjs();
           });
           $(".prev").click(function(){
           	   if($(this).hasClass("disabled")) return false;
           	   $(".pager1").html(Number($(".pager1").html())-1);
			   getwjs();
           });
           str=null;
           m=null;
		}
	});
}

function getkj(){
	var gid = ngid;
	var dates = $(".dates").val();
	$.ajax({
		type: 'POST',
		url:mulu+'kj.php',
		dataType: 'json',
		cache: false,
		data: "xtype=getkj&dates=" + dates + "&gid=" + gid + "&page=" + $(".pager1").html(),
		beforeSend:function (){
			$.showLoading("页面加载中");
		},
		complete:function(){
            $.hideLoading();		
        },
		success: function(m) {
      
           // console.log(JSON.stringify(m));
            switch(m['fenlei']){
            	case '107': 
            	    $(".kjbt").html("<div class='rBoyH hm'>号码</div><div class='ctglGG dx'>大小</div><div class='ctglGG ds'>单双</div><div class='ctglGG zh'>冠亚/龙虎</div><div class='ctglGG ft' style='display:none;'>番摊</div>");
            	break;
            	case '101':
            	case '103':
            	case '163':
            	case '121':
            	    $(".kjbt").html("<div class='rBoyH hm'>号码</div><div class='ctglGG dx'>大小</div><div class='ctglGG ds'>单双</div><div class='ctglGG zh'>总和</div><div class='ctglGG ft' style='display:none;'>番摊</div>");
            	break;
            	case '161':
            	    $(".kjbt").html("<div class='rBoyH hm'>号码</div><div class='ctglGG zh'>总和</div><div class='ctglGG ft' style='display:none;'>番摊</div>");
            	break;
            	case '151':
            	    $(".kjbt").html("<div class='rBoyH hm'>号码</div><div class='ctglGG yxx'>鱼虾蟹</div>");
            	break;
            	case '100':
            	    $(".kjbt").html("<div class='rBoyH hm'>号码</div><div class='ctglGG tmdx'>特大小</div><div class='ctglGG zh'>总和</div>");
            	break;
            }
            if(m['ft']=='1'){
                $(".kjbt .ft").show();
            }
            var kj = m['kj'];
            var kl = kj.length;
           // console.log(JSON.stringify(kj));
            $(".kjlist").empty();
            var tops=0;
			var tops2=0;
            arr = ["小","大"];
            sxarr= ['鼠', '牛', '虎', '兔', '龍', '蛇', '馬', '羊', '猴', '雞', '狗', '豬'];
            for(i=0;i<kl;i++){
            	tops=i*3.2;
				tops2=i*3.2+3.2+'em';
            	var zhstr = " yxx='"+joinarr(kj[i]['yxx'])+"' ds='"+joinarr(kj[i]['ds'])+"'  dx='"+joinarr(kj[i]['dx'])+"'  lh='"+joinarr(kj[i]['lh'])+"'  zonghe='"+joinarr(kj[i]['zonghe'])+"'  ft='"+joinarr(kj[i]['ft'])+"' ";
            	var str = "<div class='xucEQ lists' style='height: 3.2em; left: 0px; position: absolute; top: "+tops+"em; width: 100%;'><div class='zyzeo4-5 cJXNwD'>";
            	str += "<span class='title'>"+kj[i]['qishu']+"</span><span class='title'>"+kj[i]['kjtime']+"</span></div><div class='zyzeo4-5 jewTjd listma' "+zhstr+">";
            	if(m['fenlei']=='161') str += "<div class='sc-1i3dxqj-0 dZPZqu'><div class='row'>";
                 var tmp;
            	
            	for(j=0;j<m['mnum'];j++){
            		if(m['fenlei']=='100' && j==6) str += "<span class='hm eZghFE'><div class='bblack'>+</div></span>";
            		if(m['fenlei']=='100') str += "<span class='hm eZghFE'>";
            		
            		if(m['fenlei']=='161' && j==10) str += "</div><div class='row'>";

                // console.log(kj[i],'m' + j);

            		 if (kj[i]['num'][j]!='') str += qiu(m['fenlei'],kj[i]['num'][j]);

                // if(m['fenlei']=='100') str += "<div class='bblack'>"+sxarr[kj[i]['sx'][j]]+"</div>";
                if(m['fenlei']=='100' && kj[i]['sx'][j] ) str += "<div class='bblack'>"+kj[i]['sx'][j]+"</div>";

                if(m['fenlei']=='100') str += "</span>";

            	}



           		 
            		if(m['fenlei']=='151'){
                        str += "<div class='bzh'>"+kj[i]['zonghe'][0]+"</div>";
                        str += "<div class='b"+fenlei+"dx"+kj[i]['zonghe'][1]+"'>"+arr[kj[i]['zonghe'][1]]+"</div>";
            		}
            	if(m['fenlei']=='161') str += "</div></div>";
            	str += "</div></div>";
            	$(".kjlist").append(str);
            }
			$(".pager2").html("/"+m['pcount']+"");
           $(".pager1").html(m['page']);
           $(".pager1").attr('v',m['page']);
		   //分页
		    if(m['page']==1){
           	   $(".prev").addClass("disabled");
           }else{
           	   $(".prev").removeClass("disabled");
           }
           if(m['pcount']==m['page']){
           	   $(".next").addClass("disabled");
           }else{
           	   $(".next").removeClass("disabled");
           }
		   $(".next").unbind("click");
           $(".prev").unbind("click"); 
		   $(".next").click(function(){
           	   if($(this).hasClass("disabled")) return false;
           	   $(".pager1").html(Number($(".pager1").html())+1);
			   getkj()
           });
           $(".prev").click(function(){
           	   if($(this).hasClass("disabled")) return false;
           	   $(".pager1").html(Number($(".pager1").html())-1);
			   getkj()
           });
		   
            $(".listma div").addClass("hm");
         /*   $(".kjlist").parent().width($(document).width());*/
            /*$(".kjlist").css('max-width',$(document).width());*/
            $(".kjlist").css('height',tops2);
            $(".kjlist").css('max-height',tops2);
            kjfunc(m['fenlei']);
			kj=null;
			m = null;
			str=null;

		}
	})
}
function getnft(){
	$.ajax({
		type: 'POST',
		url:mulu+'nft.php',
		cache: false,
		beforeSend:function (){
			$.showLoading("页面加载中");
		},
		complete:function(){
            $.hideLoading();		
        },
		data: 'xtype=nft',
		success: function(m) {
		   $(".list_notice_container").html(m);
		   console.log(m);

		}
	});
}
function joinarr(arr){
   if(!$.isArray(arr)) return "";
   else return arr.join(',');
}
function kjfunc(fenlei) {
	switch (fenlei) {
	case '100':
		$(".kjbt div").click(function() {
			$(".kjbt div.rBoyH").removeClass("rBoyH").addClass("ctglGG");
			$(this).removeClass("ctglGG").addClass("rBoyH");
			if ($(this).hasClass("hm")) {
				$(".listma").find(".other").remove();
				$(".listma .hm").show();
			}else if ($(this).hasClass("zh")) {
				$(".listma").find(".other").remove();
				dsarr = ["单", "双"];
				dxarr = ["小", "大"];
				bsarr = ["蓝", "红", "绿"];
				$(".listma").each(function() {
					var tmp = $(this).attr("zonghe").split(",");
					$(this).find(".hm").hide();
					$(this).append("<div class='other bzh'>" + tmp[0] + "</div>");
					$(this).append("<div class='other b107ds" + tmp[2] + "'>" + dsarr[tmp[2]] + "</div>");
					$(this).append("<div class='other b107dx" + tmp[1] + "'>" + dxarr[tmp[1]] + "</div>");
					$(this).append("<div class='other b107bs" + tmp[6] + "'>" + bsarr[tmp[6]] + "</div>"); 
				});
			 }else if ($(this).hasClass("tmdx")) {
				$(".listma").find(".other").remove();
				dsarr = ["单", "双", "和"];
				dxarr = ["小", "大", "和"];
				dzarr = ["小", "大", "和", "中"];
				$(".listma").each(function() {
					var tmp = $(this).attr("zonghe").split(",");
					$(this).find(".hm").hide();
					$(this).append("<div class='other b107ds" + tmp[3] + "'>" + dsarr[tmp[3]] + "</div>");
					$(this).append("<div class='other b107dx" + tmp[4] + "'>" + dxarr[tmp[4]] + "</div>");
					$(this).append("<div class='other b107dz" + tmp[5] + "'>" + dzarr[tmp[5]] + "</div>");
				});
			 }else if ($(this).hasClass("ft")) {
				$(".listma").find(".other").remove();
				dsarr = ["单", "双"];
				dxarr = ["小", "大"];
				$(".listma").each(function() {
					var tmp = $(this).attr("ft").split(",");
					$(this).find(".hm").hide();
					$(this).append("<div class='other bblack' style='margin-right:30px;'>" + tmp[4] + "</div>");
					$(this).append("<div class='other bblack'>" + tmp[0] + "</div>");
					$(this).append("<div class='other bzh'>" + tmp[1] + "</div>");
					$(this).append("<div class='other b107dx" + tmp[2] + "'>" + dxarr[tmp[2]] + "</div>");
					$(this).append("<div class='other b107ds" + tmp[3] + "'>" + dsarr[tmp[3]] + "</div>");
					
				});
			}
		})
		break;
	case '107':
		$(".kjbt div").click(function() {
			$(".kjbt div.rBoyH").removeClass("rBoyH").addClass("ctglGG");
			$(this).removeClass("ctglGG").addClass("rBoyH");
			if ($(this).hasClass("hm")) {
				$(".listma").find(".other").remove();
				$(".listma .hm").show();
			} else if ($(this).hasClass("ds")) {
				$(".listma").find(".other").remove();
				arr = ["单", "双"];
				$(".listma").each(function() {
					var tmp = $(this).attr("ds").split(",");
					$(this).find(".hm").hide();
					for (var i in tmp) {
						$(this).append("<div class='other b" + fenlei + "ds" + tmp[i] + "'>" + arr[tmp[i]] + "</div>");
					}
				});
			} else if ($(this).hasClass("dx")) {
				$(".listma").find(".other").remove();
				arr = ["小", "大"];
				$(".listma").each(function() {
					var tmp = $(this).attr("dx").split(",");
					$(this).find(".hm").hide();
					for (var i in tmp) {
						$(this).append("<div class='other b" + fenlei + "dx" + tmp[i] + "'>" + arr[tmp[i]] + "</div>");
					}
				});
			} else if ($(this).hasClass("zh")) {
				$(".listma").find(".other").remove();
				dsarr = ["单", "双"];
				dxarr = ["小", "大"];
				lharr = ["虎", "龙"];
				$(".listma").each(function() {
					var tmp = $(this).attr("zonghe").split(",");
					$(this).find(".hm").hide();
					$(this).append("<div class='other bzh'>" + tmp[0] + "</div>");
					$(this).append("<div class='other b" + fenlei + "dx" + tmp[1] + "'>" + dxarr[tmp[1]] + "</div>");
					$(this).append("<div class='other b" + fenlei + "ds" + tmp[2] + "'>" + dsarr[tmp[2]] + "</div>");
					$(this).append("<div class='other' style='width:10px;'></div>");
					var tmp = $(this).attr("lh").split(",");
					for (var i in tmp) {
						$(this).append("<div class='other b" + fenlei + "lh" + tmp[i] + "'>" + lharr[tmp[i]] + "</div>");
					}
				});
			} else if ($(this).hasClass("ft")) {
				$(".listma").find(".other").remove();
				dsarr = ["单", "双"];
				dxarr = ["小", "大"];
				$(".listma").each(function() {
          var tmp = $(this).attr("ft").split(",");
          var nums = tmp[4].split('+');
          var str = '';
          for(var x in nums){
              str += $(this).find(".hm:eq("+(nums[x]-1)+")").prop("outerHTML");
          }
          $(this).find(".hm").hide();
          $(this).append("<div class='other bblack' style='margin-right:10px;width:100px;'>" + str + "</div>");
					$(this).append("<div class='other bblack'>" + tmp[0] + "</div>");
					$(this).append("<div class='other bzh'>" + tmp[1] + "番</div>");
					$(this).append("<div class='other b" + fenlei + "dx" + tmp[2] + "'>" + dxarr[tmp[2]] + "</div>");
					$(this).append("<div class='other b" + fenlei + "ds" + tmp[3] + "'>" + dsarr[tmp[3]] + "</div>");
					
				});
        $(".listma .other .hm").css("float","left");
        $(".listma .other .hm").css("margin-right","5px");
        $(".listma .other .hm").show();
			}
		})
		break;
	case '101':
	case '103':
	case '121':
	case '163':
	    var flid = fenlei;
	    fenlei=101;
		$(".kjbt div").click(function() {
			$(".kjbt div.rBoyH").removeClass("rBoyH").addClass("ctglGG");
			$(this).removeClass("ctglGG").addClass("rBoyH");
			if ($(this).hasClass("hm")) {
				$(".listma").find(".other").remove();
				$(".listma .hm").show();
			} else if ($(this).hasClass("ds")) {
				$(".listma").find(".other").remove();
				arr = ["单", "双","和"];
				$(".listma").each(function() {
					var tmp = $(this).attr("ds").split(",");
					$(this).find(".hm").hide();
					for (var i in tmp) {
						$(this).append("<div class='other b" + fenlei + "ds" + tmp[i] + "'>" + arr[tmp[i]] + "</div>");
					}
				});
			} else if ($(this).hasClass("dx")) {
				$(".listma").find(".other").remove();
				arr = ["小", "大","和"];
				$(".listma").each(function() {
					var tmp = $(this).attr("dx").split(",");
					$(this).find(".hm").hide();
					for (var i in tmp) {
						$(this).append("<div class='other b" + fenlei + "dx" + tmp[i] + "'>" + arr[tmp[i]] + "</div>");
					}
				});
			} else if ($(this).hasClass("zh")) {
				$(".listma").find(".other").remove();
				dsarr = ["单", "双"];
				dxarr = ["小", "大","和"];
				lharr = ["虎", "龙","和"];
				qtarr = ["豹子","顺子","对子","半顺","杂六"];
				$(".listma").each(function() {
					var tmp = $(this).attr("zonghe").split(",");
					var t2 = $(this).attr("lh").split(",");
					$(this).find(".hm").hide();
					$(this).append("<div class='other bzh'>" + tmp[0] + "</div>");
					$(this).append("<div class='other b" + fenlei + "dx" + tmp[1] + "'>" + dxarr[tmp[1]] + "</div>");
					$(this).append("<div class='other b" + fenlei + "ds" + tmp[2] + "'>" + dsarr[tmp[2]] + "</div>");
                    if(flid==101){
                      $(this).append("<div class='other b" + fenlei + "ds" + t2[0] + "'>" + lharr[t2[0]] + "</div>");
					   $(this).append("<div class='other b" + fenlei + "qt" + arrindex(qtarr,tmp[3]) + "'>" + tmp[3] + "</div>");
					   $(this).append("<div class='other' style='width:10px;'></div>");
					   $(this).append("<div class='other b" + fenlei + "qt" + arrindex(qtarr,tmp[4]) + "'>" + tmp[4] + "</div>");
					   $(this).append("<div class='other' style='width:10px;'></div>");
					   $(this).append("<div class='other b" + fenlei + "qt" + arrindex(qtarr,tmp[5]) + "'>" + tmp[5] + "</div>");
					   
                    }else if(flid==103){
				    	var tmp = $(this).attr("lh").split(",");
					    for (var i in tmp) {
						   $(this).append("<div class='other b" + fenlei + "ds" + tmp[i] + "'>" + lharr[tmp[i]] + "</div>");
					    }
                    }else{
                    	 $(this).append("<div class='other b" + fenlei + "ds" + t2[0] + "'>" + lharr[t2[0]] + "</div>");
                    }

				});
			} else if ($(this).hasClass("ft")) {
				$(".listma").find(".other").remove();
				dsarr = ["单", "双"];
				dxarr = ["小", "大"];
				$(".listma").each(function() {
					var tmp = $(this).attr("ft").split(",");
          var nums = tmp[4].split('+');
          var str = '';
          for(var x in nums){
              str += $(this).find(".hm:eq("+(nums[x]-1)+")").prop("outerHTML");
          }
					$(this).find(".hm").hide();
					$(this).append("<div class='other bblack' style='margin-right:20px;width:100px;'>" + str + "</div>");
					$(this).append("<div class='other bblack'>" + tmp[0] + "</div>");
					$(this).append("<div class='other bzh'>" + tmp[1] + "番</div>");
					$(this).append("<div class='other b" + fenlei + "dx" + tmp[2] + "'>" + dxarr[tmp[2]] + "</div>");
					$(this).append("<div class='other b" + fenlei + "ds" + tmp[3] + "'>" + dsarr[tmp[3]] + "</div>");
				});
        $(".listma .other .hm").css("float","left");
        $(".listma .other .hm").css("margin-right","2px");
        $(".listma .other .hm").show();
			}
		})
		break;
		case "161":
		$(".kjbt div").click(function() {
			$(".kjbt div.rBoyH").removeClass("rBoyH").addClass("ctglGG");
			$(this).removeClass("ctglGG").addClass("rBoyH");
			if ($(this).hasClass("hm")) {
				$(".listma").find(".other").remove();
				$(".listma .hm").show();
			}else if ($(this).hasClass("zh")) {
				$(".listma").find(".other").remove();
				dsarr = ["单", "双"];
				dxarr = ["小", "大","和"];
				qhduoarr = ["前(多)", "后(多)","和"];
				dsduoarr = ["单(多)", "双(多)","和"];
				wharr = ["金","木","水","火","土"];
				$(".listma").each(function() {
					var tmp = $(this).attr("zonghe").split(",");
					$(this).find(".hm").hide();
					$(this).append("<div class='other bzh'>" + tmp[0] + "</div>");
					$(this).append("<div class='other b" + fenlei + "dx" + tmp[1] + "'>" + dxarr[tmp[1]] + "</div>");
					$(this).append("<div class='other b" + fenlei + "ds" + tmp[2] + "'>" + dsarr[tmp[2]] + "</div>");
					$(this).append("<div class='other b" + fenlei + "wh" + arrindex(wharr,tmp[3]) + "'><strong>" + tmp[3] + "</strong></div>");
                    $(this).append("<div class='other b" + fenlei + "duo" + tmp[4] + "'>" + dsduoarr[tmp[4]] + "</div>");
                    $(this).append("<div class='other b" + fenlei + "duo" + tmp[5] + "'>" + qhduoarr[tmp[5]] + "</div>");
				});
			} else if ($(this).hasClass("ft")) {
				$(".listma").find(".other").remove();
				dsarr = ["单", "双"];
				dxarr = ["小", "大"];
				$(".listma").each(function() {
					var tmp = $(this).attr("ft").split(",");
					$(this).find(".hm").hide();
					$(this).append("<div class='other bblack' style='margin-right:30px;'>" + tmp[4] + "</div>");
					$(this).append("<div class='other bblack'>" + tmp[0] + "</div>");
					$(this).append("<div class='other bzh'>" + tmp[1] + "番</div>");
					$(this).append("<div class='other b" + fenlei + "dx" + tmp[2] + "'>" + dxarr[tmp[2]] + "</div>");
					$(this).append("<div class='other b" + fenlei + "ds" + tmp[3] + "'>" + dsarr[tmp[3]] + "</div>");
				});
			}
		})
		break;
		case "151":
		$(".kjbt div").click(function() {
			$(".kjbt div.rBoyH").removeClass("rBoyH").addClass("ctglGG");
			$(this).removeClass("ctglGG").addClass("rBoyH");
			if ($(this).hasClass("hm")) {
				$(".listma").find(".other").remove();
				$(".listma .hm").show();
			}else if ($(this).hasClass("yxx")) {
				$(".listma").find(".other").remove();
				yxxarr = ['鱼', '虾', '葫芦', '金钱', '蟹', '鸡'];
				$(".listma").each(function() {
					$(this).find(".hm").hide();
				    var tmp = $(this).attr("yxx").split(",");
					for (var i in tmp) {
					    $(this).append("<div class='other b161wh" + arrindex(yxxarr,tmp[i]) + "'>" + tmp[i]  + "</div>");
					}
				});
			}
		})
		break;
	}
}
function arrindex(arr,v){
	for(var i in arr){
		if(arr[i]==v){
			return i;
		}
	} 
}
function qiu(fenlei,ns) {
    if (isNaN(ns)){
        if(ns.indexOf("红")!=-1 || ns.indexOf("紅")!=-1){
            return "<div class='fred'>"+ns+"</div>";
        }else if(ns.indexOf("蓝")!=-1 || ns.indexOf("藍")!=-1){
            return "<div class='fblue'>"+ns+"</div>";
        }else if(ns.indexOf("绿")!=-1 || ns.indexOf("綠")!=-1){
            return "<div class='fgreen'>"+ns+"</div>";
        }else{
            return ns;
        }
    }
    var str;
    switch(fenlei){
        case '107':
            if(Number(ns)<10 && strlen(ns)==1){
               ns = '0'+ns;
            }
            str = "<div class='b"+fenlei+ns+"' m='"+ns+"'>"+Number(ns)+"</div>";
        break;
        case '103':
            if(Number(ns)>=19){
                str = "<div class='b"+fenlei+"red' m='"+ns+"'>"+Number(ns)+"</div>"; 
            }else{
                str = "<div class='b"+fenlei+"' m='"+ns+"'>"+Number(ns)+"</div>";   
            }
            
        break;
        case '121':
            if(Number(ns)==11){
                str = "<div class='b"+fenlei+"red' m='"+ns+"'>"+Number(ns)+"</div>"; 
            }else{
                str = "<div class='b"+fenlei+"' m='"+ns+"'>"+Number(ns)+"</div>";   
            }
            
        break;
        case '101':
        case '163':
            if(ngid=='101'){
                str = "<div class='bsx"+ns+"'></div>";
            }else{
                str = "<div class='b"+fenlei+"' m='"+ns+"'>"+Number(ns)+"</div>";
            }
        break;
        case '161':
            if(Number(ns)>=41){
                str = "<div class='b"+fenlei+"b' m='"+ns+"'>"+Number(ns)+"</div>"; 
            }else{
                str = "<div class='b"+fenlei+"a' m='"+ns+"'>"+Number(ns)+"</div>";   
            }
        break;
        case '151':
            ns =Number(ns);
            if(ns<10){
                str = "<div class='b"+fenlei+ns+"' m='"+ns+"'></div>";
            }else if(ns<10){
                str = "<div class='b"+fenlei+(Math.floor(ns/10))+"'></div>"+"<div class='b"+fenlei+(ns%10)+"'></div>";
            }else{
                str = "<div class='b"+fenlei+(Math.floor(ns/100))+"'></div>"+"<div class='b"+fenlei+(Math.floor(ns/10))+"'></div>"+"<div class='b"+fenlei+(ns%10)+"'></div>";
            }
            
        break;
        case '100':
           ns =Number(ns);
		   str = "<div class='odds x"+Number(ns)+"'>"+Number(ns)+"</div>"; 
         /*  if($.inArray(ns,sma['紅'])!=-1){
               str = "<div class='bred'>"+Number(ns)+"</div>"; 
           }else if($.inArray(ns,sma['藍'])!=-1){
               str = "<div class='bblue'>"+Number(ns)+"</div>"; 
           }else if($.inArray(ns,sma['綠'])!=-1){
               str = "<div class='bgreen'>"+Number(ns)+"</div>"; 
           }*/
		   
        break;
    }
    return str;
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
function getResult(num, n) {
	return Math.round(num * Math.pow(10, n)) / Math.pow(10, n)
}
function getresult(num, n) {
	return num.toString().replace(new RegExp("^(\\-?\\d*\\.?\\d{0," + n + "})(\\d*)$"), "$1") + 0
}
+function(n) {
    "use strict";
    var t = function(t, i) {
        i = i || "";
        var u = (n("<div class='weui-mask_transparent'><\/div>").appendTo(document.body),
        '<div class="weui-toast ' + i + '">' + t + "<\/div>")
          , r = n(u).appendTo(document.body);
        r.show();
        r.addClass("weui-toast--visible")
    }, i = function(t) {
        n(".weui-mask_transparent").remove();
        n(".weui-toast--visible").removeClass("weui-toast--visible").transitionEnd(function() {
            var i = n(this);
            i.remove();
            t && t(i)
        })
    }, r;
    n.toast = function(n, u, f) {
        "function" == typeof u && (f = u);
        var e, o = "weui-icon-success-no-circle", s = r.duration;
        "cancel" == u ? (e = "weui-toast_cancel",
        o = "weui-icon-cancel") : "forbidden" == u ? (e = "weui-toast--forbidden",
        o = "weui-icon-warn") : "text" == u ? e = "weui-toast--text" : "number" == typeof u && (s = u);
        t('<i class="' + o + ' weui-icon_toast"><\/i><p class="weui-toast_content">' + (n || "已经完成") + "<\/p>", e);
        setTimeout(function() {
            i(f)
        }, s)
    }
    ;
    n.showLoading = function(n) {
        var i = '<div class="weui_loading">';
        i += '<i class="weui-loading weui-icon_toast"><\/i>';
        i += "<\/div>";
        i += '<p class="weui-toast_content">' + (n || "数据加载中") + "<\/p>";
        t(i, "weui_loading_toast")
    }
    ;
    n.hideLoading = function() {
        i()
    }
    ;
    r = n.toast.prototype.defaults = {
        duration: 2500
    }
}($);

!function(n) {
    "use strict";
    n.fn.transitionEnd = function(n) {
        function r(f) {
            if (f.target === this)
                for (n.call(this, f),
                t = 0; t < i.length; t++)
                    u.off(i[t], r)
        }
        var t, i = ["webkitTransitionEnd", "transitionend", "oTransitionEnd", "MSTransitionEnd", "msTransitionEnd"], u = this;
        if (n)
            for (t = 0; t < i.length; t++)
                u.on(i[t], r);
        return this
    }
    ;
    n.support = function() {
        return {
            touch: !!("ontouchstart"in window || window.DocumentTouch && document instanceof window.DocumentTouch)
        }
    }();
    n.touchEvents = {
        start: n.support.touch ? "touchstart" : "mousedown",
        move: n.support.touch ? "touchmove" : "mousemove",
        end: n.support.touch ? "touchend" : "mouseup"
    };
    n.getTouchPosition = function(n) {
        return n = n.originalEvent || n,
        "touchstart" === n.type || "touchmove" === n.type || "touchend" === n.type ? {
            x: n.targetTouches[0].pageX,
            y: n.targetTouches[0].pageY
        } : {
            x: n.pageX,
            y: n.pageY
        }
    }
    ;
    n.fn.scrollHeight = function() {
        return this[0].scrollHeight
    }
    ;
    n.fn.transform = function(n) {
        for (var t, i = 0; i < this.length; i++)
            t = this[i].style,
            t.webkitTransform = t.MsTransform = t.msTransform = t.MozTransform = t.OTransform = t.transform = n;
        return this
    }
    ;
    n.fn.transition = function(n) {
        var i, t;
        for ("string" != typeof n && (n += "ms"),
        i = 0; i < this.length; i++)
            t = this[i].style,
            t.webkitTransitionDuration = t.MsTransitionDuration = t.msTransitionDuration = t.MozTransitionDuration = t.OTransitionDuration = t.transitionDuration = n;
        return this
    }
    ;
    n.getTranslate = function(n, t) {
        var r, f, i, u;
        return "undefined" == typeof t && (t = "x"),
        i = window.getComputedStyle(n, null),
        window.WebKitCSSMatrix ? u = new WebKitCSSMatrix("none" === i.webkitTransform ? "" : i.webkitTransform) : (u = i.MozTransform || i.OTransform || i.MsTransform || i.msTransform || i.transform || i.getPropertyValue("transform").replace("translate(", "matrix(1, 0, 0, 1,"),
        r = u.toString().split(",")),
        "x" === t && (f = window.WebKitCSSMatrix ? u.m41 : 16 === r.length ? parseFloat(r[12]) : parseFloat(r[4])),
        "y" === t && (f = window.WebKitCSSMatrix ? u.m42 : 16 === r.length ? parseFloat(r[13]) : parseFloat(r[5])),
        f || 0
    }
    ;
    n.requestAnimationFrame = function(n) {
        return window.requestAnimationFrame ? window.requestAnimationFrame(n) : window.webkitRequestAnimationFrame ? window.webkitRequestAnimationFrame(n) : window.mozRequestAnimationFrame ? window.mozRequestAnimationFrame(n) : window.setTimeout(n, 1e3 / 60)
    }
    ;
    n.cancelAnimationFrame = function(n) {
        return window.cancelAnimationFrame ? window.cancelAnimationFrame(n) : window.webkitCancelAnimationFrame ? window.webkitCancelAnimationFrame(n) : window.mozCancelAnimationFrame ? window.mozCancelAnimationFrame(n) : window.clearTimeout(n)
    }
    ;
    n.fn.join = function(n) {
        return this.toArray().join(n)
    }
}($);