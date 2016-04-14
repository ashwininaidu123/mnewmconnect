<?php
class qrmodel extends model
{
	function qrmodel()
	{
		 parent::Model();
		 $this->load->model('auditlog');
		 $this->load->model('empmodel');
	}
	function Delete_qrcode($id){
		$this->db->set('status','0');
		$this->db->where('bid',$this->session->userdata('bid'));
		$this->db->where('qrid',$id);
		$this->db->update('qr');
		return true;
	}
	function unDelete_qrcode($id){
		$this->db->set('status','1');
		$this->db->where('bid',$this->session->userdata('bid'));
		$this->db->where('qrid',$id);
		$this->db->update('qr');
		return true;
	}
	function get_downloadimage($id){
		//ECHO "select imagename from qr where qrid=$id and bid=".$this->session->userdata('bid');EXIT;
		$sql=$this->db->query("select imagename from qr where qrid=$id and bid=".$this->session->userdata('bid'))->row()->imagename;
		return $sql;
	}
	function qr_use(){
		$res=array();	
		$sql=$this->db->query("select * from qruse");
		return $sql->result_array();
	}
	function qruseinto($id){//print_r($_POST);
		if($id!=""){
			if(isset($_FILES['clogo']['name'])){
				$uploads_dir = $this->config->item('upload_path');
				$tmp_name = $_FILES['clogo']['tmp_name'];
				$name = rand().$_FILES['clogo']['name'];
				move_uploaded_file($tmp_name,$uploads_dir."/".$name);
			}else{
					$res=$this->getqrcodedetails($id);
					$name=$res->company_logo;
			}
			$this->db->set('qrtitle',$this->input->post('qrtitle'));
			$this->db->set('uid',$this->session->userdata('uid'));
			$this->db->set('source',$this->input->post('source'));
			$this->db->set('description',$this->input->post('description'));
			$this->db->set('company_logo', $name);
			$this->db->set('qruse',implode(",",$this->input->post('qruse')));
			if(in_array(1,$_POST['qruse'])) { $this->db->set('gid',$this->input->post('gid')); }
			if(in_array(2,$_POST['qruse'])) { $this->db->set('webaddress',$this->input->post('website_name')); }
			if(in_array(5,$_POST['qruse'])){$this->db->set('video',$this->input->post('video'));}
			if(in_array(6,$_POST['qruse'])){$this->db->set('qraddress',$this->input->post('qraddress'));}
			$this->db->where('qrid',$id);
			$this->db->update('qr');	
			//echo $this->db->last_query();exit;
			$qrdeals=$this->getqrdealinfo($id);
			if(in_array(4,$_POST['qruse'])){
				$this->db->query("Delete from qrdeals where bid	=".$this->session->userdata('bid')." and qrid= ".$id);
				$dealsql = "REPLACE INTO qrdeals SET 
							qrid		= '".$id."'
							,bid			= '".$this->session->userdata('bid')."'
							,dealtitle		= '".$this->input->post('dealtitle')."'
							,description	= '".$this->input->post('description')."'
							,address		= '".$this->input->post('address')."'
							,validupto		= '".$this->input->post('validupto')."'
							,phone			= '".$this->input->post('phone')."'
							,replymessage	= '".$this->input->post('replymessage')."'
							,dealvalue		= '".$this->input->post('dealvalue')."'";
				$this->db->query($dealsql);
				$this->auditlog->auditlog_info($this->lang->line('label_Qrconfig'),$this->input->post('qrtitle')." Qrcode updated");




				
				
				//print_r($_POST['custom']);
				if(isset($_POST['custom'])){
					$arrs=array_keys($_POST['custom']);
					//print_r($arrs);exit;
					for($k=0;$k<sizeof($arrs);$k++){
						if(sizeof($_POST['custom'][$arrs[$k]])>1){
								$x=implode(",",$_POST['custom'][$arrs[$k]]);
							}
							else{
								$x=$_POST['custom'][$arrs[$k]];
							}
							$this->db->set('value',$x);
							$this->db->where('dataid',$qrdeals->dealid);
							$this->db->update($this->session->userdata('bid').'_customfieldsvalue');
						}
					} 
				 
				  }
			return $id;
		}else{
			$uploads_dir = $this->config->item('upload_path');
			$tmp_name = $_FILES['clogo']['tmp_name'];
			$name = rand().$_FILES['clogo']['name'];
			move_uploaded_file($tmp_name,$uploads_dir."/".$name);
			$eid=$this->db->query("SELECT COALESCE(MAX(`qrid`),0)+1 as id FROM qr")->row()->id;
			$this->db->set('qrid',$eid);
			$this->db->set('bid',$this->session->userdata('bid'));
			$this->db->set('uid',$this->session->userdata('uid'));
			$this->db->set('qrtitle',$this->input->post('qrtitle'));
			$this->db->set('source',$this->input->post('source'));
			$this->db->set('description',$this->input->post('description'));
			$this->db->set('company_logo', $name);
			$this->db->set('qruse',implode(",",$this->input->post('qruse')));
			if(in_array(1,$_POST['qruse'])) { $this->db->set('gid',$this->input->post('gid')); }
			if(in_array(2,$_POST['qruse'])) { $this->db->set('webaddress',$this->input->post('website_name')); }
			if(in_array(5,$_POST['qruse'])){$this->db->set('video',$this->input->post('video'));}
			if(in_array(6,$_POST['qruse'])){$this->db->set('qraddress',$this->input->post('qraddress'));}
			$this->db->set('status','1');
			$this->db->insert('qr');
			$this->auditlog->auditlog_info($this->lang->line('label_Qrconfig'),$this->input->post('qrtitle')."Generated New Qrcode");
			if(in_array(4,$_POST['qruse'])){
				$dealsql = "REPLACE INTO qrdeals SET 
							qrid		= '".$eid."'
							,bid			= '".$this->session->userdata('bid')."'
							,dealtitle		= '".$this->input->post('dealtitle')."'
							,description	= '".$this->input->post('description')."'
							,address		= '".$this->input->post('address')."'
							,validupto		= '".$this->input->post('validupto')."'
							,phone			= '".$this->input->post('phone')."'
							,replymessage	= '".$this->input->post('replymessage')."'
							,dealvalue		= '".$this->input->post('dealvalue')."'";
				$this->db->query($dealsql);
				if(isset($_POST['custom'])){
					$arrs=array_keys($_POST['custom']);
					for($k=0;$k<sizeof($arrs);$k++){
						if(sizeof($_POST['custom'][$arrs[$k]])>1){
								$x=implode(",",$_POST['custom'][$arrs[$k]]);
							}
							else{
								$x=$_POST['custom'][$arrs[$k]];
							}
							$this->db->set('bid',$this->session->userdata('bid'));
							$this->db->set('modid ',19);
							$this->db->set('fieldid',$arrs[$k]);
							$this->db->set('dataid',$did);
							$this->db->set('value ',$x);
							$this->db->insert($this->session->userdata('bid').'_customfieldsvalue');
						}
					} 
				 
				  }
			return $eid;
		}
	}
	function manageQRList($ofset='0',$limit='20'){
		$q='';
		if(isset($_POST['submit'])){
			//print_r($_POST);exit;
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['title']) && $s['title']!='')?" and qrtitle like '%".$s['title']."%'":"";
	
		
		
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS qrid,qrtitle,qruse,imagename from qr where status=1 $q LIMIT $ofset,$limit
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
		
		
	}
	function manageDelQRList($ofset='0',$limit='20'){
		$q='';
		if(isset($_POST['submit'])){
			//print_r($_POST);exit;
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['title']) && $s['title']!='')?" and qrtitle like '%".$s['title']."%'":"";
	
		$res=array();
		$res['data']=$this->db->query("SELECT SQL_CALC_FOUND_ROWS qrid,qrtitle,qruse,imagename from qr where status=0 $q LIMIT $ofset,$limit
									   ")->result_array();
		$res['count'] = $this->db->query("SELECT FOUND_ROWS() as cnt")->row()->cnt;
		
		return $res;
		
		
	}
	function updateimg($id,$filename){
		$this->db->set("imagename",$filename);
		$this->db->where('qrid',$id);
		$this->db->update('qr');	
		return true;
	}
	function qruse_names($arr){
		$arrs=array();
		$sql=$this->db->query("SELECT * FROM `qruse` WHERE `qruseid` in($arr)");
		foreach($sql->result_array() as $row){
			$arrs[]=$row['name'];
		} 
		return implode(",",$arrs);
	}
	function getqrcodedetails($id){
		if($id!=""){
		$sql=$this->db->query("select * from qr where qrid=".$id);
		return $sql->row();
		}else{
		return "";	
		}
	}
	function removecompany_logo($id){
		$this->db->set("company_logo","");
		$this->db->update('qr');	
		$this->db->where('qrid',$id);
		return true;
	}
	function getqrdealinfo($qrid){
		if($qrid!=""){
			$sql=$this->db->query("select * from qrdeals where qrid=".$qrid." and bid=".$this->session->userdata('bid'));
			return $sql->row();
		}else{
			return "";	
			}	
	}
	function update_leadgenerate($id){
		
		$this->db->set('name',$this->input->post('name'));
		$this->db->set('mobile',$this->input->post('mobile'));
		$this->db->set('email',$this->input->post('email'));
		$this->db->set('query',$this->input->post('query'));
		$this->db->where('leadid',$id);
		$this->db->update("leadgeneration");	
		if(isset($_POST['custom'])){
					$arrs=array_keys($_POST['custom']);
					for($k=0;$k<sizeof($arrs);$k++){
						if(sizeof($_POST['custom'][$arrs[$k]])>1){
								$x=implode(",",$_POST['custom'][$arrs[$k]]);
							}
							else{
								$x=$_POST['custom'][$arrs[$k]];
							}
							
							$this->db->set('value ',$x);
							$this->db->where('dataid',$id);
							$this->db->update($this->session->userdata('bid').'_customfieldsvalue');
						}
					} 
		return true;		 
		
	}
	function qrreport($ofset='0',$limit='20'){
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		
		$sql="SELECT SQL_CALC_FOUND_ROWS l.leadid,l.name,
				l.mobile,l.email,l.query,q.qrtitle
				FROM leadgeneration l
				LEFT JOIN qr q on l.qrid=q.qrid
				WHERE l.status=1 
				LIMIT $ofset,$limit ";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='19'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('18');
		$keys = array();
		$header = array('#',$this->lang->line('lang_qrcodetitle'));
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
			$data = array($i,$rec['qrtitle']);
			$r = $this->configmodel->getDetail('18',$rec['leadid']);
			foreach($keys as $k){
				$v = isset($r[$k])?$r[$k]:"";
				array_push($data,$v);
			}
			if($opt_add || $opt_view || $opt_delete){
				$s='<a href="qrcode/Qrleadgenerate/'.$rec['leadid'].'"><span title="Edit" class="fa fa-edit"></span></a>
				   <a href="qrcode/deleteQrlead/'.$rec['leadid'].'" ><span title="Delete" class="glyphicon glyphicon-trash"></span></a>
				
				
				';
				
				$act = $s;
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function deleteleadgenrate($id){
		$this->db->set('status','0');
		$this->db->where('leadid',$id);
		$this->db->update("leadgeneration");	
		return true;
	}	
	
	function qrscanreport($ofset='0',$limit='20'){
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		
		$sql="SELECT SQL_CALC_FOUND_ROWS q.qrtitle,q.qrid,q.source,
				count(s.scanid) as cnt,g.groupname 
				FROM qr q
				LEFT JOIN scanreport s on s.qrid=q.qrid
				LEFT JOIN ".$this->session->userdata('bid')."_groups g on g.gid=q.gid
				GROUP BY q.qrid
				ORDER BY q.qrid DESC
				LIMIT $ofset,$limit ";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		
		$ret['header'] = array('#'
						,$this->lang->line('lang_qrcodetitle')
						,$this->lang->line('lang_qrcodesource')
						,$this->lang->line('mod_3')->groupname
						,$this->lang->line('label_count')
						);
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$data = array($i
						,'<a href="'.base_url().'qrcode/scanreport2/'.$rec['qrid'].'">'.$rec['qrtitle'].'</a>'
						,$rec['source']
						,$rec['groupname']
						,$rec['cnt']
						);
			array_push($list,$data);
			$i++;
		}
		$ret['rec'] = $list;
		return $ret;
	}
	
	function qrscanreport2($ofset='0',$limit='20'){
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		
		$sql="SELECT SQL_CALC_FOUND_ROWS q.qrtitle,s.*,q.qrid,
				count(s.platform) as cnt,g.groupname 
				FROM scanreport s
				LEFT JOIN qr q on s.qrid=q.qrid
				LEFT JOIN ".$this->session->userdata('bid')."_groups g on q.gid=g.gid
				WHERE q.qrid='".$this->uri->segment(3)."'
				GROUP BY s.platform
				ORDER BY q.qrid DESC
				LIMIT $ofset,$limit ";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		
		$ret['header'] = array('#'
						,$this->lang->line('lang_qrcodetitle')
						,$this->lang->line('label_platform')
						,$this->lang->line('label_browser')
						,$this->lang->line('label_count')
						);
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$data = array($i
						,$rec['qrtitle']
						,'<a href="'.base_url().'qrcode/scanreport3/'.$rec['qrid'].'/'.$rec['platform'].'">'.$rec['platform'].'</a>'
						,$rec['browser']
						,$rec['cnt']
						);
			array_push($list,$data);
			$i++;
		}
		$ret['rec'] = $list;
		return $ret;
	}
	
	function qrscanreport3($ofset='0',$limit='20'){
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		
		$sql="SELECT SQL_CALC_FOUND_ROWS q.qrtitle,s.*,g.groupname 
				FROM scanreport s
				LEFT JOIN qr q on s.qrid=q.qrid
				LEFT JOIN ".$this->session->userdata('bid')."_groups g on g.gid=q.gid
				WHERE q.qrid='".$this->uri->segment(3)."'
				AND s.platform='".$this->uri->segment(4)."'
				ORDER BY q.qrid DESC
				LIMIT $ofset,$limit ";
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		
		$ret['header'] = array('#'
						,$this->lang->line('lang_qrcodetitle')
						,$this->lang->line('label_platform')
						,$this->lang->line('label_browser')
						,$this->lang->line('label_location')
						,$this->lang->line('label_datetime')
						);
		$list = array();
		$i = $ofset+1;
		foreach($rst as $rec){
			$data = array($i
						,$rec['qrtitle']
						,$rec['platform']
						,$rec['browser']
						,$rec['location']
						,$rec['datetime']
						);
			array_push($list,$data);
			$i++;
		}
		$ret['rec'] = $list;
		return $ret;
	}
	function get_QRuse(){
		$res=array();
		$sql=$this->db->query("SELECT * FROM qruse");
		if($sql->num_rows()>0){
			foreach($sql->result_array() as $row){
				
				
			}
			
			
		}
		
		
	}
}
