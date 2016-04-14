<?php
Class outboundcallsmodel extends Model
{
	function outboundcallsmodel(){
		 parent::Model();
	}

	function getEmployees($grid='')
	{
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res = ($grid=='') ? array(''=>'Select Employee') : array();
		$query=$this->db->query("SELECT eid,empname FROM ".$bid."_employee WHERE status=1 ORDER BY empname");
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q='';
		$q.=($roleDetail['role']['owngroup']=='1' && $roleDetail['role']['admin']!='1') ? " AND (g.eid = '".$this->session->userdata('eid')."' OR e.eid='".$this->session->userdata('eid')."')":"";
		$q .= ($grid=='') ? '' : " AND g.gid='".$grid."'";


		$query = ($grid=='') ? "SELECT * FROM ".$bid."_employee WHERE status='1' ORDER BY empname" : "SELECT e.* FROM ".$bid."_employee e
								 LEFT JOIN ".$bid."_obc_grpemp ge ON e.eid=ge.ocbeid
								 LEFT JOIN ".$bid."_obc_groups g ON g.gid=ge.gid
								 WHERE e.status=1 ".$q." ORDER BY e.empname";
		
		$query=$this->db->query($query);

		if($query->num_rows()>0)
		{
			foreach($query->result_array() as $rt)
			$res[$rt['eid']]=$rt['empname'];
		}
		return $res;
	}
	
	function addobcGroup()
	{
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$id = $_POST['id'];
		$obcgr_use = $this->configmodel->obcusageCheck($bid,'group');
		if($obcgr_use['type'] == 1 && $obcgr_use['used'] == 0 && $id == ''){
			return 'FALSE';
		}else{
			$sql=$this->db->query("SELECT gid FROM ".$bid."_obc_groups WHERE groupname='".$_POST['groupname']."' AND gid !='".$_POST['id']."'");
			if($sql->num_rows()==0){
				$this->db->set('groupname',$_POST['groupname']);
				$this->db->set('group_desc',$_POST['group_desc']);
				$this->db->set('eid',$_POST['eid']);
				$this->db->set('group_rule',$_POST['grule']);
				if($id  ==''){
					$id=$this->db->query("SELECT COALESCE(MAX(`gid`),0)+1 as id FROM ".$bid."_obc_groups")->row()->id;
					$this->db->set('bid',$bid);
					$this->db->set('status','1');
					$this->db->set('gid',$id);
					$this->db->insert($bid."_obc_groups");
					if($obcgr_use['type'] == 1)
						$this->db->query("UPDATE business_obc_use SET `grplimit`=(`grplimit`-1) WHERE bid='".$bid."'");
					$return = $id;
				}else{
					$this->db->where('gid',$id);
					$this->db->update($bid."_obc_groups");
					$return = $id;
				}
				if(isset($_POST['custom'])){
				$arrs=array_keys($_POST['custom']);
				for($k=0;$k<sizeof($arrs);$k++){
					if(is_array($_POST['custom'][$arrs[$k]])){
						$x=implode(",",$_POST['custom'][$arrs[$k]]);
					}else{
						$x=$_POST['custom'][$arrs[$k]];
					}
					$this->db->query("DELETE FROM ".$bid."_customfieldsvalue WHERE bid= '".$bid."' AND modid= '44' AND fieldid = '".$arrs[$k]."'");
					$sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
						 bid			= '".$bid."'
						,modid			= '44'
						,fieldid		= '".$arrs[$k]."'
						,dataid			= '".$id."'
						,value			= '".$x."'";
					$this->db->query($sql);
				}
				$this->auditlog->auditlog_info('Outboundcall groups',$this->input->post('name'). " New Outboundcall Group Added");
				}
			}else{
				$return = '';
			}
			return $return;
		}
	}
	
	function obcgrpemp_existed($ogid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();
		$res1=$this->db->query("SELECT SQL_CALC_FOUND_ROWS g.groupname,e.empname,a.eid,a.gid ,a.status
							   FROM ".$bid."_obc_grpemp a
							   LEFT JOIN ".$bid."_obc_groups g on a.gid=g.gid
							   LEFT JOIN ".$bid."_employee e on a.eid=e.eid
							   WHERE a.gid=$ogid");
		if($res1->num_rows()>0){					   
			foreach($res1->result_array() as $row){
				$res[]=$row['eid'];
			}
		}
		return $res;
	}
	
	function get_empname($eid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("SELECT empname FROM ".$bid."_employee WHERE eid=$eid");
		$res=$sql->row();
		return $res;
	}
	
	function addobc_grpemp($ogid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$err=0;

		foreach($_POST['emp_ids'] as $eids){
			$check=$this->db->query("SELECT obceid FROM ".$bid."_obc_grpemp WHERE eid=".$eids." AND gid='".$ogid."'");
			if($check->num_rows()==0){
				$obcgremp_use = $this->configmodel->obcusageCheck($bid,'employee');
				if($obcgremp_use['type'] == 1 && $obcgremp_use['used'] == 0 && $id == ''){
					return '2';
				}else{
					$err++;
					$this->db->set('bid', $bid);                       
					$this->db->set('gid', $ogid);                       
					$this->db->set('eid', $eids);
					$this->db->set('status',1);
					if(isset($_POST['empweight'.$eids])){
						$this->db->set('weight', $this->input->post('empweight'.$eids)); 
					}                     
					$this->db->insert($bid."_obc_grpemp");
					$emp_name=$this->get_empname($eids);
					$gname=$this->db->query("SELECT groupname FROM 	".$bid."_obc_groups WHERE gid='".$ogid."'")->row()->groupname;
					$this->auditlog->auditlog_info('Outbound Call Group Employee',$emp_name->empname." added to the group ".$gname);
					if($obcgremp_use['type'] == 1)
						$query=$this->db->query("UPDATE business_obc_use SET `emplimit`=(`emplimit` -1) WHERE bid='".$bid."'");
				}
			}
		}
		if($err!=0){
			return 1;
		}else{
			return 0;
		}
	}
	
	function obcgrpemplist($ogid,$ofset='0',$limit='20'){
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
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS g.groupname,e.empname,a.eid,a.gid,a.status,a.weight	
							   FROM ".$bid."_obc_grpemp a
							   LEFT JOIN ".$bid."_obc_groups g on a.gid=g.gid
							   LEFT JOIN ".$bid."_employee e on a.eid=e.eid
							   WHERE a.gid=$ogid $q
							   ORDER BY e.`empname`
							   LIMIT $ofset,$limit
							   ")->result_array();
       $res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	
	function list_obc_grps($bid,$ofset,$limit,$type=''){
		$q= '';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q .= ($type == 'del') ? " a.status =2 " : " a.status =1 ";
		$q.=(isset($s['eid']) && $s['eid']!='')?" AND a.eid = '".$s['eid']."'":"";
		$q.=(isset($s['groupname']) && $s['groupname']!='')?" and a.groupname LIKE '%".$s['groupname']."%'":"";
		$q.=(isset($s['group_rule']) && $s['group_rule']!='')?" AND a.group_rule = '".$s['group_rule']."'":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$sql = "SELECT SQL_CALC_FOUND_ROWS a.* FROM ".$bid."_obc_groups a
				WHERE $q ORDER BY a.gid DESC 
		        LIMIT $ofset,$limit";  
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='44'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$group_rule = array(""=>"Select","1"=>"Sequential","2"=>"Weighted");
		$fieldset = $this->configmodel->getFields('44',$bid);
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
		if($opt_add || $opt_view || $opt_delete)
			array_push($header,$this->lang->line('level_Action'));
		$ret['header'] = $header;
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$data = array($i);
			$r = $this->configmodel->getDetail('44',$rec['gid'],'',$bid);
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
					$act .= '<a href="'.base_url().'outboundcalls/delete_obcgrp/'.$rec['gid'].'/1"><img src="system/application/img/icons/undelete.png" title="Restore" /></a>';
				}else{
					$act .= ($opt_add) ?'<a href="outboundcalls/obc_grp_add/'.$rec['gid'].'"><span title="Edit" class="fa fa-edit"></span></a>':'';
					$act .= ($opt_delete) ? '<a href="'.base_url().'outboundcalls/delete_obcgrp/'.$rec['gid'].'" class="deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
					$act .= '<a href="outboundcalls/addgrpemp/'.$rec['gid'].'" ><span class="fa fa-plus" title="Add Employee"></span></a>&nbsp;';
					$act .= '<a href="outboundcalls/obc_grpemp_list/'.$rec['gid'].'" ><span title="List Employees" class="glyphicon glyphicon-user"></span></a>';
					$act .= '<a href="outboundcalls/addcontacts/'.$rec['gid'].'" ><img src="system/application/img/icons/contact.png" title="Add Contacts" width="16" height="16" /></a>';
					$act .= '<a href="outboundcalls/obc_grpcnt_list/'.$rec['gid'].'" ><span class="fa fa-file-text"  title="View Contacts"></span></a>';
				}
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	
	function del_obc_grp($id,$bid,$type=''){
		$type = ($type =='') ? '2' : $type;
		$this->db->set('status', $type);
		$this->db->where('gid',$id);
		$this->db->update($bid."_obc_groups");
		$obcemp_use = $this->configmodel->obcusageCheck($bid,'group');
		if($obcemp_use['type'] == 1)
			$this->db->query("UPDATE  business_obc_use SET `grplimit`=(`grplimit`+1) WHERE bid='".$bid."'");
		$this->auditlog->auditlog_info('Outbound call Groups',$id. " Updated By ".$this->session->userdata('username'));
		return 1;
	}
	
	function del_obcgrpemp($id,$ogid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("DELETE FROM ".$bid."_obc_grpemp WHERE eid=$id AND gid=$ogid");
		$obcemp_use = $this->configmodel->obcusageCheck($bid,'employee');
		if($obcemp_use['type'] == 1)
			$this->db->query("UPDATE  business_obc_use SET `emplimit`=(`emplimit`+1) WHERE bid='".$bid."'");
		$emp_name=$this->get_empname($id);
		$gname=$this->db->query("SELECT groupname FROM	".$bid."_obc_groups WHERE gid='".$ogid."'")->row()->groupname;
		$this->auditlog->auditlog_info('Outbound call Group Employee',$emp_name->empname." is Removed From the Outbound call Group ".$gname);
		return true;
	}
	
	function obcgrpemp_dis($eid,$ogid,$bid){
		$check=$this->db->query("SELECT * FROM ".$bid."_obc_grpemp WHERE eid=".$eid." AND gid=$ogid")->row_array();	
		$status=($check['status']==0)?'1':'0';
		$this->db->set('status',$status);
		$this->db->where('eid',$eid);	
		$this->db->where('gid',$ogid);
		$this->db->update($bid.'_obc_grpemp');
		$itemDetail= $this->configmodel->getDetail('44',$ogid,'',$bid);
		$empDetail= $this->configmodel->getDetail('2',$eid,'',$bid);
		$text=($status)?" Enabled":" Disabled";
		$this->auditlog->auditlog_info('Group Employee', $empDetail['empname'].$text." from the group ".$itemDetail['groupname']);
		return $status;
	}
	
	function Addcontacts(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if (($handle = fopen($_FILES['filename']['tmp_name'],"r")) !== FALSE){
			$i=0;
			$err=0;
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE){
				if($i>0){
					$c = 0;
					$sql=$this->db->query("SELECT contact_no from ".$bid."_obc_contacts where contact_no='".$data['2']."' and gid='".$this->input->post('ogid')."'");
					if($sql->num_rows()==0){
						$obcgrcnt_use = $this->configmodel->obcusageCheck($bid,'contact');
						if($obcgrcnt_use['type'] == 1 && $obcgrcnt_use['used'] == 0 && $id == ''){
							return '2';
						}else{
							$conid=$this->db->query("SELECT COALESCE(MAX(`conid`),0)+1 as id FROM ".$bid."_obc_contacts")->row()->id;
							$this->db->set('conid',$conid);
							$this->db->set('bid',$bid);
							$this->db->set('gid',$this->input->post('ogid'));
							$this->db->set('name',$data['0']);
							$this->db->set('email',$data['1']);
							$this->db->set('contact_no',$data['2']);
							$this->db->set('status',1);
							$this->db->insert($bid."_obc_contacts");
							if($obcgrcnt_use['type'] == 1)
							$query=$this->db->query("UPDATE business_obc_use SET `cntlimit`=(`cntlimit` -1) WHERE bid='".$bid."'");
							$this->auditlog->auditlog_info('Contact',$data['0']. " New Contact added to Outbound call Contact List ");
						}
					}else{
						$err++;	
					}	
				}else{
					$i++;
					$fields = $data;
				}
			}
		}if($err>1){
			return false;
		}else{
			return true;
		}
	}
	function get_condetails($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("SELECT * FROM ".$bid."_obc_contacts where contact_no='".$id."'");
		return $sql->result_array();
	}
	function editcontact($conid=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if($conid!=""){
			$res=$this->get_condetails($conid);
			$this->db->set('name',$this->input->post('name'));
			$this->db->set('email',$this->input->post('email'));
			$this->db->where('contact_no',$conid);
			$this->db->update($bid."_obc_contacts");
			if(isset($_POST['custom'])){
				$arrs=array_keys($_POST['custom']);
				for($k=0;$k<sizeof($arrs);$k++){
					if(is_array($_POST['custom'][$arrs[$k]])){
						$x=implode(",",$_POST['custom'][$arrs[$k]]);
					}else{
						$x=$_POST['custom'][$arrs[$k]];
					}
					$this->db->query("DELETE FROM ".$bid."_customfieldsvalue WHERE bid= '".$bid."' AND modid= '23' AND fieldid = '".$arrs[$k]."' and dataid= '".$contid."'");
					$sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
						 bid			= '".$bid."'
						,modid			= '23'
						,fieldid		= '".$arrs[$k]."'
						,dataid			= '".$contid."'
						,value			= '".$x."'";
					$this->db->query($sql);
				}
			}
			$this->auditlog->auditlog_info('Contact',$this->input->post('name'). " Contact Updated ");
			return true;
		}else{
			$mobno=$this->db->query("SELECT contact_no FROM ".$bid."_obc_contacts where contact_no='".$this->input->post('contact_no')."' AND gid='".$this->input->post('ogid')."' ");
			if($mobno->num_rows()>0) return 2;
			$check=$this->db->query("SELECT conid FROM ".$bid."_obc_contacts WHERE conid=".$this->input->post('contact_no')." AND gid='".$this->input->post('ogid')."'");
			if($check->num_rows()==0){
				$obcgrcnt_use = $this->configmodel->obcusageCheck($bid,'contact');
				if($obcgrcnt_use['type'] == 1 && $obcgrcnt_use['used'] == 0 && $id == ''){
					return '2';
				}else{
					$conid=$this->db->query("SELECT COALESCE(MAX(`conid`),0)+1 as id FROM ".$bid."_obc_contacts")->row()->id;
					$this->db->set('conid',$conid);
					$this->db->set('bid', $bid);
					$this->db->set('gid',$this->input->post('ogid'));
					$this->db->set('name',$this->input->post('name'));
					$this->db->set('email',$this->input->post('email'));
					$this->db->set('contact_no',$this->input->post('contact_no'));
					$this->db->set('status','1');
					$this->db->insert($bid."_obc_contacts");
					if($obcgrcnt_use['type'] == 1)
						$query=$this->db->query("UPDATE business_obc_use SET `cntlimit`=(`cntlimit` -1) WHERE bid='".$bid."'");
					if(isset($_POST['custom']))
					{
						$arrs=array_keys($_POST['custom']);
						for($k=0;$k<sizeof($arrs);$k++)
						{
							if(is_array($_POST['custom'][$arrs[$k]]))
							{
								$x=implode(",",$_POST['custom'][$arrs[$k]]);
							}
							else
							{
								$x=$_POST['custom'][$arrs[$k]];
							}
							$this->db->query("DELETE FROM ".$bid."_customfieldsvalue WHERE bid= '".$bid."' AND modid= '23' AND fieldid = '".$arrs[$k]."'");
							$sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
									 bid			= '".$bid."'
									,modid			= '23'
									,fieldid		= '".$arrs[$k]."'
									,dataid			= '".$this->input->post('number')."'
									,value			= '".$x."'";
							$this->db->query($sql);
						}
					}
					$this->auditlog->auditlog_info('Contact',$this->input->post('name'). " New Contact added to Contact List ");
					return 1;
				}
			}
		}
	}
	function listcontacts($ofset='0',$limit='20'){
		$res=array();
		$q = ' WHERE 1';
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
		$q.=(isset($s['name']) && $s['name']!='')?" and name like '%".$s['name']."%'":"";
		$q.=(isset($s['contact_no']) && $s['contact_no']!='')?" and contact_no like '%".$s['contact_no']."%'":"";
		$q.=(isset($s['email']) && $s['email']!='')?" and email like '%".$s['email']."%'":"";
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM ".$bid."_obc_contacts $q LIMIT $ofset,$limit")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	function getGroups(){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$res=array();	
		$sql=$this->db->query("SELECT * FROM ".$bid."_obc_groups WHERE status=1");
		$res['']=$this->lang->line('level_select');
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $r)	{
				$res[$r['gid']]=$r['groupname'];
			}
		}
		return $res;
	}
	
	function deleteContact($conid){
		$sql=$this->db->query("DELETE FROM ".$this->session->userdata('bid')."_obc_contacts WHERE conid=".$conid);
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$this->db->query("UPDATE  business_obc_use SET `cntlimit`=(`cntlimit`+1) WHERE bid='".$bid."'");
		$this->auditlog->auditlog_info('Outbound call Group Contact',"Contact is Removed From the Outbound call Group ");
		return true;
	}
	function get_cnt_list(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();
		$sql=$this->db->query("SELECT obc.conid,obc.name,obc.contact_no 
							   FROM ".$bid."_obc_contacts obc
							   WHERE obc.status!='2'
							   ORDER BY obc.`name`");
		foreach($sql->result_array() as $re)
		$res[$re['conid']]=$re['name'].' ['.$re['contact_no'].']';
		return $res;
	}
	function addobc_grpcnt($ogid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$err=0;
		foreach($_POST['con_ids'] as $conid){
			$check=$this->db->query("SELECT obccid FROM ".$bid."_obc_grpcnt WHERE conid=".$conid." AND gid='".$ogid."'");
			if($check->num_rows()==0){
				$obcgrcnt_use = $this->configmodel->obcusageCheck($bid,'contact');
				if($obcgrcnt_use['type'] == 1 && $obcgrcnt_use['used'] == 0 && $id == ''){
					return '2';
				}else{
					$err++;
					$this->db->set('bid', $bid);                       
					$this->db->set('gid', $ogid);                       
					$this->db->set('conid', $conid);                     
					$this->db->set('status',1); 
					$this->db->insert($bid."_obc_grpcnt");
					if($obcgrcnt_use['type'] == 1)
						$query=$this->db->query("UPDATE business_obc_use SET `cntlimit`=(`cntlimit` -1) WHERE bid='".$bid."'");
				}
			}
		}
		if($err!=0){
			return 1;
		}else{
			return 0;
		}
	}
	function obcgrpcnt_existed($ogid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();
		$res1=$this->db->query("SELECT SQL_CALC_FOUND_ROWS g.groupname, c.name, a.conid, a.gid, a.status
							    FROM ".$bid."_obc_grpcnt a
								LEFT JOIN ".$bid."_obc_groups g ON a.gid = g.gid
								LEFT JOIN ".$bid."_obc_contacts c ON a.conid = c.conid
								WHERE a.gid =$ogid");
		if($res1->num_rows()>0){					   
			foreach($res1->result_array() as $row){
				$res[]=$row['conid'];
			}
		}
		return $res;
	}
	function obcgrpcntlist($ogid,$ofset='0',$limit='20'){
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
		$q.=(isset($s['name']) && $s['name']!='')?" AND oc.name = '".$s['name']."'":"";
		$q.=(isset($s['email']) && $s['email']!='')?" AND oc.email = '".$s['email']."'":"";
		$q.=(isset($s['contact_no']) && $s['contact_no']!='')?" AND oc.contact_no = '".$s['contact_no']."'":"";
		$res=array();
		$res['data']=$this->db->query("
									SELECT SQL_CALC_FOUND_ROWS og.groupname, oc.name,oc.email,oc.contact_no,oc.conid, og.gid, oc.status
									FROM  ".$bid."_obc_groups og 
									LEFT JOIN ".$bid."_obc_contacts oc ON og.gid = oc.gid
									WHERE oc.gid =$ogid $q
									ORDER BY oc.`name`
									LIMIT $ofset,$limit
									")->result_array();
       $res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
	   return $res;
	}
	function obcgrpcnt_dis($conid,$ogid,$bid){
		$check=$this->db->query("SELECT * FROM ".$bid."_obc_contacts WHERE conid=".$conid." AND gid=$ogid")->row_array();
		$status=($check['status']==0)?'1':'0';
		$this->db->set('status',$status);
		$this->db->where('conid',$conid);	
		$this->db->where('gid',$ogid);
		$this->db->update($bid.'_obc_contacts');
		$itemDetail= $this->configmodel->getDetail('45',$ogid,'',$bid);
		$text=($status)?" Enabled":" Disabled";
		$this->auditlog->auditlog_info('Group Contact', $itemDetail['name'].$text." from the group ".$itemDetail['groupname']);
		return $status;
	}
	function del_obcgrpcnt($id,$ogid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("DELETE FROM ".$bid."_obc_grpcnt WHERE conid=$id AND gid=$ogid");
		$obccnt_use = $this->configmodel->obcusageCheck($bid,'employee');
		if($obccnt_use['type'] == 1)
			$this->db->query("UPDATE  business_obc_use SET `cntlimit`=(`cntlimit`+1) WHERE bid='".$bid."'");
		$itemDetail= $this->configmodel->getDetail('45',$ogid,'',$bid);
		$gname=$this->db->query("SELECT groupname FROM	".$bid."_obc_groups WHERE gid='".$ogid."'")->row()->groupname;
		$this->auditlog->auditlog_info('Outbound call Group Contact',$itemDetail['name']." is Removed From the Outbound call Group ".$gname);
		return true;
	}
	function getobcGroupEmpDetail($gid='',$eid=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();
		$res=$this->db->query("SELECT a.*,e.empname
							   FROM ".$bid."_obc_grpemp a
							   LEFT JOIN ".$bid."_employee e ON a.eid=e.eid
							   LEFT JOIN ".$bid."_obc_groups g ON g.gid=a.gid
							   WHERE a.gid='".$gid."' AND a.eid='".$eid."'")->result_array();
		return $res['0'];
	}
	function editemp_group(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$rule = $this->db->query("SELECT group_rule FROM ".$bid."_obc_groups where gid='".$_POST['gid']."'")->row()->group_rule;
		$cnt = ($rule=='1')
			? $this->db->query("SELECT COALESCE(max(callcounter),0) as cnt FROM ".$bid."_obc_grpemp where gid='".$_POST['gid']."'")->row()->cnt
			: '0';

		$sql = "UPDATE ".$bid."_obc_grpemp SET
				weight	= '".(isset($_POST['weight'])?$_POST['weight']:0)."'
				WHERE gid		= '".$_POST['gid']."'
				AND  eid		= '".$_POST['empid']."'";
		$this->db->query($sql);
		$emp_name=$this->get_empname($_POST['empid']);
		$gname=$this->db->query("SELECT groupname from 	".$bid."_obc_groups where gid='".$_POST['gid']."'")->row()->groupname;
		$this->auditlog->auditlog_info('Group Employee',$emp_name->empname." weight changed for the group ".$gname);
		return 1;
	}
	function employee_list($grid=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res = ($grid=='') ? array(''=>'Select Employee') : array();
		$query=$this->db->query("select eid,empname from ".$bid."_employee where status=1 ORDER BY empname");
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q='';
		$q.=($roleDetail['role']['owngroup']=='1' && $roleDetail['role']['admin']!='1') ? " AND (g.eid = '".$this->session->userdata('eid')."' OR e.eid='".$this->session->userdata('eid')."')":"";
		$q .= ($grid=='') ? '' : " AND g.gid='".$grid."'";
		$query = ($grid=='') ? "SELECT * FROM ".$bid."_employee WHERE status='1' ORDER BY empname" : "SELECT e.* FROM ".$bid."_employee e
								 LEFT JOIN ".$bid."_group_emp ge ON e.eid=ge.eid
								 LEFT JOIN ".$bid."_groups g ON g.gid=ge.gid
								 WHERE e.status=1 ".$q." ORDER BY e.empname";
		$query=$this->db->query($query);
		if($query->num_rows()>0){
			foreach($query->result_array() as $rt)
			$res[$rt['eid']]=$rt['empname'];
		}
		return $res;
	}
}
?>
