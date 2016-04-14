<?php 
mysql_connect("115.249.28.89","mcube","rzNeyYWcEFnvZu3h");
mysql_select_db("m3");
$rst = mysql_query("SELECT * FROM business WHERE bid ");
if(mysql_num_rows($rst)>0){
	while($rec = mysql_fetch_assoc($rst)){
		$bid = $rec['bid'];
		$sql = "CREATE TABLE IF NOT EXISTS `".$bid."_ivrs` (
					  `ivrsid` int(11) NOT NULL AUTO_INCREMENT,
					  `bid` int(11) NOT NULL,
					  `title` varchar(100) NOT NULL,
					  `prinumber` varchar(10) NOT NULL,
					  `timeout` int(11) NOT NULL,
					  `status` tinyint(1) NOT NULL DEFAULT '1',
					  `api` text NOT NULL,
					  PRIMARY KEY (`ivrsid`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
		mysql_query($sql);
		$dat = mysql_query("INSERT INTO ".$bid."_ivrs 
								 SELECT * FROM ivrs WHERE bid='".$bid."'");
		$sql1 = "CREATE TABLE IF NOT EXISTS `".$bid."_ivrs_options` (
					  `optid` int(11) NOT NULL AUTO_INCREMENT,
					  `bid` int(11) NOT NULL,
					  `ivrsid` int(11) NOT NULL,
					  `parentopt` int(11) NOT NULL DEFAULT '0',
					  `optorder` int(11) NOT NULL,
					  `opttext` varchar(100) NOT NULL,
					  `optsound` varchar(100) NOT NULL,
					  `targettype` varchar(50) NOT NULL,
					  `targeteid` int(11) NOT NULL,
					  `api_url` text NOT NULL,
					  `sms_text` text NOT NULL,
					  PRIMARY KEY (`optid`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
		mysql_query($sql1);
		$dat1 = mysql_query("INSERT INTO ".$bid."_ivrs_options
								 SELECT * FROM ivrs_options WHERE bid='".$bid."'");
	}
	
}
?>
