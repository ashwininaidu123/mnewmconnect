<?php
$to = urldecode(trim($_REQUEST['to']));
$content = urldecode($_REQUEST['text']);
//$type = isset($_REQUEST['type']) ? 1 : 0;

//if($type=='1'){
        $api = "http://180.179.200.180/getservice.php?from=vmc.in";
        $reply = $api."&to=".$to."&text=".urlencode($content);
//}else{
//        $api = "http://203.122.14.82/kannel/re.php?username=metrix&password=metrix@123";
//        $reply = $api."&to=".$to."&content=".urlencode($content);
//}
file( $reply);
$fp = fopen('smslog.txt','a+');
fwrite($fp,"\n[".date('Y-m-d H:i:s')."] ".$reply);
fclose($fp);

?>
