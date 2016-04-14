<?php
Class Activitymodel extends Model{
	
	function Activitymodel(){
		parent::Model();
		$this->load->model('auditlog');
		$this->load->model('configmodel');
		$this->load->model('commonmodel');
		$this->load->model('emailmodel');
		$this->load->model('profilemodel');
	}
	function create_activitygroup($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$arr=array_keys($_POST);
		
		$this->db->set('groupname',$this->input->post('groupname'));
		$this->db->set('number',$_POST['number']);
		$this->db->set('status','1');
		if($id!=""){
			$this->db->where('id',$id);
			$this->db->update($bid."_activitygroup"); 
		}else{
			$id=$this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM ".$bid."_activitygroup")->row()->id;
			$this->db->set('id',$id);
			$this->db->insert($bid."_activitygroup"); 
		}
		if($_POST['oldprinumber']!=$_POST['number']){
			$sql=$this->db->query("update prinumbers set status=0,associateid=0 where number='".$_POST['oldprinumber']."'");
			$this->ivrsmodel->updatePri($_POST['number'],1,$bid,4,$id);
		}elseif($id==''){
			$this->ivrsmodel->updatePri($_POST['number'],1,$bid,4,$id);
		}
		//$gid=$this->db->insert_id();
		//$this->auditlog->auditlog_info($this->lang->line('level_module_group'),"Created New Group  ".$this->input->post('groupname'));
		
		if(isset($_POST['custom'])){
			$arrs=array_keys($_POST['custom']);
			for($k=0;$k<sizeof($arrs);$k++){
				
				if(is_array($_POST['custom'][$arrs[$k]])){
						$x=implode(",",$_POST['custom'][$arrs[$k]]);
					}
					else{
						$x=$_POST['custom'][$arrs[$k]];
					}
					$this->db->query("DELETE FROM ".$bid."_customfieldsvalue where bid= '".$bid."' and modid= '31' and fieldid		= '".$arrs[$k]."' and dataid= '".$gid."'");
					$sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
						 bid			= '".$bid."'
						,modid			= '31'
						,fieldid		= '".$arrs[$k]."'
						,dataid			= '".$gid."'
						,value			= '".$x."'";
					$this->db->query($sql);
				}
			}
		
		return $id;	
	}
	function activityList($bid,$ofset='0',$limit='20',$ss){
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['gname']) && $s['gname']!='')?" and g.groupname like '%".$s['gname']."%'":"";
		
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		
	//	$q.=($roleDetail['role']['owngroup']=='1' && $roleDetail['role']['admin']!='1') ? " AND g.eid = '".$this->session->userdata('eid')."'":"";
		
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$sql="SELECT SQL_CALC_FOUND_ROWS g.id,g.number,p.landingnumber
						  FROM ".$bid."_activitygroup g 
						  LEFT JOIN prinumbers p on g.number=p.number
						  WHERE g.status!=0 $q limit $ofset,$limit";
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
		$fieldset = $this->configmodel->getFields('31',$bid);
		$keys = array();
		$header = array('#');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					if(!in_array($field['fieldname'],array('hdayaudio','greetings','url'))){
						array_push($keys,$field['fieldname']);
						array_push($header,(($field['customlabel']!="")
											?$field['customlabel']
											:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
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
		if($opt_add || $opt_view || $opt_delete)
			array_push($header,$this->lang->line('level_Action'));
		$ret['header'] = $header;
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$data = array($i);
			$r = $this->configmodel->getDetail('31',$rec['id'],'',$bid);
			$r['number']=$rec['landingnumber'];
			foreach($keys as $k){
				$v = isset($r[$k])?$r[$k]:"";
				array_push($data,$v);
			}
			
			if($opt_add || $opt_view || $opt_delete){
				$act = "";
				if($ss!=0){
					$act .= ($opt_add) ? '<a href="activity/addactivity/'.$r['id'].'"><span title="Edit" class="fa fa-edit"></span></a>':'';
					$act .= ($opt_add) ? ' <a href="'.base_url().'activity/custom_activity/'.$r['id'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="glyphicon glyphicon-plus-sign" title="Add Custom"></span></a>':'';
					$act .= ($opt_add) ? ' <a href="'.base_url().'activity/list_Activity/'.$r['id'].'"><span title="List Activity" class="glyphicon glyphicon-user"></span></a>':'';
					//~ $act .= ($opt_add) ? ' <a href="'.base_url().'activity/activity_member/'.$r['id'].'" class="callPopup" ><img src="system/application/img/icons/addtogroup.png" title="Add Activity Member"  /></a>':'';
					//~ $act .= ($opt_add) ? ' <a href="'.base_url().'activity/listactivity_member/'.$r['id'].'" class="callPopup" ><img src="system/application/img/icons/list.png" title="Add Activity Member"  /></a>':'';
				}else{
					$act .= ($opt_add) ? '<a href="activity/reportList/'.$r['id'].'"><span title="Report" class="fa fa-list-ul"></span></a>':'';
				}
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		//$ret['addlink']="group/add_group";
		
		return $ret;
	} 
	function AddCustom($agid,$actid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if(isset($actid) && $actid==NULL){
			$actid=$this->db->query("SELECT COALESCE(MAX(`acid`),0)+1 as id FROM ".$this->session->userdata('bid')."_activity")->row()->id;
			$this->db->set('acid',$actid);
			$this->db->set('agid',$agid);
			$this->db->set('activity_name',$_POST['acname']);
			$this->db->set('keyword',$_POST['kw']);
			$this->db->set('status','1');
			$this->db->insert($bid."_activity");
		 }	
		foreach($_POST['cust'] as $key => $rs){
			$fieldid=$this->db->query("SELECT COALESCE(MAX(`fieldid`),0)+1 as id FROM ".$this->session->userdata('bid')."_customactivity")->row()->id;
			$this->db->set('fieldid',$fieldid);
			if($_FILES['cust']['error'][$key]['fname']==0){
				$ext=pathinfo($_FILES['cust']['name'][$key]['fname'],PATHINFO_EXTENSION);
				$newName = "GAC".uniqid().".".$ext;
				move_uploaded_file($_FILES['cust']['tmp_name'][$key]['fname'],$this->config->item('sound_path').$newName);
				$this->db->set('file',$newName);
			}
			$req = isset($rs['isreq']) ? $rs['isreq'] : '0';
			$this->db->set('actid',$actid);
			$this->db->set('agid',$agid);
			$this->db->set('fieldname',$rs['lname']);
			$this->db->set('fieldtype','text');
			$this->db->set('order',$rs['order']);
			$this->db->set('vtype',$rs['ftype']);
			$this->db->set('is_required',$req);
			$this->db->insert($bid."_customactivity");
		}
		return true;
	}
	function getCustomFields($agid,$actid){
		//$cbid=$this->session->userdata('cbid');
		$bid=$this->session->userdata('bid');
		$sql="SELECT ac.* FROM ".$bid."_customactivity ac
			  where ac.agid='".$agid."' and ac.actid ='".$actid."' order by ac.order asc";
		return $this->db->query($sql)->result_array();	  
	}
	
	function getCustomFieldsVal($agid,$dataid,$actid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql="SELECT v.* FROM ".$bid."_customactivityvalue v
			  LEFT JOIN ".$bid."_customactivity ac on v.agid=ac.agid
			  where v.agid='".$agid."' and v.actid='".$actid."' AND v.dataid='".$dataid."' GROUP BY v.fieldid";
		$rst = $this->db->query($sql)->result_array();	  
		$ret = array();
		foreach($rst as $rec){
			$ret['cust['.$rec['fieldid'].']'] = $rec['value'];
		}
		return $ret;
	}
	
	function activityReport($bid,$ofset='0',$limit='20',$agid,$actid){
		
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$q.=($roleDetail['role']['owngroup']=='1' && $roleDetail['role']['admin']!='1') ? " AND e.eid = '".$this->session->userdata('eid')."'":"";		
		$custiom_ids=$this->configmodel->customSearch((isset($s['custom']))?$s['custom']:'','32',$bid);	
		$precustom=$this->preCustom((isset($s['cus']))?$s['cus']:'',$agid,$bid);	
		$q.=(isset($s['aeid']) && $s['aeid']!='')?" and e.empname like '%".$s['aeid']."%'":"";			
		$q.=(isset($s['agid']) && $s['agid']!='')?" and ac.groupname like '%".$s['agid']."%'":"";	
		$q.=(strlen($custiom_ids)>1)?" and a.aid in(".$custiom_ids.")":"";
		$q.=(!$custiom_ids)?" AND 0 ":"";		
		$q.=(strlen($precustom)>1)?" and a.aid in(".$precustom.")":"";
		$q.=(!$precustom)?" AND 0 ":"";	
		
			
		$sql="SELECT SQL_CALC_FOUND_ROWS a.aid,e.empname,ac.groupname,at.activity_name from ".$bid."_activityreport a
			  left join ".$bid."_employee e on a.eid=e.eid
			  left join ".$bid."_activitygroup ac on a.agid=ac.id
			  left join ".$bid."_activity at on a.actid=at.acid and a.agid=at.agid
			  where a.agid=".$agid." and a.actid=".$actid." $q ORDER BY a.aid DESC  limit $ofset,$limit";
		//echo $sql;exit;	  
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='32'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('32',$bid);
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
		$ActCustom=$this->getCustomFields($agid,$actid);
		if(!empty($ActCustom)){
			foreach($ActCustom as $field){
				array_push($header,$field['fieldname']);
				array_push($keys,'cust['.$field['fieldid'].']');
				
			}
			
		}	
		array_push($header,'Action');
		$list = array();
		$i = $ofset+1;
		
		foreach($rst as $rec){
			$data = array($i);
			$r = $this->configmodel->getDetail('32',$rec['aid'],'',$bid);
			$x = $this->getCustomFieldsVal($agid,$rec['aid'],$actid);
			//echo "<pre>";print_r($x);echo "</pre>";
			$r['aeid']=$rec['empname'];
			$r['agid']=$rec['groupname'];
			$r['actid']=$rec['activity_name'];
			//$l=0;
			foreach($keys as $k){
				
				$v = isset($r[$k])?$r[$k]:(isset($x[$k])?$x[$k]:"");
				array_push($data,$v);
				
			}
			$v = '<a href="activity/edit_activityReport/'.$rec['aid'].'"><span title="Edit" class="fa fa-edit"></span></a>';
					array_push($data,$v);
			$i++;
			array_push($list,$data);
			
		}
		
		
		$ret['header'] = $header;
		$ret['rec'] = $list;
		return $ret;
	}
	function getActivityMembers($actid,$agid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("SELECT a.*,e.empname,e.empnumber FROM ".$bid."_activitymembers a
								left join ".$bid."_employee e on a.eid=e.eid
								where a.agid='".$actid."' and a.actid='".$agid."'");
		$ret=array();
		foreach($sql->result_array() as $row){
			$ret[$row['eid']]=$row['empname'];
		}						
		return $ret;
	}
	function Addactivity_member($actid,$agid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if(sizeof($_POST['eid'])>0){
		foreach($_POST['eid'] as $eid){
			$this->db->set('agid',$actid);
			$this->db->set('actid',$agid);
			$this->db->set('eid',$eid);
			$this->db->set('status','1');
			$this->db->insert($bid."_activitymembers");
		}
	  }
	  return true;
	}
	function activityemplist($actid,$agid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();
		$res['data']=$this->db->query("select SQL_CALC_FOUND_ROWS ac.groupname,e.empname,e.eid,a.agid,a.actid from ".$bid."_activitymembers a
									  left join	".$bid."_activitygroup ac on a.agid=ac.id
									  left join ".$bid."_employee e on a.eid=e.eid
									  where a.agid='".$actid."' and a.actid='".$agid."' ")->result_array();
       $res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	}
	function del_actemp($empid,$agid,$actid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("DELETE FROM ".$bid."_activitymembers where eid='".$empid."' and agid='".$agid."' and actid='".$actid."'");
		return true;
	}
	function editReportAct($aid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$arrs=array_keys($_POST['cus']);
		foreach($_POST['cus'] as $keys=>$post){
			 $sql = "REPLACE INTO ".$bid."_customactivityvalue SET
					fieldid		= '".$keys."'
					,agid			= '".$this->input->post('agid')."'
					,actid			= '".$this->input->post('actid')."'
					,dataid			= '".$aid."'
					,value			= '".$post[0]."'";
				$this->db->query($sql);
		}		
		return true;
	}
	function preCustom($search,$rid,$bid){
		if(!empty($search)){
		$sql="SELECT * from ".$bid."_customactivityvalue where agid ='".$rid."'";
		$data = false;
			$ids = array();
		foreach($search as $key=>$val){
				if($val!=''){
					if($data!=true) $data = true;
					$q = $sql." AND (fieldid='".$key."' AND value like '%".$val."%')";
					foreach($this->db->query($q)->result_array() as $id){
						$ids[] = "'".$id['dataid']."'";
					}
				}
			}
		$ids = array_unique($ids);
			return (count($ids)>0) ? implode(",",$ids) : (($data==true) ? false : true);
		}return true;	
		
	}
	function downloadact_Csvs($agid,$actid){
		$res=array();
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q= " WHERE 1 ";
		$limit = $roleDetail['role']['recordlimit'];

		////////////////////////////////////
		$csv_output = "";
		$ke=array();
		foreach($_POST['lisiting'] as $key=>$val){
			//~ 
			if($key=='custom'){
				foreach($val as $key=>$val){
					$header[]=$val;
					$hkey[]='custom['.$key.']';
				}
			}elseif($key=='cus'){
				foreach($val as $key=>$val){
					$header[]=$val;
					$hkey[]='cust['.$key.']';
				}
			}else{
				$hkey[]=$key;
				$header[]=$val;
			}
		}
		
		//print_r($hkey);exit;
		$csv_output .=implode(",",$header)."\n";
		
		$sql="SELECT SQL_CALC_FOUND_ROWS a.aid,e.empname,ag.groupname,ac.activity_name,a.datetime from ".$bid."_activityreport a
			  left join ".$bid."_employee e on a.eid=e.eid
			  left join ".$bid."_activitygroup ag on a.agid=ag.id
			  left join ".$bid."_activity ac on a.agid=ac.agid and a.actid=ac.acid
			  where a.agid='".$agid."' and a.actid='".$actid."' ORDER BY a.aid DESC limit 0,$limit";
		$rst = $this->db->query($sql)->result_array();
		$name = $bid.'_'.time();
		mkdir('reports/'.$name);
		chmod('reports/'.$name,0777);
		$files = array();
		foreach($rst as $rec){
			$data = array();
			$r = $this->configmodel->getDetail('32',$rec['aid'],'',$bid);
			$x = $this->getCustomFieldsVal($agid,$rec['aid'],$actid);
			
			$r['aeid']=$rec['empname'];
			$r['agid']=$rec['groupname'];
			$r['actid']=$rec['activity_name'];
			$i=0;
			foreach($hkey as $k){
			
				$v = isset($r[$k])?$r[$k]:(isset($x[$k])?$x[$k]:"");
				array_push($data,$v);
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
	function activity_createdList($agid,$bid,$ofset='0',$limit='20'){
		$res=array();
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['acname']) && $s['acname']!='')?" and activity_name like '%".$s['acname']."%'":"";	
		$sql="SELECT SQL_CALC_FOUND_ROWS *  FROM ".$bid."_activity where agid='".$agid."' $q LIMIT $ofset,$limit" ;
        $res['data'] = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$res['count'] = $rst1->row()->cnt;
		return $res;
	}
	function activityCustom($id,$bid){
		$res=array();
		$sql="SELECT SQL_CALC_FOUND_ROWS *  FROM ".$bid."_customactivity where actid='".$id."'" ;
        $res['data'] = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$res['count'] = $rst1->row()->cnt;
		return $res;
	}
	function delcust($actid,$field,$bid){
		$sql=$this->db->query("delete from ".$bid."_customactivity where actid='".$actid."' and fieldid='".$field."'");
		return true;
	}
}
/* end  */
