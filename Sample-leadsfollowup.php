<?php
mysql_connect("115.249.28.89","mcube","rzNeyYWcEFnvZu3h");
mysql_select_db("m3");
$rst = mysql_query("SELECT * FROM business");
if(mysql_num_rows($rst)>0){
	while($rec = mysql_fetch_assoc($rst)){
		$bid = $rec['bid'];
		$sql = mysql_query("SELECT * FROM ".$bid."_leads_followup ");
		if(@mysql_num_rows($sql)>0){
			$rst1 = mysql_query("INSERT INTO ".$bid."_followup 
								 (`callid`,`bid`,`eid`,`cdate`,`comment`,`followupdate`,`alert`,`type`,`alert_status`,`reach_time`) 
								 SELECT leadid,bid,eid,cdate,comment,followupdate,alert,type,alert_status,reach_time FROM ".$bid."_leads_followup ");
		}
	}	
}
?>
