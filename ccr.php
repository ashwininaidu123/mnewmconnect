<?php
error_reporting(E_ALL);
ini_set("dispaly_errors",1);
$apikey = $_GET['apikey'];
$refid = $_GET['refid'];

mysql_connect("192.168.75.3","root","581MprugU7!a42");
mysql_select_db("m3");

$sql = "SELECT * FROM business WHERE apisecret='".$apikey."'";
$rst = mysql_query($sql);

$rec = mysql_fetch_assoc($rst);

$bid = $rec['bid'];

$sql = "SELECT * FROM ".$bid."_outboundcalls where dataid='".$refid."'";
$rst = mysql_query($sql);
$itemDetail = mysql_fetch_assoc($rst);

$url=$itemDetail['url'];
if($url=='') return 0;
$status=array(
	"1"=>"Originate",
	"2"=>"Executive Busy",
	"3"=>"Customer Busy",
	"4"=>"Call Complete",
	"5"=>"Insufficient Balance"
);

$data = array(
	'callid' =>  $itemDetail['callid']
	,'executive' =>  $itemDetail['executive']
	,'customer' =>  $itemDetail['customer']
	,'starttime' =>  $itemDetail['starttime']
	,'endtime' =>  $itemDetail['endtime']
	,'refid' =>  $itemDetail['dataid']
	,'pulse' =>  $itemDetail['pulse']
	,'filename' => $itemDetail['filename']
	,'status' =>  $status[$itemDetail['status']]
);
$data1 = urlencode(json_encode($data));
$data = '&data='.$data1;
$objURL = curl_init($url);
curl_setopt($objURL, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($objURL,CURLOPT_POST,1);
curl_setopt($objURL, CURLOPT_POSTFIELDS,$data);
$out = trim(curl_exec($objURL));
//$retval = (array)json_decode($out);
curl_close($objURL);
//print_r($retval);
//$ret = serialize($retval);


echo "<pre>";
echo "<br> Callback URL : " . $url;
echo "<br> Genareted String : " . $data1;
echo "<br> Our Output: <br>";
echo "After URL Decode: <br>";
$jd = urldecode($data1);
echo $jd;
echo "<br> After Json Decode : <br>";
$obj = json_decode($jd);
print_r($obj);
echo "<br> Output as Array :<br>";
$arr = (array)$obj;
print_r($arr);
echo "<br>Returned Output form callback URL:<br> " ;
print_r($out);
?>
