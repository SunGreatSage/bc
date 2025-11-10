// JavaScript Document

function myready() {
    $(".menu li a").click(function () {
        var url = $(this).attr('x');
		if(url=='' | url==undefined) return;
        if (url == 'user') {
            parent.right.window.location.href = url + ".php?xtype=show&layer=" + $(this).attr('u');
        } else if (url == 'top') {
            parent.right.window.location.href = url + ".php?logout=yes";
        } else if (url == 'admins') {
            parent.right.window.location.href = url + ".php?xtype=list";
        } else if (url == 'class') {
            parent.right.window.location.href = url + ".php?xtype=bigclass";
        } else if (url == 'setatt') {
            parent.right.window.location.href = "zshui.php?xtype=setattshow";
        } else {
            parent.right.window.location.href = url + ".php?xtype=show";
        }
		return false;
    });
}