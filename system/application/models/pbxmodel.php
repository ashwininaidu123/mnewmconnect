<?php
class Pbxmodel extends Model {
	function Pbxmodel(){
        parent::Model();
        $this->load->model('ivrsmodel');
        $this->load->model('auditlog');
        $this->load->model('groupmodel');
    }
	function addpbx(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$arr=array_keys($_POST);
		for($i=0;$i<sizeof($arr);$i++){
			if(!in_array($arr[$i],array("update_system","pbxid","bid","title",
			"operator","remark","record","prinumber","hdaytext","noext","bday"))){
				/* Changed for custom fields */
				if(is_array($_POST[$arr[$i]]))
					$val = @implode(',',$_POST[$arr[$i]]);
				elseif($_POST[$arr[$i]]!="")
					$val=$_POST[$arr[$i]];
				else
					$val='';
				$this->db->set($arr[$i],$val);
			}
		} 
		$pbxid = (isset($_POST['pbxid']) && $_POST['pbxid']!='' && $_POST['pbxid']!='0')
					? $_POST['pbxid']
					:$this->db->query("SELECT COALESCE(MAX(`pbxid`),0)+1 as id FROM `".$bid."_pbx`")->row()->id;
		$this->db->set('pbxid',$pbxid);
		$this->db->set('bid',$bid);
		$_POST['prinumber'] = ($_POST['prinumber']!='0') ? $_POST['prinumber']:$this->ivrsmodel->getFreePri();
		if($_FILES['hdayaudio']['error']==0){
			$ext=pathinfo($_FILES['hdayaudio']['name'],PATHINFO_EXTENSION);
			$newName = "H".date('YmdHis').".".$ext;
			move_uploaded_file($_FILES['hdayaudio']['tmp_name'],$this->config->item('sound_path').$newName);
			$this->db->set('hdayaudio',$newName);
		}
		if($_FILES['greetings']['error']==0){
			$ext=pathinfo($_FILES['greetings']['name'],PATHINFO_EXTENSION);
			$newName = "G".date('YmdHis').".".$ext;
			move_uploaded_file($_FILES['greetings']['tmp_name'],$this->config->item('sound_path').$newName);
			$this->db->set('greetings',$newName);
		}
		for($i=0;$i<sizeof($arr);$i++){
			if($arr[$i]!="update_system"){
				if($arr[$i]!="custom"){
					$this->db->set($arr[$i], $_POST[$arr[$i]]);
					if(isset($_POST['bday']))$this->db->set('bday', json_encode($_POST['bday']));
				}
			}
		}
		if(!isset($_POST['noext'])) $this->db->set('noext', '0');
		if(!isset($_POST['record'])) $this->db->set('record', '0');
		$this->db->set('status','1');
		if(isset($_POST['pbxid']) && $_POST['pbxid']!='' && $_POST['pbxid']!='0'){
			if(isset($_POST['prinumber']))$this->ivrsmodel->updatePri($this->db->query("SELECT prinumber FROM ".$bid."_pbx WHERE pbxid = '".$pbxid."'")->row()->prinumber);
			$this->db->where('pbxid',$pbxid);
			$this->db->update($bid."_pbx"); 
			$this->auditlog->auditlog_info('PBX',$this->input->post('title')." PBX Group Updated");
		}else{
			$this->db->insert($bid."_pbx");
			$this->auditlog->auditlog_info('PBX',"Created New PBX Group  ".$this->input->post('title'));
		}
		$this->ivrsmodel->updatePri($_POST['prinumber'],1,$bid,2,$pbxid);
		return;
	}
	function getPBXlist($bid,$ofset='0',$limit='20'){
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$con = "";
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		$s = ($this->session->userdata('search'))?$this->session->userdata('search'):'';
		$con .= (isset($s['title']) && $s['title']!='')?" AND i.title like '%".$s['title']."%'":"";
		$con .= (isset($s['prinumber']) && $s['prinumber']!='')?" AND p.landingnumber like '%".$s['prinumber']."%'":"";
		$con .= (isset($s['operator']) && $s['operator']!='' && $s['operator']!='0')?" AND i.operator ='".$s['operator']."'":"";
		$sql = "SELECT SQL_CALC_FOUND_ROWS pbxid FROM ".$bid."_pbx i
				LEFT JOIN prinumber p on i.prinumber=p.number
				WHERE i.status='1' AND i.bid='".$bid."' ".$con."
				ORDER BY i.pbxid DESC
				LIMIT $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		$opt_add 	= $roleDetail['modules']['20']['opt_add'];
		$opt_view 	= $roleDetail['modules']['20']['opt_view'];
		$opt_delete = $roleDetail['modules']['20']['opt_delete'];
		$fieldset = $this->configmodel->getFields('20',$bid);
		$keys = array();
	    $header = array('#',"<a href='javascript://'><span id='c_all' class='glyphicon glyphicon-gok'></span></a>");
	    if($opt_add || $opt_view || $opt_delete)
			array_push($header,$this->lang->line('level_Action'));
			array_push($header,'Weekly Report');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked && !in_array($field['fieldname'],array('greetings','hdayaudio'))){
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
		
		$ret['header'] = $header;
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$data['sl'] = $i;
			$r = $this->configmodel->getDetail('20',$rec['pbxid'],'',$bid);
			$data = array($i);
			$v = '<input type="checkbox" class="blk_check" name="blk[]" value="'.$rec['pbxid'].'"/>';
			array_push($data,$v);	
			if($opt_add || $opt_view || $opt_delete){
				$act = "";
				$act .= ($opt_add)? ' <a href="PBXadd/'.$r['pbxid'].'"><span title="Edit" class="fa fa-edit"></span></a>':'';
				$act .= ($opt_delete)? ' <a href="pbx/delete/'.$r['pbxid'].'" class="confirm deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
				$act .= ($opt_add)? ' <a href="pbx/addext/'.$r['pbxid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="fa fa-plus" title="Add Extension" ></span></a>':'';
				$act .= ($opt_add)? ' <a href="Listextn/'.$r['pbxid'].'"><span title="List Option" class="fa fa-list-ul"></span></a>':'';
			
			}
			$data['act']=$act;
			$ret['graph'] = $this->recent_calls($rec['pbxid']);
			$data['graph'] = $ret['graph'];
			foreach($keys as $k){
				if(in_array($k ,array(  'record',
										'noext'))){
					$v = (isset($r[$k]) && $r[$k]=='1')?"Yes":"No";
				}else{
					$v = isset($r[$k])?nl2br(wordwrap($r[$k],80,"\n")):"";
				}
				$data[$k]=$v;
			}
			$data['prinumber'] = $r['landingnumber'];
			$data['operator'] = $r['empname'];
			$bday = json_decode($r['bday']);
			$v = '';
			foreach($bday as $b => $d){ $v .= (isset($d->day) && $d->day=='1')?$b.'='.$d->st.'-'.$d->et.'<br>':'';}
			$data['bday']=$v;

			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
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
	function getPBXExtlist($pbxid,$ofset='0',$limit='20'){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$con = "";
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		$s = ($this->session->userdata('search'))?$this->session->userdata('search'):'';
		$con .= (isset($s['ext']) && $s['ext']!='')?" AND i.ext = '".$s['ext']."'":"";
		$sql = "SELECT SQL_CALC_FOUND_ROWS i.*,
				IF(i.targettype='employee',e.empname,
				IF(i.targettype='group',g.groupname,
				IF(i.targettype='ivrs',iv.title,
				IF(i.targettype='pbx',p.title,'NA')))) as title
				FROM ".$bid."_pbxext i
				LEFT JOIN ".$bid."_employee e on (e.eid=i.targetid AND i.targettype='employee')
				LEFT JOIN ".$bid."_groups g on (g.gid=i.targetid AND i.targettype='group')
				LEFT JOIN ".$bid."_pbx p on (p.pbxid=i.targetid AND i.targettype='pbx')
				LEFT JOIN ".$bid."_ivrs iv on (iv.ivrsid=i.targetid AND i.targettype='ivrs')
				WHERE i.pbxid='".$pbxid."' ".$con."
				ORDER BY i.extid DESC
				limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		$opt_add 	= $roleDetail['modules']['20']['opt_add'];
		$opt_view 	= $roleDetail['modules']['20']['opt_view'];
		$opt_delete = $roleDetail['modules']['20']['opt_delete'];
		$header =  array('#'
						,$this->lang->line('label_pbxext')
						,$this->lang->line('label_pbxtargettype')
						,$this->lang->line('label_pbxtarget')
						,'PBX Operator'
						,$this->lang->line('level_Action')
						);
		$ret['header'] = $header;
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$opt='';
			$data['sl']		= $i;
			$data['ext']	= $rec['ext'];
			$data['targettype']	= ucfirst($rec['targettype']);
			$data['targetid']	= $rec['title'];
			$data['operator']=($rec['operator']!='1')?'No':'Yes';
			$opt.= ($opt_add) ? '<a href="pbx/addext/'.$rec['pbxid'].'/'.$rec['extid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Edit" class="fa fa-edit"></span></a>':'';
			$opt.=($opt_delete)?'&nbsp;<a href="pbx/delext/'.$rec['extid'].'" class="confirm deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>' :'';
			$opt.=($rec['operator']==1)?'&nbsp;<a href="pbx/AddasOperator/'.$rec['extid'].'/'.$pbxid.'"><span class="glyphicon glyphicon-remove"></span></a>':'&nbsp;<a href="pbx/AddasOperator/'.$rec['extid'].'/'.$pbxid.'"><span class="fa fa-check"></span></a>';
			$data['act']=$opt;
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function getPBXExtlist1($pbxid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$con = "";
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		$s = ($this->session->userdata('search'))?$this->session->userdata('search'):'';
		$con .= (isset($s['ext']) && $s['ext']!='')?" AND i.ext = '".$s['ext']."'":"";
		$sql = "SELECT SQL_CALC_FOUND_ROWS i.*,e.empname,g.groupname,iv.title FROM ".$bid."_pbxext i
				LEFT JOIN ".$bid."_employee e on e.eid=i.targetid
				LEFT JOIN ".$bid."_groups g on g.gid=i.targetid
				LEFT JOIN ".$bid."_ivrs iv on iv.ivrsid=i.targetid
				WHERE i.pbxid='".$pbxid."' ".$con."
				ORDER BY i.extid DESC";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		$opt_add 	= $roleDetail['modules']['20']['opt_add'];
		$opt_view 	= $roleDetail['modules']['20']['opt_view'];
		$opt_delete = $roleDetail['modules']['20']['opt_delete'];
		$header =  array('#'
						,$this->lang->line('label_pbxext')
						,$this->lang->line('label_pbxtargettype')
						,$this->lang->line('label_pbxtarget')
						);
		$ret['header'] = $header;
		$list = array();
		$i = 1;
		foreach($rst as $rec){
			$data['sl']		= $i;
			$data['ext']	= $rec['ext'];
			$data['targettype']	= $rec['targettype'];
			$data['targetid']	= ($data['targettype']=='employee')?$rec['empname']
								  :(($data['targettype']=='group')?$rec['groupname']
								  :(($data['targettype']=='ivrs')?$rec['title']:''));
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function getDeletedPBXlist($bid,$ofset='0',$limit='20'){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$con = "";
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		$s = ($this->session->userdata('search'))?$this->session->userdata('search'):'';
		$con .= (isset($s['title']) && $s['title']!='')?" AND i.title like '%".$s['title']."%'":"";
		$con .= (isset($s['prinumber']) && $s['prinumber']!='')?" AND p.landingnumber like '%".$s['prinumber']."%'":"";
		$con .= (isset($s['operator']) && $s['operator']!='')?" AND i.operator ='".$s['operator']."'":"";
		$sql = "SELECT SQL_CALC_FOUND_ROWS pbxid FROM ".$bid."_pbx i
				LEFT JOIN prinumber p on i.prinumber=p.number
				WHERE i.status='2' AND i.bid='".$bid."' ".$con."
				ORDER BY i.pbxid DESC
				limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		$opt_add 	= $roleDetail['modules']['20']['opt_add'];
		$opt_view 	= $roleDetail['modules']['20']['opt_view'];
		$opt_delete = $roleDetail['modules']['20']['opt_delete'];
		$fieldset = $this->configmodel->getFields('20',$bid);
		$keys = array();
		$header = array('#');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked && !in_array($field['fieldname'],array('greetings','hdayaudio'))){
					array_push($keys,$field['fieldname']);
					if($field['fieldname']!='filename')array_push($header,(($field['customlabel']!="")
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
			$data['sl'] = $i;
			$r = $this->configmodel->getDetail('20',$rec['pbxid'],'',$bid);
			foreach($keys as $k){
				$v = isset($r[$k])?nl2br(wordwrap($r[$k],80,"\n")):"";
				$data[$k]=$v;
			}
			$data['prinumber'] = $r['landingnumber'];
			$data['operator'] = $r['empname'];
			$bday = json_decode($r['bday']);
			$v = '';
			foreach($bday as $b => $d){ $v .= (isset($d->day) && $d->day=='1')?$b.'='.$d->st.'-'.$d->et.'<br>':'';}
			$data['bday']=$v;
			$data['act'] = ' <a href="pbx/undelete/'.$r['pbxid'].'" class="deleteClass" id="'.$r['pbxid'].'"><img src="system/application/img/icons/undelete.png" title="Undelete" /></a>';
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function getExtDetail($extid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql = "SELECT * FROM ".$bid."_pbxext WHERE extid='".$extid."'";
		return $this->db->query($sql)->row();
	}
	function addpbxExt(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$cnt = $this->db->query("SELECT count(*) cnt FROM ".$bid."_pbxext 
				WHERE pbxid='".$_POST['pbxid']."' AND ext='".$_POST['ext']."'")->row()->cnt;
		if($cnt>0){
			$this->session->set_flashdata(array('msgt' => 'error','msg' => 'Extension already exist'));
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$extid=(isset($_POST['extid']) && $_POST['extid']!='')
					?$_POST['extid']
					:$this->db->query("SELECT COALESCE(MAX(`extid`),0)+1 as id FROM `".$bid."_pbxext`")->row()->id;
			$this->db->set('extid',$extid);
			$this->db->set('bid',$bid);
			$this->db->set('pbxid',$_POST['pbxid']);
			$this->db->set('ext',$_POST['ext']);
			$this->db->set('targettype',$_POST['targettype']);
			$tid = (isset($_POST['targetid']) && $_POST['targetid']!='') ? $_POST['targetid'] : '0';
			$this->db->set('targetid',$tid);
			$pbxName=$this->db->query("SELECT title  FROM ".$bid."_pbx where pbxid=".$_POST['pbxid'])->row()->title;
			if(isset($_POST['extid']) && $_POST['extid']!=''){
				$this->db->where('extid',$extid);
				$this->db->update($bid."_pbxext"); 
				$this->auditlog->auditlog_info('PBX',"PBX Extension ".$_POST['ext']." updated to the PBX Group ".$pbxName);
			}else{
				$this->db->insert($bid."_pbxext");
				$this->auditlog->auditlog_info('PBX',"PBX Extension ".$_POST['ext']." added for the PBX Group ".$pbxName);
			}
			if($_POST['targettype']=="employee"){
				$this->db->set('extension',$_POST['ext']);
				$this->db->where('eid',$_POST['targetid']);
				$this->db->update($bid."_employee");
				$empName=$this->groupmodel->get_empname($_POST['targetid']);
				$this->auditlog->auditlog_info('Employee',"Extension ".$_POST['ext']." updated to the Employee ".$empName->empname);
			}
		}
		return;
	}
	function deletePBX($pbxid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$detail = $this->configmodel->getDetail('20',$pbxid,'',$bid);
		$this->ivrsmodel->updatePri($detail['prinumber']);
		$this->db->query("UPDATE ".$bid."_pbx SET
						  status='2'
						  WHERE pbxid = '".$pbxid."' 
						  AND bid='".$bid."'");
		$this->auditlog->auditlog_info('Virtual PBX',$detail['title']." Is deleted ");				  
	}
	function deletePBXExt($extid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$detail = (array)$this->getExtDetail($extid);
		$this->db->query("DELETE FROM ".$bid."_pbxext
						  WHERE extid = '".$extid."'");
		$this->auditlog->auditlog_info('Virtual PBX Extension',$detail['ext']." Is deleted ");
	}
	function undeletePBX($pbxid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$detail = $this->configmodel->getDetail('20',$pbxid,'',$bid);
		$pri = $this->db->query("SELECT * FROM prinumber WHERE number='".$detail['prinumber']."' AND bid='".$bid."' AND status='0'");
		if($pri->num_rows()>0){
			$this->db->query("UPDATE ".$bid."_pbx SET
						  status='1'
						  WHERE pbxid = '".$pbxid."' 
						  AND bid='".$bid."'");
			$this->ivrsmodel->updatePri($detail['prinumber'],'1',$bid,'2',$pbxid);
			$this->auditlog->auditlog_info('Virtual PBX',$detail['title']." Is undeleted ");
		}else{
			$this->session->set_flashdata(array('msgt' => 'error','msg' => $this->lang->line('priinuse')));
		}
	}
	function pbx_csvreport(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();
		$con='';
		if($this->session->userdata('ivrstitle')!=""){
			$ivrstitle=$this->session->userdata('ivrstitle');
			if($con!=""){
				$con.=" AND i.title LIKE '%".$ivrstitle."%'";
			}
		}
		if($this->session->userdata('ivrsdatefrom')!=""){
			$ivrsdatefrom=$this->session->userdata('ivrsdatefrom');
			if($con!=""){
				$con.=" AND date(h.datetime)>='$ivrsdatefrom'";
			}
		}
		if($this->session->userdata('ivrsdateto')!=""){
			$ivrsdateto=$this->session->userdata('ivrsdateto');
			if($con!=""){
				$con.=" AND date(h.datetime)<='$ivrsdateto'";
			}
		}
		$res=$this->db->query("SELECT SQL_CALC_FOUND_ROWS hid FROM ".$bid."_ivrshistory h
				LEFT JOIN ".$bid."_ivrs i on i.ivrsid=h.ivrsid
				WHERE h.bid='".$bid."' ".$con."
				ORDER BY h.ivrsid DESC")->result_array();
		return $res;		
	}

	function getPBXReportlist($bid,$ofset='0',$limit='20',$tab){
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
		if(isset($_POST['Adv_submit'])){
			$this->session->set_userdata('Adsearch',$_POST);
		}else{
			$this->session->unset_userdata('Adsearch');
		}
		if(isset($_POST['sav_search'])){
			Save_search_data($bid,$this->session->userdata('eid'),json_encode($_POST),'24');
			$this->session->set_userdata('Adsearch',$_POST);
		}
		if($tab>0){
			$qs=get_save_searchrow($bid,'24',$this->session->userdata('eid'),$tab);
			$content=(array)json_decode($qs['content']);
			$this->session->set_userdata('Adsearch',$content);
		}
		if($this->session->userdata('Adsearch')){
			$Ads = $this->session->userdata('Adsearch');
		}
		if(isset($Ads) && sizeof($Ads)>0){
			switch($Ads['timespan']){
				case 'all':
				default :
						 $con.='';	
				break;
				case 'today':
						 $con.=" and date(r.starttime)>= '".date('Y-m-d')."'";
				break;
				case 'last7':
						$date=date('Y-m-d',strtotime('-6 days'));
						$con.=" and date(r.starttime)>= '".$date."'";	
				break;			
				case 'month':
						$date=date('Y-m-01');
						$con.=" and date(r.starttime)>= '".$date."'";	
				break;				
			}
			if(isset($Ads['multiselect_gid']) && sizeof($Ads['multiselect_gid'])>0){
				$gids=implode(",",$Ads['multiselect_gid']);
				$con.=" and r.pbxid in (".$gids.")";
			}
			if(isset($Ads['multiselect_eids']) && sizeof($Ads['multiselect_eids'])>0){
				$eids=implode(",",$Ads['multiselect_eids']);
				$con.=" and r.eid in (".$eids.")";
			}
			$cust=0;
			$field_array=array("pbxtitle"=>"p.title","callfrom"=>"r.callfrom","starttime"=>"date(r.starttime)","endtime"=>"date(r.endtime)","name"=>"r.name","email"=>"r.email","pulse"=>"r.pulse","eid"=>"r.eid");
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
				$con .= (isset($field_array[$Ads['field_d'][$i]])) ? " ".$Ads['cond'][$i]."  ".$field_array[$Ads['field_d'][$i]]."  ".$app : '';	
			}
		}
		if(isset($s)){
            $arr = array_keys($s);
            for ($n =0;$n<count($arr);$n++){
                if(strstr($arr[$n],'c_')){
					if(is_array($s[$arr[$n]])){
						$s[$arr[$n]] = @implode(',',$s[$arr[$n]]);
					}
                    $con.=(isset($s[$arr[$n]]) && ($s[$arr[$n]]!='')  && ($s[$arr[$n]]!=' ') && ($s[$arr[$n]]!='0')) ? " AND r.".$arr[$n]." LIKE '%".$s[$arr[$n]]."%'":"";
                }
            }
        }
		$s = ($this->session->userdata('search'))?$this->session->userdata('search'):'';
		$con .= (isset($s['pbxtitle']) && $s['pbxtitle']!='')?" AND p.title like '%".$s['pbxtitle']."%'":"";
		$con .= (isset($s['callfrom']) && $s['callfrom']!='')?" AND r.callfrom like '%".$s['callfrom']."%'":"";
		$con.=(isset($s['pulse']) && $s['pulse']!='')?" and if(r.pulse>0,ceil(r.pulse/60),r.pulse) ".$s['ptype']." '".$s['pulse']."'":"";	
		$con .= (isset($s['name']) && $s['name']!='')?" AND r.name like '%".$s['name']."%'":"";
		$con .= (isset($s['email']) && $s['email']!='')?" AND r.email like '%".$s['email']."%'":"";
		$con.=(isset($s['starttime']) && $s['starttime']!='')?" and date(r.starttime)>= '".$s['starttime']."'":"";
		$con.=(isset($s['endtime']) && $s['endtime']!='')?" and date(r.endtime)<= '".$s['endtime']."'":"";
		$sql = "SELECT SQL_CALC_FOUND_ROWS p.title,r.* FROM ".$bid."_pbxreport r
				LEFT JOIN ".$bid."_pbx p on r.pbxid=p.pbxid
				WHERE 1 ".$con."
				ORDER BY r.starttime DESC
				limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		$opt_add 	= $roleDetail['modules']['20']['opt_add'];
		$opt_view 	= $roleDetail['modules']['20']['opt_view'];
		$opt_delete = $roleDetail['modules']['20']['opt_delete'];
		$rst = $this->db->query($sql)->result_array();
		$fieldset = $this->configmodel->getFields('24',$bid);
		$keys = array();
		$header = array('#',"<a href='javascript://'><span id='c_all' class='glyphicon glyphicon-gok'></span></a>");
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
						array_push($keys,$field['fieldKey']);
					array_push($header,$field['customlabel']);
				}
			}
		}
		array_push($keys,'Action');
		array_push($header,'Action');
		$ret['header'] = $header;
		$target = array(
			'employee' => 'Employee/activerecords/'
			,'group' => 'group/activerecords/'
			,'pbx' => 'pbx/detail/'
			,'ivrs' => ''
			,'voicemgs' => ''
		);
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$extList = array();
			$data = array($i);
			$r = $this->configmodel->getDetail('24',$rec['callid'],'',$bid);
			$extSql = "SELECT * FROM ".$bid."_pbxext WHERE pbxid='".$rec['pbxid']."'";
			$extListO = $this->db->query($extSql)->result_array();
			foreach($extListO as $ext) {$extList[$ext['ext']] = $ext;}
			$v='<input type="checkbox" class="blk_check" name="blk[]" value="'.$rec['callid'].'"/>';	
			array_push($data,$v);
			foreach($keys as $k){
				if($k=='pbxtitle'){
					$v = '<a href="pbx/detail/'.$rec['pbxid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r[$k].'</a>';
				}elseif($k=='Action'){
					$v = ($opt_add) ? '<a href="PBXEditReport/'.$rec['callid'].'"><span title="Edit" class="fa fa-edit"></span></a>': '';
					$v .= 				
					($roleDetail['role']['accessrecords']=='0') ? (($rec['filename']!='' && file_exists('sounds/'.$rec['filename']))
					?' <a target="_blank" href="'.site_url('sounds/'.$rec['filename']).'"><span title="Sound" class="fa fa-volume-up"></span></a>'
					:'<span class="glyphicon glyphicon-volume-off"></span> '):'';
					$v .= anchor("Report/clicktoconnect/".$rec['callid']."/3", '<span title="click To Connect" class="fa fa-phone"></span>',array('class'=>'clickToConnect'));
					$v .= ($r['email']!='')?"<a href=\"Javascript:void(null)\" onClick=\"window.open('/Email/compose/".$rec['callid']."/pbx', 'Sent Email', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=950,height=480,resizable=1')\">&nbsp;<span title='Send Mail' class='fa fa-envelope'></span></a>":'&nbsp;<span title="Send Mail" class="fa fa-envelope"></span>';
					$v .= '<a href="Report/followup/'.$rec['callid'].'/0/pbx" class="btn-followup" data-toggle="modal" data-target="#modal-followup"><img src="system/application/img/icons/comments.png" title="Followups" width="16" height="16" /></a>';
					$v .= anchor("Report/sendSms/".$rec['callid']."/pbx", '&nbsp;<span title="Click to send SMS" class="glyphicon glyphicon-comment"></span>','class="clickToSMS" data-toggle="modal" data-target="#modal-empl"');	
				    $v .= "<a href=\"Javascript:void(null)\" onClick=\"window.open('Report/sendFields/".$rec['callid']."/".$bid."/24/pbx', 'Send Fields', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=550,height=500,left=200,top=20,resizable=1')\">&nbsp;<span title='Click to Send Fields' class='fa fa-list-alt'></span></a>";
				}elseif($k=="extensions"){
					$exts=isset($r[$k])?explode(",",$r[$k]):array();
					$v = "";
					if(sizeof($exts)>0){
						foreach($exts as $ext){
							$v .= (array_key_exists($ext,$extList) && !in_array($ext,array('operator','voicemgs'))) ?'<a  class="btn-danger" data-toggle="modal" data-target="#modal-responsive" href="'.$target[$extList[$ext]['targettype']].$extList[$ext]['targetid'].'">'.$ext.'</a> , ': $ext . ', ';
						}
					}
				}else{
					$v = isset($r[$k])?nl2br(wordwrap($r[$k],80,"\n")):"";
				}
				array_push($data,$v);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function getPrintReport($bid){
		$csv_output='';
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
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
		$sql = "SELECT r.callid,p.title FROM ".$bid."_pbxreport r
				LEFT JOIN ".$bid."_pbx p on r.pbxid=p.pbxid
				WHERE 1 ";
		$sql .= (isset($_POST['stime']) && $_POST['stime']!='') ? " AND date(r.starttime)>='".$_POST['stime']."' " : "";
		$sql .= (isset($_POST['etime']) && $_POST['etime']!='') ? " AND date(r.endtime)<='".$_POST['etime']."' " : "";
		if(!empty($_POST['pbxid'])){
			if($_POST['pbxid'][0]!=""){
			$sql .= " AND p.pbxid IN (".implode(",",$_POST['pbxid']).")";
			}
		}
		$rst = $this->db->query($sql)->result_array();
		$name = $bid.'_'.time();
		mkdir('reports/'.$name);
		chmod('reports/'.$name,0777);
		$files = array();
		foreach($rst as $rec){
			$data = array();
			$r = $this->configmodel->getDetail('24',$rec['callid'],'',$bid);
			$i=0;
			foreach($hkey as $k){
					$v=(isset($r[$k])) ? '"'.str_replace("\n"," ",$r[$k]).'"' : '';
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
		$data_file = 'reports/'.$name.'/Pbxcalls.csv';
		$fp = fopen($data_file,'w');fwrite($fp,$csv_output);fclose($fp);
		chdir('reports')."<br>";
		exec('zip -r '.$name.'.zip '.$name);
		exec('rm -rf '.$name);
		return $name;
	}
	function updatePbxReport($callid){
		$bid = $this->input->post('bid');
		$r = $this->configmodel->getDetail('24',$callid,'',$bid);
		$arr=array_keys($_POST);
		$res = '';
		for($i=0;$i<sizeof($arr);$i++){
			//if(!in_array($arr[$i],array("update_system","custom","convertlead","assignto","lgid","lassignto","lalerttype","updatelead","convertsuptkt","sgid","sassignto","salerttype","updatesuptkt","number","tkt_level","tkt_esc_time","bid"))){
			if(!in_array($arr[$i],array("update_system","custom","bid"))){
				/* Changed for custom fields */
				if(is_array($_POST[$arr[$i]]))
					$val = @implode(',',$_POST[$arr[$i]]);
				elseif($_POST[$arr[$i]]!="")
					$val=$_POST[$arr[$i]];
				else
					$val='';
				$this->db->set($arr[$i],$val);
			}
		}
		$this->db->where('callid',$callid);
		$this->db->update($bid.'_pbxreport');
		//~ if($this->input->post('convertlead') || $this->input->post('updatelead')){
			//~ $source = array("type"=>"pbx","id"=>$callid,"bid"=>$bid,"keyword"=>"");
			//~ $res = $this->configmodel->callconvert($source);
		//~ }
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
		return $res;
	}
	function PbxOperator($opt,$pbxid,$bid){
		$sql=$this->db->query("SELECT * from ".$bid."_pbxext where extid='".$opt."'");
		$res=$sql->row();
		$st = ($res->operator=='1') ? '0' : '1'; 
		$sql=$this->db->query("UPDATE ".$bid."_pbxext set operator='0' where pbxid='".$pbxid."'");
		$sql=$this->db->query("UPDATE ".$bid."_pbxext set operator='".$st."' where pbxid='".$pbxid."' and extid='".$res->extid."'");
		return true;
	}
	function blkdel($arr){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$sql="UPDATE ".$bid."_pbx SET status=2 WHERE pbxid IN(".$arr.")";
		$this->db->query($sql);
		for($i=0;$i<$leadcnt;$i++){
			$this->auditlog->auditlog_info('PBX'," Deleted By ".$this->session->userdata('username'));
		}
		return 1;	
	}
}
?>
