<?
if(!in_array($_SERVER['REMOTE_ADDR'],array('182.72.110.206'))) header("Location: /");
mysql_connect("192.168.75.3","root","581MprugU7!a42");
//mysql_connect("localhost","root","root");
mysql_select_db("m3");
if(isset($_POST['submit'])){
	//print_r($_POST);
	$api = "http://180.179.200.180/getservice.php?from=vmc.in";
	$api .= "&to=".$_POST['to']."&text=".urlencode($_POST['text']);
	$ret = file_get_contents($api);
	$sql = "INSERT INTO exeSms SET
			`to`		= '".$_POST['to']."',
			`content`	= '".$_POST['text']."',
			`ret`		= '".$ret."'";
	mysql_query($sql) or die(mysql_error());

	header("Location:/sms.php");
}

$sql = "SELECT empname,contact FROM salesemp WHERE status=1 AND contact!=''";
$rst = mysql_query($sql);
$exes = array();
if(mysql_num_rows($rst)>0){
	while($rec = mysql_fetch_assoc($rst)){
		$exes[] = $rec;
	}
}
?>
<script src="/system/application/js/jquery-1.5.2.js" language="javascript" type="text/javascript" ></script>
<script src="/system/application/js/jquery.validate.js" language="javascript" type="text/javascript" ></script>
<script>
$(function() {
	jQuery.validator.addMethod("mobile", function(value, element) {
	return this.optional(element) || /^[7-9][0-9]{9}$/.test(value);
	}, "Should start with 7,8,9 and should have 10 digits");  

	$('#smsFrm').validate({
		rules:{
			to:{
				required:true,
				mobile:true
			},
			text:{
				required:true
			}
		},messages:{
			to:{
				required:"Mobile number is required"
			},
			text:{
				required:"Message is required"
			}
		},
		errorPlacement: function(error, element) {
			error.appendTo( element.parent().next() );
		}
	});
	$('#to').live('change',function(event){
		$('#exeNum').html($('#to').val());
		
	});	
});		
</script>
<form method="POST" id="smsFrm" action="sms.php">
<table align="center" border="0" cellpadding="10" width="50%">
<tr><td width="20%" align="right">To : </td>
	<td><div>
			<select name="to" id="to">
				<option>-- SELECT --</option>
			<?
				foreach($exes as $exe){
					?><option value="<?=$exe['contact']?>"><?=$exe['empname']?></option><?
				}
			?>
			</select>
		</div>
		<div></div>
	</td>
</tr>
<tr><td align="right">Number : </td><td><div id="exeNum"></div></td></tr>
<tr><td align="right">Message : </td><td><div><textarea rows="10" cols="30" name="text"></textarea></div><div></div></td></tr>
<tr><td></td><td><input type="submit" name="submit" value="Send" /></td></tr>
</table>
</form>
<style>
body{	background:#cccccc;padding:0px;margin:100px auto;}
table{	background:#FFFFFF;}
td{		vertical-align:top;}
.error{	border:1px solid #CC0000;}
label.error{border:none;color:#CC0000;}
textarea{width:250px;height:110px;}
</style>
