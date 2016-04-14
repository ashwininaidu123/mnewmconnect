<?php
class Leads extends controller {
	var $data,$roleDetail;
	function Leads(){
		parent::controller();
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('systemmodel');
		$this->load->model('groupmodel');
		$this->load->model('leadsmodel');
		$this->load->model('auditlog');
		$this->load->model('msgmodel');
		$this->roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
	}
	function __destruct() {
		$this->db->close();
	}
	function feature_access(){
		$show=0;
		$checklist=$this->systemmodel->checked_featuremanage();
		if(in_array(13,$checklist)){
			$show=1;
		}
		return $show;
	}
	/* Lead Group Module */
	function lead_grp_add($id=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['37']['opt_add']) redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('groupname', 'Group Name', 'required');
			$this->form_validation->set_rules('eid', 'Group Owner', 'required');
			if (!$this->form_validation->run() == FALSE){
				$id = $this->input->post('id');
				$res = $this->leadsmodel->addleadsGroup();
				if(is_numeric($res)){
					$this->session->set_flashdata('msgt', 'success');
					if($id == ''){
						$this->session->set_flashdata('msg', "Leads Group added Successfully");
						redirect('AddempLeadGroup/'.$res);
					}else{
						$this->session->set_flashdata('msg', "Leads Group Updated Successfully");
						redirect('ListLeadGroup/0');
					}
				}elseif($res == 'FALSE'){
					$this->session->set_flashdata('msgt', 'error');
					$this->session->set_flashdata('msg', "Limit Reached, Can not create more Lead Groups. Please contact your Account Manager.");
					redirect('ListLeadGroup/0');
				}else{
					$this->session->set_flashdata('msgt', 'error');
					$this->session->set_flashdata('msg', "Leads group already existed");
					redirect('AddLeadGroup');
				}
			}
		}
		$this->sysconfmodel->data['html']['title'] .= " | Add Leads Groups";
		$data['module']['title'] = "Add Leads Groups";
		$fieldset = $this->configmodel->getFields('37',$bid);
		$itemDetail = $this->configmodel->getDetail('37',$id,'',$bid);
		$group_rule = array(""=>"Select","1"=>"Sequential","2"=>"Weighted");
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='filename'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) 
					$formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' <img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>',
												'field'=>($field['fieldname'] == 'eid') ? form_dropdown('eid',$this->groupmodel->employee_list(),isset($itemDetail['eid']) ? $itemDetail['eid']:'','id="eid" class="form-control required"')
											  :(($field['fieldname'] == 'group_rule') ?form_dropdown('grule',$group_rule,isset($itemDetail['group_rule']) ? $itemDetail['group_rule'] : '','id="grule" class="form-control required" ')
											  :form_input(array(
												'name'      => $field['fieldname'],
												'id'        => $field['fieldname'],
												'value'		=> (isset($itemDetail[$field['fieldname']])) ? $itemDetail[$field['fieldname']] : '',
												'class'		=>($field['fieldname'] == 'groupname') ? 'form-control required' : 'form-control')))
												);
			}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)$formFields[] = array(
							'label'=>'<label class="col-sm-4 control-label" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
							'field'=>$this->configmodel->createFieldAdvance($field,isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '',''));
			}
		}
		$data['form'] = array(
		           'form_attr'=>array('action'=>'leads/lead_grp_add','name'=>'addleadgrp','id'=>'addleadgrp','enctype'=>"multipart/form-data"),
					'hidden'=>array('bid'=>$bid,'id'=>$id),
					'fields'=>$formFields,'parentids'=>$parentbids,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function lead_grp_list($type=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;	
		if(!$roleDetail['modules']['37']['opt_view']) redirect('Employee/access_denied');
		if($this->input->post('submit')){
			if($this->session->userdata('search')!=""){
				$s=$this->session->unset_userdata('search');
			}
		}
		$type = ($this->uri->segment(1) == 'ListLeadGroup') ? 'act' : (($this->uri->segment(1) == 'DeleteLeadGroup') ? 'del' :'');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$ofset = ($this->uri->segment(2)!=null)?$this->uri->segment(2):0;
		$limit = '30';
		$data['itemlist'] = $this->leadsmodel->list_leads_grps($bid,$ofset,$limit,$type);
		$this->pagination->initialize(array(
						 'base_url'=>site_url($this->uri->segment(1).'/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
						,'uri_segment'=>2				
				));
		$data['module']['title'] = "Leads Groups [".$data['itemlist']['count']."]";	
		$links = array();	
		$links[] = '<li><a href="AddLeadGroup"><span title="Add Group" class="glyphicon glyphicon-plus-sign">&nbsp;Add Group</span></a></li>';
		$links[] = ($roleDetail['modules']['37']['opt_delete']) ?'<li><a href="leads/bulkDelGrp" class="blkDel"><span title="Bulk Delete" class="glyphicon glyphicon-trash">&nbsp;Delete</span></a></li>':'';
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
											?form_dropdown('eid',$this->groupmodel->employee_list(),'',"class='form-control'")
											:form_input(array(
												'name'      => $field['fieldname'],
												'id'        => $field['fieldname'],
												'class'		=>($field['fieldname']=="createdon" || $field['fieldname']=="lastmodified"|| $field['fieldname']=="convertedon")?'datepicker_leads form-control':'form-control'
												))
									);
								}		
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { $formFields[] = array(
								'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>form_input(array(
													'name'      => 'custom['.$field['fieldid'].']',
													'class'     => 'form-control'
													)));
								//$advsearch['custom['.$field['fieldid'].']']=$field['customlabel'];							
							}						
			}
		}
		$data['links'] = $links;
		$data['form'] = array(
					'open'=>form_open_multipart(site_url('ListLeadGroup/0'),array('name'=>'listleadgrp','class'=>'form','id'=>'manageemp','method'=>'post')),
					'form_field'=>$formFields,
					'parentids'=>$parentbids,
					'adv_search'=>array(),
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close(),
					'title'=>$this->lang->line('level_search')
					);
		$data['paging'] = $this->pagination->create_links();
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->data['html']['title'] .= " | Leads Module ";
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function delete_leadsgrp($id='',$type1=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['37']['opt_delete']) redirect('Employee/access_denied');
		$type = ($type1 == '') ?'2' : $type1;
		$this->leadsmodel->del_lead_grp($id,$bid,$type);
		if($type1 == '')
			return 1;
		else
			redirect('ListLeadGroup/0');
	}
	function addgrpemp($lgid=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if($this->input->post('update_system')){
			$res=$this->leadsmodel->addlead_grpemp($lgid);		
			if($res==0){
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', $this->lang->line('error_alreadyexists'));
				redirect('AddempLeadGroup'.$lgid);
			}elseif($res == '2'){
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', "Limit Reached, Can not assign Employees. Please contact your Account Manager.");
				redirect('ListempLeadGroup/'.$lgid);
			}else{
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', $this->lang->line('error_addempsucss'));
				redirect('ListempLeadGroup/'.$lgid);
			}
		}
		$roleDetail = $this->roleDetail;
		$gpDetail = $this->configmodel->getDetail('37',$lgid,'',$bid);
		if(!$roleDetail['modules']['37']['opt_add']) redirect('Employee/access_denied');
		if(count($_POST)>0){
			redirect($_SERVER['HTTP_REFERER']);
		}
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_addemptogroup');
		$data['module']['title'] = $this->lang->line('label_addemptogroup');
		$formFields = array();
		$data['form'] = array('form_attr'=>array('action'=>'leads/addgrpemp/'.$lgid,'name'=>'addGrpEmp'),
					'fields'=>$formFields,
					'close'=>form_close(),
					'gdetail'=>$gpDetail,
					'emplist'=>$this->systemmodel->get_emp_list(),
					'empexists'=>$this->leadsmodel->leadgrpemp_existed($lgid)
				);
		$this->sysconfmodel->viewLayout('form_view_leadgrpemp',$data);
	}
	function lead_grpemp_list($lgid){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = "Lead Group Employee List";
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$gpDetail = $this->configmodel->getDetail('37',$lgid,'',$bid);
		$limit = '30';
		$header = array($this->lang->line('level_empname')
						,$this->lang->line('level_group'));
		if($gpDetail['group_rule'] == 2)
			array_push($header,"Employee Weight");
		array_push($header,"Leads Counter");
		array_push($header,$this->lang->line('level_Action'));
		$data['itemlist']['header'] = $header;
		$emp_list=$this->leadsmodel->leadgrpemplist($lgid,$ofset,$limit);
		$roleDetail = $this->roleDetail;
		$opt_add = $roleDetail['modules']['37']['opt_add']; 
		$opt_delete = $roleDetail['modules']['37']['opt_delete']; 
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$opt = '';
			$s = ($item['status']=="1")?'<a href="'.site_url('leads/leadgrpemp_disable/'.$item['eid']."/".$item['gid']).'"><span class="fa fa-unlock" title="Disable"></a>':'<a href="'.site_url('leads/leadgrpemp_disable/'.$item['eid']."/".$item['gid']).'"><span class="fa fa-lock" title="Enable"></a>';
			$opt.=($opt_delete)?'&nbsp;<a href="'.site_url('leads/delete_grp_emp/'.$item['eid']."/".$item['gid']).'">
					<span title="Delete Employee from Leads Group" class="glyphicon glyphicon-trash"></span>
				  </a>':'';
			$opt.=($opt_add)?$s:'';	 
			$r = array(
				'<a href="Employee/activerecords/'.$item['eid'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.(($item['status']=="1") ? $item['empname'] : '<font color=red>'.$item['empname'].'</font>').'</a>'
				,(($item['status']=="1") ? $item['groupname'] : '<font color=red>'.$item['groupname'].'</font>'));
			if(isset($gpDetail['group_rule']) && $gpDetail['group_rule'] == 2)
				array_push($r,(($item['status']=="1") ? $item['weight'] : '<font color=red>'.$item['weight'].'</font>'));
			array_push($r,(($item['status']=="1") ? $item['counter'] : '<font color=red>'.$item['counter'].'</font>'));
			$r = array_merge($r,array($opt));
			$rec[] = $r;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('leads/lead_grpemp_list/'.$lgid)
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_groupemp');
		$links = array();
		$links[] = '<li><a href="AddempLeadGroup/'.$lgid.'"><span title="Add Employee to Lead Group" class="glyphicon glyphicon-plus-sign">&nbsp;Add Employee</span></a></li>';
		$links[] = '<li><a href="leads/refreshcounter/'.$lgid.'"><span title="Reset Counter" class="fa fa-refresh">Refresh Counter</span></a></li>';
		$formFields = array();
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">Employee Name : </label>',
				  'field'=>form_input(array(
										'name'        => 'empname',
										'class'       => 'form-control',
										'id'          => 'empname',
										'value'       => $this->session->userdata('empname'))));
						array_push($formFields,$cf);
		$data['links'] = $links;
		$data['form'] = array(
			'open'=>form_open_multipart('leads/lead_grpemp_list/'.$lgid,array('name'=>'listgrpemp','class'=>'form','id'=>'listgrpemp','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function delete_grp_emp($eid,$lgid){
		$res=$this->leadsmodel->del_leadgrpemp($eid,$lgid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg',"Employee Deleted Successfully");
		redirect('ListempLeadGroup/'.$lgid);
	}	
	function leadgrpemp_disable($eid,$lgid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->leadsmodel->leadgrpemp_dis($eid,$lgid,$bid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', ($res)?'Lead Group Employee Enabled Succesfully':'Lead Group  Employee Disabled Succesfully');
		redirect('ListempLeadGroup/'.$lgid);
	}
	function leadgrp_active($id=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_groupfrm');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('label_groupfrm');
		$fieldset = $this->configmodel->getFields('37',$bid);
		$formFields = array();
		$itemDetail = array();
		$itemDetail = $this->configmodel->getDetail('37',$id,'',$bid);
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['37']['opt_view']) redirect('Employee/access_denied');
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
						$cf = array('label'=>'<label  class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
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
					'open'=>form_open('leads/addgrpemp/'.$id,array('name'=>'form','class'=>'form','id'=>'addgroup','method'=>'post')),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->load->view('active_view',$data);
	}
	/* Lead Group Module End */
	
	/* Leads Start */
	function addLead($type){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$roleDetail = $this->roleDetail;
		$modid = ($type == 1) ? '46' : '26' ;
		if(!$roleDetail['modules'][$modid]['opt_view']) redirect('Employee/access_denied');
		$leadtype = $this->sysconfmodel->get_leadstatus();
		if(!empty($_POST)){
			$this->form_validation->set_rules('name', 'Name', 'required');
			$this->form_validation->set_rules('number', 'Number', 'required|numeric');
			$this->form_validation->set_rules('email', 'email', 'email');
			if (!$this->form_validation->run() == FALSE){
				$res = $this->leadsmodel->addlead($type);
				if($res == '1'){
					$msg = ($type == 1) ? $leadtype[$type]." added Successfully" : " Lead added Successfully " ;
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', $msg);
					//$redirect = ($type == 1) ? "ListProspect/" : "ListLead/" ;
					$redirect = "ListLead/" ;
					redirect($redirect.$type);
				}elseif($res == '2'){
					$this->session->set_flashdata('msgt', 'error');
					$this->session->set_flashdata('msg', " Limit Reached, Can not create more Leads. Please contact your Account Manager.");
					//$redirect = ($type == 1) ? "AddProspect/" : "AddLead/" ;
					$redirect = "AddLead/" ;
					redirect($redirect.$type);
				}elseif($res == '3'){
					$msg = ($type == 1) ? $leadtype[$type]." added Successfully" : " Lead added Successfully" ;
					$this->session->set_flashdata('msgt', 'error');
					$this->session->set_flashdata('msg', $msg." You dont have enough SMS credits.");
					//$redirect = ($type == 1) ? "AddProspect/" : "AddLead/" ;
					$redirect = "AddLead/" ;
					redirect($redirect.$type);
				}else{
					$msg = ($type == 1) ? $leadtype[$type]." already existed" : " Lead already existed" ;
					$this->session->set_flashdata('msgt', 'error');
					$this->session->set_flashdata('msg', $msg );
					//$redirect = ($type == 1) ? "AddProspect/" : "AddLead/" ;
					$redirect = "AddLead/" ;
					redirect($redirect.$type);
				}
			}
		}
		$this->sysconfmodel->data['html']['title'] .= ($type == 0) ? " | Add Lead " : " | Add ".$leadtype[$type];
		$data['module']['title'] = ($type == 0) ? " Add Lead " : "Add ".$leadtype[$type];
		$fieldset = $this->configmodel->getFields($modid,$bid);
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='filename'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked && !in_array($field['fieldname'] ,array('lastmodified','leadhistory','status','createdon','enteredby','lead_status','convertedby','convertedon'))) 
					$formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' <img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>',
									'field'=>($field['fieldname']=='gid')
											?form_dropdown('gid',$this->leadsmodel->getGroups(),'','id="grempId" class="form-control"')
											:(($field['fieldname']=='assignto')
												?form_dropdown('assignto',$this->leadsmodel->employee_list(),'','id="assignemp" class="form-control"')
												:form_input(array(
												'name'      => $field['fieldname'],
												'id'        => $field['fieldname'],
												'class'		=>'form-control'))
												));
												
			}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)$formFields[] = array(
							'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
							'field'=>$this->configmodel->createFieldAdvance($field,'','search'));
			}
		}
		$cf = array('label'=>'<label class="col-sm-4 text-right">Remarks : </label>',
					'field'=>form_textarea(array(
											'name'      => 'remark',
											'id'        => 'remark',
											'class'     => 'form-control',
											'value'		=> ''
											))					);
		array_push($formFields,$cf);
		$formFields[] = array('label'=>'<label class="col-sm-4 text-right"></label>',
							   'field'=>form_input(array(
											'name'      => 'autoAssign',
											'id'        => 'autoAssign',
											'class'     => 'form-control',
											'value'		=> 'singleLead',
											'type'	  => 'hidden')
											)
								);
		$arr=array("0"=>"Select","1"=>"Email Alert","2"=>"SMS Alert","3"=>"Both");
		$cf = array('label'=>'<label class="col-sm-4 text-right"> Alert Type :</label>	'
					,"field"=>form_dropdown('alert_type',$arr,'','class="form-control"')
					);
		array_push($formFields,$cf);	
		$formFields[] = array('label'=>'<label class="col-sm-4 text-right"></label>',
							   'field'=>form_input(array(
											'name'      => 'duplicate',
											'id'        => 'duplicate',
											'class'     => 'form-control',
											'value'		=> '',
											'type'	  => 'hidden')
											)
								);
		$formFields[] = array('label'=>'<label class="col-sm-4 text-right"></label>',
							   'field'=>form_input(array(
											'name'      => 'parentId',
											'id'        => 'parentId',
											'class'     => 'form-control',
											'value'		=> '',
											'type'	  => 'hidden')
											)
								);
		//$redirect = ($type == 1) ? "AddProspect/" : "AddLead/" ;
		$redirect = "AddLead/" ;
		$data['form'] = array(
		             'form_attr'=>array('action'=> $redirect.$type,'name'=>'addlead','enctype'=>"multipart/form-data",'id'=>'addlead'),
					'fields'	=>	$formFields,
					'parentids'	=>	$parentbids,
					'busid'		=>	$bid,
					'pid'		=>	$this->session->userdata('pid'),
					'close'		=>	form_close()
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function index($type=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$type = ($type != '') ? $type : (($this->uri->segment('3') != '') ? $this->uri->segment('3') : 'all' );
		$roleDetail = $this->roleDetail;
		$modid = ($type == 1) ? '46' : '26' ;
		if(!$roleDetail['modules'][$modid]['opt_view']) redirect('Employee/access_denied');
		if(!isset($_POST['module']) || $_POST['module']!='lead'){
			$this->session->unset_userdata('Adsearch');
		}
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$dlink = "";
		if($this->input->post('download')){
		    ini_set("max_execution_time","0");
			$filename = $this->leadsmodel->leads_csv($type,$bid);
			$dlink =  "<a href='".$this->config->item('reports_path').$filename.".zip' target='_blank' style='color:#fff'><b>Download</b></a>  ";
		}elseif($this->input->post('blk_down')){
			$filename = $this->leadsmodel->blk_down($bid,$type);
			$dlink =  "<a href='".$this->config->item('reports_path').$filename.".zip"."' target='_blank' style='color:#fff'><b>Download</b></a>  ";
		}
		if($this->input->post('submit')){	
			if($this->session->userdata('search')!=""){
				$s=$this->session->unset_userdata('search');
			}
		}
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$u3 = ($this->uri->segment(3)!='')?$this->uri->segment(3):'all';
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '30';
		$data['itemlist'] = $this->leadsmodel->list_leads($type,$ofset,$limit,$u3);
		$this->pagination->initialize(array(
						 'base_url'=>site_url($this->uri->segment(1).'/'.$type.'/'.$u3.'/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
						,'uri_segment'=>4				
				));
		$leadtype = $this->sysconfmodel->get_leadstatus();
		$title_lead = ($type == 0) ? " Lead " : $leadtype[$type];
		$data['module']['title'] = $title_lead ." [".$data['itemlist']['count']."]";
		$links = array();
		$links[] = '<li><a href="AddLead/'.$type.'"><span title="Add" class="glyphicon glyphicon-plus-sign">&nbsp;Add</span></a></li>';		
		$links[] = '<li><a href="ImportLeads/'.$type.'/step1"><span title="Import" class="fa fa-upload">&nbsp;&nbsp;&nbsp;Import</span></a></li>';
		$links[] = ($roleDetail['modules'][$modid]['opt_delete']) ?'<li><a href="leads/bulkDel" class="blkDel"><span title="Bulk Delete" class="glyphicon glyphicon-trash">&nbsp;Bulk  Delete</span></a></li>':'';
		$links[] = ($roleDetail['modules'][$modid]['opt_add']) ?'<li><a href="leads/bulkAssign/'.$type.'" class="blkAssign" data-toggle="modal" data-target="#modal-blkAssign"><span title="Bulk Assign" class="glyphicon glyphicon-share">&nbsp;Bulk Assign</span></a></li>':'';
		$links[] = ($roleDetail['modules'][$modid]['opt_add']) ?'<li><a href="leads/leadowner/'.$type.'" class="leadowner" data-toggle="modal" data-target="#modal-leadowner"><span title="Bulk Lead Owner" class="glyphicon glyphicon-move">&nbsp;Bulk Lead Owner</a></li>':'';
		$links[] = '<li><a href="leads/bulkStatChng/'.$type.'" class="blkStatus" data-toggle="modal" data-target="#modal-blkStatus"><span title="Bulk Status Change" class="glyphicon glyphicon-repeat">&nbsp;Bulk  Status Change</span></a></li>';
		$links[] = '<li><a href="#" class="blkemail" rel="leads"><span title="Bulk Mail" class="glyphicon glyphicon-envelope">&nbsp;Bulk  Email</span></a></li>';
		$links[] = '<li><a href="Report/blksms" class="blkSMs" data-toggle="modal" data-target="#modal-blksms" rel="leads"><span title="Bulk SMS" class="glyphicon glyphicon-comment">&nbsp;Bulk SMS</span></a></li>';
		$links[] = '<li class="divider">&nbsp;</li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-advsearch" data-toggle="modal" data-target="#modal-advsearch" ><span title="Search" class="glyphicon glyphicon-zoom-in">&nbsp;Advance Search</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = ($roleDetail['modules'][$modid]['opt_download']!=0)? '<li><a href="leads/Bulk_down/'.$type.'" class="blk_calls"  data-toggle="modal" data-target="#modal-pop"><span title="Download" class="glyphicon glyphicon-arrow-down">&nbsp;Download Select</span></a></li>':'';
		$links[] = ($roleDetail['modules'][$modid]['opt_download']) ? '<li><a href="leads/leads_csv/'.$type.'" class="btn-csv" data-toggle="modal" data-target="#modal-csv"><span title="Download" class="glyphicon glyphicon-download-alt">&nbsp;Download All</span></a></li>':'';
		$fieldset = $this->configmodel->getFields($modid,$bid);
		$formFields = array();
		$advsearch = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],array('enteredby','lead_status','leadhistory'))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { $formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>
										($field['fieldname']=='gid')
											?form_dropdown('gid',$this->leadsmodel->getGroups(),'',"class='form-control'")
											:(($field['fieldname']=='assignto')
												?form_dropdown('assignto',$this->leadsmodel->employee_list(),'',"class='form-control'")
                                                 :(($field['fieldname']=='enteredby')
												?form_dropdown('enteredby',$this->leadsmodel->employee_list(),'',"class='form-control'")											
                                                 :(($field['fieldname']=='convertedby')
												?form_dropdown('convertedby',$this->leadsmodel->employee_list(),'',"class='form-control'")
												:(($field['fieldname']=='createdon' || $field['fieldname']=='lastmodified'|| $field['fieldname']=='convertedon')
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
												))))
										);
										$advsearch[$field['fieldname']]=(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']);
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
		unset($advsearch['gid']);
		unset($advsearch['assignto']);
		$advsearch['enteredby'] = 'Entered By';
		$save_cnt=save_search_count($bid,$modid,$this->session->userdata('eid'));
		$search_names=get_save_searchnames($bid,$modid,$this->session->userdata('eid'));
		$leadtype = $this->sysconfmodel->leadtypeCheck();
		$data['downlink'] = $dlink;	
		$data['links'] = $links;	
		$data['form'] = array(
					'open'=>form_open_multipart(site_url('ListLead/'.$type)
						,array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')
						,array('module'=>'lead')),
					'form_field'=>$formFields,
					'adv_search'=>$advsearch,
					'save_search'=>$save_cnt,
					'search_names'=>$search_names,
					'groups'=>$this->leadsmodel->getGroups(),
					'employees'=>$this->leadsmodel->employee_list(),
					'search_url'=>$this->uri->segment(1).'/'.$type.'/',
					'parentids'=>$parentbids,
					'busid'=>$bid,
					'leadtype'=>$leadtype,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close(),
					'title'=>$this->lang->line('level_search')
					);
		$data['paging'] = $this->pagination->create_links();
		$data['tab'] = true;
		$this->sysconfmodel->data['html']['title'] .= " | Leads Module";

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
	function edit($id,$type=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$type = ($type == '') ? '1' : $type ;
		$roleDetail = $this->roleDetail;
		$modid = '26' ;
		if(!$roleDetail['modules'][$modid]['opt_add']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if(!empty($_POST)){
			$res = $this->leadsmodel->edit_lead($id,$bid,$type);
			if($res == '1'){
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', "Record updated Successfully");
				redirect($_POST['httpRefer']);
			}elseif($res == '3'){
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', " Record updated Successfully. You dont have enough SMS credits.");
				redirect('EditLead/'.$id.'/0');
			}elseif($res == '0'){
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', "Modified Contact number already existed");
				redirect('EditLead/'.$id.'/0');
			}elseif($res == '2'){
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', "The converted Prospect already existed under Leads");
				redirect($_POST['httpRefer']);
			}
		}
		$leadtype = $this->sysconfmodel->get_leadstatus();
		$title_lead = ($type == 0) ? " Lead " : $leadtype[$type];
		$data['module']['title'] = "Edit ".$title_lead;
		$fieldset = $this->configmodel->getFields($modid);
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail($modid,$id,'',$bid);
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				$gid = ($itemDetail['gid'] != 0) ? $itemDetail['gid'] : '' ;
					if($checked){
						$cf = array('label'=>'<label class="col-sm-4 control-label" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>',
									'field'=>($field['fieldname']=='gid')
											 ? form_dropdown('gid',$this->leadsmodel->getGroups(),$itemDetail['gid'],'id="grempId"  class="form-control required"')
											 :(($field['fieldname']=='assignto')
												?form_dropdown('assignto',$this->leadsmodel->allEmployees($gid,$itemDetail['assignto']),$itemDetail['assignto'],"id='assignemp'  class='form-control'")
												:(($field['fieldname']=='lead_status') ? form_dropdown('lead_status',$this->leadsmodel->getLeadType($itemDetail['lead_status'],$itemDetail['type']),$itemDetail['lead_status'],"id='lead_status' class='form-control'")
												:(($field['fieldname']=='enteredby')
												  ?$itemDetail['enteredempname']
												  :(($field['fieldname']=='convertedby')
												  ?$itemDetail['convertedemp']
												  :((in_array($field['fieldname'],array('source','keyword','number')))?(($itemDetail['source'] == 'Calltrack') ? $itemDetail[$field['fieldname']] 
																					:form_input(array('name'=> $field['fieldname'], 'id'=> $field['fieldname'], 'class' => 'form-control','value'=> (isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'')))
												    ):(($field['fieldname']!="name" && $field['fieldname']!="email" && $field['fieldname']!="number" && $field['fieldname']!="caller_add" && $field['fieldname']!="caller_bus" && $field['fieldname']!="remark" && $field['fieldname']!="refId"  ) 
												     ? $itemDetail[$field['fieldname']]
												     :form_input(array(
																	  'name'      => $field['fieldname'],
																	  'id'        => $field['fieldname'],
																	  'class'     => 'form-control',
																	  'value'     => isset($itemDetail[$field['fieldname']])?stripslashes($itemDetail[$field['fieldname']]):''
														)))
												     )
												))
											 ))
											);
						array_push($formFields,$cf);
				}
			}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)$formFields[] = array(
							'label'=>'<label class="col-sm-4 control-label" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
							'field'=>$this->configmodel->createFieldAdvance($field,isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '',''));
			}
		}
		$latestRemark = $this->leadsmodel->getRemarkById($id,$bid);
		$cf = array('label'=>'<label class="col-sm-4 control-label">Remarks : </label>',
					'field'=>form_textarea(array(
											'name'      => 'remark',
											'id'        => 'remark',
											'class'     => 'form-control',
											'placeholder'=> $latestRemark
											))					);
		array_push($formFields,$cf);
		$formFields[] = array('label'=>'',
							   'field'=>form_input(array(
											'name'      => 'autoAssign',
											'id'        => 'autoAssign',
											'class'     => 'form-control',
											'value'		=> 'singleLead',
											'type'	  	=> 'hidden')
											)
								);
		$array=array("0"=>"Select","1"=>"Email Alert","2"=>"SMS Alert","3"=>"Both");
		$formFields[] =array("label"=>"<label class='col-sm-4 control-label' for='alert'>Alert :</label>"
								,"field"=>form_dropdown("alert_type",$array,'','class="form-control"')
							);
		$formFields[] = array('label'=>'',
							   'field'=>form_input(array(
											'name'      => 'duplicate',
											'id'        => 'duplicate',
											'class'     => 'form-control',
											'value'		=> '',
											'type'	  => 'hidden')
											)
								);
		$formFields[] = array('label'=>'',
							   'field'=>form_input(array(
											'name'      => 'callfrom',
											'id'        => 'callfrom',
											'class'     => 'form-control',
											'value'		=> $itemDetail['number'],
											'type'	  => 'hidden')
											)
								);
		$latestComment = $this->leadsmodel->getCommentById($id,$bid);
		$cf = array('label'=>'<label class="col-sm-4 control-label">Comments : </label>',
					'field'=>form_textarea(array(
											'name'      => 'comments',
											'id'        => 'comments',
											'placeholder'=> $latestComment,
											'class'		=> 'form-control'
											))					);
		array_push($formFields,$cf);
		$cf = array('label'=>'<label class="col-sm-4 control-label">Remarks History : </label>',                                                                        
					'field'=>anchor("leads/remarks_history/".$id."/".$type, ' <img src="system/application/img/icons/remarks.png" title="Leads Remarks"  width="16" height="16">' ,'class="btn-followup" data-toggle="modal" data-target="#modal-followup"')
					);
		array_push($formFields,$cf);
		$cf = array('label'=>'<label class="col-sm-4 control-label">Comments History : </label>',
					'field'=>anchor("leads/comments_history/".$id."/".$type, ' <span title="Leads Comments" class="fa fa-comment"></span>','class="btn-followup" data-toggle="modal" data-target="#modal-followup"')
					);
		array_push($formFields,$cf);
		$cf = array('label'=>'<label class="col-sm-4 control-label">Click To Connect : </label>',
					'field'=>anchor("Report/clicktoconnect/".$id."/4", '<span title="click To Connect" class="fa fa-phone"></span>','class="clickToConnect"')
					);
		array_push($formFields,$cf);
		$cf = array('label'=>'<label class="col-sm-4 control-label">Followup : </label>',
					'field'=>anchor("Report/followup/".$id."/0/leads",'<img src="system/application/img/icons/comments.png" style="vertical-align:top;" title="Followups" width="16" height="16">','class="btn-followup" data-toggle="modal" data-target="#modal-followup"')
					);
		array_push($formFields,$cf);
		$refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		$data['form'] = array(
		            'form_attr'=>array('action'=>'EditLead/'.$id.'/'.$type,'name'=>'editlead','enctype'=>"multipart/form-data",'id'=>'addlead'),
		            'hidden' => array('httpRefer'=>$refer,"leadid"=>$id),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function delete($id,$type=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$type = ($type == '') ? '1' : $type ;
		$roleDetail = $this->roleDetail;
		$modid = ($type == 1) ? '46' : '26' ;
		if(!$roleDetail['modules'][$modid]['opt_delete']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$this->leadsmodel->delete_lead($id,$bid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg',"Record Deleted Successfully");
		$redirect = ($type == 1) ? "ListProspect/" : "ListLead/" ;
	    redirect($redirect.$type);
	}
	function deleteList($type=''){
		$type = ($type == '') ? 0 : $type;
		if(!$this->feature_access())redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		$modid = ($type == 1) ? '46' : '26' ;
		if(!$roleDetail['modules'][$modid]['opt_delete']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$heading="Deleted List ";
		if($this->input->post('submit')){	
			if($this->session->userdata('search')!=""){
				$s=$this->session->unset_userdata('search');
			}
		}
		$redirect = ($type == 1) ? "DeleteProspect/" : "DeleteLead/" ;
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$data['itemlist'] = $this->leadsmodel->deleted_list($type,$bid,$ofset,$limit);
		$this->pagination->initialize(array(
						 'base_url'=>site_url($redirect.'/'.$type)
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
						,'uri_segment'=>3				
				));

		$data['module']['title'] = "Deleted List";
		$fieldset = $this->configmodel->getFields($modid,$bid);
		$links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search">&nbsp;Search</span></a></li>';
		$data['links'] = $links;
		$formFields = array();
		$advsearch=array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],array('enteredby'))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { $formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>
										($field['fieldname']=='gid')
											?form_dropdown('gid',$this->leadsmodel->getGroups(),'',"class='form-control'")
											:(($field['fieldname']=='assignto')
												?form_dropdown('assignto',$this->leadsmodel->employee_list(),'',"class='form-control' ")
												:form_input(array(
													'name'      => $field['fieldname'],
													'id'        => $field['fieldname'],
													'class'		=>($field['fieldname']=="createdon" || $field['fieldname']=="lastmodified"|| $field['fieldname']=="convertedon")?'datepicker_leads form-control':'form-control'
													))
												)
										);
								}			
			}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)$formFields[] = array(
							'label'=>'<label class="col-sm-4 text-right"for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
							'field'=>$this->configmodel->createFieldAdvance($field,'','search'));
			}
		}
		$save_cnt=save_search_count($bid,$modid,$this->session->userdata('eid'));
		$data['form'] = array(
					'open'=>form_open_multipart(site_url($redirect.'/'.$type),array('name'=>'del_lead','class'=>'form','id'=>'del_lead','method'=>'post')),
					'form_field'=>$formFields,
					'adv_search'=>array(),
					'parentids'=>$parentbids,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close(),
					'title'=>$this->lang->line('level_search')
					);
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | Deleted List";
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function leads_csv($type=''){
		$type = ($type == '') ? '1' : $type;
		$modid = ($type == 1) ? '46' : '26' ;
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data=array('systemfields'=>$this->configmodel->getFields($modid,$bid),
					'roleDetail'=>$this->roleDetail,
					 'attributes' => array('class' => 'form', 'id' =>'landingnumber','name'=>'landingnumber'),
					 'URL' => "leads/index/".$type,
					 'bid' => $bid
					 );
		$this->load->view('leads_csv',$data);
	}
	function undelete($leadid,$type=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->leadsmodel->undelete_lead($leadid,$bid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg',"Record restored Successfully");
		$redirect = ($type == 1) ? "DeleteProspect/" : "DeleteLead/" ;
	    redirect($redirect.$type);
	}
	function active_lead($id='',$type=''){
		$type = ($type == '') ? 1 : $type;
		if(!$this->feature_access())redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		$modid = '26' ;
		if(!$roleDetail['modules'][$modid]['opt_view']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$leadtype = $this->sysconfmodel->get_leadstatus();
		$data['module']['title'] = ($type == 0) ? " Lead Details" : $leadtype[$type];
		$fieldset = $this->configmodel->getFields($modid,$bid);
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail($modid,$id,'',$bid);
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
						$cf = array('label'=>'<label class="col-sm-4 text-right">'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).' : </label>',
									'field'=>($field['fieldname']=='gid')
											?$itemDetail['groupname']
											:(($field['fieldname']=='lead_status') ? $itemDetail['type']
											:(($field['fieldname']=='assignto')
												?$itemDetail['assignempname']
												:(($field['fieldname']=='enteredby')
												?$itemDetail['enteredempname']
												:$itemDetail[$field['fieldname']])))								
							);
						array_push($formFields,$cf);
				}
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked)$formFields[] = array(
						'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
						'field'=>isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '');
			}
		}

		$data['form'] = array(
		            'form_attr'=>array('action'=>'EditLead/'.$id,'name'=>'leadsedit','id'=>'leadsedit'),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$fdata['module']['title'] = "Followups";
		$fdata['links'] = '';
		$fdata['nosearch']=true;
		$fdata['paging'] = '';
		$fdata['itemlist'] = $this->leadsmodel->getFollowuplist($id,$bid);
		$fdata['form'] =array('adv_search'=>array());
		if(!empty($fdata['itemlist']['rec'])){
			$data['followups'] = $fdata;
		}
		$cdata['module']['title'] = "Comments";
		$cdata['links'] = '';
		$cdata['nosearch']=true;
		$cdata['paging'] = '';
		$cdata['itemlist'] = $this->leadsmodel->getComments($id,$bid);
		$cdata['form'] =array('adv_search'=>array());
		if(!empty($cdata['itemlist']['rec'])){
			$data['comments'] = $cdata;
		}
		$this->load->view('active_view',$data);
	}
	/********* Import Section ********/
	function import($ltype='',$steps=''){
		$ltype = ($ltype == '') ? 1 : $ltype;
		if(!$this->feature_access())redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		$modid = ($ltype == 1) ? '46' : '26' ;
		if(!$roleDetail['modules'][$modid]['opt_add']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		if($steps == '' || $steps =='step1'){
			$data['module']['title'] = "Import - Step1";
			$enclosure = array(" "=>"Select","&quot;"=>"Double Quote (&quot;)","'"=>"Single Quote(')");
			$formFields[] = array('label'=>'<label class="col-sm-4 text-right" for="group">Group&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="Select the group from list">&nbsp;&nbsp;: </label>',
							   'field'=>form_dropdown('gid',$this->leadsmodel->getGroups(),'','id="grempId" class="form-control required"')
							);
			$formFields[] = array('label'=>'<label class="col-sm-4 text-right" for="assignto">Assignto&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="Select the group from list">&nbsp;&nbsp;: </label>',
							   'field'=>form_dropdown('assignto',$this->leadsmodel->employee_list(),'','id="assignemp" class="form-control required"')
								);
			$formFields[] = array('label'=>'<label class="col-sm-4 text-right" for="enclosure">Data Enclosure &nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="This is the enclosure of the records which present in import file">&nbsp;&nbsp;: </label>',
							   'field'=>form_dropdown('enclosure',$enclosure,'',"class='form-control'")
								);
			$formFields[] = array('label'=>'<label class="col-sm-4 text-right" for="filename">Upload File&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="It supports only .csv format">&nbsp;&nbsp;: </label>',
							   'field'=>form_input(array(
											'name'      => 'filename',
											'id'        => 'filename',
											'class' 	=> 'required',
											'type'	  	=> 'file')
											)
								);
			$formFields[] = array('label'=>'<label class="col-sm-4 text-right" ></label>',
							   'field'=>form_input(array(
											'name'      => 'autoAssign',
											'id'        => 'autoAssign',
											'class'     => 'form-control',
											'value'		=> 'auto',
											'type'	    => 'hidden')
											)
								);
			$data['form'] = array(
			        'form_attr'=>array('action'=>'ImportLeads/'.$ltype.'/step2','name'=>'importLead1','id'=>'importLead1','enctype'=>"multipart/form-data"),
					'hidden'=>array('bid'=>$bid),
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
				redirect('ImportLeads/1/step1');
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
				$data['mapping_fileds'][0]= "Select Field";
				$system_leads = $this->leadsmodel->getLeadSystemfields($ltype,$bid);
				foreach($system_leads as $k=>$v){
					if(!in_array($v,array("leadid","bid","gid","enteredby","lastmodified","status","createdon"))){
						$data['mapping_fileds'][$k."_s"] = $v;
					}
				}
				$custom_leads = $this->leadsmodel->getLeadCustomFields($ltype);
				foreach($custom_leads as $k=>$v){
					$data['mapping_fileds'][$k."_c"] = $v;
				}
				$data['mapping_fileds']['1_o']= "Comments";
				$data['mapping_fileds']['2_o']= "Remarks";
				$data['module']['title'] = "Import - Step2";
				if($this->input->post('assignto')){
				$formFields[] = array('label'=>'<label class="col-sm-4 text-right" ></label>',
								   'field'=>form_input(array(
												'name'      => 'assignto',
												'id'        => 'assignemp',
												'class'     => 'form-control',
												'value' 	=> $this->input->post('assignto'),
												'type'	  => 'hidden')
												)
									);
				}
				$formFields[] = array('label'=>'<label class="col-sm-4 text-right" ></label>',
							   'field'=>form_input(array(
											'name'      => 'ltype',
											'id'        => 'ltype',
											'class'     => 'form-control',
											'value'		=> $ltype,
											'type'	    => 'hidden')
											)
								);
				$formFields[] = array('label'=>'<label class="col-sm-4 text-right" ></label>',
								   'field'=>form_input(array(
												'name'      => 'gid',
												'id'        => 'gid',
												'class'     => 'form-control',
												'value' 	=> $this->input->post('gid'),
												'type'	  => 'hidden')
												)
									);
				$formFields[] = array('label'=>'<label class="col-sm-4 text-right"></label>',
								   'field'=>form_input(array(
												'name'      => 'filename',
												'id'        => 'filename',
												'class'     => 'form-control',
												'value' 	=> $moved_file,
												'type'	  => 'hidden')
												)
									);
				$formFields[] = array('label'=>'<label class="col-sm-4 text-right" ></label>',
								   'field'=>form_input(array(
												'name'      => 'enclosure',
												'id'        => 'enclosure',
												'class'     => 'form-control',
												'value' 	=> $_POST['enclosure'],
												'type'	    => 'hidden')
												)
									);
				foreach($headerData as $k=>$v){
					$formFields[] = array('label'=>'<label  class="col-sm-4 text-right" for="filename">'.$v.'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="Please Map the file header fields to Database table columns"></label>',
									  'field'=>form_dropdown('field['.$k.']',$data['mapping_fileds'],' ','class="form-control"')
									);	
				}
				$multiSel = array("group"=>"Group");
				$formFields[] = array('label'=>'<label  class="col-sm-4 text-right" for="duplicatecheck"> Duplicate check &nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="Please select the columns which should not be duplicate"></label>',
									  'field'=>form_multiselect('duplicate[]',$multiSel,'group',"multiple='multiple' class='form-control'")
									);						
				$data['form'] = array(
				        'form_attr'=>array('action'=>'ImportLeads/'.$ltype.'/step3','name'=>'importLead2','id'=>'importLead2','enctype'=>"multipart/form-data"),
						'hidden'=> array('bid'=>$bid),
						'fields'=>$formFields,'parentids'=>$parentbids,
						'busid'=>$bid,
						'pid'=>$this->session->userdata('pid'),
						'close'=>form_close()
					);
				$this->sysconfmodel->viewLayout('form_view',$data);
			}
		}elseif($steps =='step3'){
			$data = $custdata = array();
			$dis_type = 1;
			$post_array = $this->input->post('field');
			$duplicatemsg = '';
			for($im=0;$im<count($post_array);$im++){
				$stat[$im] = ($post_array[$im] == 0 ) ? 0 : 1 ;
			}
			if(!(@in_array(1,$stat))){
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg',"Please Map atleast one field ");
				redirect('ImportLeads/1/step1');
			}
			$sysfields = $custfields = $othfields = array();
			foreach($post_array as $key=>$val){
				if($val != 0){
					$ex = @explode('_',$val);
					if(@in_array('s',$ex)){
						$sysfields[$key] = $ex[0];
					}elseif(@in_array('c',$ex)){
						$custfields[$key] = $ex[0];
					}elseif($ex[0] != ' '){
						$othfields[$key] = $ex[0];
					}
				}
			}
			$moved_file = $this->input->post('filename');
			$gid = $this->input->post('gid');
			$row =1;
			if (($handle = fopen($moved_file, "r")) !== FALSE) {
				while (($csvdata = fgetcsv($handle,'', ',',$this->input->post('enclosure')) )!== FALSE) {
					if($row != 1 && $csvdata[0]!=''){
						for($k=0;$k<count($csvdata);$k++){
							if(isset($sysfields[$k])){
								$fname = $this->leadsmodel->getSysfieldName($sysfields[$k]);
								if($fname == 'assignto'){
									$assempid = 0;
									$email_exists = $this->leadsmodel->empAssign_exists($csvdata[$k]);
									if($email_exists != NULL){
										 $assempid = $email_exists;
										 $data['gid'] = 0;
									}
									$data[$fname] = $assempid;
								}else{
									$data[$fname] = $csvdata[$k];
								}
							}
							if(isset($custfields[$k])){
								$fname = $this->leadsmodel->getCustfieldKey($custfields[$k],$bid);
								if($csvdata[$k] != ''){
									$custValueChk = $this->leadsmodel->getfieldValchk($csvdata[$k],$custfields[$k],$bid);
									if($custValueChk ==  TRUE){
										$data[$fname] = $csvdata[$k];
									}
								}
							}
							if(isset($othfields[$k])){
								$fname = ($othfields[$k] == 1) ? "comments" : "remarks";
								$data[$fname] = $csvdata[$k];
							}
						}
						if(! @in_array('assignto',array_keys($data))){
							$assempid = 0;
							 if(is_numeric($this->input->post('assignto'))){
								 $assempid = $this->input->post('assignto');
								 $dis_type = 1;
							 }elseif($this->input->post('assignto') =='auto'){
								$rule = $this->db->query("SELECT group_rule as rule FROM ".$bid."_leads_groups WHERE gid='".$gid."'")->row()->rule;
								if($rule == '2'){
									$resultemp = $this->db->query("SELECT e.eid,COALESCE(((weight/(SELECT sum(weight) FROM ".$bid."_leads_grpemp WHERE gid=ge.gid))-(counter/(SELECT sum(counter) FROM ".$bid."_leads_grpemp WHERE gid=ge.gid))),0) as pc FROM ".$bid."_employee e LEFT JOIN ".$bid."_leads_grpemp ge on e.eid=ge.eid WHERE ge.gid='".$gid."' AND ge.status = 1 AND e.status = 1 ORDER BY pc DESC LIMIT 0,1")->result_array();
									if(count($resultemp) > 0){
										$assempid = $resultemp[0]['eid'];
									} 
								}elseif($rule == '1'){
									$eid = $this->db->query("SELECT ge.eid FROM ".$bid."_leads_grpemp ge LEFT JOIN ".$bid."_employee e ON ge.eid = e.eid WHERE ge.gid='".$gid."' AND ge.bid='".$bid."' AND ge.status = 1 AND e.status = 1 ORDER BY ge.counter ASC LIMIT 0,1")->result_array();
									if(count($eid) > 0){
										$assempid = $eid[0]['eid'];
									}
								}
								$dis_type = 2;
							 }
							 $data['assignto'] = $assempid;
						}
						@array_key_exists('gid',$data) ? '' : ($data['gid'] = $gid);
						$data['bid'] = $bid;
						$data['enteredby'] = $this->session->userdata('eid');
						$data['leadowner'] = $this->session->userdata('eid');						
						$data['convertedby'] = $this->session->userdata('eid');
						$data['createdon'] = date("Y-m-d H:i:s");
						$data['lastmodified'] = date("Y-m-d H:i:s");
						$data['convertedon'] = date("Y-m-d H:i:s");
						$data['lead_status'] = ($ltype == 0) ? '2' : $ltype;
						$data['status'] = 1;
						$data['dis_type'] = $dis_type;
						$insertLeads = $this->leadsmodel->addimportLeads($data,$custdata);
						if(!is_numeric($insertLeads)){
							$duplicatemsg = "Duplicate Records Skipped "; 
						}
						if($data['assignto'] != '' && $data['gid'] != '' ){
							if($dis_type == 2)
								$this->db->query("UPDATE ".$bid."_leads_grpemp SET `counter`=(`counter`+1) WHERE eid='".$data['assignto']."' AND gid='".$data['gid']."'");
						}
						unset($data['assignto']);
						unset($data['gid']);
					}
					$row++;
				}
				fclose($handle);
			}
			$this->auditlog->auditlog_info('Leads'," Leads are imported by ".$this->session->userdata('username'));
			if($insertLeads == '2'){
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', "You do not have enough usage, Please contact your Account manager.");
				redirect('ListLead/'.$ltype);
			}else{
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg',"Imported Leads Successfully Done. ".$duplicatemsg);
				redirect('ListLead/'.$ltype);
			}
		}
	}
	/********************  Import END  ***********************************/
	function followup($id='',$dsh = ''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		if(isset($_POST['leadid'])){
			$this->leadsmodel->addFollowup($_POST);
			redirect($_SERVER['HTTP_REFERER']);
		}
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		$data['module']['title'] = "Followups";
		$data['links'] = '';
		$data['nosearch']=true;
		$data['paging'] = '';
		$data['itemlist'] = $this->leadsmodel->getFollowuplist($id,$bid,$dsh);
		$data['form']=array('adv_search'=>array());
		$this->load->view('list_view',$data);
		if($dsh != 1){
		$fieldset = $this->configmodel->getFields('29',$bid);
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='eid'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) 
					$formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' <img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>',
									'field'=>($field['fieldname']=="comment")?form_textarea(array(
												'name'      => 'comment',
												'id'        => 'comment'))
												:form_input(array(
													'name'      => $field['fieldname'],
													'id'        => $field['fieldname'],
													'class'		=>($field['fieldname']=="followupdate")?'datepicker_leads':''
													))
											);
			
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked)$formFields[] = array(
						'label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
						'field'=>$this->configmodel->createField($field,'','search'));
				}
			
		}	
			$array=array("0"=>"Select","1"=>"Email Alert","2"=>"SMS Alert","3"=>"Both");
			$formFields[] =array("label"=>"<label for='alert'>Alert :</label>"
								,"field"=>form_dropdown("alert",$array,"")
								);
			$formFields[] =array("label"=>"<label for='alert'>Notification Time :<img title='Time Limit of reaching SMS alert' src='system/application/img/icons/help.png' /></label>"
								,"field"=>form_input(array(
													'name'      => 'notify_time',
													'id'        => 'notify_time',
													'value'		=> '5'
													))." Mins (Previous)"
								);									
			$data['form'] = array(
			           'form_attr'=>array('action'=>'leads/followup','name'=>'followup'),
						'hidden'=>array('bid'=>$bid,'leadid'=>$id),
						'fields'=>$formFields,
						'parentids'=>'',
						'busid'=>$bid,
						'pid'=>$this->session->userdata('pid'),
						'close'=>form_close()
					);
			$this->load->view('form_view',$data);
	    }
	}
	function bulkDel(){
		$res=$this->leadsmodel->blk_del($_POST['leadids']);
		echo "1";
	}
	function bulkDelGrp(){
		$res=$this->leadsmodel->blk_delGrp($_POST['leadids']);
		echo "1";
	}
	
	function bulkAssign($type){
		if(!$this->feature_access())redirect('Employee/access_denied');
		echo '<div class="modal-dialog modal-lg">
		         <div class="modal-content">
		         	<div class="modal-body">
							
					<button aria-hidden="true" data-dismiss="modal" class="close" type="button"><i class="fa fa-times"></i></button>
                      <h4>'.$this->lang->line('level_leadsassign').'</h4>
		<form action="leads/assignEmp/'.$type.'" class="form" id="leadsassign" name="leadsassign" method="POST">
		<div class="form-group col-sm-12">
					<label class="col-sm-4 text-right">Group :</label>
						<!--<input type="text" name="groupname" id="groupname" />-->
						<input type="hidden" name="ids" id="ids" />
						 <div class="col-sm-6 input-icon right">  
							'.form_dropdown('gid',$this->leadsmodel->getGroups(),'','id="grempId" class="form-control"').'
					</div>
				</div>
					<div class="form-group col-sm-12">
					<label class="col-sm-4 text-right">Employee :</label>
						<!--<input type="text" name="empname" id="empname" />-->
						 <div class="col-sm-6 input-icon right">  
							'.form_dropdown('assignto',array(''=>"Select"),'','id="assignemp" class="form-control"').'
			     	</div>
				</div>
		       <div class="form-group text-center">
					<input id="button1" type="submit" class="btn btn-primary blk_submit" name="submit" value='.$this->lang->line('submit').' > 
                    <input id="button2" type="reset" class="btn btn-default" value='.$this->lang->line('reset').' />
                </div>
				</form>
		</div></div></div>
</div>';
	}
function leadowner($type){
		if(!$this->feature_access())redirect('Employee/access_denied');
				echo '<div class="modal-dialog modal-lg">
		     <div class="modal-content">
			<div class="modal-body">
				<div class="row">					
					<button aria-hidden="true" data-dismiss="modal" class="close" type="button"><i class="fa fa-times"></i></button>
                      <h4>'.$this->lang->line('level_leadsassign').'</h4>
		<form action="leads/assignLeadOwner/'.$type.'" class="form" id="assignLOwner" name="assignLOwner" method="POST">
		
				<div class="form-group col-sm-12">
					<label class="col-sm-4 text-right">Employee :</label>
						<td><!--<input type="text" name="empname" id="empname" />-->
						<input type="hidden" name="ids" id="ids" />
						 <div class="col-sm-6 input-icon right"> 
							'.form_dropdown('eid',$this->leadsmodel->getEmployee(),'',"id='eid' class='form-control'").'
				    	</div>
			     	</div>
			     <div class="form-group text-center">
					<input id="button1" type="submit" class="btn btn-primary blk_submit" name="submit" value='.$this->lang->line('submit').' > 
                    <input id="button2" type="reset" class="btn btn-default" value='.$this->lang->line('reset').' />
                </div>	
				</form>
				</div></div></div>
</div>';
	}
	
	function assignEmp($type){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		$result = $this->leadsmodel->blk_assign();
		if($result == 1){
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg',"Bulk Assign leads successfully done ");
			$this->auditlog->auditlog_info('Leads', "Leads Assign employee Details updated by ".$this->session->userdata('username'));
			redirect('ListLead/'.$type);
		}else{
			$this->session->set_flashdata('msgt', 'error');
			$this->session->set_flashdata('msg',$result);
			redirect('ListLead/'.$type);
		}
	}
function assignLeadOwner($type){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		$result = $this->leadsmodel->blk_Leadownerassign();
		if($result == 1){
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg',"Bulk Lead Owner Assign successfully done ");
			$this->auditlog->auditlog_info('Leads', "Leads Assign employee Details updated by ".$this->session->userdata('username'));
			redirect('ListLead/'.$type);
		}else{
			$this->session->set_flashdata('msgt', 'error');
			$this->session->set_flashdata('msg',$result);
			redirect('ListLead/'.$type);
		}
	}
	function get_grEmployees($id='',$val=''){
		$option='';
		$option .= (isset($val) && $val != '') ? '<option value="auto"> Auto Assign </option>' : '<option value=" "> Select Employee</option>';
		if($id != ''){
			$result = $this->leadsmodel->get_grEmployees($id);
			foreach($result as $res){
				$option.='<option value="'.$res['eid'].'">'.$res['empname'].'</option>';
			}
		}
		echo $option;
	}
	function comments_history($leadid='',$type=''){
		$type = ($type == '') ? 1 : $type;
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data['module']['title'] = "Leads Comments";
		$data['links'] = '';
		$data['nosearch']=true;
		$data['paging'] = '';
		$data['itemlist'] = $this->leadsmodel->getComments($leadid,$bid);
		$data['form']=array('adv_search'=>array());
		$this->load->view('popupListView',$data);
		
	}
	function remarks_history($leadid='',$type=''){
		$type = ($type == '') ? 1 : $type;
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data['module']['title'] = "Leads Remarks";
		$data['links'] = '';
		$data['nosearch']=true;
		$data['paging'] = '';
		$data['itemlist'] = $this->leadsmodel->getRemarks($leadid,$bid);
		$data['form']=array('adv_search'=>array());
		$this->load->view('popupListView',$data);
		
	}
	function lead_history($leadid='',$type=''){
		$type = ($type == '') ? 1 : $type;
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data['module']['title'] = "Leads History";
		$data['links'] = '';
		$data['nosearch']=true;
		$data['paging'] = '';
		$data['itemlist'] = $this->leadsmodel->leadhistorynames($leadid,$bid);
		//echo "<pre>"; print_r($data['itemlist']); exit;
		$data['form']=array('adv_search'=>array());
		$this->load->view('popupListView',$data);
		
	}
	function comments($leadid='',$type=''){
		$type = ($type == '') ? 1 : $type;
		if(!$this->feature_access())redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		$modid = '26';
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if(isset($_POST['leadid'])){
			$this->leadsmodel->addComments($_POST);
			redirect($_SERVER['HTTP_REFERER']);
		}
		$data['module']['title'] = "Leads Comments";
		$data['links'] = '';
		$data['nosearch']=true;
		$data['paging'] = '';
		$data['tab'] = true;
		$data['itemlist'] = $this->leadsmodel->getComments($leadid,$bid);
		$data['form']=array('adv_search'=>array());
	    if(!empty($data['itemlist']['rec'])){
			$data['comments'] = $data;
		}
		$itemDetail = $this->configmodel->getDetail($modid,$leadid,'',$bid);
		$formFields[] =array("label"=>"<label class='col-sm-4 text-right' for='comment'>Comments :</label>"
									,"field"=>form_textarea(array(
												'name'      => 'comments',
												'class'     => 'form-control',
												'value'		=> '',
												'id'        => 'comments'))
							);										
		$formFields[] =array("label"=>"<label class='col-sm-4 text-right' for='comment'>Lead Status :</label>"
									,"field"=>form_dropdown("lead_status",$this->leadsmodel->getLeadType($itemDetail['lead_status'],$itemDetail['type']),$itemDetail['lead_status'],"id='lead_status' class='form-control'")
							);										
		$data['form'] = array(
					'form_attr'=>array('action'=>'leads/comments/'.$leadid.'/'.$type,'name'=>'comments','id'=>'comments','enctype'=>"multipart/form-data"),
					'hidden'=>array('bid'=>$bid,'leadid'=>$leadid),
					'fields'=>$formFields,
					'parentids'=>'',
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close()
				);
		$this->load->view('popupFormView',$data);
	}
	function remarks($leadid=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		$modid = '26';
		if(isset($_POST['leadid'])){
			$this->leadsmodel->addRemarks($_POST);
			redirect($_SERVER['HTTP_REFERER']);
		}
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data['module']['title'] = "Leads Remarks";
		$data['links'] = '';
		$data['nosearch']=true;
		$data['paging'] = '';
		$data['itemlist'] = $this->leadsmodel->getRemarks($leadid,$bid);
		$data['form']=array('adv_search'=>array());
        if(!empty($data['itemlist']['rec'])){
			 $data['remarks'] = $data;
	    }
		$formFields[] =array("label"=>"<label  class='col-sm-4 text-right' for='remark'>Remark :</label>"
									,"field"=>form_textarea(array(
												'name'      => 'remark',
												'class'     => 'form-control',
												'value'		=> '',
												'id'        => 'remark'))
							);										
		$data['form'] = array(
		            'form_attr'=>array('action'=>'leads/remarks/'.$leadid,'name'=>'remarks','id'=>'remarks','enctype'=>"multipart/form-data"),
					'hidden'=>array('bid'=>$bid,'leadid'=>$leadid),
					'fields'=>$formFields,
					'parentids'=>'',
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close()
				);
		$this->load->view('popupFormView',$data);
	}
	function callHistory($leadid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data = $this->data;
		$data['module']['title'] = $this->lang->line('level_Report');
		$data['links'] = '';
		$data['nosearch']=true;
		$data['paging'] = '';
		$data['title'] = 'Calls History';
		$formFields=array();
		$data['itemlist'] = $this->leadsmodel->callHistoryList($leadid,$bid);
		$this->load->view('counter_view',$data);
	}
	function Bulk_down($type=''){
		$type = ($type == '') ? 1 : $type;
		$roleDetail = $this->roleDetail;
		$modid = ($type == 1) ? '46' : '26' ;
		$t='';	
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;			
		$fieldset = $this->configmodel->getFields($modid,$bid);
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$t .= '<input type="checkbox" checked name="lisiting['.$field['fieldname'].']" value="'.(($field['customlabel']!="")?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'" />'.(($field['customlabel']!="")?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'<br/>';
				}
			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)
					$t .= '<input type="checkbox" checked name="lisiting['.$field['fieldKey'].']" value="'.$field['fieldname'].'" />'.$field['customlabel'].'<br/>';
			}
	    }
	    $arr = array('coments','remark');
	    $t .= "<label><input type='checkbox' checked name='lisiting[".$arr[0]."]' value='".$arr[0]."' />Comments</label><br/>";
	    $t .= "<label><input type='checkbox' checked name='lisiting[".$arr[1]."]' value='".$arr[1]."' />Remarks</label>";
		echo '<div aria-hidden="false" id="modal-responsive" class="modal fade in" style="display: block;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<button aria-hidden="true" data-dismiss="modal" class="close" type="button"><i class="fa fa-times"></i></button>
			 <h4>Bulk Lead Download</h4>
			 <form action="ListLead/'.$type.'" class="form" id="blk_ddd" name="blk_ddd" method="POST">
			
					<TABLE>
						<tr>
							<th><label>Fields :</label></th>
							<td>
							<input type="hidden" name="call_ids" id="call_ids" />
								'.$t.'
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
	function leadDuplicate(){
		$number = $this->input->post('number');
		$email = $this->input->post('email');
		$result = $this->leadsmodel->leadDuplicate($number,$email);
		if($result != '0'){
			echo $result; exit;
		}else{
			echo "no";exit;
		}
	}
	function leadDupliCheck(){
		$number = $this->input->post('number');
		$email = $this->input->post('email');
		$leadid = $this->input->post('leadid');
		$result = $this->leadsmodel->leadDupliCheck($number,$email,$leadid);
		if($result != '0'){
			echo $result; exit;
		}else{
			echo "no";exit;
		}
	}
	function leadDupliHistory($leadid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data = $this->data;
		$data['module']['title'] = $this->lang->line('level_Report');
		$data['links'] = '';
		$data['nosearch']=true;
		$data['paging'] = '';
		$data['title'] = 'Leads History';
		$formFields=array();
		$data['itemlist'] = $this->leadsmodel->leadDupliHistory($leadid,$bid);
		$this->load->view('counter_view',$data);
	}
	function refreshcounter($lgid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res = $this->leadsmodel->refreshcounter($lgid,$bid);
		if($res == 1){
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', ' The Counter has reset to 0');
		}else{
			$this->session->set_flashdata('msgt', 'error');
			$this->session->set_flashdata('msg', ' Error while reset the counter');
		}
		redirect('ListempLeadGroup/'.$lgid);
	}
	function sendFields($leadid,$type){
		$type = ($type == '') ? 1 :$type;
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$mod = '26';
		if($this->input->post('sendSMs') || $this->input->post('sendEmail')){
				$case=(isset($_POST['sendSMs']))?'1':'2';
				$re=$this->leadsmodel->sendF($leadid,$bid,$mod);
				$this->session->set_flashdata('msgt', ($re!="Fail to sent")?'success':'error');
				$this->session->set_flashdata('msg',$re);
				redirect('ListLead/'.$type);
		}
		$formFields = array();
		$smsbalance=$this->configmodel->smsBalance($bid);
		$EmailDetail = $this->configmodel->getDetail('27',$bid,'',$bid);
		$itemDetail = $this->configmodel->getDetail($mod,$leadid,'',$bid);
		$empDetail=$this->configmodel->getDetail('2',$itemDetail['assignto'],'',$bid);
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));			
		$fieldset = $this->configmodel->getFields($mod,$bid);
		$empnumber=(isset($empDetail['empnumber']) && $empDetail['empnumber']!="")?$empDetail['empnumber']:'';
		$totalFelds=array();
		$formFields[] = array('label'=>'<label for="Email">Assign to :</label>',
									'field'=>form_dropdown('asto',$this->groupmodel->employee_list(),$itemDetail['assignto'],"id='asto'"));
		$formFields[] = array('label'=>'<label for="To">SMS To	 :</label>',
									'field'=>'<span id="enumber">'.$empnumber.'</span>');
		
		$formFields[] = array('label'=>'<label for="Email">Fields :</label>',
									'field'=>'');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$formFields[] = array('label'=>'<label for="'.$field['fieldname'].'"></label>',
									'field'=>'<input type="checkbox" name="formfields[]" value="'.(($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'~'.$itemDetail[$field['fieldname']].'"/><label>'.(($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'</label>');
				
				}
			}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$formFields[] = array('label'=>'<label for="custom['.$field['fieldid'].']"></label>',
									'field'=>'<input type="checkbox" name="customfields[]" value="'.$field['customlabel'].'~'.((isset($itemDetail[$field['fieldname']]) && $itemDetail[$field['fieldname']]!="")?$itemDetail[$field['fieldname']]:'').'"/>'.$field['customlabel']);
				
				}
			}
		}
		$data['module']['title'] = "Send Field Data";
		$data['links'] = '';
		$data['nosearch']=true;
		$data['paging'] = '';
		$data['form'] = array(
						'open'=>form_open_multipart('leads/sendFields/'.$leadid.'/'.$type
									,array('name'=>'sendField','class'=>'form','id'=>'sendField','method'=>'post')
									,array('smsBal'=>($smsbalance>0)?'1':'0','Emailconfig'=>(empty($itemDetail))?'0':'1')
									),
						'fields'=>$formFields,
						'parentids'=>'',
						'busid'=>'',
						'pid'=>$this->session->userdata('pid'),
						'close'=>form_close()
					);
		$this->load->view('form_view_field',$data);
	}
	function bulkStatChng($type){

		if(!$this->feature_access())redirect('Employee/access_denied');
		echo '<div class="modal-dialog modal-lg">
		         <div class="modal-content">
		         	<div class="modal-body">
							
					<button aria-hidden="true" data-dismiss="modal" class="close" type="button"><i class="fa fa-times"></i></button>
                      <h4>'.$this->lang->line('level_leadstatchng').'</h4>
		<form action="leads/statChng/'.$type.'" class="form" id="statuschng" name="statuschng" method="POST">
			
				 <div class="form-group col-sm-12">
					<label class="col-sm-4 text-right">Lead Status :</label>
						 <div class="col-sm-6 input-icon right">         
						<input type="hidden" name="ids" id="ids" />
							'.form_dropdown("lead_status",$this->leadsmodel->getLeadStatus(),'',"id='lead_status' class='form-control'").'
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
	function statChng($type){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		$result = $this->leadsmodel->blkStatChng();
		if($result == 1){
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg',"Bulk Status change of leads successfully done ");
			$this->auditlog->auditlog_info('Leads', "Bulk Status change of leads updated by ".$this->session->userdata('username'));
			redirect('ListLead/'.$type);
		}else{
			$this->session->set_flashdata('msgt', 'error');
			$this->session->set_flashdata('msg',"Error changing the status");
			redirect('ListLead/'.$type);
		}
	}
	/* Leads End */
}
