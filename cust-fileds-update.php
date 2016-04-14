<?php
mysql_connect("115.249.28.89","mcube","rzNeyYWcEFnvZu3h");
mysql_select_db("m3");
$modules = array("2"=>"employee","3"=>"groups","4"=>"ivrs","5"=>"ivrs_options","6"=>"callhistory","16"=>"ivrshistory","20"=>"pbx", "23"=>"contact","24"=>"pbxreport","25"=>"outboundcalls","26"=>"leads","28"=>"holiday","29"=>"followup","37"=>"leads_groups","38"=>"support_groups","40"=>"support_tickets");
$ids = array("2"=>"eid","3"=>"gid","4"=>"ivrsid","5"=>"optid","6"=>"callid","16"=>"hid","20"=>"pbxid", "23"=>"contid","24"=>"callid","25"=>"callid","26"=>"leadid","28"=>"id","29"=>"id","37"=>"gid","38"=>"gid","40"=>"tktid");
$rst = mysql_query("SELECT * FROM business WHERE bid");
if(mysql_num_rows($rst)>0){
	while($rec = mysql_fetch_assoc($rst)){
		$bid = $rec['bid'];
		$q = mysql_query("SELECT modid FROM ".$bid."_customfields");
		if(mysql_num_rows($q)>0){
			while($r = mysql_fetch_assoc($q)){
				$key = $r['modid'];
				$val = $modules[$key];
				if(!(@in_array($bid,array('1','47','257')) && ($key == 6 || $key == 26))){
					//Customfields 
					$rst = mysql_query("SELECT fieldid FROM ".$bid."_customfields WHERE modid='".$key."'");
					while ($rec = mysql_fetch_assoc($rst)){
						$field_key = "c_".$rec['fieldid'];
						if($val != ''){
							$sql = "UPDATE ".$bid."_customfields SET field_key='".$field_key."' WHERE fieldid='".$rec['fieldid']."'";
							$result = mysql_query($sql);
							$sql1 = "ALTER TABLE ".$bid."_".$val." ADD ".$field_key." VARCHAR(50)";
							$result = mysql_query($sql1);
							if($key == 6 && $result == 1){
								$sql2 = "ALTER TABLE ".$bid."_callarchive ADD ".$field_key." VARCHAR(50)";
								$result1 = mysql_query($sql2);
							}
						}
					}
					//
					$table = $val;
					$sql3 = "SELECT DISTINCT(dataid) as cid FROM ".$bid."_customfieldsvalue WHERE modid=".$key;
					$rst = mysql_query($sql3);
					while ($rec = mysql_fetch_assoc($rst)){
						if($modid == 6){
							 $sql = mysql_query("SELECT callid FROM ".$bid."_callhistory WHERE callid='".$modid."'");
							 if(mysql_num_rows($sql)){
								 $update = "UPDATE ".$bid."_callhistory SET ";
							 }else{
								 $update = "UPDATE ".$bid."_callarchive SET ";
							 }
						}else{
							$update = "UPDATE ".$bid."_".$table." SET ";
						}
						$sql = "SELECT * FROM ".$bid."_customfieldsvalue c
								LEFT JOIN ".$bid."_customfields f ON c.fieldid=f.fieldid
								WHERE c.modid=".$key." AND c.dataid='".$rec['cid']."'";
						$arr = array();
						$values = mysql_query($sql);
						if(mysql_num_rows($values)>0){
							while ($val = mysql_fetch_assoc($values)){
								$arr[] = "`".$val['field_key']."`='" . $val['value']."'" ;
							}
							$update .= implode("," ,$arr) . " WHERE ".$ids[$key]."='".$rec['cid']."'";
							$result = mysql_query($update);
						}
					}
				}
			}
		}
	}
}
