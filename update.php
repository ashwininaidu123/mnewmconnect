<?php
//mysql_connect("localhost","root","root");
mysql_connect("115.249.28.89","mcube","rzNeyYWcEFnvZu3h");
mysql_select_db("m3");
$rst = mysql_query("SELECT * FROM business");
if(mysql_num_rows($rst)>0){
	while($rec = mysql_fetch_assoc($rst)){
		$bid = $rec['bid'];	
    
	}
   ALTER TABLE  `feedbackmconnect` ADD  `siteid` INT NOT NULL AFTER  `authKey` ;
   ALTER TABLE  `1_site` ADD  `site_employee` VARCHAR( 100 ) NOT NULL AFTER  `siteicon` ;

CREATE TABLE IF NOT EXISTS `'$bid'_site_emp` (
  `bid` int(11) NOT NULL,
  `siteid` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `empnumber` varchar(15) NOT NULL,
  `callcounter` int(11) NOT NULL,
  `starttime` time NOT NULL DEFAULT '00:00:00',
  `endtime` time NOT NULL DEFAULT '00:00:00',
  `status` int(11) NOT NULL,
  `isfailover` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `siteid` (`siteid`,`eid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `1_property` (
      `propertyid` varchar(25) NOT NULL,
      `bid` int(11) NOT NULL,
      `propertyname` varchar(100) NOT NULL,
	  `propertyicon` varchar(100) NOT NULL,
	  `propdesc` varchar(250) NOT NULL,
	    `status`  tinyint(2)  NOT NULL,
      PRIMARY KEY (`propertyid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8?;
   INSERT INTO `m3`.`systemfields` (`fieldid`, `modid`, `fieldname`, `is_hidden`, `pack_module`, `addon`, `is_required`) VALUES (NULL, '48', 'site_employee', '0', '0', '0', '0');
UPDATE  `m3`.`feature_list` SET  `modules` =  '48,49,50' WHERE  `feature_list`.`feature_id` =17;
INSERT INTO `m3`.`module` (`modid`, `modname`, `moddesc`, `add_custom`, `status`) VALUES ('52', 'mproperty', 'Mproperty', '', '1');
INSERT INTO `m3`.`systemfields` (`fieldid`, `modid`, `fieldname`, `is_hidden`, `pack_module`, `addon`, `is_required`) VALUES  (NULL, '52', 'propertyname', '0', '0', '0', '0');
INSERT INTO `m3`.`systemfields` (`fieldid`, `modid`, `fieldname`, `is_hidden`, `pack_module`, `addon`, `is_required`) VALUES (NULL, '52', 'propertyicon', '0', '0', '0', '0');
INSERT INTO `m3`.`systemfields` (`fieldid`, `modid`, `fieldname`, `is_hidden`, `pack_module`, `addon`, `is_required`) VALUES (NULL, '50', 'propertyname', '0', '0', '0', '0');
ALTER TABLE  `1_mc_location` ADD  `pid` INT( 11 ) NOT NULL AFTER  `bid` ;
ALTER TABLE  `1_loc_image` ADD  `pid` INT( 11 ) NOT NULL AFTER  `locid` ;
ALTER TABLE  `1_site` ADD  `pid` INT( 11 ) NOT NULL AFTER  `bid
ALTER TABLE  `1_site_image` ADD  `pid` INT( 11 ) NOT NULL AFTER  `bid` ;
ALTER TABLE  `1_property` ADD  `status` TINYINT( 2 ) NOT NULL AFTER  `propertyicon` ;
ALTER TABLE  `beacon` ADD  `pid` INT( 11 ) NOT NULL AFTER  `business` ;

CREATE TABLE IF NOT EXISTS `feedback_mtrack` (
  `feedtrackid` int(11) NOT NULL AUTO_INCREMENT,
  `feedback` varchar(100) NOT NULL,
  `authKey` varchar(250) NOT NULL,
  PRIMARY KEY (`feedtrackid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
ALTER TABLE  `offers` ADD  `propertyname` VARCHAR( 100 ) NOT NULL AFTER  `endtime` ;
ALTER TABLE  `1_property` ADD  `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER  `status` ;
ALTER TABLE  `user_likes` ADD  `pid` INT( 11 ) NOT NULL AFTER  `bid` ;
ALTER TABLE  `tempsite` ADD  `pid` INT( 11 ) NOT NULL AFTER  `bid` ;
ALTER TABLE  `tempsite` ADD  `propertyname` VARCHAR( 100 ) NOT NULL AFTER  `pid` ;
ALTER TABLE  `visited_history` ADD  `eid` INT( 11 ) NOT NULL AFTER  `bid` ;
ALTER TABLE  `offers` CHANGE  `starttime`  `starttime` DATE NOT NULL ;
ALTER TABLE  `1_site_emp` ADD  `pid` INT( 11 ) NOT NULL AFTER  `eid` ;
ALTER TABLE  `offers` CHANGE  `endtime`  `endtime` DATE NOT NULL ;
ALTER TABLE  `visited_history` ADD  `pid` INT( 11 ) NOT NULL AFTER  `bid` ;
ALTER TABLE  `referrals` ADD  `pid` INT( 11 ) NOT NULL AFTER  `siteid` ;
ALTER TABLE  `user_likes` ADD  `like_time` TIMESTAMP NOT NULL AFTER  `liked` ;

INSERT INTO `m3`.`systemfields` (`fieldid`, `modid`, `fieldname`, `is_hidden`, `pack_module`, `addon`, `is_required`) VALUES (NULL, '52', 'propdesc', '0', '0', '0', '0');
}

?>
