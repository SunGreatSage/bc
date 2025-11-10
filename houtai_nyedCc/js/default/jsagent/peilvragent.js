var page = 1;

function myready() {
	changeh(document.documentElement.scrollHeight + 500);
	$("#scroll_div2 a.g" + gid).addClass("hover");
	$("#scroll_div2 a").click(function() {
		window.location.href = "pset.php?xtype=show&gid=" + $(this).attr('gid')
	});
	$(".data_table td").mouseover(function() {
		$(this).parent().addClass("hover")
	}).mouseout(function() {
		$(this).parent().removeClass("hover")
	});
	var pcount = Number($(".page_info").attr('pcount'));
	page = Number($(".page_info").attr('page'));
	var pstr = "<li class='prev'><a class='current'>《上一页</a></li>";
	for (i = 1; i <= pcount; i++) {
		if (i == page) {
			pstr += "<li><a class='current'>" + i + "</li>"
		} else {
			pstr += "<li><a href='javascript:void(0)' class='p'>" + i + "<li>"
		}
	}
	pstr += "<li class='next'><a class='next'>下一页》</a></li>";
	$(".page_control").html(pstr);
	pstr = null;
	$(".page a").click(function() {
		if ($(this).hasClass('prev')) {
			page -= 1
		} else if ($(this).hasClass('next')) {
			page += 1
		} else {
			page = Number($(this).html())
		}
		if (page < 1) page = 1;
		if (page > pcount) page = pcount;
		geturl()
	})
}
function geturl() {
	window.location.href = "pset.php?xtype=show&gid=" + $(this).attr('gid') + "&page=" + page
}