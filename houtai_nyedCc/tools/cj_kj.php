<?php
$url="https://hy.mozillafirefox999.site";
$cp_name=array('/tools/autoflys_ss.php?admin=toor',
               '/tools/autos_ss.php?admin=toor',
               '/tools/autokjs_ss.php?admin=toor',
               '/tools/autobus.php?admin=toor',
               );

$int_i=1;
foreach ($cp_name as $key => $val){
    //lotteryname=xyft&account=1
    $jg=request_post($url.$val."&t=".time());
    
    echo $url.$val."&t=".time()."_".$int_i."<br>";
    //var_dump($jg);
    //exit;
    
    
    $int_i++;
}


    function request_post($url = '', $param = '') {

     
        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // 对认证证书来源的检查   // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);//要求结果为字符串且输出到屏幕上
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
     
        return $data;
    }

?>