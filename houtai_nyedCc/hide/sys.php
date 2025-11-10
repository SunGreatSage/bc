<html>
<head>
<title></title>
<script language='javascript'> 
try {
	var href = top.window.location.href; /*top.window.location.href = 'http://www.baidu.com'; */
} catch (e) {
	try {
		if (parent.window.location.href.indexOf('?p') == -1) { 
		    /*parent.window.location.href = 'http://www.baidu.com'; */
		}
	} catch (e) { //window.location.href = 'http://www.baidu.com'; 	
	}

}</script>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
</head>
<frameset cols='120,*'>
	<frame src='left.php?xtype=this' style="\"height:105px;\"" frameborder="\"0\"" scrolling="\"no\"" name='left' id='left' noresize>
	<frame src='about:blank;' frameborder="\"0\"" name='right' id='right' scrolling="\"yes\"" noresize>
</frameset>
<noframes></noframes>
</html>