<?php
mysql_connect("192.168.75.3","root","581MprugU7!a42");
//mysql_connect("localhost","root","root");
mysql_select_db("m3");
$rst = mysql_query("SELECT * FROM business WHERE status = 1");
if(@mysql_num_rows($rst)>0){
	$fp =fopen("api.txt","a");
	while($rec = mysql_fetch_assoc($rst)){
		$bid = $rec['bid'];
		$rst1 = mysql_query("SELECT gid,oncallaction,onhangup FROM ".$bid."_groups WHERE oncallaction != '0' OR oncallaction != NULL OR onhangup != '0' OR onhangup != NULL");
		if(@mysql_num_rows($rst1)>0){
			while($rec1 = mysql_fetch_assoc($rst1)){      
				$gid = $rec1['gid'];
				$oncall = $rec1['oncallaction'];
				$onhangup = $rec1['onhangup'];
				$rst2 = mysql_query("SELECT callid FROM ".$bid."_callhistory WHERE gid='".$gid."' AND (starttime BETWEEN '2014-03-18 18:30:00' AND '2014-03-19 14:20:00')");
				if(@mysql_num_rows($rst2)>0){
					while($rec2 = mysql_fetch_assoc($rst2)){	
						$callid = $rec2['callid'];
						if($oncall != '' && $oncall != 0){
							$x = "http://mcube.vmc.in/process/callapi/".$bid."/".$callid."/0";
							//file_get_contents($x);
							fwrite($fp,"\n".$x);
						}
						if($onhangup != '' && $onhangup != 0){
							$x = "http://mcube.vmc.in/process/callapi/".$bid."/".$callid."/1";
							//file_get_contents($x);
							fwrite($fp,"\n".$x);
						}
					}
				}
			}
		}
	}
	fclose($fp);
}
?>

