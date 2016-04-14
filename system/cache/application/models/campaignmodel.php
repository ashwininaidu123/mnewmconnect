<?php
Class campaignmodel extends Model{
	var $KeyChar;
	function campaignmodel(){
		parent::Model();
		$this->load->model('configmodel');
	}
	function getCampaignlist($bid,$ofset='0',$limit='20'){
		
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
		$custiom_ids=$this->configmodel->customSearch((isset($s['custom']))?$s['custom']:'','4',$bid);
		$con .= (isset($s['campaign_name']) && $s['campaign_name']!='')?" AND p.campaign_name like '%".$s['campaign_name']."%'":"";
		$con .= (isset($s['campaign_startdate']) && $s['campaign_startdate']!='')?" AND date(p.campaign_startdate) = '=".$s['campaign_startdate']."='":"";
		$con .= (isset($s['campaign_enddate']) && $s['campaign_enddate']!='')?" AND date(p.campaign_enddate) = '".$s['campaign_enddate']."'":"";
		$con .= (isset($s['perday_limit']) && $s['perday_limit']!='')?" AND p.perday_limit = '".$s['perday_limit']."'":"";
		$con .= (isset($s['perday_lead']) && $s['perday_lead']!='')?" AND p.perday_lead = '".$s['perday_lead']."'":"";
		$con .= (isset($s['total_lead']) && $s['total_lead']!='')?" AND p.total_lead = '".$s['total_lead']."'":"";
		$con .= (isset($s['status']) && $s['status']!='')?" AND p.status = '".$s['status']."'":"";
		$con .= (isset($s['budget']) && $s['budget']!='')?" AND p.budget = '".$s['budget']."'":"";
		$con .= (isset($s['file_id']) && $s['file_id']!='')?" AND p.file_id = '".$s['file_id']."'":"";
		$con .= (isset($s['action_oncomplete']) && $s['action_oncomplete']!='')?" AND p.action_oncomplete = '".$s['action_oncomplete']."'":"";
		$con .= (isset($s['campaign_type']) && $s['campaign_type']!='')?" AND p.campaign_type = '".$s['campaign_type']."'":"";
		$con .=(strlen($custiom_ids)>1)?" and p.campaign_id in(".$custiom_ids.")":"";
		$con .=(!$custiom_ids)?" AND 0 ":"";
		$sql = "SELECT SQL_CALC_FOUND_ROWS campaign_id FROM ".$bid."_campaign p
				WHERE 1 ".$con."
				ORDER BY p.created_date DESC
				limit $ofset,$limit";
				//echo $sql; exit;
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		
		$opt_add 	= $roleDetail['modules']['33']['opt_add'];
		$opt_view 	= $roleDetail['modules']['33']['opt_view'];
		$opt_delete = $roleDetail['modules']['33']['opt_delete'];
		
		$fieldset = $this->configmodel->getFields('33',$bid);
		$keys = array();
		$header = array('#');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldname']);
					if($field['fieldname']!='filename')array_push($header,(($field['customlabel']!="")
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
			$r = $this->configmodel->getDetail('33',$rec['campaign_id'],'',$bid);
			foreach($keys as $k){
				if($k=='filename'){
					$sound = site_url('sounds/'.$r[$k]);
				}elseif($k=='cid'){
					$v = isset($r['companyname'])?$r['companyname']:"";
					array_push($data,$v);
				}else{
					$v = isset($r[$k])?$r[$k]:"";
					array_push($data,$v);
				}
			}
			$act = "";
			if($opt_add || $opt_view || $opt_delete){
				$act .= ($opt_add)? ' <a href="campaign/addpopup/'.$r['campaign_id'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Edit" class="fa fa-edit"></span></a>':'';
				$act .= ($opt_delete)? ' <a href="campaign/delete/'.$r['campaign_id'].'" class="confirm deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
				$act .= '<a href="campaign/details/'.$r['campaign_id'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="fa fa-file-text" title="Lead Details"></span></a>';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	
	function addCampaign(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$campaign_id=(isset($_POST['campaign_id']) && $_POST['campaign_id']!='')
					?$_POST['campaign_id']
					:$this->db->query("SELECT COALESCE(MAX(`campaign_id`),0)+1 as id FROM ".$bid."_campaign")->row()->id;
		if($_POST['campaign_id']==''){
			$sql = "INSERT INTO ".$bid."_campaign SET
					 campaign_id			= '".$campaign_id."'
					,bid			= '".$bid."'
					,campaign_name		= '".$_POST['campaign_name']."'
					,campaign_startdate			= '".$_POST['campaign_startdate']."'
					,campaign_enddate		= '".$_POST['campaign_enddate']."'
					,perday_limit		= '".$_POST['perday_limit']."'
					,perday_lead		= '".$_POST['perday_lead']."'
					,total_lead		= '".$_POST['total_lead']."'
					,status		= '".$_POST['status']."'
					,created_date		= '".$_POST['prinumber']."'
					,created_by		= '".$_POST['prinumber']."'
					,budget		= '".$_POST['budget']."'
					,action_oncomplete		= '".$_POST['action_oncomplete']."'
					,campaign_type		= '".$_POST['campaign_type']."'
					,file_id		= '".$_POST['file_id']."'";
					 
			$this->db->query($sql);
			$this->auditlog->auditlog_info($this->lang->line('label_campaignconfig'),$_POST['campaign_name']." Added successfully");
		}else{
			$sql = "UPDATE ".$bid."_campaign SET bid			= '".$bid."'";
			$sql .=	isset($_POST['campaign_name']) ? ",campaign_name			= '".$_POST['campaign_name']."'":"";
			$sql .=	isset($_POST['campaign_startdate'])? ",campaign_startdate		= '".$_POST['campaign_startdate']."'":"";
			$sql .=	isset($_POST['campaign_enddate'])? ",campaign_enddate			= '".$_POST['campaign_enddate']."'":"";
			$sql .=	isset($_POST['perday_limit'])? ",perday_limit			= '".$_POST['perday_limit']."'":"";
			$sql .=	isset($_POST['perday_lead'])? ",perday_lead			= '".$_POST['perday_lead']."'":"";
			$sql .=	isset($_POST['total_lead'])? ",total_lead			= '".$_POST['total_lead']."'":"";
			$sql .=	isset($_POST['status'])? ",status			= '".$_POST['status']."'":"";
			$sql .=	isset($_POST['budget'])? ",budget			= '".$_POST['budget']."'":"";
			$sql .=	isset($_POST['action_oncomplete'])? ",action_oncomplete			= '".$_POST['action_oncomplete']."'":"";
			$sql .=	isset($_POST['campaign_type'])? ",campaign_type			= '".$_POST['campaign_type']."'":"";
			$sql .=	isset($_POST['file_id'])? ",file_id			= '".$_POST['file_id']."'":"";
			$sql .=	" WHERE campaign_id			= '".$_POST['campaign_id']."'";
			$this->db->query($sql);
		}
		if(isset($_POST['custom']))
		foreach($_POST['custom'] as $fid=>$val){
			$sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
					 bid			= '".$bid."'
					,modid			= '".$_POST['modid']."'
					,fieldid		= '".$fid."'
					,dataid			= '".$campaign_id."'
					,value			= '".(is_array($val)?implode(',',$val):$val)."'";
			$this->db->query($sql);
		}
		
		return array('campaign_id'=>$campaign_id);
	}
	
	function del_campaign($id,$bid,$stat){
		$this->db->set('status', $stat);
		$this->db->where('campaign_id',$id);
		$this->db->update($bid."_campaign");
		$this->auditlog->auditlog_info('Campaign ',$id. " Changed By ".$this->session->userdata('username'));
		return 1;	
	}
	 
	function getReport($bid,$ofset,$limit,$type=''){
		$q= '';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
			
		}
        $custiom_ids=$this->configmodel->customSearch((isset($s['custom']))?$s['custom']:'','34',$bid);
		$q .= ($type=='del') ? " a.status = 2" : " a.status = 1";
		$q.=(isset($s['campaign_id']) && $s['campaign_id']!='') ? " AND a.campaign_id = '".$s['campaign_id']."'":"";
		$q.=(isset($s['caller_name']) && $s['caller_name']!='')?" AND a.caller_name LIKE '%".$s['caller_name']."'":"";
		$q.=(isset($s['caller_number']) && $s['caller_number']!='')?" AND a.caller_number LIKE '%".$s['caller_number']."%'":"";
		$q.=(isset($s['caller_email']) && $s['caller_email']!='')?" AND a.caller_email LIKE '%".$s['caller_email']."%'":"";
		$q.=(isset($s['call_time']) && $s['call_time']!='')?" AND date(a.call_time)>= '".$s['call_time']."'":"";
		$q.=(isset($s['duration']) && $s['duration']!='')?" AND a.duration = '".$s['duration']."'":"";
		$q.=(!$custiom_ids) ? " AND 0 ":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$sql = "SELECT SQL_CALC_FOUND_ROWS a.callid
				FROM ".$bid."_campaign_report a 
				WHERE $q ORDER BY a.callid DESC limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='34'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('34',$bid);
		$keys = array();
		$header = array('#',"<a href='javascript://'><span id='c_all' class='glyphicon glyphicon-gok'></span></a>");
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing'] && !in_array($field['fieldname'],array('status'))){
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
		//array_push($header,"Counter");
		if($opt_add || $opt_view || $opt_delete)
			array_push($header,$this->lang->line('level_Action'));
		$ret['header'] = $header;
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$data = array($i);
			$v=($opt_delete) ? '<input type="checkbox" class="blk_check" name="blk[]" value="'.$rec['callid'].'"/>':'';	
			array_push($data,$v);
			$r = $this->configmodel->getDetail('34',$rec['callid'],'',$bid);
			$chklead = $this->getLeadContact_Info($r['caller_number'],$bid);
			foreach($keys as $k){
				if($k=='campaign_id'){
					$v = '<a href="campaign/details/'.$r['campaign_id'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['campaign_name'].'</a>';
				}else{
					$v = isset($r[$k])?$r[$k]:"";	
				}
				$v = ($chklead == 0) ? "<font color='green'>".$v."</font>":$v;
				array_push($data,$v);
			}
			$act = '';
			if($type == 'del'){
				$act = ($opt_add) ?'<a href="campaign/undeleteCampReport/'.$rec['callid'].'"><img src="system/application/img/icons/undelete.png" title="Restore" /></a>':'';
			}elseif(($type == 'act' || $type == '') && $opt_add || $opt_view || $opt_delete){
				$act = ($opt_add) ?'<a href="campaign/report_edit/'.$rec['callid'].'"><span title="Edit" class="fa fa-edit"></span></a>':'';
				$act .= ($opt_delete) ? '<a href="'.base_url().'campaign/deleteCampReport/'.$rec['callid'].'" class="deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
				$act .= '<a href="campaign/reportDetails/'.$r['callid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="fa fa-file-text"  title="campaign report Details"></span></a>';
				$act .= anchor("Report/followup/".$rec['callid']."/0/campaign", ' <img src="system/application/img/icons/comments.png" title="Followups" width="16" height="16">',array(' class="btn-danger" data-toggle="modal" data-target="#modal-responsive"'));
				$act .= ($r['caller_email']!='')?"<a href=\"Javascript:void(null)\" onClick=\"window.open('/Email/compose/".$rec['callid']."/campaign', 'Counter', 'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=950,height=480,resizable=1')\">&nbsp;<span title='Send Mail' class='fa fa-envelope'></span></a>" : '&nbsp;<span title="Send Mail" class="fa fa-envelope"></span>';
				$act .= anchor("Report/clicktoconnect/".$rec['callid']."/6", '<span title="click To Connect" class="fa fa-phone"></span>',array('class'=>'clickToConnect'));
				$act .= anchor("Report/sendSms/".$rec['callid']."/campaign", '<span title="Click to send SMS" class="fa fa-comment"></span>','class="clickToSMS" data-toggle="modal" data-target="#modal-empl"');	
				$act .= ($chklead == 0) ? '&nbsp;<img src="system/application/img/icons/convertlead.png" title="Convert as lead" width="16" height="16" />' : '&nbsp;<a href="campaign/convertlead/'.$rec['callid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><img src="system/application/img/icons/convertlead.png" title="Convert as lead" width="16" height="16" /></a>';
			}
			array_push($data,$act);
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function getCampaigns(){
		$res=array();	
		$sql=$this->db->query("SELECT campaign_id,campaign_name FROM ".$this->session->userdata('bid')."_campaign WHERE status=1");
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $r)	{
				$res[$r['campaign_id']]=$r['campaign_name'];
			}
		}
		return $res;
	}
	function report_update($id,$bid){
		$itemDetail = $this->configmodel->getDetail('34',$id,'',$bid);
		$rate=0;
		$val='';
		$arr=array_keys($_POST);
		for($i=0;$i<sizeof($arr);$i++){
			if($arr[$i]!="update_system" && $arr[$i]!="custom" && $arr[$i]!="convertaslead"){
				if($_POST[$arr[$i]]!=""){$val=$_POST[$arr[$i]];}else{$val='';}
				$this->db->set($arr[$i],$val);
			}
		}
		$this->db->where('callid',$id);
		$this->db->update($bid.'_campaign_report'); 
		$this->auditlog->auditlog_info("Campaign Report ",$id. " Call Details updated by ".$this->session->userdata('username'));
/*
		if($this->input->post('callername')!='' || $this->input->post('caller_email')!=''){
			$data = array(
					'bid'		=>$bid,
					'name'		=>$this->input->post('caller_name'),
					'number'	=>$itemDetail['caller_number'],
					'email'		=>$this->input->post('caller_email')
			);
			$this->configmodel->UpdateContact($data);
		}
*/
		return 1;
	}
	function del_camp_report($id,$bid,$stat){
		$this->db->set('status', $stat);
		$this->db->where('callid',$id);
		$this->db->update($bid."_campaign_report");
		$this->auditlog->auditlog_info("Campaign Report ",$id. " Changed By ".$this->session->userdata('username'));
		return 1;	
	}
	function campcsvreport($bid,$eid=''){
		$res=array();
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q= " WHERE 1 ";
		if($_POST['endtimes']!=""){
			$q.=" AND date(a.call_time)<='".$_POST['endtimes']."'" ;
		}
		if($_POST['starttimes']!=""){
			$q.=" and date(a.call_time)>='".$_POST['starttimes']."'";
		}
/*
		if(!empty($_POST['groupname'])){
			if($_POST['groupname'][0]!=""){
			$q.=" and a.gid in (".implode(",",$_POST['groupname']).")";
			}
		}

		if(!empty($_POST['empname'])){
			if($_POST['empname'][0]!=""){
			$q.=" and a.eid in(".implode(",",$_POST['empname']).")";
			}
		}
		if($roleDetail['role']['admin']!=1){
			$q .= " AND (a.eid='".$this->session->userdata('eid')."' or d.eid='".$this->session->userdata('eid')."')";
		}*/
		$limit = $roleDetail['role']['recordlimit'];
		////////////////////////////////////
		$csv_output = "";
		$ke=array();
		foreach($_POST['lisiting'] as $key=>$val){
			if($key=='custom'){
				foreach($val as $key=>$val){
					$header[]=$val;
					$hkey[]='custom['.$key.']';
				}
			}else{
				$hkey[]=$key;
				$header[]=$val;
			}
		}
		$csv_output .=implode(",",$header)."\n";
		/*$sql="SELECT SQL_CALC_FOUND_ROWS * FROM
			 (SELECT a.callid,a.call_time
			 FROM ".$bid."_campaign_report a
			 LEFT JOIN ".$bid."_employee c on a.eid=c.eid
			 LEFT JOIN ".$bid."_groups d on a.gid=d.gid $q
			 UNION
			 SELECT a.callid,a.call_time,'ca' as type
			 FROM ".$bid."_callarchive a
			 LEFT JOIN ".$bid."_employee c on a.eid=c.eid
			 LEFT JOIN ".$bid."_groups d on a.gid=d.gid $q) a
			 ORDER BY a.call_time DESC
			 limit 0,$limit";	exit;*/
		$sql="SELECT SQL_CALC_FOUND_ROWS * FROM ".$bid."_campaign_report a $q 
				   ORDER BY a.call_time DESC limit 0,$limit";
		$rst = $this->db->query($sql)->result_array();
		$name = $bid.'_'.
				$this->session->userdata('eid').'_'.
				time();
		mkdir('reports/'.$name);
		chmod('reports/'.$name,0777);
		$files = array();
		foreach($rst as $rec){
			$data = array();
			$r = $this->configmodel->getDetail('34',$rec['callid'],'',$bid);
			$i=0;
			foreach($hkey as $k){
				$v=(isset($r[$k])) ? '"'.$r[$k].'"' : '';
				array_push($data,$v);
				if(isset($r[$k]) && $k=="filename" && $r[$k]!=''){
					$path="sounds/".$r[$k];
					if (file_exists($path))	{
						copy($path,'reports/'.$name.'/'.$r[$k]);
					}
				}
			}
			$csv_output .=implode(",",$data)."\n";
		}
		$data_file = 'reports/'.$name.'/calls.csv';
		$fp = fopen($data_file,'w');fwrite($fp,$csv_output);fclose($fp);
		chdir('reports')."<br>";
		exec('zip -r '.$name.'.zip '.$name);
		exec('rm -rf '.$name);
		return $name;
	}
	function convertaslead($campid){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$itemDetail = $this->configmodel->getDetail('34',$campid,'',$bid);
		$leadid = $this->db->query("SELECT COALESCE(MAX(`leadid`),0)+1 as id FROM ".$bid."_leads")->row()->id;
		$this->db->set('leadid',$leadid);
		$this->db->set('bid',$bid);
		$this->db->set('gid',$_POST['groupId']);
		$this->db->set('assignto',$_POST['employeeId']);
		$this->db->set('enteredby',$this->session->userdata('eid'));
		$this->db->set('name',$itemDetail['caller_name']);
		$this->db->set('email',$itemDetail['caller_email']);
		$this->db->set('number',$itemDetail['caller_number']);
		$this->db->set('source','campaign');
		$this->db->set('createdon',date("Y-m-d H:i:s"));
		$this->db->set('status',1);
		$this->db->insert($bid."_leads");
		return "1";
	}
	function getLeadContact_Info($num,$bid){
		$sql=$this->db->query("SELECT leadid FROM ".$bid."_leads  WHERE number='".$num."'");
		if($sql->num_rows()>0){
			return '0';
		}else{
			return '1';
		}
	}
}
