function myready(){
	$(".kj_s",parent.document).html($(".kj_s").html());
	changeh(document.documentElement.scrollHeight+500);
	$('#date',parent.document).change(get).datepicker();    
	$("#game",parent.document).change(function(){get();});
	$('table.list tbody tr:not(.head)').hover(function() {
		$(this).addClass('hover');
	}, function() {
		$(this).removeClass('hover');
	});
}
function get(){
   var gid = $("#game",parent.document).val();
   var date= $("#date",parent.document).val();
  window.location.href = mulu + "longs.php?gid="+gid+"&date="+date;
}

