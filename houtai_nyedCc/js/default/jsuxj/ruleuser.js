function myready(){
	$(".game").change(function(){
	   var gid = $(this).val();
	  window.location.href = mulu + "rule.php?gid="+gid;
	});
}

