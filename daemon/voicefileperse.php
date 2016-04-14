<?php
require_once ('DaemonClass.php');
class DaemonProcess extends Daemon{
	var $basePath = '/opt/lampp/htdocs/callfw/src/';
	var $pidFileLocation = '';
	var $logFileLocation = '';
	function DaemonProcess(){
		$this->pidFileLocation = $this->basePath.'daemon/voicefileperse.pid';
		$this->logFileLocation = $this->basePath.'daemon/voicefileperse.log';
		parent::Daemon();
		$fp = fopen($this->logFileLocation, 'a');
		fclose($fp);
		chmod($this->logFileLocation, 0777);

	}
	
	function _doTask(){
		ob_flush();
		$running=true;
		$link = mysql_connect('localhost:/opt/lampp/var/mysql/mysql.sock', 'root', '');
		if (!$link) {
				$this->_logMessage('Could not connect:'. mysql_error());
		}else{
			$this->_logMessage('DB Connect');
			$dbselect = mysql_select_db("m3",$link);
			$rst=mysql_query("select brfile,drid,bid,scheduleat from broadcast where type=2 and status=-1");
			while($rec=mysql_fetch_array($rst)){
				$file = $this->basePath."broadcast/".$rec['brfile'];
				if (($handle = fopen($file,"r")) !== FALSE) {
					$this->_logMessage($file. " Read");
					mysql_query("update broadcast set status=1 where drid=".$rec['drid']);
					$i=0;
					while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {$i++;
						//$sqll=mysql_query("SELECT qid FROM `sms_queue` WHERE status=1 order by `totalsent` asc");
						//$re=mysql_fetch_array($sqll);
						$network = mysql_fetch_object(mysql_query("SELECT network FROM mob_series WHERE series='".substr($data['0'],0,4)."'"))->network;
						mysql_query("INSERT INTO voice_campaign SET
									 bid		= '".$rec['bid']."'
									,brid		= '".$rec['drid']."'
									,number		= '".$data['0']."'
									,network	= '".$network."'
									,datetime	= '".$rec['scheduleat']."'");
					}
					$this->_logMessage($i ." Voice campain recorn inserted broadcast id ".$rec['drid']);
					fclose($handle);
					if(rename($file, $this->basePath.'broadcast/completed/'.$rec['brfile'])){
						$this->_logMessage($rec['brfile'] ." Move successfully");
					}else{
						$this->_logMessage($rec['brfile'] ." unable to move");
					}
				}else{
					$this->_logMessage("Fail to read file ".$rec['brfile']." For id ".$rec['drid']);
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
