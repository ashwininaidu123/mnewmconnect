<?php
class Mobappmodel extends Model{
	 
	function Mobappmodel(){
		parent::Model();
		$this->load->model('configmodel','CM');
		$this->load->model('empmodel','EMPM');
		$this->load->model('systemmodel','SYS');
	}

	function authenticate($data){
		$retArr = array();
		//print_r($data);exit;
		$sql = "SELECT u.uid,u.bid,u.eid,b.businessname as businessName,
		u.username as empEmail,u.authkey as authKey
		FROM user u
		LEFT JOIN business b on u.bid=b.bid
		WHERE  u.status='1' AND b.status='1'
		AND u.app='1' AND u.username='".$data['email']."'
		AND u.password='".md5($data['password'])."'";
		$rst = $this->db->query($sql);
		if($rst->num_rows()>0){
			$retArr = $rst->row_array();
			if($retArr['authKey']==''){
				//$authkey = "";for($i = 0; $i<=6 ; $i++){$authkey .= ($i%2==0)? chr(rand(97,122)) : rand(0,9);}
				$authkey = uniqid($retArr['bid'].'.'.$retArr['eid'].'.');
				$sql = "UPDATE user SET authkey='".$authkey."'
				WHERE uid='".$retArr['uid']."'";
				$this->db->query($sql);
				$retArr['authKey']=$authkey;
			}
			$sql = "SELECT * FROM ".$retArr['bid']."_employee WHERE eid='".$retArr['eid']."'";
			$rst = $this->db->query($sql);
			$exe = $rst->row_array();
			$retArr['empName'] = $exe['empname'];
			$retArr['empContact'] = $exe['empnumber'];
			unset($retArr['uid']);
			unset($retArr['bid']);
			unset($retArr['eid']);
		}
		return $retArr;
	}

	function userByKey($key){
		$retArr = array();
		$sql = "SELECT uid,bid,eid FROM `user` WHERE authkey='".$key."'";
		$rst = $this->db->query($sql);
		if($rst->num_rows()>0){
			$retArr = $rst->row_array();
		}
		return $retArr;
	}
	
	function getAllList($data){
		//print_r($data);	//Array ( [bid] => 1 [eid] => 1 [type] => track [ofset] => 0 [limit] => 20 [gid] => )
		$data['roleid'] = $this->EMPM->getRoleid($data['eid'],$data['bid']);
		$retArr = array();
		$mod = $data['type'];
		switch($mod){	//  track|ivrs|x|lead|followup
			case 'track':
				$listArr = $this->getAllTrack($data);
			break;
			case 'ivrs':
				$listArr = $this->getAllIvrs($data);
			break;
			case 'x':
				$listArr = $this->getAllX($data);
			break;
			case 'lead':
				$listArr = $this->getAllLead($data);
			break;
			case 'followup':
				$listArr = $this->getAllFollowup($data);
			break;
			default:
				$listArr = array();
			break;
		}
		//echo "<pre>";print_r($listArr);
		if(empty($listArr)){
			$retArr['nodata'] = '1';
		}else{
			$retArr = $listArr;
			$retArr['groups'] = $this->getAllGroups($data);
		}
		return $retArr;
	}
	
	function getAllTrack($data){
		$listArr = array();
		$q = "";
		$roleid = $this->EMPM->getRoleid($data['eid'],$data['bid']);
		$roleDetail = $this->EMPM->getRoledetail($roleid,$data['bid']);
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (h.eid='".$data['eid']."' or g.eid='".$data['eid']."')";
		}
		
		$sql = 	"SELECT SQL_CALC_FOUND_ROWS h.callid,h.callfrom,h.callername,
				 if(g.groupname!='',g.groupname,'NA') as groupname,
				 h.starttime as calltime,h.dialstatus as status
				 FROM ".$data['bid']."_callhistory h
				 LEFT JOIN ".$data['bid']."_groups g on h.gid=g.gid
				 WHERE h.status!='2' ".$q;
		$sql .=	(isset($data['gid']) && $data['gid']!="") ? " AND h.gid='".$data['gid']."'" : "";
		$sql .=	" ORDER BY h.starttime DESC ";
		$sql .=	" LIMIT ".$data['ofset'].",".$data['limit']."";
		$rst = $this->db->query($sql);
		$listArr['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		if($rst->num_rows()>0){
			$listArr['records'] = $rst->result_array();
		}
		return $listArr;
	}
	
	function getAllIvrs($data){
		$listArr = array();
		$sql = 	"SELECT SQL_CALC_FOUND_ROWS h.hid as callid,
				 h.callfrom,h.name as callername,g.title as groupname,
				 h.datetime as calltime,' ' as status
				 FROM ".$data['bid']."_ivrshistory h
				 LEFT JOIN ".$bid."_ivrs g on h.ivrsid=g.ivrsid
				 WHERE g.bid='".$data['bid']."' ";
		$sql .=	(isset($data['gid']) && $data['gid']!="") ? " AND h.ivrsid='".$data['gid']."'" : "";
		$sql .=	" ORDER BY h.datetime DESC ";
		$sql .=	" LIMIT ".$data['ofset'].",".$data['limit']."";
		$rst = $this->db->query($sql);
		$listArr['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		if($rst->num_rows()>0){
			$listArr['records'] = $rst->result_array();
		}
		return $listArr;
	}
	
	function getAllX($data){
		$listArr = array();
		$sql = 	"SELECT SQL_CALC_FOUND_ROWS h.callid,h.callfrom,
				 h.name as callername,g.title as groupname,
				 h.starttime as calltime,'' as status
				 FROM ".$data['bid']."_pbxreport h
				 LEFT JOIN ".$data['bid']."_pbx g on h.pbxid=g.pbxid
				 WHERE 1 ";
		$sql .=	(isset($data['gid']) && $data['gid']!="") ? " AND h.pbxid='".$data['gid']."'" : "";
		$sql .=	" ORDER BY h.starttime DESC ";
		$sql .=	" LIMIT ".$data['ofset'].",".$data['limit']."";
		$rst = $this->db->query($sql);
		$listArr['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		if($rst->num_rows()>0){
			$listArr['records'] = $rst->result_array();
		}
		return $listArr;
	}
	
	function getAllLead($data){
		$roleid = $this->EMPM->getRoleid($data['eid'],$data['bid']);
		$listArr = array();
		$sql = 	"SELECT SQL_CALC_FOUND_ROWS h.leadid as callid,
				 h.number as callfrom,h.name as callername,g.groupname,
				 h.createdon as calltime,ls.status
				 FROM ".$data['bid']."_leads h
				 LEFT JOIN ".$data['bid']."_leads_groups g on h.gid=g.gid
				 LEFT JOIN leads_status ls on h.lead_status=ls.id
				 WHERE h.status='1' ";
		$sql .= ($roleid!='1') ? " AND h.assignto='".$data['eid']."' " : "";
		$sql .=	(isset($data['gid']) && $data['gid']!="") ? " AND h.gid='".$data['gid']."'" : "";
		$sql .=	" ORDER BY h.createdon DESC ";
		$sql .=	" LIMIT ".$data['ofset'].",".$data['limit']."";
		$rst = $this->db->query($sql);
		$listArr['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		if($rst->num_rows()>0){
			$listArr['records'] = $rst->result_array();
		}
		return $listArr;
	}
	
	function getAllFollowup($data){
		$listArr = array();
		$type = array('1'=>'calltrack',
					  '2'=>'ivrs',
					  '3'=>'pbx',
					  '4'=>'lead'
					);
		$roleQ = ($data['roleid']!='1') ? " AND f.eid='".$data['eid']."' " : "";
		
		//$dataId = isset($data['dataid']) ? " dataid= '".$data['dataid']."'" : "";
		
		$sql = 	"SELECT SQL_CALC_FOUND_ROWS * FROM (
				SELECT f.id as callid,
				if(f.type='calltrack',ch.callfrom,
				(if(f.type='ivrs',ih.callfrom,
				(if(f.type='pbx',ph.callfrom,''))))) as callfrom,
				if(f.type='calltrack',ch.callid,
				(if(f.type='ivrs',ih.hid,
				(if(f.type='pbx',ph.callid,''))))) as dataid,
				if(f.type='calltrack',ch.callername,
				(if(f.type='ivrs',ih.name,
				(if(f.type='pbx',ph.name,''))))) as callername,
				f.type as groupname,
				f.followupdate as calltime,'' as status
				FROM ".$data['bid']."_followup f
				LEFT JOIN ".$data['bid']."_callhistory ch on (f.type='calltrack' AND f.callid=ch.callid)
				LEFT JOIN ".$data['bid']."_ivrshistory ih on (f.type='ivrs' AND f.callid=ih.hid)
				LEFT JOIN ".$data['bid']."_pbxreport ph on (f.type='pbx' AND f.callid=ph.callid)
				WHERE f.type!='support' AND date(f.followupdate)>=CURDATE() ".$roleQ."
				UNION 
				SELECT f.id as callid,l.number as callfrom,
				f.leadid as dataid,
				l.name as callername,'lead' as groupname,
				f.followupdate as calltime,'' as status
				FROM ".$data['bid']."_followup f
				LEFT JOIN ".$data['bid']."_leads l on (f.callid=l.leadid)
				WHERE 1 AND date(f.followupdate)>=CURDATE() ".$roleQ."
				) a WHERE 1 ";
		$sql .=	(isset($data['gid']) && $data['gid']!="" && isset($type[$data['gid']])) ? " AND a.groupname='".$type[$data['gid']]."'" : "";
		$sql .=	" ORDER BY a.calltime ASC ";
		$sql .=	" LIMIT ".$data['ofset'].",".$data['limit']."";
		$rst = $this->db->query($sql);
		$listArr['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		if($rst->num_rows()>0){
			$listArr['records'] = $rst->result_array();
		}
		return $listArr;
	}

	function getAllGroups($data){
		//print_r($data);
		$retArr = array();
		$listArr = array();
		$mod = $data['type'];
		switch($mod){	//  track|ivrs|x|lead|followup
			case 'track':
				$sql = "SELECT gid,groupname FROM ".$data['bid']."_groups WHERE bid='".$data['bid']."' AND status='1'";
				$rst = $this->db->query($sql);
				if($rst->num_rows()>0){
					$listArr = $rst->result_array();
				}
			break;
			case 'ivrs':
				$sql = "SELECT ivrsid as gid,title as groupname FROM ivrs WHERE bid='".$data['bid']."' AND status='1'";
				$rst = $this->db->query($sql);
				if($rst->num_rows()>0){
					$listArr = $rst->result_array();
				}
			break;
			case 'x':
				$sql = "SELECT pbxid as gid,title as groupname FROM ".$data['bid']."_pbx WHERE bid='".$data['bid']."' AND status='1'";
				$rst = $this->db->query($sql);
				if($rst->num_rows()>0){
					$listArr = $rst->result_array();
				}
			break;
			case 'lead':
				$sql = "SELECT gid,groupname FROM ".$data['bid']."_leads_groups WHERE bid='".$data['bid']."' AND status='1'";
				$rst = $this->db->query($sql);
				if($rst->num_rows()>0){
					$listArr = $rst->result_array();
				}
			break;
			case 'followup':
					$listArr = array(
						array('gid'=>'1','groupname'=>'Track'),
						array('gid'=>'2','groupname'=>'IVRS'),
						array('gid'=>'3','groupname'=>'X'),
						array('gid'=>'4','groupname'=>'Lead'),
					);
			break;
			default:
				$listArr = array();
			break;
		}
		if(empty($listArr)){
			$retArr['nodata'] = '1';
		}else{
			$gArr[] = array("key"=>"0","val"=>"All");
			foreach($listArr as $group){
				$gArr[] = array("key"=>$group['gid'],"val"=>$group['groupname']);
			}
			$retArr = $gArr;
		}
		
		return $retArr;
	}
	
	function getDetails($data){
		$dataArr = array();
		$retArr = array();
		
		$mod = $data['type'];
		switch($mod){	//  track|ivrs|x|lead|followup
			case 'track':
				$dataArr = $this->genDataTrack($data);
			break;
			case 'ivrs':
				$dataArr = $this->genDataIvrs($data);
			break;
			case 'x':
				$dataArr = $this->genDataX($data);
			break;
			case 'lead':
				$dataArr = $this->genDataLead($data);
			break;
			case 'followup':
				$dataArr = $this->genDataFollowup($data);
			break;
		}
		if(empty($dataArr)){
			$retArr['nodata'] = '1';
		}else{
			$retArr['fields'] = $dataArr;
		}
		return $retArr;
	}
	
	function getFollowupFielsd($data){
		$fields = array();
		$retArr = array();
		
		$fields[] = array(
					'name'		=>'comment'
					,'label'	=>'Comment'
					,'type'		=>'textarea'
					,'options'	=>''
					,'value'	=>''
				);
		
		$fields[] = array(
					'name'		=>'followupdate'
					,'label'	=>'Next Followup Date'
					,'type'		=>'datetime'
					,'options'	=>''
					,'value'	=>''
				);
		
		$fields[] = array(
					'name'		=>'alert'
					,'label'	=>'Alert Type'
					,'type'		=>'dropdown'
					,'options'	=>array('1'=>'Mail','2'=>'SMS','3'=>'Both')
					,'value'	=>''
				);
		
		
		if(empty($fields)){
			$retArr['nodata'] = '1';
		}else{
			$retArr['fields'] = $fields;
		}
		return $retArr;
	}
	
	function genDataTrack($data){
		$roleid = $this->EMPM->getRoleid($data['eid'],$data['bid']);
		$roleDetail = $this->EMPM->getRoledetail($roleid,$data['bid']);
		$fieldset = $this->CM->getFields('6',$data['bid']);
		$itemDetail = $this->CM->getDetail('6',$data['callid'],$formname='',$data['bid']);
		$fields = array();
		$fields[] = array(
						'name'		=>'callid'
						,'label'	=>''
						,'type'		=>'hidden'
						,'options'	=>''
						,'value'	=>$data['callid']
					);
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					
					$fieldtype = 'hidden';
					$fieldtype = (in_array($field['fieldname'],
									array(	'callfrom',
											'gid',
											'eid',
											'starttime',
											'endtime',
											'calid',
											'dialstatus',
											'region',
											'keyword',
											'calid',
											'rate',
											'pulse',
											'source',
											'region')
								 )? 'label' : $fieldtype);
					$fieldtype = (in_array($field['fieldname'],
									array(	'refid',
											'callername',
											'caller_email',
											'callerbusiness')
								 )? 'text' : $fieldtype);
								 
					$fieldtype = (in_array($field['fieldname'],
									array(	'remark',
											'calleraddress')
								 )? 'textarea' : $fieldtype);
								 
					$fieldtype = (in_array($field['fieldname'],
									array(	'assignto')
								 )? 'dropdown' : $fieldtype);
								 
					$field['options'] = (in_array($field['fieldname'],
									array('assignto')
								 )? $this->TrackGroupEmpList($data['bid']) : $field['options']);
								 
					$itemDetail[$field['fieldname']] = (in_array($field['fieldname'],
									array(	'assignto')
								 )? $itemDetail['asto'] : $itemDetail[$field['fieldname']]);
								 
					$fields[] = array(
						'name'		=>$field['fieldname']
						,'label'	=>(($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname'])
						,'type'		=>$fieldtype
						,'options'	=>$field['options']
						,'value'	=>(($itemDetail[$field['fieldname']]!='') 
											? $itemDetail[$field['fieldname']] 
											: (($fieldtype=='label') ? 'NA' : ''))
					);
				}
			}elseif($field['type']=='c' && $field['show']){
				$optArr = array();
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					if(in_array($field['fieldtype'],array('checkbox','dropdown','radio'))){
						$optAr = explode("\n",$field['options']);
						foreach($optAr as $opt){ $op = current(explode("|",$opt));$optArr[$op] = $op;}
					}
					$value = isset($itemDetail['custom['.$field['fieldid'].']'])
								?(is_array($itemDetail['custom['.$field['fieldid'].']'])
									? implode(",",$itemDetail['custom['.$field['fieldid'].']'])
									: $itemDetail['custom['.$field['fieldid'].']'])
								:'';
					$fields[] = array(
						'name'		=>(in_array($field['fieldtype'],array('checkbox')) 
											? 'custom['.$field['fieldid'].'][]' 
											: 'custom['.$field['fieldid'].']')
						,'label'	=>$field['customlabel']
						,'type'		=>$field['fieldtype']
						,'options'	=>((!empty($optArr)) ? $optArr : '')
						,'value'	=>$value
					);
				}
			}
		}
		/*
		add-on fields for convert to lead and support
		*/	
		/*
		$feature = $this->getFeatures($data['bid']);
		if(in_array(13,$feature)){
			if($itemDetail['leadid']=='0'){
				$fields[] = array(
								'name'		=>'convertlead'
								,'label'	=>'Convert As Lead'
								,'type'		=>'checkbox'
								,'options'	=>array('1'=>'')
								,'value'	=>''
							);
			}else{
				$fields[] = array(
								'name'		=>'updatelead'
								,'label'	=>'Update Lead'
								,'type'		=>'checkbox'
								,'options'	=>array('1'=>'')
								,'value'	=>''
							);
			}
			$fields[] = array(
								'name'		=>'leadgroup'
								,'label'	=>'Lead Groups'
								,'type'		=>'dropdown'
								,'options'	=>$this->LeadGroupList($data['bid'])
								,'value'	=>''
							);
			$fields[] = array(
								'name'		=>'leademp'
								,'label'	=>'Lead assign to'
								,'type'		=>'dropdown'
								,'options'	=>$this->LeadGroupEmpList($data['bid'])
								,'value'	=>''
							);
		}
		if(in_array(14,$feature)){
			if($itemDetail['tktid']=='0'){
				$fields[] = array(
								'name'		=>'convertsupport'
								,'label'	=>'Convert as Support'
								,'type'		=>'checkbox'
								,'options'	=>array('1'=>'')
								,'value'	=>''
							);
			}else{
				$fields[] = array(
								'name'		=>'updatesupport'
								,'label'	=>'Update Support'
								,'type'		=>'checkbox'
								,'options'	=>array('1'=>'')
								,'value'	=>''
							);
			}
		}
		*/
		return $fields;
	}
	
	function genDataIvrs($data){
		$roleid = $this->EMPM->getRoleid($data['eid'],$data['bid']);
		$roleDetail = $this->EMPM->getRoledetail($roleid,$data['bid']);
		$fieldset = $this->CM->getFields('16',$data['bid']);
		$itemDetail = $this->CM->getDetail('16',$data['callid'],$formname='',$data['bid']);
		$fields = array();
		$fields[] = array(
						'name'		=>'callid'
						,'label'	=>''
						,'type'		=>'hidden'
						,'options'	=>''
						,'value'	=>$data['callid']
					);
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					
					$fieldtype = 'hidden';
					$fieldtype = (in_array($field['fieldname'],
									array(	'ivrstitle',
											'callfrom',
											'datetime',
											'endtime',
											'options',
											'employee')
								 )? 'label' : $fieldtype);
					$fieldtype = (in_array($field['fieldname'],
									array(	'name',
											'email')
								 )? 'text' : $fieldtype);
								 
					$itemDetail[$field['fieldname']] = ($field['fieldname']=='options') ? str_replace("<br>","",$itemDetail[$field['fieldname']]) : $itemDetail[$field['fieldname']] ;
					
					$fields[] = array(
						'name'		=>$field['fieldname']
						,'label'	=>(($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname'])
						,'type'		=>$fieldtype
						,'options'	=>$field['options']
						,'value'	=>(($itemDetail[$field['fieldname']]!='') 
											? $itemDetail[$field['fieldname']] 
											: (($fieldtype=='label') ? 'NA' : ''))
					);
				}
			}elseif($field['type']=='c' && $field['show']){
				$optArr = array();
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					if(in_array($field['fieldtype'],array('checkbox','dropdown','radio'))){
						$optAr = explode("\n",$field['options']);
						foreach($optAr as $opt){ $op = current(explode("|",$opt));$optArr[$op] = $op;}
					}
					$value = isset($itemDetail['custom['.$field['fieldid'].']'])
								?(is_array($itemDetail['custom['.$field['fieldid'].']'])
									? implode(",",$itemDetail['custom['.$field['fieldid'].']'])
									: $itemDetail['custom['.$field['fieldid'].']'])
								:'';
					$fields[] = array(
						'name'		=>(in_array($field['fieldtype'],array('checkbox')) 
											? 'custom['.$field['fieldid'].'][]' 
											: 'custom['.$field['fieldid'].']')
						,'label'	=>$field['customlabel']
						,'type'		=>$field['fieldtype']
						,'options'	=>((!empty($optArr)) ? $optArr : '')
						,'value'	=>$value
					);
				}
			}
		}
		return $fields;
	}
	
	function genDataX($data){
		$roleid = $this->EMPM->getRoleid($data['eid'],$data['bid']);
		$roleDetail = $this->EMPM->getRoledetail($roleid,$data['bid']);
		$fieldset = $this->CM->getFields('24',$data['bid']);
		$itemDetail = $this->CM->getDetail('24',$data['callid'],$formname='',$data['bid']);
		$fields[] = array(
						'name'		=>'callid'
						,'label'	=>''
						,'type'		=>'hidden'
						,'options'	=>''
						,'value'	=>$data['callid']
					);
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					
					$fieldtype = 'hidden';
					$fieldtype = (in_array($field['fieldname'],
									array(	'pbxtitle',
											'callfrom',
											'starttime',
											'endtime',
											'pulse',
											'extensions')
								 )? 'label' : $fieldtype);
					$fieldtype = (in_array($field['fieldname'],
									array(	'name',
											'email')
								 )? 'text' : $fieldtype);
								 
					$fields[] = array(
						'name'		=>$field['fieldname']
						,'label'	=>(($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname'])
						,'type'		=>$fieldtype
						,'options'	=>$field['options']
						,'value'	=>(($itemDetail[$field['fieldname']]!='') 
											? $itemDetail[$field['fieldname']] 
											: (($fieldtype=='label') ? 'NA' : ''))
					);
				}
			}elseif($field['type']=='c' && $field['show']){
				$optArr = array();
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					if(in_array($field['fieldtype'],array('checkbox','dropdown','radio'))){
						$optAr = explode("\n",$field['options']);
						foreach($optAr as $opt){ $op = current(explode("|",$opt));$optArr[$op] = $op;}
					}
					$value = isset($itemDetail['custom['.$field['fieldid'].']'])
								?(is_array($itemDetail['custom['.$field['fieldid'].']'])
									? implode(",",$itemDetail['custom['.$field['fieldid'].']'])
									: $itemDetail['custom['.$field['fieldid'].']'])
								:'';
					$fields[] = array(
						'name'		=>(in_array($field['fieldtype'],array('checkbox')) 
											? 'custom['.$field['fieldid'].'][]' 
											: 'custom['.$field['fieldid'].']')
						,'label'	=>$field['customlabel']
						,'type'		=>$field['fieldtype']
						,'options'	=>((!empty($optArr)) ? $optArr : '')
						,'value'	=>$value
					);
				}
			}
		}
		return $fields;
	}
	
	function genDataLead($data){
		$roleid = $this->EMPM->getRoleid($data['eid'],$data['bid']);
		$roleDetail = $this->EMPM->getRoledetail($roleid,$data['bid']);
		$fieldset = $this->CM->getFields('26',$data['bid']);
		$itemDetail = $this->CM->getDetail('26',$data['callid'],$formname='',$data['bid']);
		$itemDetail['enteredby'] = $itemDetail['enteredempname'];
		$fields = array();
		$fields[] = array(
						'name'		=>'callid'
						,'label'	=>''
						,'type'		=>'hidden'
						,'options'	=>''
						,'value'	=>$data['callid']
					);
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					
					$fieldtype = 'hidden';
					$fieldtype = (in_array($field['fieldname'],
									array(	'source',
											'createdon',
											'lastmodified',
											'enteredby',
											'status')
								 )? 'label' : $fieldtype);
					$fieldtype = (in_array($field['fieldname'],
									array(	'name',
											'email',
											'number',
											'caller_bus',
											'refId')
								 )? 'text' : $fieldtype);
								 
					$fieldtype = (in_array($field['fieldname'],
									array(	'remark',
											'caller_add')
								 )? 'textarea' : $fieldtype);
								 
					$fieldtype = (in_array($field['fieldname'],
									array(	'gid',
											'assignto',
											'lead_status')
								 )? 'dropdown' : $fieldtype);
								 
					$field['options'] = (in_array($field['fieldname'],
									array('gid')
								 )? $this->LeadGroupList($data['bid'],$itemDetail['gid']) : $field['options']);
								 
					$field['options'] = (in_array($field['fieldname'],
									array('assignto')
								 )? $this->LeadGroupEmpList($data['bid'],$itemDetail['gid']) : $field['options']);
								 
					$field['options'] = (in_array($field['fieldname'],
									array('lead_status')
								 )? $this->getleadstatus() : $field['options']);
								 
					$fields[] = array(
						'name'		=>$field['fieldname']
						,'label'	=>(($field['customlabel']!="")
										?$field['customlabel']
										:''//$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										)
						,'type'		=>$fieldtype
						,'options'	=>$field['options']
						,'value'	=>(($itemDetail[$field['fieldname']]!='') 
											? $itemDetail[$field['fieldname']] 
											: (($fieldtype=='label') ? 'NA' : ''))
					);
				}
			}elseif($field['type']=='c' && $field['show']){
				$optArr = array();
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					if(in_array($field['fieldtype'],array('checkbox','dropdown','radio'))){
						$optAr = explode("\n",$field['options']);
						foreach($optAr as $opt){ $op = current(explode("|",$opt));$optArr[$op] = $op;}
					}
					$value = isset($itemDetail['custom['.$field['fieldid'].']'])
								?(is_array($itemDetail['custom['.$field['fieldid'].']'])
									? implode(",",$itemDetail['custom['.$field['fieldid'].']'])
									: $itemDetail['custom['.$field['fieldid'].']'])
								:'';
					$fields[] = array(
						'name'		=>(in_array($field['fieldtype'],array('checkbox')) 
											? 'custom['.$field['fieldid'].'][]' 
											: 'custom['.$field['fieldid'].']')
						,'label'	=>$field['customlabel']
						,'type'		=>$field['fieldtype']
						,'options'	=>((!empty($optArr)) ? $optArr : '')
						,'value'	=>$value
					);
				}
			}
		}
		return $fields;
	}
	
	function genDataFollowup($data){
		$itemDetail = $this->getFollowup($data);
		$fields = array();
		$fields[] = array(
						'name'		=>'callid'
						,'label'	=>''
						,'type'		=>'hidden'
						,'options'	=>''
						,'value'	=>$data['callid']
					);
		$fields[] = array(
						'name'		=>'dataid'
						,'label'	=>''
						,'type'		=>'hidden'
						,'options'	=>''
						,'value'	=>$itemDetail['dataid']
					);
		$fields[] = array(
						'name'		=>'number'
						,'label'	=>'Followup Number'
						,'type'		=>'label'
						,'options'	=>''
						,'value'	=>$itemDetail['callfrom']
					);
		$fields[] = array(
						'name'		=>'Name'
						,'label'	=>'Followup Name'
						,'type'		=>'label'
						,'options'	=>''
						,'value'	=>$itemDetail['callername']
					);
		$fields[] = array(
						'name'		=>'followupdate'
						,'label'	=>'Date Time'
						,'type'		=>'label'
						,'options'	=>''
						,'value'	=>$itemDetail['followupdate']
					);
		$fields[] = array(
						'name'		=>'comment'
						,'label'	=>'Comment'
						,'type'		=>'label'
						,'options'	=>''
						,'value'	=>$itemDetail['comment']
					);
		return $fields;
	}
	
	function TrackGroupEmpList($bid,$grid=''){
		$listArr = array();
		$sql = "select e.* from ".$bid."_employee e
				 LEFT JOIN ".$bid."_group_emp ge on e.eid=ge.eid
				 LEFT JOIN ".$bid."_groups g on g.gid=ge.gid
				 where e.status=1 ".(($grid!='') ? "  AND g.gid='".$grid."'" : "")."
				 ORDER BY e.empname";
		$rst = $this->db->query($sql);
		if($rst->num_rows()>0){
			$list = $rst->result_array();
			foreach($list as $item){
				$listArr[$item['eid']] = $item['empname'];
			}
		}
		return $listArr;
	}
	
	function LeadGroupEmpList($bid,$grid){
		$listArr = array();
		$sql = "select e.* from ".$bid."_employee e
				LEFT JOIN ".$bid."_leads_grpemp ge on e.eid=ge.eid
				LEFT JOIN ".$bid."_leads_groups g on g.gid=ge.gid
				where e.status=1 AND g.gid='".$grid."' 
				ORDER BY e.empname";
		$rst = $this->db->query($sql);
		if($rst->num_rows()>0){
			$list = $rst->result_array();
			foreach($list as $item){
				$listArr[$item['eid']] = $item['empname'];
			}
		}
		return $listArr;
	}
	
	function LeadGroupList($bid){
		$listArr = array();
		$sql = "SELECT gid,groupname FROM ".$bid."_leads_groups WHERE bid='".$bid."' AND status='1'";
		$rst = $this->db->query($sql);
		if($rst->num_rows()>0){
			$list = $rst->result_array();
			foreach($list as $item){
				$listArr[$item['gid']] = $item['groupname'];
			}
		}
		return $listArr;
	}
	
	function getleadstatus(){
		$res=array();	
		$sql=$this->db->query("SELECT * FROM leads_status ");
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $r)	{
				$res[$r['id']]=$r['status'];
			}
		}
		return $res;
	}
	
	function getFollowup($data){
		$fid = $data['callid'];
		$mod = $data['groupname'];
		$bid = $data['bid'];
		$retArr = array();
		switch($mod){
			case 'lead':
				$sql = "SELECT f.id as callid,f.comment,f.followupdate,
						l.number as callfrom,l.leadid as dataid,
						l.name as callername,f.type as groupname,
						f.followupdate as calltime,'' as status
						FROM ".$bid."_leads_followup f
						LEFT JOIN ".$bid."_leads l on (f.leadid=l.leadid)
						WHERE f.id='".$fid."'";
				$retArr =  $this->db->query($sql)->row_array();
			break;
			case 'calltrack':
				$sql = "SELECT f.id as callid,f.comment,f.followupdate,
						if(f.type='calltrack',ch.callfrom,'') as callfrom,
						if(f.type='calltrack',ch.callername,'') as callername,
						f.type as groupname,ch.callid as dataid,
						f.followupdate as calltime,'' as status
						FROM ".$bid."_followup f
						LEFT JOIN ".$bid."_callhistory ch on (f.type='calltrack' AND f.callid=ch.callid)
						WHERE f.id='".$fid."'";
				$retArr =  $this->db->query($sql)->row_array();
			break;
			case 'ivrs':
				$sql = "SELECT f.id as callid,f.comment,f.followupdate,
						if(f.type='ivrs',ih.callfrom,'') as callfrom,
						if(f.type='ivrs',ih.name,'') as callername,
						f.type as groupname,ih.hid as dataid,
						f.followupdate as calltime,'' as status
						FROM ".$bid."_followup f
						LEFT JOIN ".$bid."_ivrshistory ih on (f.type='ivrs' AND f.callid=ih.hid)
						WHERE f.id='".$fid."'";
				$retArr =  $this->db->query($sql)->row_array();
			break;
			case 'pbx':
				$sql = "SELECT f.id as callid,f.comment,f.followupdate,
						if(f.type='pbx',ph.callfrom,'') as callfrom,
						if(f.type='pbx',ph.name,'') as callername,
						f.type as groupname,ph.callid as dataid,
						f.followupdate as calltime,'' as status
						FROM ".$bid."_followup f
						LEFT JOIN ".$bid."_pbxreport ph on (f.type='pbx' AND f.callid=ph.callid)
						WHERE f.id='".$fid."'";
				$retArr =  $this->db->query($sql)->row_array();
			break;
		}
		return $retArr;
	}
	
	function getFollowupHistory($data){
		$mod = $data['type'];
		$bid = $data['bid'];
		$dataid = $data['dataid'];
		$retArr = array();
		switch($mod){
			case 'lead':
				$sql = "SELECT SQL_CALC_FOUND_ROWS f.id as callid,f.comment,f.followupdate,
						l.number as callfrom,l.leadid as dataid,
						l.name as callername,'lead' as groupname,
						f.followupdate as calltime,'' as status
						FROM ".$bid."_followup f
						LEFT JOIN ".$bid."_leads l on (f.callid=l.leadid)
						WHERE 1 ";
				$sql .= " AND f.leadid='".$dataid."'";
				$sql .= " ORDER BY f.followupdate ASC";
				$sql .= " LIMIT ".$data['ofset'].",".$data['limit']."";
				$rst = $this->db->query($sql);
				$retArr['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
				if($rst->num_rows()>0){
					$retArr['records'] = $rst->result_array();
				}
			break;
			case 'calltrack':
				$sql = "SELECT SQL_CALC_FOUND_ROWS f.id as callid,f.comment,f.followupdate,
						if(f.type='calltrack',ch.callfrom,'') as callfrom,
						if(f.type='calltrack',ch.callername,'') as callername,
						f.type as groupname,ch.callid as dataid,
						f.followupdate as calltime,'' as status
						FROM ".$bid."_followup f
						LEFT JOIN ".$bid."_callhistory ch on (f.type='calltrack' AND f.callid=ch.callid)
						WHERE 1 ";
				$sql .= " AND f.callid='".$dataid."'";
				$sql .= " ORDER BY f.followupdate ASC";
				$sql .= " LIMIT ".$data['ofset'].",".$data['limit']."";
				$rst = $this->db->query($sql);
				$retArr['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
				if($rst->num_rows()>0){
					$retArr['records'] = $rst->result_array();
				}
			break;
			case 'ivrs':
				$sql = "SELECT SQL_CALC_FOUND_ROWS f.id as callid,f.comment,f.followupdate,
						if(f.type='ivrs',ih.callfrom,'') as callfrom,
						if(f.type='ivrs',ih.name,'') as callername,
						f.type as groupname,ih.hid as dataid,
						f.followupdate as calltime,'' as status
						FROM ".$bid."_followup f
						LEFT JOIN ".$bid."_ivrshistory ih on (f.type='ivrs' AND f.callid=ih.hid)
						WHERE 1 ";
				$sql .= " AND f.callid='".$dataid."'";
				$sql .= " ORDER BY f.followupdate ASC";
				$sql .= " LIMIT ".$data['ofset'].",".$data['limit']."";
				$rst = $this->db->query($sql);
				$retArr['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
				if($rst->num_rows()>0){
					$retArr['records'] = $rst->result_array();
				}
			break;
			case 'pbx':
				$sql = "SELECT SQL_CALC_FOUND_ROWS f.id as callid,f.comment,f.followupdate,
						if(f.type='pbx',ph.callfrom,'') as callfrom,
						if(f.type='pbx',ph.name,'') as callername,
						f.type as groupname,ph.callid as dataid,
						f.followupdate as calltime,'' as status
						FROM ".$bid."_followup f
						LEFT JOIN ".$bid."_pbxreport ph on (f.type='pbx' AND f.callid=ph.callid)
						WHERE 1 ";
				$sql .= " AND f.callid='".$dataid."'";
				$sql .= " ORDER BY f.followupdate ASC";
				$sql .= " LIMIT ".$data['ofset'].",".$data['limit']."";
				$rst = $this->db->query($sql);
				$retArr['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
				if($rst->num_rows()>0){
					$retArr['records'] = $rst->result_array();
				}
			break;
		}
		return $retArr;
	}
	
	function postDetails($data){
		//echo "<pre>";print_r($data);exit;
		$dataArr = array();
		$retArr = array();
		
		$mod = $data['type'];
		switch($mod){	//  track|ivrs|x|lead|followup
			case 'track':
				$dataArr = $this->UpdateTrack($data);
			break;
			case 'ivrs':
				$dataArr = $this->UpdateIvrs($data);
			break;
			case 'x':
				$dataArr = $this->UpdateX($data);
			break;
			case 'lead':
				$dataArr = $this->UpdateLead($data);
			break;
			case 'followup':
				$dataArr = $this->CreateFollowup($data);
			break;
		}
		return true;
	}
	
	function UpdateTrack($data){
		$sql = "UPDATE ".$data['bid']."_callhistory SET ";
		$sql .= isset($data['callername'])		? " callername		='".$data['callername']."',"	: "";
		$sql .= isset($data['calleraddress'])	? " calleraddress	='".$data['calleraddress']."',"	: "";
		$sql .= isset($data['caller_email'])	? " caller_email	='".$data['caller_email']."',"	: "";
		$sql .= isset($data['callerbusiness'])	? " callerbusiness	='".$data['callerbusiness']."',": "";
		$sql .= isset($data['remark'])			? " remark			='".$data['remark']."',"		: "";
		$sql .= isset($data['refid'])			? " refid			='".$data['refid']."',"			: "";
		$sql .= isset($data['assignto'])		? " assignto		='".$data['assignto']."',"		: "";
		$sql .= " callid			='".$data['callid']."'";
		$sql .= " WHERE callid		='".$data['callid']."'";
		$this->db->query($sql);
		
		if(isset($_POST['custom'])){
			//echo "<pre>";
			foreach($_POST['custom'] as $fid=>$val){
				$this->db->query("DELETE FROM ".$data['bid']."_customfieldsvalue 
								  where bid = '".$data['bid']."' 
								  AND modid = '6' 
								  AND fieldid = '".$fid."' 
								  AND dataid= '".$data['callid']."'");
				$sql = "REPLACE INTO ".$data['bid']."_customfieldsvalue SET
						 bid			= '".$data['bid']."'
						,modid			= '6'
						,fieldid		= '".$fid."'
						,dataid			= '".$data['callid']."'
						,value			= '".mysql_real_escape_string((is_array($val)?implode(',',$val):$val))."'";
				$this->db->query($sql);
			}
			//echo "</pre>";
		}
	}
	
	function UpdateIvrs($data){
		$sql = "UPDATE ".$data['bid']."_ivrshistory SET ";
		$sql .= isset($data['name'])		? " name		='".$data['name']."',"	: "";
		$sql .= isset($data['email'])		? " email		='".$data['email']."',"	: "";
		$sql .=" hid		='".$data['callid']."'";
		$sql .=" WHERE hid		='".$data['callid']."'";
		$this->db->query($sql);
		
		if(isset($_POST['custom'])){
			foreach($_POST['custom'] as $fid=>$val){
				$this->db->query("DELETE FROM ".$data['bid']."_customfieldsvalue 
								  where bid= '".$data['bid']."' 
								  AND modid= '16' 
								  AND fieldid = '".$fid."' 
								  AND dataid= '".$data['callid']."'");
				$sql = "REPLACE INTO ".$data['bid']."_customfieldsvalue SET
						 bid			= '".$data['bid']."'
						,modid			= '16'
						,fieldid		= '".$fid."'
						,dataid			= '".$data['callid']."'
						,value			= '".mysql_real_escape_string((is_array($val)?implode(',',$val):$val))."'";
				$this->db->query($sql);
				
			}
		}
	}
	
	function UpdateX($data){
		$sql = "UPDATE ".$data['bid']."_pbxreport SET ";
		$sql .= isset($data['name'])		? " name		='".$data['name']."',"	: "";
		$sql .= isset($data['email'])		? " email		='".$data['email']."',"	: "";
		$sql .=" callid		='".$data['callid']."'";
		$sql .=" WHERE callid		='".$data['callid']."'";
		$this->db->query($sql);
		
		if(isset($_POST['custom'])){
			foreach($_POST['custom'] as $fid=>$val){
				$this->db->query("DELETE FROM ".$data['bid']."_customfieldsvalue 
								  where bid= '".$data['bid']."' 
								  AND modid= '24' 
								  AND fieldid = '".$fid."' 
								  AND dataid= '".$data['callid']."'");
				$sql = "REPLACE INTO ".$data['bid']."_customfieldsvalue SET
						 bid			= '".$data['bid']."'
						,modid			= '24'
						,fieldid		= '".$fid."'
						,dataid			= '".$data['callid']."'
						,value			= '".mysql_real_escape_string((is_array($val)?implode(',',$val):$val))."'";
				$this->db->query($sql);
				
			}
		}
	}
	
	function UpdateLead($data){
		$sql = "UPDATE ".$data['bid']."_leads SET ";
		$sql .= isset($data['gid'])				? " gid		='".$data['gid']."',"	: "";
		$sql .= isset($data['assignto'])		? " assignto		='".$data['assignto']."',"		: "";
		$sql .= isset($data['name'])			? " name			='".$data['name']."',"			: "";
		$sql .= isset($data['email'])			? " email			='".$data['email']."',"			: "";
		$sql .= isset($data['number'])			? " number			='".$data['number']."',"		: "";
		$sql .= isset($data['caller_add'])		? " caller_add		='".$data['caller_add']."',"	: "";
		$sql .= isset($data['caller_bus'])		? " caller_bus		='".$data['caller_bus']."',"	: "";
		$sql .= isset($data['remark'])			? " remark			='".$data['remark']."',"		: "";
		$sql .= isset($data['refId'])			? " refId			='".$data['refId']."',"			: "";
		$sql .= isset($data['lead_status'])		? " lead_status		='".$data['lead_status']."',"	: "";
		$sql .=" leadid		='".$data['callid']."'";
		$sql .=" WHERE leadid		='".$data['callid']."'";
		$this->db->query($sql);
		
		if(isset($_POST['custom'])){
			foreach($_POST['custom'] as $fid=>$val){
				$this->db->query("DELETE FROM ".$data['bid']."_customfieldsvalue 
								  where bid= '".$data['bid']."' 
								  AND modid= '26' 
								  AND fieldid = '".$fid."' 
								  AND dataid= '".$data['callid']."'");
				$sql = "REPLACE INTO ".$data['bid']."_customfieldsvalue SET
						 bid			= '".$data['bid']."'
						,modid			= '26'
						,fieldid		= '".$fid."'
						,dataid			= '".$data['callid']."'
						,value			= '".mysql_real_escape_string((is_array($val)?implode(',',$val):$val))."'";
				$this->db->query($sql);
				
			}
		}
	}
	
	function CreateFollowup($data){
		$table = ($data['groupname']=='lead') ? '_leads_followup' : '_followup';
		$id=$this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM ".$data['bid'].$table)->row()->id;
		$sql = "INSERT INTO ".$data['bid'].$table ." SET ";
		$sql .=" id				='".$id."',";
		$sql .=" bid			='".$data['bid']."',";
		$sql .=" callid			='".$data['callid']."',";
		$sql .=" comment		='".$data['comment']."',";
		$sql .=" type			='".$data['groupname']."',";
		$sql .=" alert			='".$data['alert']."',";
		$sql .=" followupdate	='".$data['followupdate']."',";
		$sql .=" eid			='".$data['eid']."'";
		$this->db->query($sql);
	}
	
	function getFeatures($bid){
		$sql=$this->db->query("SELECT feature_id FROM `business_feature` where bid=".$bid);
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $ress){
				$res[]=$ress['feature_id'];
			}
		}
		return $res;
	}
}

/* end of model*/
