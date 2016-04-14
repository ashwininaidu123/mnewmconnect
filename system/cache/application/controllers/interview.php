<?php
class Interview extends Controller {
	var $data,$roleDetail,$access;
	function Interview(){
		parent::controller();
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('systemmodel');
		$this->load->model('configmodel');
		$this->load->model('interviewmodel');
		$this->roleDetail=$this->empmodel->getRoledetail($this->session->userdata('roleid'));
	}
	function feature_access($access){
		$show=0;
		$checklist=$this->systemmodel->checked_featuremanage();
		if(in_array($access,$checklist)){
			$show=1;
			}
		return $show;
	}
	function index(){
		redirect('interview/listGroups');
	}
	/* Interview Group Module START*/
	function groupAdd($id=''){
		if(!$this->feature_access(15))redirect('Employee/access_denied');
		$id = ($id != '') ? $id : (isset($_POST['id']) ? $_POST['id'] : '');
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('group_name', 'Group Name', 'required|min_length[4]|max_length[50]|alpha_numeric|callback_uniquegroup');
			$this->form_validation->set_rules('intw_id', 'Interview Id', 'required');
			$this->form_validation->set_rules('eid', 'Interviewer', 'required');
			if(!$this->form_validation->run() == FALSE){	
				$res=$this->interviewmodel->create_group($id);
				redirect('interview/listGroups');
			}	
		}
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_groupfrm');
		$data['module']['title'] = "Interview Group Add";
		$fieldset = $this->configmodel->getFields('41',$bid,'1');
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('41',$id,$bid);
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['41']['opt_add']) redirect('Employee/access_denied');
		$number = '';
		for ($i=1;$i<=5;$i++){
			$number .= ($i ==1) ? rand(1,9) : rand(0,9);
		}
		$intwID = $number;
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] ){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=> ($field['fieldname'] == 'interviewer')? form_dropdown('eid',$this->groupmodel->employee_list(),(isset($itemDetail['eid']) ? $itemDetail['eid'] : ''),'id="eid" class="auto"') 
											  : (($field['fieldname'] == 'group_desc')? form_textarea(array('name' => 'group_desc','value' => (isset($itemDetail[$field['fieldname']]) ? $itemDetail[$field['fieldname']] : ''),'id' => 'group_desc'))
											  : (form_input(array(
												'name'      => $field['fieldname'],
												'id'        => $field['fieldname'],
												'value'     => isset($itemDetail[$field['fieldname']]) ? $itemDetail[$field['fieldname']] : (($field['fieldname'] == 'intw_id')? $intwID :$this->session->userdata($field['fieldname']))
											))
												)));
							 }				
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$advsearch['custom['.$field['fieldid'].']']=$field['customlabel'];
					$cf = array('label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,'','search')
						);
					array_push($formFields,$cf);
				}
			}
		}
		$data['form'] = array(
		                    'form_attr'=>array('action'=>'interview/groupAdd/','name'=>'intwgrpadd'),
							//~ 'open'=>form_open_multipart('interview/groupAdd/',array('name'=>'intwgrpadd','class'=>'form','id'=>'intwgrpadd','method'=>'post'),array("id"=>$id)),
							'fields'=>$formFields,
							'parentids'=>$parentbids,
							'busid'=>$bid,
							'pid'=>$this->session->userdata('pid'),
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function listGroups($type=''){
		if(!$this->feature_access(15))redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['41']['opt_view']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '30';
		$data['itemlist'] = $this->interviewmodel->getgrouplist($bid,$ofset,$limit,$type);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('interview/listGroups/'.$type.'/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
						,'uri_segment'=>4				
				));
		$data['paging'] = $this->pagination->create_links();
		$data['module']['title'] = "Interview Groups [".$data['itemlist']['count']."]";
		$this->sysconfmodel->data['html']['title'] .= " | List Interview Groups";
		$this->sysconfmodel->data['links'] = '<a href="interview/groupAdd"><span title="Add Group" class="glyphicon glyphicon-plus-sign"></span></a>';
		$fieldset = $this->configmodel->getFields('41',$bid);
		$formFields = array();
		$advsearch=array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){ $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>($field['fieldname']=='eid')
												?form_dropdown('eid',$this->groupmodel->employee_list(),''," class='auto'")
												:form_input(array(
													'name'      => $field['fieldname'],
													'id'        => $field['fieldname'],
													'value'     => $this->session->userdata($field['fieldname'])))
											);
										 }
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$cf = array('label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,'','search')
						);
					array_push($formFields,$cf);
				}
			}
		}
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$data['form'] = array(
			'open'=>form_open_multipart('interview/listGroups/',array('name'=>'listgrp','class'=>'form','id'=>'listgrp','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>$advsearch,
			'parentids'=>$parentbids,
			'busid'=>$bid,
			'pid'=>$this->session->userdata('pid'),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function deleteGroup($id){
		if(!$this->feature_access(15))redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['41']['opt_delete']) redirect('Employee/access_denied');
		$this->interviewmodel->deleteGroup($id,$bid,'1');
		return 1;
	}
	function undelete($gid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->interviewmodel->deleteGroup($gid,$bid,'2');
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg',"Record restored Successfully");
		redirect('interview/listGroups/del');
	}
	function active_group($id=''){
		if(!$this->feature_access(1))redirect('Employee/access_denied');
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$this->sysconfmodel->data['html']['title'] .= " | Interview Group";
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('label_groupfrm');
		$fieldset = $this->configmodel->getFields('41',$bid);
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('41',$id,'',$bid);
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['41']['opt_add']) redirect('Employee/access_denied');
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){ 
						$v = '';
						if($field['fieldname']=='interviewer'){
							$v = '<a href="Employee/activerecords/'.$itemDetail['eid'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$itemDetail[$field['fieldname']].'</a>';
						}else{
							$v = isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:"";
						}
						$cf = array('label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).' : </label>',
									'field'=>$v
							);
						array_push($formFields,$cf);
						
				}
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$cf = array('label'=>'<label>'.$field['customlabel'].' : </label>',
								'field'=> isset($itemDetail['custom['.$field['fieldid'].']'])?
											$itemDetail['custom['.$field['fieldid'].']']:''
						);
					array_push($formFields,$cf);
				}
			}
		}
		$data['form'] = array(
					'open'=>form_open('interview/addGroup/'.$id,array('name'=>'listgrp','class'=>'form','id'=>'listgrp','method'=>'post')),
					'fields'=>$formFields,
					'close'=>form_close()
				);
				$this->load->view('active_view',$data);
	}
	function addQBtoGrp($gid=''){
		if(!$this->feature_access(1))redirect('Employee/access_denied');
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if($this->input->post('update_system')){
			$res=$this->interviewmodel->addQBtoGrp($gid);		
			if($res==0){
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', "Question bank already exists");
				redirect('interview/addQBtoGrp/'.$gid);
			}else{
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', "Question banks added Succesfully");
				redirect('interview/listGroups/');
			}
		}
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['41']['opt_add']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['html']['title'] .= " | Add Question Bank to Interview Group ";
		$data['module']['title'] = "Add Question Bank to Interview Group";
		$formFields[] = array('label'=>' ',
							  'field'=>form_multiselect('selQBs[]',$this->interviewmodel->getQBs($gid),'',"id='selQBs'")
							);
		$data['form'] = array(
		           // 'form_attr'=>array('action'=>'group/add_group/'.$id,'name'=>'addgroup'),
					'open'=>form_open_multipart(current_url()
								,array('name'=>'addemp','class'=>'form','id'=>'addemp','method'=>'post')
								,array('bid'=>$bid
								  ,'gid'=>$gid
								  )),
					'fields'=>$formFields,
					'close'=>form_close(),
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function listQBsGrp($gid){
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('label_groupemp');
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '30';
		$header = array("Interview Group","Question Bank","Action");
		$data['itemlist']['header'] = $header;
		$qblist=$this->interviewmodel->listQBsGrp($gid,$ofset,$limit);
		$roleDetail = $this->roleDetail;
		$opt_add=$roleDetail['modules']['41']['opt_add']; 
		$opt_delete=$roleDetail['modules']['41']['opt_delete']; 
		$rec = array();
		if(count($qblist['data'])>0)
		foreach ($qblist['data'] as $item){
			$opt='';
			$s=($item['status']=="1")?'<a href="'.site_url('interview/disQB/'.$item['qb_id']."/".$item['gid']).'"><span class="fa fa-unlock" title="Disable"></a>':'<a href="'.site_url('interview/disQB/'.$item['qb_id']."/".$item['gid']).'"><span class="fa fa-lock" title="Enable"></a>';
			$opt.=($opt_delete)?'&nbsp;<a href="'.site_url('interview/delQBtoGrp/'.$item['qb_id']."/".$item['gid']).'">
						<span title="Delete Question bank of Group" class="glyphicon glyphicon-trash"></span>
				  </a>':'';
			$opt.=($opt_add)?$s:'';	 
			$r = array((($item['status']=="1") ? $item['group_name'] : '<font color=red>'.$item['group_name'].'</font>')
				,(($item['status']=="1") ? $item['name'] : '<font color=red>'.$item['name'].'</font>'));
			$r = array_merge($r,array($opt));
			$rec[] = $r;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $qblist['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('interview/listQBsGrp/'.$this->uri->segment(3))
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_groupemp');
		$data['links']=$data['links']='<a href="interview/addQBsGroup/'.$this->uri->segment(3).'"><span title="Add Qustion Bank" class="glyphicon glyphicon-plus-sign"></span></a>';
		$formFields = array();
		$cf=array('label'=>'<label for="groupname">Employee Name : </label>',
				  'field'=>form_input(array(
										'name'        => 'empname',
										'id'          => 'empname',
										'value'       => $this->session->userdata('empname'))));
						array_push($formFields,$cf);
	
		$data['form'] = array(
			'open'=>form_open_multipart('interview/listQBsGrp/'.$gid,array('name'=>'dbaddgrp','class'=>'form','id'=>'dbaddgrp','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function delQBtoGrp($qb_id,$gid){
		$res=$this->interviewmodel->delQBtoGrp($qb_id,$gid);
		redirect('interview/listQBsGrp/'.$gid);
	}
	function disQB($qb_id,$gid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->interviewmodel->disQB($qb_id,$gid,$bid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', ($res)?'Question Bank Enabled Succesfully':'Question Bank Disabled Succesfully');
		redirect('interview/listQBsGrp/'.$gid);
	}
	/* Interview Group Module END*/
	/* Question Bank Module START*/
	function addQBank($id=''){
		if(!$this->feature_access(15))redirect('Employee/access_denied');
		$id = ($id != '') ? $id : (isset($_POST['id']) ? $_POST['id'] : '');
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('name', 'Question Bank name', 'required|min_length[4]|max_length[50]|alpha_numeric|callback_uniquegroup');
			if(!$this->form_validation->run() == FALSE){	
				$res=$this->interviewmodel->create_qb($id);
				redirect('interview/listQbank');
			}	
		}
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_groupfrm');
		$data['module']['title'] = "Question Bank";
		$fieldset = $this->configmodel->getFields('42',$bid,'1');
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('42',$id,$bid);
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['42']['opt_add']) redirect('Employee/access_denied');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] ){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=> ($field['fieldname'] == 'description') ? form_textarea(array('name' => 'description','value' =>          (isset($itemDetail[$field['fieldname']]) ? $itemDetail[$field['fieldname']] : ''),'id' => 'description'))
											  : form_input(array(
												'name'      => $field['fieldname'],
												'id'        => $field['fieldname'],
												'value'     => isset($itemDetail[$field['fieldname']]) ? $itemDetail[$field['fieldname']] : ''
											    )));
							 }				
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$advsearch['custom['.$field['fieldid'].']']=$field['customlabel'];
					$cf = array('label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,'','search')
						);
					array_push($formFields,$cf);
				}
			}
		}
		$data['form'] = array(
		                    'form_attr'=>array('action'=>'interview/addQBank/','name'=>'qbadd'),
							//~ 'open'=>form_open_multipart('interview/addQBank/',array('name'=>'qbadd','class'=>'form','id'=>'qbadd','method'=>'post'),array("id"=>$id)),
							'fields'=>$formFields,
							'parentids'=>$parentbids,
							'busid'=>$bid,
							'pid'=>$this->session->userdata('pid'),
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function listQbank($type=''){
		if(!$this->feature_access(15))redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['42']['opt_view']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '30';
		$data['itemlist'] = $this->interviewmodel->getqblist($bid,$ofset,$limit,$type);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('interview/listQbank/'.$type.'/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
						,'uri_segment'=>4				
				));
		$data['paging'] = $this->pagination->create_links();
		$data['module']['title'] = "Question Banks [".$data['itemlist']['count']."]";
		$this->sysconfmodel->data['html']['title'] .= " | Question Banks";
		$this->sysconfmodel->data['links'] = '<a href="interview/addQBank"><span title="Add Question Bank" class="glyphicon glyphicon-plus-sign"></span></a>';
		$fieldset = $this->configmodel->getFields('42',$bid);
		$formFields = array();
		$advsearch=array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){ $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>form_input(array(
													'name'      => $field['fieldname'],
													'id'        => $field['fieldname'],
													'value'     => $this->session->userdata($field['fieldname'])))
											);
										 }
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$cf = array('label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,'','search')
						);
					array_push($formFields,$cf);
				}
			}
		}
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$data['form'] = array(
			'open'=>form_open_multipart('interview/listQbank/',array('name'=>'listqb','class'=>'form','id'=>'listqb','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>$advsearch,
			'parentids'=>$parentbids,
			'busid'=>$bid,
			'pid'=>$this->session->userdata('pid'),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function deleteqb($id){
		if(!$this->feature_access(15))redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['42']['opt_delete']) redirect('Employee/access_denied');
		$this->interviewmodel->deleteQB($id,$bid,'1');
		return 1;
	}
	function undeleteqb($qbid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->interviewmodel->deleteQB($qbid,$bid,'2');
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg',"Record restored Successfully");
		redirect('interview/listQbank/del');
	} 
	function activeQB($id=''){
		if(!$this->feature_access(1))redirect('Employee/access_denied');
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$this->sysconfmodel->data['html']['title'] .= " | Question Bank";
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data['module']['title'] = "Question Bank";
		$fieldset = $this->configmodel->getFields('42',$bid);
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('42',$id,'',$bid);
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['42']['opt_add']) redirect('Employee/access_denied');
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){ 
						$v = '';
						$v = isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:"";
						$cf = array('label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).' : </label>',
									'field'=>$v
							);
						array_push($formFields,$cf);
				}
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$cf = array('label'=>'<label>'.$field['customlabel'].' : </label>',
								'field'=> isset($itemDetail['custom['.$field['fieldid'].']'])?
											$itemDetail['custom['.$field['fieldid'].']']:''
						);
					array_push($formFields,$cf);
				}
			}
		}
		$data['form'] = array(
					'open'=>form_open('interview/addQB/'.$id,array('name'=>'addqb','class'=>'form','id'=>'addqb','method'=>'post')),
					'fields'=>$formFields,
					'close'=>form_close()
				);
				$this->load->view('active_view',$data);
	}
	function addQuestionToQB($qb_id=''){
		if(!$this->feature_access(1))redirect('Employee/access_denied');
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if($this->input->post('update_system')){
			$qb_id = $_POST['qb_id'];
			$res=$this->interviewmodel->addQuestionToQB($qb_id);		
			if($res==0){
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', "Question already exists");
				redirect('interview/addQuestionToQB/'.$qb_id);
			}else{
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', "Questions added Succesfully");
				redirect('interview/listQuestionsToQB/'.$qb_id);
			}
		}
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['42']['opt_add']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['html']['title'] .= " | Add Question to QUestion Bank ";
		$data['module']['title'] = "Add Question to Question Bank";
		$formFields[] = array('label'=>' ',
							  'field'=>form_multiselect('selQues[]',$this->interviewmodel->getQuestions($qb_id),'',"id='selQues'")
							);
		$data['form'] = array(
		
					'open'=>form_open_multipart(current_url()
								,array('name'=>'addquestoqb','class'=>'form','id'=>'addquestoqb','method'=>'post')
								,array('bid'=>$bid
								  ,'qb_id'=>$qb_id
								  )),
					'fields'=>$formFields,
					'close'=>form_close(),
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function listQuestionsToQB($qb_id){
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = "List Questions of Question Bank";
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '30';
		$header = array("Question Bank" , "Questions" , "Action");
		$data['itemlist']['header'] = $header;
		$qblist=$this->interviewmodel->listQuestionsToQB($qb_id,$ofset,$limit);
		$roleDetail = $this->roleDetail;
		$opt_add=$roleDetail['modules']['42']['opt_add']; 
		$opt_delete=$roleDetail['modules']['42']['opt_delete']; 
		$rec = array();
		if(count($qblist['data'])>0)
		foreach ($qblist['data'] as $item){
			$opt='';
			$s=($item['status']=="1")?'<a href="'.site_url('interview/disQuesToQB/'.$item['qid']."/".$item['qb_id']).'"><span class="fa fa-unlock" title="Disable"></a>':'<a href="'.site_url('interview/disQuesToQB/'.$item['qid']."/".$item['qb_id']).'"><span class="fa fa-lock" title="Enable"></a>';
			$opt.=($opt_delete)?'&nbsp;<a href="'.site_url('interview/delQuestoQB/'.$item['qid']."/".$item['qb_id']).'">
					<span title="Delete Question bank of Group" class="glyphicon glyphicon-trash"></span>
				  </a>':'';
			$opt.=($opt_add)?$s:'';	 
			$r = array((($item['status']=="1") ? $item['name'] : '<font color=red>'.$item['name'].'</font>')
				,(($item['status']=="1") ? $item['question'] : '<font color=red>'.$item['question'].'</font>'));
			$r = array_merge($r,array($opt));
			$rec[] = $r;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $qblist['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('interview/listQuestionsToQB/'.$this->uri->segment(3))
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_groupemp');
		$data['links']=$data['links']='<a href="interview/addQuestionToQB/'.$this->uri->segment(3).'"><span title="Add Question to Qustion Bank" class="glyphicon glyphicon-plus-sign"></span></a>';
		$formFields = array();
		$cf=array('label'=>'<label for="groupname">Question : </label>',
				  'field'=>form_input(array(
										'name'        => 'question',
										'id'          => 'question',
										'value'       =>  '')));
						array_push($formFields,$cf);
		$data['form'] = array(
			'open'=>form_open_multipart('interview/listQuestionsToQB/'.$qb_id,array('name'=>'questoqb','class'=>'form','id'=>'questoqb','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function delQuestoQB($qb_id,$qid){
		$res=$this->interviewmodel->delQuestoQB($qb_id,$qid);
		redirect('interview/listQuestionsToQB/'.$qb_id);
	}
	function disQuesToQB($qb_id,$qid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->interviewmodel->disQuestion($qb_id,$qid,$bid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', ($res)?'Question Enabled Succesfully':'Question Disabled Succesfully');
		redirect('interview/listQuestionsToQB/'.$qb_id);
	}
	/* Question Bank Module END*/
	
	/* Questions Module START */
	function addQuestion($id=''){
		if(!$this->feature_access(15))redirect('Employee/access_denied');
		$id = ($id != '') ? $id : (isset($_POST['id']) ? $_POST['id'] : '');
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('question', 'Question', 'required|min_length[4]|max_length[50]|alpha_numeric');
			if(!$this->form_validation->run() == FALSE){	
				$res=$this->interviewmodel->addQuestion($id);
				redirect('interview/listQuestions');
			}	
		}
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$this->sysconfmodel->data['html']['title'] .= " | Add Question";
		$data['module']['title'] = "Questions";
		$fieldset = $this->configmodel->getFields('43',$bid,'1');
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('43',$id,$bid);
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['43']['opt_add']) redirect('Employee/access_denied');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] ){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=> ($field['fieldname'] == 'question_audio') ?   
									(form_input(array('name' => $field['fieldname'],'value' => '','id' => $field['fieldname'],'type' => 'file')).
									(isset($itemDetail[$field['fieldname']]) ? 
										(($itemDetail[$field['fieldname']] != '' && file_exists('sounds/'.$itemDetail[$field['fieldname']])) ? '<a target="_blank" href="'.site_url('sounds/'.$itemDetail[$field['fieldname']]).'"><span title="Sound" class="fa fa-volume-up"></span></a> '
										: '') 
										: ''))
											  :(($field['fieldname'] == 'rel_id') ? form_dropdown('pqid',$this->interviewmodel->queslist(),isset($itemDetail[$field['fieldname']]) ? $itemDetail[$field['fieldname']] : ''," class='auto'")
											  : form_input(array(
												'name'      => $field['fieldname'],
												'id'        => $field['fieldname'],
												'value'     => isset($itemDetail[$field['fieldname']]) ? $itemDetail[$field['fieldname']] : ''
											    ))));
							 }				
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$advsearch['custom['.$field['fieldid'].']']=$field['customlabel'];
					$cf = array('label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,'','search')
						);
					array_push($formFields,$cf);
				}
			}
		}
		$data['form'] = array(
		                    'form_attr'=>array('action'=>'interview/addQuestion/','name'=>'quesadd'),
							//~ 'open'=>form_open_multipart('interview/addQuestion/',array('name'=>'quesadd','class'=>'form','id'=>'quesadd','method'=>'post'),array("id"=>$id)),
							'fields'=>$formFields,
							'parentids'=>$parentbids,
							'busid'=>$bid,
							'pid'=>$this->session->userdata('pid'),
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function listQuestions($type=''){
		if(!$this->feature_access(15))redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['43']['opt_view']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '30';
		$data['itemlist'] = $this->interviewmodel->listQuestions($bid,$ofset,$limit,$type);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('interview/listQuestions/'.$type.'/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
						,'uri_segment'=>4				
				));
		$data['paging'] = $this->pagination->create_links();
		$data['module']['title'] = "Questions [".$data['itemlist']['count']."]";
		$this->sysconfmodel->data['html']['title'] .= " | Questions";
		$this->sysconfmodel->data['links'] = '<a href="interview/addQuestion"><span title="Add Question" class="glyphicon glyphicon-plus-sign"></span></a>';
		$fieldset = $this->configmodel->getFields('43',$bid);
		$formFields = array();
		$advsearch=array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !in_array($field['fieldname'],
						array('question_audio'))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){ $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>form_input(array(
													'name'      => $field['fieldname'],
													'id'        => $field['fieldname'],
													'value'     => $this->session->userdata($field['fieldname'])))
											);
										 }
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$cf = array('label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,'','search')
						);
					array_push($formFields,$cf);
				}
			}
		}
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$data['form'] = array(
			'open'=>form_open_multipart('interview/listQuestions/',array('name'=>'listques','class'=>'form','id'=>'listques','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>$advsearch,
			'parentids'=>$parentbids,
			'busid'=>$bid,
			'pid'=>$this->session->userdata('pid'),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function delQuestion($id){
		if(!$this->feature_access(15))redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['43']['opt_delete']) redirect('Employee/access_denied');
		$this->interviewmodel->delQuestion($id,$bid,'1');
		return 1;
	}
	function undelQuestion($qbid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->interviewmodel->delQuestion($qbid,$bid,'2');
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg',"Record restored Successfully");
		redirect('interview/listQuestions/del');
	}
	function activeQuestion($id=''){
		if(!$this->feature_access(1))redirect('Employee/access_denied');
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$this->sysconfmodel->data['html']['title'] .= " | Question Bank";
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data['module']['title'] = "Question";
		$fieldset = $this->configmodel->getFields('43',$bid);
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('43',$id,'',$bid);
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['43']['opt_add']) redirect('Employee/access_denied');
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],
						array('question_audio'))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){ 
						$v = '';
						if($field['fieldname'] == 'rel_id')
							$v = $itemDetail['parent'];
						else
							$v = isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:"";
						$cf = array('label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).' : </label>',
									'field'=>$v
							);
						array_push($formFields,$cf);
				}
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$cf = array('label'=>'<label>'.$field['customlabel'].' : </label>',
								'field'=> isset($itemDetail['custom['.$field['fieldid'].']'])?
											$itemDetail['custom['.$field['fieldid'].']']:''
						);
					array_push($formFields,$cf);
				}
			}
		}
		$data['form'] = array(
					'open'=>form_open('interview/addQuestion/'.$id,array('name'=>'addques','class'=>'form','id'=>'addques','method'=>'post')),
					'fields'=>$formFields,
					'close'=>form_close()
				);
				$this->load->view('active_view',$data);
	}
	/* Questions Module END */
}
/* end */

?>
