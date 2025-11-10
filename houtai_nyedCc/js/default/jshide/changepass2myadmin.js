function myready() {
	$("input.cz").click(function() {
	     $("input:password").val('');
	});
	$("input.btn1").click(function() {
		var pass0 = $("#oldPassword").val();
		var pass1 = $("#password").val();
		var pass2 = $("#ckPassword").val();
		if (pass0 == '') {
			$(".fter").html('请输入原始密码！');
			 $("input:reset").click();
			return false
		}
		if (strlen(pass1) < 6 | strlen(pass1) > 15 | pass1 != pass2) {
			$(".fter").html('密码必须由字母数字组成，6-20位（不区分大小写）!');
			 $("input:reset").click();
			return false
		}
		if (pass0 == pass1) {
			$(".fter").html('新旧密码不能一样！');
			 $("input:reset").click();
			return false
		}
		pass0= men_md5_password(pass0);
		pass1= men_md5_password(pass1);
		pass2= men_md5_password(pass2);
		var str = "&pass0=" + pass0 + "&pass1=" + pass1 + "&pass2=" + pass2;
		$.ajax({
			type: 'POST',
			url: mulu + 'changepass.php',
			data: 'xtype=changepass' + str,
			success: function(m) {
				m = Number(m);
				if (m == 1) {
					$(".fter").html('你输入的旧密码不正确!');
			         $("input:reset").click();
				} else if (m == 2) {
					alert("修改成功,请重新登陆");
					top.window.location.href = "/Home/Logout";
				}
			}
		})
	})
}
function strlen(sString) {
	var sStr, iCount, i, strTemp;
	iCount = 0;
	sStr = sString.split("");
	for (i = 0; i < sStr.length; i++) {
		strTemp = escape(sStr[i]);
		if (strTemp.indexOf("%u", 0) == -1) {
			iCount = iCount + 1
		} else {
			iCount = iCount + 2
		}
	}
	return iCount
}