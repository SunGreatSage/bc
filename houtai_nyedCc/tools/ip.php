<?php
function Rand_IP(){

 $ip2id= round(rand(600000, 2550000) / 10000); //第一种方法，直接生成

 $ip3id= round(rand(600000, 2550000) / 10000);

 $ip4id= round(rand(600000, 2550000) / 10000);

 //下面是第二种方法，在以下数据中随机抽取

 $arr_1 = array("218","218","66","66","218","218","60","60","202","204","66","66","66","59","61","60","222","221","66","59","60","60","66","218","218","62","63","64","66","66","122","211");

 $randarr= mt_rand(0,count($arr_1)-1);

 $ip1id = $arr_1[$randarr];

 return $ip1id.".".$ip2id.".".$ip3id.".".$ip4id;

}

//抓取页面内容

function Curl($url){

  $ch2 = curl_init();

  $user_agent = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.66 Safari/537.36";//模拟windows用户正常访问

  curl_setopt($ch2, CURLOPT_URL, $url);

  curl_setopt($ch2, CURLOPT_TIMEOUT, 10);

  curl_setopt($ch2, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:'.Rand_IP(), 'CLIENT-IP:'.Rand_IP()));

//追踪返回302状态码，继续抓取

  curl_setopt($ch2, CURLOPT_HEADER, true); 

  curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true); 

  curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);

  curl_setopt($ch2, CURLOPT_NOBODY, false);

  curl_setopt($ch2, CURLOPT_REFERER, 'http://www.baidu.com/');//模拟来路

  curl_setopt($ch2, CURLOPT_USERAGENT, $user_agent);

  $temp = curl_exec($ch2);

  curl_close($ch2);

  return $temp;

}

$postData = array(

 "user" => "root",

 "pwd" => "123456"

);

echo $ip = Rand_IP();
echo "<BR>";
$headerIp = []

 'CLIENT-IP:'.$ip,

 'X-FORWARDED-FOR:'.$ip,

);

$refer = 'http://www.baidu.com';

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'http://ip138.com');

//伪造来源refer

curl_setopt($ch, CURLOPT_REFERER, $refer);

//伪造来源ip

curl_setopt($ch, CURLOPT_HTTPHEADER, $headerIp);

//提交post传参

curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

//...各种curl属性参数设置

echo $out_put = curl_exec($ch);

curl_close($ch);

//var_dump($out_put);
//

function aaa($arr=["bb"=>1,"cc"=>2]){
    print_r($arr);
}
aaa();
aaa(["bb"=>5,"cc"=>6]);

function CURL($arr=["cookietype"=>false,"cookie_jar"=>"","url"=>"","posttype"=>false,"postdata"=>[],"head"=>true,"location"=>true,"refer"=>"","headip"=>"127.0.0.1","sslhostflag"=>true,"json"=>false]){
    $url =str_replace('://', '---', $arr["url"]);
    $url =str_replace('//', '/', $url);
    $url =str_replace('---', '://', $url);
    //echo $url;
    $SSL = substr($url, 0, 8) == "https://" ? true : false;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if ($SSL) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $arr["sslhostflag"]);
    }
    if ($arr["head"]) {
        curl_setopt($ch, CURLOPT_HEADER, true);
    }
     if($arr["refer"]!=""){
        curl_setopt($ch, CURLOPT_REFERER, $arr["refer"]);
    }
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $arr["location"]);
    if ($arr["cookietype"]) {
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_jar);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar);
        
    }
    $headers = ['CLIENT-IP:'.$headip,'X-FORWARDED-FOR:'.$headip];
    if ($arr["posttype"]) {
        if($arr["json"]){
            $post = json_encode($post);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            $headers[] = 'Content-Type: application/json; charset=utf-8';
            $headers[] = 'Content-Length: ' . strlen($post);
        }else{
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    $info = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    if(curl_error($ch)){
        echo curl_error($ch);
    }
    curl_close($ch);
    return ['res' => $result, 'location' => $info];
}