<?php
class Process extends Controller {
	function Process(){
		parent::controller();
		$this->load->model('configmodel');
	}
	public function __destruct() {
		$this->db->close();
	}
	function index(){
		echo "This page is only for internal process";
	}

	function savefile(){
		//$fp =fopen("process.log","a");fwrite($fp,$_FILES['file']['name']);fclose($fp);
		$ret = true;
		if($_POST['secret']=='metrixcalltracking'){
			if($_FILES['file']['error']==0){
				move_uploaded_file($_FILES['file']['tmp_name'],$this->config->item('sound_path').$_FILES['file']['name']);
			}else{
				$ret = false;
			}
		}else{
			$ret = false;
		}
		echo ($ret)?"success":"failed";
	}
	
	function autodial(){
		$ret = true;
		if(md5($_POST['key'])=='50be1c4031bc43bb164abe49fcfb5ef0'){
			$sql = "UPDATE ".$_POST['bid']."_autodialhistory SET
					dialtime		= '".$_POST['dialedtime']."',
					answeredtime	= '".$_POST['answeredtime']."',
					callstatus		= '".$_POST['dialstatus']."'
					WHERE hid		= '".$_POST['hid']."'";
			$this->db->query($sql);
			$ret = true;
		}else{
			$ret = false;
		}
		echo ($ret)?"success":"failed";
	}
	
	function sendmail($bid,$callid){
		$sql = "SELECT e.empname,e.empnumber,e.empemail,
				c.pulse,c.starttime,c.callfrom,g.groupname,
				e1.empemail as ownermail,
				g.mailalerttowoner
				FROM ".$bid."_callhistory c 
				LEFT JOIN ".$bid."_employee e on c.eid=e.eid
				LEFT JOIN ".$bid."_groups g on c.gid=g.gid
				LEFT JOIN ".$bid."_employee e1 on g.eid=e1.eid 
				WHERE c.callid='".$callid."'";
				
		$emp = $this->db->query($sql)->row_array();
				
		if($emp['empemail']!=''){
			$to = $emp['empemail'];
			$ownermail = $emp['ownermail'];
			if($bid=='640'){
				$subject = ($emp['pulse']>0) 
						? "" 
						: "Missed ";
				$subject.= "Call via your Canvera Profile on ".date('d M Y',strtotime($emp['starttime']))
							." at ".date('H:i:s',strtotime($emp['starttime']));
				$message = ($emp['pulse']>0)
						   ? "You have just spoken to a Canvera lead with number ".$emp['callfrom']
							." on ".date('d M Y',strtotime($emp['starttime']))
							." at ".date('H:i:s',strtotime($emp['starttime']))
							.". Sent via your Canvera Profile."
						   : "You have just missed a call from Canvera lead with number ".$emp['callfrom']
							." on ".date('d M Y',strtotime($emp['starttime']))
							." at ".date('H:i:s',strtotime($emp['starttime']))
							.". Sent via your Canvera Profile.";
			}else{
				$subject = ($emp['pulse']>0) ? "MCube Attended Call" : "MCube Missed Call";
				$subject .= " at ".$emp['starttime'];
				$message = ($emp['pulse']>0)
						   ? "You have attended a call from ".$emp['callfrom']." for ".$emp['groupname']
						   : "You have a missed call from ".$emp['callfrom']." for ".$emp['groupname'];
			}
			$this->load->library('email');
			$this->email->from('noreply@mcube.com', 'MCube');
			$this->email->to($to);
			if($emp['mailalerttowoner']=='1')$this->email->cc($ownermail);
			if($emp['mailalerttowoner']=='1') $to .= ",".$ownermail;
			$this->email->subject($subject);
			$this->email->message($message);
			$this->email->send();	
			//MCubeMail($to,$subject,$message);
		}
	}
	
	function callapi($bid,$callid,$type='0'){
		$itemDetail = $this->configmodel->getDetail('6',$callid,'',$bid);
		$bdetail = $this->configmodel->getDetail('1',$bid,'',$bid);
		$apiKey = $bdetail['apisecret'];
		$itemDetail['apikey'] = $apiKey;
		$rs=$this->configmodel->getDetail('3',$itemDetail['grid'],'',$bid);
		$url = ($type=='0') ? $rs['oncallaction'] : $rs['onhangup'];
		if($url=='' || $url=='0') return 0;
		
		$jsonStr = str_replace("null",'""',json_encode($itemDetail));
		$data = 'data='.urlencode($jsonStr);
		$objURL = curl_init($url);
		curl_setopt($objURL, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($objURL,CURLOPT_POST,1);
		curl_setopt($objURL, CURLOPT_POSTFIELDS,$data);
		$retval = trim(curl_exec($objURL));
		curl_close($objURL);
		$ret = serialize($retval);
		$fp =fopen("apilog.txt","a");fwrite($fp,"\n".'['.date('Y-m-d H:i:s').'] bid:'.$bid.' Output:'. addslashes($ret));fclose($fp);
		/*if($bid=='1'){		
			$fp =fopen("bid_1.txt","a");fwrite($fp,"\n".'['.date('Y-m-d H:i:s').'] bid:'.$bid.' URL:'.$url.' Data:'.$data.' Output:'. addslashes($ret));fclose($fp);
		}*/
	}
	
	function ivrsapi($bid,$callid){
		$itemDetail = $this->configmodel->getDetail('16',$callid,'',$bid);
		$rs=$this->configmodel->getDetail('4',$itemDetail['ivrsid'],'',$bid);
		$itemDetail['callid']=$itemDetail['hid'];
		unset($itemDetail['ivrsid']);
		unset($itemDetail['endtime']);
		unset($itemDetail['options']);
		unset($itemDetail['eid']);
		unset($itemDetail['employee']);
		//unset($itemDetail['filename']);
		$url = $rs['api'];
		if($url=='' || $url=='0') return 0;
		$data = 'data='.urlencode(json_encode($itemDetail));
		$objURL = curl_init($url);
		curl_setopt($objURL, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($objURL,CURLOPT_POST,1);
		curl_setopt($objURL, CURLOPT_POSTFIELDS,$data);
		$retval = trim(curl_exec($objURL));
		curl_close($objURL);
		$ret = serialize($retval);
		$fp =fopen("apilog.txt","a");fwrite($fp,"\n".'['.date('Y-m-d H:i:s').'] bid:'.$bid.' '. $ret);fclose($fp);
	}
	
	function ivrssms($bid,$callid,$option=''){
		if($option!=''){
			$smsBal = $this->configmodel->smsBalance($bid);
			if($smsBal>'0'){
				$itemDetail = $this->configmodel->getDetail('16',$callid,'',$bid);
				$to = $itemDetail['callfrom'];
				$optDetail = $this->configmodel->getDetail('5',$option,'',$bid);
				$sms_text = $optDetail['sms_text'];
				$count = ceil(strlen($sms_text)/60);
				$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
				$api.= "&to=".$to."&text=".urlencode($sms_text);
				file( $api);
				$fp = fopen('smslog.txt','a+');fwrite($fp,"\n[".date('Y-m-d H:i:s')."] ".$api);fclose($fp);
				$this->configmodel->smsDeduct($bid,$count);
				echo "success";
			}
		}else{
			echo "Invalid Request";
		}
	}

	/*   ivrs api */
	function ivrs_api($bid,$callid,$option){
		$itemDetail = $this->configmodel->getDetail('16',$callid,'',$bid);
		$optDetail= $this->configmodel->getDetail('5',$option,'',$bid);
		//print_r($optDetail);exit;
		$url=$optDetail['api_url'];
		if($url=='' || $url=='0') return 0;
		$data = array(
			'callfrom' =>  $itemDetail['callfrom']
			,'option'  =>  $optDetail['optorder']
		);
		$data = 'data='.urlencode(json_encode($data));
		$objURL = curl_init($url);
		curl_setopt($objURL, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($objURL,CURLOPT_POST,1);
		curl_setopt($objURL, CURLOPT_POSTFIELDS,$data);
		$retval = (array)json_decode(trim(curl_exec($objURL)));
		curl_close($objURL);
		//print_r($retval);
		$ret = serialize($retval);
		$fp =fopen("apilog.txt","a");fwrite($fp,"\n".'['.date('Y-m-d H:i:s').'] bid:'.$bid.' '. $ret);fclose($fp);
		if($retval['type']=='sms'){
			$smsBal = $this->configmodel->smsBalance($bid);
			if($smsBal>'0'){
				$to = $itemDetail['callfrom'];
				$sms_text = $retval['data'];
				$count = ceil(strlen($sms_text)/60);
				$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
				$api.= "&to=".$to."&text=".urlencode($sms_text);
				file( $api);
				$fp = fopen('smslog.txt','a+');fwrite($fp,"\n[".date('Y-m-d H:i:s')."] ".$api);fclose($fp);
				$this->configmodel->smsDeduct($bid,$count);
			}
		}
		echo json_encode($retval);
	}
	
	function click_callback($bid,$callid){
		$itemDetail = $this->configmodel->getDetail('click',$callid,'',$bid);
		$url=$itemDetail['url'];
		$fp =fopen("apilog.txt","a");fwrite($fp,"\n".'['.date('Y-m-d H:i:s').'] bid:'.$bid.' Call Bacl URL:'. $url);fclose($fp);
		if($url=='' || $url=='0') return 0;
		$status=array(
			"0"=>"Failed",
			"1"=>"Originate",
			"2"=>"Executive Busy",
			"3"=>"Customer Busy",
			"4"=>"Call Complete",
			"5"=>"Insufficient Balance"
		);
		
		$data = array(
			 'callid' =>  $itemDetail['callid']
			,'executive' =>  $itemDetail['executive']
			,'customer' =>  $itemDetail['customer']
			,'starttime' =>  $itemDetail['starttime']
			,'endtime' =>  $itemDetail['endtime']
			,'refid' =>  $itemDetail['dataid']
			,'pulse' =>  $itemDetail['pulse']
			,'filename' => $itemDetail['filename']
			,'status' =>  $status[$itemDetail['status']]
		);
		$data = '&data='.urlencode(json_encode($data));
		$objURL = curl_init($url);
		curl_setopt($objURL, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($objURL,CURLOPT_POST,1);
		curl_setopt($objURL, CURLOPT_POSTFIELDS,$data);
		$retval = (array)json_decode(trim(curl_exec($objURL)));
		curl_close($objURL);
		//print_r($retval);
		$ret = serialize($retval);
		$fp =fopen("apilog.txt","a");fwrite($fp,"\n".'['.date('Y-m-d H:i:s').'] bid:'.$bid.' '. $ret);fclose($fp);
		echo json_encode($retval);
	}
	
	function datatalk($callid,$opt,$data=''){
		$sql = "SELECT * FROM 427_datatalk WHERE apiid='".$opt."'";
		$url = $this->db->query($sql)->row()->apiurl;
		$itemDetail = $this->configmodel->getDetail('16',$callid,'','427');
		$fp =fopen("datatalk.txt","a");
		fwrite($fp,"\n".'['.date('Y-m-d H:i:s').'] bid:426 callid:'.$callid.' opt:'.$opt.' data:'.$data);
		fwrite($fp,"\n".'['.date('Y-m-d H:i:s').'] bid:426 api:'.serialize($itemDetail));
		$itemDetail['callid']=$itemDetail['hid'];
		$itemDetail['calltime']=$itemDetail['datetime'];
		unset($itemDetail['hid']);
		unset($itemDetail['datetime']);
		unset($itemDetail['ivrsid']);
		unset($itemDetail['endtime']);
		unset($itemDetail['options']);
		unset($itemDetail['eid']);
		unset($itemDetail['employee']);
		unset($itemDetail['filename']);
		switch($opt){
			case 1:
				$itemDetail['option']=$data;
			break;
			case 2:
				$itemDetail['recordurl']='https://mcube.vmc.in/sounds/'.$callid.'_nameadd.wav';
			break;
			case 3:
				$itemDetail['recordurl']='https://mcube.vmc.in/sounds/'.$callid.'_grievance.wav';
			break;
			case 4:
				$itemDetail['ferid']=$data;
			break;
			case 5:
				$itemDetail['stdcode']=$data;
			break;
			case 6:
				$itemDetail['recordurl']='https://mcube.vmc.in/sounds/'.$callid.'_grievance.wav';
			break;
			case 7:
				$itemDetail['option']=$data;
			break;
			case 8:
				$itemDetail['recordurl']='https://mcube.vmc.in/sounds/'.$callid.'.wav';
			break;
		}
		
		//print_r($itemDetail);exit;
		
		$post = "?";
		foreach($itemDetail as $key => $val){
			$post .= '&'.$key.'='.urlencode($val);
		}
		$ret = file_get_contents($url.$post);
		fwrite($fp,"\n".'['.date('Y-m-d H:i:s').'] bid:426 api:'.$url.$post.' output:'. $ret);fclose($fp);
		echo $ret;	
	}
}
?>
