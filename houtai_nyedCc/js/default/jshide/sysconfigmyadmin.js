function myready() {
		$.ajaxSetup({
        beforeSend:function (){
			 $(".infotb").mask("正在执行...");
		},
		complete:function(){
			$(".infotb").unmask("正在执行...");		
        },
      });
	  
      var agentstime = new Date(formattedDate());
      agentstime.setDate(agentstime.getDate() - agents);
      agentstime = agentstime.toISOString().substring(0, 10); 
	  
	 /* var memberstime = new Date(formattedDate());
      memberstime.setDate(memberstime.getDate() - members);
      memberstime = memberstime.toISOString().substring(0, 10); */
	  $(".agents").html(agentstime);
	/*  $(".members").html(memberstime);*/
	  $(".time").html(formattedDate());
	  
    $(".data_table tr").mouseover(function(){$(this).addClass('hover')}).mouseout(function(){$(this).removeClass('hover')});  
	$(".editall").click(function() {
		var passtime = $(".passtime").val();
		var livetime = $(".livetime").val();
		var maxpc = $(".maxpc").val();
		var tzjg = $(".tzjg").val();
		var supass= $(".supass").val();
		var reseted = $(".reseted").val();
		var editstart = $(".editstart").val();
		var editend = $(".editend").val();
		var moneytype = $(".moneytype").val();
		var comattpeilv = $("input.comattpeilv:checked").val();
		var flyflag = $("input.flyflag:checked").val();
		var autobaoma = $(".autobaoma").prop('checked') ? 1 : 0;
		var ifopen = $(".ifopen").val();
		var editzc = $("input.editzc:checked").val();
		var deluser = $("input.deluser:checked").val();
		var autoresetpl = $(".autoresetpl").val();
		//var autold = $(".autold").prop('checked') ? 1 : 0;
		var plresetfs = $(".plresetfs").val();
		var loginfenli = $("input.loginfenli:checked").val();
		//var zcmode = $(".zcmode").val();
		var plc = $(".plc").val();
		var minje = $(".minje").val();
		var merge = $("input.merge:checked").val();
		var baobf = $("input.baobf:checked").val();
		var agents = $(".agents").val();
		var members = $(".members").val();
		var webname = $(".webname").val();
		var panel = $("input.panel:checked").val();
		var iprecord = $("input.iprecord:checked").val();
	  // alert(merge);
		//var pk10num = $(".pk10num").val();
		//var pk10ts = $(".pk10ts").val();
		//var pk10niu = $(".pk10niu").prop('checked') ? 1 : 0;
        //var yingxz = $(".yingxz").val();
        //var yingxzje = $(".yingxzje").val();
		
        str = "&passtime="+passtime;
		str += "&livetime="+livetime;
		str += "&maxpc="+maxpc;
		str += "&tzjg="+tzjg;
		str += "&supass="+supass;
		str += "&reseted="+reseted;
		str += "&editstart="+editstart;
		str += "&editend="+editend;
		str += "&moneytype="+moneytype;
		str += "&comattpeilv="+comattpeilv;
		str += "&flyflag="+flyflag;
		str += "&autobaoma="+autobaoma;
		str += "&ifopen="+ifopen;
		str += "&editzc="+editzc;
		str += "&deluser="+deluser;
		str += "&autoresetpl="+autoresetpl;
		//str += "&autold="+autold;
		str += "&plresetfs="+plresetfs;
		str += "&loginfenli="+loginfenli;
		//str += "&zcmode="+zcmode;
		str += "&plc="+plc;
		str += "&minje="+minje;
		str += "&merge="+merge;
		str += "&baobf="+baobf;
		str += "&agents="+agents;
		str += "&members="+members;
		str += "&webname="+webname;
		str += "&panel="+panel;
		str += "&iprecord="+iprecord;
		//str += "&pk10num="+pk10num;
		//str += "&pk10ts="+pk10ts;
		//str += "&pk10niu="+pk10niu;
       // str += "&yingxz="+yingxz;
        //str += "&yingxzje="+yingxzje;
		if(!confirm("是否保存系统设置"))	return false;
        var pass = prompt("请输入密码:", '');
        str += "&pass="+pass;
		
		$.ajax({
			type: 'POST',
			url: mulu + 'sysconfig.php',
			data: 'xtype=setsys' + str,
			success: function(m) {			
				if (Number(m) == 1) {
					alert('系统设置保存成功！');
			//		window.location.href = window.location.href
				}
				if (Number(m) == 2) {
					alert("密码错误!");
				}
			}
		})
	});
   
	$(".czmoneypass").click(function(){
		$.ajax({
			type: 'POST',
			url: mulu + 'suser.php',
			data: 'xtype=rmoneypass',
			success: function(m) {			
				if (Number(m) == 1) {
					alert('重置转账密码成功！');
				}
				if (Number(m) == 2) {
					alert("密码错误!");
				}
			}
		})
	});
}
function formattedDate() {
var date = new Date();
var nowMonth = date.getMonth() + 1;
var strDate = date.getDate();
var seperator = "-";
if (nowMonth >= 1 && nowMonth <= 9) {
   nowMonth = "0" + nowMonth;
}
if (strDate >= 0 && strDate <= 9) {
   strDate = "0" + strDate;
}
var nowDate = date.getFullYear() + seperator + nowMonth + seperator + strDate;
  return nowDate;
}