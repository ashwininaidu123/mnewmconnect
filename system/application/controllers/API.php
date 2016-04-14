<style>
body{
	width:100%;
	padding:0px;
	margin:0 auto;
	background:grey;
}
td{
	 background:#FFF;
	 vertical-align:top;
	 align:left;
}
select,input[type=text]{
	width:250px;
	font-size:14px;
	height:30px;
}
label{
	width:30%;
	float:left;
}
</style>
<html>
<head>
<title>Mob App API Test Page</title>
</head>
<body>
<?
//$url = "http://mcube.vmctechnologies.com/";
//$url = "http://mcube.vmc.in/";
$url = "http://localhost/mconnect/";
//$url = "http://localhost/mcube-new/";
?>
	
<table width="100%" cellspacing="1" cellpadding="0">
	<tr>
	<td width="75%">
	<div  style="width:100%;height:650px;overflow-y: auto;">
<?

function createFeild($field){
	$ret = "";
	switch($field['type']){
		case 'hidden':
			$ret = '<label>'.$field['name'].'</label> : <input type="text" name="'.$field['name'].'" value="'.$field['value'].'">';
		break;
		case 'label':
			$ret = '<label>'.$field['label'].'</label> : '.$field['value'];
		break;
		case 'text':
			$ret = '<label>'.$field['label'].'</label> : <input type="text" name="'.$field['name'].'" value="'.$field['value'].'">';
		break;
		case 'textarea':
			$ret = '<label>'.$field['label'].'</label> : <textarea rows="10" cols="50" name="'.$field['name'].'">'.$field['value'].'</textarea>';
		break;
		case 'checkbox':
			$ret = '<label>'.$field['label'].'</label> : ';
			foreach($field['options'] as $k => $v)
				$ret.= '<input type="checkbox" name="'.$field['name'].'" value="'.$k.'"> '.$v;
		break;
		case 'radio':
			$ret = '<label>'.$field['label'].'</label> : ';
			foreach($field['options'] as $k => $v)
				$ret.= '<input type="radio" name="'.$field['name'].'" value="'.$k.'"> '.$v;
		break;
		case 'dropdown':
			$ret = '<label>'.$field['label'].'</label> : ';
			$ret.= '<select name="'.$field['name'].'">';
			foreach($field['options'] as $k => $v)
				$ret.= '<option value="'.$k.'" '.(($field['value']==$k) ? 'selected="Selected"' : '').'> '.$v.'</option>';
			$ret.= '</select>';
		break;
	}
	return $ret;
}

if(isset($_POST['submit'])){
	$req = $_POST['req'];
	$purl = $_POST['api'];
	unset($_POST['submit']);
	unset($_POST['req']);
	unset($_POST['api']);
	//echo "<pre>";print_r($_POST);//exit;
	$data = "";
	$data1 = "";
	foreach($_POST as $k => $v){
		if($k=='custom'){
			foreach($_POST['custom'] as $fid=>$val){
				$data .='&custom['.$fid.']='. (is_array($val) ? implode(",",$val) : $val);
				$data1 .='<br>custom['.$fid.']='. (is_array($val) ? implode(",",$val) : $val);
			}
		}else{
			$data .='&'.$k.'='.$v;
			$data1 .="<br>".$k.'='.$v;
		}
		
	}
	echo '<b>API:</b>'.$purl.'<br><b>Post Data:</b>'.$data1;
	$objURL = curl_init($purl);
	curl_setopt($objURL, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($objURL,CURLOPT_POST,1);
	curl_setopt($objURL, CURLOPT_POSTFIELDS,$data);
	$retval1 = curl_exec($objURL);
	$retval = (array)json_decode(trim($retval1));
	//$retval = trim(curl_exec($objURL));
	curl_close($objURL);
	echo "<br>";
	//~ echo "Output:<br>";
	//~ echo "<pre>";
	//~ print_r($retval);
	//~ echo "</pre>";
	//~ exit;
	switch($req){
		case 1:
	?>
		<table width="100%" align="left" border="1">
			<tr><td>Code</td><td><?=$retval['code']?></td></tr>
			<tr><td>Msg</td><td><?=$retval['msg']?></td></tr>
			<tr><td>Business Name</td><td><?=$retval['businessName']?></td></tr>
			<tr><td>Emp Email</td><td><?=$retval['empEmail']?></td></tr>
			<tr><td>Auth Key</td><td><?=$retval['authKey']?></td></tr>
			<tr><td>Emp Name</td><td><?=$retval['empName']?></td></tr>
			<tr><td>Emp Contact</td><td><?=$retval['empContact']?></td></tr>
		</table>
	<?
		break;
		case 2:
	?>
		<table width="100%" align="left" border="1">
			<tr><td>Code</td><td><?=$retval['code']?></td></tr>
			<tr><td>Msg</td><td><?=$retval['msg']?></td></tr>
			<tr><td>Total Records</td><td><?=$retval['count']?></td></tr>
			<tr><td>Groups</td><td>
				<div  style="width:100%;height:200px;overflow-y: auto;">
				<table width="100%" align="left" border="1">
					
			<?
				$i = 1;
					foreach($retval['groups'] as $r){
						$r = (array)$r;
						//print_r($r);
						if($i=='1'){
							$keys = array_keys($r);
							echo "<tr>";
							echo "<td>SL.No.</td>";
							foreach ($keys as $key) echo "<td>".$key."</td>";
							echo "</tr>\n";
						}
						echo "<tr><td>". $i ."</td>";
						foreach($r as $d) echo "<td>".$d."</td>";
						echo "</tr>\n";
						$i++;
					}
			?>
				</table>
				</div>
			</td></tr>
			<tr><td>Records</td><td>
				<div  style="width:100%;height:300px;overflow: auto;">
				<table width="100%" align="left" border="1">
				<?	
					$ofset = $_POST['ofset'];
					$i = 1;
					foreach($retval['records'] as $r){
						$r = (array)$r;
						//print_r($r);
						if($i=='1'){
							$keys = array_keys($r);
							echo "<tr>";
							echo "<td>SL.No.</td>";
							foreach ($keys as $key) echo "<td>".$key."</td>";
							echo "</tr>\n";
						}
						echo "<tr><td>". ($ofset+$i) ."</td>";
						foreach($r as $d) echo "<td>".$d."</td>";
						echo "</tr>\n";
						$i++;
					}
				?>
				</table>
				</div>
			</td></tr>
		</table>
	<?
		break;
		case 3:
		$api = $url.'mobapp/postDetail';
	?>
		<form name="getDetail" action="" method="POST">
		<input type="hidden" name="api" value="<?=$api?>">
		<input type="hidden" name="req" value="4">
		<table width="100%" align="left" border="1">
			<tr><td>Code</td><td><?=$retval['code']?></td></tr>
			<tr><td>Msg</td><td><?=$retval['msg']?></td></tr>
			<tr><td>Fields</td><td>
				<div  style="width:100%;height:450px;overflow: auto;">
				<table width="100%" align="left" border="1">
					<tr><td><label>Auth Key</label> : <input type="text" name="authKey" value="<?=isset($_POST['authKey'])?$_POST['authKey']:''?>"></td></tr>
					<tr><td><label>Type</label> : <input type="text" name="type" value="<?=isset($_POST['type'])?$_POST['type']:''?>"></td></tr>
					<tr><td><label>Group Name</label> : <input type="text" name="groupname" value="<?=isset($_POST['groupname'])?$_POST['groupname']:''?>"></td></tr>
			
					
			<?
					foreach($retval['fields'] as $f){
						$f = (array)$f;
						//echo "<pre>";print_r($f);echo "</pre>";
						echo "<tr><td>".createFeild($f)."</td></tr>\n";
						$i++;
					}
			?>
					<tr><td align="center"><input type="submit" name="submit"></td></tr>
				</table>
				</div>
			</td></tr>
		</table>
		</form>
	<?
		break;
		case 4:
			//~ echo "<pre>";
			//~ print_r($retval);
			//~ echo "</pre>";
			
	?>
		<table width="100%" align="left" border="1">
			<tr><td>Code</td><td><?=$retval['code']?></td></tr>
			<tr><td>Msg</td><td><?=$retval['msg']?></td></tr>
		</table>
	<?
		break;
	}

} ?>
	</div>
	</td>	
	<td width="25%">
	<div  style="width:100%;height:650px;overflow-y: auto;">
	<table width="100%" border='1' cellspacing="0" cellpadding="2">
<!-- Login API -->
	<tr><td width="100%">
	<? $api = $url.'mobapp/checkAuth'; ?>
	<b>Authentication API:</b><br><?=$api?>
	<form name="checkAuth" action="" method="POST">
	<input type="hidden" name="api" value="<?=$api?>">
	<input type="hidden" name="req" value="1">
	<table>
		<tr><td><b>Email :</b><br><input type="text" name="email" value="<?=isset($_POST['email'])?$_POST['email']:''?>"></td></tr>
		<tr><td><b>Password :</b><br><input type="text" name="password" value="<?=isset($_POST['password'])?$_POST['password']:''?>"></td></tr>
		<tr><td align="center"><input type="submit" name="submit"></td></tr>
	</table>
	</form>
	</td></tr>
<!-- List API -->
	<tr><td width="100%">
	<? $api = $url.'mobapp/getList'; ?>
	<b>Get List API :</b><br><?=$api?>
	<form name="getList" action="" method="POST">
	<input type="hidden" name="api" value="<?=$api?>">
	<input type="hidden" name="req" value="2">
	<table>
		<tr><td><b>Auth Key :</b><br><input type="text" name="authKey" value="<?=isset($_POST['authKey'])?$_POST['authKey']:''?>"></td></tr>
		<tr><td><b>Type :</b><br>
		<select name="type">
			<option value="track" <?=((isset($_POST['type']) && $_POST['type']=='track') ? 'Selected="Selected"' : '')?>>track</option>
			<option value="ivrs" <?=((isset($_POST['type']) && $_POST['type']=='ivrs') ? 'Selected="Selected"' : '')?>>ivrs</option>
			<option value="x" <?=((isset($_POST['type']) && $_POST['type']=='x') ? 'Selected="Selected"' : '')?>>x</option>
			<option value="lead" <?=((isset($_POST['type']) && $_POST['type']=='lead') ? 'Selected="Selected"' : '')?>>lead</option>
			<option value="followup" <?=((isset($_POST['type']) && $_POST['type']=='followup') ? 'Selected="Selected"' : '')?>>followup</option>
		</select>
		<tr><td><b>Ofset :</b><br><input type="text" name="ofset" value="<?=isset($_POST['ofset'])?$_POST['ofset']:'0'?>"></td></tr>
		<tr><td><b>Limit :</b><br><input type="text" name="limit" value="<?=isset($_POST['limit'])?$_POST['limit']:'10'?>"></td></tr>
		<tr><td><b>Gid :</b><br><input type="text" name="gid" value="<?=isset($_POST['gid'])?$_POST['gid']:'0'?>"></td></tr>
		<tr><td align="center"><input type="submit" name="submit"></td></tr>
	</table>
	</form>
	</td></tr>
<!-- Detail Page API -->
	<tr><td width="100%">
	<? $api = $url.'mobapp/getDetail'; ?>
	<b>Get List API :</b><br><?=$api?>
	<form name="getDetail" action="" method="POST">
	<input type="hidden" name="api" value="<?=$api?>">
	<input type="hidden" name="req" value="3">
	<table>
		<tr><td><b>Auth Key :</b><br><input type="text" name="authKey" value="<?=isset($_POST['authKey'])?$_POST['authKey']:''?>"></td></tr>
		<tr><td><b>Type :</b><br>
		<select name="type">
			<option value="track" <?=((isset($_POST['type']) && $_POST['type']=='track') ? 'Selected="Selected"' : '')?>>track</option>
			<option value="ivrs" <?=((isset($_POST['type']) && $_POST['type']=='ivrs') ? 'Selected="Selected"' : '')?>>ivrs</option>
			<option value="x" <?=((isset($_POST['type']) && $_POST['type']=='x') ? 'Selected="Selected"' : '')?>>x</option>
			<option value="lead" <?=((isset($_POST['type']) && $_POST['type']=='lead') ? 'Selected="Selected"' : '')?>>lead</option>
			<option value="followup" <?=((isset($_POST['type']) && $_POST['type']=='followup') ? 'Selected="Selected"' : '')?>>followup</option>
		</select>
		<tr><td><b>Callid :</b><br><input type="text" name="callid" value="<?=isset($_POST['callid'])?$_POST['callid']:''?>"></td></tr>
		<tr><td><b>Group Name :</b><br><input type="text" name="groupname" value="<?=isset($_POST['groupname'])?$_POST['groupname']:''?>"></td></tr>
		<tr><td align="center"><input type="submit" name="submit"></td></tr>
	</table>
	</form>
	</td></tr>
	
	</table>
	</div>
	</td>
	</tr>
</table>
</body>
</html>
