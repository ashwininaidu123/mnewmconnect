<?php
class Msgmodel extends Model {
	var $data;
    function Msgmodel(){
        parent::Model();
    }
    
    function addVoiceTemplate(){//new
		$newName = "VIOCE".date('YmdHis').".wav";
		if(move_uploaded_file($_FILES['soundfile']['tmp_name'],$this->config->item('sound_path').$newName)){
			$soundid = ($_POST['soundid']!='')
						?$_POST['soundid']
						:$this->db->query("SELECT COALESCE(MAX(`soundid`),0)+1 as id FROM `sounds`")->row()->id;
			$sql = "REPLACE INTO sounds SET
					 bid			= '".$this->session->userdata('bid')."'
					,soundid		= '".$soundid."'
					,title			= '".$_POST['title']."'
					,soundfile		= '".$newName."'
					,duration		= '".$this->getDuration($this->config->item('sound_path').$newName)."'";
			$this->db->query($sql);
			$this->auditlog->auditlog_info($this->lang->line('label_viocebroadcast'),$_POST['title']." New Voice template created");
			if(isset($_POST['custom']))
				foreach($_POST['custom'] as $fid=>$val){
				$sql = "REPLACE INTO ".$this->session->userdata('bid')."_customfieldsvalue SET
						 bid			= '".$this->session->userdata('bid')."'
						,modid			= '".$_POST['modid']."'
						,fieldid		= '".$fid."'
						,dataid			= '".$soundid."'
						,value			= '".(is_array($val)?implode(',',$val):$val)."'";
				$this->db->query($sql);
				}
		}
		return ;
	}
	
	function addPhonebook(){//new
		$filename = $_FILES['filename']['tmp_name'];
		$handle = fopen($filename, "r");
		if(!$handle){
			$this->session->set_flashdata(array('msgt' => 'error','msg' => $this->lang->line('ivrs_errorfile')));
				return;
		}else{
			$pbid = ($_POST['pbid']!='')
					?$_POST['pbid']
					:$this->db->query("SELECT COALESCE(MAX(`pbid`),0)+1 as id FROM `phonebook`")->row()->id;
			$sql = "REPLACE INTO phonebook SET
					 bid			= '".$this->session->userdata('bid')."'
					,pbid			= '".$pbid."'
					,pbname			= '".$_POST['pbname']."'
					,createby		= '".$this->session->userdata('uid')."'";
			$this->db->query($sql);
			//$gdetails=$this->configmodel->getDetail('10',$_POST['pbid']);
			$this->auditlog->auditlog_info($this->lang->line('label_phonebook'),$_POST['pbname']." New Address book created");
			if(isset($_POST['custom']))
			foreach($_POST['custom'] as $fid=>$val){
			$sql = "REPLACE INTO ".$this->session->userdata('bid')."_customfieldsvalue SET
					 bid			= '".$this->session->userdata('bid')."'
					,modid			= '".$_POST['modid']."'
					,fieldid		= '".$fid."'
					,dataid			= '".$pbid."'
					,value			= '".(is_array($val)?implode(',',$val):$val)."'";
			$this->db->query($sql);
			}
			while (($contact = fgetcsv($handle,filesize($filename), ",")) !== FALSE) {
				$num = substr($contact[0],-10,10);
				if(preg_match('/^[7-9][0-9]{9}$/',$num)){
					$name = isset($contact['1']) ? $contact['1'] : "";
					$contactid=$this->db->query("SELECT COALESCE(MAX(`contactid`),0)+1 as id FROM ".$this->session->userdata('bid')."_contactlist")->row()->id;
					$sql = "REPLACE into ".$this->session->userdata('bid')."_contactlist SET
							 bid		= '".$this->session->userdata('bid')."'
							,contactid	= '".$contactid."'
							,pbid		= '".$pbid."'
							,number		= '".$num."'
							,name		= '".$name."'";
					$this->db->query($sql);
				}
			}
			fclose($handle);
		}
		return ;
	}
	
	function deletePhonebook($pbid=''){
		$gdetails=$this->configmodel->getDetail('10',$pbid);
		$this->db->query("DELETE FROM ".$this->session->userdata('bid')."_contactlist WHERE
						  bid		= '".$this->session->userdata('bid')."'
						  AND pbid	= '".$pbid."'");
		$this->db->query("DELETE FROM ".$this->session->userdata('bid')."_customfieldsvalue WHERE
						  bid		= '".$this->session->userdata('bid')."'
						  AND modid	= '10'
						  AND dataid= '".$pbid."'");
		$this->db->query("DELETE FROM phonebook WHERE
						  bid		= '".$this->session->userdata('bid')."'
						  AND pbid	= '".$pbid."'");
		$this->auditlog->auditlog_info($this->lang->line('label_phonebook'),$gdetails['pbname']."  Address book deleted successfully");					
	}
    
	function deleteSender($snid){//new
		$sql = "DELETE FROM senderid WHERE snid = '".$snid."' AND bid='".$this->session->userdata('bid')."'";
		$rst = $this->db->query($sql);
	}
	function getSenderidList(){//new
		$sql = "SELECT senderid FROM senderid
				WHERE bid='".$this->session->userdata('bid')."'
				AND status='1'
				ORDER BY senderid DESC";
		$rst = $this->db->query($sql)->result_array();
		$ret[''] = 'Select SenderId';
		foreach ($rst as $rec){
			$ret[$rec['senderid']] = $rec['senderid'];
		}
		return $ret;
	}
	function getSoundList(){//new
		$sql = "SELECT soundid,title FROM sounds
				WHERE bid='".$this->session->userdata('bid')."'
				ORDER BY soundid DESC";
		$rst = $this->db->query($sql)->result_array();
		$ret[''] = 'Select Sound';
		foreach ($rst as $rec){
			$ret[$rec['soundid']] = $rec['title'];
		}
		return $ret;
	}
	function getTmplList(){//new
		$sql = "SELECT * FROM sms_template
				WHERE bid='".$this->session->userdata('bid')."'
				ORDER BY templateid DESC";
		$rst = $this->db->query($sql)->result_array();
		$ret[''] = 'Select Template';
		foreach ($rst as $rec){
			$ret[$rec['templateid']] = $rec['title'];
		}
		return $ret;
	}
	function getPBList(){//new
		$sql = "SELECT * FROM phonebook
				WHERE bid='".$this->session->userdata('bid')."'
				ORDER BY pbid DESC";
		$rst = $this->db->query($sql)->result_array();
		$ret[''] = 'Select Phonebook';
		foreach ($rst as $rec){
			$ret[$rec['pbid']] = $rec['pbname'];
		}
		return $ret;
	}
	
	
    
	function getDuration($file){//new
		if(!file_exists($file))
			return 0;
		$fp = fopen($file, 'r');
		$size_in_bytes = filesize($file);
		fseek($fp, 20);
		$rawheader = fread($fp, 16);
		$header = unpack('vtype/vchannels/Vsamplerate/Vbytespersec/valignment/vbits',$rawheader);
		$sec = ceil($size_in_bytes/$header['bytespersec']);
		return $sec;
	}
	
	
	function addBroadcast(){
		$count = ceil($this->getDuration($this->config->item('sound_path').$this->getSoundfilename($_POST['soundid']))/60);
		
		$contact = "";
		$drid=$this->db->query("SELECT COALESCE(MAX(`drid`),0)+1 as id FROM `broadcast`")->row()->id;
		$sql = "INSERT INTO broadcast SET
				 bid			= '".$this->session->userdata('bid')."'
				,drid			= '".$drid."'
				,uid			= '".$this->session->userdata('uid')."'";
								
		if(isset($_POST['to']) && $_POST['to']!=''){
			$sql .= ",type		= '0'
					 ,status	= '1'
					 ,`count`	= '1'";
			$cr = $count * 1;
			$contact = $_POST['to'];
		}elseif(isset($_POST['pbid']) && $_POST['pbid']!=''){
			$sql .= ",type		= '1'
					 ,pbid		= '".$_POST['pbid']."'
					 ,`count`	= '". $this->countPB($_POST['pbid']) ."'
					 ,status	= '-1'";
			$cr = $count * $this->countPB($_POST['pbid']);
		}elseif($_FILES['brfile']['error']==0){
			$newName = "BDR".date('YmdHis').".".end(explode(".",$_FILES['brfile']['name']));
			move_uploaded_file($_FILES['brfile']['tmp_name'],"broadcast/".$newName);
			$sql .= ",type		= '2'
					 ,brfile	= '".$newName."'
					 ,`count`	= '". count(file("broadcast/".$newName)) ."'
					 ,status	= '-1'";
			$cr = $count * count(file("broadcast/".$newName));
		}else{
			$this->session->set_flashdata(
				array('msgt' => 'error'
					, 'msg' => $this->lang->line('level_error')
				));
			return ;
		}
		
		$bal = $this->checkCredit($this->session->userdata('bid'));
		
		if(($cr * $bal['rate']['1'])>$bal['balance']){
			$this->session->set_flashdata(
				array('msgt' => 'error'
					, 'msg' => 'insufficient balance'
				));
			return ;
		}else{
			$trid=$this->db->query("SELECT COALESCE(MAX(`trid`),0)+1 as id FROM `credit_use`")->row()->id;
			$this->db->query("INSERT INTO credit_use SET
							  trid		= '".$trid."'
							 ,bid		= '".$this->session->userdata('bid')."'
							 ,amount	= '".($cr * $bal['rate']['1'])."'
							 ,remark	= 'SEND Call Broadcast'");
		}

							
		$sql .= ",soundid		= '".$_POST['soundid']."'
				 ,soundfile		= '".$this->getSoundfilename($_POST['soundid'])."'
				 ,scheduleat	= '".$_POST['scheduleat']."'";
		$this->db->query($sql);
		$gdetails=$this->configmodel->getDetail('10',$_POST['pbid']);
		$this->auditlog->auditlog_info($this->lang->line('label_viocebroadcast'),"shedule voice broadcast for ".$gdetails['title']);	
		$drid = $this->db->insert_id();
		if($contact!=''){
			$vcid=$_POST['to'].time();
			$sql = "INSERT INTO voice_campaign SET
					 bid			= '".$this->session->userdata('bid')."'
					,vcid			= '".$vcid."'
					,brid			= '".$drid."'
					,number			= '".$_POST['to']."'
					,network		= '".$this->db->query("SELECT network FROM mob_series WHERE series='".substr($_POST['to'],0,4)."'")->row()->network."'
					,datetime		= '".$_POST['scheduleat']."'";
			$this->db->query($sql);
		}
		if(isset($_POST['custom']))
		foreach($_POST['custom'] as $fid=>$val){
			$sql = "REPLACE INTO ".$this->session->userdata('bid')."_customfieldsvalue SET
					 bid			= '".$this->session->userdata('bid')."'
					,modid			= '".$_POST['modid']."'
					,fieldid		= '".$fid."'
					,dataid			= '".$drid."'
					,value			= '".(is_array($val)?implode(',',$val):$val)."'";
			$this->db->query($sql);
		}
		$this->session->set_flashdata(
				array('msgt' => 'success'
					, 'msg' => $this->lang->line('sms_send_success')
				));
				
		$this->sendMail();
		return ;
	}
	function getSoundfilename($soundid=''){
		return $this->db->query("SELECT soundfile FROM sounds 
								 WHERE bid='".$this->session->userdata('bid')."'
								 AND soundid='".$soundid."'")->row()->soundfile;
	}
	
	function deleteVoice($id=''){//new
		$gdetails=$this->configmodel->getDetail('9',$id);
		//print_r($gdetails);exit;
		$this->db->query("DELETE FROM sounds 
					WHERE soundid	= '".$id."' 
					AND bid			='".$this->session->userdata('bid')."'");
		$this->db->query("DELETE FROM ".$this->session->userdata('bid')."_customfieldsvalue 
					WHERE bid		= '".$this->session->userdata('bid')."'
					AND modid		= '9'
					AND dataid		= '".$id."'");
		$this->auditlog->auditlog_info($this->lang->line('label_viocebroadcast'),$gdetails['title']."  voice template deleted successfully");								
					
	}
	

	function deleteSMSTemplate($templateid){//new
		$itemDetail = $this->configmodel->getDetail('8',$templateid);
		$this->db->query("DELETE FROM sms_template
					WHERE templateid = '".$templateid."' 
					AND bid			 ='".$this->session->userdata('bid')."'");
		$this->db->query("DELETE FROM ".$this->session->userdata('bid')."_customfieldsvalue 
					WHERE bid		 = '".$this->session->userdata('bid')."'
					AND modid		 = '8'
					AND dataid		 = '".$templateid."'");
		$this->auditlog->auditlog_info($this->lang->line('label_Sendsms'),$itemDetail['title']." deleted sms template");	
	}

	function addSmsTemplate(){//new
		$tmplid = ($_POST['templateid']!='')
					?$_POST['templateid']
					:$this->db->query("SELECT COALESCE(MAX(`templateid`),0)+1 as id FROM `sms_template`")->row()->id;
		$sql = "REPLACE INTO sms_template SET
				 templateid		= '".$tmplid."'
				,bid			= '".$this->session->userdata('bid')."'
				,title			= '".$_POST['title']."'
				,content		= '".mysql_real_escape_string($_POST['content'])."'
				,createdby		= '".$this->session->userdata('uid')."'";
		if($_POST['templateid']!=""){
			$this->auditlog->auditlog_info($this->lang->line('label_Sendsms'),$this->input->post('title')." updated sms template");	
		}else{
		$this->auditlog->auditlog_info($this->lang->line('label_Sendsms'),$this->input->post('title')." created New sms template");	
		}
		$this->db->query($sql);
		
		if(isset($_POST['custom']))
		foreach($_POST['custom'] as $fid=>$val){
			$sql = "REPLACE INTO ".$this->session->userdata('bid')."_customfieldsvalue SET
					 bid			= '".$this->session->userdata('bid')."'
					,modid			= '".$_POST['modid']."'
					,fieldid		= '".$fid."'
					,dataid			= '".$tmplid."'
					,value			= '".(is_array($val)?implode(',',$val):$val)."'";
			$this->db->query($sql);
		}
		return ;
	}
	
	function sendSMS(){//new
		$_POST['content'] = str_replace("\r","",$_POST['content']);
		$_POST['content'] = str_replace("\"","'",$_POST['content']);
		$_POST['content'] = str_replace('"',"'",$_POST['content']);
		$count = ceil(strlen($_POST['content'])/160);
		$contact = "";
		$contentid=$this->db->query("SELECT COALESCE(MAX(`contentid`),0)+1 as id FROM `sms_content`")->row()->id;
		$sql = "INSERT INTO sms_content SET
				 bid			= '".$this->session->userdata('bid')."'
				,contentid		= '".$contentid."'
				,uid			= '".$this->session->userdata('uid')."'";
				
		if(isset($_POST['to']) && $_POST['to']!=''){
			$sql .= ",type		= '0'
					 ,status	= '1'
					 ,total		= '1'";
			$cr = $count * 1;
			$contact = $_POST['to'];
		}elseif(isset($_POST['pbid']) && $_POST['pbid']!=''){
			$sql .= ",type		= '1'
					 ,pbid	= '".$_POST['pbid']."'
					 ,total		= '". $this->countPB($_POST['pbid']) ."'
					 ,status	= '-1'";
			$cr = $count * $this->countPB($_POST['pbid']);
		}elseif($_FILES['filename']['error']==0){
			$newName = "SMS".date('YmdHis').".".end(explode(".",$_FILES['filename']['name']));
			move_uploaded_file($_FILES['filename']['tmp_name'],"broadcast/".$newName);
			$sql .= ",type		= '2'
					 ,filename	= '".$newName."'
					 ,total		= '". count(file("broadcast/".$newName)) ."'
					 ,status	= '-1'";
			$cr = $count * count(file("broadcast/".$newName));
		}else{
			$this->session->set_flashdata(
				array('msgt' => 'error'
					, 'msg' => $this->lang->line('level_error')
				));
			return ;
		}
		$bal = $this->checkCredit($this->session->userdata('bid'));
		if(($cr * $bal['rate']['2'])>$bal['balance']){
			$this->session->set_flashdata(
				array('msgt' => 'error'
					, 'msg' => 'insufficient balance'
				));
			return ;
		}else{
			$trid=$this->db->query("SELECT COALESCE(MAX(`trid`),0)+1 as id FROM `credit_use`")->row()->id;
			$this->db->query("INSERT INTO credit_use SET
							  trid		= '".$trid."'
							 ,bid		= '".$this->session->userdata('bid')."'
							 ,amount	= '".($cr * $bal['rate']['2'])."'
							 ,remark	= 'SEND SMS'");
		}

							
		$sql .= ",senderid		= '".$_POST['senderid']."'
				 ,scheduleat	= '".$_POST['scheduleat']."'
				 ,content		= '".mysql_real_escape_string($_POST['content'])."'";
		$this->db->query($sql);
		$contentid = $this->db->insert_id();
		if($contact!=''){
			//$smsid=$this->db->query("SELECT COALESCE(MAX(`smsid`),0)+1 as id FROM `sms_queue_1`")->row()->id;
			$sql = "INSERT INTO sms_queue_1 SET
					 bid			= '".$this->session->userdata('bid')."'
					,smsid			= '".$_POST['to'].time()."'
					,contentid		= '".$contentid."'
					,senderid		= '".$_POST['senderid']."'
					,number			= '".$_POST['to']."'
					,network		= '".$this->db->query("SELECT network FROM mob_series WHERE series='".substr($_POST['to'],0,4)."'")->row()->network."'
					,datetime		= '".$_POST['scheduleat']."'";
			$this->db->query($sql);
		}
		if(isset($_POST['custom']))
		foreach($_POST['custom'] as $fid=>$val){
			$sql = "REPLACE INTO ".$this->session->userdata('bid')."_customfieldsvalue SET
					 bid			= '".$this->session->userdata('bid')."'
					,modid			= '".$_POST['modid']."'
					,fieldid		= '".$fid."'
					,dataid			= '".$contentid."'
					,value			= '".(is_array($val)?implode(',',$val):$val)."'";
			$this->db->query($sql);
		}
		$this->session->set_flashdata(
				array('msgt' => 'success'
					, 'msg' => $this->lang->line('sms_send_success')
				));
		return ;
	}
	function countPB($pbid){//new
		$sql = "SELECT count(contactid) as cnt 
				FROM ".$this->session->userdata('bid')."_contactlist
				WHERE pbid='".$pbid."'";
		return $this->db->query($sql)->row->cnt;
	}
	function getPhonebookList($bid,$ofset='0',$limit='20'){//new
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
					
		$con = "";
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$con .= (isset($s['pbname']) && $s['pbname']!='')?" AND pbname like '%".$s['pbname']."%'":"";
		$con .= (isset($s['createby']) && $s['createby']!='')?" AND createby like '%".$s['createby']."%'":"";
		$con .= (isset($s['datetime']) && $s['datetime']!='')?" AND date(datetime) = '".$s['datetime']."'":"";
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS pbid FROM phonebook
				WHERE bid='".$bid."' ".$con."
				ORDER BY pbid DESC
				limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		
		$opt_add 	= $roleDetail['modules']['10']['opt_add'];
		$opt_view 	= $roleDetail['modules']['10']['opt_view'];
		$opt_delete = $roleDetail['modules']['10']['opt_delete'];
		
		$fieldset = $this->configmodel->getFields('10');
		$keys = array();
		$header = array('#');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['listing'] && $field['fieldname']!='filename'){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldname']);
					array_push($header,(($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
				}
			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,'custom['.$field['fieldid'].']');
					array_push($header,$field['customlabel']);
				}
			}
		}
		if($opt_add || $opt_view || $opt_delete)
			array_push($header,$this->lang->line('level_Action'));
		$ret['header'] = $header;
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$data = array($i);
			$r = $this->configmodel->getDetail('10',$rec['pbid']);
			foreach($keys as $k){
					$v = isset($r[$k])?$r[$k]:"";
				array_push($data,$v);
			}
			$act = "";
			if($opt_add || $opt_view || $opt_delete){
				$act .= ($opt_add)? '<a href="msg/bppopup/'.$r['pbid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Edit" class="fa fa-edit"></span></a>':'';
				//$act .= ($opt_view)? '<a href="msg/bplist/'.$r['pbid'].'" class="callPopup"><img src="system/application/img/icons/edit.png" title="View Phonebook" /></a>':'';
				$act .= ($opt_delete)? '<a href="msg/delpb/'.$r['pbid'].'" class="confirm deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	} 
	function getSMSTmplList($bid,$ofset='0',$limit='20'){//new
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		
		$con = "";
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$con .= (isset($s['title']) && $s['title']!='')?" AND title like '%".$s['title']."%'":"";
		$con .= (isset($s['content']) && $s['content']!='')?" AND content like '%".$s['content']."%'":"";
		$con .= (isset($s['datetime']) && $s['datetime']!='')?" AND date(datetime) >= '".$s['datetime']."'":"";
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS templateid FROM sms_template
				WHERE bid='".$bid."' ".$con."
				ORDER BY templateid DESC
				limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		
		$opt_add 	= $roleDetail['modules']['8']['opt_add'];
		$opt_view 	= $roleDetail['modules']['8']['opt_view'];
		$opt_delete = $roleDetail['modules']['8']['opt_delete'];
		
		$fieldset = $this->configmodel->getFields('8');
		$keys = array();
		$header = array('#');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldname']);
					array_push($header,(($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
				}
			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,'custom['.$field['fieldid'].']');
					array_push($header,$field['customlabel']);
				}
			}
		}
		if($opt_add || $opt_view || $opt_delete)
			array_push($header,$this->lang->line('level_Action'));
		$ret['header'] = $header;
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$data = array($i);
			$r = $this->configmodel->getDetail('8',$rec['templateid']);

			foreach($keys as $k){
					$v = isset($r[$k])?$r[$k]:"";
				array_push($data,$v);
			}
			$act = "";
			if($opt_add || $opt_view || $opt_delete){
				$act .= ($opt_add)? '<a href="msg/smstmpl/'.$r['templateid'].'"><span title="Edit" class="fa fa-edit"></span></a>':'';
				$act .= ($opt_add)? '<a href="msg/delsmstmlp/'.$r['templateid'].'" class="confirm deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	} 
	function getVoiceTmplList($bid,$ofset='0',$limit='20'){//new
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
					
		$con = "";
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$con .= (isset($s['title']) && $s['title']!='')?" AND title like '%".$s['title']."%'":"";
		$con .= (isset($s['duration']) && $s['duration']!='')?" AND duration >= '".$s['duration']."'":"";
		$con .= (isset($s['datetime']) && $s['datetime']!='')?" AND date(datetime) >= '".$s['datetime']."'":"";
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS soundid FROM sounds
				WHERE bid='".$bid."'
				ORDER BY soundid DESC
				limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		
		$opt_add 	= $roleDetail['modules']['9']['opt_add'];
		$opt_view 	= $roleDetail['modules']['9']['opt_view'];
		$opt_delete = $roleDetail['modules']['9']['opt_delete'];
		
		$fieldset = $this->configmodel->getFields('9');
		$keys = array();
		$header = array('#');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldname']);
					array_push($header,(($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
				}
			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,'custom['.$field['fieldid'].']');
					array_push($header,$field['customlabel']);
				}
			}
		}
		if($opt_add || $opt_view || $opt_delete)
			array_push($header,$this->lang->line('level_Action'));
		$ret['header'] = $header;
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$data = array($i);
			$r = $this->configmodel->getDetail('9',$rec['soundid']);

			foreach($keys as $k){
				if($k=='soundfile'){
					$v = '<embed src="'.site_url('sounds/'.$r[$k]).'" volume="100"
						  loop="false" controls="console" height="29" wmode="transparent"
						  autostart="FALSE" width="150" hidden="false">';
				}else{
					$v = isset($r[$k])?$r[$k]:"";
				}
				array_push($data,$v);
			}
			$act = "";
			if($opt_add || $opt_view || $opt_delete){
				$act .= ($opt_add)? '<a href="msg/voivetmpl/'.$r['soundid'].'"><span title="Edit" class="fa fa-edit"></span></a>':'';
				$act .= ($opt_delete)? '<a href="msg/delvoivetmpl/'.$r['soundid'].'" class="confirm deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	} 
	function addSenderId(){
		$snid = ($_POST['snid']!='')
				?$_POST['snid']
				:$this->db->query("SELECT COALESCE(MAX(`snid`),0)+1 as id FROM `senderid`")->row()->id;
		$sql = "REPLACE INTO senderid SET
				 snid			= '".$_POST['snid']."'
				,bid			= '".$this->session->userdata('bid')."'
				,uid			= '".$this->session->userdata('uid')."'
				,senderid		= '".$_POST['senderid']."'";
		$this->db->query($sql);
		$snid = $this->db->insert_id();
		if(isset($_POST['custom']))
		foreach($_POST['custom'] as $fid=>$val){
			$sql = "REPLACE INTO ".$this->session->userdata('bid')."_customfieldsvalue SET
					 bid			= '".$this->session->userdata('bid')."'
					,modid			= '".$_POST['modid']."'
					,fieldid		= '".$fid."'
					,dataid			= '".$snid."'
					,value			= '".(is_array($val)?implode(',',$val):$val)."'";
			$this->db->query($sql);
		}
		return ;
	}
	function getSMSSenderIdList($bid,$ofset='0',$limit='20'){//new
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);

		$con = "";
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$con .= (isset($s['senderid']) && $s['senderid']!='')?" AND senderid like '%".$s['senderid']."%'":"";
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS snid FROM senderid
				WHERE bid='".$bid."' ".$con."
				ORDER BY snid DESC
				limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		
		$opt_add 	= $roleDetail['modules']['14']['opt_add'];
		$opt_view 	= $roleDetail['modules']['14']['opt_view'];
		$opt_delete = $roleDetail['modules']['14']['opt_delete'];

		$fieldset = $this->configmodel->getFields('14');
		$keys = array();
		$header = array('#');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldname']);
					array_push($header,(($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
				}
			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,'custom['.$field['fieldid'].']');
					array_push($header,$field['customlabel']);
				}
			}
		}
		if($opt_add || $opt_delete)
			array_push($header,$this->lang->line('level_Action'));
		$ret['header'] = $header;
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$data = array($i);
			$r = $this->configmodel->getDetail('14',$rec['snid']);

			foreach($keys as $k){
					$v = isset($r[$k])?$r[$k]:"";
				array_push($data,$v);
			}
			$act = "";
			if($opt_add || $opt_view || $opt_delete){
				$act .= ($opt_add)? '<a href="msg/senderadd/'.$r['snid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Edit" class="fa fa-edit"></span></a>':'';
				$act .= ($opt_delete)? '<a href="msg/delsender/'.$r['snid'].'" class="confirm deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	} 
	
	function checkCredit($bid){
		$rst = $this->db->query("SELECT r.bid,r.product_id,p.product_name,r.rate FROM `product_rate` r
									LEFT JOIN products p on r.product_id=p.product_id
									WHERE r.bid='".$bid."'")->result_array();
		foreach($rst as $rec) $rate[$rec['product_id']] = $rec['rate'];
		$data['balance'] = $this->db->query("SELECT balance FROM credit_bal WHERE bid='".$bid."'")->row()->balance;
		$data['rate'] = $rate;
		//echo "<pre>";print_r($data);echo "</pre>";
		return $data;
	}
	
	function sendMail(){
		$to = 'alok.gupta@vmc.in';
		$subject = "Voice Droadcast Request";
		$message = "Dear Alok,\n
				\n\tA voice bradcast is requested from mcube.vmc.in please process this request\n\n
				Regards\n
				MCube System Bangalore";
		$headers  = 'MIME-Version: 1.0' . "\n";
		$headers .= 'Content-type: text/plain; charset=UTF-8' . "\n";
		$headers .= 'To: Alok <alok.gupta@vmc.in>' . "\n";
		$headers .= 'From: MCube <noreply@vmc.in>' . "\n";
		$headers .= 'Cc: sundeep.misra@vmc.in' . "\n";
		$headers .= 'Bcc: tapan.chatterjee@vmc.in' . "\n";
		$mail = mail($to, $subject, $message, $headers);	
	}
}
?>
