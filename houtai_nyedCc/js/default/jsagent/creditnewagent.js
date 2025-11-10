function myready() {
	$(".page-tabs a").click(function() {
		var gid = $(this).attr('gid');
		$(".gametr").hide();
		$("tr.g" + gid).show();
		$(".page-tabs a").removeClass('hover');
		$(this).addClass("hover")
	});
	$(".page-tabs a:eq(0)").click();
	$(".editpan").click(function() {
		var pan = $(".add_tb .pan").val();
		$.ajax({
			type: 'POST',
			url: mulu + 'account.php',
			data: "xtype=changepan&pan=" + pan,
			success: function(m) {
				if (Number(m) == 1) {
					alert("ok")
				}
			}
		});
		return false
	});
	$(".editgid").click(function() {
		var gid = $("select.gametype").val();
		$.ajax({
			type: 'POST',
			url: mulu + 'account.php',
			data: "xtype=changegid&gid=" + gid,
			success: function(m) {
				if (Number(m) == 1) {
					alert("ok")
				}
			}
		});
		return false
	})
}