<?php
class Scnmodel extends Model {
	var $data;
    function Scnmodel(){
        parent::Model();
    }
	function addscn(){
		$scnid = (isset($_POST['scnid']) && $_POST['scnid']!='')
					?$_POST['scnid']
					:$this->db->query("SELECT COALESCE(MAX(`scnid`),0)+1 as id FROM `".$this->session->userdata('bid')."_scn`")->row()->id;
		$_POST['prinumber'] = ($_POST['prinumber']!='0') ? $_POST['prinumber']:$this->getFreePri();
		if($_POST['scnid']==''){
			$sql = "INSERT INTO ".$this->session->userdata('bid')."_scn SET
					 scnid			= '".$scnid."'
					,bid			= '".$this->session->userdata('bid')."'
					,prinumber		= '".$_POST['prinumber']."'";
					 
			$sql .=	isset($_POST['title']) ? ",title			= '".$_POST['title']."'":"";
			
			$this->db->query($sql);
			//$this->auditlog->auditlog_info($this->lang->line('level_module_Ivrs'),$_POST['title']." Added successfully");
			$this->updatePri($_POST['prinumber'],'1',$this->session->userdata('bid'),'3',$scnid);
		}else{
			if(isset($_POST['prinumber']))$this->updatePri($this->db->query("SELECT prinumber FROM `".$this->session->userdata('bid')."_scn` WHERE scnid = '".$_POST['scnid']."'")->row()->prinumber);
			$sql  = "UPDATE ".$this->session->userdata('bid')."_scn SET bid	= '".$this->session->userdata('bid')."'";
			$sql .=	isset($_POST['title']) ? ",title			= '".$_POST['title']."'":"";
			$sql .=	isset($_POST['prinumber'])? ",prinumber		= '".$_POST['prinumber']."'":"";
			$sql .=	" WHERE scnid = '".$_POST['scnid']."'";
			$this->db->query($sql);
			$this->updatePri($_POST['prinumber'],'1',$this->session->userdata('bid'),'3',$scnid);
		}
		
		if(isset($_POST['custom']))
		foreach($_POST['custom'] as $fid=>$val){
			$sql = "REPLACE INTO ".$this->session->userdata('bid')."_customfieldsvalue SET
					 bid			= '".$this->session->userdata('bid')."'
					,modid			= '".$_POST['modid']."'
					,fieldid		= '".$fid."'
					,dataid			= '".$scnid."'
					,value			= '".(is_array($val)?implode(',',$val):$val)."'";
			$this->db->query($sql);
		}
		
		return array('scnid'=>$scnid);
	}

	function getFreePri(){
		$sql = "SELECT * FROM dummynumber WHERE status='0' AND bid='0' LIMIT 0,1";
		$rst = $this->db->query($sql);
		$rec = $rst->result_array();
		return $rec[0]['number'];
	}

	function updatePri($pri,$status=0,$bid=0,$type=0,$assid=0){
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
					,type		= '".$type."'
					,associateid= '".$assid."'
					WHERE number= '".$pri."'";
			//,bid		= '".$bid."'
		}
		
		$this->db->query($sql);
	}
	function getScnlist($bid,$ofset='0',$limit='20'){
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
		$con .= (isset($s['title']) && $s['title']!='')?" AND s.title like '%".$s['title']."%'":"";
		$con .= (isset($s['prinumber']) && $s['prinumber']!='')?" AND p.landingnumber like '%".$s['prinumber']."%'":"";
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS s.scnid,p.landingnumber as number FROM ".$this->session->userdata('bid')."_scn s
				LEFT JOIN prinumber p on s.prinumber=p.number
				WHERE s.status='1' AND s.bid='".$bid."' ".$con."
				ORDER BY s.scnid DESC
				limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		
		$opt_add 	= $roleDetail['modules']['21']['opt_add'];
		$opt_view 	= $roleDetail['modules']['21']['opt_view'];
		$opt_delete = $roleDetail['modules']['21']['opt_delete'];
		
		$fieldset = $this->configmodel->getFields('21');
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
			$r = $this->configmodel->getDetail('21',$rec['scnid']);
			foreach($keys as $k){
					$v = isset($r[$k])?$r[$k]:"";
					array_push($data,$v);
			}
			$act = "";
			if($opt_add || $opt_view || $opt_delete){
				$act .= ($opt_add)? ' <a href="scn/addpopup/'.$r['scnid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Edit" class="fa fa-edit"></span></a>':'';
				//$act .= ($opt_delete)? ' <a href="scn/deletescn/'.$r['scnid'].'" class="confirm deleteClass"><img src="system/application/img/icons/delete.png" title="Delete" /></a>':'';
				$act .= ($opt_add)? ' <a href="scn/member/'.$r['scnid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="fa fa-plus" title="Add Option" ></span></a>':'';
				$act .= ($opt_add)? ' <a href="scn/members/'.$r['scnid'].'"><span title="List Option" class="fa fa-list-ul"></span></a>':'';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	} 
	function getMembers($args){
		foreach($args as $k=>$v) $$k = $v;
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
		$con .= (isset($s['exten']) && $s['exten']!='')?" AND exten = '".$s['exten']."'":"";
		$con .= (isset($s['mnumber']) && $s['mnumber']!='')?" AND mnumber like '%".$s['mnumber']."%'":"";
		$con .= (isset($s['name']) && $s['name']!='')?" AND name like '%".$s['name']."%'":"";
		$con .= (isset($s['email']) && $s['email']!='')?" AND email like '%".$s['email']."%'":"";
		$con .= (isset($s['dob']) && $s['dob']!='')?" AND dob like '%".$s['dob']."%'":"";
		$con .= (isset($s['profession']) && $s['profession']!='')?" AND profession like '%".$s['profession']."%'":"";
		$con .= (isset($s['city']) && $s['city']!='')?" AND city like '%".$s['city']."%'":"";
		$con .= (isset($s['locality']) && $s['locality']!='')?" AND locality like '%".$s['locality']."%'":"";
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS mid FROM ".$this->session->userdata('bid')."_scnmembers
				WHERE status='1' AND scnid='".$scnid."' ".$con."
				ORDER BY exten ASC
				limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		
		$opt_add 	= $roleDetail['modules']['22']['opt_add'];
		$opt_view 	= $roleDetail['modules']['22']['opt_view'];
		$opt_delete = $roleDetail['modules']['22']['opt_delete'];
		
		$fieldset = $this->configmodel->getFields('22');
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
			$r = $this->configmodel->getDetail('22',$rec['mid']);
			foreach($keys as $k){
					$v = isset($r[$k])?$r[$k]:"";
					array_push($data,$v);
			}
			$act = "";
			if($opt_add || $opt_view || $opt_delete){
				$act .= ($opt_add)? ' <a href="scn/member/'.$r['scnid'].'/'.$r['mid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Edit" class="fa fa-edit"></span></a>':'';
				$act .= ($opt_delete)? ' <a href="scn/deletemem/'.$r['mid'].'" class="confirm deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	} 
	
	function deleteScn($args){
		$detail = $this->configmodel->getDetail('21',$args['scnid']);
		$this->updatePri($detail['prinumber']);
		$this->db->query("UPDATE ivrs SET
						  status='2'
						  WHERE ivrsid = '".$args['ivrsid']."' 
						  AND bid='".$this->session->userdata('bid')."'");
		//$this->auditlog->auditlog_info($this->lang->line('level_module_Ivrs'),$detail['title']." Is deleted ");				  
	}
	function addMember(){
		$mid = (isset($_POST['mid']) && $_POST['mid']!='')
					?$_POST['mid']
					:$this->db->query("SELECT COALESCE(MAX(`mid`),0)+1 as id FROM `".$this->session->userdata('bid')."_scnmembers`")->row()->id;
		if($_POST['mid']==''){
			$sql = "INSERT INTO ".$this->session->userdata('bid')."_scnmembers SET
					 mid			= '".$mid."'
					,scnid			= '".$_POST['scnid']."'
					,exten			= '".$_POST['exten']."'
					,mnumber			= '".$_POST['mnumber']."'";
					 
			$sql .=	isset($_POST['name']) ? ",name				= '".$_POST['name']."'":"";
			$sql .=	isset($_POST['email']) ? ",email			= '".$_POST['email']."'":"";
			$sql .=	isset($_POST['dob']) ? ",dob				= '".$_POST['dob']."'":"";
			$sql .=	isset($_POST['profession']) ? ",profession	= '".$_POST['profession']."'":"";
			$sql .=	isset($_POST['city']) ? ",city				= '".$_POST['city']."'":"";
			$sql .=	isset($_POST['locality']) ? ",locality		= '".$_POST['locality']."'":"";
			
			$this->db->query($sql);
		}else{
			$sql  = "UPDATE ".$this->session->userdata('bid')."_scnmembers SET  mid	= '".$mid."'";
			$sql .=	isset($_POST['exten']) ? ",exten			= '".$_POST['exten']."'":"";
			$sql .=	isset($_POST['mnumber'])? ",mnumber		= '".$_POST['mnumber']."'":"";
			$sql .=	isset($_POST['name']) ? ",name				= '".$_POST['name']."'":"";
			$sql .=	isset($_POST['email']) ? ",email			= '".$_POST['email']."'":"";
			$sql .=	isset($_POST['dob']) ? ",dob				= '".$_POST['dob']."'":"";
			$sql .=	isset($_POST['profession']) ? ",profession	= '".$_POST['profession']."'":"";
			$sql .=	isset($_POST['city']) ? ",city				= '".$_POST['city']."'":"";
			$sql .=	isset($_POST['locality']) ? ",locality		= '".$_POST['locality']."'":"";
			$sql .=	" WHERE mid = '".$_POST['mid']."'";
			$this->db->query($sql);
		}
		
		if(isset($_POST['custom']))
		foreach($_POST['custom'] as $fid=>$val){
			$sql = "REPLACE INTO ".$this->session->userdata('bid')."_customfieldsvalue SET
					 bid			= '".$this->session->userdata('bid')."'
					,modid			= '".$_POST['modid']."'
					,fieldid		= '".$fid."'
					,dataid			= '".$mid."'
					,value			= '".(is_array($val)?implode(',',$val):$val)."'";
			$this->db->query($sql);
		}
		
		return ;
	}
	function delMember($mid){
		$sql = "DELETE FROM ".$this->session->userdata('bid')."_scnmembers
				WHERE mid='".$mid."'";
		$this->db->query($sql);
		return;
	}
}
?>
