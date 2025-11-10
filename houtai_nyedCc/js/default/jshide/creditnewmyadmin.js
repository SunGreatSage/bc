function myready() {
	$(".page-tabs a").click(function() {
		var gid = $(this).attr('gid');
		$(".gametr").hide();
		$("tr.g" + gid).show();
		$(".page-tabs a").removeClass('hover');
		$(this).addClass("hover")
	});
	$(".page-tabs a:eq(0)").click();
}