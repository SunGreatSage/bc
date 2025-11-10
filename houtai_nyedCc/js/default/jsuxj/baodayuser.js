var page=1;
var pcount = 1;
function myready(){
	 Dialog.Load("正在加载......");
	var loadingTimer = setTimeout(function () {
		Dialog.Close()
		}, 100);
$(".rcount0").html("此页面统计"+$('#betList tr').size()+"笔");
   page = Number($(".page_info").attr('tpage'));
   var rcount =Number($(".page_info").attr("rcount"));
   var psize = Number($(".page_info").attr("psize"));
   if(rcount==0){$("#betList").html("<tr class=''><td colspan='9' align='center'>暂无数据!</td></tr>");}
    if(psize>=rcount){
		$(".statistics").hide();
		console.log(psize);
	  }
   pcount = rcount%psize==0 ? rcount/psize : ((rcount-rcount%psize)/psize)+1;
   $(".page_count").html("共 "+pcount+" 页");
    var str='';
   for(i=1;i<=pcount;i++){ 
       str += "<li class='page-number current ";
	   if(i==page) str += "pgCurrent";
	   str += "'>"+i+"</li>";

   }

   $(".current").replaceWith(str);
   $(".page_info li").click(function(){
      if($(this).hasClass('next')){
	     page++;
	  }else if($(this).hasClass('previous')){
	     page--;
	  }else if($(this).hasClass('front')){
	     page=1;
	  }else if($(this).hasClass('last')){
	     page=pcount;
	  }else{
	     page= Number($(this).html());
	  }
	 
	  if(isNaN(page) | page<1 | page%1!=0 | page>pcount)  return false;
	  var date = $(".date").attr('date');
	 window.location.href = mulu + "baoday.php?xtype=show&tpage="+page+"&date="+date;
	  return false;
   });
   
   $(".list tr").hover(function(){$(this).find("td").addClass("hover")},function(){$(this).find("td").removeClass("hover")});
   $("td.result").each(function(){
       if(Number($(this).html())<0) {
		 $(this).css('color','red');
	    }else{
	  $(this).css('color','blue');
		}   
   });
   
}
    //获取get传值的方法
function getQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return decodeURI(r[2]);
        return null;
}