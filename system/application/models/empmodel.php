<?php
Class Empmodel extends Model{
	var $KeyChar;
	function Empmodel(){
		parent::Model();
		$this->load->model('auditlog');
		$this->load->model('configmodel');
		$this->load->model('commonmodel');
		$this->load->model('emailmodel');
		$this->load->model('profilemodel');
		$this->KeyChar=array(
			'a'=>'2',	'b'=>'2',	'c'=>'2',
			'd'=>'3',	'e'=>'3',	'f'=>'3',
			'g'=>'4',	'h'=>'4',	'i'=>'4',
			'j'=>'5',	'k'=>'5',	'l'=>'5',
			'm'=>'6',	'n'=>'6',	'o'=>'6',
			'p'=>'7',	'q'=>'7',	'r'=>'7',	's'=>'7',
			't'=>'8',	'u'=>'8',	'v'=>'8',
			'w'=>'9',	'x'=>'9',	'y'=>'9',	'z'=>'9',' '=>'0'
		);
		
		
	}
	function add_emp(){
		if($this->email_employee()){
			$cbid=$this->session->userdata('cbid');
			$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
			$arr=array_keys($_POST);
			$eid=$this->db->query("SELECT COALESCE(MAX(`eid`),0)+1 as id FROM ".$bid."_employee")->row()->id;
			$this->db->set('eid',$eid);
			$this->db->set('bid',$bid);
			$name = strtolower($this->input->post('empname'));
			$ds = $this->KeyChar[$name['0']].$this->KeyChar[$name['1']].$this->KeyChar[$name['2']];
			for($i=0;$i<sizeof($arr);$i++){
				if(!in_array($arr[$i],array("update_system","custom","username","password","cpassword","reportto"))){
					if(is_array($_POST[$arr[$i]]))
						 $val = @implode(',',$_POST[$arr[$i]]);
					elseif($_POST[$arr[$i]]!="")
						 $val=$_POST[$arr[$i]];
					else
						$val='';
					$this->db->set($arr[$i],$val);
				}
			}
			$this->db->set('directory_string',$ds);
			if(isset($_POST['empday']))$this->db->set('empday', json_encode($_POST['empday']));
			if(!isset($_POST['tollfree'])) $this->db->set('tollfree', '0');
			if(isset($_POST['reportto'])) $this->db->set('reportto',$_POST['reportto']);
			$this->load->helper('mcube_helper');
			$dnd = (array)filter_dnd($_POST['empnumber']);	
			if($dnd['dnd']==0){
				$this->db->set('dnd','0');
				$this->db->set('verify','1');	
			}else{
				$this->db->set('dnd','1');
				$this->db->set('verify','0');
			}
			$this->db->set('status','3');
			$this->db->insert($bid."_employee");
			$this->auditlog->auditlog_info($this->lang->line('label_Employee'),$this->input->post('empname')." Employee added successfully");
			$id=$this->db->insert_id();
			if(isset($_POST['custom'])){
			$arrs=array_keys($_POST['custom']);
				for($k=0;$k<sizeof($arrs);$k++){
					if(is_array($_POST['custom'][$arrs[$k]])){
						$x=implode(",",$_POST['custom'][$arrs[$k]]);
					}else{
						$x=$_POST['custom'][$arrs[$k]];
					}
					$this->db->query("DELETE FROM ".$bid."_customfieldsvalue 
									where bid= '".$bid."' and modid= '2' 
									and fieldid = '".$arrs[$k]."' and dataid= '".$gid."'");
					$sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
						 bid			= '".$bid."'
						,modid			= '2'
						,fieldid		= '".$arrs[$k]."'
						,dataid			= '".$id."'
						,value			= '".$x."'";
					$this->db->query($sql);
				}
			}
			
			if(isset($_POST['empemail']) && $_POST['empemail']!=''){
				$usertable=$this->db->query("select * from user where username='".$_POST['empemail']."'");
				if($usertable->num_rows()==0){
					$uid=$this->db->query("SELECT COALESCE(MAX(`uid`),0)+1 as id FROM `user`")->row()->id;
					$this->db->set('uid', $uid); 
					$this->db->set('bid', $bid);
					$this->db->set('eid', $eid); 
					$this->db->set('status', '2'); 
					$this->db->set('username',$_POST['empemail']); 
					$this->db->insert('user'); 
				}
			}
			if($this->input->post('empnumber')!='' && ($this->input->post('empname')!='' || $this->input->post('empemail')!='')){
				$data = array(
						'bid'		=>$bid,
						'name'		=>$this->input->post('empname'),
						'number'	=>$this->input->post('empnumber'),
						'email'		=>$this->input->post('empemail'),
						'remarks'	=>''
				);
				$this->configmodel->UpdateContact($data);
			}
			if(isset($_POST['empemail']) && !in_array($bid,array('640'))){
				$body=$this->emailmodel->newEmailBody($this->emailmodel->email_body($this->input->post('empname'),$eid,$bid),$this->input->post('empname'));
				$to  = $this->input->post('empemail'); // note the comma
				$subject = 'Registered Employee Details';
				$from='MCube <noreply@mcube.com>';
				$this->load->library('email');
				$this->email->from('noreply@mcube.com', 'MCube');
				$this->email->to($to);
                $this->email->subject($subject);
				$this->email->message($body);
				$this->email->send();
				//return $id;
				return $eid;
			}
		}else{
			return "Email Exists";	
		}	
	}
	function bulkDel($arr){
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$sql="UPDATE ".$bid."_employee SET status=2 WHERE eid IN(".$arr.")";
		$this->db->query($sql);
		
		for($i=0;$i<$leadcnt;$i++){
			$this->auditlog->auditlog_info('Employee',"Deleted By ".$this->session->userdata('username'));
		}
		return 1;	
	}
	function getDeletedEmplist($bid,$ofset='0',$limit='20'){
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['empemail']) && $s['empemail']!='')?" and a.empemail like '%".$s['empemail']."%'":"";
		$q.=(isset($s['empname']) && $s['empname']!='')?" and a.empname like '%".$s['empname']."%'":"";
		$q.=(isset($s['empnumber']) && $s['empnumber']!='')?" and a.empnumber like '%".$s['empnumber']."%'":"";
		$q.=(isset($s['role']) && $s['role']!='')?" and b.rolename like '%".$s['role']."%'":"";
		$q.=(isset($s['reportto']) && $s['reportto']!='')?" and e.empname like '%".$s['reportto']."%'":"";
		$q.=(isset($s['extension']) && $s['extension']!='')?" and a.extension like '%".$s['extension']."%'":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$sql="select SQL_CALC_FOUND_ROWS a.*,b.rolename  as role 
						  from ".$bid."_employee a 
						  LEFT JOIN ".$bid."_user_role b on a.roleid=b.roleid
						  LEFT JOIN ".$bid."_employee e on a.reportto=e.eid
						  where a.status=2 $q ORDER BY a.`empname` limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='2'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('2',$bid);
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
			$r = $this->configmodel->getDetail('2',$rec['eid'],'',$bid);
				$r['reportto']=$r['reporttoname'];
			foreach($keys as $k){
				$v = isset($r[$k])?$r[$k]:"";
				if($k=='empday' && $r[$k]!=''){
					$empday = json_decode($r[$k]);
					$v = '';
					foreach($empday as $b => $d){ $v .= (isset($d->day) && $d->day=='1')?$b.'='.$d->st.'-'.$d->et.'<br>':'';}}
				array_push($data,$v);
			}
			if($opt_add || $opt_view || $opt_delete){
				$act = '<a href="'.base_url().'Employee/UnDelete_Employee/'.$r['eid'].'" class="deleteClass"><img src="system/application/img/icons/undelete.png" title="Undelete" /></a>
						';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
		
	}
	function getgroups($bid){
	   $res=array();
	   $sql=$this->db->query("SELECT SQL_CALC_FOUND_ROWS g.gid, g.groupname, e.eid
				  FROM ".$bid."_groups g 
				  LEFT JOIN group_rules r on g.rules=r.rulesid 
				  LEFT JOIN ".$bid."_employee e on g.eid=e.eid
				  LEFT JOIN prinumbers p on (g.prinumber=p.number AND p.bid='".$bid."')
				  WHERE g.bid='".$bid."' 
				  and g.status!=0");
	    foreach($sql->result_array() as $re)
		$res[$re['gid']]=$re['groupname'];
		return $res;
	}
		function addemp2groups(){
		$eids = $_POST['empid'];
		$bid = $_POST['bid'];
		$err=0;
		foreach($_POST['grp_ids'] as $id){
		
			$check=$this->db->query("select callid from ".$bid."_group_emp where eid=".$eids." and gid='".$id."'");
			$rule = $this->db->query("SELECT rules FROM ".$bid."_groups where gid='".$id."'")->row()->rules;
		    $cnt = ($rule=='1')? $this->db->query("SELECT COALESCE(max(callcounter),0) as cnt FROM ".$bid."_group_emp where gid='".$id."'")->row()->cnt
			:'0';
			if($check->num_rows()==0){
				$err++;
				$this->db->set('bid', $bid);                       
				$this->db->set('gid', $id);                       
				$this->db->set('eid', $eids); 
				$this->db->set('starttime', $this->input->post('starttime'.$id));                       
				$this->db->set('endtime', $this->input->post('endtime'.$id));                       
				$this->db->set('status',1);
				$this->db->set('callcounter',$cnt);
				
				if($this->input->post('empweight'.$id)){
					$this->db->set('empweight', $this->input->post('empweight'.$id)); 
				}
				if($this->input->post('empPriority'.$id)){
					$this->db->set('empPriority', $this->input->post('empPriority'.$id)); 
				}
				if($this->input->post('isfailover'.$id)){
					$this->db->set('isfailover',$this->input->post('isfailover'.$id));	
				} 
				if($this->input->post('pcode'.$id)){
					$this->db->set('pincode', $this->input->post('pcode'.$id)); 
				} 
				$this->db->insert($bid."_group_emp");
				$emp_name=$this->get_empname($eids);
				$gname=$this->db->query("SELECT groupname from 	".$bid."_groups where gid='".$id."'")->row()->groupname;
				$this->auditlog->auditlog_info('Group Employee',$emp_name->empname." added to the group ".$gname);
			}
		}
		if($rule!='1'){
			$query=$this->db->query("update ".$bid."_group_emp set callcounter=0 where gid='".$id."'");
		}
		if($err!=0){
			return 1;
		}else{
			return 0;
		}
	}
	function get_empname($eid)
	{
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("select * from ".$bid."_employee where eid=$eid");
		$res=$sql->row();
		return $res;
	}
	function getEmplist($bid,$ofset='0',$limit='20'){
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
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
                    $q.=(isset($s[$arr[$n]]) && $s[$arr[$n]]!='' && $s[$arr[$n]]!=' ') ? " AND a.".$arr[$n]."= '".$s[$arr[$n]]."'":"";
                }
            }
        }
		$q.=(isset($s['empemail']) && $s['empemail']!='')?" and a.empemail like '%".$s['empemail']."%'":"";
		$q.=(isset($s['empname']) && $s['empname']!='')?" and a.empname like '%".$s['empname']."%'":"";
		$q.=(isset($s['reportto']) && $s['reportto']!='')?" and e.empname like '%".$s['reportto']."%'":"";
		$q.=(isset($s['empnumber']) && $s['empnumber']!='')?" and a.empnumber like '%".$s['empnumber']."%'":"";
		$q.=(isset($s['extension']) && $s['extension']!='')?" and a.extension like '%".$s['extension']."%'":"";
		$q.=(isset($s['role']) && $s['role']!='')?" and b.rolename like '%".$s['role']."%'":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$sql="select SQL_CALC_FOUND_ROWS a.*,if(u.status=1,a.`login`,0) `login`,
						   if(a.tollfree='1','Yes','No') as tollfree, b.rolename  as role 
						  from ".$bid."_employee a 
						  LEFT JOIN ".$bid."_user_role b on a.roleid=b.roleid
						  LEFT JOIN ".$bid."_employee e on a.reportto=e.eid
						  LEFT JOIN user u on (u.eid=a.eid AND u.bid=a.bid)
						  where (a.status=1 or a.status=0)  $q ORDER BY a.`empname` limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='2'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('2',$bid);
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
			array_push($header,$this->lang->line('level_Action'));
		$ret['header'] = $header;
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$data = array($i);
			$r = $this->configmodel->getDetail('2',$rec['eid'],'',$bid);
			$v='<input type="checkbox" class="blk_check" name="blk[]" value="'.$r['eid'].'"/>';
			array_push($data,$v);
				$r['reportto']=$r['reporttoname'];
			foreach($keys as $k){
				$v = isset($r[$k])?$r[$k]:"";
				if($k=='empday' && $r[$k]!=''){
					$bday = json_decode($r[$k]);
					$v = '';
					foreach($bday as $b => $d){ $v .= (isset($d->day) && $d->day=='1')?$b.'='.$d->st.'-'.$d->et.'<br>':'';}
				}
				$v = ($k=='tollfree') ? $rec['tollfree']: $v;
				$v = ($rec['status']=='0')? '<font color=red>'.$v.'</font>' : $v ;
				array_push($data,$v);
			}
			if($opt_add || $opt_view || $opt_delete){
				($r['status']=="0")
				?$s=' <span '.(($r['eid']!=1)?'class="fa fa-lock ChangeStatus confirm" id="'.$r['eid'].'"':'class="fa fa-lock" ').'  title="Enable"></span>'
				:$s=' <span '.(($r['eid']!=1)?'class="fa fa-unlock ChangeStatus confirm" id="'.$r['eid'].'"':'class="fa fa-unlock"').' title="Disable"></span>';
				$act  = ($opt_add && $r['eid']!=1)?'<a href="EditEmployee/'.$r['eid'].'"><span title="Edit" class="fa fa-edit"></span></a>':'<a><span title="Not Editable" class="fa fa-edit"></span></a>';
				$act .= ($opt_view)?' <a href="Employee/activerecords/'.$r['eid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="fa fa-file-text" title="View Employee"></span></a>':'';
				$act .= ($opt_delete && $r['eid']!=1)?' <a href="'.base_url().'Employee/Delete_Employee/'.$r['eid'].'" class="deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':' <a><span title="Not Deleteable" class="glyphicon glyphicon-trash"></span> </a>';
				$act .= ($opt_add)?$s:'';
				$act .=  anchor("Report/sendSms/".$r['eid']."/employee", '&nbsp;<span title="Click to send SMS" class="glyphicon glyphicon-comment"></span>','class="clickToSMS" data-toggle="modal" data-target="#modal-empl"');	
				$act .=  ($r['click2connect']==1)?'&nbsp;<a href="Employee/ClickasEmp/'.$r['eid'].'"><span class="glyphicon glyphicon-remove"></span></a>':'&nbsp;<a href="Employee/ClickasEmp/'.$r['eid'].'"><span class="fa fa-check"></span></a>';
				$img  =  ($r['selfdisable']=='1') ? '<img src="system/application/img/icons/offline.png" title="Offline" width="16" height="16" style="vertical-align:top;" />':'<img src="system/application/img/icons/online.png" title="Online" width="16" height="16" style="vertical-align:top;" />';
				if(($opt_add && $r['eid']!=1) || ($this->session->userdata('eid') == $r['eid']) ) 
					$act .='<a href="user/selfdisable/'.$r['selfdisable'].'/'.$r['eid'].'">'.$img.'</a>';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	} 
	function getunconfrimEmplist($bid,$ofset='0',$limit='20'){
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['empemail']) && $s['empemail']!='')?" and a.empemail like '%".$s['empemail']."%'":"";
		$q.=(isset($s['empname']) && $s['empname']!='')?" and a.empname like '%".$s['empname']."%'":"";
		$q.=(isset($s['empnumber']) && $s['empnumber']!='')?" and a.empnumber like '%".$s['empnumber']."%'":"";
		$q.=(isset($s['extension']) && $s['extension']!='')?" and a.extension like '%".$s['extension']."%'":"";
		$q.=(isset($s['role']) && $s['role']!='')?" and b.rolename like '%".$s['role']."%'":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$sql="select SQL_CALC_FOUND_ROWS a.eid,a.bid,a.`empname`,
						  a.`empid`,a.`empnumber`,a.`empemail`,
						  a.`login`,a.`status`,b.rolename  as role 
						  from ".$bid."_employee a LEFT JOIN
						  ".$bid."_user_role b on a.roleid=b.roleid
						  where a.status=3 $q limit $ofset,$limit";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='2'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('2',$bid);
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
			$r = $this->configmodel->getDetail('2',$rec['eid'],'',$bid);
			$r['reportto']=$r['reporttoname'];
			foreach($keys as $k){
				$v = isset($r[$k])?$r[$k]:"";
				if($k=='empday' && $r[$k]!=''){
					$empday = json_decode($r[$k]);
					$v = '';
					foreach($empday as $b => $d){ $v .= (isset($d->day) && $d->day=='1')?$b.'='.$d->st.'-'.$d->et.'<br>':'';}}
				array_push($data,$v);
			}
			if($opt_add || $opt_view){
				$act  = ($opt_add && $r['eid']!=1)?'<a href="unconfirmEmployee/'.$r['eid'].'/uncomfirm"><span title="Edit" class="fa fa-edit"></span></a>':'<span title="Edit" class="fa fa-edit"></span>';
			}
			$act.=($opt_delete)?'<a href="Employee/del_unconfirm/'.$r['eid'].'" rel="'.$r['eid'].'" class="unconfirmEmP"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
			array_push($data,$act);
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	} 
	function emp_list($start,$limit){
		$res=array();
		$q='';
		if($this->session->userdata('ename')!=""){
			$empnmae=$this->session->userdata('ename');
			$q.=" and a.empname like '%".$empnmae."%'";
		}
		if($this->session->userdata('mobilenumber')!=""){
			$mobilenumber=$this->session->userdata('mobilenumber');
			$q.=" and a.empnumber like '%".$mobilenumber."%'";
		}
		$sql=$this->db->query("select a.eid,a.`empname`,a.`empid`,a.`empnumber`,a.`empemail`,a.`status`,b.businessname as  e_busninessname from ".$bid."_employee a,business  b where a.bid=b.bid  $q limit $start,$limit");
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}
		return $res;
	}
	function delete_emp($eid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->get_employee($eid);
		$this->auditlog->auditlog_info($this->lang->line('level_module_Employee'),"Employee ".$res[0]['empname']  ." is Deleted");
		$sql=$this->db->query("UPDATE ".$bid."_employee SET status=2,login=0 WHERE eid=$eid");
		$sql=$this->db->query("UPDATE user SET status=2 WHERE eid=$eid AND bid='".$bid."'");
		$sqls=$this->db->query("DELETE FROM ".$bid."_group_emp WHERE eid=".$eid);
		return 1;
	}
	function undelete_emp($eid){
		$res=$this->get_employee($eid);
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$this->auditlog->auditlog_info($this->lang->line('level_module_Employee'),"Employee ".$res[0]['empname']  ." is UnDeleted");
		$sql=$this->db->query("update ".$bid."_employee set status=1 where eid=$eid");
		$sql=$this->db->query("update user set status=1 where eid=$eid AND bid='".$bid."'");
		return 1;
	}
	function ChangeStatus($eid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql = $this->db->query("SELECT status,empname FROM ".$bid."_employee WHERE eid = '".$eid."'");
		$rec = $sql->row();
		if($rec->status=='1'){
			$sql1 = $this->db->query("UPDATE ".$bid."_employee SET status='0' WHERE eid = '".$eid."'");

			$sql=$this->db->query("update user set status=0 where eid='".$eid."' and bid='".$bid."'");
			$this->auditlog->auditlog_info($this->lang->line('level_module_Employee'),"Employee ".$rec->empname  ." status disabled");
		}else{
			$sql1 = $this->db->query("UPDATE ".$bid."_employee SET status='1' WHERE eid = '".$eid."'");
			$sql=$this->db->query("update user set status=1 where eid='".$eid."' and bid='".$bid."'");
			$gids = $this->db->query("SELECT distinct gid FROM ".$bid."_group_emp WHERE eid=$eid");
			if($gids->num_rows() > 0){
				foreach($gids->result_array() as $gid){ 
					$gd[] = $gid['gid'];
				}
				$this->db->query("UPDATE ".$bid."_group_emp SET callcounter='0' WHERE gid in (".implode(",",$gd).")");
			}
			$this->auditlog->auditlog_info($this->lang->line('level_module_Employee'),"Employee ".$rec->empname  ." status enabled");
		}
		return true;
	}
	function get_employee($eid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("select a.* from ".$bid."_employee a where a.eid=$eid");
		if($sql->num_rows()>0){
			$res=$sql->result_array();
		}	
		return $res;
	}

	function update_employee($eid){
		$res=$this->get_employee($eid);
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$process=$this->check_Mobile_limit($res[0]['empnumber'],$_POST['empnumber']);
		if($process!=2){
	    $ds = $this->KeyChar[$name['0']].$this->KeyChar[$name['1']].$this->KeyChar[$name['2']];
		$login_s=$res[0]['login'];
		$arr=array_keys($_POST);
		for($i=0;$i<sizeof($arr);$i++){
			if(!in_array($arr[$i],array("update_system","custom","login","username","password","cpassword"))){
				$this->db->set($arr[$i], $_POST[$arr[$i]]);
			}
		}
		$this->db->set('directory_string',$ds);
		if(!isset($_POST['tollfree'])) $this->db->set('tollfree', '0');
		$this->db->set('status','1');	
		if(isset($_POST['login'])){
			$this->db->set('login','1');
		}else{
			$this->db->set('login','0');
		}
		if(isset($_POST['empday']))$this->db->set('empday', json_encode($_POST['empday']));
		if(isset($_POST['reportto'])) $this->db->set('reportto', $this->input->post('reportto'));
		$this->db->where('eid',$eid);
		$this->db->update($bid.'_employee'); 
		if($this->input->post('extension')!=""){
			$this->db->query("update ".$bid."_pbxext set ext='".$this->input->post('extension')."' where targettype ='employee' and targetid='".$eid."'");
		}
		$this->auditlog->auditlog_info($this->lang->line('level_module_Employee'),"Employee ".$res[0]['empname']  ." Info updated ");
		if(isset($_POST['login'])){
				$password="";for($i = 0; $i<=10 ; $i++){$password .= ($i%2==0)? chr(rand(97,122)) : rand(0,9);}
				$check_user_auth=$this->db->query("Select * from user where username='".$res[0]['empemail']."' and eid=".$res[0]['eid']." and bid=".$res[0]['bid']);
				if($check_user_auth->num_rows()==0){
					$uid=$this->db->query("SELECT COALESCE(MAX(`uid`),0)+1 as id FROM `user`")->row()->id;
					$this->db->set('uid', $uid); 
					$this->db->set('bid', $bid);
					$this->db->set('eid', $res[0]['eid']); 
					$this->db->set('username',$res[0]['empemail']); 
					$this->db->set('password',md5($password)); 
					$this->db->insert('user'); 
					$body=$this->emailmodel->newEmailBody($this->emailmodel->email_body1($res[0]['empname'],$res[0]['empemail'],$password,$res[0]['bid']),$res[0]['empname']);
					$to  = $res[0]['empemail']; // note the comma
					$subject = 'Login Details';
					$this->load->library('email');
					$this->email->from('noreply@mcube.com', 'MCube');
					$this->email->to($to);
					$this->email->subject($subject);
					$this->email->message($body);
					$this->email->send();
				}else{
					$password="";for($i = 0; $i<=10 ; $i++){$password .= ($i%2==0)? chr(rand(97,122)) : rand(0,9);}
					$check_user_auth=$this->db->query("Select * from user where username='".$res[0]['empemail']."' and eid=".$res[0]['eid']." and bid='".$res[0]['bid']."' and password=''");
					if($check_user_auth->num_rows()>0){
							$this->db->set('password',md5($password)); 
							$this->db->set('status','1');
							$this->db->where('eid',$res[0]['eid']);
							$this->db->where('bid',$res[0]['bid']);
							$this->db->where('username',$res[0]['empemail']);
							$this->db->update('user');
							$body=$this->emailmodel->newEmailBody($this->emailmodel->email_body1($res[0]['empname'],$res[0]['empemail'],$password,$res[0]['bid']),$res[0]['empname']);
							$to  = $res[0]['empemail']; // note the comma
							$subject = 'Login Details';
							$this->load->library('email');
							$this->email->from('noreply@mcube.com', 'MCube');
							$this->email->to($to);
							$this->email->subject($subject);
							$this->email->message($body);
							$this->email->send();
					}else{
							$sql=$this->db->query("select status from user where eid='".$res[0]['eid']."' and bid='".$res[0]['bid']."'")->row();
							if($sql->status==0){
								$this->db->set('status','1');
								$this->db->where('eid',$res[0]['eid']);
								$this->db->where('bid',$res[0]['bid']);
								$this->db->where('username',$res[0]['empemail']);
								$this->db->update('user');	
								$mbody="<br/>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Login Access has been activated,login with your old username and password <br/>";
								$body=$this->emailmodel->newEmailBody($mbody,$res[0]['empname']);
								$to  = $res[0]['empemail']; // note the comma
								$subject = 'Login Access Activated';
								$this->load->library('email');
								$this->email->from('noreply@mcube.com', 'MCube');
								$this->email->to($to);
								$this->email->subject($subject);
								$this->email->message($body);
								$this->email->send();	
							}
					}
				}
			}else{
					$check_user_auth=$this->db->query("Select * from user where username='".$res[0]['empemail']."' and eid=".$res[0]['eid']." and bid=".$res[0]['bid']);
					if($check_user_auth->num_rows()>0){
							$this->db->set('status','0');
							$this->db->where('eid',$res[0]['eid']);
							$this->db->where('bid',$res[0]['bid']);
							$this->db->where('username',$res[0]['empemail']);
							$this->db->update('user');
							$body=$this->emailmodel->newEmailBody($this->emailmodel->email_body_login($res[0]['empname'],$res[0]['bid']),$res[0]['empname']);
							$to  		= $res[0]['empemail']; // note the comma
							$subject 	= 'Login Acess Denied';
							$this->load->library('email');
							$this->email->from('noreply@mcube.com', 'MCube');
							$this->email->to($to);
							$this->email->subject($subject);
							$this->email->message($body);
							$this->email->send();		
					}
			}
		if(isset($_POST['custom'])){
			$arrs=array_keys($_POST['custom']);
			for($k=0;$k<sizeof($arrs);$k++){
				if(is_array($_POST['custom'][$arrs[$k]])){
					$x=implode(",",$_POST['custom'][$arrs[$k]]);
				}else{
					$x=$_POST['custom'][$arrs[$k]];
				}
				$this->db->query("DELETE FROM ".$bid."_customfieldsvalue 
								  where bid= '".$bid."' and modid= '2' 
								  and fieldid = '".$arrs[$k]."' and dataid= '".$gid."'");
				$sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
					 bid			= '".$bid."'
					,modid			= '2'
					,fieldid		= '".$arrs[$k]."'
					,dataid			= '".$eid."'
					,value			= '".$x."'";
				$this->db->query($sql);
			}
		}
		return 1;	
		}else{
			return 3;
		}
		
	}
	function update_uncofirmemployee($eid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		//print_r($_POST['custom']);exit;
		$res=$this->get_employee($eid);
		$arr=array_keys($_POST);
		$name = strtolower($this->input->post('empname'));
		$ds = $this->KeyChar[$name['0']].$this->KeyChar[$name['1']].$this->KeyChar[$name['2']];
		for($i=0;$i<sizeof($arr);$i++){
			if(!in_array($arr[$i],array("update_system","custom","login","username","password","cpassword"))){
				$this->db->set($arr[$i], $_POST[$arr[$i]]);
			}
		}
		$this->db->set('directory_string',$ds);
		if(!isset($_POST['tollfree'])) $this->db->set('tollfree', '0');
		
		if(isset($_POST['login']) && $_POST['login']==1){
			$this->db->set('login',$_POST['login']);
		}else{
			$this->db->set('login','0');
		}
		if(isset($_POST['empday']))$this->db->set('empday', json_encode($_POST['empday']));
		if(isset($_POST['reportto'])) $this->db->set('reportto', $this->input->post('reportto'));
		$this->db->where('eid',$eid);
		$this->db->update($bid.'_employee'); 
		$this->db->query("update ".$bid."_pbxext set ext='".$this->input->post('extension')."' where targettype ='employee' and targetid=".$eid);
		$this->auditlog->auditlog_info($this->lang->line('level_module_Employee'),"Employee ".$res[0]['empname']  ." Info updated ");
		if(isset($_POST['custom'])){
			$arrs=array_keys($_POST['custom']);
			for($k=0;$k<sizeof($arrs);$k++){
				
				if(is_array($_POST['custom'][$arrs[$k]])){
						$x=implode(",",$_POST['custom'][$arrs[$k]]);
					}
					else{
						$x=$_POST['custom'][$arrs[$k]];
					}
					$this->db->query("DELETE FROM ".$bid."_customfieldsvalue where bid= '".$bid."' and modid= '2' and fieldid		= '".$arrs[$k]."' and dataid= '".$gid."'");
					$sql = "REPLACE INTO ".$bid."_customfieldsvalue SET
						 bid			= '".$bid."'
						,modid			= '2'
						,fieldid		= '".$arrs[$k]."'
						,dataid			= '".$eid."'
						,value			= '".$x."'";
					$this->db->query($sql);
				}
			}
		$string=($bid>100)?'1':(($bid>10))?'2':'3';
		$generate_string="MCB".$bid."E".$eid.substr($this->config->item('server_loc'),0,$string);
		if(isset($_POST['login'])){
			$this->db->set('username',$this->input->post('empemail')); 
			/*if($res[0]['empnumber']!=$this->input->post('empnumber')){
				$mess="Hi".$this->input->post('empname')."\nPlease verify your mobile using this code:".$generate_string."\nLog on to ".base_url()."user/mobile";
					$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
					$reply = $api."&to=".substr($this->input->post('empnumber'),-10,10)."&text=".urlencode($mess."\nPowered by MCube");
					file($reply);
				$this->db->set('userid',$generate_string); 
			}*/
			$this->db->where('eid',$eid);
			$this->db->where('bid',$bid);
			$this->db->update('user');
			}
			if(isset($_POST['empemail'])){
		//$message_body=$this->emailmodel->email_body($this->input->post('empname'),$eid,$this->session->userdata('bid'));
		$body=$this->emailmodel->newEmailBody($this->emailmodel->email_body($this->input->post('empname'),$eid,$bid),$this->input->post('empname'));
		$to  = $this->input->post('empemail'); // note the comma
		$subject = 'Registered Employee Details';
		$this->load->library('email');
		$this->email->from('noreply@mcube.com', 'MCube');
		$this->email->to($to);
        $this->email->subject($subject);
		$this->email->message($body);
		$this->email->send();
		
		//~ $headers = 'MIME-Version: 1.0' . "\n";
		//~ $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
		//~ $headers .= 'From:'.$from. "\n";
		//'Reply-To:"MCube" <support@vmc.in>'."\r\n" .
		//'X-Mailer: PHP/' . phpversion();
		// echo $message;exit;
		//mail($to, $subject, $body, $headers);
		}
		return 1;	
		
	}
	function get_emp_count(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$query=$this->db->query("select * from ".$bid."_employee");
		return $query->num_rows();
	}
	function getRoledetail($roleid='',$bid=''){
		$bid = ($bid=='')?$this->session->userdata('bid'):$bid;
		$DB2 = (in_array($bid,array('257','538'))) ? $this->load->database('download', TRUE) : $this->load->database('download1', TRUE);
		$detail['role'] = (array)$DB2->query("SELECT * FROM ".$bid."_user_role
							WHERE roleid='".$roleid."'
							AND bid='".$bid."'")
					->row();
					
		$modules = $DB2->query("SELECT m.modid,m.modname,m.moddesc,
							COALESCE(o.opt_add,0) as opt_add,
							COALESCE(o.opt_view,0) as opt_view,
							COALESCE(o.opt_download,0) as opt_download,
							COALESCE(opt_delete,0) as opt_delete 
							FROM module m
							LEFT JOIN (SELECT * FROM ".$bid."_role_mod_opt
							WHERE roleid='".$roleid."' 
							AND bid='".$bid."') as o
							ON m.modid=o.modid where m.status=1")
					->result_array();
					
		foreach ($modules as $mod)
		$detail['modules'][$mod['modid']] = $mod;
		
		$detail['system'] = $DB2->query("SELECT a.*,f.fieldname FROM ".$bid."_role_access a
							LEFT JOIN systemfields f on a.fieldid=f.fieldid
							WHERE a.roleid='".$roleid."'
							AND a.bid='".$bid."'
							AND a.fieldtype='s'")
					->result_array();
					
		$detail['custom'] = $DB2->query("SELECT * FROM ".$bid."_role_access
							WHERE roleid='".$roleid."'
							AND bid='".$bid."'
							AND fieldtype='c'")
					->result_array();
			
		return $detail;
	}
	function getRoleid($eid,$bid=''){
		$bid = ($bid=='')?$this->session->userdata('bid'):$bid;
		$DB2 = (in_array($bid,array('257','538'))) ? $this->load->database('download', TRUE) : $this->load->database('download1', TRUE);
		return $DB2->query("SELECT roleid FROM ".$bid."_employee
							WHERE eid='".$eid."'
							AND bid='".$bid."'")
					->row()->roleid;
	}
	function addrole(){
	
		$roleid = (isset($_POST['roleid']) && $_POST['roleid']!='')
					? $_POST['roleid']
					: $this->db->query("SELECT COALESCE(MAX(`roleid`),0)+1 as id FROM ".$this->session->userdata('bid')."_user_role")->row()->id;
		$sql = "REPLACE INTO ".$this->session->userdata('bid')."_user_role SET
				 roleid			= '".$_POST['roleid']."'
				,bid			= '".$_POST['bid']."'
				,rolename		= '".$_POST['rolename']."'
				,recordlimit	= '".$_POST['recordlimit']."'
				,owngroup		= '".(isset($_POST['owngroup'])? 1:0)."'
				,admin 			= '".(isset($_POST['coadmin'])? 1:0)."'
				,accessrecords	= '".(isset($_POST['accessrecords'])? 1:0)."'";
		//echo $sql;exit;		
		$this->db->query($sql);
		if($_POST['roleid']!=''){
			$this->auditlog->auditlog_info('Role config',"update ".$this->input->post('rolename')." Role");
		}else{
		$this->auditlog->auditlog_info('Role config',"Created New role  ".$this->input->post('rolename'));
		}
		foreach($_POST['module'] as $mod){
			$sql = "REPLACE INTO ".$this->session->userdata('bid')."_role_mod_opt SET
					 bid			= '".$_POST['bid']."'
					,roleid			= '".$roleid."'
					,modid			= '".$mod['modid']."'
					,opt_add		= '".(isset($mod['opt_add'])? 1 : 0)."'
					,opt_view		= '".(isset($mod['opt_view'])? 1 : 0)."'
					,opt_download	= '".(isset($mod['opt_download'])? 1 : 0)."'
					,opt_delete		= '".(isset($mod['opt_delete'])? 1 : 0)."'";
			$this->db->query($sql);
			$sql = "DELETE FROM ".$this->session->userdata('bid')."_role_access WHERE
					roleid			= '".$roleid."'
					AND modid		= '".$mod['modid']."'";
			$this->db->query($sql);
			if(isset($mod['system']))
			foreach($mod['system'] as $field => $val){
				$sql = "REPLACE INTO ".$this->session->userdata('bid')."_role_access SET
						 bid			= '".$_POST['bid']."'
						,roleid			= '".$roleid."'
						,modid			= '".$mod['modid']."'
						,fieldid		= '".$field."'
						,fieldtype		= 's'";
				$this->db->query($sql);
			}
			if(isset($mod['custom']))
			foreach($mod['custom'] as $field => $val){
				$sql = "REPLACE INTO ".$this->session->userdata('bid')."_role_access SET
						 bid			= '".$_POST['bid']."'
						,roleid			= '".$roleid."'
						,modid			= '".$mod['modid']."'
						,fieldid		= '".$field."'
						,fieldtype		= 'c'";
				$this->db->query($sql);
			}
		}
		return $roleid;
	}
	function get_roles(){
		$res=array(""=>"Select Role");
		$sql=$this->db->query("select * from ".$this->session->userdata('bid')."_user_role");
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $re)
			$res[$re['roleid']]=$re['rolename'];
		}
		return $res;
	}
	function get_areacodes($regionid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=array();
		$sql=$this->db->query("select * from ".$bid."_group_region WHERE regionid='".$regionid."'");
		$res['0']='[Select Region]';
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $re)
				$res[$re['gregionid']]=$re['regionname'];
		}
		return $res;
	}
	function email_employee($empemail=''){
		$e=($empemail!="")?$empemail:$this->input->post('empemail');
		$usertable=$this->db->query("select * from user where username='".$e."'");
		if($usertable->num_rows()==0){
			$sql=$this->db->query("select * from business where status=1");
			if($sql->num_rows()>0){
				foreach($sql->result_array() as $rows){
					$sqls=$this->db->query("select * from ".$rows['bid']."_employee where empemail='".$e."'");
					if($sqls->num_rows()!=0){
						return false;
					}
					
				}
			}
			return true;				
		}else{
			return false;
		}
	}
	function getEmployee_byname($ename=''){
		
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$sql=$this->db->query("select a.eid from ".$bid."_employee a where a.empname='".$ename."'");
		if($sql->num_rows()>0){
			
			return $sql->row();
		}else{
			
			return '';
		}
		
	}
	
	function getUserDomain($username){
		$sql = "SELECT * FROM alluser WHERE username = '".$username."'";
		$rst = $this->db->query($sql);
		if($rst->num_rows()>0){
			return $rst->result_array();
		}else{
			return array();
		}
	}
	function CheckExtension($con=''){
		if($this->input->post('extension')!=""){
			$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$cond1=($con!="")?" and eid!='".$con."'":'';
		$cond2=($con!="")?" and targetid!='".$con."'":'';
		$sql=$this->db->query("SELECT * FROM ".$bid."_employee where extension='".$this->input->post('extension')."'  $cond1 ");
		if($sql->num_rows()==0){
			$sq=$this->db->query("SELECT * FROM ".$bid."_pbxext where ext='".$this->input->post('extension')."'  $cond2 ");
			if($sq->num_rows()>0){
				return false;
			}else{
				return true;
			}
			
		}else{
			return false;
		}
	
	}else{
		return true;
	}
  }
  function unconfirmDel($eid){
	  $cbid=$this->session->userdata('cbid');
	  $bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
	  $this->db->query("DELETE FROM user where bid='".$bid."' and eid='".$eid."'");
	  $this->db->query("DELETE FROM ".$bid."_group_emp where eid='".$eid."'");
	  $this->db->query("DELETE FROM ".$bid."_employee where eid='".$eid."'");
	  return '1';
  }
  function check_Mobile_limit($number,$post){
	  $bid=$this->session->userdata('bid');
	  $date=date('Y-m-d');
	  if($number!=$post){
		  $sql=$this->db->query("SELECT * from business where bid=".$bid)->row();
		  if($sql->cdate!=$date){
			  $this->db->set('cdate',$date);
			  $this->db->set('count','1');
			  $this->db->where('bid',$sql->bid);
			  $this->db->update('business');
			  return '1';
		 }else{
			 if($sql->count<3){
				 $this->db->query("update business set count=count+1 where bid=".$bid);
				return '1';
			 }else{
				 return '2';
			 }
		 }
	  }else{
		  return '1';
	  }
  }
  function getEmailTemplate($ofset='0',$limit='20'){
	  $q='where status=1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$bid=$this->session->userdata('bid');
		$roleDetail = $this->getRoledetail($this->session->userdata('roleid'));
		$q.=(isset($s['tname']) && $s['tname']!='')?" and `template_name` like '%".$s['tname']."%'":"";
		
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$res=array();
		
		$res['data']=$this->db->query("select * from ".$bid."_emailtemplate	
										$q
										LIMIT $ofset,$limit
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
	  
	  
  }
  function getSMSTemplate($ofset='0',$limit='20'){
	  $q='where status=1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$bid=$this->session->userdata('bid');
		$roleDetail = $this->getRoledetail($this->session->userdata('roleid'));
		$q.=(isset($s['tname']) && $s['tname']!='')?" and `template_name` like '%".$s['tname']."%'":"";
		
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$res=array();
		
		$res['data']=$this->db->query("select * from ".$bid."_smstemplate	
										$q
										LIMIT $ofset,$limit
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
 }
 function smsTempnames(){
	$bid=$this->session->userdata('bid');
	$id=$this->db->query("SELECT template_id,template_name FROM ".$bid."_smstemplate WHERE status=1");
	$res=array();
	if($id->num_rows()>0){
		$res['']="Select";
		foreach($id->result_array() as $rows){
				$res[$rows['template_id']]=$rows['template_name'];
		}
	}
	return $res;

 }
  function getSendList($bid,$ofset='0',$limit='20'){
		$q='where s.status=1';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$roleDetail = $this->getRoledetail($this->session->userdata('roleid'));
		$q.=(isset($s['to']) && $s['to']!='')?" and s.`to` like '%".$s['to']."%'":"";
		$q.=(isset($s['subject']) && $s['subject']!='')?" and s.`subject` like '%".$s['subject']."%'":"";
		$q.=($roleDetail['role']['admin']!='1') ? " AND e.eid = '".$this->session->userdata('eid')."'":"";
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		$res=array();
		$res['data']=$this->db->query("select SQL_CALC_FOUND_ROWS s.*,e.empname from ".$bid."_sentmails	s
										left join ".$bid."_employee e on s.eid=e.eid
										$q
										ORDER BY s.id DESC
										LIMIT $ofset,$limit
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		return $res;
	}
	function temp_details($itemid=''){
		$bid=$this->session->userdata('bid');
		$sql="SELECT * FROM ".$bid."_emailtemplate where template_id='".$itemid."'";
		$rst = (array)$this->db->query($sql)->row();
		return $rst;
	}
	function SMStemp_details($itemid=''){
		$bid=$this->session->userdata('bid');
		$sql="SELECT * FROM ".$bid."_smstemplate where template_id='".$itemid."'";
		$rst = (array)$this->db->query($sql)->row();
		return $rst;
	}
	function getSendItem($id){
		$bid=$this->session->userdata('bid');
		$sql="select s.*,e.empname,e.empemail from ".$bid."_sentmails	s
										left join ".$bid."_employee e on s.eid=e.eid where id='".$id."'";
		return $this->db->query($sql)->row_array();
	}
	function ClickasEmp($eid,$bid){
		$sql=$this->db->query("SELECT * from ".$bid."_employee where click2connect='1'");
		$res=$sql->row();
		if(!empty($res) && $res->eid!=$eid){
			$this->db->query("update ".$bid."_employee set click2connect='0' where eid='".$res->eid."'");
			$this->db->query("update ".$bid."_employee set click2connect='1' where eid='".$eid."'");
			return true;
		}else{
			$sql=$this->db->query("SELECT * from ".$bid."_employee where eid='".$eid."'");
			$res=$sql->row();
			$st = ($res->click2connect=='1') ? '0' : '1'; 
			$this->db->query("update ".$bid."_employee set click2connect='".$st."' where eid='".$eid."'");
			return true;
		}
	}
	function selfdisable($val,$eid=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$eid = ($eid == '') ? $this->session->userdata('eid') : $eid;
		$time = date('Y-m-d h:i:s');
		if($val == 0){
			$this->db->query("INSERT INTO ".$bid."_emp_break (`id`, `bid`, `eid`, `start_time`, `end_time`, `duration`) VALUES (NULL,'".$bid."','".$eid."','".$time."','','')");
		}elseif($val == 1){
			$sql = "SELECT id,start_time FROM ".$bid."_emp_break WHERE eid='".$eid."' AND end_time = '0000-00-00 00:00:00' LIMIT 0,1";
			$rst = (array)$this->db->query($sql)->row();
			$duration =ceil((strtotime($time) - strtotime($rst['start_time']))/60);
			$this->db->query("UPDATE ".$bid."_emp_break SET end_time='".$time."',duration='".$duration."' WHERE eid='".$eid."' AND id='".$rst['id']."'");
			$gids = $this->db->query("SELECT DISTINCT(gid) FROM ".$bid."_group_emp WHERE eid=$eid");
			if($gids->num_rows() > 0){
				foreach($gids->result_array() as $gid){
						$rscnt = $this->db->query("SELECT COALESCE(MAX(`callcounter`),0) as maxcnt FROM ".$bid."_group_emp  WHERE gid ='".$gid['gid']."'")->row()->maxcnt;
						$this->db->query("UPDATE ".$bid."_group_emp SET callcounter='".$rscnt."' WHERE gid = '".$gid['gid']."'");
					}
			}
		}
		$val = ($val == 0) ? '1' : '0';
		$this->db->query("UPDATE ".$bid."_employee SET selfdisable=".$val." WHERE eid='".$eid."'");
		return true;
	}
	function empdownload($bid){
		$DB2 = (in_array($bid,array('257','538'))) ? $this->load->database('download', TRUE) : $this->load->database('download1', TRUE);
		$res = array();	
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = $roleDetail['role']['recordlimit'];
		$csv_output = "";
		$ke = array();
		foreach($_POST['lisiting'] as $key=>$val){
			$hkey[] = $key;
			$header[] = $val;
		}
		$csv_output .= @implode(",",$header)."\n";
		$sql="SELECT SQL_CALC_FOUND_ROWS eid FROM ".$bid."_employee LIMIT 0,$limit";		 
		$rst = $DB2->query($sql)->result_array();
		$total_record_count = $DB2->query($sql)->num_rows();
		$name = $bid.'_'.
				$this->session->userdata('eid').'_'.
				time();
		mkdir('reports/'.$name);
		chmod('reports/'.$name,0777);
		$files = array();
		$data_file = 'reports/'.$name.'/employee.csv';
		$fp = fopen($data_file,'w');
		fwrite($fp,$csv_output);
		foreach($rst as $rec){
			$data = array();
			$r = $this->configmodel->getDetail('2',$rec['eid'],'',$bid);
			$i=0;
			foreach($hkey as $k){
				$v=(isset($r[$k])) ? '"'.str_replace("\n"," ",$r[$k]).'"' : '';
				array_push($data,$v);
			}
			$csv_output = @implode(",",$data)."\n";
			fwrite($fp,$csv_output);
		}
		fclose($fp);
		chdir('reports');
		exec('zip -r '.$name.'.zip '.$name);
		exec('rm -rf '.$name);
		return $name;
	}
}
/* end  */
