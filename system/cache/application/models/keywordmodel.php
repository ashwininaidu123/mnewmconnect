<?php
class keywordmodel extends model
{
	function keywordmodel()
	{
		 parent::Model();
		 $this->load->model('auditlog');
		 $this->load->model('empmodel');
	}
	function forward()
	{
		$res=array(''=>'select','group'=>'Group','employee'=>'Employee');
		return $res;
	}
	function getshorcode()
	{
		$res=array();	
		$sql=$this->db->query("select * from shortcode");
		if($sql->num_rows()>0)
		{
			foreach($sql->result_array() as $r)
			$res[$r['codeid']]=$r['code'];
		}
		return $res;
	}
	function keyworduse()
	{
		$res=array();	
		$sql=$this->db->query("select * from keyword_use");
		$res['']=$this->lang->line('level_select');
		if($sql->num_rows()>0)
		{
			foreach($sql->result_array() as $r)
			$res[$r['keyword_useid']]=$r['keyworduse'];
		}
		return $res;
	}
	function addkeyword()
	{
		//print_r($_POST);exit;
		$arr=array_keys($_POST);
		$this->db->set('bid',$this->session->userdata('bid'));
		for($i=0;$i<sizeof($arr);$i++)
		{
			if($arr[$i]!="update_system"){
				if($arr[$i]!="custom"){
					if($arr[$i]!="group"){
						if($arr[$i]!="employee"){
							$this->db->set($arr[$i], $_POST[$arr[$i]]);
							
						}
					}	
				}
			}
		}	
		($this->input->post('fowardto_type')=="group")
			?$this->db->set('forwardto_id',$this->input->post('group'))
			:$this->db->set('forwardto_id',$this->input->post('employee'));
		$keyword_id=$this->db->query("SELECT COALESCE(MAX(`keyword_id`),0)+1 as id FROM `keword`")->row()->id;
		$this->db->set('keyword_id', $keyword_id);
		$this->db->set('status', 0);
		$this->db->insert('keword');
		$message=$this->input->post('keyword')." Keyword added successfully";
		$this->auditlog->auditlog_info($this->lang->line('level_Incoming_msg'),$message);
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
					$this->db->set('modid ',7);
					$this->db->set('fieldid',$arrs[$k]);
					$this->db->set('dataid',$gid);
					$this->db->set('value ',$x);
					$this->db->insert($this->session->userdata('bid').'_customfieldsvalue');
				}
			}
		return $keyword_id;

	}
	function managekeyword($bid,$ofset='0',$limit='20')
	{
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['keyword_use']) && $s['keyword_use']!='')?" and c.keyworduse like '%".$s['keyword_use']."%'":"";
		$q.=(isset($s['keyword']) && $s['keyword']!='')?" and a.keyword like '%".$s['keyword']."%'":"";
		$q.=(isset($s['code_id']) && $s['code_id']!='')?" and b.code like '%".$s['code_id']."%'":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:(($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0;
		 $sql="select a.status,a.`keyword_id`,a.`keyword`,a.`default_msg`,
						  b.code as code_id,c.keyworduse as keyword_use
						  from keword a,shortcode b,keyword_use c
						  where a.status=1 and a.`code_id`=b.codeid and a.bid='".$this->session->userdata('bid')."'
						  and a.keyword_use=c.keyword_useid $q  limit $ofset,$limit";
		
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='7'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('7');
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
			$r = $this->configmodel->getDetail('7',$rec['keyword_id']);
			foreach($keys as $k){
				if($k=='filename'){
					$v = '<embed src="'.site_url('sounds/'.$r[$k]).'" volume="100"
						  loop="false" controls="console" height="29" wmode="transparent"
						  autostart="FALSE" width="150" hidden="false">';
				}else{
					$v = isset($r[$k])?$r[$k]:"";
				}
				array_push($data,$v);
			}
			if($opt_add || $opt_view || $opt_delete){
				if($r['keyword_use']!="Ussd"){
					$urk="keyword/showsubkeywords/".$r['keyword_id'];
					$adding="keyword/subkeyword/".$r['keyword_id'];
				}else{
					$urk="keyword/ListingUssd/".$r['keyword_id'];
					$adding="keyword/for_popup_addusd/".$r['keyword_id'];
				}
				$act = '
						<a href="keyword/addkeyword/'.$r['keyword_id'].'"><span title="Edit" class="fa fa-edit"></span></a>
						<a href="'.$adding.'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><img src="system/application/img/icons/bricks.png" title="Add Subkeyword" /></a>
						<a href="'.$urk.'"><img src="system/application/img/icons/cog.png" title="List Subkeyword" /></a>
				';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
		

	}
	function ListUssd($data,$ofset='0',$limit='20')
	{
		$q='';
		
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:(($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0;
		$sql="select a.optiontext,a.ussd_id,b.keyword
						  from keyword_ussd a
						  left join keword b on b.keyword_id=a.keyword_id
						  where a.keyword_id='".$data['keyword']."' and a.parentopt='".$data['parentid']."' 
						  and a.bid='".$this->session->userdata('bid')."'
						  $q  limit $ofset,$limit";
		
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='17'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('17');
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
			$r = $this->configmodel->getDetail('17',$rec['ussd_id']);
			foreach($keys as $k){
				if($k=='filename'){
					$v = '<embed src="'.site_url('sounds/'.$r[$k]).'" volume="100"
						  loop="false" controls="console" height="29" wmode="transparent"
						  autostart="FALSE" width="150" hidden="false">';
				}else{
					$v = isset($r[$k])?$r[$k]:"";
				}
				array_push($data,$v);
			}
			if($opt_add || $opt_view || $opt_delete){
				$act = '
						<a href="keyword/addussd/'.$r['keyword_id'].'/'.$r['ussd_id'].'"><span title="Edit" class="fa fa-edit"></span></a>
						<a href="keyword/addussd_popup/'.$r['keyword_id'].'/'.$r['ussd_id'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><img src="system/application/img/icons/bricks.png" title="Add Subkeyword" /></a>
						<a href="keyword/showsubmenulist/'.$r['ussd_id'].'"><img src="system/application/img/icons/cog.png" title="List Subkeyword" /></a>
				';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
		

	}
	function ListsubUssd($optioid,$ofset='0',$limit='20')
	{
		$q='';
		
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:(($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0;
		echo $sql="select a.optiontext,a.ussd_id,b.keyword
						  from keyword_ussd a
						  left join keword b on b.keyword_id=a.keyword_id
						  where a.parentopt=$optioid and a.bid='".$this->session->userdata('bid')."'
						  $q  limit $ofset,$limit";
		
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='17'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('17');
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
			$r = $this->configmodel->getDetail('17',$rec['ussd_id']);
			foreach($keys as $k){
				if($k=='filename'){
					$v = '<embed src="'.site_url('sounds/'.$r[$k]).'" volume="100"
						  loop="false" controls="console" height="29" wmode="transparent"
						  autostart="FALSE" width="150" hidden="false">';
				}else{
					$v = isset($r[$k])?$r[$k]:"";
				}
				array_push($data,$v);
			}
			if($opt_add || $opt_view || $opt_delete){
				$act = '
						<a href="keyword/addussd/'.$r['keyword_id'].'/'.$r['ussd_id'].'"><span title="Edit" class="fa fa-edit"></span></a>
						
						
				';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;
		

	}
	function managesubkeyword($bid,$ofset='0',$limit='20',$id)
	{
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['subkeyword']) && $s['subkeyword']!='')?" and subkeyword like '%".$s['subkeyword']."%'":"";
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:(($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0;
		$sql="SELECT `subkeyword_id`,subkeyword from subkeywordid where `keyword_id`=$id
			  AND bid=".$this->session->userdata('bid').$q;	
			//echo $sql;
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		//echo $ret['count'];
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='13'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('13');
		$keys = array();
		$header = array('#');
		//echo "<pre>";
		//print_r($fieldset);
		
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
		//echo "<pre>";
		//print_r($rst);
		$act='';
		foreach($rst as $rec){
			$data = array($i);//echo $rec['subkeyword_id'];
			$r = $this->configmodel->getDetail('13',$rec['subkeyword_id']);
			//print_r($keys);
			
			foreach($keys as $k){
				 $v=isset($r[$k])?$r[$k]:"";
				array_push($data,$v);
			}
			if($opt_add || $opt_view || $opt_delete){
				$act= '<a href="keyword/subkeyword/'.$id.'/'.$rec['subkeyword_id'].'"><span title="Edit" class="fa fa-edit"></span></a>';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		//print_r($list);exit;
		$ret['rec'] = $list;
		return $ret;
		

	}
	function subkeywordlist($id)
	{
		$sql=$this->db->query("SELECT a.`subkeyword_id`,a.`subkeyword`,a.`customvalue`,a.`replymsg`,b.code from subkeywordid a,shortcode b where a.`keyword_id`=$id and a.`code_id`=b.codeid and a.bid=".$this->session->userdata('bid'));
		if($sql->num_rows()>0)
		{
			return $sql->result_array();
		}
	}
	function get_keyword($id)	
	{
		$sql=$this->db->query("select * from keword where keyword_id=$id");
		return $sql->result_array();
	}
	function updatekeyword($id)
	{
		$arr=array_keys($_POST);
		//$this->db->set('bid',$this->session->userdata('bid'));
		for($i=0;$i<sizeof($arr);$i++)
		{
			if($arr[$i]!="update_system"){
				if($arr[$i]!="custom"){
					if($arr[$i]!="group"){
						if($arr[$i]!="employee"){
							$this->db->set($arr[$i], $_POST[$arr[$i]]);
							
						}
					}	
				}
			}
		}	
		($this->input->post('fowardto_type')=="group")?$this->db->set('forwardto_id',$this->input->post('group')):$this->db->set('forwardto_id',$this->input->post('employee'));
		$this->db->where('keyword_id',$id);
		$this->db->update('keword');
		
		
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
					$this->db->where('modid ',13);
					$this->db->where('fieldid',$arrs[$k]);
					$this->db->update($this->session->userdata('bid').'_customfieldsvalue');
				}
		}
		
		
		
	}
	function addsubkeyword($id){
			$res=$this->get_keyword($id);
			$arr=array_keys($_POST);
			$subkeyword_id=$this->db->query("SELECT COALESCE(MAX(`subkeyword_id`),0)+1 as id FROM `subkeywordid`")->row()->id;
			$this->db->set('subkeyword_id',$subkeyword_id);
			$this->db->set('bid',$this->session->userdata('bid'));
			$this->db->set('code_id', $res[0]['code_id']); 
			$this->db->set('keyword_id', $id); 
			
			$this->db->set('subkeyword',$_POST['subkeyword']); 
			$this->db->set('customvalue',$_POST['customvalue']); 
			$this->db->set('replymsg',mysql_real_escape_string($_POST['replymsg'])); 
			$this->db->insert('subkeywordid');
			
			$message1=$_POST['subkeyword'][$i]." Subkeyword added to".$res[0]['keyword']."keyword  successfully";
			$this->auditlog->auditlog_info($this->lang->line('level_Incoming_msg'),$message1);
			return $this->db->insert_id();
	}
	function updatesubkeyword($kid,$id){
			$this->db->set('subkeyword',$_POST['subkeyword']); 
			$this->db->set('customvalue',$_POST['customvalue']); 
			$this->db->set('replymsg',mysql_real_escape_string($_POST['replymsg'])); 
			$this->db->where('keyword_id', $kid); 
			$this->db->where('subkeyword_id', $id); 
			$this->db->update('subkeywordid');
			
			$message1=$_POST['subkeyword'][$i]." Subkeyword updated to".$res[0]['keyword']."keyword  successfully";
			$this->auditlog->auditlog_info($this->lang->line('level_Incoming_msg'),$message1);
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
					$this->db->where('modid ',13);
					$this->db->where('fieldid',$arrs[$k]);
					$this->db->update($this->session->userdata('bid').'_customfieldsvalue');
				}
			}
	}
	function get_recevied_messages($bid,$ofset='0',$limit='20'){
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$q='';
		if(isset($_POST['submit'])){
			$this->session->set_userdata('search',$_POST);
		}else{
			$this->session->unset_userdata('search');
		}
		if($this->session->userdata('search')){
			$s = $this->session->userdata('search');
		}
		$q.=(isset($s['code_id']) && $s['code_id']!='')?" and c.code like '%".$s['code_id']."%'":"";
		$q.=(isset($s['keyword']) && $s['keyword']!='')?" and a.keyword like '%".$s['keyword']."%'":"";
		$q.=(isset($s['subkeyword']) && $s['subkeyword']!='')?" and a.subkeyword like '%".$s['subkeyword']."%'":"";
		$limit = ($roleDetail['role']['recordlimit']>($ofset+$limit))?$limit
					:((($roleDetail['role']['recordlimit'] - $ofset)>0)?($roleDetail['role']['recordlimit'] - $ofset):0);
		//$sql="select SQL_CALC_FOUND_ROWS eid from ".$this->session->userdata('bid')."_employee where status!=2 $q limit $ofset,$limit";
		//$sql="select SQL_CALC_FOUND_ROWS incid from ".$this->session->userdata('bid')."_keywordinbox where 1 limit $ofset,$limit";
		$sql="select SQL_CALC_FOUND_ROWS a.`incid`,a.`from`,a.`keyword`,
			  a.`subkeyword`,a.`date_time`,b.empname as eid,
			  c.code as code_id from ".$this->session->userdata('bid')."_keywordinbox a
			  ,shortcode c,".$this->session->userdata('bid')."_employee b 
			  where a.eid=b.eid and a.`code_id`=c.codeid $q limit $ofset,$limit";
		//echo $sql;
		$rst = $this->db->query($sql)->result_array();
		$rst1 = $this->db->query("SELECT FOUND_ROWS() as cnt");
		$ret['count'] = $rst1->row()->cnt;
		foreach($roleDetail['modules'] as $mod){
			if($mod['modid']=='15'){
				$opt_add 	= $mod['opt_add'];
				$opt_view 	= $mod['opt_view'];
				$opt_delete = $mod['opt_delete'];
			}
		}
		$fieldset = $this->configmodel->getFields('15');
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
			$r = $this->configmodel->getDetail('15',$rec['incid']);
			foreach($keys as $k){
				$v = isset($r[$k])?$r[$k]:"";
				array_push($data,$v);
			}
			if($opt_add || $opt_view || $opt_delete){
				$act = '<a href="keyword/inboxmess/'.$r['incid'].'"><span title="Edit" class="fa fa-edit"></span></a>
						<a href="keyword/activerecords/'.$r['incid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="fa fa-file-text"  title="List Employee Group"></span></a>
						';
				array_push($data,$act);
			}
			$i++;
			array_push($list,$data);
		}
		$ret['rec'] = $list;
		return $ret;

	}
	function get_count_messages()
	{	
		$sql=$this->db->query("select a.`incid`,a.`from`,a.`keyword`,a.`subkeyword`,a.`date_time`,b.empname,c.code from ".$this->session->userdata('bid')."_keywordinbox a,shortcode c,".$this->session->userdata('bid')."_employee b where a.eid=b.eid and a.`code_id`=c.codeid");
		return $sql->num_rows();
	}
	function shortcode_select($id='')
	{
		if($id!=''){
		$sql=$this->db->query("select * from shortcode where code='$id'");
		$res=$sql->row();
		return $res->codeid;
		}
	}
	function keyworduse_select($id='')
	{
		if($id!=''){
		$sql=$this->db->query("select * from keyword_use where keyworduse='$id'");
		$res=$sql->row();
		return $res->keyword_useid;
		}
	}
	function updateincomingmessages($id)
	{
		//print_r($_POST['custom']);
		
		if(isset($_POST['custom'])){
			$arrs=array_keys($_POST['custom']);
			//print_r($arrs);exit;
			for($k=0;$k<sizeof($arrs);$k++){
				//echo $arrs[$k];
				if(sizeof($_POST['custom'][trim($arrs[$k])])>0){
						(is_array($_POST['custom'][trim($arrs[$k])]))?$x=implode(",",$_POST['custom'][trim($arrs[$k])]):$x=$_POST['custom'][$arrs[$k]];
						
					}
					else{
						$x=$_POST['custom'][$arrs[$k]];
					}
					//echo $x;

					$sql=$this->db->query("select fieldid from ".$this->session->userdata('bid')."_customfieldsvalue where fieldid=".$arrs[$k]." and modid=15");
					if($sql->num_rows()>0){
					
					$this->db->set('value ',$x);
					$this->db->where('modid ',15);
					$this->db->where('fieldid',$arrs[$k]);
					$this->db->update($this->session->userdata('bid').'_customfieldsvalue');
					}else{
					$this->db->set('bid',$this->session->userdata('bid'));
					$this->db->set('modid ',15);
					$this->db->set('fieldid',$arrs[$k]);
					$this->db->set('dataid',$id);
					$this->db->set('value ',$x);
					$this->db->insert($this->session->userdata('bid').'_customfieldsvalue');
					}

				}
		}
		return 1;
	
	}
	function addussd_parent($id,$parent){
		$keyword_id=$this->db->query("SELECT COALESCE(MAX(`ussd_id`),0)+1 as id FROM `keyword_ussd`")->row()->id;
			$this->db->set('ussd_id', $keyword_id);
			$this->db->set('bid',$this->session->userdata('bid'));
			$this->db->set('keyword_id',$id);
			$this->db->set('optioncode',$this->input->post('optioncode'));
			$this->db->set('optiontext',$this->input->post('optiontext'));
			$this->db->set('parentopt',$parent);
			$this->db->insert('keyword_ussd');
			return true;
		
	}
	function addussd($id,$parent=''){
		if($parent!=""){
			$this->db->set('optioncode',$this->input->post('optioncode'));
			$this->db->set('optiontext',$this->input->post('optiontext'));	
			$this->db->where('ussd_id', $parent);
			$this->db->update('keyword_ussd');
		}else{
			$keyword_id=$this->db->query("SELECT COALESCE(MAX(`ussd_id`),0)+1 as id FROM `keyword_ussd`")->row()->id;
			$this->db->set('ussd_id', $keyword_id);
			$this->db->set('bid',$this->session->userdata('bid'));
			$this->db->set('keyword_id',$id);
			$this->db->set('optioncode',$this->input->post('optioncode'));
			$this->db->set('optiontext',$this->input->post('optiontext'));
			$this->db->insert('keyword_ussd');
			return true;
		  }	
	}
}
 /*end of keywordmodel */
