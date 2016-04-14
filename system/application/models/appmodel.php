<?php
class Appmodel extends Model{
	 
	 function Appmodel(){
        parent::Model();
        $this->load->model('configmodel');
     }
	 function authenticate($email){
		 $ret = array();
		 $sql=$this->db->query("SELECT * FROM business WHERE status='1'");
		 if($sql->num_rows()>0){
			 $business = $sql->result_array();
			 foreach($business as $bus){
				 $sql = "SELECT * FROM ".$bus['bid']."_employee WHERE empemail='".$email."'";
				 $rst = $this->db->query($sql);
				 if($rst->num_rows()>0){
					$exe = $rst->row_array();
					$ret = array('reqid'=>base64_encode($bus['bid'].'_'.$exe['eid']));
					$appkey = "";for($i = 0; $i<=6 ; $i++){$appkey .= ($i%2==0)? chr(rand(97,122)) : rand(0,9);}
					$sql = "UPDATE ".$bus['bid']."_employee SET appkey='".$appkey."' WHERE eid='".$exe['eid']."'";
					$this->db->query($sql);
					$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
					$message = "Your App Key for MCube is: ".$appkey;
					$sms = $api."&to=".substr($exe['empnumber'],-10,10)."&text=".urlencode($message);
					$sms = file($sms);
				 }
			 }
		 }
		 return $ret;
	 }
	function auth($data){
		//print_r($data);
		$ret = array();
		$sql = "SELECT * FROM ".$data['bid']."_employee WHERE eid='".$data['eid']."' AND appkey='".$data['authkey']."'";
		$rst = $this->db->query($sql);
		if($rst->num_rows()>0){
			$ret = array('bid'=>$data['bid'],'eid'=>$data['eid']);
		}
		return $ret;
	}
	
	 
	function getLastCall($post){
		$ret = array();
		$sql = "SELECT empnumber FROM ".$post['bid']."_employee WHERE eid='".$post['eid']."'";
		$empnumber = $this->db->query($sql)->row()->empnumber;
		
		$sql = "SELECT 
				h.callid,h.callfrom as number,
				h.callername as name,
				h.caller_email as email,
				h.calleraddress as address,
				h.remark,
				g.groupname
				FROM ".$post['bid']."_callhistory h
				LEFT JOIN ".$post['bid']."_groups g ON h.gid=g.gid
				WHERE h.callto='".$empnumber."' 
				ORDER BY h.starttime DESC
				LIMIT 0,1";
		$rst = $this->db->query($sql);
		if($rst->num_rows()>0){
			$ret = $rst->row_array();
		}
		return $ret;
	 }
	 
	 function updateCallDetail($data){
		$sql = "SELECT * FROM ".$data['bid']."_callhistory WHERE callid='".$data['callid']."'";
		$rst = $this->db->query($sql);
		if($rst->num_rows()>0){
			$sql = "UPDATE ".$data['bid']."_callhistory SET callid='".$data['callid']."'";
			$sql .= isset($data['name'])	? ",callername		='".$data['name']."'"		: "";
			$sql .= isset($data['email'])	? ",caller_email	='".$data['email']."'"		: "";
			$sql .= isset($data['address'])	? ",calleraddress	='".$data['address']."'"	: "";
			$sql .= isset($data['remark'])	? ",remark	='".$data['remark']."'"	: "";
			$sql .= "WHERE callid='".$data['callid']."'";
			echo $sql;exit;
			$this->db->query($sql);
			$ret = array('success'=>'true');
		}else{
			$ret = array('error'=>'true');
		}
		return $ret;
	 }
	 function getCallerId(){
		$ret = array();
		$sql = "SELECT pilotno as 'callerid' FROM pri_groups";
		$rst = $this->db->query($sql);
		if($rst->num_rows()>0){
			$ret = $rst->result_array();
			//~ foreach($list as $key){
				//~ $ret[] = 
			//~ }
		}
		return $ret;
	}
	function getEmpDetail($bid,$eid){
		$sql=$this->db->query("SELECT * FROM  ".$bid."_employee WHERE bid='".$bid."' and eid='".$eid."'");
		 if($sql->num_rows()>0){
			 return $sql->row();
		 }else{
			 return array();
		 }
	 }
	 
	 function getRoledetail($roleid='',$bid){
		$detail['role'] = (array)$this->db
					->query("SELECT * FROM ".$bid."_user_role
							WHERE roleid='".$roleid."'
							AND bid='".$bid."'")
					->row();
		$modules = $this->db
					->query("SELECT m.modid,m.modname,m.moddesc,COALESCE(o.opt_add,0) as opt_add,
							COALESCE(o.opt_view,0) as opt_view,COALESCE(opt_delete,0) as opt_delete FROM module m
							LEFT JOIN (SELECT * FROM ".$bid."_role_mod_opt
							WHERE roleid='".$roleid."'
							AND bid='".$bid."') as  o
							ON m.modid=o.modid")
					->result_array();
		foreach ($modules as $mod)$detail['modules'][$mod['modid']] = $mod;
		$detail['system'] = $this->db
					->query("SELECT a.*,f.fieldname FROM ".$bid."_role_access a
							LEFT JOIN systemfields f on a.fieldid=f.fieldid
							WHERE a.roleid='".$roleid."'
							AND a.bid='".$bid."'
							AND a.fieldtype='s'")
					->result_array();
		$detail['custom'] = $this->db
					->query("SELECT * FROM ".$bid."_role_access
							WHERE roleid='".$roleid."'
							AND bid='".$bid."'
							AND fieldtype='c'")
					->result_array();
		return $detail;
	}
	
	function getCallReport($user,$roleid,$limit='',$cond=''){
		$bid = $user->bid;
		$eid = $user->eid;
		$q = ($roleid!='1') ? " AND (h.eid='".$eid."' or g.eid='".$eid."') ":"";
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		$q .= ($type == '0') ? " AND h.pulse > '0'" : "";
		if($cond!=''){
			$q .= $cond;
		}
		$roleDetail=$this->getRoledetail($roleid,$bid);
		
		$sql="SELECT SQL_CALC_FOUND_ROWS h.callid FROM ".$bid."_callhistory h
				LEFT JOIN ".$bid."_groups g on g.gid=h.gid
				where h.status!=2 ".$q;
		$sql.=($limit=='')?' AND h.starttime="'.date('Y-m-d').'"':'';		
		$sql.=" ORDER BY h.starttime DESC ";						
		$sql.=($limit!='')?' limit 0,'.$limit:"";	
		//echo $sql;	
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$fieldset = $this->configmodel->getFields('6',$bid);
		$keys = array();
		$header = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldname']);
					/*$header[$field['fieldname']] = (($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_6')->$field['fieldname']);*/
					$header[$field['fieldname']] = $field['fieldname'];
				}
			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,'custom['.$field['fieldid'].']');
					$header['custom['.$field['fieldid'].']'] = $field['customlabel'];
				}
			}
		}
		$list = array();
		foreach($rst as $rec){
			$data = array('callid'=>$rec['callid']);
			$r = $this->configmodel->getDetail('6',$rec['callid'],'',$bid);
			foreach($keys as $k){
				if($k=='callername'){
					$data[$header[$k]] = (isset($r[$k])&&$r[$k]!='')?$r[$k]:"unknown";
				}else if($k=='filename'){
					$data[$header[$k]] = (isset($r[$k])&&$r[$k]!='')?base_url().'sounds/'.$r[$k]:"";
					//$data[$ikey]['recorded_file'] = $rec['recorded_file']!=''?base_url().'sounds/'.$rec['recorded_file']:'';
				}else{
					$data[$header[$k]] = (isset($r[$k])&&$r[$k]!='')?$r[$k]:"";
				}
			}
			$data['empemail']=$r['empemail'];
			$data['recorded_file']=(isset($r['filename'])&&$r['filename']!='')?base_url().'sounds/'.$r['filename']:"";
			array_push($list,$data);
		}
		return $list;
	}
	
	
	function update_caller_details($id,$bid){
		$itemDetail = $this->configmodel->getDetail('6',$id,'',$bid);
		$rate=0;
		$val='';
		$arr=array_keys($_POST);
		for($i=0;$i<sizeof($arr);$i++){
			if($arr[$i]!="update_system" && $arr[$i]!="custom" && $arr[$i]!="convertaslead"){
				if($_POST[$arr[$i]]!=""){$val=$_POST[$arr[$i]];	$rate=$rate+1;}else{$val='';}
				$this->db->set($arr[$i],$val);
			}
		}
		$this->db->set('rate',$rate);
		$this->db->where('callid',$id);
		$this->db->update($bid.'_callhistory'); 
		//$this->auditlog->auditlog_info('Report',$id. "Call Details updated by ".$this->session->userdata('username'));
		
		if($this->input->post('callername')!='' || $this->input->post('caller_email')!=''){
			$data = array(
					'bid'		=>$bid,
					'name'		=>$this->input->post('callername'),
					'number'	=>$itemDetail['callfrom'],
					'email'		=>$this->input->post('caller_email'),
					'remarks'	=>$this->input->post('remark')
			);
			$this->configmodel->UpdateContact($data);
		}
		if($this->input->post('convertaslead')=='1'){
			if(isset($itemDetail['empid'])){ 
				$leadid = $this->db->query("SELECT COALESCE(MAX(`leadid`),0)+1 as id FROM ".$bid."_leads")->row()->id;
				$this->db->set('leadid',$leadid);
				$this->db->set('bid',$bid);
				$this->db->set('gid',$itemDetail['grid']);
				$this->db->set('assignto',$itemDetail['empid']);
				$this->db->set('enteredby',$itemDetail['empid']);
				$this->db->set('name',$this->input->post('callername'));
				$this->db->set('email',$this->input->post('caller_email'));
				$this->db->set('number',$itemDetail['callfrom']);
				$this->db->set('source','Calltrack');
				$this->db->set('createdon',date("Y-m-d H:i:s"));
				$this->db->set('status',1);
				$this->db->insert($bid."_leads");
			}
			
		}
		
		if(isset($_POST['custom'])){
			foreach($_POST['custom'] as $fid=>$val){
				$this->db->query("DELETE FROM ".$bid."_customfieldsvalue where bid= '".$bid."' and modid= '6' and fieldid		= '".$fid."' and dataid= '".$fid."'");
				$sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
						 bid			= '".$bid."'
						,modid			= '6'
						,fieldid		= '".$fid."'
						,dataid			= '".$id."'
						,value			= '".(is_array($val)?implode(',',$val):$val)."'";
				$this->db->query($sql);
				
			}
		}

		/*if(isset($_POST['custom'])){
			$fids = array_keys($_POST['custom']);
			$sql = "SELECT * FROM ".$bid."_customfields WHERE fieldid in (".implode(',',$fids).") 
					AND fieldtype in ('checkbox','dropdown','radio')";
			$fields = $this->db->query($sql)->result_array();
			if(count($fields)>0){
				$itemDetailNew = $this->configmodel->getDetail('6',$id,'',$bid);
				$fieldset = $this->configmodel->getFields('6',$bid);
				$data = array();
				foreach($fieldset as $field){
					if($field['type']=='s' && !$field['is_hidden']){
						$data[$field['customlabel']]=$itemDetailNew[$field['fieldname']];
					}elseif($field['type']=='c' && $field['show']){
						$data[$field['customlabel']]=$itemDetailNew['custom['.$field['fieldid'].']'];
					}
				}
			}
			foreach($fields as $fid){
				$cf = (is_array($_POST['custom'][$fid['fieldid']])
						?implode(',',$_POST['custom'][$fid['fieldid']])
						:$_POST['custom'][$fid['fieldid']]);
				if($cf!=$itemDetail['custom['.$fid['fieldid'].']']){
					$set = explode("\n",$fid['options']);
					foreach($set as $s){
						$val = explode("|",$s);
						//echo $val[1];exit;
						if(count($val)>1 && $val['0']==$cf){
							$url = $val[1];
							$data = 'data='.urlencode(json_encode($itemDetailNew));
							$objURL = curl_init($url);
							curl_setopt($objURL, CURLOPT_RETURNTRANSFER, 1); 
							curl_setopt($objURL,CURLOPT_POST,1);
							curl_setopt($objURL, CURLOPT_POSTFIELDS,$data);
							$retval = trim(curl_exec($objURL));
							curl_close($objURL);
							$ret = serialize($retval);
							$fp =fopen("apilog.txt","a");fwrite($fp,"\n".'['.date('Y-m-d H:i:s').'] bid:'.$bid.' '. $ret);fclose($fp);
						}
					}
				}
			}
			//print_r($itemDetailNew);exit;
		}*/
		$itemDetailNew = $this->configmodel->getDetail('6',$id,'',$bid);
		
		//print_r($itemDetailNew);exit;
	/*	$rs=$this->configmodel->getDetail('3',$itemDetail['grid'],'',$bid);
		if($rs['oneditaction']!=""){
			$url = $rs['oneditaction'];
			$data = 'data='.urlencode(json_encode($itemDetailNew));
			$objURL = curl_init($url);
			curl_setopt($objURL, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($objURL,CURLOPT_POST,1);
			curl_setopt($objURL, CURLOPT_POSTFIELDS,$data);
			$retval = trim(curl_exec($objURL));
			curl_close($objURL);
			$ret = serialize($retval);
			$fp =fopen("apilog.txt","a");fwrite($fp,"\n".'['.date('Y-m-d H:i:s').'] bid:'.$bid.' '. $ret);fclose($fp);

		}*/
		return 1;
	}
	
	function delete_call($id,$bid)
	{
		$this->db->set('status', '2');
		$this->db->where('callid',$id);
		$this->db->update($bid."_callhistory");
		
		//$this->auditlog->auditlog_info('Report',$id. " Deleted By ".$this->session->userdata('username'));
		//echo $this->db->last_query();exit;
		return 1;	
	}
	
	function getReportlist2($id,$bid){
		$sql="select SQL_CALC_FOUND_ROWS c.calltime,c.lat,c.long,
				e.eid,e.empname,e.empnumber,e.empemail from 
				".$bid."_calltrackemp c
				LEFT JOIN ".$bid."_employee e on c.eid=e.eid
				where c.callid='".$id."'
				ORDER BY c.calltime ASC";	
		$rst = $this->db->query($sql)->result_array();
		return $rst;
	}
	
	function getFollowuplist($id,$bid,$dsh=''){
		$where = ($dsh != '' && $dsh == 1) ? " followupdate >= CURRENT_DATE() AND " : " ";
		$sql="select SQL_CALC_FOUND_ROWS * from ".$bid."_followup where ".$where." callid='".$id."' ORDER BY cdate ASC";					 
		$rst = $this->db->query($sql)->result_array();
		return $rst;
	}	
	function createFollowup($bid,$eid,$set_array){
		$modules = array('Track','IVRS','X','Logs');
		$table = in_array($set_array['module'],$modules)?$bid."_followup":$bid."_leads_followup";
		$id=$this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM ".$table)->row()->id;
		$add_array["id"]=$id;
		$add_array["bid"]=$bid;
		$add_array["eid"]=$eid;
		if(in_array($set_array['module'],$modules)){
			$add_array["callid"]=$set_array['callid'];
		}else{
			$add_array["leadid"]=$set_array['callid'];
		}
		$add_array["comment"]=$set_array['comment'];
		$add_array["followupdate"]=$set_array['followupdate'];
		$add_array["alert"]=$set_array['alert'];
		//$add_array["alert_status"]=$set_array['alert_status'];
		if(in_array($set_array['module'],$modules)){
			$add_array["type"]=strtolower($set_array['module']);
		}
	
		$this->db->insert($table,$add_array);
		return true;
	}
	
	function getIvrsReport($user,$roleid,$limit='',$cond=''){
		$bid = $user->bid;
		$eid = $user->eid;
		$q = ($roleid!='1') ? " AND (h.eid='".$eid."' or g.eid='".$eid."') ":"";
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		$q .= ($type == '0') ? " AND h.pulse > '0'" : "";
		$q .= ($cond != '') ? $cond : "";
		$roleDetail=$this->getRoledetail($roleid,$bid);
		$sql = "SELECT SQL_CALC_FOUND_ROWS hid FROM ".$bid."_ivrshistory h
				LEFT JOIN ".$bid."'_ivrs i on i.ivrsid=h.ivrsid
				WHERE h.bid='".$bid."' ".$q;
		$sql.=($limit=='')?' AND h.datetime="'.date('Y-m-d').'"':'';		
		$sql.=" ORDER BY h.datetime DESC ";						
		$sql.=($limit!='')?' limit 0,'.$limit:"";	
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$fieldset = $this->configmodel->getFields('16',$bid);
		$keys = array();
		$header = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldname']);
					/*$header[$field['fieldname']] = (($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_16')->$field['fieldname']);*/
					$header[$field['fieldname']] = $field['fieldname'];					
				}
			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,'custom['.$field['fieldid'].']');
					$header['custom['.$field['fieldid'].']'] = $field['customlabel'];
				}
			}
		}
		$list = array();
		foreach($rst as $rec){
			$data = array('hid'=>$rec['hid']);
			$r = $this->configmodel->getDetail('16',$rec['hid'],'',$bid);
			foreach($keys as $k){
				if($k=='name'){
					$data['callername'] = (isset($r[$k])&&$r[$k]!='')?$r[$k]:"unknown";
				}else if($k=='ivrstitle'){
					$data['title'] = (isset($r[$k])&&$r[$k]!='')?$r[$k]:"";
				}else if($k=='datetime'){
					$data['starttime'] = (isset($r[$k])&&$r[$k]!='')?$r[$k]:"";
				}else{
					$data[$header[$k]] = (isset($r[$k])&&$r[$k]!='')?$r[$k]:"";
				}
			}
			//$data['empemail']=$r['empemail'];
			array_push($list,$data);
		}
		return $list;
	}
	
	function updateIvrsReport($callid,$bid){
		$r = $this->configmodel->getDetail('16',$callid,'',$bid);
		$this->db->set('name',$this->input->post('name'));
		$this->db->set('email',$this->input->post('email'));
		$this->db->where('hid',$callid);
		$this->db->update($bid.'_ivrshistory');
		//echo $this->db->last_query();exit;
		if(isset($_POST['custom'])){
			$arrs=array_keys($_POST['custom']);
			for($k=0;$k<sizeof($arrs);$k++){
				
				if(is_array($_POST['custom'][$arrs[$k]])){
						$x=implode(",",$_POST['custom'][$arrs[$k]]);
					}
					else{
						$x=$_POST['custom'][$arrs[$k]];
					}
					$this->db->query("DELETE FROM ".$bid."_customfieldsvalue where bid= '".$bid."' and modid= '16' and fieldid		= '".$arrs[$k]."' and dataid= '".$callid."'");
					$sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
						 bid			= '".$bid."'
						,modid			= '16'
						,fieldid		= '".$arrs[$k]."'
						,dataid			= '".$callid."'
						,value			= '".$x."'";
					$this->db->query($sql);
				}
			}
			if($this->input->post('name')!='' || $this->input->post('email')!=''){
				$data = array(
						'bid'		=>$bid,
						'name'		=>$this->input->post('name'),
						'number'	=>$r['callfrom'],
						'email'		=>$this->input->post('email'),
						'remarks'	=>''
				);
				$this->configmodel->UpdateContact($data);
			}
	}
	
	function getPbxReport($user,$roleid,$limit='',$cond=''){
		$bid = $user->bid;
		$eid = $user->eid;
		$q = ($roleid!='1') ? " AND (p.eid='".$eid."' or g.eid='".$eid."') ":"";
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		$q .= ($type == '0') ? " AND h.pulse > '0'" : "";
		$q .= ($cond != '') ? $cond : "";
		$roleDetail=$this->getRoledetail($roleid,$bid);
		$sql = "SELECT SQL_CALC_FOUND_ROWS p.title,r.* FROM ".$bid."_pbxreport r
				LEFT JOIN ".$bid."_pbx p on r.pbxid=p.pbxid
				WHERE 1  ".$q;
		$sql.=($limit=='')?' AND r.starttime="'.date('Y-m-d').'"':'';		
		$sql.=" ORDER BY r.starttime DESC ";						
		$sql.=($limit!='')?' limit 0,'.$limit:"";	
		//echo $sql;exit;
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$fieldset = $this->configmodel->getFields('24',$bid);
		$keys = array();
		$header = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldname']);
					/*$header[$field['fieldname']] = (($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_24')->$field['fieldname']);*/
					$header[$field['fieldname']] = $field['fieldname'];		
				}
			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,'custom['.$field['fieldid'].']');
					$header['custom['.$field['fieldid'].']'] = $field['customlabel'];
				}
			}
		}
		$list = array();
		foreach($rst as $rec){
			$data = array('callid'=>$rec['callid']);
			$r = $this->configmodel->getDetail('24',$rec['callid'],'',$bid);
			foreach($keys as $k){
				if($k=='name'){
					$data['callername'] = (isset($r[$k])&&$r[$k]!='')?$r[$k]:"unknown";
				}else if($k=='pbxtitle'){
					$data['title'] = (isset($r[$k])&&$r[$k]!='')?$r[$k]:"";
				}else{
					$data[$header[$k]] = (isset($r[$k])&&$r[$k]!='')?$r[$k]:"";
				}
			}
			//$data['empemail']=$r['empemail'];
			array_push($list,$data);
		}
		return $list;
	}
	
	function updatePbxReport($callid,$bid){
		$r = $this->configmodel->getDetail('24',$callid,'',$bid);
		$this->db->set('name',$this->input->post('name'));
		$this->db->set('email',$this->input->post('email'));
		$this->db->where('callid',$callid);
		$this->db->update($bid.'_pbxreport');
		//echo $this->db->last_query();exit;
		if(isset($_POST['custom'])){
			$arrs=array_keys($_POST['custom']);
			for($k=0;$k<sizeof($arrs);$k++){
				
				if(is_array($_POST['custom'][$arrs[$k]])){
						$x=implode(",",$_POST['custom'][$arrs[$k]]);
					}
					else{
						$x=$_POST['custom'][$arrs[$k]];
					}
					$this->db->query("DELETE FROM ".$bid."_customfieldsvalue where bid= '".$bid."' and modid= '24' and fieldid		= '".$arrs[$k]."' and dataid= '".$callid."'");
					$sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
						 bid			= '".$bid."'
						,modid			= '24'
						,fieldid		= '".$arrs[$k]."'
						,dataid			= '".$callid."'
						,value			= '".$x."'";
					$this->db->query($sql);
				}
			}
			if($this->input->post('name')!='' || $this->input->post('email')!=''){
				$data = array(
						'bid'		=>$bid,
						'name'		=>$this->input->post('name'),
						'number'	=>$r['callfrom'],
						'email'		=>$this->input->post('email'),
						'remarks'	=>''
				);
				$this->configmodel->UpdateContact($data);
			}
		}
	
	function get_leads($user,$roleid,$limit=''){
		$bid = $user->bid;
		$eid = $user->eid;
		$q = ($roleid!='1') ? " AND (p.eid='".$eid."' or g.eid='".$eid."') ":"";
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		$q .= ($type == '0') ? " AND a.pulse > '0'" : "";
		$roleDetail=$this->getRoledetail($roleid,$bid);
		$sql = "SELECT SQL_CALC_FOUND_ROWS `leadid`,a.`bid`,a.`gid`,`assignto`,`enteredby`,a.`name` as callername,`email`,a.`number` as callfrom,`source`,
				`lastmodified`,`createdon`,a.`status`,b.cnt,b.callid   
				FROM ".$bid."_leads a 
				LEFT JOIN ".$bid."_groups d on a.gid=d.gid 
				LEFT JOIN
				(SELECT count(callid) as cnt,callid,
				callfrom,gid,eid FROM `".$bid."_callhistory`
				GROUP BY callfrom,gid,eid) b
				ON (a.number=b.callfrom AND a.gid=b.gid AND a.assignto=b.eid)
				WHERE a.status!=2  ".$q;
		//$sql.=($limit=='')?' AND r.starttime="'.date('Y-m-d').'"':'';		
		$sql.=" ORDER BY a.createdon DESC ";						
		$sql.=($limit!='')?' limit 0,'.$limit:"";	
		//echo $sql;exit;
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$fieldset = $this->configmodel->getFields('26',$bid);
		$keys = array();
		$header = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldname']);
					/*$header[$field['fieldname']] = (($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_24')->$field['fieldname']);*/
					$header[$field['fieldname']] = $field['fieldname'];		
				}
			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,'custom['.$field['fieldid'].']');
					$header['custom['.$field['fieldid'].']'] = $field['customlabel'];
				}
			}
		}
		$list = array();
		foreach($rst as $rec){
			$data = array('leadid'=>$rec['leadid']);
			$r = $this->configmodel->getDetail('26',$rec['leadid'],'',$bid);
			foreach($keys as $k){
				if($k=='assignto'){
					$data[$header[$k]] = $r['assignempname']!=''?$r['assignempname']:'None';
				}elseif($k=='enteredby'){
					$data[$header[$k]] = $r['enteredempname'];
				}elseif($k=='gid'){
					$data[$header[$k]]= $r['groupname'];
				}elseif($k=='name'){
					$data['callername']= $r['name'];
				}elseif($k=='number'){
					$data['callfrom']= $r['number'];
				}else{
					$data[$header[$k]] = isset($r[$k])?$r[$k]:"";	
				}
			}
			//$data['empemail']=$r['empemail'];
			array_push($list,$data);
		}
		return $list;
	}
	
	function edit_lead($id,$bid){
		$itemDetail = $this->configmodel->getDetail('26',$id,'',$bid);
		$rate=0;
		$val='';
		$arr=array_keys($_POST);
		$existedId ='';
		if($existedId == ''){
			for($i=0;$i<sizeof($arr);$i++){
				if($arr[$i]!="bid" && $arr[$i]!="eid" && $arr[$i]!="leadid" && $arr[$i]!="httpRefer" && $arr[$i]!="callid"  && $arr[$i]!="custom"){
					if($_POST[$arr[$i]]!=""){$val=$_POST[$arr[$i]];}else{$val='';}
					$this->db->set($arr[$i],$val);
				}
			}
			$this->db->set('lastmodified',date("Y-m-d H:i:s"));
			$this->db->where('leadid',$id);
			$this->db->update($bid.'_leads'); 
			
			if($this->input->post('name')!='' || $this->input->post('email')!=''){
				$data = array(
						'bid'		=>$bid,
						'name'		=>$this->input->post('name'),
						'number'	=>$itemDetail['number'],
						'email'		=>$this->input->post('email'),
						'remarks'	=>''
				);
				$this->configmodel->UpdateContact($data);
			}
			
			
			if(isset($_POST['custom'])){
				foreach($_POST['custom'] as $fid=>$val){
					$this->db->query("DELETE FROM ".$bid."_customfieldsvalue where bid= '".$bid."' and modid= '26' and fieldid		= '".$fid."' and dataid= '".$fid."'");
					$sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
							 bid			= '".$bid."'
							,modid			= '26'
							,fieldid		= '".$fid."'
							,dataid			= '".$id."'
							,value			= '".(is_array($val)?implode(',',$val):$val)."'";
					$this->db->query($sql);
					
				}
			}

			/*if(isset($_POST['custom'])){
				$fids = array_keys($_POST['custom']);
				$sql = "SELECT * FROM ".$bid."_customfields WHERE fieldid in (".implode(',',$fids).") 
						AND fieldtype in ('checkbox','dropdown','radio')";
				$fields = $this->db->query($sql)->result_array();
				if(count($fields)>0){
					$itemDetailNew = $this->configmodel->getDetail('26',$id,'',$bid);
					$fieldset = $this->configmodel->getFields('26',$bid);
					$data = array();
					foreach($fieldset as $field){
						if($field['type']=='s' && !$field['is_hidden']){
							$data[$field['customlabel']]=$itemDetailNew[$field['fieldname']];
						}elseif($field['type']=='c' && $field['show']){
							$data[$field['customlabel']]=$itemDetailNew['custom['.$field['fieldid'].']'];
						}
					}
				}
				foreach($fields as $fid){
					$cf = (is_array($_POST['custom'][$fid['fieldid']])
							?implode(',',$_POST['custom'][$fid['fieldid']])
							:$_POST['custom'][$fid['fieldid']]);
					if($cf!=$itemDetail['custom['.$fid['fieldid'].']']){
						$set = explode("\n",$fid['options']);
						foreach($set as $s){
							$val = explode("|",$s);
							//echo $val[1];exit;
							if(count($val)>1 && $val['0']==$cf){
								$url = $val[1];
								$data = 'data='.urlencode(json_encode($itemDetailNew));
								$objURL = curl_init($url);
								curl_setopt($objURL, CURLOPT_RETURNTRANSFER, 1); 
								curl_setopt($objURL,CURLOPT_POST,1);
								curl_setopt($objURL, CURLOPT_POSTFIELDS,$data);
								$retval = trim(curl_exec($objURL));
								curl_close($objURL);
							}
						}
					}
				}
			}*/
			return 1;
		}else{
			return '0';
		}
	}
	
	function get_call_logs($user,$roleid,$limit=''){
		$bid = $user->bid;
		$eid = $user->eid;
		$q = " AND (a.created_by='".$eid."') ";
		$roleDetail=$this->getRoledetail($roleid,$bid);
		$sql = "SELECT SQL_CALC_FOUND_ROWS a.*
				FROM ".$bid."_call_logs a 
				WHERE status=1  ".$q;
		//$sql.=($limit=='')?' AND r.starttime="'.date('Y-m-d').'"':'';		
		$sql.=" ORDER BY a.call_time DESC ";						
		$sql.=($limit!='')?' limit 0,'.$limit:"";	
		//echo $sql;exit;
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$fieldset = $this->configmodel->getFields('35',$bid);
		$keys = array();
		$header = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldname']);
					$header[$field['fieldname']] = $field['fieldname'];		
				}
			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,'custom['.$field['fieldid'].']');
					$header['custom['.$field['fieldid'].']'] = $field['customlabel'];
				}
			}
		}
		$list = array();$data = array();
		if(count($rst)>0){
			foreach($rst as $ikey=>$rec){
				$data[$ikey]['recorded_file'] = $rec['recorded_file']!=''?base_url().'sounds/'.$rec['recorded_file']:'';
				$data[$ikey]['call_type'] = $rec['call_type'];
				$data[$ikey]['callername'] = $rec['name'];
				$data[$ikey]['callfrom'] = $rec['number'];
				$data[$ikey]['call_time'] = $rec['call_time'];
				$data[$ikey]['duration'] = $rec['duration'];
				$data[$ikey]['callid'] = $rec['call_id'];
			}
		}
		return $data;
	}
	
	function update_callLog($id,$bid){
		$itemDetail = $this->configmodel->getDetail('35',$id,'',$bid);
		$rate=0;
		$val='';
		$arr=array_keys($_POST);
		for($i=0;$i<sizeof($arr);$i++){
			if($arr[$i]!="eid" && $arr[$i]!="bid" && $arr[$i]!="callid" && $arr[$i]!="convertaslead"){
				if($_POST[$arr[$i]]!=""){$val=$_POST[$arr[$i]];}else{$val='';}
				$this->db->set($arr[$i],$val);
			}
		}
		$this->db->where('call_id',$id);
		$this->db->update($bid.'_call_logs'); 
		//$this->auditlog->auditlog_info('Call Logs',$id. "Call Details updated by ".$this->session->userdata('username'));
		if($this->input->post('convertaslead')=='1'){
			if(isset($itemDetail['empid'])){ 
				$leadid = $this->db->query("SELECT COALESCE(MAX(`leadid`),0)+1 as id FROM ".$bid."_leads")->row()->id;
				$this->db->set('leadid',$leadid);
				$this->db->set('bid',$bid);
				//$this->db->set('gid',$itemDetail['grid']);
				$this->db->set('assignto',$itemDetail['empid']);
				$this->db->set('enteredby',$itemDetail['empid']);
				$this->db->set('name',$this->input->post('name'));
				$this->db->set('email',$this->input->post('email'));
				$this->db->set('number',$itemDetail['number']);
				$this->db->set('source','CallLog');
				$this->db->set('createdon',date("Y-m-d H:i:s"));
				$this->db->set('status',1);
				$this->db->insert($bid."_leads");
			}
			
		}
		return 1;
	}
	
	function get_followups($user,$roleid,$limit=''){
		$bid = $user->bid;
		$eid = $user->eid;
		$q = ($roleid!='1') ? " AND (f.eid='".$eid."') ":"";
		$rst=array();
		$roleDetail=$this->getRoledetail($roleid,$bid);
		$q.=($limit=='')?"  AND f.followupdate = CURRENT_DATE() " : " ";
		$sql = "select SQL_CALC_FOUND_ROWS number,name,callid,cdate,comment,followupdate,type as module from ".$bid."_followup where 1 ";
		$sql = "SELECT  c.callfrom as number, c.callername as name, f.callid as call_id, f.cdate as comment_date, 
				f.comment , f.followupdate as followup_date, 'Calltrack' as module 
				FROM ".$bid."_followup f 
				JOIN ".$bid."_callhistory c ON f.callid=c.callid
				WHERE f.type='track' ".$q."

				UNION ALL 

				SELECT  c.callfrom as number, c.name as name, f.callid as call_id, f.cdate as comment_date, 
				f.comment , f.followupdate as followup_date, 'IVRS' as module 
				FROM ".$bid."_followup f 
				JOIN ".$bid."_ivrshistory c ON f.callid=c.hid
				WHERE f.type='ivrs' ".$q."

				UNION ALL 

				SELECT  c.callfrom as number, c.name as name, f.callid as call_id, f.cdate as comment_date, 
				f.comment , f.followupdate as followup_date, 'PBX' as module 
				FROM ".$bid."_followup f 
				JOIN ".$bid."_pbxreport c ON f.callid=c.callid
				WHERE f.type='x' ".$q."

				UNION ALL
				
				SELECT  c.number as number, c.name as name, f.callid as call_id, f.cdate as comment_date, 
				f.comment , f.followupdate as followup_date, 'Logs' as module 
				FROM ".$bid."_followup f 
				JOIN ".$bid."_call_logs c ON f.callid=c.call_id
				WHERE f.type='logs' ".$q."

				UNION ALL 

				SELECT  c.number as number, c.name as name, f.leadid as call_id, f.cdate as comment_date, 
				f.comment , f.followupdate as followup_date, 'Leads' as module 
				FROM ".$bid."_leads_followup f 
				JOIN ".$bid."_leads c ON f.leadid=c.leadid 
				WHERE 1 ".$q;
		$sql.=" ORDER BY comment_date DESC ";						
		$sql.=($limit!='')?' limit 0,'.$limit:"";	
		//echo $sql;exit;
		$rst = $this->db->query($sql)->result_array();
		if(count($rst)>0){
			foreach($rst as $skey=>$r){
				if($r['name']==''){
					$rst[$skey]['name'] = 'Unknown';
				}
			}
		}
		return $rst;
	}
	
	function addCallDetail($data){
		$callid = $data['callfrom'].time();
		$cdata =array(
				'callid'	=>$callid,
				'bid'		=>$data['bid'],
				'gid'		=>$data['gid'],
				'source'	=>$data['source'],
				'callfrom'	=>$data['callfrom'],
				'starttime'	=>$data['starttime'],
				'endtime'	=>$data['endtime'],
				'status'	=>'1',
				'filename'	=>$data['filename'],
									
				);
		$this->db->insert($data['bid']."_callhistory", $cdata);
		$ret = array('success'=>'true');
		return $ret;
	}
	function addCallLog($data){
		$number = (substr($data['number'],0,3)=='+91')? substr($data['number'],-10,10):$data['number'];
		$callid = $number.time();
		$file = "";
		if(isset($_FILES['recorded_file']) && $_FILES['recorded_file']['error']==0){
			$ext=pathinfo($_FILES['recorded_file']['name'],PATHINFO_EXTENSION);
			$newName = "call_log".date('YmdHis').".".$ext;
			move_uploaded_file($_FILES['recorded_file']['tmp_name'],$this->config->item('sound_path').$newName);
			$file =$newName;
		}
		$cdata =array(
				'call_id'	=>$callid,
				'bid'		=>$data['bid'],
				'call_type'		=>$data['call_type'],
				'number'	=>$data['number'],
				'name'	=>$data['name'],
				'email'	=>$data['email'],
				'call_time'	=>date('Y-m-d H:i:s',strtotime($data['call_time'])),
				'duration'	=>$data['duration'],
				'recorded_file'	=>$file,
				'created_by'	=>$data['created_by'],
				);
		$this->db->insert($data['bid']."_call_logs", $cdata);
		$ret = array('success'=>'true');
		return $ret;
	}
	
	function update_access(){
		$empDetail=$this->getEmpDetail($_POST['bid'],$_POST['eid']);
		$update = 0;
		if(count($empDetail)>0)
		{
			$sql = $this->db->query("SELECT * FROM user_track_access WHERE eid=".$_POST['eid']." AND bid=".$_POST['bid']);
			if($sql->num_rows()>0)
			{
				$res = $sql->row();
				if($this->input->post('track_incoming')!= $res->track_incoming){$update = 1;}
				else if($this->input->post('track_outgoing')!= $res->track_outgoing){$update = 1;}
				else if($this->input->post('record_conversation')!= $res->record_conversation){$update = 1;}
				if($update)
				{
					$this->db->set('track_incoming',$this->input->post('track_incoming')); 
					$this->db->set('track_outgoing',$this->input->post('track_outgoing')); 
					$this->db->set('record_conversation',$this->input->post('record_conversation'));
					$this->db->where('eid',$_POST['eid']);
					$this->db->where('bid',$_POST['bid']);
					$this->db->update('user_track_access');
				}
				
			}
			else
			{
				$data = array(
					'eid' => $_POST['eid'],
					'bid' => $_POST['bid'],
					'track_incoming' => $this->input->post('track_incoming'),
					'track_outgoing' => $this->input->post('track_outgoing'),
					'record_conversation' => $this->input->post('record_conversation'),
				);
				$this->db->insert("user_track_access", $data);
				$update = 1;
			}
		}
		
		return $update;
		
	}
	
	function add_contact($uid,$contact=array(),$bid){
		
		$this->db->set('name', $contact['name']); 
		$this->db->set('email', $contact['email']); 
		$this->db->set('number', $contact['number']); 
		$this->db->set('bid',$bid); 
		$this->db->set('created_by',$uid); 
		
		$this->db->insert($bid.'_contact');
		return 1;
	}
	
	function del_contacts($uid,$bid){
		
		$s2=$this->db->query("Delete from ".$bid."_contact WHERE created_by='".$uid."'");
		return true;	
	}
}

/* end of model*/
