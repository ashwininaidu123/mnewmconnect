<?php
Class Leadsmodel extends Model
{
	function Leadsmodel(){
		 parent::Model();
		 $this->load->model('reportmodel');
	}

	function list_leads($type,$ofset='0',$limit='20',$tab){
		$lead_status = $type;
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$modid = ($lead_status == 1) ? '46' : '26' ;
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$q= '';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		if(isset($s['module']) && $s['module'] != 'lead'){
			$this->session->unset_userdata('search');
			$s = array();
		}
		if(isset($_POST['Adv_submit'])){
			$this->session->set_userdata('Adsearch',$_POST);
		}else{
			$this->session->unset_userdata('Adsearch');
		}
		if(isset($_POST['sav_search'])){
			Save_search_data($bid,$this->session->userdata('eid'),json_encode($_POST),$modid);
			$this->session->set_userdata('Adsearch',$_POST);
		}
		if($tab>0){
			$qs = get_save_searchrow($bid,$modid,$this->session->userdata('eid'),$tab);
			$content = (array)json_decode($qs['content']);
			$this->session->set_userdata('Adsearch',$content);
		}
		if($this->session->userdata('Adsearch')){
			$Ads = $this->session->userdata('Adsearch');
		}
		if(isset($Ads) && sizeof($Ads)>0){
			$this->session->unset_userdata('search');
			$s = array();
			switch($Ads['timespan']){
				case 'all':
				default :
						 $q.='';	
				break;
				case 'today':
						 $q.=" and date(a.createdon)>= '".date('Y-m-d')."'";
				break;
				case 'last7':
						$date=date('Y-m-d',strtotime('-6 days'));
						$q.=" and date(a.createdon)>= '".$date."'";	
				break;			
				case 'month':
						$date=date('Y-m-01');
						$q.=" and date(a.createdon)>= '".$date."'";	
				break;				
			}
			if(isset($Ads['multiselect_gid']) && sizeof($Ads['multiselect_gid'])>0){
				$gids=implode(",",$Ads['multiselect_gid']);
				      $q.=" and a.gid in (".$gids.")";
				
			}
			if(isset($Ads['multiselect_eids']) && sizeof($Ads['multiselect_eids'])>0){
				$eids=implode(",",$Ads['multiselect_eids']);
				      $q.=" and (a.assignto IN (".$eids.") OR a.enteredby IN (".$eids.") )";
				
			}
			$custiom_ids=array();
			$cust=0;
			$field_array=array("gid"=>"d.groupname",
			                   "assignto"=>"e.empname",
							   "createdon"=>"date(a.createdon)",
			                   "lastmodified"=>"date(a.lastmodified)",
			                   "name"=>"a.name",
			                   "number"=>"a.number",
			                   "email"=>"a.email",
			                   "caller_add"=>"a.caller_add",
			                   "caller_bus"=>"a.caller_bus",
			                   "refId"=>"a.refId",
			                   "keyword"=>"a.keyword",
			                   "lead_status"=>"a.lead_status",
			                   "enteredby"=>"e1.empname",
			                   "convertedby"=>"e2.empname",
			                   "convertedon"=>"date(a.convertedon)",
			                   "source"=>"a.source");
			$app='';
			for($n=0;$n<sizeof($Ads['field_d']);$n++){
				if(strstr($Ads['field_d'][$n],'c_')){
					$field_array[$Ads['field_d'][$n]] = $Ads['field_d'][$n];
				}
			}
			for($i=0;$i<sizeof($Ads['field_d']);$i++){
				switch($Ads['equ'][$i]){
					case 1:
						$app=" like '%".$Ads['fval'][$i]."%' ";
					break;
					case 2:
						$app=" not like '%".$Ads['fval'][$i]."%' ";
					break;
					case 4:
						$app=" != '".$Ads['fval'][$i]."' ";
					 break;
					 case 5:
						$app=" > '".$Ads['fval'][$i]."' ";
					 break;
					 case 6:
						$app=" < '".$Ads['fval'][$i]."' ";
					 break;
					 case 7:
						$app=" >= '".$Ads['fval'][$i]."' ";
					break;
					case 8:
						 $app=" <= '".$Ads['fval'][$i]."' ";	
					break;	 
					case 3:
					default:
						$app=" = '".$Ads['fval'][$i]."' ";
					break;
				}
				$q .= (isset($field_array[$Ads['field_d'][$i]])) ? " ".$Ads['cond'][$i]."  ".$field_array[$Ads['field_d'][$i]]."  ".$app : '';
			}
		}
		if(isset($s)){
			$arr = array_keys($s);
			for ($n =0;$n<count($arr);$n++){
				if(strstr($arr[$n],'c_')){
					if(is_array($s[$arr[$n]])){
						$s[$arr[$n]] = @implode(',',$s[$arr[$n]]);
					}
					$q.=(isset($s[$arr[$n]]) && ($s[$arr[$n]]!='')  && ($s[$arr[$n]]!=' ') && ($s[$arr[$n]]!='0')) ? " AND a.".$arr[$n]." LIKE '%".$s[$arr[$n]]."%'":"";
				}
			}
		}
		$q.=(isset($s['gid']) && $s['gid']!='')?" and a.gid = '".$s['gid']."'":"";
		$q.=(isset($s['assignto']) && $s['assignto']!='')?" and a.assignto = '".$s['assignto']."'":"";
		$q.=(isset($s['enteredby']) && $s['enteredby']!='')?" and a.enteredby = '".$s['enteredby']."'":"";
		$q.=(isset($s['convertedby']) && $s['convertedby']!='')?" and a.convertedby = '".$s['convertedby']."'":"";
		$q.=(isset($s['name']) && $s['name']!='')?" and a.name like '%".$s['name']."%'":"";
		$q.=(isset($s['number']) && $s['number']!='')?" and a.number like '".$s['number']."%'":"";
		$q.=(isset($s['email']) && $s['email']!='')?" and a.email like '%".$s['email']."%'":"";
		$q.=(isset($s['caller_add']) && $s['caller_add']!='')?" and a.caller_add like '%".$s['caller_add']."%'":"";
		$q.=(isset($s['caller_bus']) && $s['caller_bus']!='')?" and a.caller_bus like '%".$s['caller_bus']."%'":"";
		$q.=(isset($s['keyword']) && $s['keyword']!='')?" and a.keyword like '%".$s['keyword']."%'":"";
		$q.=(isset($s['refId']) && $s['refId']!='')?" and a.refId like '%".$s['refId']."%'":"";
		$q.=(isset($s['lead_status']) && $s['lead_status']!='')?" and a.lead_status ='".$s['lead_status']."'":"";
		
		if(isset($s['createdon_from']) && $s['createdon_from']!='' && isset($s['createdon_to']) && $s['createdon_to']!=''){
			$q .= " and (date(a.createdon) BETWEEN '".$s['createdon_from']."' AND '".$s['createdon_to']."')";
		}else{
			$q.=(isset($s['createdon_from']) && $s['createdon_from']!='')?" and a.createdon >= '".$s['createdon_from']."'":"";
			$q.=(isset($s['createdon_to']) && $s['createdon_to']!='')?" and a.createdon <= '".$s['createdon_to']."'":"";
		}
		
		if(isset($s['lastmodified_from']) && $s['lastmodified_from']!='' && isset($s['lastmodified_to']) && $s['lastmodified_to']!=''){
			$q .= " and (date(a.lastmodified) BETWEEN '".$s['lastmodified_from']."' AND '".$s['lastmodified_to']."')";
		}else{
			$q.=(isset($s['lastmodified_from']) && $s['lastmodified_from']!='')?" and a.lastmodified >= '".$s['lastmodified_from']."'":"";
			$q.=(isset($s['lastmodified_to']) && $s['lastmodified_to']!='')?" and a.lastmodified <= '".$s['lastmodified_to']."'":"";
		}
		
		if(isset($s['convertedon_from']) && $s['convertedon_from']!='' && isset($s['convertedon_to']) && $s['convertedon_to']!=''){
			$q .= " and (date(a.convertedon) BETWEEN '".$s['convertedon_from']."' AND '".$s['convertedon_to']."')";
		}else{
			$q.=(isset($s['convertedon_from']) && $s['convertedon_from']!='')?" and a.convertedon >= '".$s['convertedon_from']."'":"";
			$q.=(isset($s['convertedon_to']) && $s['convertedon_to']!='')?" and a.convertedon <= '".$s['convertedon_to']."'":"";
		}
		$q.=(isset($s['source']) && $s['source']!='')?" and a.source like '%".$s['source']."%'":"";
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$ge = " ";		
		if($roleDetail['role']['admin']!=1){
			if($roleDetail['role']['roleid']==4){
				$q .= " AND ge.eid is not null ";
				$ge = " LEFT JOIN ".$bid."_leads_grpemp ge ON (d.gid=ge.gid AND ge.eid= '".$this->session->userdata('eid')."') ";
			}else{
				$q .= " AND (a.enteredby='".$this->session->userdata('eid')."' OR a.convertedby ='".$this->session->userdata('eid')."' OR d.eid='".$this->session->userdata('eid')."' OR a.assignto='".$this->session->userdata('eid')."')";
			}
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND a.pulse > '0'";
		}
		if($lead_status != 0 && $lead_status != 'all')
			$q .= " AND a.lead_status = '".$lead_status."'" ;
		$sortOrder = ($lead_status == 1 || $lead_status == 0 || $lead_status == 'all') ? " a.`leadid` DESC "  :  " a.`convertedon` DESC ";
		$sql = "SELECT SQL_CALC_FOUND_ROWS a.`leadid`
				FROM ".$bid."_leads a 
				LEFT JOIN ".$bid."_leads_groups d on a.gid=d.gid 
				LEFT JOIN ".$bid."_employee e on a.assignto=e.eid 
				LEFT JOIN ".$bid."_employee e1 on a.enteredby=e1.eid 
				LEFT JOIN ".$bid."_employee e2 on a.convertedby=e2.eid 
				$ge 
				WHERE a.status!='2' $q ORDER BY  $sortOrder LIMIT $ofset,$limit";   //a.`lastmodified` DESC,
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']==$modid){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields($modid,$bid);
		$keys = array();
		$header = array('#',"<a href='javascript://'><span id='c_all' class='glyphicon glyphicon-gok'></span></a>");
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing'] &&  !in_array($field['fieldname'],
						array('leadhistory'))){
				foreach($roleDetail['system'] as $f){
					if($f['fieldid']==$field['fieldid'])$checked = true;
				}
				if($checked){
					array_push($keys,$field['fieldname']);
					array_push($header,(($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
				}
			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldKey']);
					array_push($header,$field['customlabel']);
				}
			}
		}
	
		array_push($header,'Remark');
		array_push($header,'Comments');
		array_push($header,'Distribution Type');
		array_push($header,'Alert');
		array_push($header,'Counter');
		array_push($header,'Duplicate');
		
		if($opt_add || $opt_view || $opt_delete)
			array_push($header,$this->lang->line('level_Action'));
		$ret['header'] = $header;
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$data = array($i);
			$v = '<input type="checkbox" class="blk_check" name="blk[]" value="'.$rec['leadid'].'"/>';	
			array_push($data,$v);
			$r = $this->configmodel->getDetail($modid,$rec['leadid'],'',$bid);
			$callCnt = $this->callcount($rec['leadid']);
			$dis_type = array("0"=>"Uncategorized","1"=>"Manual","2"=>"Auto");
			$alert_type = array("0"=>" No ","1"=>" Email Alert ","2"=>" SMS Alert ","3"=>" Both ");
			foreach($keys as $k){
				if($k=='assignto'){
					$v = '<a href="Employee/activerecords/'.$r['assignto'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['assignempname'].'</a>';
				}elseif($k=='enteredby'){
					$v = '<a href="Employee/activerecords/'.$r['enteredby'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['enteredempname'].'</a>';
				}elseif($k=='convertedby'){
					$v = '<a href="Employee/activerecords/'.$r['convertedby'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['convertedemp'].'</a>';
				}elseif($k=='gid'){
					$v = '<a href="leads/leadgrp_active/'.$r['gid'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['groupname'].'</a>';
				}elseif($k == 'lead_status'){
					$v = $r['type'];
				}elseif($k == 'leadowner'){
					$v = $r['leadowner'];
				//~ }elseif($k == 'leadhistory'){
				    //~ $leadhistory = $this->leadhistorynames($r['leadid'],$bid);
				      //~ $v = $leadhistory;
			    }else{
						$v = isset($r[$k])?nl2br(wordwrap(stripslashes($r[$k]),80,"\n")):"";	
				}
				array_push($data,$v);
			}	
			$v = $this->getRemarkById($rec['leadid'],$bid,1);
			array_push($data,nl2br(wordwrap($v,80,"\n")));
			$v = $this->getCommentById($rec['leadid'],$bid,1);
			array_push($data,nl2br(wordwrap($v,80,"\n")));
			$v = (isset($r['dis_type']) && $r['dis_type'] != '') ? $dis_type[$r['dis_type']]: '';
			array_push($data,$v);
			$v = (isset($r['alert_type']) && $r['alert_type'] != '') ? $alert_type[$r['alert_type']]: '';
			array_push($data,$v);
			$v = (isset($callCnt) && $r['source'] == 'Calltrack') ? "<a href=\"Javascript:void(null)\" onClick=\"window.open('/leads/callHistory/".$rec['leadid']."', 'Counter', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=980,height=700,resizable=1')\">".$callCnt.'</a>': '';
			array_push($data,$v);
			$v = (isset($r['duplicate']) && $r['duplicate'] == 1) ? "<a href=\"Javascript:void(null)\" onClick=\"window.open('/leads/leadDupliHistory/".$rec['leadid']."', 'Show', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=980,height=700,resizable=1')\">Show</a>": '';
			array_push($data,$v);
				
			foreach ($data as $k=>$v){
				$v = ($r['duplicate'] == 1) ? "<font color='RED'>".$v."</font>" :  $v;
				$data[$k] = $v;
			}	
			if($opt_add || $opt_view || $opt_delete){
				$act = ($opt_add) ?'<a href="EditLead/'.$rec['leadid'].'/'.$lead_status.'"><span title="Edit" class="fa fa-edit"></span></a>':'';
				$act .= ($opt_delete) ? '<a href="'.base_url().'leads/delete/'.$rec['leadid'].'/'.$lead_status.'" class="deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
				$act .= '<a href="leads/active_lead/'.$rec['leadid'].'/'.$lead_status.'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="fa fa-list" title="Lead Details"></span></a>';
				$act .= '<a href="Report/followup/'.$rec['leadid'].'/0/leads" class="btn-followup" data-toggle="modal" data-target="#modal-followup"><img src="system/application/img/icons/comments.png" style="vertical-align:top;" title="Followups" width="16" height="16" /></a>';		
				$act .= ($r['email']!='')?"<a href=\"Javascript:void(null)\" onClick=\"window.open('/Email/compose/".$rec['leadid']."/leads', 'Sent Email', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=950,height=480,resizable=1')\">&nbsp;<span title='Send Mail' class='fa fa-envelope'></span></a>":'&nbsp;<span title="Send Mail" class="fa fa-envelope"></span>';
				$act .= anchor("Report/clicktoconnect/".$rec['leadid']."/4", '<span title="click To Connect" class="fa fa-phone"></span>',array('class'=>'clickToConnect'));
				$act .= anchor("Report/sendSms/".$rec['leadid']."/leads", '&nbsp;<span title="Click to send SMS" class="glyphicon glyphicon-comment"></span>','class="clickToSMS" data-toggle="modal" data-target="#modal-empl"');	
				$act .= "<a href=\"Javascript:void(null)\" onClick=\"window.open('leads/sendFields/".$rec['leadid']."/".$bid."/26/lead', 'Send Fields', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=550,height=500,left=200,top=20,resizable=1')\">&nbsp;<span title='Click to Send Fields' class='fa fa-list-alt'></span></a>";
				$act .= '&nbsp;<a href="leads/comments/'.$rec['leadid'].'/'.$lead_status.'" class="btn-followup" data-toggle="modal" data-target="#modal-followup"><span title="Lead Comments" class="fa fa-comment"></span></a>';
				$act .= '&nbsp;<a href="leads/remarks/'.$rec['leadid'].'/" class="btn-followup" data-toggle="modal" data-target="#modal-followup"><img src="system/application/img/icons/remarks.png" style="vertical-align:top;" title="Leads Remarks" width="16" height="16" /></a>';
				$act .= '&nbsp;<a href="leads/lead_history/'.$rec['leadid'].'/'.$bid.'" class="btn-leadhis" data-toggle="modal" data-target="#modal-leadhis"><span title="Lead History" class="glyphicon glyphicon-share"></span></a>';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
  
	function edit_lead($id,$bid,$type){
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$modid = '26' ;
		$itemDetail = $this->configmodel->getDetail($modid,$id,'',$bid);
		if($itemDetail['lead_status'] != $this->input->post('lead_status') && $this->input->post('lead_status') == 2 && $type == 1){
			if(isset($_POST['email']) && $_POST['email'] != '')
				$q = " OR email ='".$_POST['email']."'";
			else
				$q = '';
			$sql = $this->db->query("SELECT leadid FROM ".$bid."_leads WHERE (number='".$_POST['callfrom']."' ".$q.") AND lead_status = 2 AND source='Calltrack'");
			if($sql->num_rows() > 0)
				return 2;
		}
		$rate=0;
		$val='';
		$arr=array_keys($_POST);
		$assempid = 0;
		for($i=0;$i<sizeof($arr);$i++){
			if($arr[$i]!="update_system" && $arr[$i]!="custom" && $arr[$i]!="autoAssign" && $arr[$i]!="httpRefer" && $arr[$i] != 'assignto' && $arr[$i] != 'comments' && $arr[$i] != 'remark' && $arr[$i] != 'duplicate' && $arr[$i] != 'callfrom'){
					if(is_array($_POST[$arr[$i]]))
						$val = @implode(',',$_POST[$arr[$i]]);
					elseif($_POST[$arr[$i]]!="")
						$val=$_POST[$arr[$i]];
					else
						$val='';
					$this->db->set($arr[$i],$val);
			}
		}
		$gid = $this->input->post('gid');
		if(is_numeric($this->input->post('assignto'))){
			 $assempid = $this->input->post('assignto');
			 $dis_type = 1;
		 }elseif($roleDetail[$modid]['fieldname'] == 'assignto'){
			$rule = $this->db->query("SELECT group_rule as rule FROM ".$bid."_leads_groups WHERE gid='".$gid."'")->row()->rule;
			if($rule == '2'){
				$resultemp = $this->db->query("SELECT e.eid,COALESCE(((weight/(SELECT sum(weight) FROM ".$bid."_leads_grpemp WHERE gid=ge.gid))-(counter/(SELECT sum(counter) FROM ".$bid."_leads_grpemp WHERE gid=ge.gid))),0) as pc FROM ".$bid."_employee e LEFT JOIN ".$bid."_leads_grpemp ge on e.eid=ge.eid WHERE ge.gid='".$gid."' AND ge.status ='1' AND e.status = '1' ORDER BY pc DESC LIMIT 0,1")->result_array();
				if(count($resultemp) > 0)
					$assempid = $resultemp[0]['eid'];
			}elseif($rule == '1'){
				$eid = $this->db->query("SELECT ge.eid FROM ".$bid."_leads_grpemp ge LEFT JOIN ".$bid."_employee e ON ge.eid = e.eid WHERE ge.gid='".$this->input->post('gid')."' AND ge.bid='".$bid."' AND ge.status = 1 AND e.status = 1 ORDER BY ge.counter ASC LIMIT 0,1")->result_array();
				if(count($eid) > 0){
					$assempid = $eid[0]['eid'];
				}	
			}
			$dis_type = 2;
		}
		(isset($assempid) && $assempid != 0 ) ? $this->db->set('assignto',$assempid) : '';
		if($itemDetail['assignto'] != $assempid){
			$history = $itemDetail['assignto']."->".$assempid."/".date('Y-m-d H:i:s').',';
			$leadhistory = ($itemDetail['leadhistory'] == '') ? $history :$itemDetail['leadhistory'].$history;
			$this->db->set('leadhistory', $leadhistory);
		}
		if($type == 1 && $this->input->post('lead_status') == 2){
			$this->db->set('convertedby',$this->session->userdata('eid'));
			if(isset($_POST['source'])){
				$src = ($_POST['source'] != '') ? $this->input->post('source') : "Prospects" ;
				$this->db->set('source',$src);
			}
		}
		if($itemDetail['lead_status'] != $this->input->post('lead_status')){
			$this->db->set('convertedon',date("Y-m-d H:i:s"));
		}
		$this->db->set('lastmodified',date("Y-m-d H:i:s"));
		if($this->input->post('duplicate') == 1) 
			$this->db->set('duplicate',$this->input->post('duplicate'));
		$this->db->where('leadid',$id);
		$this->db->update($bid.'_leads');
		if(intval($this->input->post('number')) != 0 && $this->input->post('lead_status') != 1){
			$this->db->query("UPDATE ".$bid."_callhistory SET `leadid`=".$id." WHERE callfrom='".$this->input->post('number')."' AND leadid = '0'");
		}
		$leadnumber = $this->leadnumber($id);
		$this->auditlog->auditlog_info('Leads',$id." - ".$leadnumber. " Lead Details updated by ".$this->session->userdata('username'));
		if(trim($this->input->post('comments'))!= ''){
			$this->db->set('bid',$bid);
			$this->db->set('leadid',$id);
			$this->db->set('cdate',date("Y-m-d H:i:s"));
			$this->db->set('eid',$this->session->userdata('eid'));
			$this->db->set('comment',$this->input->post('comments'));
			$this->db->insert($bid.'_leads_comments'); 
		}
		if(trim($this->input->post('remark')) != ''){
			$this->db->set('bid',$bid);
			$this->db->set('leadid',$id);
			$this->db->set('cdate',date("Y-m-d H:i:s"));
			$this->db->set('eid',$this->session->userdata('eid'));
			$this->db->set('remark',$this->input->post('remark'));
			$this->db->insert($bid.'_leads_remarks');
		}
		$sql = $this->db->query("SELECT empemail,empnumber,empname FROM ".$bid."_employee WHERE eid='".$assempid."'")->result_array();
		if(isset($_POST['alert_type']) && ($this->input->post('alert_type') =='3' || $this->input->post('alert_type') =='1')){
			$message = "You have assigned to New Lead. The New Lead Details are <br/>";
			$message .= "<br/>Name:".$this->input->post('name');
			$message .= "<br/>Email:".$this->input->post('email');
			$message .= "<br/>Number:".$this->input->post('number');
			$message .= "<br/>Address:".$this->input->post('caller_add');
			$message .= "<br/>Business:".$this->input->post('caller_bus');
			$message .= "<br/>Remarks:".$this->input->post('remark');
			$message .= "<br/>Comments:".$this->input->post('comments');
			$message .= "<br/>Assigned By:".$this->empgetname($this->session->userdata('eid'));
			$body = $this->emailmodel->newEmailBody($message,$sql[0]['empname']);
			$to  = $sql[0]['empemail']; 
			$subject = ' Assigned Lead details ';
			$this->load->library('email');
			$this->email->from('noreply@mcube.com', 'MCube');
			$this->email->to($to);
			$this->email->subject($subject);
			$this->email->message($body);
			$this->email->send();
		}
		//SMS
		if(isset($_POST['alert_type']) && ($this->input->post('alert_type') =='3' || $this->input->post('alert_type') =='2')){
			$smsbal = $this->configmodel->smsBalance($bid);
			if($smsbal > 0){
				$message = "Your New assigned Lead Details are ";
				$message .= " no:".$this->input->post('number')." and Name:".$this->input->post('name').".Powered By Mcube";
				$api = "http://180.179.200.180/getservice.php?from=vmc.in";
				$sms = $api."&to=".substr($sql[0]['empnumber'],-10,10)."&text=".urlencode($message);
				$sms = file($sms);
				$this->configmodel->smsDeduct($bid,'1');
			}else{
				$return = '3';
			}
		}
		/*if($bid != 1 && $bid != 47 && $bid != 257 ){
			if(isset($_POST['custom'])){
				$modid = ($modid=='46') ? '26' : $modid;
				foreach($_POST['custom'] as $fid=>$val){
					if($val!=''){
						$this->db->query("DELETE FROM ".$bid."_customfieldsvalue where bid= '".$bid."' and modid= '".$modid."' and fieldid= '".$fid."' and dataid= '".$id."'");
						$sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
								 bid			= '".$bid."'
								,modid			= '".$modid."'
								,fieldid		= '".$fid."'
								,dataid			= '".$id."'
								,value			= '".(is_array($val)?implode(',',$val):$val)."'";
						$this->db->query($sql);
					}
					
				}
			}
			/* custom field action execution 
			
			if(isset($_POST['custom'])){
				$modid = ($modid=='46') ? '26' : $modid;
				$fids = array_keys($_POST['custom']);
				$sql = "SELECT * FROM ".$bid."_customfields WHERE fieldid in (".implode(',',$fids).") 
						AND fieldtype in ('checkbox','dropdown','radio')";
				$fields = $this->db->query($sql)->result_array();
				if(count($fields)>0){
					$itemDetailNew = $this->configmodel->getDetail($modid,$id,'',$bid);
					$fieldset = $this->configmodel->getFields($modid,$bid);
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
			}
		}
		/* custom field value changed action api needs to execute*/
		//if($bid == 1 || $bid == 47  || $bid == 257){
			$modid = ($modid == 26) ? 26 : 46 ;
			$sql = "SELECT * FROM ".$bid."_customfields WHERE modid='".$modid."' AND fieldtype in ('checkbox','dropdown','radio')";
			$rst = $this->db->query($sql)->result_array();
			foreach($rst as $rec){
				if(@in_array($rec['field_key'],$arr)){
					$itemDetailNew = $this->configmodel->getDetail($modid,$id,'',$bid);
					$fieldset = $this->configmodel->getFields($modid,$bid);
					//$fieldKey = $this->systemmodel->getFieldKey($field['fieldid'],$bid);
					$data = array();
					foreach($fieldset as $field){
						if($field['type']=='s' && !$field['is_hidden']){
							$data[$field['customlabel']]=$itemDetailNew[$field['fieldname']];
						}elseif($field['type']=='c' && $field['show']){
							//$fieldKey = $this->systemmodel->getFieldKey($field['fieldid'],$bid);
							$data[$field['customlabel']]=$itemDetailNew[$field['fieldKey']];
						}
					}
					$set = @explode("\n",$rec['options']);
					foreach($set as $s){
						$val = @explode("|",$s);
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
							$fp = fopen("apilog.txt","a");fwrite($fp,"\n".'['.date('Y-m-d H:i:s').'] bid:'.$bid.' '. $ret);fclose($fp);
						}
					}
				}
			}	
		//}
		return 1;
	}
	function delete_lead($id,$bid){
		$this->db->set('status', '2');
		$this->db->where('leadid',$id);
		$this->db->update($bid."_leads");
		$leademp_use = $this->configmodel->leadusageCheck($bid,'lead');
		if($leademp_use['type'] == 1)
			$this->db->query("UPDATE  business_lead_use SET `leadlimit`=(`leadlimit`+1) WHERE bid='".$bid."'");
		$leadnumber = $this->leadnumber($id);
		$this->auditlog->auditlog_info('Leads',$id." - ".$leadnumber. " Deleted By ".$this->session->userdata('username'));
		return 1;	
	}
	function deleted_list($ltype,$bid,$ofset='0',$limit='20'){
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$modid = ($ltype == 1) ? '46' : '26' ;
		$q= '';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
        //$custiom_ids=$this->configmodel->customSearch((isset($s['custom']))?$s['custom']:'',$modid,$bid);
		$q.=(isset($s['gid']) && $s['gid']!='')?" and a.gid = '".$s['gid']."'":"";
		$q.=(isset($s['assignto']) && $s['assignto']!='')?" and a.assignto = '".$s['assignto']."'":"";
		$q.=(isset($s['enteredby']) && $s['enteredby']!='')?" and a.enteredby = '".$s['enteredby']."'":"";
		$q.=(isset($s['name']) && $s['name']!='')?" and a.name like '%".$s['name']."%'":"";
		$q.=(isset($s['email']) && $s['email']!='')?" and a.email like '%".$s['email']."%'":"";
		$q.=(isset($s['number']) && $s['number']!='')?" and a.number like '".$s['number']."%'":"";
		$q.=(isset($s['caller_add']) && $s['caller_add']!='')?" and a.caller_add like '%".$s['caller_add']."%'":"";
		$q.=(isset($s['caller_bus']) && $s['caller_bus']!='')?" and a.caller_bus like '%".$s['caller_bus']."%'":"";
		$q.=(isset($s['remark']) && $s['remark']!='')?" and a.remark like '%".$s['remark']."%'":"";
		$q.=(isset($s['refId']) && $s['refId']!='')?" and a.refId like '%".$s['refId']."%'":"";
		//$q.=(isset($s['createdon']) && $s['createdon']!='')?" and date(a.createdon)>= '".$s['createdon']."'":"";
		//$q.=(isset($s['lastmodified']) && $s['lastmodified']!='')?" and date(a.lastmodified)<= '".$s['lastmodified']."'":"";
		$q.=(isset($s['source']) && $s['source']!='')?" and a.source like '%".$s['source']."%'":"";
		//$q.=(strlen($custiom_ids)>1)?" and a.leadid in(".$custiom_ids.")":"";
		//$q.=(!$custiom_ids)?" ":"";
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (a.assignto='".$this->session->userdata('eid')."' OR a.enteredby='".$this->session->userdata('eid')."'  or d.eid='".$this->session->userdata('eid')."')";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND a.pulse > '0'";
		}
		if($ltype == 1)
			$q .= " AND a.lead_status='".$ltype."' ";
		else
			$q .= " AND a.lead_status !='1' ";
		$sql = "SELECT a.leadid FROM ".$bid."_leads a 
				LEFT JOIN ".$bid."_leads_groups d on a.gid=d.gid 
				WHERE a.status='2' $q ORDER BY a.leadid DESC  LIMIT $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']== $modid){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields($modid,$bid);
		$keys = array();
		$header = array('#');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
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
					/* New Structure Code for custom fields*/
				//	if($bid == 1 || $bid == 47  || $bid == 257){
						//$fieldKey = $this->systemmodel->getFieldKey($field['fieldid'],$bid);
						array_push($keys,$field['fieldKey']);
					/*}else{
						array_push($keys,'custom['.$field['fieldid'].']');
					}/*
					/* ENd */
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
			$r = $this->configmodel->getDetail($modid,$rec['leadid'],'',$bid);
			
			foreach($keys as $k){
				if($k=='assignto'){
					$v = '<a href="Employee/activerecords/'.$r['assignto'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['assignempname'].'</a>';
				}elseif($k=='enteredby'){
					$v = '<a href="Employee/activerecords/'.$r['enteredby'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['enteredempname'].'</a>';
				}elseif($k=='convertedby'){
					$v = '<a href="Employee/activerecords/'.$r['convertedby'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['convertedemp'].'</a>';
				}elseif($k=='gid'){
					$v = '<a href="leads/leadgrp_active'.$r['gid'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['groupname'].'</a>';
				}elseif($k=='lead_status'){
					$v = $r['type'];
				}else{
						$v = isset($r[$k])?nl2br(wordwrap($r[$k],80,"\n")):"";	
				}
				array_push($data,$v);
			}
			if($opt_add || $opt_view || $opt_delete){
				$act = '<a href="'.base_url().'leads/undelete/'.$r['leadid'].'/'.$ltype.'"><img src="system/application/img/icons/undelete.png" style="vertical-align:top;" title="Restore" /></a>';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function undelete_lead($leadid,$bid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$this->db->set('status','1');
		$this->db->where('leadid',$leadid);
		$this->db->update($bid."_leads");
		$leademp_use = $this->configmodel->leadusageCheck($bid,'lead');
		$leadnumber = $this->leadnumber($leadid);
		$this->auditlog->auditlog_info('Leads',$leadid." - ".$leadnumber. " Restored By ".$this->session->userdata('username'));
		if($leademp_use['type'] == 1)
			$this->db->query("UPDATE  business_lead_use SET `leadlimit`=(`leadlimit`-1) WHERE bid='".$bid."'");
		return true;
	}
	function addlead($type){
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$modid = ($type == 1) ? '46' : '26' ;
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$arr = array_keys($_POST);
		$leads_use = $this->configmodel->leadusageCheck($bid,'lead');
		$assempid = 0;
		if($leads_use['type'] == 1 && $leads_use['used'] == 0 && $id == ''){
			return '2';
		}else{
			$proces = FALSE;
			if(isset($_POST['email']) && $_POST['email'] != '')
				$q = " OR email ='".$_POST['email']."'";
			else
				$q = '';
			$sql = $this->db->query("SELECT leadid FROM ".$bid."_leads l WHERE number='".$this->input->post('number')."'".$q);
			if($sql->num_rows() > 0){
				if($this->input->post('duplicate') == 1){
					$proces = "TRUE";
				}else{
					return 0;
				}
			}else{
				$proces = "TRUE";
			}
			if($proces == "TRUE"){
				$dis_type = 0;
				if($this->input->post('assignto') != '' && $this->input->post('assignto') !=0){
					$assempid = $this->input->post('assignto');
					$dis_type = 1;
				}else{
					if($this->input->post('gid') != ''){
						$rule = $this->db->query("SELECT group_rule as rule FROM ".$bid."_leads_groups WHERE gid='".$this->input->post('gid')."'")->row()->rule;
						if($rule == '2'){
							$resultemp = $this->db->query("SELECT e.eid,COALESCE(((weight/(SELECT sum(weight) FROM ".$bid."_leads_grpemp WHERE gid=ge.gid))-(counter/(SELECT sum(counter) FROM ".$bid."_leads_grpemp WHERE gid=ge.gid))),0) as pc FROM ".$bid."_employee e LEFT JOIN ".$bid."_leads_grpemp ge on e.eid=ge.eid WHERE ge.gid='".$this->input->post('gid')."' AND ge.status = '1' AND e.status = '1' ORDER BY pc DESC LIMIT 0,1")->result_array();
							if(count($resultemp) > 0){
								$assempid = $resultemp[0]['eid'];
							} 
						}elseif($rule == '1'){
							$eid = $this->db->query("SELECT ge.eid FROM ".$bid."_leads_grpemp ge LEFT JOIN ".$bid."_employee e ON ge.eid = e.eid WHERE ge.gid='".$this->input->post('gid')."' AND ge.bid='".$bid."' AND ge.status = '1'  AND e.status = '1' ORDER BY ge.counter ASC LIMIT 0,1")->result_array();
							if(count($eid) > 0){
								$assempid = $eid[0]['eid'];
							}
						}
						$dis_type = 2;
					}
				}
				$leadid = $this->db->query("SELECT COALESCE(MAX(`leadid`),0)+1 as id FROM ".$bid."_leads")->row()->id;
				$this->db->set('leadid',$leadid);
				for($i=0;$i<sizeof($arr);$i++){
					if($arr[$i]!="custom" && $arr[$i]!="autoAssign" && $arr[$i]!="duplicate" && $arr[$i]!="remark" && $arr[$i]!="update_system"){
						/* Changed for custom fields */
							if(is_array($_POST[$arr[$i]]))
								$val = @implode(',',$_POST[$arr[$i]]);
							elseif($_POST[$arr[$i]]!="")
								$val=$_POST[$arr[$i]];
							else
								$val='';
							$this->db->set($arr[$i],$val);
						/* Changed for custom fields end */
					}
				}
			
				$this->db->set('bid',$bid);
				$this->db->set('assignto',$assempid);
				$this->db->set('leadowner',$this->session->userdata('eid'));
				$this->db->set('enteredby',$this->session->userdata('eid'));
				$this->db->set('convertedby',$this->session->userdata('eid'));
				$this->db->set('lastmodified',date("Y-m-d H:i:s"));
				$this->db->set('convertedon',date("Y-m-d H:i:s"));
				$this->db->set('status',1);
				$this->db->set('dis_type',$dis_type);
				$this->db->set('duplicate',$this->input->post('duplicate'));
				$this->db->set('parentId',$this->input->post('parentId'));
				$type = ($type == 0) ? '2' : $type;
				$this->db->set('lead_status',$type);
				$this->db->insert($bid."_leads");
				if(intval($this->input->post('number')) != 0 && $type != 1){
					$this->db->query("UPDATE ".$bid."_callhistory SET `leadid`=".$leadid." WHERE callfrom='".$this->input->post('number')."' AND leadid = '0'");
				}
				if($leads_use['type'] == 1)
					$this->db->query("UPDATE business_lead_use SET `leadlimit`=(`leadlimit`-1) WHERE bid='".$bid."'");
				if($dis_type == 2)
					$this->db->query("UPDATE ".$bid."_leads_grpemp SET `counter`=(`counter`+1) WHERE eid='".$assempid."' AND gid='".$this->input->post('gid')."'");
				if($this->input->post('remark') != ''){
					$this->db->set('bid',$bid);
					$this->db->set('leadid',$leadid);
					$this->db->set('cdate',date("Y-m-d H:i:s"));
					$this->db->set('eid',$this->session->userdata('eid'));
					$this->db->set('remark',$this->input->post('remark'));
					$this->db->insert($bid.'_leads_remarks');
				}
				$this->auditlog->auditlog_info('Leads',$leadid." - ".$this->input->post('number'). " New Lead added to List ");
				$sql = $this->db->query("SELECT empemail,empnumber,empname FROM ".$bid."_employee WHERE eid='".$assempid."'")->result_array();
				if(isset($_POST['alert_type']) && ($this->input->post('alert_type') =='3' || $this->input->post('alert_type') =='1')){
					$message = "You have assigned to New Lead. The New Lead Details are <br/>";
					$message .= "<br/>Name:".$this->input->post('name');
					$message .= "<br/>Email:".$this->input->post('email');
					$message .= "<br/>Number:".$this->input->post('number');
					$message .= "<br/>Address:".$this->input->post('caller_add');
					$message .= "<br/>Business:".$this->input->post('caller_bus');
					$message .= "<br/>Remarks:".$this->input->post('remark');
					$message .= "<br/>Comments:".$this->input->post('comments');
					$message .= "<br/>Assigned By:".$this->empgetname($this->session->userdata('eid'));
					$body = $this->emailmodel->newEmailBody($message,$sql[0]['empname']);
					$to  = $sql[0]['empemail']; 
					$subject = ' Assigned Lead details ';
					$this->load->library('email');
					$this->email->from('noreply@mcube.com', 'MCube');
					$this->email->to($to);
					$this->email->subject($subject);
					$this->email->message($body);
					$this->email->send();
				}
				//SMS
				if(isset($_POST['alert_type']) && ($this->input->post('alert_type') =='3' || $this->input->post('alert_type') =='2')){
					$smsbal = $this->configmodel->smsBalance($bid);
					if($smsbal > 0){
						$message = "Your New assigned Lead Details are ";
						$message .= " no:".$this->input->post('number')." and Name:".$this->input->post('name').".Powered By Mcube";
						$api = "http://180.179.200.180/getservice.php?from=vmc.in";
						$sms = $api."&to=".substr($sql[0]['empnumber'],-10,10)."&text=".urlencode($message);
						$sms = file($sms);
						$this->configmodel->smsDeduct($bid,'1');
					}else{
						$return = '3';
					}
				}
				return '1';
			}
		}
	}
	function leads_csv($type,$bid){
		$modid = '26' ;
		$res=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q= " ";
		if($_POST['endtimes']!=""){
			$q.=" AND date(a.createdon)<='".$_POST['endtimes']."'" ;
		}
		if($_POST['starttimes']!=""){
			$q.=" and date(a.createdon)>='".$_POST['starttimes']."'";
		}
		if(!empty($_POST['groupname'])){
			if($_POST['groupname'][0]!=""){
				$q.=" and a.gid in (".implode(",",$_POST['groupname']).")";
			}
		}
		if(!empty($_POST['empname'])){
			if($_POST['empname'][0]!=""){
			$q.=" and a.assignto in(".implode(",",$_POST['empname']).")";
			}
		}
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (a.enteredby='".$this->session->userdata('eid')."'  OR a.assignto='".$this->session->userdata('eid')."' or d.eid='".$this->session->userdata('eid')."')";
		}
		if($type != '' && $type != 0){
			$q .= " AND lead_status='".$type."'";
		}
		$limit = $roleDetail['role']['recordlimit'];
		$csv_output = "";
		foreach($_POST['lisiting'] as $key=>$fiels){
		//	if($bid == 1 || $bid == 47  || $bid == 257){
				$hkey[]=$key;
				$header[]=$fiels;
			/*}else{
				if($key=='custom'){
					foreach($fiels as $key=>$fiels){
						$header[]=$fiels;
						$hkey[]='custom['.$key.']';
					}
				}else{
					$hkey[]=$key;
					$header[]=$fiels;
				}
			}*/
		}
		$csv_output .= @implode(",",$header)."\n";
		$sql="SELECT SQL_CALC_FOUND_ROWS a.leadid
				FROM ".$bid."_leads a
				LEFT JOIN ".$bid."_leads_groups d ON a.gid=d.gid 
				WHERE a.status!='2'  $q
				ORDER BY a.leadid DESC
				LIMIT 0,$limit"; 
		$rst = $this->db->query($sql)->result_array();
		$name = $bid.'_'.$this->session->userdata('eid').'_'.time();
		mkdir('reports/'.$name);
		chmod('reports/'.$name,0777);
		$data_file = 'reports/'.$name.'/leads.csv';
		$fp = fopen($data_file,'w');
		fwrite($fp,$csv_output);
		$files = array();
		//~ if($bid == 47 && $type == 1 ){
			//~ foreach($rst as $rec){
				//~ $data = array();
				//~ $sql1 = "SELECT * FROM gg_prospect WHERE leadid='".$rec['leadid']."'";
				//~ $r = $this->db->query($sql1)->row_array();
				//~ $i=0;
				//~ foreach($header as $k){
					//~ $k = trim($k);
					//~ $k = ($k == 'comments') ? 'Comments' : $k ;
					//~ $k = ($k == 'remarks') ? 'Remarks' : $k ;
					//~ $v=(isset($r[$k])) ? '"'.str_replace("\n"," ",$r[$k]).'"' : '';
					//~ array_push($data,$v);
				//~ }
				//~ $csv_output =implode(",",$data)."\n";
				//~ fwrite($fp,$csv_output);
			//~ }
		//~ }else{
			//~ /* other than GG */
			foreach($rst as $rec){
				$data = array();
				$r = $this->configmodel->getDetail($modid,$rec['leadid'],'',$bid);

				$i=0;
				foreach($hkey as $k){
					if(@in_array($k,array('gid','assignto','enteredby'))){
						$v=($k=='gid')?$r['groupname']:((($k=='enteredby')) ? $r['enteredempname'] : $r['assignempname']);
					}elseif(@in_array($k,array('lead_status'))){
						$v=$r['type'];
					}elseif(@in_array($k,array('convertedby'))){
						$v=$r['convertedemp'];
					}elseif($k == "comments"){
						$v = $this->getLeadComments($rec['leadid'],$bid);
					}elseif($k == "remarks"){
						$v = $this->getLeadRemarks($rec['leadid'],$bid);
					}else{
						$v=(isset($r[$k])) ? '"'.str_replace("\n"," ",$r[$k]).'"' : '';
					}
					array_push($data,$v);
				}
				$csv_output =implode(",",$data)."\n";
				fwrite($fp,$csv_output);
			}
		//}
		fclose($fp);
		chdir('reports');
		exec('zip -r '.$name.'.zip '.$name);
		exec('rm -rf '.$name);
		return $name;
	}
	function getLeadSystemfields($type,$bid){
		//$modid = ($type == 1) ? '46' : '26' ;
		$modid = '26' ;
		$result = array();
		//~ $sql = "SELECT fieldid,fieldname FROM systemfields WHERE modid='26'";
		//~ $rst = $this->db->query($sql)->result_array();
		$fieldset = $this->configmodel->getFields($modid,$bid);
		foreach($fieldset as $r){
			if(!@in_array($r['fieldname'],array('bid','leadid','gid','enteredby','createdon','lastmodified','status','lead_status','dis_type','alert_type','refId','convertedby','duplicate','parentId','remark','convertedon','leadowner')) && $r['show'] == 1 && $r['type'] == 's')
				$result[$r['fieldid']] = $this->lang->line('mod_'.$modid)->$r['fieldname'];
		}
		return $result;
	}
	function Systemfields($type){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		//if($bid == 1 || $bid == 47  || $bid == 257){
			$sql = "DESC ".$bid."_leads";
		/*}else{
			$sql = "SELECT fieldid,fieldname FROM systemfields WHERE modid='26'";
		}*/
		$rst = $this->db->query($sql)->result_array();
		foreach($rst as $r){
			//if($bid == 1 || $bid == 47  || $bid == 257){
				$result[] = $r['Field'];
			/*}else{
				$result[$r['fieldid']] = $r['fieldname'];
			}*/
		}
		return $result;
	}
	function getLeadCustomFields($type){
		$modid = '26' ;
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql = "SELECT fieldname,fieldid FROM ".$bid."_customfields WHERE modid='".$modid."' AND bid='".$bid."'";
		$rst = $this->db->query($sql)->result_array();
		if(!empty($rst)){
			foreach($rst as $r){
				$result[$r['fieldid']] = $r['fieldname'];
			}
		}else{
			$result = array();
		}
		return $result;
	}
	function addimportLeads($data,$custdata){
			
	     $bid = $data['bid'];
		 $type = $data['lead_status'];
		//$modid = ($type == 1) ? '46' : '26' ;
		$modid = '26';
		$dup = 0;$parent = 0;
		$ext = 0;
		$vnum = $vemail = 0;
		$leads_use = $this->configmodel->leadusageCheck($bid,'lead');
		if($leads_use['type'] == 1 && $leads_use['used'] == 0){
			return '2';
		}else{
			if(!(@array_key_exists("number",$data)) && !(@array_key_exists("email",$data))){
				return 0;
			}
			if(isset($data['number']) && preg_match("/^[0-9]+$/",$data['number']) && intval($data['number']) != 0){
			 $leadid = $this->db->query("SELECT leadid,gid FROM ".$bid."_leads WHERE number='".$data['number']."'");
				if($leadid->num_rows() > 0){
					if(isset($_POST['duplicate']) || $data['gid']==$leadid->row()->gid){
						return 0;
					}
					$parent = $leadid->row()->leadid;
					$dup = 1;
				}
				$ext = 1;
				$vnum = 1;
			}else{
				$data['number']='';
			}
			if(isset($data['email']) && preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $data['email'])){
				$leadid = $this->db->query("SELECT leadid,gid FROM ".$bid."_leads WHERE email='".$data['email']."'");
				if($leadid->num_rows() > 0){
					if(isset($_POST['duplicate']) || $data['gid']==$leadid->row()->gid){
						return 0;
					}
					$parent = $leadid->row()->leadid;
					$dup = 1;
				}
				$ext = 1;
				$vemail = 1;
			}else{
				$data['email']='';
			}

			if($vnum =='0' && $vemail=='0'){
				return 0;
			}else{
				$leadid = $this->db->query("SELECT COALESCE(MAX(`leadid`),0)+1 as id FROM ".$bid."_leads")->row()->id;
				$fields = $this->Systemfields($type);
				//array_push($fields,"bid");array_push($fields,"dis_type");
		     
				foreach($data as $k=>$v){
					if(@in_array($k,$fields)){
						if($k != '' || $v != '' ){
							($k == 'duplicate' ) ? ' ' : $this->db->set($k,mysql_real_escape_string($v));
							
						}
					}
				}
				$this->db->set('duplicate',$dup);
				$this->db->set('parentId',$parent);
				$this->db->set('leadid',$leadid);
				$this->db->insert($bid."_leads");
				/* New code for resolving the performance issue(temp solution) */
				//$this->db->query("REPLACE INTO ".$bid."_lead_child SET leadid='".$leadid."',bid = '".$bid."';");
				/* End */
				if(intval($data['number']) != 0 && $type != 1){
					$this->db->query("UPDATE ".$bid."_callhistory SET `leadid`=".$leadid." WHERE callfrom='".$data['number']."' AND leadid = '0'");
				}
				if($leads_use['type'] == 1)
					$this->db->query("UPDATE business_lead_use SET `leadlimit`=(`leadlimit`-1) WHERE bid='".$bid."'");
				if(!in_array($bid,array('1','47','257'))){
					foreach($custdata as $k=>$v){
						if($v!=''){
							$sql = "INSERT INTO ".$bid."_customfieldsvalue SET
									 bid                  = '".$bid."'
									,modid                = '".$modid."'
									,fieldid              = '".$k."'
									,dataid               = '".$leadid."'
									,value                = '".mysql_real_escape_string($v)."'";
							$this->db->query($sql);
						}
					}
				}
				if(isset($data['remarks'])){
					if($data['remarks'] != ''){
						$sql = "INSERT INTO ".$bid."_leads_remarks SET
								leadid		='".$leadid."',
								bid			='".$bid."',
								eid			='".$this->session->userdata('eid')."',
								remark		='".mysql_real_escape_string($data['remarks'])."'";
						$this->db->query($sql);
					}
				}
				if(isset($data['comments'])){
					if($data['comments'] != ''){
						$sql = "INSERT INTO ".$bid."_leads_comments SET
								leadid		='".$leadid."',
								bid			='".$bid."',
								eid			='".$this->session->userdata('eid')."',
								comment		='".mysql_real_escape_string($data['comments'])."'";
						$this->db->query($sql);
					}
				}
			}
			return 1;
		}
	}
				
	function empAssign($gid){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$result = array();
		$rst = $this->db->query("SELECT eid FROM ".$bid."_leads_grpemp WHERE gid='".$gid."' AND status = '1'")->result_array();
		if(count($rst) > 0){
			foreach($rst as $r){
				$result[] = $r['eid'];
			}
		}
		return $result;
	}
	function addFollowup($post){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$id=$this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM ".$bid."_followup")->row()->id;
		$sql = "INSERT INTO ".$bid."_followup SET
				id			=	'".$id."',
				bid			=	'".$bid."',
				eid			=	'".$this->session->userdata('eid')."',
				callid		=	'".$_POST['leadid']."',
				comment		=	'".$_POST['comment']."',
				alert		=	'".$_POST['alert']."',
				reach_time	=	'".$_POST['notify_time']."',
				type	=	'leads',
				followupdate='".$_POST['followupdate']."'";
		$this->db->query($sql);
		/* New code for resolving the performance issue(temp solution) */
			//$this->db->query("REPLACE INTO ".$bid."_lead_child SET leadid='".$_POST['leadid']."',bid = '".$bid."';");
		/* End */
		if(isset($_POST['custom'])){
			$modid = ($modid=='46') ? '26' : $modid;
			$arrs=array_keys($_POST['custom']);
			for($k=0;$k<sizeof($arrs);$k++){
				if(is_array($_POST['custom'][$arrs[$k]])){
					$x=implode(",",$_POST['custom'][$arrs[$k]]);
				}else{
					$x=$_POST['custom'][$arrs[$k]];
				}
				if($x!=''){
					//$this->db->query("DELETE FROM ".$bid."_customfieldsvalue WHERE bid= '".$bid."' AND modid= '29' AND fieldid = '".$arrs[$k]."'");
					$sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
						 bid			= '".$bid."'
						,modid			= '29'
						,fieldid		= '".$arrs[$k]."'
						,dataid			= '".$id."'
						,value			= '".mysql_real_escape_string($x)."'";
					$this->db->query($sql);
				}
			}
			$this->auditlog->auditlog_info('Leads',$id." ".$this->session->userdata('username'). " Added followup to lead ");
			return '1';
			}
		return ;
	}
	
	function getFollowuplist($id,$bid,$dsh=''){
		$q = '';
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		//$custiom_ids=$this->configmodel->customSearch((isset($s['custom']))?$s['custom']:'','29',$bid);
		//$q.=(strlen($custiom_ids)>1)?" and a.leadid in(".$custiom_ids.")":"";
		//$q.=(!$custiom_ids)?"":"";
		$where = ($dsh != '' && $dsh == 1) ? " followupdate >= CURRENT_DATE() AND " : " ";
		$sql="SELECT * FROM ".$bid."_followup WHERE ".$where." callid='".$id."' $q  ORDER BY cdate ASC";					 
		$rst = $this->db->query($sql)->result_array();
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='29'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('29',$bid);
		$keys = array();
		$header = array('#');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){
					if($f['fieldid']==$field['fieldid'])$checked = true;
				}
				if($checked){
					if($field['fieldname'] != 'eid'){
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
		array_push($keys,"reach_time");
		array_push($header,"Notification Time Limit");
		$ret['header'] = $header;
		$list = array();
		$i = 1;
		foreach($rst as $rec){
			$data = array($i);
			$r = $this->configmodel->getDetail('29',$rec['id'],'',$bid);
			foreach($keys as $k){
				$v = isset($r[$k])?nl2br(wordwrap($r[$k],80,"\n")):"";	
				array_push($data,$v);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function empAssign_exists($empemail){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$empassignId = $this->db->query("SELECT e.eid FROM ".$bid."_employee e WHERE e.empemail='".$empemail."' AND e.status='1'")->result_array();
		return (@array_key_exists('eid',$empassignId[0])) ? $empassignId[0]['eid'] : NULL;
	}
	
	function empAssigngr_exists($eid){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$empgroupId = $this->db->query("SELECT ge.gid FROM ".$bid."_leads_grpemp ge LEFT JOIN ".$bid."_groups g ON ge.gid = g.gid WHERE ge.eid ='".$eid."' AND g.status='1' AND ge.status = '1' LIMIT 0,1")->result_array();
		$grID = (@array_key_exists('gid',$empgroupId[0])) ? $empgroupId[0]['gid'] : NULL;
		if($grID == NULL){
			$empgroupId = $this->db->query("SELECT g.gid FROM ".$bid."_leads_groups g WHERE g.eid ='".$eid."' AND g.status='1'")->result_array();
			$grID = (@array_key_exists('gid',$empgroupId[0])) ? $empgroupId[0]['gid'] : NULL;
		}
		return $grID;
	}

	function blk_del($arr){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$sql="UPDATE ".$bid."_leads SET status=2 WHERE leadid IN(".$arr.")";
		$this->db->query($sql);
		$leadarray = @explode(',',$arr);
		$leadcnt = count($leadarray);
		$leademp_use = $this->configmodel->leadusageCheck($bid,'lead');
		if($leademp_use['type'] == 1){
			$this->db->query("UPDATE  business_lead_use SET `leadlimit`=(`leadlimit`+".$leadcnt.") WHERE bid='".$bid."'");
		}
		for($i=0;$i<$leadcnt;$i++){
			$leadnumber = $this->leadnumber($leadarray[$i]);
			$this->auditlog->auditlog_info('Leads',$leadarray[$i]." - ".$leadnumber. " Deleted By ".$this->session->userdata('username'));
		}
		return 1;	
	}
	function blk_delGrp($arr){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$sql="UPDATE ".$bid."_leads_groups SET status=2 WHERE gid IN(".$arr.")";
		$this->db->query($sql);
		for($i=0;$i<$leadcnt;$i++){
			$leadnumber = $this->leadnumber($leadarray[$i]);
			$this->auditlog->auditlog_info('Leads',$leadarray[$i]." - ".$leadnumber. " Deleted By ".$this->session->userdata('username'));
		}
		return 1;	
	}
	function blk_assign(){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$leadIds = @explode(',',$this->input->post('ids'));
		$modid = '26' ;
		$assempid = '0';
		if($this->input->post('gid') != '' || $this->input->post('assignto') != ''){
			for($id=0;$id<count($leadIds);$id++){
				$itemDetail = $this->configmodel->getDetail($modid,$leadIds[$id],'',$bid);
				$leadAssign = $this->db->query("SELECT assignto,gid FROM ".$bid."_leads WHERE leadid='".$leadIds[$id]."'")->result_array();
				if($this->input->post('assignto') != '' && $this->input->post('assignto') !=0){
					$assempid = $this->input->post('assignto');
				}else{
					if($this->input->post('gid') != ''){
						$rule = $this->db->query("SELECT group_rule as rule FROM ".$bid."_leads_groups WHERE gid='".$this->input->post('gid')."'")->row()->rule;
						if($rule == '2'){
							$resultemp = $this->db->query("SELECT e.eid,COALESCE(((weight/(SELECT sum(weight) FROM ".$bid."_leads_grpemp WHERE gid=ge.gid))-(counter/(SELECT sum(counter) FROM ".$bid."_leads_grpemp WHERE gid=ge.gid))),0) as pc FROM ".$bid."_employee e LEFT JOIN ".$bid."_leads_grpemp ge ON e.eid=ge.eid WHERE ge.gid='".$this->input->post('gid')."' AND ge.status = '1' AND e.status = '1' ORDER BY pc DESC LIMIT 0,1")->result_array();
							if(count($resultemp) > 0){
								$assempid = $resultemp[0]['eid'];
							} 
						}elseif($rule == '1'){
							$eid = $this->db->query("SELECT ge.eid FROM ".$bid."_leads_grpemp ge LEFT JOIN ".$bid."_employee e ON ge.eid = e.eid WHERE ge.gid='".$this->input->post('gid')."' AND ge.bid='".$bid."' AND ge.status = '1' AND e.status ='1' ORDER BY counter ASC LIMIT 0,1")->result_array();
							if(count($eid) > 0){
								$assempid = $eid[0]['eid'];
							}
						}
					}
				}
				if(isset($_POST['gid']))
				$this->db->set('gid',$this->input->post('gid'));
				$this->db->set('assignto',$assempid);
				if($itemDetail['assignto'] != $assempid){
				$history = $itemDetail['assignto']."->".$assempid."/".date('Y-m-d H:i:s').',';
				$leadhistory = ($itemDetail['leadhistory'] == '') ? $history :$itemDetail['leadhistory'].$history;
				$this->db->set('leadhistory', $leadhistory);
			    }
				$this->db->where('leadid',$leadIds[$id]);
				$this->db->update($bid."_leads");
				/* New code for resolving the performance issue(temp solution) */
				//$this->db->query("REPLACE INTO ".$bid."_lead_child SET leadid='".$leadIds[$id]."',bid = '".$bid."';");
				/* End */
				$this->db->query("UPDATE ".$bid."_leads_grpemp SET `counter`=(`counter`+1) WHERE eid='".$assempid."' AND gid='".$this->input->post('gid')."'");
			}
			return 1;
		}else{
			return "Please Select Group and Employee";
		}
	}
	
	function blk_Leadownerassign(){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$leadIds = @explode(',',$this->input->post('ids'));
		//echo "<pre>"; print_r($leadIds);exit;
		$assempid = '0';
		if($this->input->post('eid') != ''){
			for($id=0;$id<count($leadIds);$id++){
				$this->db->set('leadowner',$this->input->post('eid'));
		        $this->db->where('leadid',$leadIds[$id]);
				$this->db->update($bid."_leads");
			
		}return 1;
		}
		else{
			return "Please Select Employee";
		}
	}

	
	function getGroups(){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$res=array();	
		$sql=$this->db->query("SELECT * FROM ".$bid."_leads_groups WHERE status=1");
		$res['']=$this->lang->line('level_select');
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $r)	{
				$res[$r['gid']]=$r['groupname'];
			}
		}
		return $res;
	}
	function getEmployee(){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$res=array();	
		$sql=$this->db->query("SELECT * FROM ".$bid."_employee WHERE status=1");
		$res['']=$this->lang->line('level_select');
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $r)	{
				$res[$r['eid']]=$r['empname'];
			}
		}
		return $res;
	}
	function addleadsGroup(){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$id = $_POST['id'];
		$arr=array_keys($_POST);
		$leadgr_use = $this->configmodel->leadusageCheck($bid,'group');
		if($leadgr_use['type'] == 1 && $leadgr_use['used'] == 0 && $id == ''){
			return 'FALSE';
		}else{
			$sql=$this->db->query("SELECT gid FROM ".$bid."_leads_groups WHERE groupname='".$_POST['groupname']."' AND gid !='".$_POST['id']."'");
			if($sql->num_rows()==0){
						for($i=0;$i<sizeof($arr);$i++){
				   if(!in_array($arr[$i],array("update_system","bid","id","grule"))){
						/* Changed for custom fields */
					//	if($bid == 1 || $bid == 47  || $bid == 257){
							if(is_array($_POST[$arr[$i]]))
								echo $val = @implode(',',$_POST[$arr[$i]]);
							elseif($_POST[$arr[$i]]!="")
								echo $val=$_POST[$arr[$i]];
							else
								$val='';
							$this->db->set($arr[$i],$val);
						//}
					}
				}
				$this->db->set('groupname',$_POST['groupname']);
				$this->db->set('group_rule',$_POST['grule']);
				$this->db->set('group_desc',$_POST['group_desc']);
				$this->db->set('eid',$_POST['eid']);
				if($id  ==''){
					$id=$this->db->query("SELECT COALESCE(MAX(`gid`),0)+1 as id FROM ".$bid."_leads_groups")->row()->id;
					for($i=0;$i<sizeof($arr);$i++){
				   if(!in_array($arr[$i],array("update_system","grule","id"))){
						/* Changed for custom fields */
					//	if($bid == 1 || $bid == 47  || $bid == 257){
							if(is_array($_POST[$arr[$i]]))
								echo $val = @implode(',',$_POST[$arr[$i]]);
							elseif($_POST[$arr[$i]]!="")
								echo $val=$_POST[$arr[$i]];
							else
								$val='';
							$this->db->set($arr[$i],$val);
					//	}
					}
				}
					$this->db->set('status','1');
					$this->db->set('gid',$id);
					$this->db->insert($bid."_leads_groups");
					if($leadgr_use['type'] == 1)
						$this->db->query("UPDATE business_lead_use SET `grplimit`=(`grplimit`-1) WHERE bid='".$bid."'");
					$return = $id;
				}else{
					$this->db->where('gid',$id);
					$this->db->update($bid."_leads_groups");
					$return = $id;
				}
				
				$this->auditlog->auditlog_info('Leads groups',$id." - ".$this->input->post('name'). " New Lead Group Added");
				
			}else{
				$return = '';
			}
			return $return;
		}
	}
	function list_leads_grps($bid,$ofset,$limit,$type=''){
		$q= '';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		
		$q .= ($type == 'del') ? " a.status ='2' " : " a.status ='1' ";
        //$custiom_ids=$this->configmodel->customSearch((isset($s['custom']))?$s['custom']:'','37',$bid);
		$q.=(isset($s['eid']) && $s['eid']!='0')?" AND a.eid = '".$s['eid']."'":"";
		$q.=(isset($s['groupname']) && $s['groupname']!='')?" and a.groupname LIKE '%".$s['groupname']."%'":"";
		$q.=(isset($s['group_desc']) && $s['group_desc']!='')?" and a.group_desc LIKE '%".$s['group_desc']."%'":"";
		//$q.=(strlen($custiom_ids)>1)?" and a.gid in(".$custiom_ids.")":"";
		//$q.=(!$custiom_ids)?" ":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$sql = "SELECT SQL_CALC_FOUND_ROWS a.* FROM ".$bid."_leads_groups a
				WHERE $q ORDER BY a.gid DESC 
		        LIMIT $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
	
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='37'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$group_rule = array(""=>"Select","1"=>"Sequential","2"=>"Weighted");
		$fieldset = $this->configmodel->getFields('37',$bid);
		$keys = array();
		$header = array('#',"<a href='javascript://'><span id='c_all' class='glyphicon glyphicon-gok'></span></a>");
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){
					if($f['fieldid']==$field['fieldid'])$checked = true;
				}
				if($checked){
					array_push($keys,$field['fieldname']);
					array_push($header,(($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
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
			$v = '<input type="checkbox" class="blk_check" name="blk[]" value="'.$rec['gid'].'"/>';	
			array_push($data,$v);
			$r = $this->configmodel->getDetail('37',$rec['gid'],'',$bid);
			foreach($keys as $k){
				if($k == 'eid')
					$v = $r['empname'];
				elseif($k == 'group_rule')
					$v = $group_rule[$r[$k]];
				else
					$v = isset($r[$k])?nl2br(wordwrap($r[$k],80,"\n")):"";				
				array_push($data,$v);
			}
			
			if($opt_add || $opt_view || $opt_delete){
				$act = '';
				if($type == 'del'){
					$act .= '<a href="'.base_url().'leads/delete_leadsgrp/'.$rec['gid'].'/1"><img src="system/application/img/icons/undelete.png" style="vertical-align:top;" title="Restore" /></a>';
				}else{
					$act .= ($opt_add) ?'<a href="EditLeadGroup/'.$rec['gid'].'"><span class="fa fa-edit"></span></a>':'';
					$act .= ($opt_delete) ? '<a href="'.base_url().'leads/delete_leadsgrp/'.$rec['gid'].'" class="deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
					$act .= '<a href="AddempLeadGroup/'.$rec['gid'].'" ><span class="fa fa-plus" title="Add Employee"></span></a>&nbsp;';
					$act .= '<a href="ListempLeadGroup/'.$rec['gid'].'" ><span title="List Employees" class="glyphicon glyphicon-user"></span></a>';
				}
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function del_lead_grp($id,$bid,$type=''){
		$type = ($type =='') ? '2' : $type;
		$this->db->set('status', $type);
		$this->db->where('gid',$id);
		$this->db->update($bid."_leads_groups");
		$leademp_use = $this->configmodel->leadusageCheck($bid,'group');
		if($leademp_use['type'] == 1)
			$this->db->query("UPDATE  business_lead_use SET `grplimit`=(`grplimit`+1) WHERE bid='".$bid."'");
		$this->auditlog->auditlog_info('Leads Groups',$id. " Updated By ".$this->session->userdata('username'));
		return 1;
	}
	function addlead_grpemp($lgid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$err=0;
		$lgdetails = $this->configmodel->getDetail('37',$lgid,'',$bid);
		$rule = $lgdetails['group_rule'];
		foreach($_POST['emp_ids'] as $eids){
			$check=$this->db->query("SELECT leid FROM ".$bid."_leads_grpemp WHERE eid=".$eids." AND gid='".$lgid."'");
			if($check->num_rows()==0){
				$lgremp_use = $this->configmodel->leadusageCheck($bid,'employee');
				if($lgremp_use['type'] == 1 && $lgremp_use['used'] == 0 && $id == ''){
					return '2';
				}else{
					$cnt = ($rule=='1')
					? $this->db->query("SELECT COALESCE(MIN(counter),0) as cnt FROM ".$bid."_leads_grpemp WHERE gid='".$lgid."' AND status = 1")->row()->cnt
					: '0';
					$err++;
					$this->db->set('bid', $bid);                       
					$this->db->set('gid', $lgid);                       
					$this->db->set('eid', $eids); 
					$this->db->set('counter', $cnt);                    
					if(isset($_POST['empweight'.$eids])){
						$this->db->set('weight', $this->input->post('empweight'.$eids)); 
					}
					$this->db->set('status',1); 
					$this->db->insert($bid."_leads_grpemp");
					$emp_name=$this->get_empname($eids);
					$gname=$this->db->query("SELECT groupname FROM 	".$bid."_leads_groups WHERE gid='".$lgid."'")->row()->groupname;
					$this->auditlog->auditlog_info('Leads Group Employee',$emp_name->empname." added to the group ".$gname);
					if($lgremp_use['type'] == 1)
						$query=$this->db->query("UPDATE business_lead_use SET `emplimit`=(`emplimit` -1) WHERE bid='".$bid."'");
				}
			}
		}
		if($err!=0){
			return 1;
		}else{
			return 0;
		}
	}
	function leadgrpemp_existed($lgid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();
		$res1=$this->db->query("SELECT SQL_CALC_FOUND_ROWS g.groupname,e.empname,a.eid,a.gid ,a.status
							   FROM ".$bid."_leads_grpemp a
							   LEFT JOIN ".$bid."_leads_groups g on a.gid=g.gid
							   LEFT JOIN ".$bid."_employee e on a.eid=e.eid
							   WHERE a.gid=$lgid");
		if($res1->num_rows()>0){					   
			foreach($res1->result_array() as $row){
				$res[]=$row['eid'];
			}
		}
		return $res;
	}
	function leadgrpemplist($lgid,$ofset='0',$limit='20'){
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
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS g.groupname,e.empname,a.weight,a.counter,a.eid,a.gid,a.status	
							   FROM ".$bid."_leads_grpemp a
							   LEFT JOIN ".$bid."_leads_groups g on a.gid=g.gid
							   LEFT JOIN ".$bid."_employee e on a.eid=e.eid
							   WHERE a.gid=$lgid $q
							   ORDER BY e.`empname`
							   LIMIT $ofset,$limit
							   ")->result_array();
       $res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	function get_grEmployees($lgid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res = $this->db->query("SELECT e.empname,a.eid	
							   FROM ".$bid."_leads_grpemp a
							   LEFT JOIN ".$bid."_employee e on a.eid=e.eid
							   WHERE a.gid=$lgid AND e.status = 1 AND a.status = 1 
							   ORDER BY e.`empname`")->result_array();
		return $res;
	}
	function del_leadgrpemp($id,$lgid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("DELETE FROM ".$bid."_leads_grpemp WHERE eid=$id AND gid=$lgid");
		$leademp_use = $this->configmodel->leadusageCheck($bid,'employee');
		if($leademp_use['type'] == 1)
			$this->db->query("UPDATE  business_lead_use SET `emplimit`=(`emplimit`+1) WHERE bid='".$bid."'");
		$emp_name=$this->get_empname($id);
		$gname=$this->db->query("SELECT groupname FROM	".$bid."_leads_groups WHERE gid='".$lgid."'")->row()->groupname;
		$this->auditlog->auditlog_info('Lead Group Employee',$emp_name->empname." is Removed From the Lead Group ".$gname);
		return true;
	}
	function get_empname($eid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("SELECT empname FROM ".$bid."_employee WHERE eid=$eid");
		$res=$sql->row();
		return $res;
	}
	function leadgrpemp_dis($eid,$lgid,$bid){
		$check=$this->db->query("SELECT * FROM ".$bid."_leads_grpemp WHERE eid=".$eid." AND gid=$lgid")->row_array();	
		$status=($check['status']==0)? '1':'0';
		if($status == 1){
			$cnt = $this->db->query("SELECT COALESCE(MIN(counter),0) as cnt FROM ".$bid."_leads_grpemp WHERE gid='".$lgid."' AND status = '1'")->row()->cnt;
			$this->db->set('counter',$cnt);
		}
		$this->db->set('status',$status);
		$this->db->where('eid',$eid);	
		$this->db->where('gid',$lgid);
		$this->db->update($bid.'_leads_grpemp');
		$itemDetail= $this->configmodel->getDetail('37',$lgid,'',$bid);
		$empDetail= $this->configmodel->getDetail('2',$eid,'',$bid);
		$text=($status)?" Enabled":" Disabled";
		$this->auditlog->auditlog_info('Group Employee', $empDetail['empname'].$text." from the group ".$itemDetail['groupname']);
		return $status;
	}
	function employee_list($grid=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res = ($grid=='') ? array(''=>'Select Employee') : array();
		$query=$this->db->query("select eid,empname from ".$bid."_employee WHERE status=1 ORDER BY empname");
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q='';
		$q.=($roleDetail['role']['owngroup']=='1' && $roleDetail['role']['admin']!='1') ? " AND (g.eid = '".$this->session->userdata('eid')."' OR e.eid='".$this->session->userdata('eid')."')":"";
		$q .= ($grid=='') ? '' : " AND g.gid='".$grid."'";
		$query = ($grid=='') ? "SELECT eid,empname FROM ".$bid."_employee WHERE status = '1' ORDER BY empname" : "SELECT e.eid,e.empname FROM ".$bid."_employee e
								 LEFT JOIN ".$bid."_leads_grpemp ge on e.eid=ge.eid
								 LEFT JOIN ".$bid."_leads_groups g on g.gid=ge.gid
								 WHERE e.status = '1' ".$q." ORDER BY e.empname";
		$query=$this->db->query($query);
		if($query->num_rows()>0){
			foreach($query->result_array() as $rt)
			$res[$rt['eid']]=$rt['empname'];
		}
		return $res;
	}
	function allEmployees($gid,$eid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res = array();
		$query = "SELECT e.eid,e.empname FROM ".$bid."_employee e
				 LEFT JOIN ".$bid."_leads_grpemp ge on e.eid=ge.eid
				 LEFT JOIN ".$bid."_leads_groups g on g.gid=ge.gid
				 WHERE e.status = '1' AND ge.status = '1' AND g.gid='".$gid."' ORDER BY e.empname";
		$query=$this->db->query($query);
		if($query->num_rows()>0){
			if($eid == 0){
				$res[0] = "Select";
			}
			foreach($query->result_array() as $rt){
				$res[$rt['eid']]=$rt['empname'];
			}
		}
		if( ! array_key_exists($eid,$res)){
			if($eid != 0){
				$emp = $this->empgetname($eid);
			}
			if(isset($emp) && $emp != '')
				$res[$eid] = $emp;
		}
		return $res;
	}
	function addComments($post){

		$leadid = $_POST['leadid'];
		$bid = $_POST['bid'];
		$sql = "INSERT INTO ".$bid."_leads_comments SET
				leadid		='".$leadid."',
				bid			='".$bid."',
				eid			='".$this->session->userdata('eid')."',
				comment		='".addslashes($_POST['comments'])."'";
		$this->db->query($sql);
		/* New code for resolving the performance issue(temp solution) */
		//$this->db->query("REPLACE INTO ".$bid."_lead_child SET leadid='".$leadid."',bid = '".$bid."';");
		/* End */
		$leadtype = $this->db->query("SELECT lead_status as type FROM ".$bid."_leads WHERE leadid ='".$leadid."'")->row()->type;
		$q = ($leadtype != $_POST['lead_status']) ? ",convertedon='".date("Y-m-d H:i:s")."'"  : '';
		$this->db->query("UPDATE ".$bid."_leads SET `lead_status` = '".$_POST['lead_status']."' $q  WHERE leadid='".$leadid."'");
		$this->auditlog->auditlog_info('Leads',$this->session->userdata('username'). " Added new comments to Lead ".$leadid);
		return 1;
	}
	function addRemarks($post){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$sql = "INSERT INTO ".$bid."_leads_remarks SET
				leadid		='".$_POST['leadid']."',
				bid			='".$bid."',
				eid			='".$this->session->userdata('eid')."',
				remark		='".addslashes($_POST['remark'])."'";
		$this->db->query($sql);
		/* New code for resolving the performance issue(temp solution) */
		//$this->db->query("REPLACE INTO ".$bid."_lead_child SET leadid='".$_POST['leadid']."',bid = '".$bid."';");
		/* End */
		$this->auditlog->auditlog_info('Leads',$this->input->post('name'). " Added new Remarks to Lead ".$_POST['leadid']);
		return 1;
	}
	function getComments($leadid,$bid){
		$sql="SELECT SQL_CALC_FOUND_ROWS lc.*,e.empname FROM ".$bid."_leads_comments lc
			  LEFT JOIN ".$bid."_employee e ON lc.eid = e.eid 
			  WHERE lc.leadid='".$leadid."' ORDER BY lc.cdate DESC";				 
		$rst = $this->db->query($sql)->result_array();
		$header = array('#'
						,'Comments'
						,'Commented By'
						,'Date'
						);
		$ret['header'] = $header;
		$list = array();
		$i = 1;
		foreach($rst as $rec){
			$data = array($i
						  ,stripslashes(nl2br(wordwrap($rec['comment'],80,"\n")))
						  ,$rec['empname']
						  ,$rec['cdate']
						  );
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	 function leadhistorynames($leadid,$bid){
			$leadhistory = $this->db->query("SELECT GROUP_CONCAT(leadhistory SEPARATOR ',') as leadhistory FROM ".$bid."_leads WHERE leadid IN(".$leadid." )")->row()->leadhistory;
			$header = array('#'
						,'Lead Assigned From '
						,'Lead Assigned To'
						,'Date'
						);
			$ret['header'] = $header;
			$list = array();
            $rec1 =isset($leadhistory)?@explode(",", $leadhistory):'';
			 $i = 1;
             foreach($rec1 as $rec){
				if($rec != ''){
				 $rec2 = @explode("->", $rec);
				 $emp = $this->empname($rec2['0']);
				 $rec4 = @explode("/", $rec2['1']);
				 $emp2 = $this->empname($rec4['0']);
				 $date1 = @explode(",", $rec4['1']);
				 $data = array($i
							  ,$emp
							  ,$emp2
							  ,$date1['0']
							  );
				$i++;
				array_push($list,$data);
			}

		$ret['rec']=$list;
	   }
		return $ret;
	}
	
    function empname($eid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("SELECT empname FROM ".$bid."_employee WHERE eid='".$eid."'");
		$res=$sql->result_array();
		$rec = array();
		foreach($res as $re){
	      $rec =  $re['empname'];
	  }
	  	return $rec;
	}
	
	function getRemarks($leadid,$bid){
		$sql="SELECT SQL_CALC_FOUND_ROWS lc.*,e.empname FROM ".$bid."_leads_remarks lc
			  LEFT JOIN ".$bid."_employee e ON lc.eid = e.eid 
			  WHERE lc.leadid='".$leadid."' ORDER BY lc.cdate DESC ";				 
		$rst = $this->db->query($sql)->result_array();
		$header = array('#'
						,'Remark'
						,'Remark By'
						,'Date'
						);
		$ret['header'] = $header;
		$list = array();
		$i = 1;
		foreach($rst as $rec){
			$data = array($i
						  ,stripslashes(nl2br(wordwrap($rec['remark'],80,"\n")))
						  ,$rec['empname']
						  ,$rec['cdate']
						  );
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		
		return $ret;
	}
	function getCommentById($leadid,$bid){
		$sql="SELECT lc.comment FROM ".$bid."_leads_comments lc
			  WHERE lc.leadid='".$leadid."' ORDER BY lc.cdate DESC LIMIT 0,1";				 
		$rst = $this->db->query($sql)->result_array();
		return isset($rst[0]['comment']) ? $rst[0]['comment'] : '' ;
	}
	function getRemarkById($leadid,$bid){
		$sql="SELECT lc.remark FROM ".$bid."_leads_remarks lc
			  WHERE lc.leadid='".$leadid."' ORDER BY lc.cdate DESC LIMIT 0,1";				 
		$rst = $this->db->query($sql)->result_array();
		return isset($rst[0]['remark']) ? $rst[0]['remark'] : '' ;
	}
	function getLeadComments($leadid,$bid){
		$sql="SELECT SQL_CALC_FOUND_ROWS lc.*,e.empname FROM ".$bid."_leads_comments lc
			  LEFT JOIN ".$bid."_employee e ON lc.eid = e.eid 
			  WHERE lc.leadid='".$leadid."' ORDER BY lc.cdate DESC";				 
		$rst = $this->db->query($sql)->result_array();
		$data = '';
		foreach($rst as $rec){
			$data .= $rec['empname']." : ".stripslashes(str_replace("\n"," ",$rec['comment']))."  ";
		}
		return $data;
	}
	function getLeadRemarks($leadid,$bid){
		$sql="SELECT SQL_CALC_FOUND_ROWS lc.*,e.empname FROM ".$bid."_leads_remarks lc
			  LEFT JOIN ".$bid."_employee e ON lc.eid = e.eid 
			  WHERE lc.leadid='".$leadid."' ORDER BY lc.cdate DESC";				 
		$rst = $this->db->query($sql)->result_array();
		$data = '';
		foreach($rst as $rec){
			$data .= $rec['empname']." : ".stripslashes(str_replace("\n"," ",$rec['remark']))."  ";
		}
		return $data;
	}
	function getLeadType($type,$statname){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$res=array();
		if($type == 1){
			$res[$type] = $statname;
			$res[2] = " Lead ";
			$res[5] = " Junk ";
		}else{	
			$sql=$this->db->query("SELECT * FROM ".$bid."_leads_status WHERE status=1");
			if($sql->num_rows()>0){
				foreach($sql->result_array() as $r)	{
					$res[$r['id']]=$r['type'];
				}
			}
		}
		return $res;
	}
	function callHistoryList($leadid,$bid){
		$q = '';
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (a.eid='".$this->session->userdata('eid')."' OR d.eid='".$this->session->userdata('eid')."' OR a.assignto='".$this->session->userdata('eid')."')";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND a.pulse > '0'";
		}
		 $sql ="SELECT SQL_CALC_FOUND_ROWS a.callid,a.pulse=0,
				if(a.pulse=0,'1','0') as missed
				FROM ".$bid."_callhistory a
				LEFT JOIN ".$bid."_employee c ON a.eid=c.eid
				LEFT JOIN ".$bid."_groups d ON a.gid=d.gid
				WHERE a.status!='2' $q 
				AND a.leadid=$leadid
				ORDER BY a.starttime DESC";							 
		$rst = $this->db->query($sql)->result_array();
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='6'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('6');
		$keys = array();
		$header = array('#');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldname']);
					array_push($header,(($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
					if($field['fieldname'] == 'pulse'){
						array_push($keys,'duration');
						array_push($header,'Duration');
					}
				}
			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,'custom['.$field['fieldid'].']');
					array_push($header,$field['customlabel']);
				}
			}
		}
		array_push($header,$this->lang->line('level_Action'));
		$ret['header'] = $header;
		$list = array();
		$i = 1;
		foreach($rst as $rec){
			$data = array($i);
			$r = $this->configmodel->getDetail('6',$rec['callid'],'',$bid);
			foreach($keys as $k){
				$v = isset($r[$k])?$r[$k]:"";
				$v = ($rec['missed']=='1')? "<font color='red'>".$v."</font>":$v;
				array_push($data,$v);
			}
			$act = ($opt_add) ?'<a href="Report/edit/'.$r['callid'].'"><span title="Edit" class="fa fa-edit"></span></a>':'';
			$act .= ($opt_delete) ? '<a href="'.base_url().'Report/Delete_call/'.$r['callid'].'" class="deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
			$act .= '<a href="Report/activerecords/'.$r['callid'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="fa fa-file-text" title="List Employee Group"></span></a>';
			$act .= ($roleDetail['role']['accessrecords']=='0') ? (($r['filename']!='' && file_exists('sounds/'.$r['filename']))
					?'<a target="_blank" href="'.site_url('sounds/'.$r['filename']).'"><span title="Sound" class="fa fa-volume-up"></span></a>'
					:'<span class="glyphicon glyphicon-volume-off"></span> '):"";
			$act .= anchor("Report/empdetail/".$rec['callid'], ' <span title="List Employees" class="glyphicon glyphicon-user"></span>','class="btn-empl" data-toggle="modal" data-target="#modal-empl"');
			array_push($data,$act);
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function blk_down($bid,$type=''){
		$roleDetail = $this->roleDetail;
		$modid = ($type == 1) ? '46' : '26' ;
		$itemDetail=array();
		$fieldset = $this->configmodel->getFields($modid,$bid);
		$csv_output = "";
		$hkey=array();
		$csv_output ='';
		$header = array();
		foreach($_POST['lisiting'] as $key=>$fiels){
				$hkey[]=$key;
				$header[]=$fiels;
		}
		$csv_output = @implode(",",$header)."\n";
		$call_ids = @explode(",",$_POST['call_ids']);
		$name = $bid.'_'.
				$this->session->userdata('eid').'_'.
				time();
		mkdir('reports/'.$name);
		chmod('reports/'.$name,0777);
		$data_file = 'reports/'.$name.'/leadsdownload.csv';
		$fp = fopen($data_file,'w');
		fwrite($fp,$csv_output);
		foreach($call_ids as $callids){
			$data = array();
			$r = $this->configmodel->getDetail($modid,$callids,'',$bid);
			//print_r($r);
			$i=0;
			foreach($hkey as $k){
				if(in_array($k,array('gid','assignto','enteredby'))){
					$v=($k == 'gid')?$r['groupname']:((($k=='enteredby')) ? $r['enteredempname'] : $r['assignempname']);
				}elseif($k == 'lead_status'){
					$v=$r['type'];
				}elseif($k == 'convertedby'){
					$v=$r['convertedemp'];
				}elseif($k == "comments"){
					$v = $this->getLeadComments($callids,$bid);
				}elseif($k == "remarks"){
					$v = $this->getLeadRemarks($callids,$bid);
				}else{
					$v=(isset($r[$k])) ? '"'.$r[$k].'"' : '';
				}
				array_push($data,$v);
			}
			$csv_output =implode(",",$data)."\n";
			fwrite($fp,$csv_output);
		}
		fclose($fp);
		chdir('reports')."<br>";
		exec('zip -r '.$name.'.zip '.$name);
		exec('rm -rf '.$name);
		return $name;
	}
	function empgetname($eid){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$empname=$this->db->query("SELECT empname FROM ".$bid."_employee WHERE eid='".$eid."'")->row()->empname;
		return ($empname != '') ? $empname : '';
	}
	function leadnumber($leadid){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$number=$this->db->query("SELECT number FROM ".$bid."_leads WHERE leadid='".$leadid."'")->row()->number;
		return ($number != '') ? $number : '';
	}
	function leadDuplicate($number,$email){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$rst=$this->db->query("SELECT leadid FROM ".$bid."_leads WHERE number='".$number."'");
		if($rst->num_rows() > 0){
			$rst1 = $rst->row();
			return isset($rst1->leadid) ? $rst1->leadid : '' ;
		}elseif($email!=""){
			$rst=$this->db->query("SELECT leadid FROM ".$bid."_leads WHERE email='".$email."'");
			if($rst->num_rows() > 0){
				$rst1 = $rst->row();
				return isset($rst1->leadid) ? $rst1->leadid : '' ;
			}else{
				return '0';
			}
		}
		return '0';
	}
	function leadDupliCheck($number,$email,$leadid){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$rst = $this->db->query("SELECT number,email,parentId FROM ".$bid."_leads WHERE leadid ='".$leadid."'")->result_array();
		if($number == $rst[0]['number'] && $email == $rst[0]['email']){
			return 0;
		}else{
			$rst1 = $this->db->query("SELECT leadid FROM ".$bid."_leads WHERE number ='".$number."' AND email='".$email."' AND leadid !='".$leadid."'");
			if($rst1->num_rows() > 0){
				$res1 = $this->db->query("SELECT number,email FROM ".$bid."_leads WHERE leadid ='".$rst[0]['parentId']."'")->result_array();
				if($number == $res1[0]['number'] || $email == $res1[0]['email'])
					return 0;
				else{
					$rst2 = $rst1->row();
					return isset($rst2->leadid) ? $rst2->leadid : '' ;
				}
			}else{
				return 0;
			}
		}
		//~ if($rst->num_rows() > 0){
			//~ $res1 = $this->db->query("SELECT duplicate as dup FROM ".$bid."_leads WHERE leadid ='".$leadid."'")->row()->dup;
			//~ if($res1 == 0)
				//~ return '0';
			//~ else
				//~ return '1';
		//~ }else{
			//~ $rst=$this->db->query("SELECT leadid FROM ".$bid."_leads WHERE email='".$email."' AND leadid !='".$leadid."'");
			//~ if($rst->num_rows() > 0){
				//~ $res2 = $this->db->query("SELECT duplicate as dup FROM ".$bid."_leads WHERE leadid ='".$leadid."'")->row()->dup;
				//~ if($res2 == 0)
					//~ return '0';
				//~ else
					//~ return '1';
			//~ }else{
				//~ return '0';
			//~ }
		//~ }
	}
	function leadDupliHistory($leadid,$bid){
		$q = '';
		$modid = '26';
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (a.eid='".$this->session->userdata('eid')."' OR d.eid='".$this->session->userdata('eid')."' OR a.assignto='".$this->session->userdata('eid')."')";
		}
		$type = $this->db->query("SELECT lead_generate FROM business WHERE bid='".$bid."'")->row()->lead_generate;
		if($type == '0'){
			$q .= " AND a.pulse > '0'";
		}
		$sql ="SELECT parentId as leadid
				FROM ".$bid."_leads 
				WHERE leadid ='".$leadid."'";							 
		$rst = $this->db->query($sql)->result_array();
		$ret['count'] = 1;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']==$modid){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields($modid,$bid);
		$keys = array();
		$header = array('#');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){
					if($f['fieldid']==$field['fieldid'])$checked = true;
				}
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
		array_push($header,'Distribution Type');
		array_push($header,'Alert');
		$ret['header'] = $header;
		$list = array();
		$i = 1;
		foreach($rst as $rec){
			$data = array($i);
			$r = $this->configmodel->getDetail($modid,$rec['leadid'],'',$bid);
			$dis_type = array("0"=>"Uncategorized","1"=>"Manual","2"=>"Auto");
			$alert_type = array("0"=>" No ","1"=>" Email Alert ","2"=>" SMS Alert ","3"=>" Both ");
			foreach($keys as $k){
				if($k=='assignto'){
					$v = '<a href="Employee/activerecords/'.$r['assignto'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['assignempname'].'</a>';
				}elseif($k=='enteredby'){
					$v = '<a href="Employee/activerecords/'.$r['enteredby'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['enteredempname'].'</a>';
				}elseif($k=='convertedby'){
					$v = '<a href="Employee/activerecords/'.$r['convertedby'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['convertedemp'].'</a>';
				}elseif($k=='gid'){
					$v = '<a href="leads/leadgrp_active/'.$r['gid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['groupname'].'</a>';
				}elseif($k == 'lead_status'){
					$v = $r['type'];
				}else{
						$v = isset($r[$k])?nl2br(wordwrap(stripslashes($r[$k]),80,"\n")):"";	
				}
				array_push($data,$v);
			}
			$v = (isset($r['dis_type']) && $r['dis_type'] != '') ? $dis_type[$r['dis_type']]: '';
			array_push($data,$v);
			$v = (isset($r['alert_type']) && $r['alert_type'] != '') ? $alert_type[$r['alert_type']]: '';
			array_push($data,$v);
			foreach ($data as $k=>$v){
				$v = ($r['duplicate'] == 1) ? "<font color='RED'>".$v."</font>" :  $v;
				$data[$k] = $v;
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function refreshcounter($lgid,$bid){
		$sql=$this->db->query("UPDATE ".$bid."_leads_grpemp SET counter = 0 WHERE gid=$lgid");
		if($this->db->affected_rows() >0){
			$this->auditlog->auditlog_info('Leads',$lgid." Counter Reset By ".$this->session->userdata('username'));
			return 1;
		}
		else
			return 0;
	}
	function callcount($leadid){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$sql=$this->db->query("SELECT count(callid) as cnt FROM `".$bid."_callhistory` WHERE leadid='".$leadid."'")->result_array();
		$callcnt = $sql[0]['cnt'];
		return $callcnt;
	}
	function sendF($leadid,$bid,$mod){
		$getDetail=$this->configmodel->getDetail($mod,$leadid,'',$bid);
		if($getDetail['assignto']!=$_POST['asto']){
			$this->db->set('assignto',$_POST['asto']);
			$this->db->where('leadid',$leadid);
			$this->db->update($bid."_leads");
			/* New code for resolving the performance issue(temp solution) */
			$this->db->query("REPLACE INTO ".$bid."_lead_child SET leadid='".$leadid."',bid = '".$bid."';");
			/* End */
		}
		$case=(isset($_POST['sendSMs']))?'1':'2';
		$message="";
		 $arr=array(
	        "Group Name " => "Grp",
	        "Assign To" => "AsgnTo",
	        "Entered By" => "Entby",
	        "Ticket Id" => "id",
	        "Name" => "Name",
			"Email" => "Email",
		    "Number" => "Nbr",
			"Source" => "src",
			"Created" => "Crt",
			"Reference ID" => "RefID",
			"Converted On" => "Covton",
			"Lead Owner" => "Ownr");
				
				
 	foreach($fieldset as $field){
     foreach($arr as $key => $valu) {
		if($field['customlabel'] == $key){
		$arr[$key] = $valu;
	}
  }
}

		foreach($_POST['formfields'] as $form){
		$fs=explode("~",$form);
		$label = $fs[0];
		$value = $fs[1];
 
		if($arr[$label] != ''){
		$message.=$arr[$label].":".$value.",";
		}else{
		$message.=$label.":".$value.",";
		}
}
		$empDetail=$this->configmodel->getDetail('2',$_POST['asto'],'',$bid);
		switch($case){
				case '1':
							if($_POST['smsBal']>0){
								$reply=sms_send($empDetail['empnumber'],$message);
								$set_array=array("contentid"=>(isset($reply[0]))?$reply[0]:'',
										 "number"=>$empDetail['empnumber'],
										"content"=>$message,
										"datetime"=>date('Y-m-d h:i:s'),
										"source"=>'Lead',
										"dnd_status"=>'0',
										"status"=>1);	
								$sms_push=$this->sms_message($set_array);
								return "SMS Sent Successfully";exit;
							}else{
								return "Fail to sent";exit;
							}
						break;
			case '2':	
					if($_POST['Emailconfig']>0){
							$itemDetail = $this->configmodel->getDetail('27',$bid,'',$bid);
							$config['protocol']    = 'smtp';
							$config['smtp_host']    = $itemDetail['smtp'];
							$config['smtp_port']    = $itemDetail['port'];
							$config['smtp_user']    = $itemDetail['email'];
							$config['smtp_pass']    = base64_decode($itemDetail['password']);
							$config['charset']    = 'utf-8';
							$config['newline']    = "\r\n";
							$config['mailtype'] = 'html'; // or html
							$config['validation'] = TRUE; // bool whether to validate email or not   
							$config['validation'] = TRUE; // bool whether to validate email or not      
							$this->email->initialize($config);
							$this->email->from($itemDetail['email'],$itemDetail['fname']);
							$this->email->to($empDetail['empemail']); 
							$this->email->subject("Field data from ". $empDetail['empemail']);
							$this->email->message($message);  
							$status=$this->email->send();
							$res=$this->configmodel->sentmails($bid,$itemDetail['email'],$empDetail['empemail'],$status);
							return "Email Sent Successfully";exit;
						}else{
							return "Fail to sent";exit;
					}
		}		
	}
	function sms_message($set_arr){
		$bid=$this->session->userdata('bid');
		$id=$this->db->query("SELECT COALESCE(MAX(`smsid`),0)+1 as id FROM ".$bid."_smsreport")->row()->id;
		$set_arr['smsid']=$id;
		$set_arr['eid']=$this->session->userdata('eid');
		$this->db->insert($bid."_smsreport",$set_arr);
		$this->reportmodel->sms_use('1');
	}
	function getLeadStatus(){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$res=array(""=>"Select");
		$leadchk = $this->sysconfmodel->leadtypeCheck();
		$sql=$this->db->query("SELECT * FROM ".$bid."_leads_status WHERE status=1");
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $r)	{
				if(!($leadchk == 1 && $r['id'] == 1))
					$res[$r['id']]=$r['type'];
			}
		}
		return $res;
	}
	function blkStatChng(){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$leadIds = @explode(',',$this->input->post('ids'));
		$leadstatus = $this->input->post('lead_status');
		if(isset($_POST['lead_status']) && $leadstatus != ''){
			for($id=0;$id<count($leadIds);$id++){
				$this->db->set('lead_status',$leadstatus);
				$this->db->set('convertedon',date("Y-m-d H:i:s"));
				$this->db->where('leadid',$leadIds[$id]);
				$this->db->update($bid."_leads");
				/* New code for resolving the performance issue(temp solution) */
				//$this->db->query("REPLACE INTO ".$bid."_lead_child SET leadid='".$leadIds[$id]."',bid = '".$bid."';");
				/* End */
			}
		}
		return '1';
	}
	function getSysfieldName($fieldid){
		$fname = $this->db->query("SELECT fieldname as fname FROM systemfields WHERE fieldid='".$fieldid."'")->row()->fname;
		return $fname;
	}
	function getCustfieldName($fieldid,$bid){
		$fname = $this->db->query("SELECT fieldname as fname FROM ".$bid."_customfields WHERE fieldid='".$fieldid."' AND modid='26'")->row()->fname;
		return $fname;
	}
	function getCustfieldKey($fieldid,$bid){
		$fname = $this->db->query("SELECT field_key as fname FROM ".$bid."_customfields WHERE fieldid='".$fieldid."' AND modid='26'")->row()->fname;
		return $fname;
	}
	function getfieldValchk($fieldval,$fieldid,$bid){
		$fielddetails = $this->db->query("SELECT * FROM ".$bid."_customfields WHERE fieldid='4' AND modid='26'")->result_array();
		if(!empty($fielddetails)){
			if($fielddetails[0]['fieldtype'] == 'checkbox' || $fielddetails[0]['fieldtype'] == 'radio' || $fielddetails[0]['fieldtype'] == 'dropdown'){
				$options = $fielddetails[0]['options'];
				if(strstr($options,$fieldval)){
					return true;
				}
			}else{
				return true;
			}
		}
		return false;
	}
}
