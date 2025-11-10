function myready(){
	 Dialog.Load("正在加载......");
	var loadingTimer = setTimeout(function () {
		Dialog.Close()
		}, 100);
   $(".list tr").hover(function(){$(this).find("td").addClass("hover")},function(){$(this).find("td").removeClass("hover")});
   $(".result").each(function(){
       if(Number($(this).html())<0) $(this).css('color','red');
   });
   $(".list tbody tr a").click(function(){
       var date = $(this).parent().parent().attr('date');
	  window.location.href = mulu + "baoday.php?xtype=show&tpage=1&date="+date;
   });
}