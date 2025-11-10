<?php
class Json{

public static function encode($str){

$code = json_encode($str);

return preg_replace("#\\\u([0-9a-f]+)#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))", $code);

}



public static function decode($str){

return json_decode($str);

}

}
?>
