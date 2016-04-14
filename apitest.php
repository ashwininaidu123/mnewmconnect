<form name="call" method="POST" action="">
<table align="center">
<tr><td>API Key</td><td>:</td><td><input type="text" name="url" value="<?=isset($_POST['url'])?$_POST['url']:''?>"></td></tr>
<tr><td>Data</td><td>:</td><td><textarea name="data"><?=isset($_POST['data'])?$_POST['data']:''?></textarea></td></tr>
<tr><td></td><td></td><td><input type="submit" name="submit" value="Submit"></td></tr>
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
$url = $_POST['url'];
$data.= "&data=".urlencode($_POST['data']);

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

}
?>

