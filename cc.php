<form name="call" method="POST" action="">
<table align="center">
<tr><td>API Key</td><td>:</td><td><input type="text" name="apikey" value="<?=isset($_POST['apikey'])?$_POST['apikey']:''?>"></td></tr>
<tr><td>Executive Number</td><td>:</td><td><input type="text" name="exenumber" value="<?=isset($_POST['exenumber'])?$_POST['exenumber']:''?>"></td></tr>
<tr><td>Customer Number</td><td>:</td><td><input type="text" name="custnumber"  value="<?=isset($_POST['custnumber'])?$_POST['custnumber']:''?>"></td></tr>
<tr><td>Referance ID</td><td>:</td><td><input type="text" name="refid"></td></tr>
<tr><td>Callback URL</td><td>:</td><td><input type="text" name="url" value="<?=isset($_POST['url'])?$_POST['url']:''?>"></td></tr>
<tr><td></td><td></td><td><input type="submit" name="submit" value="Connect"></td></tr>
</table>
</form>
<?
if(isset($_POST['submit'])){
/*$api = "http://mcube.vmc.in/api/outboundcall?
 * apikey=72c0b8bc268540b1e30dcc4cf938aa7d
 * &exenumber=9224194067
 * &custnumber=9920331415
 * &refid=5
 * &url=".urlencode("http://eurekasolutions.in/smscall/callstatus.asp");*/
$url = "http://mcube.vmc.in/api/outboundcall?";;
$data.= "&apikey=".$_POST['apikey'];
$data.= "&exenumber=".$_POST['exenumber'];
$data.= "&custnumber=".$_POST['custnumber'];
$data.= "&refid=".$_POST['refid'];
$data.= "&url=".urlencode($_POST['url']);

echo "<pre>";
echo $api;
echo "<br><br><br>";
$objURL = curl_init($url);
curl_setopt($objURL, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($objURL,CURLOPT_POST,1);
curl_setopt($objURL, CURLOPT_POSTFIELDS,$data);
//$retval = (array)json_decode(trim(curl_exec($objURL)));
$retval = trim(curl_exec($objURL));
curl_close($objURL);
print_r($retval);
echo "</pre>";

echo '<a href="ccr.php?apikey='.$_POST['apikey'].'&refid='.$_POST['refid'].'">Click To response</a>';

}
?>

