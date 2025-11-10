function myready() {
$("#ExportBetInfo").click(function() {
  var gid = $('#lotteryType').val();
  var qishu = $('#qishu').val();
  var agentId = $('#agentId').val(); // 添加agentId
  var s = $('#status').attr('s');
  if (gid == "") {
    alert("请选择彩种！");
    return false;
  } else {
    var url = "betinfo.php?xtype=downfast&gid=" + gid + "&qishu=" + qishu + "&agentId=" + agentId + "&s=" + s; 
    $('#downfastfrm').attr('src', url);
  }
});



$('#lotteryType').on('change', function() {
  var selectedValue = $(this).val();
  if (selectedValue === '') {
    alert('请选择彩种！');
    return;
  }
  $.post('betinfo.php', { xtype: 'period', gid: selectedValue }, function(data) {
    // 更新 #qishu 的内容
    $('#qishu').text(data.thisqishu + '期'); // Append "期" to the data

    // 更新 #status 的内容
    if (data.status == 0) {
      $('#status').text('未结算').css('color', 'red').attr('s', 0);
    } else if (data.status == 1) {
      $('#status').text('已结算').css('color', 'blue').attr('s', 1);
    }
  }, 'json');
});


}

//$("#downfastfrm").attr('src', "betinfo.php?xtype=downfast&qishu="+$("#qishu").val());