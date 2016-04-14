<?php
Class Groupmodel extends Model
{
	function Groupmodel(){
		parent::Model();
		$this->load->model('ivrsmodel');
		$this->load->model('auditlog');
		$this->load->model('configmodel');
		$this->load->model('empmodel');
		$this->load->model('keywordmodel');
		$this->load->model('pollmodel');
	}	
	function BusinessList(){
		$res=array();
		$sql =$this->db->query("SELECT bid,businessname FROM business WHERE status = '1'");
		if($sql->num_rows()>0){
			$ress=$sql->result_array();
			$res['']=$this->lang->line('level_select');
			foreach($ress as $rec)
					$res[$rec['bid']] = $rec['businessname'];
		}				
		return $res;
	}
	function NumberList($bid='-1'){		
		$sql=$this->db->query("SELECT * FROM prinumber	WHERE bid = '".$bid."' AND status='1'");
		return $sql->result_array();
	}

	function getPri($val,$mod){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$rst=$this->db->query("SELECT p.status,p.number,p.landingnumber from prinumbers p
									LEFT JOIN landingnumbers l on l.pri=p.pri
									WHERE p.bid='".$bid."' AND p.status=0
									AND l.region = '".$val."' AND p.ntype=0 AND l.module_id='1'
									ORDER BY l.region")->result_array();
		return $rst;
	}
	function RuleList(){
		$res=array();	
		$sql = $this->db->query("SELECT * FROM group_rules");
		 if($sql->num_rows()>0) {
 			$ress=$sql->result_array();
			$res['']=$this->lang->line('level_select');
			foreach($ress as $rec)
				$res[$rec['rulesid']] = $rec['rulename'];
	    }
	   			
	    return $res;
	}

	function PrimaryRuleList(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();	
		$sql = $this->db->query("SELECT * FROM ".$bid."_custom_region");
		$res['0']='All';
		if($sql->num_rows()>0){
 			$ress=$sql->result_array();	
			foreach($ress as $rec)
				$res[$rec['regionid']] = $rec['regionname'];
	    }				
	    return $res;
	}
	
	function getLanReg(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();	
		$sql = $this->db->query("SELECT l.region, p.bid,l.number FROM landingnumbers l 
		                        LEFT JOIN prinumbers p ON p.landingnumber=l.number 
		                        WHERE p.bid='".$bid."' 
		                        AND p.status = '0' AND p.type = '0' AND l.module_id = '1'
		                         ");   
        	             
		if($sql->num_rows()>0){
 			$ress=$sql->result_array();
 			
 	        $res['']=$this->lang->line('level_select');
 		       foreach($ress as $rec){
				$res[$rec['region']] = $rec['region'];
				
	    }		
			}
	    return $res;
	}
	function create_group(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$arr=array_keys($_POST);
	    	for($i=0;$i<sizeof($arr);$i++){
				if(!in_array($arr[$i],array("update_system","gid","clone","gids","oldprinumber","groupname","prinumber","rules","eid","keyword"
			,"primary_rule","record","bday","hdaytext","replymessage","replytocustomer","replytoexecutive","replytoexecutive","timeout"
			,"oneditaction","onhangup","replyattmsg","leadaction","oncallaction","supportaction","supportgrp"))){
							if(is_array($_POST[$arr[$i]]))
								 $val = @implode(',',$_POST[$arr[$i]]);
							elseif($_POST[$arr[$i]]!="")
								 $val=$_POST[$arr[$i]];
							else
								$val='';
							$this->db->set($arr[$i],$val);
					}
				}
		$gid=$this->db->query("SELECT COALESCE(MAX(`gid`),0)+1 as id FROM ".$bid."_groups")->row()->id;
		$this->db->set('gid',$gid);
		$this->db->set('bid',$bid);
		$this->db->set('timeout',$this->input->post('timeout'));
		$_POST['prinumber'] = ($_POST['prinumber']!='0') ? $_POST['prinumber']:$this->ivrsmodel->getFreePri();
		if($_FILES['hdayaudio']['error']==0){
			$ext=pathinfo($_FILES['hdayaudio']['name'],PATHINFO_EXTENSION);
			$newName = "H".date('YmdHis').".".$ext;
			move_uploaded_file($_FILES['hdayaudio']['tmp_name'],$this->config->item('sound_path').$newName);
			$this->db->set('hdayaudio',$newName);
		}
	   //~ if(isset($_FILES['voicemessage']) && $_FILES['voicemessage']['error']==0){
			//~ $ext=pathinfo($_FILES['voicemessage']['name'],PATHINFO_EXTENSION);
			//~ $newName = "V".date('YmdHis').".".$ext;
			//~ move_uploaded_file($_FILES['voicemessage']['tmp_name'],$this->config->item('sound_path').$newName);
			//~ $this->db->set('voicemessage',$newName);
		//~ }
		if($_FILES['greetings']['error']==0){
			$ext=pathinfo($_FILES['greetings']['name'],PATHINFO_EXTENSION);
			$newName = "G".date('YmdHis').".".$ext;
			move_uploaded_file($_FILES['greetings']['tmp_name'],$this->config->item('sound_path').$newName);
			$this->db->set('greetings',$newName);
		}
		for($i=0;$i<sizeof($arr);$i++){
				if(!in_array($arr[$i] , array('custom','clone','update_system','oldprinumber','gid','gids'))){
					if(is_array($_POST[$arr[$i]]))
					$val = @implode(',',$_POST[$arr[$i]]); 
					elseif($_POST[$arr[$i]]!="")
						$val=$_POST[$arr[$i]];
					else
						$val='';
					$this->db->set($arr[$i],$val);
			}
		}
		if(isset($_POST['url']))$this->db->set('url', $_POST['url']);
		if(isset($_POST['bday']))$this->db->set('bday', json_encode($_POST['bday']));
		if(isset($_POST['access_reports']) && !empty($_POST['access_reports']))$this->db->set('access_reports', @implode(',',$_POST['access_reports']));
        $this->db->set('oncallaction',$this->input->post('oncallaction'));
        $this->db->set('landingregion',$this->input->post('landingregion'));
		$this->db->set('oneditaction',$this->input->post('oneditaction'));
		$this->db->set('onhangup',$this->input->post('onhangup'));
		$this->db->set('connectowner',$this->input->post('connectowner'));
		$this->db->set('replytocustomer',$this->input->post('replytocustomer'));
		$this->db->set('replytoexecutive',$this->input->post('replytoexecutive'));
		$recordnotice=(isset($_POST['recordnotice']))?"1":"0";
		$this->db->set('recordnotice',$recordnotice);
		$record_conversation=(isset($_POST['record']))?"1":"0";
		$this->db->set('record',$record_conversation);
		$sameexe=(isset($_POST['sameexe']))?"1":"0";
		$replytocust_missed=(isset($_POST['replytocust_missed']))?"1":"0";
		$this->db->set('replytocust_missed',$replytocust_missed);
		$replytocust_attended=(isset($_POST['replytocust_attended']))?"1":"0";
		$this->db->set('replytocust_attended',$replytocust_attended);
		$replytocust_repcal=(isset($_POST['replytocust_repcal']))?"1":"0";
		$this->db->set('replytocust_repcal',$replytocust_repcal);
		$replytocust_voice=(isset($_POST['replytocust_voice']))?"1":"0";
		$this->db->set('replytocust_voice',$replytocust_voice);
		$this->db->set('replytocust_voitext',$this->input->post('replytocust_voitext'));
		$this->db->set('sameexe',$sameexe);
		$allgroup=(isset($_POST['allgroup']))?"1":"0";
		$this->db->set('allgroup',$allgroup);
		$misscall=(isset($_POST['misscall']))?"1":"0";
		$this->db->set('misscall',$misscall);
		$mailalerttowoner=(isset($_POST['mailalerttowoner']))?"1":"0";
		$this->db->set('mailalerttowoner',$mailalerttowoner);
		$this->db->set('timeout',$this->input->post('timeout'));
		$groupKey=base64_encode($bid.'_'.$gid);
		$this->db->set('groupkey',$groupKey);
	    $supportgrp=(isset($_POST['supportgrp']))?$this->input->post('supportgrp'):"";
		$this->db->set('supportgrp',$supportgrp);
		//$this->db->set('cid',$this->input->post('cid'));
		$this->db->insert($bid."_groups"); 
		
		//$gid=$this->db->insert_id();
		$this->auditlog->auditlog_info($this->lang->line('level_module_group'),"Created New Group  ".$this->input->post('groupname'));
		
		$this->configmodel->supportAlert(array(
							'subject'=>$this->session->userdata('username').' create a group '.$this->input->post('groupname'),
							'message'=>'Missed Call SMS:'.$_POST['replymessage'].'<br>Received Call SMS:'.$_POST['replyattmsg']
		));
		
		$this->ivrsmodel->updatePri($_POST['prinumber'],1,$bid,0,$gid);
		if(isset($_POST['custom'])){
			$arrs=array_keys($_POST['custom']);
			for($k=0;$k<sizeof($arrs);$k++){
				
				if(is_array($_POST['custom'][$arrs[$k]])){
						$x=implode(",",$_POST['custom'][$arrs[$k]]);
					}
					else{
						$x=$_POST['custom'][$arrs[$k]];
					}
					if($x!=''){
						$this->db->query("DELETE FROM ".$bid."_customfieldsvalue where bid= '".$bid."' and modid= '3' and fieldid		= '".$arrs[$k]."' and dataid= '".$gid."'");
						$sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
							 bid			= '".$bid."'
							,modid			= '3'
							,fieldid		= '".$arrs[$k]."'
							,dataid			= '".$gid."'
							,value			= '".$x."'";
						$this->db->query($sql);
					}
				}
			}
		
		return $gid;			
	}
	function update_group($gid){
		if($_POST['gids']!=''){
			$gds=explode(",",$_POST['gids']);
			$arr=array_keys($_POST);
		for($i=0;$i<sizeof($arr);$i++){
				if(!in_array($arr[$i],array("update_system","gid","clone","gids","oldprinumber","groupname","prinumber","rules","eid","keyword"
			,"primary_rule","record","bday","hdaytext","replymessage","replytocustomer","replytoexecutive","replytoexecutive","timeout"
			,"oneditaction","onhangup","replyattmsg","leadaction","oncallaction","supportaction","supportgrp"))){
					if(is_array($_POST[$arr[$i]]))
						$val = @implode(',',$_POST[$arr[$i]]);
					elseif($_POST[$arr[$i]]!="")
						$val=$_POST[$arr[$i]];
					else
						$val='';
					$this->db->set($arr[$i],$val);
			}
		}
		foreach($gds as $gd){
		$array=array("prinumber","groupname","eid");
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($_POST['gids']==''){
			if($_POST['oldprinumber']!=$_POST['prinumber'] && $_POST['prinumber']==0){
				$sql=$this->db->query("update prinumbers set status=0 where number='".$_POST['oldprinumber']."'");
			}
			$_POST['prinumber'] = ($_POST['prinumber']!='0') ? $_POST['prinumber']:$this->ivrsmodel->getFreePri();
			if(isset($_POST['prinumber'])){
				$this->ivrsmodel->updatePri($this->db->query("SELECT prinumber FROM ".$bid."_groups WHERE gid = '".$gid."' ")->row()->prinumber);
				$this->ivrsmodel->updatePri($_POST['prinumber'],1,$bid,0,$gid);
			}
		}	
		$sf = $roleDetail['system'];
		$arr=array_keys($_POST);
		$ext='';
		if($_FILES['hdayaudio']['error']==0){
			$ext=pathinfo($_FILES['hdayaudio']['name'],PATHINFO_EXTENSION);
			$newName = "H".date('YmdHis').".".$ext;
			move_uploaded_file($_FILES['hdayaudio']['tmp_name'],$this->config->item('sound_path').$newName);
			$this->db->set('hdayaudio',$newName);
		}
	    $ext='';
		//~ if($_FILES['voicemessage']['error']==0){
			//~ $ext=pathinfo($_FILES['voicemessage']['name'],PATHINFO_EXTENSION);
			//~ $newName = "V".date('YmdHis').".".$ext;
			//~ move_uploaded_file($_FILES['voicemessage']['tmp_name'],$this->config->item('sound_path').$newName);
			//~ $this->db->set('voicemessage',$newName);
		//~ }
		$ext='';
		if($_FILES['greetings']['error']==0){
			$ext=pathinfo($_FILES['greetings']['name'],PATHINFO_EXTENSION);
			$newName = "G".date('YmdHis').".".$ext;
			move_uploaded_file($_FILES['greetings']['tmp_name'],$this->config->item('sound_path').$newName);
			$this->db->set('greetings',$newName);
		}
		foreach($sf as $f){
			$f['fieldname'] = ($f['fieldname']!='addnumber')?$f['fieldname']:'prinumber';
			if($f['modid']==3 && isset($_POST[$f['fieldname']])){
				if($_POST['gids']==''){
					if($f['fieldname'] =='replymessage'){
						$this->db->set($f['fieldname'], mysql_real_escape_string($_POST[$f['fieldname']]));
					}
					$this->db->set($f['fieldname'], $_POST[$f['fieldname']]);
					if(isset($_POST['url']))$this->db->set('url', $_POST['url']);
					if(isset($_POST['bday']))$this->db->set('bday', json_encode($_POST['bday']));
					if(isset($_POST['access_reports']) && !empty($_POST['access_reports']))$this->db->set('access_reports', @implode(',',$_POST['access_reports']));
				}else{
					if(!in_array($f['fieldname'],$array)){
						if($f['fieldname'] =='replymessage'){
							$this->db->set($f['fieldname'], mysql_real_escape_string($_POST[$f['fieldname']]));
						}
						$this->db->set($f['fieldname'], $_POST[$f['fieldname']]);
						if(isset($_POST['url']))$this->db->set('url', $_POST['url']);
						if(isset($_POST['bday']))$this->db->set('bday', json_encode($_POST['bday']));
						if(isset($_POST['access_reports']) && !empty($_POST['access_reports']))$this->db->set('access_reports', @implode(',',$_POST['access_reports']));
					}
				 
				}
			}
		}
		$replytocust_missed=(isset($_POST['replytocust_missed']))?"1":"0";
		$this->db->set('replytocust_missed',$replytocust_missed);
		$replytocust_attended=(isset($_POST['replytocust_attended']))?"1":"0";
		$this->db->set('replytocust_attended',$replytocust_attended);
		$replytocust_repcal=(isset($_POST['replytocust_repcal']))?"1":"0";
		$this->db->set('replytocust_repcal',$replytocust_repcal);
		$replytocust_voice=(isset($_POST['replytocust_voice']))?"1":"0";
		$this->db->set('replytocust_voice',$replytocust_voice);
		$this->db->set('replytocust_voitext',$this->input->post('replytocust_voitext'));
		$connectowner=(isset($_POST['connectowner']))?"1":"0";
		$this->db->set('connectowner',$connectowner);
		$rply_check=(isset($_POST['replytocustomer']))?"1":"0";
		$this->db->set('replytocustomer',$rply_check);
		$rply_check1=(isset($_POST['replytoexecutive']))?"1":"0";
		$this->db->set('replytoexecutive',$rply_check1);
		$recordnotice=(isset($_POST['recordnotice']))?"1":"0";
		$this->db->set('recordnotice',$recordnotice);
		$record_conversation=(isset($_POST['record']))?"1":"0";
		$this->db->set('record',$record_conversation);
		$sameexe=(isset($_POST['sameexe']))?"1":"0";
		$this->db->set('sameexe',$sameexe);
		$misscall=(isset($_POST['misscall']))?"1":"0";
		$this->db->set('misscall',$misscall);
		$pincode=(isset($_POST['pincode']))?"1":"0";
		$this->db->set('pincode',$pincode);
		$allgroup=$this->input->post('allgroup');
		$this->db->set('allgroup',$allgroup);
		$mailalerttowoner=(isset($_POST['mailalerttowoner']))?"1":"0";
		$this->db->set('mailalerttowoner',$mailalerttowoner);
		$this->db->set('timeout',$this->input->post('timeout'));
		$this->db->set('oncallaction',$this->input->post('oncallaction'));
		$this->db->set('landingregion',$this->input->post('landingregion'));
		$this->db->set('oneditaction',$this->input->post('oneditaction'));
		$this->db->set('onhangup',$this->input->post('onhangup'));
		$this->db->set('supportgrp',$this->input->post('supportgrp'));
		$groupKey=base64_encode($bid.'_'.$gid);
		$this->db->set('groupkey',$groupKey);
		$this->db->where('gid',$gd);
		$this->db->update($bid."_groups");
		$this->db->query("UPDATE ".$bid."_group_emp SET callcounter='0' WHERE gid='".$gd."'");
		$this->auditlog->auditlog_info($this->lang->line('level_module_group'),"Updated Group  ".$this->input->post('groupname'));
		}
		}else{
		$array=array("prinumber","groupname","eid");
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($_POST['gids']==''){
			if($_POST['oldprinumber']!=$_POST['prinumber'] && $_POST['prinumber']==0){
				$sql=$this->db->query("update prinumbers set status=0 where number='".$_POST['oldprinumber']."'");
			}
			$_POST['prinumber'] = ($_POST['prinumber']!='0') ? $_POST['prinumber']:$this->ivrsmodel->getFreePri();
			if(isset($_POST['prinumber'])){
				$this->ivrsmodel->updatePri($this->db->query("SELECT prinumber FROM ".$bid."_groups WHERE gid = '".$gid."' ")->row()->prinumber);
				$this->ivrsmodel->updatePri($_POST['prinumber'],1,$bid,0,$gid);
			}
		}	
		$sf = $roleDetail['system'];
		$arr=array_keys($_POST);
		for($i=0;$i<sizeof($arr);$i++){
			if(!in_array($arr[$i],array("update_system","gid","clone","gids","oldprinumber","groupname","prinumber","rules","eid","keyword"
			,"primary_rule","record","bday","hdaytext","replymessage","replytocustomer","replytoexecutive","replytoexecutive","timeout"
			,"oneditaction","onhangup","replyattmsg","leadaction","oncallaction","supportaction","supportgrp"))){
			if(is_array($_POST[$arr[$i]]))
				$val = @implode(',',$_POST[$arr[$i]]);
			elseif($_POST[$arr[$i]]!="")
				$val=$_POST[$arr[$i]];
			else
				$val='';
			$this->db->set($arr[$i],$val);

			}
		}
		$ext='';
		if($_FILES['hdayaudio']['error']==0){
			$ext=pathinfo($_FILES['hdayaudio']['name'],PATHINFO_EXTENSION);
			$newName = "H".date('YmdHis').".".$ext;
			move_uploaded_file($_FILES['hdayaudio']['tmp_name'],$this->config->item('sound_path').$newName);
			$this->db->set('hdayaudio',$newName);
		}
	   //~ $ext='';
	   //~ if($_FILES['voicemessage']['error']==0){
			//~ $ext=pathinfo($_FILES['voicemessage']['name'],PATHINFO_EXTENSION);
			//~ $newName = "V".date('YmdHis').".".$ext;
			//~ move_uploaded_file($_FILES['voicemessage']['tmp_name'],$this->config->item('sound_path').$newName);
			//~ $this->db->set('voicemessage',$newName);
		//~ }

		$ext='';
		if($_FILES['greetings']['error']==0){
			$ext=pathinfo($_FILES['greetings']['name'],PATHINFO_EXTENSION);
			$newName = "G".date('YmdHis').".".$ext;
			move_uploaded_file($_FILES['greetings']['tmp_name'],$this->config->item('sound_path').$newName);
			$this->db->set('greetings',$newName);
		}
		foreach($sf as $f){
			$f['fieldname'] = ($f['fieldname']!='addnumber')?$f['fieldname']:'prinumber';
			if($f['modid']==3 && isset($_POST[$f['fieldname']])){
				if($_POST['gids']==''){
				if($f['fieldname'] =='replymessage'){
					$this->db->set($f['fieldname'], mysql_real_escape_string($_POST[$f['fieldname']]));
				}
				$this->db->set($f['fieldname'], $_POST[$f['fieldname']]);
				 if(isset($_POST['url']))$this->db->set('url', $_POST['url']);
				 if(isset($_POST['bday']))$this->db->set('bday', json_encode($_POST['bday']));
				 if(isset($_POST['access_reports']) && !empty($_POST['access_reports']))$this->db->set('access_reports', @implode(',',$_POST['access_reports']));
				}else{
					if(!in_array($f['fieldname'],$array)){
						if($f['fieldname'] =='replymessage'){
							$this->db->set($f['fieldname'], mysql_real_escape_string($_POST[$f['fieldname']]));
						}
						$this->db->set($f['fieldname'], $_POST[$f['fieldname']]);
						if(isset($_POST['url']))$this->db->set('url', $_POST['url']);
						if(isset($_POST['bday']))$this->db->set('bday', json_encode($_POST['bday']));
						if(isset($_POST['access_reports']) && !empty($_POST['access_reports']))$this->db->set('access_reports', @implode(',',$_POST['access_reports']));
					}
				}
			}
		}
		$replytocust_missed=(isset($_POST['replytocust_missed']))?"1":"0";
		$this->db->set('replytocust_missed',$replytocust_missed);
		$replytocust_attended=(isset($_POST['replytocust_attended']))?"1":"0";
		$this->db->set('replytocust_attended',$replytocust_attended);
		$replytocust_repcal=(isset($_POST['replytocust_repcal']))?"1":"0";
		$this->db->set('replytocust_repcal',$replytocust_repcal);
		$replytocust_voice=(isset($_POST['replytocust_voice']))?"1":"0";
		$this->db->set('replytocust_voice',$replytocust_voice);
		$this->db->set('replytocust_voitext',$this->input->post('replytocust_voitext'));
		$connectowner=(isset($_POST['connectowner']))?"1":"0";
		$this->db->set('connectowner',$connectowner);
		$rply_check=(isset($_POST['replytocustomer']))?"1":"0";
		$this->db->set('replytocustomer',$rply_check);
		$rply_check1=(isset($_POST['replytoexecutive']))?"1":"0";
		$this->db->set('replytoexecutive',$rply_check1);
		$recordnotice=(isset($_POST['recordnotice']))?"1":"0";
		$this->db->set('recordnotice',$recordnotice);
		$record_conversation=(isset($_POST['record']))?"1":"0";
		$this->db->set('record',$record_conversation);
		$sameexe=(isset($_POST['sameexe']))?"1":"0";
		$this->db->set('sameexe',$sameexe);
		$misscall=(isset($_POST['misscall']))?"1":"0";
		$this->db->set('misscall',$misscall);
		$pincode=(isset($_POST['pincode']))?"1":"0";
		$this->db->set('pincode',$pincode);
		$allgroup=$this->input->post('allgroup');
		$this->db->set('allgroup',$allgroup);
		$mailalerttowoner=(isset($_POST['mailalerttowoner']))?"1":"0";
		$this->db->set('mailalerttowoner',$mailalerttowoner);
		$this->db->set('timeout',$this->input->post('timeout'));
		$this->db->set('oncallaction',$this->input->post('oncallaction'));
		$this->db->set('landingregion',$this->input->post('landingregion'));
		$this->db->set('oneditaction',$this->input->post('oneditaction'));
		$this->db->set('onhangup',$this->input->post('onhangup'));
		$this->db->set('supportgrp',$this->input->post('supportgrp'));
		$groupKey=base64_encode($bid.'_'.$gid);
		$this->db->set('groupkey',$groupKey);
		$this->db->where('gid',$gid);
		$this->db->update($bid."_groups");
		$this->db->query("UPDATE ".$bid."_group_emp SET callcounter='0' WHERE gid='".$gid."'");
		$this->auditlog->auditlog_info($this->lang->line('level_module_group'),"Updated Group  ".$this->input->post('groupname'));
		}  
		return 1;
	}
	function Update_UNdeletegroup($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$_POST['prinumber'] = ($_POST['prinumber']!='0') ? $_POST['prinumber']:$this->ivrsmodel->getFreePri();
		$this->db->set('status','1');
		$this->db->set('prinumber',$_POST['prinumber']);
		$this->db->where('gid',$id);
		$this->db->update($bid."_groups"); 
		$this->ivrsmodel->updatePri($_POST['prinumber'],1,$bid,0,$id);
		$this->auditlog->auditlog_info($this->lang->line('level_module_group'),"Updated Group  ".$this->input->post('groupname'));
		return true;
	}
	function getgrouplist($bid,$ofset='0',$limit='20'){
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		if(isset($s)){
            $arr = array_keys($s);
            for ($n =0;$n<count($arr);$n++){
                if(strstr($arr[$n],'c_')){
					if(is_array($s[$arr[$n]])){
						$s[$arr[$n]] = @implode(',',$s[$arr[$n]]);
					}
                    $q.=(isset($s[$arr[$n]]) && $s[$arr[$n]]!='' && $s[$arr[$n]]!=' ') ? " AND g.".$arr[$n]."= '".$s[$arr[$n]]."'":"";
                }
            }
        }
		$q.=(isset($s['groupname']) && $s['groupname']!='')?" and g.groupname like '%".$s['groupname']."%'":"";
		$q.=(isset($s['addnumber']) && $s['addnumber']!='')?" and p.landingnumber like '%".$s['addnumber']."%'":"";
		$q.=(isset($s['rules']) && $s['rules']!='')?" and r.rulename like '%".$s['rules']."%'":"";
		$q.=(isset($s['empid']) && $s['empid']!='' && $s['empid']!='0')?" and g.eid = '".$s['empid']."'":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q.=($roleDetail['role']['owngroup']=='1' && $roleDetail['role']['admin']!='1') ? " AND g.eid = '".$this->session->userdata('eid')."'":"";
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$sql="SELECT SQL_CALC_FOUND_ROWS g.*
				  FROM ".$bid."_groups g 
				  LEFT JOIN group_rules r on g.rules=r.rulesid 
				  LEFT JOIN ".$bid."_employee e on g.eid=e.eid
				  LEFT JOIN prinumbers p on (g.prinumber=p.number AND p.bid='".$bid."')
				  WHERE g.bid='".$bid."' 
				  and g.status!=0 $q limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='3'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('3',$bid);
		$keys = array();
		$header = array('#');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){

					if(!in_array($field['fieldname'],array('hdayaudio','greetings','url'))){

						$supChk = ($field['fieldname'] == 'supportgrp') ? $this->supportNumberChk($bid) : '1' ;
						if($supChk == 1){
							array_push($keys,$field['fieldname']);
							array_push($header,(($field['customlabel']!="")
											?$field['customlabel']
											:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
						}
					}
				}
			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldKey']);
					array_push($header,$field['customlabel']);
				}
			}
		}
		if($opt_add || $opt_view || $opt_delete)
			array_push($header,$this->lang->line('level_Action'));
		    array_push($header,'Weekly Report');
		$ret['header'] = $header;
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$data = array($i);
			$r = $this->configmodel->getDetail('3',$rec['gid'],'',$bid);
			foreach($keys as $k){
				if($k=='eid'){
					$v = '<a href="Employee/activerecords/'.$r['epid'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r[$k].'</a>';
				}elseif($k=='supportgrp'){
					$v = $r['supportgroup'];
				}elseif($k=='primary_rule'){
					$v = isset($r['regionname'])?$r['regionname']:"";
				}elseif($k=='rules'){
					$v = ($r['url']!='')?'<a title="'.$r['url'].'">'.$r[$k].'</a>':$r[$k];
				}elseif($k=='bday' && $r[$k]!=''){
					$bday = json_decode($r[$k]);
					$v = '';
					foreach($bday as $b => $d){ $v .= (isset($d->day) && $d->day=='1')?$b.'='.$d->st.'-'.$d->et.'<br>':'';}
				}elseif(in_array($k ,array( 'replytocustomer',
											'replytoexecutive',
											'connectowner',
											'record',
											'recordnotice',
											'sameexe',
											'misscall',
											'pincode',
											'allgroup'
										
											))){
					$v = (isset($r[$k]) && $r[$k]=='1')?"Yes":"No";
				}elseif($k=='access_reports'){
					$empl = $this->empgetnames($r[$k],$bid);
					$v = $empl;
				}else{
					$v = isset($r[$k])?$r[$k]:"";
				}
				array_push($data,$v);
			}
			if($opt_add || $opt_view || $opt_delete){
				$act = "";
				$act .= ($opt_add) ? '<a href="EditGroup/'.$r['gid'].'"><span title="Edit" class="fa fa-edit"></span></a>':'';
				$act .= ($opt_view) ? '&nbsp;<a href="group/activerecords/'.$r['gid'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="fa fa-file-text"  title="View Group"></span></a>':'';
				$act .= ($opt_delete) ? '&nbsp;<a href="'.base_url().'group/Delete_group/'.$r['gid'].'" class="deleteClass" title="Are you sure to Delete the group"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
				$act .= ($opt_add) ? '&nbsp;<a href="AddempGroup/'.$r['gid'].'"><span class="fa fa-plus" title="Add Employee"></span></a>':'';
				$act .= ($opt_view) ? '&nbsp;<a href="ListempGroup/'.$r['gid'].'"><span title="List Employees" class="glyphicon glyphicon-user"></span></a>':'';
				$act .= ($opt_add) ? '&nbsp;<a href="group/add_group/'.$r['gid'].'/clone"><span title="Clone Group" class="fa fa-clipboard"></span></a>':'';
				array_push($data,$act);
			}
			$ret['graph'] = $this->recent_calls($r['gid']);
			array_push($data,$ret['graph'] );
			$i++;
			array_push($list,$data);
		}	
		$ret['rec'] = $list;
		//echo "<pre>"; print_r($ret['rec']); exit;
		return $ret;
	} 
	function recent_calls($gid){
		$res=array();
		$ret=array();
		$sql=$this->db->query("SELECT COALESCE(COUNT(callid), 0) AS cnt
							FROM ".$this->session->userdata('bid')."_callhistory h 
							WHERE (h.`starttime` >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)) AND h.gid = ".$gid." GROUP BY DAY(h.starttime)");
							

		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}
		foreach($res as $item){
		    foreach($item as $im){
	         $ret[] = $im;
        }
	  } 
		return $ret;
	}	
	function getdeletedgrouplist($bid,$ofset='0',$limit='20'){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		if(isset($s)){
            $arr = array_keys($s);
            for ($n =0;$n<count($arr);$n++){
                if(strstr($arr[$n],'c_')){
					if(is_array($s[$arr[$n]])){
						$s[$arr[$n]] = @implode(',',$s[$arr[$n]]);
					}
                    $q.=(isset($s[$arr[$n]]) && $s[$arr[$n]]!='' && $s[$arr[$n]]!=' ') ? " AND g.".$arr[$n]."= '".$s[$arr[$n]]."'":"";
                }
            }
        }
		$q.=(isset($s['groupname']) && $s['groupname']!='')?" and g.groupname like '%".$s['groupname']."%'":"";
		$q.=(isset($s['rules']) && $s['rules']!='')?" and r.rulename like '%".$s['rules']."%'":"";
		$q.=(isset($s['eid']) && $s['eid']!='' && $s['eid']!='0')?" and g.eid = '".$s['eid']."'":"";
		$sql="SELECT SQL_CALC_FOUND_ROWS g.gid
				  FROM ".$bid."_groups g 
				  LEFT JOIN group_rules r on g.rules=r.rulesid 
				  LEFT JOIN ".$bid."_employee e on g.eid=e.eid
				  WHERE g.bid='".$bid."' 
				  and g.status=0  $q limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='3'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('3',$bid);
		$keys = array();
		$header = array('#');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					if(!in_array($field['fieldname'],array('hdayaudio','greetings'))){

						array_push($keys,$field['fieldname']);
						array_push($header,(($field['customlabel']!="")
											?$field['customlabel']
											:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
					}
				}
		}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldKey']);
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
			$r = $this->configmodel->getDetail('3',$rec['gid'],'',$bid);
			foreach($keys as $k){
				if($k=='eid'){
					$v = '<a href="Employee/activerecords/'.$r['epid'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r[$k].'</a>';
				}elseif($k=='supportgrp'){
					$v = $r['supportgroup'];
				}elseif($k=='primary_rule'){
					$v = isset($r['regionname'])?$r['regionname']:"";
				}elseif($k=='rules'){
					$v = ($r['url']!='')?'<a title="'.$r['url'].'">'.$r[$k].'</a>':$r[$k];
				}elseif($k=='bday' && $r[$k]!=''){
					$bday = json_decode($r[$k]);
					$v = '';
					foreach($bday as $b => $d){ $v .= (isset($d->day) && $d->day=='1')?$b.'='.$d->st.'-'.$d->et.'<br>':'';}
				}elseif(in_array($k ,array( 'replytocustomer',
											'replytoexecutive',
											'connectowner',
											'record',
											'recordnotice',
											'sameexe',
											'misscall',
											'pincode',
											'allgroup'
											))){
					$v = (isset($r[$k]) && $r[$k]=='1')?"Yes":"No";
				}elseif($k=='access_reports'){
					$empl = $this->empgetnames($r[$k],$bid);
					$v = $empl;
				}else{
					$v = isset($r[$k])?$r[$k]:"";
				}
				array_push($data,$v);
			}
			if($opt_add || $opt_view || $opt_delete){
				$act = '<a href="'.base_url().'group/UNDelete_group/'.$r['gid'].'" class="deleteClass1" data-toggle="modal" data-target="#modal-responsive" id="'.$r['gid'].'"><img src="system/application/img/icons/undelete.png" title="Undelete" style="vertical-align:top;" /></a>';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	} 
	function custom_group_list($type=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();	
		$sql=$this->db->query("SELECT a.`fieldid`,b.value,b.typeid from ".$bid."_metafields a,".$bid."_metafieldsvalue b where a.bid=b.bid and a.`fieldid`=b.`fieldid` and a.bid=".$bid);
		if($sql->num_rows()>0){
			$rer=$sql->result_array();
			foreach($rer as $rec){
				$res[$rec['fieldid']."~".$rec['typeid']] = $rec['value'];
			}
		}	
		return $res;
	}
	function get_group($gid){	
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("SELECT p.package_id, c.eid as empcount, a.gid, a.bid, a.eid, a.`groupname` , a.`prinumber` , a.rules FROM ".$bid."_groups a LEFT JOIN prinumbers p ON a.prinumber = p.number LEFT JOIN package c ON p.package_id = c.package_id WHERE a.gid =$gid");
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}	
		return $res;
	}
	function get_custom_group($gid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$ret=array();
		$sql=$this->db->query("select * from ".$bid."_metafieldsvalue where typeid=$gid");
		if($sql->num_rows()>0){
			$res=$sql->result_array();
			foreach($res as $rec)
				$ret[$rec['fieldid']] = $rec['value'];
		}	
		return $ret;
	}
	function employee_list($grid=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res = ($grid=='') ? array('0'=>'Select Employee') : array();
		$query=$this->db->query("select eid,empname from ".$bid."_employee where status=1 ORDER BY empname");
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q='';
		$q.=($roleDetail['role']['owngroup']=='1' && $roleDetail['role']['admin']!='1') ? " AND (g.eid = '".$this->session->userdata('eid')."' OR e.eid='".$this->session->userdata('eid')."')":"";
		$q .= ($grid=='') ? '' : " AND g.gid='".$grid."'";
		$query = ($grid=='') ? "SELECT * FROM ".$bid."_employee WHERE status='1' ORDER BY empname" : "select e.* from ".$bid."_employee e
								 LEFT JOIN ".$bid."_group_emp ge on e.eid=ge.eid
								 LEFT JOIN ".$bid."_groups g on g.gid=ge.gid
								 where e.status=1 ".$q." ORDER BY e.empname";
		$query=$this->db->query($query);
		if($query->num_rows()>0){
			foreach($query->result_array() as $rt)
			$res[$rt['eid']]=$rt['empname'];
		}
		return $res;
	}
	function empllist($grid=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res = ($grid=='') ? array('0'=>'Select Employee') : array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q='';
		$q.=($roleDetail['role']['owngroup']=='1' && $roleDetail['role']['admin']!='1') ? " AND (g.eid = '".$this->session->userdata('eid')."' OR e.eid='".$this->session->userdata('eid')."')":"";
		$query = "SELECT e.* FROM ".$bid."_employee e LEFT JOIN ".$bid."_group_emp ge ON e.eid=ge.eid LEFT JOIN ".$bid."_groups g ON g.gid=ge.gid WHERE e.status=1 ".$q." ORDER BY e.empname";
		$query=$this->db->query($query);
		if($query->num_rows()>0){
			foreach($query->result_array() as $rt)
			$res[$rt['eid']]=$rt['empname'];
		}
		return $res;
	}
	function addemp_group($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$err=0;
		$rule = $this->db->query("SELECT rules FROM ".$bid."_groups where gid='".$id."'")->row()->rules;
		$cnt = ($rule=='1')
			? $this->db->query("SELECT COALESCE(max(callcounter),0) as cnt FROM ".$bid."_group_emp where gid='".$id."'")->row()->cnt
			: '0';
		foreach($_POST['emp_ids'] as $eids){
			$check=$this->db->query("select callid from ".$bid."_group_emp where eid=".$eids." and gid='".$id."'");
			if($check->num_rows()==0){
				$err++;
				$this->db->set('bid', $bid);                       
				$this->db->set('gid', $id);                       
				$this->db->set('eid', $eids); 
				$this->db->set('starttime', $this->input->post('starttime'.$eids));                       
				$this->db->set('endtime', $this->input->post('endtime'.$eids));                       
				$this->db->set('status',1);
				$this->db->set('callcounter',$cnt);
				if($this->input->post('area_code'.$eids)){
					$this->db->set('area_code', $this->input->post('area_code'.$eids)); 
				}
				if($this->input->post('empweight'.$eids)){
					$this->db->set('empweight', $this->input->post('empweight'.$eids)); 
				}
				if($this->input->post('empPriority'.$eids)){
					$this->db->set('empPriority', $this->input->post('empPriority'.$eids)); 
				}
				if($this->input->post('isfailover'.$eids)){
					$this->db->set('isfailover',$this->input->post('isfailover'.$eids));	
				} 
				if($this->input->post('pcode'.$eids)){
					$this->db->set('pincode', $this->input->post('pcode'.$eids)); 
				} 
				$this->db->insert($bid."_group_emp");
				$emp_name=$this->get_empname($eids);
				$gname=$this->db->query("SELECT groupname from 	".$bid."_groups where gid='".$id."'")->row()->groupname;
				$this->auditlog->auditlog_info('Group Employee',$emp_name->empname." added to the group ".$gname);
			}
		}
		if($rule!='1'){
			$query=$this->db->query("update ".$bid."_group_emp set callcounter=0 where gid='".$id."'");
		}
		if($err!=0){
			return 1;
		}else{
			return 0;
		}
	}
	function editemp_group(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$rule = $this->db->query("SELECT rules FROM ".$bid."_groups where gid='".$_POST['gid']."'")->row()->rules;
		$cnt = ($rule=='1')
			? $this->db->query("SELECT COALESCE(max(callcounter),0) as cnt FROM ".$bid."_group_emp where gid='".$_POST['gid']."'")->row()->cnt
			: '0';
		$sql = "UPDATE ".$bid."_group_emp SET
				empweight	= '".(isset($_POST['empweight'])?$_POST['empweight']:0)."'
				,empPriority= '".(isset($_POST['empPriority'])?$_POST['empPriority']:0)."'
				,area_code	= '".(isset($_POST['area_code'])?$_POST['area_code']:0)."'
				,starttime	= '".$_POST['starttime']."'
				,endtime	= '".$_POST['endtime']."'
				,pincode	= '".(isset($_POST['pcode'])?$_POST['pcode']:'')."'
				,callcounter= '".$cnt."'
				,isfailover	= '".(isset($_POST['isfailover'])?$_POST['isfailover']:0)."'
				WHERE gid		= '".$_POST['gid']."'
				AND  eid		= '".$_POST['empid']."'";
		$this->db->query($sql);
		if($rule!='1'){
			$query=$this->db->query("update ".$bid."_group_emp set callcounter=0 where gid='".$_POST['gid']."'");
		}
		$emp_name=$this->get_empname($_POST['empid']);
		$gname=$this->db->query("SELECT groupname from 	".$bid."_groups where gid='".$_POST['gid']."'")->row()->groupname;
		$this->auditlog->auditlog_info('Group Employee',$emp_name->empname." weight changed for the group ".$gname);
		return 1;
	}

	function get_empname($eid)
	{
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("select * from ".$bid."_employee where eid=$eid");
		$res=$sql->row();
		return $res;
	}
	function groupemplist($gid,$ofset='0',$limit='20'){
		$q='';
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		
		$q= (isset($s['empname']) && $s['empname']!='')?" AND e.empname like '%".$s['empname']."%'":"";
		$res=array();
		$res['data']=$this->db->query("select SQL_CALC_FOUND_ROWS e.empnumber,a.empweight,a.empPriority,
								a.starttime,a.endtime,g.groupname,a.callcounter,e.empname,a.eid,a.gid ,
								if(a.isfailover=1,'yes','no') as failover,a.status,
								if(e.status='0',0,if(e.selfdisable='1','0','1')) as estatus,
								r.regionname as region	
								from ".$bid."_group_emp a
								left join ".$bid."_groups g on a.gid=g.gid
								left join ".$bid."_employee e on a.eid=e.eid
								LEFT JOIN ".$bid."_group_region r on r.gregionid=a.area_code
								where a.gid=".$gid." $q
								ORDER BY e.`empname`
								LIMIT $ofset,$limit
							   ")->result_array();
       $res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
	   return $res;
	}
	function group_enteremplist($gid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();
		$res1=$this->db->query("select SQL_CALC_FOUND_ROWS a.empnumber,a.empweight,
								a.starttime,a.endtime,g.groupname,e.empname,a.eid,a.gid ,
								if(a.isfailover=1,'yes','no') as failover,a.status,
								r.regionname as region	
								from ".$bid."_group_emp a
								left join ".$bid."_groups g on a.gid=g.gid
								left join ".$bid."_employee e on a.eid=e.eid
								LEFT JOIN ".$bid."_group_region r on r.gregionid=a.area_code
								where a.gid=$gid
							   ");
		if($res1->num_rows()>0){					   
			foreach($res1->result_array() as $row){
				$res[]=$row['eid'];
			}
		}
		return $res;
	}
	function group_enteremplist1(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();
		$res1=$this->db->query("select SQL_CALC_FOUND_ROWS a.empnumber,a.empweight,
								a.starttime,a.endtime,g.groupname,e.empname,a.eid,a.gid ,
								if(a.isfailover=1,'yes','no') as failover,a.status,
								r.regionname as region	
								from ".$bid."_group_emp a
								left join ".$bid."_groups g on a.gid=g.gid
								left join ".$bid."_employee e on a.eid=e.eid
								LEFT JOIN ".$bid."_group_region r on r.gregionid=a.area_code
								where g.bid=$bid
							   ");
		if($res1->num_rows()>0){					   
			foreach($res1->result_array() as $row){
				$res[]=$row['eid'];
			}
		}
		return $res;
	}
	function getGroupEmpDetail($gid='',$eid=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();
		$res=$this->db->query("select a.*,e.empname
							   from ".$bid."_group_emp a
							   left join ".$bid."_employee e on a.eid=e.eid
							   where a.gid='".$gid."' and a.eid='".$eid."'")->result_array();
		return $res['0'];
	}
	
	function delete_grp($id,$gid)
	{
	$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("Delete from ".$bid."_group_emp where eid=$id and gid=$gid");
		$query=$this->db->query("update ".$bid."_group_emp set callcounter=0 where gid=$id");
		$emp_name=$this->get_empname($id);
		$gname=$this->db->query("SELECT groupname from 	".$bid."_groups where gid='".$gid."'")->row()->groupname;
		$this->auditlog->auditlog_info('Group Employee',$emp_name->empname." is Removed From the Group ".$gname);
		return true;
	}
	function delete_group($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$this->db->set('status',0); 
		$this->db->where('gid', $id); 
		$this->db->update($bid."_group_emp"); 
		$sql1=$this->db->query("update ".$bid."_groups set status=0 where gid=".$id);
		$gdetails=$this->configmodel->getDetail('3',$id,'',$bid);
		$this->ivrsmodel->updatePri($gdetails['prinumber']);
		$this->auditlog->auditlog_info($this->lang->line('level_module_group'),$gdetails['groupname']." is Deleted ");
		
		return true;
	}	
	function undelete_group($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$gdetails=$this->configmodel->getDetail('3',$id,'',$bid);
		$q = ($gdetails['prinumber']>10000)
				?"select landingnumber from prinumber where status=0 and bid=".$bid." and number='".$gdetails['prinumber']."'"
				:"select landingnumber from prinumber where status=0 and number='".$gdetails['prinumber']."'";
		$sql=$this->db->query($q);
		if($sql->num_rows()>0){
			$this->db->set('status',1); 
			$this->db->where('gid', $id); 
			$this->db->update($bid."_group_emp"); 
			$sql1=$this->db->query("update ".$bid."_groups set status=1 where gid=".$id);
			$this->ivrsmodel->updatePri($gdetails['prinumber'],1,$bid,0,$id);
			$this->auditlog->auditlog_info($this->lang->line('level_module_group'),$gdetails['groupname']." is UnDeleted ");
			return "succ";
		}else{
			return "err";
		}
	}	
	
	function emplist()
	{
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$opt='';
		$res=$this->db->query("select eid,empname,empnumber from ".$bid."_employee where status!=''");
		if($res->num_rows()>0){
			foreach($res->result_array() as $re)
				$opt.="<option value=".$re['eid'].">".$re['empname'].".[".$re['empnumber']."]</option>";
		}
		return $opt;
	}
	function confirmundelete($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$this->db->set('status',1); 
		$this->db->where('gid', $id); 
		$this->db->update($bid."_group_emp"); 
		$landing=$this->db->query("select number from prinumber where status=0 and bid=".$bid." limit 0,1")->row()->number;
		if($landing!=""){
			$sql1=$this->db->query("update ".$bid."_groups set status=1,prinumber=$landing where gid=".$id);
			$this->ivrsmodel->updatePri($landing,1,$bid,0,$id);
			
			return true;
		}
		else{
			return false;
		}
	}
	function getAutodiallerList($refer_id,$ofset,$limit){
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$sql = "SELECT SQL_CALC_FOUND_ROWS a.*,g.groupname,e.empname
					  FROM ".$this->session->userdata('bid')."_autodialler a 
					  LEFT JOIN ".$this->session->userdata('bid')."_groups g on a.gid=g.gid
					  LEFT JOIN ".$this->session->userdata('bid')."_employee e on a.eid=e.eid
					  WHERE a.bid='".$this->session->userdata('bid')."' 
					  AND a.status!='3' AND a.refer_id=".$refer_id;
		$sql .= ($this->session->userdata('eid')==1)?''
				:" AND a.gid in (SELECT gid FROM ".$this->session->userdata('bid')."_group_emp WHERE eid='".$this->session->userdata('eid')."') 
				   OR a.gid in (SELECT gid FROM ".$this->session->userdata('bid')."_groups WHERE eid='".$this->session->userdata('eid')."') ";
		$sql .= " limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		$ret['header'] = array(
						'#'
						,$this->lang->line('label_autodiallernumber')
						,$this->lang->line('label_autodiallergroup')
						,$this->lang->line('label_autodialleremp')
						,$this->lang->line('level_Action')
					);
		
		$list = array();
		$i =$ofset+1;
		foreach($rst as $rec){
			if($rec['status']=='0')
				$act = ' <a class="callconnect" href="group/callconnect/'.$rec['did'].'"><span title="Connect" class="fa fa-phone"></span></a>';
			elseif($rec['status']=='1' && $rec['eid']==$this->session->userdata('eid'))
				$act = ' <img src="system/application/img/icons/lock.png" style="vertical-align:top;" title="Record in process" />';
			elseif($rec['status']=='2' && $rec['eid']==$this->session->userdata('eid'))
				$act = ' <a href="group/autoedit/'.$rec['did'].'"><span title="Edit" class="fa fa-edit"></span></a>';
			else
				$act = ' <img src="system/application/img/icons/lock.png" style="vertical-align:top;" title="Record is lock by anathor user" />';
				
				$act .= ' <a class="btn-danger" data-toggle="modal" data-target="#modal-responsive" href="group/autodetail/'.$rec['did'].'"><img src="system/application/img/icons/page_gear.png" style="vertical-align:top;" title="View detail" /></a>';
			
			$data = array(
						$i
						,$rec['number']
						,$rec['groupname']
						,$rec['empname']
						,$act
					);
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function AutoDailerList($bid,$ofset,$limit){
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$sql = "SELECT SQL_CALC_FOUND_ROWS a.*,g.groupname
					  FROM ".$this->session->userdata('bid')."_autodailerlist a 
					  LEFT JOIN ".$this->session->userdata('bid')."_groups g on a.gid=g.gid
					  WHERE a.status!='3' ";
		$sql .= ($this->session->userdata('eid')==1)?''
				:" AND a.gid in (SELECT gid FROM ".$this->session->userdata('bid')."_group_emp 
				   WHERE eid='".$this->session->userdata('eid')."') 
				  OR a.gid in (SELECT gid FROM ".$this->session->userdata('bid')."_groups 
				   WHERE eid='".$this->session->userdata('eid')."') ";
		$sql .= " limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		$ret['header'] = array(
						'#'
						,$this->lang->line('label_autodiallernumber')
						,$this->lang->line('label_autodiallergroup')
						);
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
				$data = array(
						$i
						,'<a href="group/getAutoCalls/'.$rec['id'].'">'.$rec['title'].'</a>'
						,$rec['groupname']
					);
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function callconnect($did){
		$sql = "UPDATE ".$this->session->userdata('bid')."_autodialler SET
				 eid			= '".$this->session->userdata('eid')."'
				,status			= '1'
				WHERE did='".$did."'";
		$this->db->query($sql);
		$sql = "SELECT a.*,e.empnumber FROM ".$this->session->userdata('bid')."_autodialler a
				LEFT JOIN ".$this->session->userdata('bid')."_employee e on a.eid=e.eid
				WHERE a.did='".$did."'";
		$rst = $this->db->query($sql)->row_array();
		$hid = time().$rst['number'];
		$sql = "INSERT INTO ".$this->session->userdata('bid')."_autodialhistory SET
				hid			='".$hid."',
				bid			='".$this->session->userdata('bid')."',
				did			='".$rst['did']."',
				eid			='".$rst['eid']."',
				calltime	='".date('Y-m-d H:i:s')."',
				recordfile	='". $hid.'.wav' ."'";
		$this->db->query($sql);
		$url = 'http://203.122.14.85/mcube/autodialer.php?';
		$data = array('key'			=> '50be1c4031bc43bb164abe49fcfb5ef0',
					  'executive'	=> $rst['empnumber'],
					  'cust'		=> $rst['number'],
					  'bid'			=> $this->session->userdata('bid'),
					  'did'			=> $rst['did'],
					  'hid'			=> $hid,
					  'server'		=> '1'
					);
		$objURL = curl_init($url);
		curl_setopt($objURL, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($objURL,CURLOPT_POST,1);
		curl_setopt($objURL, CURLOPT_POSTFIELDS,$data);
		$retval = trim(curl_exec($objURL));
		if($retval === false){
		}else{
			return true;
		}
	}
	function addautodialler(){
			$listid=$this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM ".$this->session->userdata('bid')."_autodailerlist")->row()->id;
			$this->db->set('title',$this->input->post('title')); 
			$this->db->set('gid',$this->input->post('gid')); 
			$this->db->set('id',$listid); 
			$this->db->insert($this->session->userdata('bid')."_autodailerlist");
			if (($handle = fopen($_FILES['filename']['tmp_name'],"r")) !== FALSE) {
				$i=0;
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					if($i>0){
						$c = 0;
						foreach($fields as $f){
							$others[$f] = $data[$c];
							$c++;
						}
						
						$did = substr($data['0'],-10,10).time();
						$sql = "INSERT INTO ".$this->session->userdata('bid')."_autodialler SET
								did		= '".$did."'
								,bid	= '".$this->session->userdata('bid')."'
								,gid	= '".$_POST['gid']."'
								,number	= '".substr($data['0'],-10,10)."'
								,others	= '".serialize($others)."'
								,refer_id= '".$listid."'
								,status	= '0'";
						$this->db->query($sql);
					}else{
						$i++;
						$fields = $data;
					}
				}
			}
			$gdetails=$this->configmodel->getDetail('3',$_POST['gid']);
			$this->auditlog->auditlog_info($this->lang->line('level_module_group'),"Autodailer is created for the group ".$gdetails['groupname']);
			fclose($handle);
		
	}
	function blacknumberexists($str){
		$sql=$this->db->query("select id from ".$this->session->userdata('bid')."_blocknumbers where number='$str'");
		if($sql->num_rows()>0){
			return "exists";
		}else{
			return "available";
		}
	}
	function add_blacklistnumber(){
		$id=$this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM ".$this->session->userdata('bid')."_blocknumbers")->row()->id;
		$this->db->set('id',$id);
		$this->db->set('number',$this->input->post('blacklist'));
		$this->db->set('reason',$this->input->post('reason'));
		$this->db->set('blockedby',$this->session->userdata('eid'));
		$this->db->set('status','0');
		$this->db->insert($this->session->userdata('bid')."_blocknumbers");
		$this->auditlog->auditlog_info($this->lang->line('level_module_black'),$this->input->post('blacklist')." number added to blacklist");
		$empname=$this->empmodel->get_employee($this->session->userdata('eid'));
		$r=$this->emailmodel->get_busineeprofiledetails($this->session->userdata('bid'));
		$body='Hi '.$r[0]['contactname'].'<br/>'.
		
		
				$this->input->post('blacklist').'  Has number blocked by   '.$empname[0]['empname'].'<br/>'.
				
				$this->input->post('reason').'<br/>
				
				
				Regards<br/>
				MCube Team
				
				
				'; 
		$subject = 'Number Blocked on MCube';
			$from='"MCube" <noreply@mcube.com>';
			$message = $this->emailmodel->email_header().$body.$this->emailmodel->email_footer();
			//~ $headers	 = 'MIME-Version: 1.0' . "\n".
						//~ 'Content-type: text/html; charset=iso-8859-1' . "\n".
						//~ 'From:'.$from. "\n" .
						//~ 'Reply-To:"MCube" <support@vmc.in>'."\n" .
						//~ 'X-Mailer: PHP/' . phpversion();
			 //~ mail($to, $subject, $message, $headers);
			 
			 $this->load->library('email');
			 $this->email->from('noreply@mcube.com', 'MCube');
			 $this->email->to($to);
			 $this->email->subject($subject);
			 $this->email->message($body);
			 $this->email->send();	
		
		
		return true;
	}
	function blocknumber_list($ofset='0',$limit='20'){
		$q=' WHERE 1';$s='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		
		$s = ($this->session->userdata('search'))?$this->session->userdata('search'):'';
		
		$q.= (isset($s['blnumber']) && $s['blnumber']!='')?" AND b.number like '%".$s['blnumber']."%'":"";
		
		$res=array();
/*
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS * from ".$this->session->userdata('bid')."_blocknumbers  $q LIMIT $ofset,$limit
									   ")->result_array();
*/
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS b.*,e.empname as requestby FROM ".$this->session->userdata('bid')."_blocknumbers b LEFT JOIN ".$this->session->userdata('bid')."_employee e ON b.blockedby = e.eid
		$q  ORDER BY b.status ASC LIMIT $ofset,$limit
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function Delete_blocknumber($id)
	{
		$this->auditlog->auditlog_info($this->lang->line('level_module_black'),$this->get_blocknumber($id)." number has deleted from blacklist");
		$s=$this->db->query("DELETE FROM ".$this->session->userdata('bid')."_blocknumbers where id=$id");
		return true;
	}
	function get_blocknumber($id){
		$sql=$this->db->query("select * from ".$this->session->userdata('bid')."_blocknumbers where id=$id")->row()->number;
		return $sql;
	}
	function get_PriNumber_Associate($assid){
		$sql=$this->db->query("select number from prinumbers where associateid=$assid and bid=".$this->session->userdata('bid'))->row()->number;
		
		return $sql;
	}
	function Insert_Same($id){
		$arr=array_keys($_POST);
	    	for($i=0;$i<sizeof($arr);$i++){
				if(!in_array($arr[$i],array("update_system","gid","clone","gids","oldprinumber","groupname","prinumber","rules","eid","keyword"
			,"primary_rule","record","bday","hdaytext","replymessage","replytocustomer","replytoexecutive","replytoexecutive","timeout"
			,"oneditaction","onhangup","replyattmsg","leadaction","oncallaction","supportaction","supportgrp"))){
							if(is_array($_POST[$arr[$i]]))
						    $val = @implode(',',$_POST[$arr[$i]]);
							elseif($_POST[$arr[$i]]!="")
						    $val=$_POST[$arr[$i]];
							else
								$val='';
							$this->db->set($arr[$i],$val);
					}
				}
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$sf = $roleDetail['system'];
		$oldGroup=$this->db->query("SELECT * FROM ".$this->session->userdata('bid')."_groups where gid=$id")->row();
		$gid=$this->db->query("SELECT COALESCE(MAX(`gid`),0)+1 as id FROM ".$this->session->userdata('bid')."_groups")->row()->id;

		$this->db->set('gid',$gid);
		$this->db->set('bid',$this->session->userdata('bid'));
	
		$_POST['prinumber'] = ($_POST['prinumber']!='0') ? $_POST['prinumber']:$this->ivrsmodel->getFreePri();
		if(isset($_POST['prinumber']))$this->ivrsmodel->updatePri($this->db->query("SELECT prinumber FROM ".$this->session->userdata('bid')."_groups WHERE gid = '".$oldGroup->prinumber."' ")->row()->prinumber);
		if($_FILES['hdayaudio']['error']==0){
			$ext=pathinfo($_FILES['hdayaudio']['name'],PATHINFO_EXTENSION);
			$newName = "H".date('YmdHis').".".$ext;
			move_uploaded_file($_FILES['hdayaudio']['tmp_name'],$this->config->item('sound_path').$newName);
			$this->db->set('hdayaudio',$newName);
		
		}else{
			$this->db->set('hdayaudio',$oldGroup->hdayaudio);	
		}

	   //~ if($_FILES['voicemessage']['error']==0){
			//~ $ext=pathinfo($_FILES['voicemessage']['name'],PATHINFO_EXTENSION);
			//~ $newName = "V".date('YmdHis').".".$ext;
			//~ move_uploaded_file($_FILES['voicemessage']['tmp_name'],$this->config->item('sound_path').$newName);
			//~ $this->db->set('voicemessage',$newName);
		//~ }else{
			//~ $this->db->set('voicemessage',$oldGroup->voicemessage);	
		//~ }
		if($_FILES['greetings']['error']==0){
			$ext=pathinfo($_FILES['greetings']['name'],PATHINFO_EXTENSION);
			$newName = "G".date('YmdHis').".".$ext;
			move_uploaded_file($_FILES['greetings']['tmp_name'],$this->config->item('sound_path').$newName);
			$this->db->set('greetings',$newName);
		}else{
			$this->db->set('greetings',$oldGroup->greetings);	
		}	
			$this->db->set('connectowner',$this->input->post('connectowner'));	
			//$this->db->set('landingregion',$this->input->post('landingregion'));	
			$this->db->set('recordnotice',$this->input->post('recordnotice'));	
			$this->db->set('replytoexecutive',$this->input->post('replytoexecutive'));	
			$this->db->set('replytocustomer',$this->input->post('replytocustomer'));
			$this->db->set('replyattmsg',$this->input->post('replyattmsg'));
			$this->db->set('replymessage',$this->input->post('replymessage'));
		foreach($sf as $f){
			$f['fieldname'] = ($f['fieldname']=='addnumber')?'prinumber':$f['fieldname'];
			
		
			if($f['modid']==3 && isset($_POST[$f['fieldname']]) && !in_array($f['fieldname'],array('oldprinumber','gid','clone'))){
				$this->db->set($f['fieldname'], $_POST[$f['fieldname']]);
				if(isset($_POST['url']))$this->db->set('url', $_POST['url']);
				if(isset($_POST['bday']))$this->db->set('bday', json_encode($_POST['bday']));
				if(isset($_POST['access_reports']) && !empty($_POST['access_reports']))$this->db->set('access_reports', @implode(',',$_POST['access_reports']));
			}
		}
		$this->db->insert($this->session->userdata('bid')."_groups"); 
		//echo $this->db->last_query();exit;
		$this->ivrsmodel->updatePri($_POST['prinumber'],1,$this->session->userdata('bid'),0,$gid);
		$this->auditlog->auditlog_info($this->lang->line('level_module_group'),"Updated Group  ".$this->input->post('groupname'));
		
		/************Insert Employees***********************/
		$qw=$this->db->query("select * from ".$this->session->userdata('bid')."_group_emp where gid=$id")->result_array();
		foreach($qw as $row){
			$this->db->set('bid', $this->session->userdata('bid'));                       
			$this->db->set('gid', $gid);                       
			$this->db->set('eid', $row['eid']); 
			$this->db->set('starttime', $row['starttime']);                       
			$this->db->set('endtime', $row['endtime']);                       
		                     
			$this->db->set('status',1);
			$this->db->set('area_code', $row['area_code']); 
			$this->db->set('empweight', $row['empweight']); 
			$this->db->set('isfailover',$row['isfailover']);	
			$this->db->insert($this->session->userdata('bid')."_group_emp");
			}
		return true;
		/*************End****************************/
	}
	function clone_delete($id){
		$this->db->set('status',0); 
		$this->db->where('gid', $id); 
		$this->db->update($this->session->userdata('bid')."_group_emp"); 
		$sql1=$this->db->query("update ".$this->session->userdata('bid')."_groups set status=0 where gid=".$id);
		$gdetails=$this->configmodel->getDetail('3',$id);
		$this->auditlog->auditlog_info($this->lang->line('level_module_group'),$gdetails['groupname']." is Deleted ");
	}
	function blocknumber_status($id){
		$sql=$this->db->query("SELECT * FROM ".$this->session->userdata('bid')."_blocknumbers WHERE id=".$id)->row();
		$ST=$sql->status;
		if($ST==0){
			$status=1;
		}else{
			$status=0;
		}
		$this->db->set('status',$status);
		$this->db->where('id',$id);
		$this->db->update($this->session->userdata('bid')."_blocknumbers");
		$this->auditlog->auditlog_info('Block Number',$sql->number." status updated");
		
		return true;
	}
	function uniquegroup($str,$id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if($id!=""){
			$query=$this->db->query("select	groupname from ".$bid."_groups where groupname='$str' and gid!=$id");
		}else{
			$query=$this->db->query("select	groupname from ".$bid."_groups where groupname='$str'");
		}
		if($query->num_rows()==0){
			return true;
		}else{
			return false;
		}
	}
	function uniquegroupclone($str,$id){
		$query=$this->db->query("select	groupname from ".$this->session->userdata('bid')."_groups where groupname='$str'");
		if($query->num_rows()==0){
			return true;
		}else{
			return false;
		}
	}
	function autodetail($did){
		$sql = "select * from ".$this->session->userdata('bid')."_autodialler where did='".$did."'";
		$rst=$this->db->query($sql);
		if($rst->num_rows()>0){
			return $rst->row_array();
		}else{
			return array();
		}
	}
	
	function addConfirmNumber($id){
		if($id!=""){
			$rows=$this->get_ConfirmNumber($id);
			if($rows->pri!=$this->input->post('pri')){
				$this->pollmodel->updatePri($rows->pri,0,$this->session->userdata('bid'),0,0);
				$this->pollmodel->updatePri($this->input->post('pri'),1,$this->session->userdata('bid'),6,$id);
			}
			$this->db->set('pri',$this->input->post('pri'));
			$this->db->set('aurl',$this->input->post('aurl'));
			$this->db->set('akey1',$this->input->post('aopt1'));
			$this->db->set('akey2',$this->input->post('aopt2'));
			$this->db->set('status','1');
			$this->db->set('durl',$this->input->post('durl'));
			$this->db->set('dkey1',$this->input->post('dopt1'));
			$this->db->set('dkey2',$this->input->post('dopt2'));
			$this->db->where('cid',$id);
			$this->db->update($this->session->userdata('bid')."_confirmnumber");
			return true;
		}else{
			$cid=$this->db->query("SELECT COALESCE(MAX(`cid`),0)+1 as id FROM ".$this->session->userdata('bid')."_confirmnumber")->row()->id;
			$this->db->set('cid',$cid);
			$this->db->set('pri',$this->input->post('pri'));
			$this->db->set('aurl',$this->input->post('aurl'));
			$this->db->set('akey1',$this->input->post('aopt1'));
			$this->db->set('akey2',$this->input->post('aopt2'));
			$this->db->set('durl',$this->input->post('durl'));
			$this->db->set('dkey1',$this->input->post('dopt1'));
			$this->db->set('dkey2',$this->input->post('dopt2'));
			$this->db->set('status','1');
			$this->db->insert($this->session->userdata('bid')."_confirmnumber");
			$this->pollmodel->updatePri($this->input->post('pri'),1,$this->session->userdata('bid'),6,$cid);
			return $cid;
		}
	}
	function configList($ofset='0',$limit='20'){
			$res=array();
		$q='where 1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['name']) && $s['name']!='')?" and name like '%".$s['name']."%'":"";
		$q.=(isset($s['number']) && $s['number']!='')?" and number like '%".$s['number']."%'":"";
		$q.=(isset($s['email']) && $s['email']!='')?" and email like '%".$s['email']."%'":"";
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS c.*,p.*, c.status as stat FROM ".$this->session->userdata('bid')."_confirmnumber c left join prinumbers p on p.associateid=c.cid and p.number=c.pri $q")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	function get_ConfirmNumber($id){
		$r=array();
		if($id!=""){
			$res=$this->db->query("SELECT * FROM ".$this->session->userdata('bid')."_confirmnumber where cid=".$id);
			return $res->row();
		}else{
			return $r;
		}
	}
	function status_updateConfirm($id){
		$status='';
		$row=$this->get_ConfirmNumber($id);
		if($row->status!=0){
			$this->db->set('status','0');
			$status=1;
		}else{
			$this->db->set('status','1');
			$status=2;
		}
		$this->db->where('cid',$id);
		$this->db->update($this->session->userdata('bid')."_confirmnumber");
		
		
		return $status;
	}
	function miscal_pris($num=''){
		//$res=array('0'=>'None');
		$s='';
		$res=array('0'=>'System');
		if($num!=''){
			$s=' and number="'.$num.'"';
		}
		$rst=$this->db->query("SELECT number,landingnumber,status FROM prinumbers where bid='".$this->session->userdata('bid')."' and ntype=1")->result_array();
		if(count($rst)>0){
			foreach($rst as $re){
				if($re['status']==0 || $re['number']==$num){
					$res[$re['number']]=$re['landingnumber'];
				}
			}
		}
		return $res;
	}
	function groupbyId($id){
		if($id!=""){
			$sql=$this->db->query("SELECT * FROM ".$this->session->userdata('bid')."_groups where gid=".$id);
			return $sql->row();
		}else{
			return array();
		}
			
	}
	function addHoliday(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
        $arr=array_keys($_POST);
		if (($handle = fopen($_FILES['filename']['tmp_name'],"r")) !== FALSE) {
			$i=0;
			$err=0;
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				
				if($i>0){
					
					$c = 0;
					$h=(isset($data['1']))?$data['1']:'';
					$sql=$this->db->query("SELECT holiday from ".$bid."_holiday where holiday='".$h."'");
						if($sql->num_rows()==0){
				$hday=$this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM ".$bid."_holiday")->row()->id;
				for($i=0;$i<sizeof($arr);$i++){
				if(!in_array($arr[$i],array("update_system","holiday","date","bid"))){
						/* Changed for custom fields */
						//if($bid == 1 || $bid == 47  || $bid == 257){
							if(is_array($_POST[$arr[$i]]))
								echo $val = @implode(',',$_POST[$arr[$i]]);
							elseif($_POST[$arr[$i]]!="")
								echo $val=$_POST[$arr[$i]];
							else
								$val='';
							$this->db->set($arr[$i],$val);
						//}
						/* Changed for custom fields end */
					}
					
				}
								$this->db->set('id',$hday);
								$this->db->set('holiday',(isset($data['1']))?$data['1']:'');
								$this->db->set('date',(isset($data['2']))?$data['2']:'');
								$this->db->insert($bid."_holiday");
								$this->auditlog->auditlog_info('holiday',(isset($data['1']))?$data['1']:''. " New Holiday added to Holiday List ");
						}else{
								$err++;	
						}	
					}else{
						$i++;
						$fields = $data;
					 }
			}
		}
		if($err>1){
			return false;
		}else{
			return true;
		}
	}
	function listholidays($ofset='0',$limit='20'){
		$res=array();
		$q='where 1';
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['holiday']) && $s['holiday']!='')?" and holiday like '%".$s['holiday']."%'":"";
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM ".$bid."_holiday $q LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	function EditHoliday($id=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$arr=array_keys($_POST);
		if($id!=""){
			$res=$itemDetail = $this->configmodel->getDetail('28',$id,'',$bid);
			$this->db->set('holiday',$this->input->post('holiday'));
			$this->db->set('date',$this->input->post('date'));
		    for($i=0;$i<sizeof($arr);$i++){
				if(!in_array($arr[$i],array("update_system","holiday","date"))){
					/* Changed for custom fields */
					if(is_array($_POST[$arr[$i]]))
						echo $val = @implode(',',$_POST[$arr[$i]]);
					elseif($_POST[$arr[$i]]!="")
						echo $val=$_POST[$arr[$i]];
					else
						$val='';
					$this->db->set($arr[$i],$val);
					/* Changed for custom fields end */
				}
			}
			$this->db->update($bid."_holiday");
			$this->db->where('id',$id);
			$this->auditlog->auditlog_info('Holiday',$this->input->post('Holiday'). " Holiday Updated ");
			return true;
		}else{
			$id=$this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM ".$bid."_holiday")->row()->id;
			$this->db->set('id',$id);
			$this->db->set('holiday',$this->input->post('holiday'));
			$this->db->set('date',$this->input->post('date'));
            for($i=0;$i<sizeof($arr);$i++){
				if(!in_array($arr[$i],array("update_system","holiday","date","bid"))){
					/* Changed for custom fields */
						if(is_array($_POST[$arr[$i]]))
							echo $val = @implode(',',$_POST[$arr[$i]]);
						elseif($_POST[$arr[$i]]!="")
							echo $val=$_POST[$arr[$i]];
						else
							$val='';
						$this->db->set($arr[$i],$val);
					/* Changed for custom fields end */
				}
			}
			$this->db->insert($bid."_holiday");
			$this->auditlog->auditlog_info('Holiday',$this->input->post('Holiday'). " New Holiday added to Holiday List ");
			return true;
		}
		
	}
	function deleteHoliday($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("DELETE FROM ".$bid."_holiday WHERE id=".$id);
		return true;
	}

	function dis_grp_employee($eid,$gid,$bid){
		$check=$this->db->query("select * from ".$bid."_group_emp where eid=".$eid." and gid=$gid")->row_array();	
		$status=($check['status']==0)?'1':'0';
		$cnt = $this->db->query("SELECT COALESCE(max(callcounter),0) as cnt FROM ".$bid."_group_emp where gid='".$gid."'")->row()->cnt;
		$this->db->set('callcounter',$cnt);
		$this->db->set('status',$status);
		$cnt = $this->db->query("SELECT COALESCE(max(callcounter),0) as cnt FROM ".$bid."_group_emp where gid='".$gid."'")->row()->cnt;
                $this->db->set('callcounter',$cnt);
		$this->db->where('eid',$eid);	
		$this->db->where('gid',$gid);
		$this->db->update($bid.'_group_emp');
		$itemDetail= $this->configmodel->getDetail('3',$gid,'',$bid);
		$empDetail= $this->configmodel->getDetail('2',$eid,'',$bid);
		$text=($status)?" Enabled":" Disabled";
		$this->auditlog->auditlog_info('Group Employee', $empDetail['empname'].$text." from the group ".$itemDetail['groupname']);
		return $status;
	}
	function getbusinessgcnt($bid){
		
		$cnt=$this->db->query("SELECT COALESCE( count( * ) ) AS cnt FROM `".$bid."_groups` g
							   left join prinumbers p on g.prinumber=p.number and g.gid=p.associateid
							   where p.bid='".$bid."'")->row()->cnt;
		return $cnt;					   
	}
	function get_glist($bid){
		$res=array();
		$sql=$this->db->query("SELECT gid,groupname FROM `".$bid."_groups` where status=1");
		if($sql->num_rows()>0){
			$res= $sql->result_array();
		}
		return $res;
	}
	function MUpdategroup(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$sf = $roleDetail['system'];
		$ext='';
		if($_FILES['hdayaudio']['error']==0){
			$ext=pathinfo($_FILES['hdayaudio']['name'],PATHINFO_EXTENSION);
			$newName = "H".date('YmdHis').".".$ext;
			move_uploaded_file($_FILES['hdayaudio']['tmp_name'],$this->config->item('sound_path').$newName);
			$this->db->set('hdayaudio',$newName);
		}
		//~ $ext='';
		//~ if($_FILES['voicemessage']['error']==0){
			//~ $ext=pathinfo($_FILES['voicemessage']['name'],PATHINFO_EXTENSION);
			//~ $newName = "V".date('YmdHis').".".$ext;
			//~ move_uploaded_file($_FILES['voicemessage']['tmp_name'],$this->config->item('sound_path').$newName);
			//~ $this->db->set('voicemessage',$newName);
		//~ }
		$ext='';
		if($_FILES['greetings']['error']==0){
			$ext=pathinfo($_FILES['greetings']['name'],PATHINFO_EXTENSION);
			$newName = "G".date('YmdHis').".".$ext;
			move_uploaded_file($_FILES['greetings']['tmp_name'],$this->config->item('sound_path').$newName);
			$this->db->set('greetings',$newName);
		}
		foreach($sf as $f){
			$f['fieldname'] = ($f['fieldname']!='addnumber')?$f['fieldname']:'prinumber';
			if($f['modid']==3 && isset($_POST[$f['fieldname']])){
				if($f['fieldname'] =='replymessage'){
					$this->db->set($f['fieldname'], mysql_real_escape_string($_POST[$f['fieldname']]));
				}
				if($f['fieldname']!='groupname'){
					$this->db->set($f['fieldname'], $_POST[$f['fieldname']]);
				}
				 if(isset($_POST['url']))$this->db->set('url', $_POST['url']);
				 if(isset($_POST['bday']))$this->db->set('bday', json_encode($_POST['bday']));
			}
		}
		$connectowner=(isset($_POST['connectowner']))?"1":"0";
		$this->db->set('connectowner',$connectowner);
		$rply_check=(isset($_POST['replytocustomer']))?"1":"0";
		$this->db->set('replytocustomer',$rply_check);
		$rply_check1=(isset($_POST['replytoexecutive']))?"1":"0";
		$this->db->set('replytoexecutive',$rply_check1);
		$recordnotice=(isset($_POST['recordnotice']))?"1":"0";
		$this->db->set('recordnotice',$recordnotice);
		$record_conversation=(isset($_POST['record']))?"1":"0";
		$this->db->set('record',$record_conversation);
		$sameexe=(isset($_POST['sameexe']))?"1":"0";
		$this->db->set('sameexe',$sameexe);
		$misscall=(isset($_POST['misscall']))?"1":"0";
		$this->db->set('misscall',$misscall);
		$pincode=(isset($_POST['pincode']))?"1":"0";
		$this->db->set('pincode',$pincode);
		$this->db->set('timeout',$this->input->post('timeout'));
		$this->db->set('oncallaction',$this->input->post('oncallaction'));
		$this->db->set('oneditaction',$this->input->post('oneditaction'));
		$groupKey=base64_encode($bid.'_'.$gid);
		$this->db->set('groupkey',$groupKey);
		$this->db->set('groupname', $_POST['groupname']);
		$this->db->where('gid',$gid);
		$this->db->update($bid."_groups");  
		$this->db->query("UPDATE ".$bid."_group_emp SET callcounter='0' WHERE gid='".$gid."'");
		$this->auditlog->auditlog_info($this->lang->line('level_module_group'),"Updated Group  ".$this->input->post('groupname'));
		
		$this->configmodel->supportAlert(array(
							'subject'=>$this->session->userdata('username').' update a group '.$this->input->post('groupname'),
							'message'=>'Missed Call SMS:'.$_POST['replymessage'].'<br>Received Call SMS:'.$_POST['replyattmsg']
		));
		
		if(isset($_POST['custom'])){
			$arrs=array_keys($_POST['custom']);
			for($k=0;$k<sizeof($arrs);$k++){
				if(is_array($_POST['custom'][$arrs[$k]])){
						$x=implode(",",$_POST['custom'][$arrs[$k]]);
					}else{
						$x=$_POST['custom'][$arrs[$k]];
					}
					if($x!=''){
						$this->db->query("DELETE FROM ".$bid."_customfieldsvalue where bid= '".$bid."' and modid= '3' and fieldid		= '".$arrs[$k]."' and dataid= '".$gid."'");
						$sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
							 bid			= '".$bid."'
							,modid			= '3'
							,fieldid		= '".$arrs[$k]."'
							,dataid			= '".$gid."'
							,value			= '".$x."'";
						$this->db->query($sql);
					}
				}
			}
		return 1;
	}
	function refreshcounter($gid,$bid){
		$sql=$this->db->query("UPDATE ".$bid."_group_emp SET callcounter = 0 WHERE gid=$gid");
		if($this->db->affected_rows() >0){
			$this->auditlog->auditlog_info('Group',$lgid." Call Counter Reset By ".$this->session->userdata('username'));
			return 1;
		}
		else
			return 0;
	}
	function supportNumberChk($bid){
		$rst = $this->db->query("SELECT support FROM `prinumbers` WHERE bid='".$bid."' ")->result_array();
		$ret = 0;
		if(count($rst)>0){
			foreach($rst as $re){
				if($re['support']==1){
					$ret = 1;
				}
			}
		}
		return $ret;
	}
     function delaudio($gid,$bid){
	   $sql=$this->db->query("UPDATE ".$bid."_groups SET hdayaudio = NULL WHERE gid='".$gid."'");	
		return 1;
	}
	 function delgreeting($gid,$bid){  
		$sql=$this->db->query("UPDATE ".$bid."_groups SET greetings = NULL WHERE gid='".$gid."'");	
		return 1;
	}
     //~ function delvoicemessage($gid,$bid){  
		//~ $sql=$this->db->query("UPDATE ".$bid."_groups SET voicemessage = NULL WHERE gid='".$gid."'");	
		//~ return 1;
	//~ }
	 function empgetnames($eids,$bid){
		if($eids != ''){
			$empname=$this->db->query("SELECT GROUP_CONCAT(empname SEPARATOR ',') as empname FROM ".$bid."_employee WHERE eid IN(".$eids." )")->row()->empname;
			return ($empname != '') ? $empname : '';
		}
	}
	
}
/* end of group model */
