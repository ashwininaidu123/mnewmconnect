<?php
Class Interviewmodel extends Model
{
	function Interviewmodel(){
		parent::Model();
	}	
	function create_group($id=''){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$this->db->set('group_name',$_POST['group_name']);
		$this->db->set('interviewer',$_POST['eid']);
		$this->db->set('group_desc',$_POST['group_desc']);
		if($id == ''){
			$gid = $this->db->query("SELECT COALESCE(MAX(`gid`),0)+1 as id FROM ".$bid."_intw_groups")->row()->id;
			$this->db->set('gid',$gid);
			$this->db->set('bid',$bid);
			$this->db->set('intw_id',$_POST['intw_id']);
			$this->db->set('status',1);
			$this->db->insert($bid."_intw_groups"); 
		}else{
			$gid = $id;
			$this->db->where('gid',$gid);
			$this->db->update($bid."_intw_groups"); 
		}
		if(isset($_POST['custom'])){
			$arrs=array_keys($_POST['custom']);
			for($k=0;$k<sizeof($arrs);$k++){
				if(is_array($_POST['custom'][$arrs[$k]])){
						$x=implode(",",$_POST['custom'][$arrs[$k]]);
					}else{
						$x=$_POST['custom'][$arrs[$k]];
					}
					$this->db->query("DELETE FROM ".$bid."_customfieldsvalue where bid= '".$bid."' and modid= '41' and fieldid = '".$arrs[$k]."' and dataid= '".$gid."'");
					$sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
						 bid			= '".$bid."'
						,modid			= '41'
						,fieldid		= '".$arrs[$k]."'
						,dataid			= '".$gid."'
						,value			= '".$x."'";
					$this->db->query($sql);
				}
			}
		return $gid;			
	}
	function getgrouplist($bid,$ofset='0',$limit='20',$type=''){
		$q=' ';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$custiom_ids=$this->configmodel->customSearch((isset($s['custom']))?$s['custom']:'','41',$bid);
		$q.=(isset($s['groupname']) && $s['groupname']!='')?" AND g.groupname like '%".$s['groupname']."%'":"";
		$q.=(isset($s['eid']) && $s['eid']!='')?" AND g.interviewer = '".$s['eid']."'":"";
		$q.=(strlen($custiom_ids)>1)?" AND g.gid in(".$custiom_ids.")":"";
		$q.=(!$custiom_ids)?" AND 0 ":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q.=($roleDetail['role']['owngroup']=='1' && $roleDetail['role']['admin']!='1') ? " AND g.interviewer = '".$this->session->userdata('eid')."'":"";
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$q .= ($type == 'del') ? " AND g.status=2" : " AND g.status=1" ;
		$sql="SELECT SQL_CALC_FOUND_ROWS g.gid
			  FROM ".$bid."_intw_groups g 
			  LEFT JOIN ".$bid."_employee e on g.interviewer=e.eid
			  WHERE g.bid='".$bid."' 
			  $q LIMIT $ofset,$limit";	  
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='41'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('41',$bid);
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
			$r = $this->configmodel->getDetail('41',$rec['gid'],'',$bid);
			foreach($keys as $k){
				if($k=='interviewer'){
					$v = '<a href="Employee/activerecords/'.$r['eid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r[$k].'</a>';
				}else{
					$v = isset($r[$k])?$r[$k]:"";
				}
				array_push($data,$v);
			}
			$act = "";
			if($type == ''){
				$act .= ($opt_add) ? '<a href="interview/groupAdd/'.$r['gid'].'"><span title="Edit" class="fa fa-edit"></span></a>':'';
				$act .= ($opt_view) ? '&nbsp;<a href="interview/active_group/'.$r['gid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="fa fa-file-text"  title="View Group"></span></a>':'';
				$act .= ($opt_delete) ? '&nbsp;<a href="'.base_url().'interview/deleteGroup/'.$r['gid'].'" class="deleteClass" title="Are you sure to Delete the group"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
				$act .= ($opt_add) ? '&nbsp;<a href="interview/addQBtoGrp/'.$r['gid'].'"><img src="system/application/img/icons/addtogroup.png" title="Add Question Bank" width="16" height="16" /></a>':'';
				$act .= ($opt_view) ? '&nbsp;<a href="interview/listQBsGrp/'.$r['gid'].'"><span title="List Question Bank" class="glyphicon glyphicon-user"></span></a>':'';
			}else if($type =='del'){
				$act .= ($opt_delete) ? '&nbsp;<a href="interview/undelete/'.$r['gid'].'"><img src="system/application/img/icons/undelete.png" title="Restore" width="16" height="16" /></a>':'';
			}
			array_push($data,$act);
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	} 
	function deleteGroup($id,$bid,$type=''){
		$type = ($type == 1) ? '2' : '1';
		$this->db->set('status', $type);
		$this->db->where('gid',$id);
		$this->db->update($bid."_intw_groups");
		$this->auditlog->auditlog_info('Interview',$id. " Interview Group Updated By ".$this->session->userdata('username'));
		return 1;	
	}
	function listQBsGrp($gid,$ofset='0',$limit='20'){
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
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS g.group_name,qb.name,g.gid,gq.qb_id,gq.status
							   FROM ".$bid."_intw_grp_qb gq
							   LEFT JOIN ".$bid."_intw_groups g on gq.gid=g.gid
							   LEFT JOIN ".$bid."_intw_ques_bank qb on gq.qb_id=qb.qb_id
							   WHERE gq.gid=$gid $q
							   LIMIT $ofset,$limit
							   ")->result_array();
       $res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
	   return $res;
	}
	function delQBtoGrp($id,$gid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("DELETE FROM ".$bid."_intw_grp_qb WHERE qb_id=$id AND gid=$gid");
		return true;
	}
	function disQB($qb_id,$gid,$bid){
		$check=$this->db->query("SELECT * FROM ".$bid."_intw_grp_qb WHERE qb_id=".$qb_id." AND gid=$gid")->row_array();	
		$status=($check['status']==0)?'1':'0';
		$this->db->set('status',$status);
		$this->db->where('qb_id',$qb_id);	
		$this->db->where('gid',$gid);
		$this->db->update($bid.'_intw_grp_qb');
		//$itemDetail= $this->configmodel->getDetail('3',$gid,'',$bid);
		//$empDetail= $this->configmodel->getDetail('2',$eid,'',$bid);
		//$text=($status)?" Enabled":" Disabled";
		//$this->auditlog->auditlog_info('Group Employee', $empDetail['empname'].$text." from the group ".$itemDetail['groupname']);
		return $status;
	}
	function create_qb($id=''){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$this->db->set('name',$_POST['name']);
		$this->db->set('description',$_POST['description']);
		if($id == ''){
			$qbid = $this->db->query("SELECT COALESCE(MAX(`qb_id`),0)+1 as id FROM ".$bid."_intw_ques_bank")->row()->id;
			$this->db->set('qb_id',$qbid);
			$this->db->set('bid',$bid);
			$this->db->set('status',1);
			$this->db->insert($bid."_intw_ques_bank"); 
		}else{
			$qbid = $id;
			$this->db->where('qb_id',$qbid);
			$this->db->update($bid."_intw_ques_bank"); 
		}
		if(isset($_POST['custom'])){
			$arrs=array_keys($_POST['custom']);
			for($k=0;$k<sizeof($arrs);$k++){
				if(is_array($_POST['custom'][$arrs[$k]])){
					$x=implode(",",$_POST['custom'][$arrs[$k]]);
				}else{
					$x=$_POST['custom'][$arrs[$k]];
				}
				$this->db->query("DELETE FROM ".$bid."_customfieldsvalue where bid= '".$bid."' and modid= '42' and fieldid = '".$arrs[$k]."' and dataid= '".$gid."'");
				$sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
					 bid			= '".$bid."'
					,modid			= '42'
					,fieldid		= '".$arrs[$k]."'
					,dataid			= '".$qbid."'
					,value			= '".$x."'";
				$this->db->query($sql);
			}
		}
		return $qbid;			
	}
	function getqblist($bid,$ofset='0',$limit='20',$type=''){
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$custiom_ids=$this->configmodel->customSearch((isset($s['custom']))?$s['custom']:'','42',$bid);
		$q.=(isset($s['name']) && $s['name']!='')?" AND qb.name like '%".$s['name']."%'":"";
		$q.=(isset($s['description']) && $s['description']!='')?" AND qb.description like '%".$s['description']."%'":"";
		$q.=(strlen($custiom_ids)>1)?" AND qb.qb_id in(".$custiom_ids.")":"";
		$q.=(!$custiom_ids)?" AND 0 ":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		//$q.=($roleDetail['role']['owngroup']=='1' && $roleDetail['role']['admin']!='1') ? " AND g.eid = '".$this->session->userdata('eid')."'":"";
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$q .= ($type == 'del') ? " AND qb.status=2" : " AND qb.status=1" ;
		$sql="SELECT SQL_CALC_FOUND_ROWS qb.qb_id
			  FROM ".$bid."_intw_ques_bank qb 
			  WHERE qb.bid='".$bid."' 
			   $q LIMIT $ofset,$limit";	  
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='42'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('42',$bid);
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
			$r = $this->configmodel->getDetail('42',$rec['qb_id'],'',$bid);
			foreach($keys as $k){
				$v = isset($r[$k])?$r[$k]:"";
				array_push($data,$v);
			}
			if($type == ''){
				$act = "";
				$act .= ($opt_add) ? '<a href="interview/addQBank/'.$r['qb_id'].'"><span title="Edit" class="fa fa-edit"></span></a>':'';
				$act .= ($opt_view) ? '&nbsp;<a href="interview/active_group/'.$r['qb_id'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="fa fa-file-text"  title="View Question Bank"></span></a>':'';
				$act .= ($opt_delete) ? '&nbsp;<a href="'.base_url().'interview/deleteqb/'.$r['qb_id'].'" class="deleteClass" title="Are you sure to Delete the Question Bank"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
				$act .= ($opt_add) ? '&nbsp;<a href="interview/addQuestionToQB/'.$r['qb_id'].'"><img src="system/application/img/icons/addtogroup.png" title="Add Questions to QB" /></a>':'';
				$act .= ($opt_add) ? '&nbsp;<a href="interview/listQuestionsToQB/'.$r['qb_id'].'"><span title="List Question Bank to QB" class="glyphicon glyphicon-user"></span></a>':'';
			}else if($type =='del'){
				$act = "";
				$act .= ($opt_delete) ? '&nbsp;<a href="interview/undeleteqb/'.$r['qb_id'].'"><img src="system/application/img/icons/undelete.png" title="Restore" width="16" height="16" /></a>':'';
			}
			array_push($data,$act);
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	} 
	function deleteQB($id,$bid,$type=''){
		$type = ($type == 1) ? '2' : '1';
		$this->db->set('status', $type);
		$this->db->where('qb_id',$id);
		$this->db->update($bid."_intw_ques_bank");
		$this->auditlog->auditlog_info('Interview',$id. " Question Bank Updated By ".$this->session->userdata('username'));
		return 1;	
	} 
	function addQuestion($id=''){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$oldQues=($id != '') ? $this->db->query("SELECT * FROM ".$bid."_intw_questions WHERE qid='$id'")->row() : array();
		$this->db->set('question',$_POST['question']);
		$this->db->set('rel_id',$_POST['pqid']);
		$this->db->set('question_speech',$_POST['question_speech']);
		$this->db->set('answer',$_POST['answer']);
        if($_FILES['question_audio']['error']==0){
			$ext=pathinfo($_FILES['question_audio']['name'],PATHINFO_EXTENSION);
			$newName = "IQ".date('YmdHis').".".$ext;
			move_uploaded_file($_FILES['question_audio']['tmp_name'],$this->config->item('sound_path').$newName);
			$this->db->set('question_audio',$newName);
		}else{
			$this->db->set('question_audio',$oldQues->question_audio);	
		}
		if($id == ''){
			$qid = $this->db->query("SELECT COALESCE(MAX(`qid`),0)+1 as id FROM ".$bid."_intw_questions")->row()->id;
			$this->db->set('qid',$qid);
			$this->db->set('bid',$bid);
			$this->db->set('status',1);
			$this->db->insert($bid."_intw_questions"); 
		}else{
			$qid = $id;
			$this->db->where('qid',$qid);
			$this->db->update($bid."_intw_questions"); 
		}
		if(isset($_POST['custom'])){
			$arrs=array_keys($_POST['custom']);
			for($k=0;$k<sizeof($arrs);$k++){
				if(is_array($_POST['custom'][$arrs[$k]])){
					$x=implode(",",$_POST['custom'][$arrs[$k]]);
				}else{
					$x=$_POST['custom'][$arrs[$k]];
				}
				$this->db->query("DELETE FROM ".$bid."_customfieldsvalue where bid= '".$bid."' and modid= '43' and fieldid = '".$arrs[$k]."' and dataid= '".$gid."'");
				$sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
					 bid			= '".$bid."'
					,modid			= '43'
					,fieldid		= '".$arrs[$k]."'
					,dataid			= '".$qid."'
					,value			= '".$x."'";
				$this->db->query($sql);
			}
		}
		return $qid;			
	}
	function listQuestions($bid,$ofset='0',$limit='20',$type=''){
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$custiom_ids=$this->configmodel->customSearch((isset($s['custom']))?$s['custom']:'','43',$bid);
		$q.=(isset($s['question']) && $s['question']!='')?" AND q.question LIKE '%".$s['question']."%'":"";
		$q.=(isset($s['rel_id']) && $s['rel_id']!='')?" AND q.rel_id = '".$s['rel_id']."'":"";
		$q.=(strlen($custiom_ids)>1)?" AND q.qid in(".$custiom_ids.")":"";
		$q.=(!$custiom_ids)?" AND 0 ":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q.=($roleDetail['role']['owngroup']=='1' && $roleDetail['role']['admin']!='1') ? " AND g.eid = '".$this->session->userdata('eid')."'":"";
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$q .= ($type == 'del') ? "q.status=2" : "q.status=1" ;
		$sql="SELECT SQL_CALC_FOUND_ROWS q.qid 
			  FROM ".$bid."_intw_questions q 
			  WHERE q.bid='".$bid."' 
			  AND $q LIMIT $ofset,$limit";	  
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='43'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('43',$bid);
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
			$r = $this->configmodel->getDetail('43',$rec['qid'],'',$bid);
			foreach($keys as $k){
				if($k=='rel_id' && $r['rel_id'] !=0){
					$v = '<a href="interview/active_question/'.$r['rel_id'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r['parent'].'</a>';
				}elseif($k == 'question_audio'){
					$v = ($r[$k] != '') ? ((file_exists('sounds/'.$r[$k])) ? '<a target="_blank" href="'.site_url('sounds/'.$r[$k]).'"><span title="Sound" class="fa fa-volume-up"></span></a>' : '') : '';
				}else{
					$v = isset($r[$k])?$r[$k]:"";
				}
				array_push($data,$v);
			}
			if($type == ''){
				$act = "";
				$act .= ($opt_add) ? '<a href="interview/addQuestion/'.$r['qid'].'"><span title="Edit" class="fa fa-edit"></span></a>':'';
				$act .= ($opt_view) ? '&nbsp;<a href="interview/activeQuestion/'.$r['qid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="fa fa-file-text"  title="View Question"></span></a>':'';
				$act .= ($opt_delete) ? '&nbsp;<a href="'.base_url().'interview/delQuestion/'.$r['qid'].'" class="deleteClass" title="Are you sure to Delete the Question"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
			}else if($type =='del'){
				$act = "";
				$act .= ($opt_delete) ? '&nbsp;<a href="interview/undelQuestion/'.$r['qid'].'"><img src="system/application/img/icons/undelete.png" title="Restore" width="16" height="16" /></a>':'';
			}
			array_push($data,$act);
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	} 
	function delQuestion($id,$bid,$type=''){
		$type = ($type == 1) ? '2' : '1';
		$this->db->set('status', $type);
		$this->db->where('qid',$id);
		$this->db->update($bid."_intw_questions");
		$this->auditlog->auditlog_info('Interview',$id. " Questions Updated By ".$this->session->userdata('username'));
		return 1;	
	}
	function queslist(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array(" "=>"Select Question");
		$query=$this->db->query("SELECT qid,question FROM ".$bid."_intw_questions WHERE status=1");
		if($query->num_rows()>0){
			foreach($query->result_array() as $rt)
			$res[$rt['qid']]=$rt['question'];
		}
		return $res;
	}
	function getQBs($gid){
		$options=array();$options1=array();
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res1=$this->db->query("SELECT qb_id FROM ".$bid."_intw_grp_qb WHERE gid='".$gid."'")->result_array();
		foreach($res1 as $r){
			$options1[]=$r['qb_id'];
		}
		$res=$this->db->query("SELECT qb_id,name FROM ".$bid."_intw_ques_bank")->result_array();
		foreach($res as $r){
			if(!@in_array($r['qb_id'],$options1))
				$options[$r['qb_id']]=$r['name'];
		}
		return $options;
	}
	function selQBNames($ids){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$options=array();
		$res=$this->db->query("SELECT qb_id,name FROM ".$bid."_intw_ques_bank WHERE qb_id IN(".$ids.")")->result_array();
		foreach($res as $r){
			$options[$r['qb_id']]=$r['name'];
		}
		return $options;
	}
	function addQBtoGrp($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$err=0;
		foreach($_POST['selQBs'] as $qbids){
			$err++;
			$refid = $this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM ".$bid."_intw_grp_qb")->row()->id;
			$this->db->set('id', $refid);  
			$this->db->set('bid', $bid);                       
			$this->db->set('gid', $id);                       
			$this->db->set('qb_id', $qbids);                      
			$this->db->set('status',1);
			$this->db->insert($bid."_intw_grp_qb");
		}
		if($err!=0){
			return 1;
		}else{
			return 0;
		}
	}
	function getQuestions($qb_id){
		$options=array();$options1=array();
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res1=$this->db->query("SELECT qid FROM ".$bid."_intw_qb_ques WHERE qb_id='".$qb_id."'")->result_array();
		foreach($res1 as $r){
			$options1[]=$r['qid'];
		}
		$res=$this->db->query("SELECT qid,question FROM ".$bid."_intw_questions")->result_array();
		foreach($res as $r){
			if(!@in_array($r['qid'],$options1))
				$options[$r['qid']]=$r['question'];
		}
		return $options;
	}
	function addQuestionToQB($qb_id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$err=0;
		foreach($_POST['selQues'] as $qids){
			$err++;
			$refid = $this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM ".$bid."_intw_qb_ques")->row()->id;
			$this->db->set('id', $refid);
			$this->db->set('bid', $bid);                       
			$this->db->set('qb_id', $qb_id);                       
			$this->db->set('qid', $qids);                      
			$this->db->set('status',1);
			$this->db->insert($bid."_intw_qb_ques");
		}
		if($err!=0){
			return 1;
		}else{
			return 0;
		}
	}
	function listQuestionsToQB($qb_id,$ofset='0',$limit='20'){
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
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS qb.name,q.question,qq.qb_id,qq.qid,qq.status
							   FROM ".$bid."_intw_qb_ques qq
							   LEFT JOIN ".$bid."_intw_ques_bank qb on qb.qb_id=qq.qb_id
							   LEFT JOIN ".$bid."_intw_questions q on qq.qid=q.qid
							   WHERE qq.qb_id=$qb_id $q
							   LIMIT $ofset,$limit
							   ")->result_array();
       $res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
	   return $res;
	}
	function delQuestoQB($qb_id,$qid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("DELETE FROM ".$bid."_intw_qb_ques WHERE qid=$qid AND qb_id=$qb_id");
		return true;
	}
	function disQuestion($qb_id,$qid,$bid){
		$check=$this->db->query("SELECT * FROM ".$bid."_intw_qb_ques WHERE qid=".$qid." AND qb_id=$qb_id")->row_array();	
		$status=($check['status']==0)?'1':'0';
		$this->db->set('status',$status);
		$this->db->where('qid',$qid);	
		$this->db->where('qb_id',$qb_id);
		$this->db->update($bid.'_intw_qb_ques');
		//$itemDetail= $this->configmodel->getDetail('3',$gid,'',$bid);
		//$empDetail= $this->configmodel->getDetail('2',$eid,'',$bid);
		//$text=($status)?" Enabled":" Disabled";
		//$this->auditlog->auditlog_info('Group Employee', $empDetail['empname'].$text." from the group ".$itemDetail['groupname']);
		return $status;
	}
}
?>
