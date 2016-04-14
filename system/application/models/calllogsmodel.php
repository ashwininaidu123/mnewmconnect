<?php
Class callLogsmodel extends Model{
	var $KeyChar;
	function callLogsmodel(){
		parent::Model();
	}
	
	function getcallLogsList($bid,$ofset,$limit,$type=''){
		$q= '';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
			
		}
        $custiom_ids=$this->configmodel->customSearch((isset($s['custom']))?$s['custom']:'','35',$bid);
		$q .= ($type=='del') ? " a.status = 2" : " a.status = 1";
		$q.=(isset($s['call_id']) && $s['call_id']!='')?" AND a.call_id = '".$s['call_id']."'":"";
		$q.=(isset($s['call_type']) && $s['call_type']!='')?" AND a.call_type LIKE '%".$s['call_type']."'":"";
		$q.=(isset($s['number']) && $s['number']!='')?" AND a.number = '".$s['number']."'":"";
		$q.=(isset($s['name']) && $s['name']!='')?" AND a.name LIKE '%".$s['name']."%'":"";
		$q.=(isset($s['email']) && $s['email']!='')?" AND a.email LIKE '%".$s['email']."%'":"";
		$q.=(isset($s['call_time']) && $s['call_time']!='')?" AND date(a.call_time)>= '".$s['call_time']."'":"";
		$q.=(isset($s['duration']) && $s['duration']!='')?" AND a.duration = '".$s['duration']."'":"";
		$q.=(isset($s['recorded_file']) && $s['recorded_file']!='')?" AND a.recorded_file LIKE '%".$s['recorded_file']."%'":"";
		$q.=(isset($s['created_by']) && $s['created_by']!='')?" AND a.created_by = '".$s['created_by']."'":"";
		$q.=(!$custiom_ids)?" AND 0 ":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$sql = "SELECT SQL_CALC_FOUND_ROWS a.call_id
				FROM ".$bid."_call_logs a 
				WHERE $q ORDER BY a.call_time DESC limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='35'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('35',$bid);
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
		if($opt_add || $opt_view || $opt_delete)
			array_push($header,$this->lang->line('level_Action'));
		$ret['header'] = $header;
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$data = array($i);
			$v=($opt_delete) ? '<input type="checkbox" class="blk_check" name="blk[]" value="'.$rec['call_id'].'"/>':'';	
			array_push($data,$v);
			$r = $this->configmodel->getDetail('35',$rec['call_id'],'',$bid);
			foreach($keys as $k){
				if($k=='created_by'){
					$v = '<a href="Employee/activerecords/'.$r['empid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r[$k].'</a>';
				}else if($k=='recorded_file'){
					//$v = '<a href="Employee/activerecords/'.$r['empid'].'" class="callPopup">'.$r[$k].'</a>';
					$v = ($roleDetail['role']['accessrecords']=='0') ? (($r['recorded_file']!='' && file_exists('sounds/'.$r['recorded_file']))
					?'<a target="_blank" href="'.site_url('sounds/'.$r['recorded_file']).'"><span title="Sound" class="fa fa-volume-up"></span></a>'
					//?'<a href="group/player/'.$r['filename'].'" class="callPopup"><img src="'.site_url('system/application/img/icons/sound_high.png').'"></a>'
					:'<span class="glyphicon glyphicon-volume-off"></span>'):"";
				}else{
					$v = isset($r[$k])?$r[$k]:"";	
				}
				array_push($data,$v);
			}
			$act = '';
			if($type == 'del'){
				$act = ($opt_add) ?'<a href="callLogs/undelete_callLog/'.$rec['call_id'].'"><img src="system/application/img/icons/undelete.png" title="Restore" /></a>':'';
			}elseif(($type == 'act' || $type == '') && $opt_add || $opt_view || $opt_delete){
				$act = ($opt_add) ?'<a href="callLogs/callLog_edit/'.$rec['call_id'].'"><span title="Edit" class="fa fa-edit"></span></a>':'';
				$act .= ($opt_delete) ? '<a href="'.base_url().'callLogs/delete_callLog/'.$rec['call_id'].'" class="deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
				$act .= '<a href="callLogs/Details/'.$r['call_id'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="fa fa-file-text"  title="Lead Details"></span></a>';
				$act .= anchor("Report/followup/".$rec['call_id']."/0/callLogs", ' <img src="system/application/img/icons/comments.png" title="Followups" width="16" height="16">',array(' class="btn-danger" data-toggle="modal" data-target="#modal-responsive"'));
				$act .= ($r['email']!='')?"<a href=\"Javascript:void(null)\" onClick=\"window.open('/Email/compose/".$rec['call_id']."/callLogs', 'Counter', 'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=950,height=480,resizable=1')\">&nbsp;<span title='Send Mail' class='fa fa-envelope'></span></a>" : '&nbsp;<span title="Send Mail" class="fa fa-envelope"></span>';
				$act .= anchor("Report/clicktoconnect/".$rec['call_id']."/35", '<span title="click To Connect" class="fa fa-phone"></span>',array('class'=>'clickToConnect'));	
				$act .= anchor("Report/sendSms/".$rec['call_id']."/callLogs", '&nbsp;<span title="Click to send SMS" class="glyphicon glyphicon-comment"></span>','class="clickToSMS" data-toggle="modal" data-target="#modal-empl"');	
			}
			array_push($data,$act);
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function update_callLog($id,$bid){
		$itemDetail = $this->configmodel->getDetail('35',$id,'',$bid);
		$rate=0;
		$val='';
		$arr=array_keys($_POST);
		for($i=0;$i<sizeof($arr);$i++){
			if($arr[$i]!="update_system" && $arr[$i]!="custom" && $arr[$i]!="convertaslead"){
				if($_POST[$arr[$i]]!=""){$val=$_POST[$arr[$i]];}else{$val='';}
				$this->db->set($arr[$i],$val);
			}
		}
		$this->db->where('call_id',$id);
		$this->db->update($bid.'_call_logs'); 
		$this->auditlog->auditlog_info('Call Logs',$id. "Call Details updated by ".$this->session->userdata('username'));
		
		return 1;
	}
	function del_callLog($id,$bid,$stat){
		$this->db->set('status', $stat);
		$this->db->where('call_id',$id);
		$this->db->update($bid."_call_logs");
		$this->auditlog->auditlog_info('Call Log',$id. " Changed By ".$this->session->userdata('username'));
		return 1;	
	}
}
