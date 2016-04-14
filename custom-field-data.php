<?php
//mysql_connect("115.249.28.89","mcube","rzNeyYWcEFnvZu3h");
mysql_connect("localhost","root","root");
mysql_select_db("m3");
$sql1 = "SELECT TABLE_NAME as tab FROM INFORMATION_SCHEMA.TABLES WHERE TABLES.TABLE_ROWS >0 AND TABLE_NAME LIKE '%_customfieldsvalue%'";
$rst1 = mysql_query($sql1);
$modules = array(
	"2"=>"employee",
	"3"=>"groups",
	"4"=>"ivrs",
	"5"=>"ivrs_options",
	"6"=>"callhistory",
	"16"=>"ivrshistory",
	"20"=>"pbx",
	"23"=>"contact",
	"24"=>"pbxreport",
	"25"=>"outboundcalls",
	"26"=>"leads",
	"28"=>"holiday",
	"29"=>"followup",
	"37"=>"leads_groups",
	"38"=>"support_groups",
	"40"=>"support_tickets");
$ids = array("2"=>"eid","3"=>"gid","4"=>"ivrsid","5"=>"optid","6"=>"callid","16"=>"hid","20"=>"pbxid", "23"=>"contid","24"=>"callid","25"=>"callid","26"=>"leadid","28"=>"id","29"=>"id","37"=>"gid","38"=>"gid","40"=>"tktid");
while ($rec1 = mysql_fetch_assoc($rst1)){
	$bid = str_replace("_customfieldsvalue","",$rec1['tab']);
	$sql2 = "SELECT modid FROM ".$bid."_customfields";
	$rst2 = mysql_query($sql2);
	while ($rec2 = mysql_fetch_assoc($rst2)){
		$modid = $rec2['modid'];
		if(!(@in_array($bid,array('1','47','257')) && ($modid == 6 || $modid == 26))){
			$table = $modules[$modid];
			// Custom fields
			$sql3 = "SELECT field_key,fieldtype FROM ".$bid."_customfields WHERE modid='".$modid."'";
			$rst3 = mysql_query($sql3);
			while ($rec3 = mysql_fetch_assoc($rst3)){
				$field_key = $rec3['field_key'];
				if($rec3['fieldtype'] == 'textarea'){
					$clength = " text ";
				}else{
					$clength = " VARCHAR(100) ";
				}
				if($table != ''){
					$sql4 = "ALTER TABLE ".$bid."_".$table." ADD ".$field_key.$clength;
					$rst4 = mysql_query($sql4);
					if($modid == 6){
						$sql5 = "ALTER TABLE ".$bid."_callarchive ADD ".$field_key.$clength;
						$rst5 = mysql_query($sql5);
					}
				}
			}
			// Custom values
			$sql6 = "SELECT DISTINCT(dataid) as dataid FROM ".$bid."_customfieldsvalue WHERE modid='".$modid."'";
			$rst6 = mysql_query($sql6);
			while ($rec6 = mysql_fetch_assoc($rst6)){
				if($modid == 6){
					 $sql7 = mysql_query("SELECT callid FROM ".$bid."_callhistory WHERE callid='".$rec6['dataid']."'");
					 if(mysql_num_rows($sql7)){
						 $update = "UPDATE ".$bid."_callhistory SET ";
					 }else{
						 $update = "UPDATE ".$bid."_callarchive SET ";
					 }
				}else{
					$update = "UPDATE ".$bid."_".$table." SET ";
				}
				$sql7 = "SELECT f.field_key,c.value FROM ".$bid."_customfieldsvalue c
						LEFT JOIN ".$bid."_customfields f ON c.fieldid=f.fieldid
						WHERE c.modid=".$modid." AND c.dataid='".$rec6['dataid']."'";
				$arr = array();
				$rst7 = mysql_query($sql7);
				if(mysql_num_rows($rst7)>0){
					while ($rec7 = mysql_fetch_assoc($values)){
						$arr[] = "`".$rec7['field_key']."`='" . $rec7['value']."'" ;
					}
					$update .= @implode("," ,$arr) . " WHERE ".$ids[$modid]."='".$rec6['dataid']."'";
					$result = mysql_query($update);
				}
			}
		}
	}
}
?>
