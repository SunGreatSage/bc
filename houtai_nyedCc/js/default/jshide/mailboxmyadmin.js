function myready(){
	$.ajaxSetup({
        beforeSend:function (){
			 $(".data_table").mask("正在执行...");
		},
		complete:function(){
			$(".data_table").unmask("正在执行...");		
        },
      });
	  
 $('#smtpbtn').on('click', function() {
  let selectedGids = [];
  $('input[name="gamemail"]:checked').each(function() {
    selectedGids.push($(this).val());
  });

  const senderEmail = $('#sender').val();
  const recipientEmail = $('#recipient').val();
  if (hasRestrictedEmailSuffix(senderEmail) || hasRestrictedEmailSuffix(recipientEmail)) {
    alertMessage('禁止使用 @126.com, @163.com, @sina.com 等后缀的国内邮箱地址');
    return;
  }

  let data = {
    mailbox: $('input[name="mailbox"]:checked').val(),
	unsettlement : $('input[name="unsettlement"]:checked').val(),
	settled : $('input[name="settled"]:checked').val(),
    smtpserver: $('#smtpserver').val(),
    smtpusername: $('#smtpusername').val(),
    smtppassword: $('#smtppassword').val(),
    smtppost: $('#smtppost').val(),
    smtpencryption: $('input[name="smtpencryption"]:checked').val(),
    sender: senderEmail,
    recipient: recipientEmail,
	utime: $('#utime').val(),
	stime: $('#stime').val(),
	sendername: $('#sendername').val(),
	recipientname: $('#recipientname').val(),
	smtptitle: $('#smtptitle').val(),
	smtpcontent: $('#smtpcontent').val(),
    gid: selectedGids.join(',')
  };

  $.ajax({
    url: 'mailbox.php?xtype=smtpupdate',
    type: 'POST',
    data: data,
    success: function(m) {
      if (Number(m) == 1) {
        alert('邮箱设置保存成功！');
        // window.location.href = window.location.href
      }
    }
  });
});
}

function hasRestrictedEmailSuffix(email) {
  const restrictedSuffixes = [
    "@126.com", "@163.com", "@sina.com", "@21cn.com", "@sohu.com", "@yahoo.com.cn",
    "@tom.com", "@qq.com", "@etang.com", "@eyou.com", "@56.com", "@x.cn",
    "@chinaren.com", "@sogou.com", "@citiz.com"
  ];

  return restrictedSuffixes.some(suffix => email.endsWith(suffix));
}