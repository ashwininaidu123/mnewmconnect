<?php
class Ivrsmodel extends Model {
	var $data;
    function Ivrsmodel(){
        parent::Model();
    }
	function addivrs(){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$ivrsid = (isset($_POST['ivrsid']) && $_POST['ivrsid']!='')
					?$_POST['ivrsid']
					:$this->db->query("SELECT COALESCE(MAX(`ivrsid`),0)+1 as id FROM `".$bid."_ivrs`")->row()->id;
		$_POST['prinumber'] = ($_POST['prinumber']!='0') ? $_POST['prinumber']:$this->ivrsmodel->getFreePri();
		if($_POST['ivrsid'] == ''){
            $arr = array_keys($_POST);
			for($i=0;$i<sizeof($arr);$i++){
				if(!in_array($arr[$i],array("update_system","ivrsid","optid","modid"))){
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
			$this->db->set('ivrsid',$ivrsid);
			$this->db->insert($bid.'_ivrs');
			$this->auditlog->auditlog_info($this->lang->line('level_module_Ivrs'),$_POST['title']." Added successfully");
			$this->updatePri($_POST['prinumber'],'1',$bid,'1',$ivrsid);
		}else{
			if(isset($_POST['prinumber']))
				$this->updatePri($this->db->query("SELECT prinumber FROM `".$bid."_ivrs` WHERE ivrsid = '".$_POST['ivrsid']."' ")->row()->prinumber);
			$arr = array_keys($_POST);
			for($i=0;$i<sizeof($arr);$i++){
				if(!in_array($arr[$i],array("update_system","ivrsid","optid","modid"))){
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
			$this->db->where('ivrsid',$ivrsid);
			$this->db->update($bid.'_ivrs');
			$this->updatePri($_POST['prinumber'],'1',$bid,'1',$ivrsid);
			$this->auditlog->auditlog_info($this->lang->line('level_module_Ivrs'),$_POST['title']." Updated successfully");
		}
		$file = "";
		if($_FILES['filename']['error']==0){
			$ext = pathinfo($_FILES['filename']['name'],PATHINFO_EXTENSION);
			$newName = "IVRS".date('YmdHis').".".$ext; 
			move_uploaded_file($_FILES['filename']['tmp_name'],$this->config->item('sound_path').$newName);
			$file = ",optsound		= '".$newName."'";
		}
		$optid = ($_POST['optid']!='')
				 ?$_POST['optid']
				 :$this->db->query("SELECT COALESCE(MAX(`optid`),0)+1 as id FROM ".$bid."_ivrs_options")->row()->id;
		$sql = ($_POST['optid']!='')?" UPDATE ":"REPLACE INTO ";
		$sql .= "".$bid."_ivrs_options SET
				 bid			= '".$bid."'
				,optid			= '".$optid."'
				,ivrsid			= '".$ivrsid."'
				,parentopt		= '0'
				,optorder		= '0'
				,opttext		= ''
				".$file."
				,targettype		= 'list'";
		$sql .= ($_POST['optid']!='')? " WHERE optid			= '".$optid."'":"";
		$this->db->query($sql);			
		return array('ivrsid'=>$ivrsid,'optid'=>$optid);
	}
	function getFreePri(){
		$sql = "SELECT * FROM dummynumber WHERE status='0' AND bid='0' LIMIT 0,1";
		$rst = $this->db->query($sql);
		$rec = $rst->result_array();
		return $rec[0]['number'];
	}

	function updatePri($pri,$status,$bid=0,$type=0,$assid=0){
		if($pri<=10000){
			$sql = "UPDATE dummynumber SET
					status		= '".$status."'
					,type		= '".$type."'
					,associateid= '".$assid."'
					,bid		= '".$bid."'
					WHERE number= '".$pri."'";
		}else{
			$sql = "UPDATE prinumbers SET
					status		= '".$status."'
					,associateid= '".$assid."'
					WHERE number= '".$pri."'";
			//,bid		= '".$bid."'
		}
		$this->db->query($sql);
	}
	function getOptions($args){
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$args['limit'] = ($roleDetail['role']['recordlimit']>($args['ofset']+$args['limit']))?$args['limit']
					:((($roleDetail['role']['recordlimit'] - $args['ofset'])>0)?($roleDetail['role']['recordlimit'] - $args['ofset']):0);
		$con = "";
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$con .= (isset($s['optorder']) && $s['optorder']!='')?" AND o.optorder='".$s['optorder']."'":"";
		$con .= (isset($s['opttext']) && $s['opttext']!='')?" AND o.opttext like '%".$s['opttext']."%'":"";	
		$sql = "SELECT SQL_CALC_FOUND_ROWS o.*,
				if(o.targettype='group',g.groupname,
				if(o.targettype='ivrs',i.title,
				if(o.targettype='pbx',p.title,
				if(o.targettype='employee',e.empname,'')))) as target
				FROM ".$args['bid']."_ivrs_options o
				LEFT JOIN ".$args['bid']."_groups g ON (o.targeteid=g.gid AND o.targettype='group')
				LEFT JOIN ".$args['bid']."_pbx p ON (o.targeteid=p.pbxid AND o.targettype='pbx')
				LEFT JOIN ".$args['bid']."_employee e ON (o.targeteid=e.eid AND o.targettype='employee')
				LEFT JOIN ".$args['bid']."_ivrs i ON (o.targeteid=i.ivrsid AND o.targettype='ivrs')
				WHERE o.bid='".$args['bid']."'
				AND o.ivrsid='".$args['ivrsid']."'
				AND o.parentopt='".$args['parentopt']."'
				".$con."
				ORDER BY o.optorder
				LIMIT ".$args['ofset']." , ".$args['limit'];
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;	
		$opt_add 	= $roleDetail['modules']['5']['opt_add'];
		$opt_view 	= $roleDetail['modules']['5']['opt_view'];
		$opt_delete = $roleDetail['modules']['5']['opt_delete'];	
		$fieldset = $this->configmodel->getFields('5');
		$keys = array();
		$header = array('#');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldname']);
					if($field['fieldname']!='optsound')array_push($header,(($field['customlabel']!="")
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
		$i = $args['ofset']+1;
		$targetTypes = array( 'list'=> 'List'
					  ,'employee'=> 'Employee'
					  ,'group'=> 'Group'
					  ,'pbx'=> 'PBX'
					  ,'sms'=> 'SMS'
					  ,'api'=> 'Api'
					  ,'input'=> 'Connect Reference'
					  ,'chkref'=> 'Check Reference'
					  ,'previous'=> 'Previous'
					  ,'main'=> 'Main'
					  ,'voicemgs'=> 'Voicemsg'
					  ,'hangup'=> 'Hangup'
				);
		foreach($rst as $rec){
			$data = array($i);
            $r = array();
			$r = $this->configmodel->getDetail('5',$rec['optid'],'',$bid);
			foreach($keys as $k){
				if($k=='optsound'){
					$sound = site_url('sounds/'.$r[$k]);
				}elseif($k=='targettype'){
					$v = (isset($r[$k]) && isset($targetTypes[$r[$k]])) ?$targetTypes[$r[$k]]:"";
					array_push($data,$v);
				}elseif($k == 'targeteid'){
					switch($r['targettype']){
						case 'group' : $v = (isset($r[$k])) ? $rec['target'] : '';break;
						case 'employee' : $v = (isset($r[$k])) ? $rec['target'] : '';break;
						case 'pbx' : $v = (isset($r[$k])) ? $rec['target'] : '';break;
						case 'api' : $v = $r['api_url'];break;
						case 'sms' : $v = $r['sms_text'];break;
					}
					array_push($data,$v);
				}else{
					$v = isset($r[$k])?nl2br(wordwrap($r[$k],80,"\n")):"";
					array_push($data,$v);
				}
			}
			$act = "";
			if($opt_add || $opt_view || $opt_delete){
				$act .= ($opt_add)? '<a href="AddOption/'.$r['ivrsid'].'/'.$r['parentopt'].'/'.$r['optid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Edit" class="fa fa-edit"></span></a>':'';
				$act .= ($opt_delete)? '<a href="ivrs/deleteopt/'.$r['ivrsid'].'/'.$r['optid'].'" class="confirm deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
				$act .= ($opt_add && $rec['targettype']=='list') ? '<a href="ivrs/addopt/'.$r['ivrsid'].'/'.$r['optid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="fa fa-plus" title="Add Option"></span></a>':'<span title="Add Option" class="fa fa-plus"></span>';
				$act .= ($opt_add && $rec['targettype']=='list') ? '<a href="ListOption/'.$r['ivrsid'].'/'.$r['optid'].'"><span title="List Option" class="fa fa-list-ul"></span></a>':'<span title="List Option" class="fa fa-list-ul"></span>';
				$act .= ($opt_view)? ' <a target="_blank" href="'.$sound.'"><span title="Sound" class="fa fa-volume-up"></span></a>':'';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
			unset($r);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function addOption(){
		$bid = $_POST['bid'];
		$sql = "SELECT optid FROM ".$bid."_ivrs_options WHERE ivrsid ='".$_POST['ivrsid']."' AND parentopt='".$_POST['parentopt']."' AND optorder='".$_POST['optorder']."'";
		$res = $this->db->query($sql);
		if($res->num_rows() == 0){
			$arr = array_keys($_POST);
			for($i=0;$i<count($arr);$i++){
				if(isset($_POST[$arr[$i]]) && $_POST[$arr[$i]] != '' && !in_array($arr[$i],array('update_system','modid',"extURL"))){
					$this->db->set($arr[$i],$_POST[$arr[$i]]);
				}
			}
			$ext = pathinfo($_FILES['optsound']['name'],PATHINFO_EXTENSION);
			$newName = "IVRS".date('YmdHis').".".$ext;
			if(@move_uploaded_file($_FILES['optsound']['tmp_name'],$this->config->item('sound_path').$newName)){
				$optid = $this->db->query("SELECT COALESCE(MAX(`optid`),0)+1 as id FROM ".$bid."_ivrs_options")->row()->id;
				$this->db->set('optid',$optid);
				$this->db->set('optsound',$newName);
				$this->db->insert($bid."_ivrs_options");
				$gdetails=$this->configmodel->getDetail('4',$_POST['ivrsid'],'',$bid);
				$this->auditlog->auditlog_info($this->lang->line('level_module_Ivrs'),'ivrsoption added to '.$gdetails['title']);
				$optid = $this->db->insert_id();
			}
			$ivrsname = $this->db->query("SELECT title FROM ".$bid."_ivrs WHERE bid='".$bid."' AND ivrsid= '".$_POST['ivrsid']."'")->row()->title;
			$this->auditlog->auditlog_info('IVRS',"IVRS option added for the IVRS group ".$ivrsname);
			$return = '1';
		}else{
			$return = '2';
		}
		return $return;
	}
	function editOption(){
		$file = "";
		$bid = $_POST['bid'];
		$sql = "SELECT optid FROM ".$bid."_ivrs_options WHERE ivrsid ='".$_POST['ivrsid']."' AND parentopt='".$_POST['parentopt']."' AND optorder='".$_POST['optorder']."' AND optid != '".$_POST['optid']."'";
		$res = $this->db->query($sql);
		if($res->num_rows() == 0){
			$target = (isset($_POST['targeteid']))? ",targeteid = '".$_POST['targeteid']."'" : ",targeteid = 0";
			$api_url = (isset($_POST['api_url']))? ",api_url = '".$_POST['api_url']."'" : ",api_url = ''";
			$sms_text = (isset($_POST['sms_text']))? ",sms_text = '".$_POST['sms_text']."'" : " ,sms_text = ''";
			if($_FILES['optsound']['error']==0){
				$ext = pathinfo($_FILES['optsound']['name'],PATHINFO_EXTENSION);
				$newName = "IVRS".date('YmdHis').".".$ext;
				move_uploaded_file($_FILES['optsound']['tmp_name'],$this->config->item('sound_path').$newName);
				$file = ",optsound = '".$newName."'";
			}
			$arr = array_keys($_POST);
			for($i=0;$i<sizeof($arr);$i++){
				if(!in_array($arr[$i],array("update_system","optid","modid","extURL"))){
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
			$this->db->where('optid',$_POST['optid']);
			$result = $this->db->update($bid."_ivrs_options");
			$ivrsname = $this->db->query("SELECT title FROM ".$bid."_ivrs WHERE bid='".$bid."' AND ivrsid= '".$_POST['ivrsid']."'")->row()->title;
			$this->auditlog->auditlog_info('IVRS',"IVRS option updated for the ivrs group ".$ivrsname);
			$return = '1';
		}else{
			$return = '2';
		}
		return $return;
	}
	function getIvrslist($bid,$ofset='0',$limit='20'){
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
		if(isset($s)){
            $arr = array_keys($s);
            for ($n =0;$n<count($arr);$n++){
                if(strstr($arr[$n],'c_')){
					if(is_array($s[$arr[$n]])){
						$s[$arr[$n]] = @implode(',',$s[$arr[$n]]);
					}
                    $con.=(isset($s[$arr[$n]]) && $s[$arr[$n]]!='' && $s[$arr[$n]]!=' ') ? " AND i.".$arr[$n]."= '".$s[$arr[$n]]."'":"";
                }
            }
        }
		$con .= (isset($s['title']) && $s['title']!='')?" AND i.title like '%".$s['title']."%'":"";
		$con .= (isset($s['ivrsnumber']) && $s['ivrsnumber']!='')?" AND p.landingnumber like '%".$s['ivrsnumber']."%'":"";
		$con .= (isset($s['timeout']) && $s['timeout']!='')?" AND i.timeout ='".$s['timeout']."'":"";
		$sql = "SELECT SQL_CALC_FOUND_ROWS i.* FROM ".$bid."_ivrs i
				LEFT JOIN prinumber p on i.prinumber=p.number
				WHERE i.status='1' AND i.bid='".$bid."' ".$con." 
				ORDER BY i.ivrsid DESC
				limit $ofset,$limit"; 
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		$opt_add 	= $roleDetail['modules']['4']['opt_add'];
		$opt_view 	= $roleDetail['modules']['4']['opt_view'];
		$opt_delete = $roleDetail['modules']['4']['opt_delete'];
		$fieldset = $this->configmodel->getFields('4',$bid);
		$keys = array();
		$header = array('#',"<a href='javascript://'><span id='c_all' class='glyphicon glyphicon-gok'></span></a>");
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
			$r = $this->configmodel->getDetail('4',$rec['ivrsid'],'',$bid);
			$v='<input type="checkbox" class="blk_check" name="blk[]" value="'.$r['ivrsid'].'"/>';
			array_push($data,$v);
			foreach($keys as $k){
				if($k=='filename'){
					$sound = site_url('sounds/'.$r[$k]);
				}elseif($k=='cid'){
					$v = isset($r['companyname'])?$r['companyname']:"";
					array_push($data,$v);
				}else{
					$v = isset($r[$k])?nl2br(wordwrap($r[$k],80,"\n")):"";
					array_push($data,$v);
				}
			}
			$act = "";
			if($opt_add || $opt_view || $opt_delete){
				$act .= ($opt_add)? ' <a href="ivrs/addpopup/'.$r['ivrsid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Edit" class="fa fa-edit"></span></a>':'';
				$act .= ($opt_delete)? ' <a href="ivrs/deleteivrs/'.$r['ivrsid'].'/'.$r['optid'].'" class="confirm deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
				$act .= ($opt_add)? ' <a href="AddOption/'.$r['ivrsid'].'/0/'.$r['optid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="fa fa-plus" title="Add Option" ></span></a>':'';
				$act .= ($opt_add)? ' <a href="ListOption/'.$r['ivrsid'].'/'.$r['optid'].'"><span title="List Option" class="fa fa-list-ul"></a>':'';
				$act .= ($opt_view && isset($sound))? ' <a target="_blank" href="'.$sound.'"><span title="Sound" class="fa fa-volume-up"></span></a>':'';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	} 

	function getIvrslistDeleted($bid,$ofset='0',$limit='20'){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
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
		$con .= (isset($s['title']) && $s['title']!='')?" AND i.title like '%".$s['title']."%'":"";
		$con .= (isset($s['ivrsnumber']) && $s['ivrsnumber']!='')?" AND p.landingnumber like '%".$s['ivrsnumber']."%'":"";
		$con .= (isset($s['timeout']) && $s['timeout']!='')?" AND i.timeout ='".$s['timeout']."'":"";
		$sql = "SELECT SQL_CALC_FOUND_ROWS i.* FROM ".$bid."_ivrs i
				LEFT JOIN prinumber p on i.prinumber=p.number
				WHERE i.status='2' AND i.bid='".$bid."' ".$con."
				ORDER BY i.ivrsid DESC
				limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		$opt_add 	= $roleDetail['modules']['4']['opt_add'];
		$opt_view 	= $roleDetail['modules']['4']['opt_view'];
		$opt_delete = $roleDetail['modules']['4']['opt_delete'];
		$fieldset = $this->configmodel->getFields('4',$bid);
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
			$r = $this->configmodel->getDetail('4',$rec['ivrsid'],'',$bid);
			foreach($keys as $k){
				if($k=='filename'){
					$sound = site_url('sounds/'.$r[$k]);
				}else{
					$v = isset($r[$k])?nl2br(wordwrap($r[$k],80,"\n")):"";
					array_push($data,$v);
				}
			}
			$act = "";
			if($opt_add||$opt_view){
				$act .= ($opt_add)? '<a href="ivrs/undelete/'.$r['ivrsid'].'" class="deleteClass"><img src="system/application/img/icons/undelete.png"  style="vertical-align:top;" id="'.$r['ivrsid'].'" title="Undelete" /></a>':'';
				$act .= ($opt_view)? ' <a target="_blank" href="'.$sound.'"><span title="Sound" class="fa fa-volume-up"></span></a>':'';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	} 
	function getIvrsReportlist($bid,$ofset='0',$limit='20',$tab){
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$con = "";
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		if(isset($_POST['Adv_submit'])){
			$this->session->set_userdata('Adsearch',$_POST);
		}
		if(isset($_POST['sav_search'])){
			Save_search_data($bid,$this->session->userdata('eid'),json_encode($_POST),'16');
			$this->session->set_userdata('Adsearch',$_POST);
		}
		if($tab>0){
			$qs=get_save_searchrow($bid,'16',$this->session->userdata('eid'),$tab);
			$content=(array)json_decode($qs['content']);
			$this->session->set_userdata('Adsearch',$content);
		}
		if($this->session->userdata('Adsearch')){
			$Ads = $this->session->userdata('Adsearch');
		}else{
			$this->session->unset_userdata('Adsearch');
		}
		if(isset($Ads) && sizeof($Ads)>0){
			switch($Ads['timespan']){
				case 'all':
				default :
						 $con.='';	
				break;
				case 'today':
						 $con.=" and date(h.datetime)>= '".date('Y-m-d')."'";
				break;
				case 'last7':
						$date=date('Y-m-d',strtotime('-6 days'));
						$con.=" and date(h.datetime)>= '".$date."'";	
				break;			
				case 'month':
						$date=date('Y-m-01');
						$con.=" and date(h.datetime)>= '".$date."'";	
				break;				
			}
			if(isset($Ads['multiselect_gid']) && sizeof($Ads['multiselect_gid'])>0){
				$gids=implode(",",$Ads['multiselect_gid']);
				      $con.=" and i.ivrsid in (".$gids.")";
				
			}
			if(isset($Ads['multiselect_eids']) && sizeof($Ads['multiselect_eids'])>0){
				$eids=implode(",",$Ads['multiselect_eids']);
				      $con.=" and i.eid in (".$eids.")";
				
			}
			$custiom_ids=array();
			$cust=0;
			$field_array=array("ivrstitle"=>"i.title","callfrom"=>"h.callfrom","options"=>"options","datetime"=>"date(h.datetime)","endtime"=>"date(h.endtime)","name"=>"h.name","email"=>"h.email");
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
				$con.= (isset($field_array[$Ads['field_d'][$i]])) ? " ".$Ads['cond'][$i]."  ".$field_array[$Ads['field_d'][$i]]."  ".$app : '';	
			}
		}
		if(isset($s)){
            $arr = array_keys($s);
            for ($n =0;$n<count($arr);$n++){
                if(strstr($arr[$n],'c_')){
					if(is_array($s[$arr[$n]])){
						$s[$arr[$n]] = @implode(',',$s[$arr[$n]]);
					}
                    $con.=(isset($s[$arr[$n]]) && ($s[$arr[$n]]!='')  && ($s[$arr[$n]]!=' ') && ($s[$arr[$n]]!='0')) ? " AND h.".$arr[$n]." LIKE '%".$s[$arr[$n]]."%'":"";
                }
            }
        }
		$con .= (isset($s['ivrstitle']) && $s['ivrstitle']!='')?" AND i.title like '%".$s['ivrstitle']."%'":"";
		$con .= (isset($s['callfrom']) && $s['callfrom']!='')?" AND h.callfrom like '%".$s['callfrom']."%'":"";
		$con .= (isset($s['name']) && $s['name']!='')?" AND h.name like '%".$s['name']."%'":"";
		$con .= (isset($s['email']) && $s['email']!='')?" AND h.email like  '%".$s['email']."%'":"";
		$con .= (isset($s['datetime']) && $s['datetime']!='')?" and date(h.datetime)>= '".$s['datetime']."'":"";
		$con .= (isset($s['endtime']) && $s['endtime']!='')?" and date(h.endtime)<= '".$s['endtime']."'":"";
		$sql = "SELECT SQL_CALC_FOUND_ROWS h.*,l.lead_status FROM ".$bid."_ivrshistory h
				LEFT JOIN ".$bid."_ivrs i on i.ivrsid=h.ivrsid
				LEFT JOIN ".$bid."_leads l ON h.leadid = l.leadid
				WHERE h.bid='".$bid."' ".$con."
				ORDER BY h.datetime DESC
				LIMIT $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		$opt_add 	= $roleDetail['modules']['16']['opt_add'];
		$opt_view 	= $roleDetail['modules']['16']['opt_view'];
		$opt_delete = $roleDetail['modules']['16']['opt_delete'];
		$fieldset = $this->configmodel->getFields('16',$bid);
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
		if($opt_add || $opt_view || $opt_delete)
			array_push($keys,$this->lang->line('level_Action'));
			array_push($header,$this->lang->line('level_Action'));
		$ret['header'] = $header;
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$data = array($i);
			$r = $this->configmodel->getDetail('16',$rec['hid'],'',$bid);
			$v = '<input type="checkbox" class="blk_check" name="blk[]" value="'.$rec['hid'].'"/>';	
			array_push($data,$v);
			foreach($keys as $k){
				if($k == 'callfrom'){
					$v = '<a href="ivrs/calldetail/'.$rec['hid'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r[$k].'</a>';
				}elseif($k=='employee'){
					$v = '<a href="Employee/activerecords/'.$r['eid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$r[$k].'</a>';
				}elseif($k=='Action'){
                    $v = '<a href="IVRSEditReport/'.$rec['hid'].'"><span title="Edit" class="fa fa-edit"></span></a>';
                    $v .= ($roleDetail['role']['accessrecords']=='0') ? (($r['filename']!='' && file_exists('sounds/'.$r['filename']))?' <a target="_blank" href="'.site_url('sounds/'.$r['filename']).'"><span title="Sound" class="fa fa-volume-up"></span></a>':'<span class="glyphicon glyphicon-volume-off"></span> '):"";
                    $v .= anchor("Report/clicktoconnect/".$rec['hid']."/2", ' <span title="click To Connect" class="fa fa-phone"></span>',array('class'=>'clickToConnect'));
                    $v .= ($r['email']!='')?"<a href=\"Javascript:void(null)\" onClick=\"window.open('/Email/compose/".$r['hid']."/ivrs', 'Send Mail', 'toolbar=0,scrollbars=0,location=0,status=1,menubar=0,width=950,height=480,resizable=1')\"><span title='Send Mail' class='fa fa-envelope'></span></a>":'&nbsp;<span title="Send Mail" class="fa fa-envelope"></span>';
                    $v .= anchor("Report/followup/".$r['hid']."/0/ivrs", ' <img src="system/application/img/icons/comments.png" title="Followups" style="vertical-align:top;" width="16" height="16">','class="btn-danger" data-toggle="modal" data-target="#modal-responsive"');                           
                    $v .= anchor("Report/sendSms/".$rec['hid']."/ivrs", '&nbsp;<span title="Click to send SMS" class="glyphicon glyphicon-comment"></span>','class="clickToSMS" data-toggle="modal" data-target="#modal-empl"');           
                    $v .= "<a href=\"Javascript:void(null)\" onClick=\"window.open('Report/sendFields/".$rec['hid']."/".$bid."/16/ivrs', 'Send Fields', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=550,height=500,left=200,top=20,resizable=1')\">&nbsp;<span title='Click to Send Fields' class='fa fa-list-alt'></span></a>";
                }else{
                    $v = isset($r[$k])?nl2br(wordwrap($r[$k],80,"\n")):"";
                }
				$leadColor = $rec['lead_status'];
				$v = ($r['lead']!=null && $leadColor != 1) ? "<font color='green'>".$v."</font>"
						:($leadColor == 1 ? "<font color='#007F55'>".$v."</font>"
						:(($r['suptkt']!=null && $r['tktid'] != 0) ? "<font color='orange'>".$v."</font>" :$v)
						);
				array_push($data,$v);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function getOptionDetail($args){
		$sql = "SELECT * FROM ".$args['bid']."_ivrs_options WHERE ivrsid='".$args['ivrsid']."' AND bid='".$args['bid']."' 		AND optid='".$args['optid']."'";
		$rst = $this->db->query($sql);
		return $rst->result_array();
	}
	function getEmplist(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$rst = array();
		$sql = "SELECT eid,empname,empnumber FROM ".$bid."_employee";
		$rst = $this->db->query($sql)->result_array();
		return $rst;
	}
	function deleteOpt($optid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$this->db->query("DELETE FROM ".$bid."_ivrs_options 
						WHERE optid = '".$optid."' 
						AND bid='".$bid."'");
		return;
	}
	function deleteIvrs($args){
		$detail = $this->configmodel->getDetail('4',$args['ivrsid']);
		$this->updatePri($detail['prinumber']);
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$this->db->query("UPDATE ".$bid."_ivrs SET
						  status='2'
						  WHERE ivrsid = '".$args['ivrsid']."' 
						  AND bid='".$bid."'");
		$this->auditlog->auditlog_info($this->lang->line('level_module_Ivrs'),$detail['title']." Is deleted ");				  
	}
	function undeleteIvrs($args){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$detail = $this->configmodel->getDetail('4',$args['ivrsid'],'',$bid);
		$pri = $this->db->query("SELECT * FROM prinumber WHERE number='".$detail['prinumber']."' AND bid='".$bid."' AND status='0'");
		if($pri->num_rows()>0){
			$this->db->query("UPDATE ".$bid."_ivrs SET
						  status='1'
						  WHERE ivrsid = '".$args['ivrsid']."' 
						  AND bid='".$bid."'");
			$this->updatePri($_POST['prinumber'],'1',$bid,'1',$args['ivrsid']);
			$this->auditlog->auditlog_info($this->lang->line('level_module_Ivrs'),$detail['title']." Is undeleted ");
		}else{
			$this->session->set_flashdata(array('msgt' => 'error','msg' => $this->lang->line('priinuse')));
		}
	}
	function ivrs_csvreport($bid){
		$res=array();
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
		$csv_output .= implode(",",$header)."\n";
		$sql="SELECT SQL_CALC_FOUND_ROWS h.*,i.title
				FROM ".$bid."_ivrshistory h
				LEFT JOIN ".$bid."_ivrs i on i.ivrsid=h.ivrsid
				WHERE h.bid='".$bid."'";
		$sql .= (isset($_POST['ivrsdatefrom']) && $_POST['ivrsdatefrom']!='') ? " AND date(h.datetime)>='".$_POST['ivrsdatefrom']."' " : "";
		$sql .= (isset($_POST['ivrsdateto']) && $_POST['ivrsdateto']!='') ? " AND date(h.endtime)<='".$_POST['ivrsdateto']."' " : "";
		if(!empty($_POST['ivrsid'])){
			if($_POST['ivrsid'][0]!=""){
				$sql .= " AND h.ivrsid IN (".implode(",",$_POST['ivrsid']).")";
			}
		}
		$rst = $this->db->query($sql)->result_array();
		$name = $bid.'_'.time();
		mkdir('reports/'.$name);
		chmod('reports/'.$name,0777);
		$files = array();
		foreach($rst as $rec){
			$data = array();
			$r = $this->configmodel->getDetail('16',$rec['hid'],'',$bid);
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
			$csv_output .= implode(",",$data)."\n";
		}
		$data_file = 'reports/'.$name.'/Ivrscalls.csv';
		$fp = fopen($data_file,'w');fwrite($fp,$csv_output);fclose($fp);
		chdir('reports')."<br>";
		exec('zip -r '.$name.'.zip '.$name);
		exec('rm -rf '.$name);
		return $name;
	}	
		
	function updateIvrsReport($callid){
		$bid = $this->input->post('bid');
		$arr = array_keys($_POST);
		$id = $this->uri->segment(2);
		$res = '';
		for($i=0;$i<sizeof($arr);$i++){
			if(!@in_array($arr[$i],array("update_system","custom","convertlead","assignto","lgid","lassignto","lalerttype","updatelead","convertsuptkt","sgid","sassignto","salerttype","updatesuptkt","number","tkt_level","tkt_esc_time","bid"))){
					if(is_array($_POST[$arr[$i]]))
						$val = @implode(',',$_POST[$arr[$i]]);
					elseif($_POST[$arr[$i]]!="")
						$val=$_POST[$arr[$i]];
					else
						$val='';
					$this->db->set($arr[$i],$val);
				}
		}
		$this->db->where('hid',$callid);
		$this->db->update($bid.'_ivrshistory');
		if($this->input->post('convertlead') || $this->input->post('updatelead')){
			$source = array("type"=>"ivrs","id"=>$id,"bid"=>$bid,"keyword"=>"");
			$res = $this->configmodel->callconvert($source);
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
		return $res;
	}
	function getIvreflist($ofset='0',$limit='20'){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$q = ' WHERE 1 ';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['ivrsid']) && $s['ivrsid']!='')?" and r.ivrsid=".$s['ivrsid']."":"";
		$q.=(isset($s['contactName']) && $s['contactName']!='')?" and r.contactName like '%".$s['contactName']."%'":"";
		$q.=(isset($s['number']) && $s['number']!='')?" and r.number like '%".$s['number']."%'":"";
		$q.=(isset($s['refinput']) && $s['refinput']!='')?" and r.refinput like '%".$s['refinput']."%'":"";
		$q.=(isset($s['file_number']) && $s['file_number']!='')?" and r.file_number like '%".$s['file_number']."%'":"";
		$q.=(isset($s['targettype']) && $s['targettype']!='')?" and r.targettype like '%".$s['targettype']."%'":"";
		$q.=(isset($s['targetid']) && $s['targetid']!='')?" and r.targetid like '%".$s['targetid']."%'":"";
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS i.title,
										if(r.targettype='group',gi.groupname,
										if(r.targettype='pbx',pi.title,
										if(r.targettype='ivrs',ti.title,
										if(r.targettype='employee',ei.empname,'')))) as targetName
										,r.* FROM ".$bid."_ivrsref r
										LEFT JOIN ".$bid."_ivrs i ON r.ivrsid=i.ivrsid 
										LEFT JOIN ".$bid."_ivrs ti ON r.targetid=ti.ivrsid
										LEFT JOIN ".$bid."_pbx pi ON r.targetid=pi.pbxid
										LEFT JOIN ".$bid."_groups gi ON r.targetid=gi.gid
										LEFT JOIN ".$bid."_employee ei ON r.targetid=ei.eid
										$q 
										ORDER BY r.assigndate DESC
										LIMIT $ofset,$limit")->result_array();
								
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;							
	}
	function getIvlist(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("SELECT i.ivrsid,i.title FROM ".$bid."_ivrs i
							   LEFT JOIN prinumbers p ON i.prinumber=p.number
							   WHERE i.bid=".$bid." AND i.status='1'");
		$res=array();					   
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $rows){
				$res[$rows['ivrsid']]=$rows['title'];
			}					   
		}
		return $res;
	}
	function Iv_addRef($id){
		if($id!=''){
			$this->db->set('ivrsid',$this->input->post('ivrs'));
			$this->db->set('refinput',$this->input->post('refinput'));
			$this->db->set('contactName',$this->input->post('cname'));
			$this->db->set('number',$this->input->post('cnumber'));
			$this->db->set('file_number',$this->input->post('fnumber'));
			$this->db->set('targettype',$this->input->post('targettype'));
			$this->db->set('targetid',$this->input->post('targetid'));
			$this->db->where('refid',$id);
			$this->db->update($this->session->userdata('bid')."_ivrsref");
			return true;
		}else{
			$Ivref_id=$this->db->query("SELECT COALESCE(MAX(`refid`),0)+1 as id FROM ".$this->session->userdata('bid')."_ivrsref")->row()->id;
			$this->db->set('refid',$Ivref_id);
			$this->db->set('ivrsid',$this->input->post('ivrs'));
			$this->db->set('refinput',$this->input->post('refinput'));
			$this->db->set('contactName',$this->input->post('cname'));
			$this->db->set('number',$this->input->post('cnumber'));
			$this->db->set('file_number',$this->input->post('fnumber'));
			$this->db->set('targettype',$this->input->post('targettype'));
			$this->db->set('targetid',$this->input->post('targetid'));
			$this->db->set('assigndate',date('Y-m-d h:i:s'));
			$this->db->set('status','1');
			$this->db->insert($this->session->userdata('bid')."_ivrsref");
			return true;
		}
	}
	function getIvRefDetail($id){
		if($id!=''){
			$sql=$this->db->query("SELECT * FROM ".$this->session->userdata('bid')."_ivrsref where refid='".$id."'");
			return (array)$sql->row();
		}else{
			return array();
		}
	}
	function delIvrRef($iv_id){
		$ivDetail=$this->getIvRefDetail($iv_id);
		$status=($ivDetail['status']==1)?0:1;
		$this->db->set('status',$status);
		$this->db->where('refid',$iv_id);
		$this->db->update($this->session->userdata('bid')."_ivrsref");
	}
	function StatusIvrRef($iv_id){
		$ivDetail=$this->getIvRefDetail($iv_id);
		$status=($ivDetail['status']==1)?2:1;
		$this->db->set('status',$status);
		$this->db->where('refid',$iv_id);
		$this->db->update($this->session->userdata('bid')."_ivrsref");
	}
	function getFieldKey($fieldid,$bid){
		$fieldkey = $this->db->query("SELECT field_key FROM ".$bid."_customfields WHERE fieldid='".$fieldid."'")->row()->field_key;
		return $fieldkey;
	}
		function blk_del($arr){
			$cbid = $this->session->userdata('cbid');
			$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
			$sql="UPDATE ".$bid."_ivrs SET status=2 WHERE ivrsid IN(".$arr.")";
			$this->db->query($sql);
			
			for($i=0;$i<$leadcnt;$i++){
				$this->auditlog->auditlog_info('IVRS',"Deleted By ".$this->session->userdata('username'));
			}
			return 1;	
	}
}
?>
