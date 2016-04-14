<?php
require_once ('DaemonClass.php');
class DaemonProcess extends Daemon{
	var $pidFileLocation = 'smsfileperse.pid';
	var $logFileLocation = 'smsfileperse.log';
	function DaemonProcess(){
	  parent::Daemon();
	  $fp = fopen($this->pidFileLocation, 'a');
	  chmod($this->pidFileLocation, 0777);
	  fclose($fp);
	  $fp = fopen($this->logFileLocation, 'a');
	  chmod($this->logFileLocation, 0777);
	  fclose($fp);
	}

	function _doTask(){
		ob_flush();
		$running=true;
		$link = mysql_connect('localhost:/opt/lampp/var/mysql/mysql.sock', 'root', '');
		if (!$link) {
				$this->_logMessage('Could not connect:'. mysql_error());
		}else{
			$dbselect = mysql_select_db("m3",$link);
		$sql=mysql_query("select filename,contentid,bid,scheduleat,senderid from sms_content where type=2 and status=-1");
		$share=0;
		while($res=mysql_fetch_array($sql)){
			$lines = count(file($res['filename']));
			$file = "/opt/lampp/htdocs/callfw/src/broadcast/".$res['filename'];
			if (($handle = fopen($file,"r")) !== FALSE) {
				$i=0;
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {$i++;
					$sqll=mysql_query("SELECT qid FROM `sms_queue` WHERE status=1 order by `totalsent` asc");
					$re=mysql_fetch_array($sqll);
					$network = mysql_fetch_object(mysql_query("SELECT network FROM mob_series WHERE series='".substr($data['0'],0,4)."'"))->network;
					mysql_query("INSERT INTO `sms_queue_".$re['qid']."` (`smsid`, `bid`, `contentid`, `senderid`, `number`, `datetime`, `network`, `status`) VALUES (NULL, '".$res['bid']."', '".$res['contentid']."', '".$res['senderid']."', '".$data[0]."', '".$res['scheduleat']."', '".$network."', '0');");
				}
				$this->_logMessage($i ." Record inserted fro content id ".$res['contentid']);
				$sql=mysql_query("update sms_content set status=1 where contentid=".$res['contentid']);
				fclose($handle);
				if(rename($file, '/opt/lampp/htdocs/callfw/src/broadcast/completed/'.$res['filename'])){
					$this->_logMessage($res['filename'] ." Move successfully");
				}else{
					$this->_logMessage($res['filename'] ." unable to move");
				}
			}else{
					$this->_logMessage("Fail to read file ".$res['filename']);
			}
		}


			$running = false;		
		}
		if(!$link || !$running){
			sleep(10);
		}
	}
}
$BQ = new DaemonProcess();
$BQ->start();
?>
