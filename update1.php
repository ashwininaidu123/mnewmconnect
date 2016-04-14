<?php
mysql_connect("localhost","root","root");
//mysql_connect("192.168.1.51","mcube","XNns8dMb9GJ2xDMw");
//mysql_connect("192.168.75.3","root","581MprugU7!a42");
mysql_select_db("m3");
$rst = mysql_query("SELECT * FROM business");
if(mysql_num_rows($rst)>0){
	while($rec = mysql_fetch_assoc($rst)){
		$bid = $rec['bid'];	
		mysql_query("CREATE TABLE IF NOT EXISTS `".$bid."_lead_child` (
					  `bid` int(11) NOT NULL,
					  `leadid` int(11) NOT NULL,
					   UNIQUE KEY (`bid`, `leadid`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	}	
}
?>
