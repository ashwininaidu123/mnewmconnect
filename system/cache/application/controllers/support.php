<?php
class Support extends controller
{
	var $data,$roleDetail;
	function Support(){
		parent::controller();
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('systemmodel');
		$this->load->model('reportmodel');
		$this->load->model('supportmodel');
		$this->load->model('auditlog');
		$this->load->model('msgmodel');
		$this->roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
	}
	public function __destruct() {
		$this->db->close();
	}
	function feature_access(){
		$show=0;
		$checklist=$this->systemmodel->checked_featuremanage();
		if(in_array(14,$checklist)){
			$show=1;
		}
		return $show;
	}
	function addSupportGrp($id=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['38']['opt_view']) redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('groupname', 'Group Name', 'required');
			$this->form_validation->set_rules('grule', 'Group Rule', 'required');
			$this->form_validation->set_rules('eid', 'Group Owner', 'required');
			if (!$this->form_validation->run() == FALSE){
				$res = $this->supportmodel->addSupportGrp();
				if(is_numeric($res)){
					$this->session->set_flashdata('msgt', 'success');
					if($id == ''){
						$this->session->set_flashdata('msg', "Support Group added Successfully");
						redirect('AddempSupGroup/'.$res);
					}else{
						$this->session->set_flashdata('msg', "Support Group Updated Successfully");
						redirect('ListSupGroup/0');
					}
				}elseif($res == '2'){
					$this->session->set_flashdata('msgt', 'error');
					$this->session->set_flashdata('msg', "Limit Reached, Can not create more groups. Please contact your Account Manager.");
					redirect('ListSupGroup/0');
				}else{
					$this->session->set_flashdata('msgt', 'error');
					$this->session->set_flashdata('msg', "Support group already existed");
					redirect('AddSupGroup');
				}
			}
		}
		$this->sysconfmodel->data['html']['title'] .= " | Add Support Groups";
		$data['module']['title'] = "Add Support Groups";
		$fieldset = $this->configmodel->getFields('38',$bid);
		$itemDetail = $this->configmodel->getDetail('38',$id,'',$bid);
		$group_rule = array(""=>"Select","1"=>"Sequential","2"=>"Weighted");
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='filename'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked && !in_array($field['fieldname'] ,array('lastmodified','status','createdon','enteredby'))) 
					$formFields[] = array(
									'label'=>'<label  class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' <img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>',
												'field'=>($field['fieldname'] == 'eid') ? form_dropdown('eid',$this->supportmodel->getEmployees(),isset($itemDetail['eid']) ? $itemDetail['eid']:'','id="eid" class="form-control required" ')
											  :(($field['fieldname'] == 'group_rule') ?form_dropdown('grule',$group_rule,isset($itemDetail['group_rule']) ? $itemDetail['group_rule'] : '','id="grule" class="form-control required"')
											  :form_input(array(
												'name'      => $field['fieldname'],
												'id'        => $field['fieldname'],
												'value'		=> (isset($itemDetail[$field['fieldname']])) ? $itemDetail[$field['fieldname']] : '',
												'class'		=> ($field['fieldname'] == 'groupname') ? 'form-control required' : 'form-control')))
												);
			}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)$formFields[] = array(
							'label'=>'<label  class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
							'field'=>$this->configmodel->createFieldAdvance($field,isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '',''));
			}
		}
		$data['form'] = array(
		            'form_attr'=>array('action'=>'support/addSupportGrp','name'=>'addSupportGrp','id'=>'addSupportGrp','enctype'=>"multipart/form-data"),
					'hidden'=>array('bid'=>$bid,'id'=>$id),
					'fields'=>$formFields,'parentids'=>$parentbids,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function listSupportGrp($type=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;	
		if(!$roleDetail['modules']['38']['opt_view']) redirect('Employee/access_denied');
		if($this->input->post('submit')){
			if($this->session->userdata('search')!=""){
				$s=$this->session->unset_userdata('search');
			}
		}
		$type = ($this->uri->segment(1) == 'ListSupGroup') ? 'act' : (($this->uri->segment(1) == 'DeleteSupGroup') ? 'del' :'');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$ofset = ($this->uri->segment(2)!=null)?$this->uri->segment(2):0;
		$limit = '30';
		$data['itemlist'] = $this->supportmodel->listSupportGrp($bid,$ofset,$limit,$type);
		$this->pagination->initialize(array(
						 'base_url'=>site_url($this->uri->segment(1).'/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
						,'uri_segment'=>2				
				));
		$data['module']['title'] = "Support Groups [".$data['itemlist']['count']."]";	
		$links = array();	
		$links[] = '<li><a href="AddSupGroup"><span title="Add Support Group" class="glyphicon glyphicon-plus-sign">&nbsp;Add</span></a></li>';
	    $links[] = ($roleDetail['modules']['38']['opt_delete']) ? '<li><a href="support/bulkDelSupGrp" class="blkDel"><span title="Bulk Delete" class="glyphicon glyphicon-trash">&nbsp;Delete</span></a></li>':'';
	    $links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$fieldset = $this->configmodel->getFields('37',$bid);
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],array('enteredby','group_rule'))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { $formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>
										($field['fieldname']=='eid')
											?form_dropdown('eid',$this->supportmodel->getEmployees(),'',"class='form-control'")
											:form_input(array(
												'name'      => $field['fieldname'],
												'id'        => $field['fieldname'],
												'class'		=>($field['fieldname']=="createdon" || $field['fieldname']=="lastmodified")?'datepicker_leads form-control':'form-control'
												))
									);
								}		
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { $formFields[] = array(
								'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>form_input(array(
													'name'      => 'custom['.$field['fieldid'].']',
													'class'      => 'form-control'
													)));
								//$advsearch['custom['.$field['fieldid'].']']=$field['customlabel'];							
							}						
			}
		}
		$data['links'] = $links;
		$data['form'] = array(
					'open'=>form_open_multipart(site_url('ListSupGroup/0'),array('name'=>'listsupgrp','class'=>'form','id'=>'listsupgrp','method'=>'post')),
					'form_field'=>$formFields,
					'parentids'=>$parentbids,
					'adv_search'=>array(),
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close(),
					'title'=>$this->lang->line('level_search')
					);
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | Support Module ";
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function delSupportGrp($id='',$type1=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['38']['opt_delete']) redirect('Employee/access_denied');
		$type = ($type1 == '') ?'2' : $type1;
		$this->supportmodel->delSupportGrp($id,$bid,$type);
		if($type1 == '')
			redirect('ListSupGroup/0');
		else
			redirect('DeleteSupGroup/0');
	}
	function addGrpEmp($sgid=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if($this->input->post('update_system')){
			$res=$this->supportmodel->addGrpEmp($sgid);		
			if($res==0){
					$this->session->set_flashdata('msgt', 'error');
					$this->session->set_flashdata('msg', $this->lang->line('error_alreadyexists'));
					redirect('AddempSupGroup/'.$sgid);
			}elseif($res == '2'){
					$this->session->set_flashdata('msgt', 'error');
					$this->session->set_flashdata('msg', "Limit Reached, Can not assign more employees. Please contact your Account Manager.");
					redirect('ListempSupGroup/'.$sgid);
			}else{
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', $this->lang->line('error_addempsucss'));
					redirect('ListempSupGroup/'.$sgid);
			}
		}
		$roleDetail = $this->roleDetail;
		$gpDetail = $this->configmodel->getDetail('38',$sgid,'',$bid);
		if(!$roleDetail['modules']['38']['opt_add']) redirect('Employee/access_denied');
		if(count($_POST)>0){
			redirect($_SERVER['HTTP_REFERER']);
		}
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_addemptogroup');
		$data['module']['title'] = $this->lang->line('label_addemptogroup');
		$formFields = array();
		$data['form'] = array('form_attr'=>array('action'=>'support/addGrpEmp/'.$sgid,'name'=>'addGrpEmp'),
								'fields'=>$formFields,
								'close'=>form_close(),
								'gdetail'=>$gpDetail,
								'emplist'=>$this->systemmodel->get_emp_list(),
								'empexists'=>$this->supportmodel->supportGrpEmpExist($sgid)
							);
		$this->sysconfmodel->viewLayout('form_view_leadgrpemp',$data);
	}
	function listGrpEmp($sgid){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid = $this->session->userdata('cbid');
		$bid = (isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data['module']['title'] = "Support Group Employee List";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$gpDetail = $this->configmodel->getDetail('38',$sgid,'',$bid);
		$limit = '30';
		$header = array($this->lang->line('level_empname')
						,$this->lang->line('level_group'));
		if($gpDetail['group_rule'] == 2)
			array_push($header,"Employee Weight");
		array_push($header,"Ticket Counter");
		array_push($header,$this->lang->line('level_Action'));
		$data['itemlist']['header'] = $header;
		$emp_list = $this->supportmodel->listGrpEmp($sgid,$ofset,$limit);
		$roleDetail = $this->roleDetail;
		$opt_add = $roleDetail['modules']['38']['opt_add']; 
		$opt_delete = $roleDetail['modules']['38']['opt_delete']; 
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$opt='';
			$s=($item['status']=="1")?'<a href="'.site_url('support/disGrpEmp/'.$item['eid']."/".$item['gid']).'"><span class="fa fa-unlock" title="Disable"></span></a>':'<a href="'.site_url('support/disGrpEmp/'.$item['eid']."/".$item['gid']).'"> <span class="fa fa-lock" title="Enable"></span></a>';
			$opt.=($opt_delete)?'<a href="'.site_url('support/delGrpEmp/'.$item['eid']."/".$item['gid']).'">
						<span  title="Delete Employee from Support Group" class="glyphicon glyphicon-trash"></span>
				  </a>':'';
			$opt.=($opt_add)?$s:'';	 
			$r = array(
				'<a href="Employee/activerecords/'.$item['eid'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.(($item['status']=="1") ? $item['empname'] : '<font color=red>'.$item['empname'].'</font>').'</a>'
				,(($item['status']=="1") ? $item['groupname'] : '<font color=red>'.$item['groupname'].'</font>'));
			if($gpDetail['group_rule'] == 2)
				array_push($r,(($item['status']=="1") ? $item['weight'] : '<font color=red>'.$item['weight'].'</font>'));
			array_push($r,$item['counter']);
			$r = array_merge($r,array($opt));
			$rec[] = $r;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('ListempSupGroup/'.$this->uri->segment(2))
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_groupemp');
		$links = array();
		$links[] = '<li><a href="AddempSupGroup/'.$sgid.'"><span title="Add Employee to Support Group" class="glyphicon glyphicon-plus-sign">Add Employee</span></a></li>';
		$links[] =	'<li><a href="support/refreshcounter/'.$sgid.'"> <span title="Reset Counter" class="fa fa-refresh">Reset Counter</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$formFields = array();
		$cf=array('label'=>'<label  class="col-sm-4 text-right" for="groupname">Employee Name : </label>',
				  'field'=>form_input(array(
										'name'        => 'empname',
										'class'       => 'form-control',
										'id'          => 'empname',
										'value'       => $this->session->userdata('empname'))));
		array_push($formFields,$cf);
		$data['links'] = $links;
		$data['form'] = array(
			'open'=>form_open_multipart('ListempSupGroup/'.$sgid,array('name'=>'listgrpemp','class'=>'form','id'=>'listgrpemp','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function delGrpEmp($eid,$sgid){
		$res=$this->supportmodel->delGrpEmp($eid,$sgid);
		redirect('ListempSupGroup/'.$sgid);
	}	
	function disGrpEmp($eid,$lgid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->supportmodel->disGrpEmp($eid,$lgid,$bid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', ($res)?'Support Group Employee Enabled Succesfully':'Support Group  Employee Disabled Succesfully');
		redirect('ListempSupGroup/'.$lgid);
	}
	function actSupportGrp($id=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_groupfrm');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('label_groupfrm');
		$fieldset = $this->configmodel->getFields('38',$bid);
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('38',$id,'',$bid);
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['38']['opt_add']) redirect('Employee/access_denied');
		$group_rule = array(""=>"Select","1"=>"Sequential","2"=>"Weighted");
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
						$v = '';
						if($field['fieldname']=='eid'){
							$v = '<a href="Employee/activerecords/'.$itemDetail['eid'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$itemDetail['empname'].'</a>';
						}elseif($field['fieldname']=='group_rule'){
							$v = $group_rule[$itemDetail['group_rule']];
						}else{
							$v = isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:"";
						}
						$cf = array('label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).' : </label>',
									'field'=>$v
							);
						array_push($formFields,$cf);
						
				}
			}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)$formFields[] = array(
							'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
							'field'=>isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '','');
			}
		}
		$data['form'] = array(
					'open'=>form_open('support/actSupportGrp/'.$id,array('name'=>'actsup','class'=>'form','id'=>'actsup','method'=>'post')),
					'fields'=>$formFields,
					'close'=>form_close()
				);
				$this->load->view('active_view',$data);
	}
	function addSupportTkt($type=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['40']['opt_view']) redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('name', 'Name', 'required');
			$this->form_validation->set_rules('number', 'Number', 'required|numeric|callback_check_uniqNumber');
			$this->form_validation->set_rules('email', 'email', 'email');
			if (!$this->form_validation->run() == FALSE){
				$res = $this->supportmodel->addSupportTkt($type);
				if($res == '1'){
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', "Support Ticket added Successfully");
					redirect('ListSupTkt/'.$type);
				}elseif($res == '2'){
					$this->session->set_flashdata('msgt', 'error');
					$this->session->set_flashdata('msg', "Limit Reached, Can not create more tickets. Please contact your Account Manager.");
					redirect('ListSupTkt/'.$type);
				}else{
					$this->session->set_flashdata('msgt', 'error');
					$this->session->set_flashdata('msg', "Support Ticket already existed");
					redirect('AddSupTkt/'.$type);
				}
			}
		}
		$roleDetail = $this->roleDetail;
		$this->sysconfmodel->data['html']['title'] .= " | Add Support Ticket";
		$data['module']['title'] = "Add Support Ticket";
		$fieldset = $this->configmodel->getFields('40',$bid);
		$notinarray = array('lastmodified','status','enteredby','ticket_id');
		$escProcess = $this->systemmodel->getSupEscBusiness();
		if($escProcess != 1)
			array_push($notinarray,'tkt_esc_time');
		array_push($notinarray,'tkt_status');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='filename'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked && !in_array($field['fieldname'] ,$notinarray)) 
					$formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' <img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>',
									'field'=>($field['fieldname']=='gid')
											?form_dropdown('gid',$this->supportmodel->getSupportGrps(),'','id="supgrId" class="form-control" required')
											:(($field['fieldname']=='assignto')
												?form_dropdown('assignto',$this->supportmodel->getEmployees(),'',"id='supEmpid' class='form-control' required")
												:(($field['fieldname']=='tkt_status') ? form_dropdown('tkt_status',$this->supportmodel->getSupStatus($bid),'',"class='form-control'")
												:(($field['fieldname']=='tkt_criticality') ? form_dropdown('tkt_criticality',$this->supportmodel->getSupTktCritic(),'',"class='form-control'")
												:(($field['fieldname']=='tkt_level') ? form_dropdown('tkt_level',$this->supportmodel->getSupTktLevel(),'',"id='tkt_level' class='form-control'")
												:(($field['fieldname']=='auto_followup') ? (form_radio(array('name'=> $field['fieldname'], 'id'=> $field['fieldname'], 'value'=> '1' ))." ON &nbsp;&nbsp;&nbsp;".form_radio(array('name'=> $field['fieldname'], 'id'=> $field['fieldname'], 'value'=> '0' ))."&nbsp;OFF")
												:(($field['fieldname']=='tkt_esc_time') ? form_input(array('name'      => $field['fieldname'],
																  'id'        => $field['fieldname'],
																  'value'	  => '',
																  'class'	  => 'form-control ' ))
												:form_input(array('name'      => $field['fieldname'],
																  'id'        => $field['fieldname'],
																  'value'	  => ($field['fieldname'] == 'createdon') ? date('Y-m-d H:i:s') : '',
																  'class'	  => ($field['fieldname'] == 'createdon') ? 'datepicker_leads form-control' : (($field['fieldname'] == 'number' || $field['fieldname'] == 'email' || $field['fieldname'] == 'name') ? 'form-control required' : 'form-control'))))
												))))));
				}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)$formFields[] = array(
							'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
							'field'=>$this->configmodel->createFieldAdvance($field,isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '',''));
				}
		}
		$formFields[] = array('label'=>'',
							   'field'=>form_input(array(
											'name'      => 'autoAssign',
											'id'        => 'autoAssign',
											'class'     => 'form-control',
											'value'		=> 'single',
											'type'	    => 'hidden')
											)
								);		
		$data['form'] = array(
		            'form_attr'=>array('action'=>'AddSupTkt/'.$type,'name'=>'addSupportTkt','id'=>'addSupportTkt','enctype'=>"multipart/form-data"),
					'hidden'=>array('bid'=>$bid),
					'fields'=> $formFields,'parentids'=>$parentbids,
					'busid' => $bid,
					'pid'   => $this->session->userdata('pid'),
					'close' => form_close()
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function listSupportTkt($type=''){
		if(!isset($_POST['module']) || $_POST['module']!='support'){
			$this->session->unset_userdata('Adsearch');
		}
		$type = ($type != '') ? $type : (($this->uri->segment('3') != '') ? $this->uri->segment('3') : '1' );
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;	
		$dlink = "";
		
		if($this->input->post('download')){
		    $filename = $this->supportmodel->supportTktCSV($bid);
			$dlink =  "<a href='".$this->config->item('reports_path').$filename.".zip' target='_blank' style='color:#fff'><b>Download</b></a>  ";
		}elseif($this->input->post('blk_down')){
			$filename = $this->supportmodel->blk_down($bid);
			$dlink =  "<a href='".$this->config->item('reports_path').$filename.".zip"."' target='_blank' style='color:#fff'><b>Download</b></a>  ";
		}
		if(!$roleDetail['modules']['40']['opt_view']) redirect('Employee/access_denied');
		if($this->input->post('submit')){

			if($this->session->userdata('search')!=""){
				$s=$this->session->unset_userdata('search');
			}
		}
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$u3 = ($this->uri->segment(4)!='')?$this->uri->segment(4):'all';
		$ofset = ($this->uri->segment(5)!=null)?$this->uri->segment(5):0;
		$limit = '30';
		$data['itemlist'] = $this->supportmodel->listSupportTkt($type,$ofset,$limit,$u3);

		$this->pagination->initialize(array(
						 'base_url'=>site_url('support/listSupportTkt/'.$type.'/'.$u3.'/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
						,'uri_segment'=>5				
				));
		$data['module']['title'] = "Support Tickets [".$data['itemlist']['count']."]";
		$links = array();
		$links[] = '<li><a href="support/addSupportTkt/'.$type.'"><span title="Add Ticket" class="glyphicon glyphicon-plus-sign">&nbsp;Add Ticket</span></a></li>';
		$links[] = ($roleDetail['modules']['40']['opt_delete']) ? '<li><a href="support/bulkDelSupTkt" class="blkDel"><span title="Bulk Delete" class="glyphicon glyphicon-trash">&nbsp;Delete</span></a></li>':'';
		$links[] = ($roleDetail['modules']['40']['opt_add']) ? '<li><a href="support/bulkAssign" class="blkAssign"  data-toggle="modal" data-target="#modal-blkAssign"><span title="Bulk Assign" class="glyphicon glyphicon-share">&nbsp;Assign</span></a></li>':'';
		$links[] = '<li><a href="#" class="blkemail" rel="calls"><span title="Bulk Mail" class="glyphicon glyphicon-envelope">&nbsp;Email</span></a></li>';
		$links[] = '<li><a href="Report/blksms" class="blkSMs" data-toggle="modal" data-target="#modal-blksms" rel="support"><span title="Bulk SMS" class="glyphicon glyphicon-comment">&nbsp;SMS</span></a></li>';
		$links[] = '<li  class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-advsearch" data-toggle="modal" data-target="#modal-advsearch" ><span title="Search" class="glyphicon glyphicon-zoom-in">&nbsp;Advance Search</span></a></li>';
		$links[] = '<li  class="divider"><a>&nbsp;</a></li>';
		$links[] = ($roleDetail['modules']['40']['opt_download']!=0)? '<li><a href="support/Bulk_down/" class="blk_calls" data-toggle="modal" data-target="#modal-pop"><span title="Download" class="glyphicon glyphicon-arrow-down">Download Select</span></a></li>':'';
		$links[] = ($roleDetail['modules']['40']['opt_download']) ? '<li><a href="support/supportTktCSV/" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Download" class="glyphicon glyphicon-download-alt">Download All</span></a></li>':'';
	
		$fieldset = $this->configmodel->getFields('40',$bid);
		$formFields = array();
		$notinarray = array();
		$escProcess = $this->systemmodel->getSupEscBusiness();
		if($escProcess != 1)
			array_push($notinarray,'tkt_esc_time');
		$advsearch=array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked && !in_array($field['fieldname'] ,$notinarray)) {
					$formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>
										($field['fieldname']=='gid')
											?form_dropdown('gid',$this->supportmodel->getSupportGrps(),'',"class='form-control'")
											:(($field['fieldname']=='assignto')
												?form_dropdown('assignto',$this->supportmodel->getEmployees(),'',"class='form-control'")
												:(($field['fieldname']=='tkt_status') ? form_dropdown('tkt_status',$this->supportmodel->getSupStatus($bid),'',"class='form-control'")
												:(($field['fieldname']=='tkt_criticality') ? form_dropdown('tkt_criticality',$this->supportmodel->getSupTktCritic(),'',"class='form-control'")
												:(($field['fieldname']=='tkt_level') ? form_dropdown('tkt_level',$this->supportmodel->getSupTktLevel(),'',"class='form-control'")
												:(($field['fieldname']=='tkt_esc_time') ? form_input(array('name'      => $field['fieldname'],
																  'id'        => $field['fieldname'],
																  'value'	  => '',
																  'class'	  => 'form-control' ))
												:(($field['fieldname']=='createdon' || $field['fieldname']=='lastmodified')
													?form_input(array(
													'name'      => $field['fieldname']."_from",
													'id'        => $field['fieldname']."_from",
													'class'		=> 'datepicker_leads form-control'
													),'',"style='width:187px;'").' TO  '.
													form_input(array(
													'name'      => $field['fieldname']."_to",
													'id'        => $field['fieldname']."_to",
													'class'		=> 'datepicker_leads form-control'
													),'',"style='width:187px;'")
													:form_input(array(
													'name'      => $field['fieldname'],
													'id'        => $field['fieldname'],
													'class'		=> 'form-control'
													))
												))))))
										);
										if(!in_array($field['fieldname'],array('assignto','gid','tkt_status','tkt_criticality'))){
											$advsearch[$field['fieldname']]=(($field['customlabel']!="")
												 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']);
										}
								}		
			}elseif($field['type']=='c' && $field['show'] ){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
						$formFields[] = array(
							'label'=>'<label class=" col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
							'field'=>$this->configmodel->createFieldAdvance($field,'','search',"class='form-control'"));
						$advsearch['custom['.$field['fieldid'].']']=$field['customlabel'];
					}
					
			}
		}
		$save_cnt=save_search_count($bid,'40',$this->session->userdata('eid'));
		$search_names=get_save_searchnames($bid,'40',$this->session->userdata('eid'));
		$data['links'] = $links;
		$data['downlink'] = $dlink;
		$data['form'] = array(
					'open'=>form_open_multipart(site_url('support/listSupportTkt/'.$type.'/')
						,array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')
						,array('module'=>'support')),
					'form_field'=>$formFields,
					'adv_search'=>$advsearch,
					'save_search'=>$save_cnt,
					'search_names'=>$search_names,
					'groups'=>$this->supportmodel->getSupportGrps(),
					'employees'=>$this->supportmodel->getEmployees(),
					'search_url'=>'support/listSupportTkt/'.$type.'/',
					'parentids'=>$parentbids,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close(),
					'title'=>$this->lang->line('level_search')
					);
		$data['paging'] = $this->pagination->create_links();
		$data['tab'] = true;	
		$this->sysconfmodel->data['html']['title'] .= " | Support Module";
		if(isset($_POST['search'])){
			if($_POST['search'] == 'search'){
				$this->load->view('search_view',$data);
				return true;
			}
			if($_POST['search'] == 'advsearch'){
				$this->load->view('advsearch_view',$data);
				return true;
			}
		}
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function editSupportTkt($id){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['40']['opt_add']) redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			$res=$this->supportmodel->editSupportTkt($id,$bid);
			if($res == '1'){
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', "Record updated Successfully");
				redirect($_POST['httpRefer']);
			}elseif($res == '3'){
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', " Record updated Successfully. You dont have enough SMS credits.");
				redirect('EditSupTkt/'.$id);
			}elseif($res == '0'){
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', "Modified Contact number already existed");
				redirect('EditSupTkt/'.$id);
			}
		}
		$data['module']['title'] = "Support Module";
		$fieldset = $this->configmodel->getFields('40');
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('40',$id,'',$bid);
		$notinarray = array();
		$escProcess = $this->systemmodel->getSupEscBusiness();
		if($escProcess != 1)
			array_push($notinarray,'tkt_esc_time');
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] ){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				$gid = ($itemDetail['gid'] != 0) ? $itemDetail['gid'] : '' ;
					if($checked && !in_array($field['fieldname'] ,$notinarray)) {
						$cf = array('label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>',
									'field'=>($field['fieldname']=='gid')
											 ? form_dropdown('gid',$this->supportmodel->getSupportGrps(),$itemDetail['gid'],'id="supgrId" class="form-control"')
											 :(($field['fieldname']=='assignto')
												?form_dropdown('assignto',$this->supportmodel->allEmployees($gid,$itemDetail['assignto']),$itemDetail['assignto'],"id='supEmpid' class='form-control'")
												:(($field['fieldname']=='tkt_status') ? form_dropdown('tkt_status',$this->supportmodel->getSupStatus($bid),$itemDetail['tkt_status'],"class='form-control'")
												:(($field['fieldname']=='tkt_criticality') ? form_dropdown('tkt_criticality',$this->supportmodel->getSupTktCritic(),$itemDetail['tkt_criticality'],"class='form-control'")
												:(($field['fieldname']=='enteredby')
												  ?$itemDetail['enteredempname']
												  :(($field['fieldname']=='source')?(($itemDetail[$field['fieldname']] == 'Calltrack') ? $itemDetail[$field['fieldname']] 
																					:form_input(array('name'=> $field['fieldname'], 'id'=> $field['fieldname'],'class' => 'form-control','value'=> (isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'')))
												    ):(($field['fieldname']!="name" && $field['fieldname']!="email" && $field['fieldname']!="number" && $field['fieldname']!="caller_add" && $field['fieldname']!="caller_bus" && $field['fieldname']!="refId" && $field['fieldname']!="tkt_level" && $field['fieldname']!="tkt_esc_time"&& $field['fieldname']!="auto_followup" ) 
												     ? $itemDetail[$field['fieldname']]
												     :(($field['fieldname']=='tkt_level') ? form_dropdown('tkt_level',$this->supportmodel->getSupTktLevel(),$itemDetail['tkt_level'],"id='tkt_level' class='form-control'")
												     :(($field['fieldname']=='tkt_esc_time') ? form_input(array('name'=> $field['fieldname'],
																  'id'        => $field['fieldname'],
																  'value'	  => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'',
																  'class'	  => 'form-control' ))
												     :(($field['fieldname']=='auto_followup') ? (form_checkbox(array(
												     'name'=> $field['fieldname'], 
												     'id'=> $field['fieldname'], 
												     'value'=> '1' ,
												     'checked'=> (isset($itemDetail['auto_followup']) && $itemDetail['auto_followup'] == 1) ? 'TRUE' : ''
												     )))
												     
														:form_input(array(
																	  'name'      => $field['fieldname'],
																	  'id'        => $field['fieldname'],
																	  'class'     => 'form-control',
																	  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:''
															))
												     ))
												     )
												     )
												)
											 ))))
											);
						array_push($formFields,$cf);
				}
				}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)$formFields[] = array(
							'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
							'field'=>$this->configmodel->createFieldAdvance($field,isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '',''));
			}
		}
		$latestRemark = $this->supportmodel->getRemarkById($id,$bid);
		$cf = array('label'=>'<label class="col-sm-4 text-right">Remarks : </label>',
					'field'=>form_textarea(array(
											'name'      => 'remark',
											'id'        => 'remark',
											'class'     => 'form-control',
											'value'		=> '',
											'placeholder'=> $latestRemark
											))					);
		array_push($formFields,$cf);
		$formFields[] = array('label'=>'',
							   'field'=>form_input(array(
											'name'      => 'autoAssign',
											'id'        => 'autoAssign',
											'class'     => '',
											'value'		=> 'singleLead',
											'type'	  	=> 'hidden')
											)
								);
		$array=array("0"=>"Select","1"=>"Email Alert","2"=>"SMS Alert","3"=>"Both");
		$formFields[] =array("label"=>"<label class='col-sm-4 text-right' for='alert'>Alert :</label>"
								,"field"=>form_dropdown("alert_type",$array,'',"class= 'form-control'")
							);
		$latestComment = $this->supportmodel->getCommentById($id,$bid);
		$cf = array('label'=>'<label class="col-sm-4 text-right">Comments : </label>',
					'field'=>form_textarea(array(
											'name'      => 'comments',
											'id'        => 'comments',
											'class'     => 'form-control',
											'value'		=> '',
											'placeholder'=> $latestComment
											))					);
		array_push($formFields,$cf);
		$cf = array('label'=>'<label class="col-sm-4 text-right">Remarks History : </label>',
					'field'=>anchor("support/remarksHistory/".$id, ' <img src="system/application/img/icons/remarks.png" title="Support Ticket Remarks" width="16" height="16">','class="btn-followup" data-toggle="modal" data-target="#modal-followup"')
					);
		array_push($formFields,$cf);
		$cf = array('label'=>'<label class="col-sm-4 text-right">Comments History : </label>',
					'field'=>anchor("support/commentsHistory/".$id, '<span title="Support Ticket Comments" class="glyphicon glyphicon-comment"></span>','class="btn-followup" data-toggle="modal" data-target="#modal-followup"')
					);
		array_push($formFields,$cf);
		$cf = array('label'=>'<label class="col-sm-4 text-right">Click To Connect : </label>',
					'field'=>anchor("Report/clicktoconnect/".$id."/4", '<span title="click To Connect" class="fa fa-phone"></span>','class="clickToConnect"')
					);
		array_push($formFields,$cf);
		$cf = array('label'=>'<label class="col-sm-4 text-right">Followup : </label>',
					'field'=>anchor("Report/followup/".$id."/0/support", ' <img src="system/application/img/icons/comments.png" title="Followups" width="16" height="16">','class="btn-followup" data-toggle="modal" data-target="#modal-followup"')
					);
		array_push($formFields,$cf);
		$refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		$data['form'] = array(
		            'form_attr'=>array('action'=>'EditSupTkt/'.$id,'name'=>'edittkt','id'=>'edittkt','enctype'=>"multipart/form-data"),
					'fields'=>$formFields,
					'hidden'=>array("httpRefer"=>$refer),
					'close'=>form_close()
				);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function delSupportTkt($id){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['40']['opt_delete']) redirect('Employee/access_denied');
		$this->supportmodel->delSupportTkt($id,$bid);
		return 1;
	}
	function activeSupportTkt($id=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['40']['opt_add']) redirect('Employee/access_denied');
		$data['module']['title'] = "Support Tickets";
		$fieldset = $this->configmodel->getFields('40',$bid);
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('40',$id,'',$bid);
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
						$cf = array('label'=>'<label  class="col-sm-4 text-right">'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).' : </label>',
									'field'=>($field['fieldname']=='gid')
											?$itemDetail['groupname']
											:(($field['fieldname']=='assignto')
												?$itemDetail['assignempname']
												:(($field['fieldname']=='enteredby')
												?$itemDetail['enteredempname']
												:$itemDetail[$field['fieldname']]))									
							);
						array_push($formFields,$cf);
				}
						}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)$formFields[] = array(
							'label'=>'<label  class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
							'field'=>isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '','');
			}
		}
		 $data['form'] = array(
		            'form_attr'=>array('action'=>'support/editSupportTkt/'.$id,'name'=>'tktedit','id'=>'tktedit'),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$fdata['module']['title'] = "Followups";
		$fdata['links'] = '';
		$fdata['nosearch']=true;
		$fdata['paging'] = '';
		$fdata['itemlist'] = $this->reportmodel->getFollowuplist($id,$bid,'support');
		$fdata['form'] =array('adv_search'=>array());
		if(!empty($fdata['itemlist']['rec'])){
			$data['followups'] = $fdata;
		}
		$cdata['module']['title'] = "Comments";
		$cdata['links'] = '';
		$cdata['nosearch']=true;
		$cdata['paging'] = '';
		$cdata['itemlist'] = $this->supportmodel->getComments($id,$bid);
		$cdata['form'] =array('adv_search'=>array());
		if(!empty($cdata['itemlist']['rec'])){
			$data['comments'] = $cdata;
		}
		$this->load->view('active_view',$data);
	}
	function delSupTktList(){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$heading="Deleted Support Tickets";
		if(!$roleDetail['modules']['40']['opt_view']) redirect('Employee/access_denied');
		if($this->input->post('submit')){	
			if($this->session->userdata('search')!=""){
				$s=$this->session->unset_userdata('search');
			}
		}
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '20';
		$data['itemlist'] = $this->supportmodel->delSupTktList($bid,$ofset,$limit,$url='');
		$this->pagination->initialize(array(
						 'base_url'=>site_url('support/delSupTktList')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
						,'uri_segment'=>3				
				));
		$links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$data['module']['title'] = "Support Tickets";
		$fieldset = $this->configmodel->getFields('40',$bid);
		$formFields = array();
		$advsearch=array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],array('enteredby'))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { $formFields[] = array(
									'label'=>'<label  class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>
										($field['fieldname']=='gid')
											?form_dropdown('gid',$this->supportmodel->getSupportGrps(),'',"class='form-control'")
											:(($field['fieldname']=='assignto')
												?form_dropdown('assignto',$this->supportmodel->getEmployees(),'',"class='form-control'")
												:(($field['fieldname']=='tkt_status') ? form_dropdown('tkt_status',$this->supportmodel->getSupStatus($bid),'',"class='form-control'")
												:(($field['fieldname']=='tkt_criticality') ? form_dropdown('tkt_criticality',$this->supportmodel->getSupTktCritic(),'',"class='form-control'")
												:form_input(array(
													'name'      => $field['fieldname'],
													'id'        => $field['fieldname'],
													'class'		=>($field['fieldname']=="createdon" || $field['fieldname']=="lastmodified")?'datepicker_leads form-control':'form-control'
													))
												)))
										);
										$advsearch[$field['fieldname']]=(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']);
								}			
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { $formFields[] = array(
								'label'=>'<label  class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>form_input(array(
													'name'      => 'custom['.$field['fieldid'].']',
													'class'     => 'form-control',
													)));
													$advsearch['custom['.$field['fieldid'].']']=$field['customlabel'];	
												}
			}
		}
		$save_cnt=save_search_count($bid,'40',$this->session->userdata('eid'));	
		$data['links'] = $links;
		$data['form'] = array(
					'open'=>form_open_multipart(site_url('support/delSupTktList'),array('name'=>'deltkt','class'=>'form','id'=>'deltkt','method'=>'post')),
					'form_field'=>$formFields,
					'adv_search'=>array(),
					'save_search'=>$save_cnt,
					'parentids'=>$parentbids,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close(),
					'title'=>$this->lang->line('level_search')
					);
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | Support Module ";
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function supportTktCSV($eid=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data=array('systemfields'=>$this->configmodel->getFields('40',$bid),
					'roleDetail'=>$this->roleDetail,
					 'eid'=>($eid!="")?$eid:'',
					 'bid' => $bid,
					 'attributes' => array('class' => 'form', 'id' =>'landingnumber','name'=>'landingnumber'),
					 'URL' => 'support/listSupportTkt/'
					 );
		$this->load->view('supportTktCSV',$data);
	}
	function undelSupportTkt($tktid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->supportmodel->undelSupportTkt($tktid,$bid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg',"Deleted Record restored Successfully");
		redirect('ListSupTkt/0');
	}
	/********* Import Section ********/
	function import($steps=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['40']['opt_add']) redirect('Employee/access_denied');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		if($steps == '' || $steps =='step1'){	
			$data['module']['title'] = "Import - Step1";
			$enclosure = array(" "=>"Select","&quot;"=>"Double Quote (&quot;)","'"=>"Single Quote(')");
			$formFields[] = array('label'=>'<label for="group">Group&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="Select the group from list">&nbsp;&nbsp;: </label>',
							   'field'=>form_dropdown('gid',$this->supportmodel->getSupportGrps(),'','id="supgrId"')
								);
			$formFields[] = array('label'=>'<label for="assignto">Assignto&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="Select the group from list">&nbsp;&nbsp;: </label>',
							   'field'=>form_dropdown('assignto',$this->supportmodel->getEmployees(),'',"id='supEmpid'")
								);
			$formFields[] = array('label'=>'<label for="enclosure">Data Enclosure &nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="This is the enclosure of the records which present in import file">&nbsp;&nbsp;: </label>',
							   'field'=>form_dropdown('enclosure',$enclosure,'',"class='auto'")
								);
			$formFields[] = array('label'=>'<label for="filename">Upload File&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="It supports only .csv format">&nbsp;&nbsp;: </label>',
							   'field'=>form_input(array(
											'name'      => 'filename',
											'id'        => 'filename',
											'class'     => '',
											'type'	  => 'file')
											)
								);
			$formFields[] = array('label'=>'',
							   'field'=>form_input(array(
											'name'      => 'autoAssign',
											'id'        => 'autoAssign',
											'class'     => '',
											'value'		=> 'auto',
											'type'	    => 'hidden')
											)
								);
			$data['form'] = array(
			        'form_attr'=>array('action'=>'support/import/step2','name'=>'importtkt1'),
					'fields'=>$formFields,'parentids'=>$parentbids,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close()
				);					
			$this->sysconfmodel->viewLayout('form_view',$data);
		}elseif($steps =='step2'){
			$err = "FALSE";$msg = '';
			$ext = pathinfo($_FILES['filename']['name'], PATHINFO_EXTENSION);
			if(!(($_FILES['filename']['type'] == 'text/csv') || ($ext == 'csv'))){
				$msg .= "Please upload the .csv format file. ";
				$err = "TRUE";
			}
			if($this->input->post('gid') == ''){
				$msg .= ($msg != '' )? '<br/>':'';
				$msg .= "Please Select Group from list. ";
				$err = "TRUE";
			}
			if($err == "TRUE"){
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg',$msg);
				redirect('support/import/step1');
			}else{
				$ext = pathinfo($_FILES['filename']['name'],PATHINFO_EXTENSION);
				$newName = "I".date('YmdHis').".".$ext;
				@move_uploaded_file($_FILES['filename']['tmp_name'],$this->config->item('sound_path').$newName);
				$moved_file = $this->config->item('sound_path').$newName;
				@chmod($moved_file,0777);
				$row = 1;
				if (($handle = fopen($moved_file, "r")) !== FALSE) {
					while (($csvdata = fgetcsv($handle,1000, ',',$_POST['enclosure']) )!== FALSE) {
						$num = count($csvdata);
						if($row == 1)
							$headerData = $csvdata;
						$row++;
					}
					fclose($handle);
				}
				$result = $this->supportmodel->getSupTktSysfields($bid,'40');
				$custom_tkt = $this->supportmodel->getSupTktCustfields();
				$data['mapping_fileds'] = @array_merge($result,$custom_tkt);
				$data['mapping_fileds']['comments']= "Comments";
				$data['mapping_fileds']['remarks']= "Remarks";
				$data['mapping_fileds'][' ']= "Select Field";
				$data['module']['title'] = "Import - Step2";
				if($this->input->post('assignto')){
				$formFields[] = array('label'=>'',
								   'field'=>form_input(array(
												'name'      => 'assignto',
												'id'        => 'assignemp',
												'class'     => '',
												'value' 	=> $this->input->post('assignto'),
												'type'	  => 'hidden')
												)
									);
				}
				$formFields[] = array('label'=>'',
								   'field'=>form_input(array(
												'name'      => 'gid',
												'id'        => 'gid',
												'class'     => '',
												'value' 	=> $this->input->post('gid'),
												'type'	  => 'hidden')
												)
									);
				$formFields[] = array('label'=>'',
								   'field'=>form_input(array(
												'name'      => 'filename',
												'id'        => 'filename',
												'class'     => '',
												'value' 	=> $moved_file,
												'type'	  => 'hidden')
												)
									);
				$formFields[] = array('label'=>'',
								   'field'=>form_input(array(
												'name'      => 'enclosure',
												'id'        => 'enclosure',
												'class'     => '',
												'value' 	=> $_POST['enclosure'],
												'type'	  => 'hidden')
												)
									);
				foreach($headerData as $k=>$v){
					$formFields[] = array('label'=>'<label for="filename">'.$v.'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="Please Map the file header fields to Database table columns"</label>',
									  'field'=>form_dropdown('field['.$k.']',$data['mapping_fileds'],' ',"class='auto'")
									);	
				}
				
				$data['form'] = array(
			         	'form_attr'=>array('action'=>'support/import/step3','name'=>'importtkt2'),
						'fields'=>$formFields,'parentids'=>$parentbids,
						'busid'=>$bid,
						'pid'=>$this->session->userdata('pid'),
						'close'=>form_close()
					);					
				$this->sysconfmodel->viewLayout('form_view',$data);
			}
		}elseif($steps =='step3'){
			$post_array = $this->input->post('field');
			if(@in_array("number",$post_array)){
				for($im=0;$im<count($post_array);$im++){
					$stat[$im] = ($post_array[$im] == ' ' ) ? 0 : 1 ;
				}
				if(!(@in_array(1,$stat))){
					$this->session->set_flashdata('msgt', 'error');
					$this->session->set_flashdata('msg',"Please Map atleast one field ");
					redirect('support/import/step1');
				}
				$moved_file = $this->input->post('filename');
				$row = 1;
				$i = 0;
				$gid = $this->input->post('gid');
				$dis_type = 0;
				//~ if($this->input->post('assignto') == 'auto'){
					//~ $assign_emp = $this->supportmodel->empAssign($gid);
				//~ }elseif(isset($_POST['assignto'])){
					//~ $assign_emp[0] = $this->input->post('assignto');
				//~ }
				//~ if($this->input->post('duplicate'))
					//~ $data['duplicate'] = @implode(',',$this->input->post('duplicate'));
				$assign_header = 0;
				if (($handle = fopen($moved_file, "r")) !== FALSE) {
					while (($csvdata = fgetcsv($handle,10000, ',',$this->input->post('enclosure')) )!== FALSE) {
						$email_exists = '';
						$num = count($csvdata);
						if($row == 1){
							$assign_header = (@in_array('assignto',$csvdata) || @in_array('Assignto',$csvdata)) ? 1 : 0;
						}
						if($row != 1 && $csvdata[0]!=''){
							for($k=0;$k<count($post_array);$k++){
								if($post_array[$k] != 'select' ){
									if($assign_header && $post_array[$k] == 'assignto'){
										$assempid = 0;
										$email_exists = $this->supportmodel->empAssign_exists($csvdata[$k]);
										if($email_exists != NULL){
											 $assempid = $email_exists;
											 $data['gid'] = '';
										}else{
											 if(is_numeric($this->input->post('assignto'))){
												 $assempid = $this->input->post('assignto');
												 $dis_type = 1;
											 }elseif($this->input->post('assignto') =='auto'){
												$rule = $this->db->query("SELECT group_rule as rule FROM ".$bid."_support_groups WHERE gid='".$gid."'")->row()->rule;
												if($rule == '2'){
													$resultemp = $this->db->query("SELECT e.eid,COALESCE(((weight/(SELECT sum(weight) FROM ".$bid."_support_grpemp WHERE gid=ge.gid))-(counter/(SELECT sum(counter) FROM ".$bid."_support_grpemp WHERE gid=ge.gid))),0) as pc FROM ".$bid."_employee e LEFT JOIN ".$bid."_support_grpemp ge on e.eid=ge.eid AND ge.status = 1 AND e.status = 1 WHERE ge.gid='".$gid."' ORDER BY pc DESC LIMIT 0,1")->result_array();
													if(count($resultemp) > 0)
														$assempid = $resultemp[0]['eid'];
												}elseif($rule == '1'){
													$eid = $this->db->query("SELECT ge.eid FROM ".$bid."_support_grpemp ge LEFT JOIN ".$bid."_employee e ON ge.eid = e.eid WHERE ge.gid='".$gid."' AND ge.bid='".$bid."' AND ge.status = 1 AND e.status = 1 ORDER BY ge.counter ASC LIMIT 0,1")->row()->eid;
													if($eid != '')
														$assempid = $eid;
												}
												$dis_type = 2;
											 }
										}
										$data[$post_array[$k]] = $assempid;
									}else{
										$data[$post_array[$k]] = @(array_key_exists($k,$csvdata)) ? $csvdata[$k] : ' ';
									}
								}else{
									$data[$post_array[$k]] = ' ';
								}
							}
							if($this->input->post('assignto') && !array_key_exists('assignto',$data)) {
								$assempid = 0;
								 if(is_numeric($this->input->post('assignto'))){
									 $assempid = $this->input->post('assignto');
									 $dis_type = 1;
								 }
								 elseif($this->input->post('assignto') =='auto'){
									$rule = $this->db->query("SELECT group_rule as rule FROM ".$bid."_support_groups WHERE gid='".$gid."'")->row()->rule;
									if($rule == '2'){
										$resultemp = $this->db->query("SELECT e.eid,COALESCE(((weight/(SELECT sum(weight) FROM ".$bid."_leads_grpemp WHERE gid=ge.gid))-(counter/(SELECT sum(counter) FROM ".$bid."_support_grpemp WHERE gid=ge.gid))),0) as pc FROM ".$bid."_employee e LEFT JOIN ".$bid."_support_grpemp ge on e.eid=ge.eid WHERE ge.gid='".$gid."' AND ge.status = 1 AND e.status = 1 ORDER BY pc DESC LIMIT 0,1")->result_array();
										if(count($resultemp) > 0)
											$assempid = $resultemp[0]['eid'];
									}elseif($rule == '1'){
										$eid = $this->db->query("SELECT ge.eid FROM ".$bid."_support_grpemp ge LEFT JOIN ".$bid."_employee e ON ge.eid = e.eid WHERE ge.gid='".$this->input->post('gid')."' AND ge.bid='".$bid."' AND ge.status = 1 AND e.status =1  ORDER BY counter ASC LIMIT 0,1")->result_array();
										if(count($eid) > 0){
											$assempid = $eid[0]['eid'];
										}
									}
									$dis_type = 1;
								 }
								 $data['assignto'] = $assempid;
							}
							@array_key_exists('gid',$data) ? '' : ($data['gid'] = $gid);
							$data['bid'] = $bid;
							$data['enteredby'] = $this->session->userdata('eid');
							$data['createdon'] = date("Y-m-d H:i:s");
							$data['dis_type'] = $dis_type;
							$insertsuptkt = $this->supportmodel->addImportSupTkt($data);
							unset($data['assignto']);
							unset($data['gid']);
						}
						$row++;$i++;
					}
					fclose($handle);
				}	
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg',"Imported Support Tickets Successfully By ".$this->session->userdata('username'));
				redirect('support/listSupportTkt/');
			}else{
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg'," Please Map the Number Field in List ");
				redirect('support/listSupportTkt/');
			}
		}
	}
	/********************  Import END  ***********************************/
	function bulkDelSupTkt(){
		$res=$this->supportmodel->bulkDelSupTkt($_POST['leadids']);
		echo "1";
	}
	function bulkDelSupGrp(){
		$res=$this->supportmodel->bulkDelSupGrp($_POST['leadids']);
		echo "1";
	}
	function bulkAssign(){
		if(!$this->feature_access())redirect('Employee/access_denied');
		echo '<div class="modal-dialog modal-lg">
		         <div class="modal-content">
		         	<div class="modal-body">
							
					<button aria-hidden="true" data-dismiss="modal" class="close" type="button"><i class="fa fa-times"></i></button>
                      <h4>Support Assign to Employees</h4>
		<form action="support/assignEmp/" class="form" id="supassign" name="supassign" method="POST">
		<div class="form-group col-sm-12">
					<label class="col-sm-4 text-right">Group :</label>
						<!--<input type="text" name="groupname" id="groupname" />-->
						<input type="hidden" name="ids" id="ids" />
						 <div class="col-sm-6 input-icon right">  
							'.form_dropdown('gid',$this->supportmodel->getSupportGrps(),'','id="supgrId" class="form-control"').'
						</div>
				</div>
					<div class="form-group col-sm-12">
					<label class="col-sm-4 text-right">Employee :</label>
						<!--<input type="text" name="empname" id="empname" />-->
						 <div class="col-sm-6 input-icon right">  
							'.form_dropdown('assignto',$this->supportmodel->getEmployees(),'',"id='supEmpid' class='form-control'").'
				    	</div>
				</div>
				<div class="form-group text-center">
					<input id="button1" type="submit" class="btn btn-primary" name="submit" value='.$this->lang->line('submit').' > 
                    <input id="button2" type="reset" class="btn btn-default" value='.$this->lang->line('reset').' />
                </div>
				</form>
	</div></div></div>
</div>';
	}
	function assignEmp(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		$result = $this->supportmodel->bulkAssignEmpTkt();
		if($result == 1){
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg',"Bulk Assign Support Tickets successfully done ");
			$this->auditlog->auditlog_info('Support', "Tickets Assign employee Details updated by ".$this->session->userdata('username'));
			redirect('support/listSupportTkt/');
		}else{
			$this->session->set_flashdata('msgt', 'error');
			$this->session->set_flashdata('msg',$result);
			redirect('support/listSupportTkt/');
		}
	}
	function getSupGrpEmp($id='',$val=''){
		$option='';
		$option .= (isset($val) && $val != '') ? '<option value="auto"> Auto Assign </option>' : '<option value=" "> Select Employee</option>';
		if($id != ''){
			$res = $this->supportmodel->get_grEmployees($id);
			foreach($res as $result){
				$option.='<option value="'.$result['eid'].'">'.$result['empname'].'</option>';
			}
		}
		echo $option;
	}
	function getSupLevelTime($id=''){
		$res = $this->supportmodel->getSupLevelTimes($id);
		echo $res;
	}
	function dnl_test(){
		//print_r($_POST);
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		//if(is_array($_PSOT)){
			$this->supportmodel->supportCsvTest($bid);
		//}
		$cbid=$this->session->userdata('cbid');
		
		$data=array('systemfields'=>$this->configmodel->getFields('40',$bid),
					'roleDetail'=>$this->roleDetail,
					 'eid'=>($eid!="")?$eid:'',
					 'attributes' => array('class' => 'form', 'id' =>'landingnumber','name'=>'landingnumber'),
					 'URL' => "support/dnl_test/"
					 );
		$this->load->view('supportTktCSV',$data);
	}
	function comments($tktid=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$tktid = isset($_POST['tktid']) ? $_POST['tktid'] : $tktid;
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		$itemDetail = $this->configmodel->getDetail('40',$tktid,'',$bid);
		if(isset($_POST['update_system'])){
			if(isset($_POST['comments']) && $_POST['comments'] != ''){
				$tkt_status = $_POST['tkt_status'];
				$this->supportmodel->addComments($_POST);
				$this->auditlog->auditlog_info('Support', "Ticketid - ".$itemDetail['ticket_id']." Comments updated by ".$this->session->userdata('username'));
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg',"Comments Updated Successfully ");
				redirect($_SERVER['HTTP_REFERER']);
			}else{
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg',"Your changes are not affected.Please enter the comments" );
				redirect($_SERVER['HTTP_REFERER']);
			}
			
		}
 		$data['module']['title'] = "Support Comments";
		$data['links'] = '';
		$data['nosearch']=true;
		$data['paging'] = '';
		$data['itemlist'] = $this->supportmodel->getComments($tktid,$bid);
		$data['form']=array('adv_search'=>array());
	 if(!empty($data['itemlist']['rec'])){
			$data['comments'] = $data;
		}
		$formFields[] =array("label"=>"<label class='col-sm-4 text-right' for='comment'>Comments :</label>"
									,"field"=>form_textarea(array(
												'name'      => 'comments',
												'class'     => 'form-control',
												'value'		=> '',
												'id'        => 'comments'))
							);
		$formFields[] =array("label"=>"<label class='col-sm-4 text-right' for='supstatus'>Ticket Status :</label>"
									,"field"=>form_dropdown("tkt_status",$this->supportmodel->getTktStatus($bid),$itemDetail['tkt_status'],"id='tkt_status' class='form-control'")
							);									
		$data['form'] = array(
		            'form_attr'=>array('action'=>'support/comments/','name'=>'comments','id'=>'comments','enctype'=>"multipart/form-data"),
					//~ 'open'=>form_open_multipart('support/comments/'
								//~ ,array('name'=>'comments','class'=>'form','id'=>'comments','method'=>'post')
								//~ ,array('bid'=>$bid,'tktid'=>$tktid)
								//~ ),
					'hidden'=>array('bid'=>$bid,'tktid'=>$tktid),
					'fields'=>$formFields,
					'parentids'=>'',
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close()
				);
		$this->load->view('popupFormView',$data);
	}
	function remarks($tktid=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		$modid = '40';
		if(isset($_POST['tktid'])){
			$this->supportmodel->addRemarks($_POST);
			redirect($_SERVER['HTTP_REFERER']);
		}
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data['module']['title'] = "Support Remarks";
		$data['links'] = '';
		$data['nosearch']=true;
		$data['paging'] = '';
		$data['itemlist'] = $this->supportmodel->getRemarks($tktid,$bid);
		$data['form']=array('adv_search'=>array());
		if(!empty($data['itemlist']['rec'])){
			 $data['remarks'] = $data;
	    }
		$formFields[] =array("label"=>"<label class='col-sm-4 text-right' for='remark'>Remark :</label>"
									,"field"=>form_textarea(array(
												'name'      => 'remark',
												'class'     => 'form-control',
												'value'		=> '',
												'id'        => 'remark'))
							);										
		$data['form'] = array(
		            'form_attr'=>array('action'=>'support/remarks/'.$tktid,'name'=>'remarks','id'=>'remarks','enctype'=>"multipart/form-data"),
					//~ 'open'=>form_open_multipart('support/remarks/'.$tktid
								//~ ,array('name'=>'remarks','class'=>'form','id'=>'remarks','method'=>'post')
								//~ ,array('bid'=>$bid,'tktid'=>$tktid)
								//~ ),
					'fields'=>$formFields,
					'hidden'=>array('bid'=>$bid,'tktid'=>$tktid),
					'parentids'=>'',
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close()
				);
		$this->load->view('popupFormView',$data);
	}
	function remarksHistory($tktid=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data['module']['title'] = "Ticket Remarks";
		$data['links'] = '';
		$data['nosearch']=true;
		$data['paging'] = '';
		$data['itemlist'] = $this->supportmodel->getRemarks($tktid,$bid);
		$data['form']=array('adv_search'=>array());
		$this->load->view('popupListView',$data);
	}
	function commentsHistory($tktid=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data['module']['title'] = "Ticket Comments";
		$data['links'] = '';
		$data['nosearch']=true;
		$data['paging'] = '';
		$data['itemlist'] = $this->supportmodel->getComments($tktid,$bid);
		$data['form']=array('adv_search'=>array());
		$this->load->view('popupListView',$data);
		
	}
	function callHistory($tktid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data = $this->data;
		$data['module']['title'] = $this->lang->line('level_Report');
		$data['links'] = '';
		$data['nosearch']=true;
		$data['paging'] = '';
		$data['title'] = 'Calls History';
		$formFields=array();
		$data['itemlist'] = $this->supportmodel->callHistoryList($tktid,$bid);
		$this->load->view('counter_view',$data);
	}
	function Bulk_down(){
		$daf='';	
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;			
		$fieldset = $this->configmodel->getFields('40',$bid);

		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
					$daf .= '<input type="checkbox" checked name="formfields['.$field['fieldname'].']" value="'.(($field['customlabel']!="")?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'" />'.(($field['customlabel']!="")?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'<br/>';
				}
			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$daf.='<input type="checkbox"  checked name="formfields['.$field['fieldKey'].']" value="'.$field['fieldname'].'"/>'.$field['customlabel'].'<br/>' ;
				}
			}
			
	   }
		$daf .= "<label><input type='checkbox' checked name='formfields['comments']' value='comments' />Comments</label><br/>\n";
	    $daf .= "<label><input type='checkbox' checked name='formfields['remark']' value='remark' />Remarks</label>";
		echo '
<div aria-hidden="false" id="modal-responsive" class="modal fade in" style="display: block;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
									<button aria-hidden="true" data-dismiss="modal" class="close" type="button"><i class="fa fa-times"></i></button>
			 <h4>Bulk Ticket Download</h4>
			 <form action="support/listSupportTkt/" class="form" id="blk_ddd" name="blk_ddd" method="POST">
					<TABLE>
						<tr>
							<th><label>Fields :</label></th>
							<td><!--<input type="text" name="empname" id="empname" />-->
							<input type="hidden" name="call_ids" id="call_ids" />
								'.$daf.'
							</td>
							<td></td>
						</tr>
					</TABLE>
					<div class="form-group text-center">
							<input id="button1" type="submit" class="btn btn-primary blk_submit" name="blk_down" value='.$this->lang->line('submit').' > 
                            <input id="button2" type="reset" class="btn btn-default" value='.$this->lang->line('reset').' />
                         </div>
		
					</form>
	             </div>
	          </div>
	       </div>
	      </div>
	    </div>';
	}
	function refreshcounter($sgid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res = $this->supportmodel->refreshcounter($sgid,$bid);
		if($res == 1){
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', ' The Counter has reset to 0');
		}else{
			$this->session->set_flashdata('msgt', 'error');
			$this->session->set_flashdata('msg', ' Error while reset the counter');
		}
		redirect('ListempSupGroup/'.$sgid);
	}
	function followupsetup(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$itemlist = $this->supportmodel->getconfiguration($bid);
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('interval', 'Folowup Time Interval', 'numeric');
			if (!$this->form_validation->run() == FALSE){
				$res = $this->supportmodel->followupSetup($bid);
				if($res == '1'){
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', "Record updated Successfully");
					redirect('support/followupsetup');
				}elseif($res == '3'){
					$this->session->set_flashdata('msgt', 'error');
					$this->session->set_flashdata('msg', " Record updated Successfully. You dont have enough SMS credits.");
					redirect('support/followupsetup');
				}
			}
		}
		$data['module']['title'] = "Support Configuration";
		$data['links'] = '';
		$data['nosearch']=true;
		$formFields[] =array("label"=>"<label class='col-sm-4 text-right' for='followup'>Followup :</label>"
									,"field"=>form_radio(array(
												'name'      => 'followup',
								
												'value'		=> '1',
												'id'        => 'followup',
												'checked'	=> (isset($itemlist->followup) && ($itemlist->followup == 1)) ? 'checked' : ''
												
											))." ON &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
											.form_radio(array(
												'name'      => 'followup',
											
												'value'		=> '0',
												'id'        => 'followup',
												'checked'	=> (isset($itemlist->followup) && ($itemlist->followup == 0)) ? 'checked' : '' 
											))." OFF "
							);
		$formFields[] =array("label"=>"<label class='col-sm-4 text-right' for='followup'>Followup Interval :</label>"
									,"field"=>form_input(array(
												'name'      => 'interval',
												'class'     => 'form-control',
												'value'		=> (isset($itemlist->time_interval)) ? $itemlist->time_interval : '',
												'id'        => 'interval',
												'class'        => 'form-control',
											))."&nbsp;&nbsp;(Hours)&nbsp;&nbsp;");								
		$data['form'] = array(
		                    'form_attr'=>array('action'=>'support/followupsetup','name'=>'supconfigure','id'=>'supconfigure','enctype'=>"multipart/form-data"),
							//~ 'open'=>form_open_multipart('support/followupsetup'
										//~ ,array('name'=>'supconfigure','class'=>'form','id'=>'supconfigure','method'=>'post')
										//~ ),
							'fields'=>$formFields,
							'parentids'=>'',
							'busid'=>$bid,
							'pid'=>$this->session->userdata('pid'),
							'close'=>form_close()
						);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
}
