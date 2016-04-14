<?php
class outboundcalls extends controller
{
	var $data;
	var $roleDetail;
	
	function outboundcalls()
	{
		parent::controller();
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('systemmodel');
		$this->load->model('groupmodel');
		$this->load->model('outboundcallsmodel');
		$this->load->model('auditlog');
		$this->load->model('msgmodel');
		$this->load->library('zip');
		$this->roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
	}
	
	function feature_access()
	{
		$show=0;
		$checklist=$this->systemmodel->checked_featuremanage();
		if(in_array(16,$checklist)){
			$show=1;
		}
		return $show;
	}
	
	function index()
	{
		redirect('outboundcalls/obc_grp_list');
	}
	
	function obc_grp_add($id=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['44']['opt_view']) redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('groupname', 'Group Name', 'required');
			$this->form_validation->set_rules('eid', 'Group Owner', 'required');
			$this->form_validation->set_rules('grule', 'Group Rule', 'required');
			if (!$this->form_validation->run() == FALSE){
				$res = $this->outboundcallsmodel->addobcGroup();
				if(is_numeric($res)){
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', "Outbound Call Group added Successfully");
					redirect('outboundcalls/addgrpemp/'.$res);
				}elseif($res == 'FALSE'){
					$this->session->set_flashdata('msgt', 'error');
					$this->session->set_flashdata('msg', "Limit Reached, Can not create more Outbound Call Groups. Please contact your Account Manager.");
					redirect('outboundcalls/obc_grp_list');
				}else{
					$this->session->set_flashdata('msgt', 'error');
					$this->session->set_flashdata('msg', "Outbound Call group already existed");
					redirect('outboundcalls/obc_grp_add');
				}
			}
		}
		$this->sysconfmodel->data['html']['title'] .= " | Add Outboundcalls Groups";
		$data['module']['title'] = "Add Outboundcalls Groups";
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$fieldset = $this->configmodel->getFields('44',$bid);
		$itemDetail = $this->configmodel->getDetail('44',$id,'',$bid);
		$group_rule = array(""=>"Select","1"=>"Sequential","2"=>"Weighted");
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='filename'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked && !in_array($field['fieldname'] ,array('lastmodified','status','createdon','enteredby'))) 
					$formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' <img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>',
									'field'=>($field['fieldname'] == 'eid') ? form_dropdown('eid',$this->outboundcallsmodel->getEmployees(),isset($itemDetail['eid']) ? $itemDetail['eid']:'','id="eid" ')
											  :(($field['fieldname'] == 'group_rule') ?form_dropdown('grule',$group_rule,isset($itemDetail['group_rule']) ? $itemDetail['group_rule'] : '','id="grule" ')
											  :form_input(array(
												'name'      => $field['fieldname'],
												'id'        => $field['fieldname'],
												'value'		=> (isset($itemDetail[$field['fieldname']])) ? $itemDetail[$field['fieldname']] : '',
												'class'		=>''))
												));
									
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked)$formFields[] = array(
						'label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
						'field'=>$this->configmodel->createField($field,'','search'));
				}
		}
		$data['form'] = array(
		            'form_attr'=>array('action'=>'outboundcalls/obc_grp_add','name'=>'addobcgrp'),
					//~ 'open'=>form_open_multipart('outboundcalls/obc_grp_add'
								//~ ,array('name'=>'addobcgrp','class'=>'form','id'=>'addobcgrp','method'=>'post')
								//~ ,array('bid'=>$bid,'id'=>$id)
							//~ ),
					'fields'=>$formFields,'parentids'=>$parentbids,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	
	function addgrpemp($ogid='',$eid=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if($this->input->post('update_system')){
			if($_POST['empid']!=''){
				$res=$this->outboundcallsmodel->editemp_group($ogid);
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', 'Updated Successfully');
				redirect('outboundcalls/obc_grpemp_list/'.$ogid);
			}
			else
			{
				$res=$this->outboundcallsmodel->addobc_grpemp($ogid);
				echo $res;		
				if($res==0){
						$this->session->set_flashdata('msgt', 'error');
						$this->session->set_flashdata('msg', $this->lang->line('error_alreadyexists'));
						redirect('outboundcalls/addgrpemp/'.$ogid);
				}elseif($res == '2'){
						$this->session->set_flashdata('msgt', 'error');
						$this->session->set_flashdata('msg', "Limit Reached, Can not assign Employees. Please contact your Account Manager.");
						redirect('outboundcalls/obc_grpemp_list/'.$ogid);
				}else{
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', $this->lang->line('error_addempsucss'));
					redirect('outboundcalls/obc_grpemp_list/'.$ogid);
				}
			}
		}
		$roleDetail = $this->roleDetail;
		$gpDetail = $this->configmodel->getDetail('44',$ogid,'',$bid);
		if(!$roleDetail['modules']['44']['opt_add']) redirect('Employee/access_denied');
		if(count($_POST)>0){
			redirect($_SERVER['HTTP_REFERER']);
		}
		$gpEmp = ($eid!='')?$this->outboundcallsmodel->getobcGroupEmpDetail($ogid,$eid):array();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_addemptogroup');
		$data['module']['title'] = $this->lang->line('label_addemptogroup');
		$formFields = array();
		$data['form'] = array(
					'open'=>form_open_multipart(current_url()
								,array('name'=>'addobcgrpemp','class'=>'form','id'=>'addobcgrpemp','method'=>'post')
								,array('bid'=>$bid
								  ,'gid'=>$ogid
								  ,'empid'=>$eid
								  )),
					'fields'=>$formFields,
					'close'=>form_close(),
					'gdetail'=>$gpDetail,
					'emplist'=>$this->systemmodel->get_emp_list(),
					'empexists'=>$this->outboundcallsmodel->obcgrpemp_existed($ogid),
					'gpEmp'=>$gpEmp,
					'empid'=>$eid
				);
		$this->sysconfmodel->viewLayout('form_view_obcgrpemp',$data);
	}
	
	function obc_grpemp_list($ogid){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = "Outbound Calls Group Employee List";
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$gpDetail = $this->configmodel->getDetail('44',$ogid,'',$bid);
		$limit = '30';
		$header=array($this->lang->line('level_empname')
						,$this->lang->line('level_group'));
		if($gpDetail['group_rule']=='2') $header[]=$this->lang->line('level_empweight');
		 array_push($header,$this->lang->line('level_Action'));
		$data['itemlist']['header'] = $header;
		$emp_list=$this->outboundcallsmodel->obcgrpemplist($ogid,$ofset,$limit);
		$roleDetail = $this->roleDetail;
		$opt_add=$roleDetail['modules']['44']['opt_add']; 
		$opt_delete=$roleDetail['modules']['44']['opt_delete']; 
		
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$opt='';
			$s=($item['status']=="1")?'<a href="'.site_url('outboundcalls/obcgrpemp_disable/'.$item['eid']."/".$item['gid']).'"><span class="fa fa-unlock" title="Disable"></span></a>':'<a href="'.site_url('outboundcalls/obcgrpemp_disable/'.$item['eid']."/".$item['gid']).'"><span class="fa fa-lock" title="Enable"></span></a>';
			$opt.=($opt_add && $gpDetail['group_rule']=='2')?'<a href="'.site_url('outboundcalls/addgrpemp/'.$item['gid']."/".$item['eid']).'">
						<span title="Edit" class="fa fa-edit"></span>
				  </a>':'';
			$opt.=($opt_delete)?'&nbsp;<a href="'.site_url('outboundcalls/delete_grp_emp/'.$item['eid']."/".$item['gid']).'">
					<span title="Delete Employee from Outboundcalls Group" class="glyphicon glyphicon-trash"></span>
				  </a>':'';
			$opt.=($opt_add)?$s:'';	 
			 		$r = array(
				'<a href="Employee/activerecords/'.$item['eid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.(($item['status']=="1") ? $item['empname'] : '<font color=red>'.$item['empname'].'</font>').'</a>'
				,(($item['status']=="1") ? $item['groupname'] : '<font color=red>'.$item['groupname'].'</font>'));
				if($gpDetail['group_rule']=='2') $r[]=($item['status']=="1") ? $item['weight'] : '<font color=red>'.$item['weight'].'</font>';
			$r = array_merge($r,array($opt));
			$rec[] = $r;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('outboundcalls/obc_grpemp_list/'.$this->uri->segment(3))
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_groupemp');
		$links = array();
		$links[] = '<li><a href="outboundcalls/addgrpemp/'.$this->uri->segment(3).'"><span class="glyphicon glyphicon-plus-sign" title="Add Employee to Outboundcall Group">Add Group</span></a></li>';
		$formFields = array();
		$cf=array('label'=>'<label for="groupname">Employee Name : </label>',
				  'field'=>form_input(array(
									  'name'        => 'empname',
									  'class'        => 'form-control',
										'id'          => 'empname',
										'value'       => $this->session->userdata('empname'))));
						array_push($formFields,$cf);
		$data['links'] = $links;
		$data['form'] = array(
			'open'=>form_open_multipart('outboundcalls/obc_grpemp_list/'.$ogid,array('name'=>'listgrpemp','class'=>'form','id'=>'listgrpemp','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	
	function obc_grp_list($type='')
	{
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;	
		if(!$roleDetail['modules']['44']['opt_view']) redirect('Employee/access_denied');
		if($this->input->post('submit')){	
			if($this->session->userdata('search')!=""){
				$s=$this->session->unset_userdata('search');
			}
		}
		$type = ($type == '') ? 'act' : $type;
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '30';
		$data['itemlist'] = $this->outboundcallsmodel->list_obc_grps($bid,$ofset,$limit,$type);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('outboundcalls/list_obc_grps/'.$type.'/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
						,'uri_segment'=>4				
				));
		$data['module']['title'] = "Outbound Calls Groups [".$data['itemlist']['count']."]";
		$links= array();	
		$links[] = '<li><a href="outboundcalls/obc_grp_add/"><span class="glyphicon glyphicon-plus-sign" title="Add Group">$nbsp; Add Group</span></a></li>';
		$fieldset = $this->configmodel->getFields('44',$bid);
		$itemDetail = $this->configmodel->getDetail('44','','',$bid);
		$group_rule = array(""=>"Select","1"=>"Sequential","2"=>"Weighted");
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],array('enteredby'))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>
										($field['fieldname']=='eid')?form_dropdown('eid',$this->outboundcallsmodel->employee_list(),'',"class='form-control'")
											:(($field['fieldname'] == 'group_rule') ?form_dropdown('group_rule',$group_rule,'','id="group_rule" class="form-control"')
											:form_input(array(
												'name'      => $field['fieldname'],
												'id'        => $field['fieldname'],
												'class'		=>($field['fieldname']=="createdon" || $field['fieldname']=="lastmodified")?'datepicker_leads form-control':'form-control'
												))
									));
							}		
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { $formFields[] = array(
								'label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>form_input(array(
													'name'      => 'custom['.$field['fieldid'].']'
													)));
								$advsearch['custom['.$field['fieldid'].']']=$field['customlabel'];							
							}						
			}
		}
		$data['links'] = $links;
		$data['form'] = array(
					'open'=>form_open_multipart(site_url('outboundcalls/obc_grp_list'),array('name'=>'listobcgrp','class'=>'form','id'=>'manageemp','method'=>'post')),
					'form_field'=>$formFields,
					'parentids'=>$parentbids,
					'adv_search'=>array(),
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close(),
					'title'=>$this->lang->line('level_search')
					);
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | Outbound Call Module ";
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	
	function delete_obcgrp($id='',$type1=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['44']['opt_delete']) redirect('Employee/access_denied');
		$type = ($type1 == '') ?'2' : $type1;
		$this->outboundcallsmodel->del_obc_grp($id,$bid,$type);
		if($type1 == '')
			return 1;
		else
			redirect('outboundcalls/obc_grp_list/del');
	}
	
	function delete_grp_emp($eid,$ogid){
		$res=$this->outboundcallsmodel->del_obcgrpemp($eid,$ogid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg','Outboundcalls Group Employee Deleted Succesfully');
		redirect('outboundcalls/obc_grpemp_list/'.$ogid);
	}
	
	function obcgrpemp_disable($eid,$ogid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->outboundcallsmodel->obcgrpemp_dis($eid,$ogid,$bid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', ($res)?'Outboundcalls Group Employee Enabled Succesfully':'Outboundcalls Group  Employee Disabled Succesfully');
		redirect('outboundcalls/obc_grpemp_list/'.$ogid);
	}
	
	function addContacts($ogid=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		if(!$this->feature_access(16))redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['45']['opt_add']) redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			if($_FILES['filename']['size']>0){
				$this->form_validation->set_rules('filename', 'Filename', 'callback_file_extensions');
				if (!$this->form_validation->run() == FALSE){
					$res=$this->outboundcallsmodel->Addcontacts();
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', "Contacts added Successfully");
					redirect('outboundcalls/obc_grp_list'.$ogid);
				}
			}else{
				$this->form_validation->set_rules('name', 'Name', 'required');
				$this->form_validation->set_rules('contact_no', 'Number', 'required|numeric|callback_check_uniqNumber');
				$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
				
				if (!$this->form_validation->run() == FALSE){
					$res=$this->outboundcallsmodel->editcontact();
					if($res!=2){
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', "Contact added Successfully");
					redirect('outboundcalls/obc_grp_list/'.$ogid);
					}
					else
					{
						$this->session->set_flashdata('msgt', 'error');
						$this->session->set_flashdata('msg', "Mobile No already exit");
						redirect('outboundcalls/obc_grp_list/'.$ogid);
					}
				}
			}
		}
		$roleDetail = $this->roleDetail;
		$this->sysconfmodel->data['html']['title'] .= " | Add Contacts";
		$data['module']['title'] = "Add Contacts";
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$formFields[] = array('label'=>'<label class="col-sm-4 control-label" for="filename">'.$this->lang->line('label_autodiallerfile').'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="Upload csv or txt file with the list of numbers to be called and tracked. The format of the file should be  Name, Email,Number,">&nbsp;&nbsp;: </label>',
							  'field'=>form_input(array(
											'name'      => 'filename',
											'id'        => 'filename',
											'type'	  => 'file')
											)
								);
		$formFields[] = array('label'=>'',
							  'field'=>'[or]'
								);
							
				$fieldset = $this->configmodel->getFields('45',$bid);
				foreach($fieldset as $field){
					$checked = false;
					if($field['type']=='s' && $field['show'] && $field['fieldname']!='filename'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked && !in_array($field['fieldname'] ,array('greetings','bday','hdaytext','hdayaudio','operator','prinumber','record','remark','noext'))) 
					$formFields[] = array(
									'label'=>'<label class="col-sm-4 control-label" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>(($field['fieldname']=='remarks')?form_textarea(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname'],
											'class'		=>'')):form_input(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname'],
											'class'		=>'form-control'))
											));
						}elseif($field['type']=='c' && $field['show']){
						foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
						if($checked)$formFields[] = array(
								'label'=>'<label class="col-sm-4 control-label" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,'','search'));
						}
					
				}				
		$formFields[] = array('label'=>'',
							  'field'=>form_input(array(
											'name'      => 'ogid',
											'id'        => 'ogid',
											'class'     => 'form-control',
											'type'		=> 'hidden',
											'value'     => $ogid,
											))
											);
		$data['form'] = array(
		           'form_attr'=>array('action'=>'outboundcalls/addContacts','name'=>'conadd','id'=>'conadd','enctype'=>"multipart/form-data"),
					//~ 'open'=>form_open_multipart('outboundcalls/addContacts'
								//~ ,array('name'=>'conadd','class'=>'form','id'=>'conadd','method'=>'post')
								//~ ,array('bid'=>$bid)
							//~ ),
					'fields'=>$formFields,'parentids'=>$parentbids,
					'hidden'=> '',
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	
	function listcontacts()
	{
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['45']['opt_view']) redirect('Employee/access_denied');
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$fieldset = $this->configmodel->getFields('45',$bid);
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
					array_push($keys,'custom['.$field['fieldid'].']');
					array_push($header,$field['customlabel']);
				}
			}
		}	
		$opt_add 	= $roleDetail['modules']['45']['opt_add'];
		$opt_view 	= $roleDetail['modules']['45']['opt_view'];
		$opt_delete = $roleDetail['modules']['45']['opt_delete'];
		if($opt_add || $opt_view || $opt_delete){
			array_push($header,$this->lang->line('level_Action'));
			array_push($keys,"Action");			
		}
		
		$data['itemlist']['header'] = $header;
		$emp_list=$this->outboundcallsmodel->listcontacts($ofset,$limit);
		if($this->input->post('download')){
			$filename = $this->reportmodel->Contacts_Csv($bid);
			$dlink =  "<a href='".$this->config->item('reports_path').$filename.".zip"."' target='_blank' style='color:#fff;'>Start Download</a>  ";
		}else{
			$dlink = "";
		}
		$links =array();
		$links[]=($roleDetail['modules']['45']['opt_download']!=0)?'<li><a href="Report/ContactsCsv/"  class="blkSMs" data-toggle="modal" data-target="#modal-blksms"><span title="Download" class="glyphicon glyphicon-download-alt">$nbsp;Download All</span></a></li>':'';
		$data['module']['title'] ="List Contacts". "[".$emp_list['count']."]";
		$rec = array();
		if(count($emp_list['data'])>0)
		$i = $ofset+1;
		foreach ($emp_list['data'] as $item){
			$arrs = array($i);
			$r = $this->configmodel->getDetail('45',$item['contact_no'],'',$bid);
			
			$v='<input type="checkbox" class="blk_check" name="blk[]" value="'.$item['contact_no'].'"/>';
			array_push($arrs,$v);
			foreach($keys as $k){
				$v='';
				if($k=="Action"){
					$v.=($opt_add)? '<a href="outboundcalls/Editcontact/'.$item['contact_no'].'"><span title="Edit" class="fa fa-edit"></span></a>':'';
					$v.=($opt_delete)?'<a href="outboundcalls/deletContact/'.$item['conid'].'"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
					
				}else{
						$v = isset($r[$k])?$r[$k]:"";
					}
				array_push($arrs,$v);
			}
			$i++;
			array_push($rec,$arrs);
		}

		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Report/listcontacts/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		//$data['addlinks']="group/add_group";		
		$data['paging'] = $this->pagination->create_links();
		$formFields = array();
		$advsearch = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='filename'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked && !in_array($field['fieldname'] ,array('greetings','bday','hdaytext','hdayaudio','operator','prinumber','record','remark','noext'))){ 
					$formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>form_input(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname'],
											'class'		=>($field['fieldname']=="endtime" ||$field['fieldname']=="datetime")?'datepicker':''))
											);
							$advsearch[$field['fieldname']]=(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']);				
						}					
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { $formFields[] = array(
								'label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>form_input(array(
													'name'      => 'custom['.$field['fieldid'].']'
													)));
								$advsearch['custom['.$field['fieldid'].']']=$field['customlabel'];					
							 }						
			}
		}
		 $save_cnt=save_search_count($bid,'45',$this->session->userdata('eid'));	
		$data['downlink'] = $dlink;	
		$data['links'] = $links;		
		$data['form'] = array(
			'open'=>form_open_multipart('outboundcalls/listcontacts/',array('name'=>'managecnt','class'=>'form','id'=>'managecnt','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'save_search'=>$save_cnt,
			'parentids'=>$parentbids,
			'busid'=>$bid,
			'pid'=>$this->session->userdata('pid'),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
			//print_r($data);exit;
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	
	function deletContact($conid){
		$res=$this->outboundcallsmodel->deleteContact($conid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', "Contacts Deleted Successfully");
		redirect('outboundcalls/listcontacts');
	}
	
	function Editcontact($conid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if($this->input->post('update_system')){
			$res=$this->outboundcallsmodel->editcontact($conid);
			
			if($res!=""){
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', "Contacts updated Successfully");
				redirect('outboundcalls/obc_grp_list');
			}else{
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', "Error While Updating contact");
				redirect('outboundcalls/obc_grp_list');
			}
		}
		$this->sysconfmodel->data['html']['title'] .= " | Edit Contact";
		$data['module']['title'] = "Edit Contact";
		$fieldset = $this->configmodel->getFields('45',$bid);						
		$itemDetail = $this->configmodel->getDetail('45',$conid,'',$bid);
		//print_r($itemDetail);exit;
		$formFields = array();
		$cf=array('label'=>'<label>Name :</label>',
				  'field'=>form_input(array(
									  'name'        => 'name',
										'id'          => 'name',
										'value'       => (isset($itemDetail['name']))?$itemDetail['name']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'<label>Email :</label>',
				  'field'=>form_input(array(
									  'name'        => 'email',
										'id'          => 'email',
										'value'       => (isset($itemDetail['email']))?$itemDetail['email']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'<label>Number :</label>',
				  'field'=>(isset($itemDetail['contact_no']))?$itemDetail['contact_no']:'');
							array_push($formFields,$cf);
		
			
			foreach($fieldset as $field){
					$checked = false;
					if($field['type']=='c' && $field['show']){
						foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
						if($checked)$formFields[] = array(
								'label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,isset($itemDetail['custom['.$field['fieldid'].']'])?
											$itemDetail['custom['.$field['fieldid'].']']:''));
						}
					
				}						
							
			
			$data['form'] = array(
			       'form_attr'=>array('action'=>'outboundcalls/Editcontact/'.$conid,'name'=>'editcontact'),
					//~ 'open'=>form_open_multipart('outboundcalls/Editcontact/'.$conid,array('name'=>'editcontact','class'=>'form','id'=>'editcontact','method'=>'post')),
					'fields'=>$formFields,
					'close'=>form_close(),
					
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	
	function addgrpcnt($ogid=''){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if($this->input->post('update_system')){
			$res=$this->outboundcallsmodel->addobc_grpcnt($ogid);
			if($res==0){
					$this->session->set_flashdata('msgt', 'error');
					$this->session->set_flashdata('msg', $this->lang->line('Contact already exists'));
					redirect('outboundcalls/addgrpcnt/'.$ogid);
			}elseif($res == '2'){
					$this->session->set_flashdata('msgt', 'error');
					$this->session->set_flashdata('msg', "Limit Reached, Can not assign Contacts. Please contact your Account Manager.");
					redirect('outboundcalls/obc_grpcnt_list/'.$ogid);
			}else{
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', 'Contact added to group successfully');
				redirect('outboundcalls/obc_grpcnt_list/'.$ogid);
			}
		}
		$roleDetail = $this->roleDetail;
		$gpDetail = $this->configmodel->getDetail('45','','',$bid);
		if(!$roleDetail['modules']['45']['opt_add']) redirect('Employee/access_denied');
		if(count($_POST)>0){
			redirect($_SERVER['HTTP_REFERER']);
		}
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_addcnttogroup');
		$data['module']['title'] = $this->lang->line('label_addcnttogroup');
		$formFields = array();
		$data['form'] = array(
					'open'=>form_open_multipart(current_url()
								,array('name'=>'addobcgrpcon','class'=>'form','id'=>'addobcgrpcon','method'=>'post')
								,array('bid'=>$bid
								  ,'gid'=>$ogid
								  )),
					'fields'=>$formFields,
					'close'=>form_close(),
					'gdetail'=>$gpDetail,
					'cntlist'=>$this->outboundcallsmodel->get_cnt_list(),
					'cntexists'=>$this->outboundcallsmodel->obcgrpcnt_existed($ogid)
				);
		$this->sysconfmodel->viewLayout('form_view_obccnt',$data);
	}
	
	function obc_grpcnt_list($ogid){
		if(!$this->feature_access())redirect('Employee/access_denied');
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = "Outbound Calls Group Contact List";
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$gpDetail = $this->configmodel->getDetail('44',$ogid,'',$bid);
		$limit = '30';
		$header = array('Contact Name','Email','Contact Number'
						,$this->lang->line('level_group')
						,$this->lang->line('level_Action'));
		$data['itemlist']['header'] = $header;
		$cnt_list=$this->outboundcallsmodel->obcgrpcntlist($ogid,$ofset,$limit);
		$roleDetail = $this->roleDetail;
		$opt_add=$roleDetail['modules']['45']['opt_add']; 
		$opt_delete=$roleDetail['modules']['45']['opt_delete']; 
		$rec = array();
		if(count($cnt_list['data'])>0)
		foreach ($cnt_list['data'] as $item){
			$opt='';
			$s=($item['status']=="1")?'<a href="'.site_url('outboundcalls/obcgrpcnt_disable/'.$item['conid']."/".$item['gid']).'"><span class="fa fa-unlock" title="Disable"></span></a>':'<a href="'.site_url('outboundcalls/obcgrpcnt_disable/'.$item['conid']."/".$item['gid']).'"><span class="fa fa-lock" title="Enable"></span></a>';
			$opt.=($opt_add)? '<a href="'.site_url('outboundcalls/Editcontact/'.$item['contact_no']).'"><span title="Edit" class="fa fa-edit"></span></a>':'';
			$opt.=($opt_delete)?'<a href="'.site_url('outboundcalls/deletContact/'.$item['conid']).'"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
			$opt.=($opt_add)?$s:'';	 
			 		$r = array(
				(($item['status']=="1") ? $item['name'] : '<font color=red>'.$item['name'].'</font>')
				,(($item['status']=="1") ? $item['email'] : '<font color=red>'.$item['email'].'</font>')
				,(($item['status']=="1") ? $item['contact_no'] : '<font color=red>'.$item['contact_no'].'</font>')
				,(($item['status']=="1") ? $item['groupname'] : '<font color=red>'.$item['groupname'].'</font>'));
			
			$r = array_merge($r,array($opt));
			$rec[] = $r;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $cnt_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('outboundcalls/obc_grpcnt_list/'.$this->uri->segment(3))
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_groupemp');
		$links =array();
		$links[]='<li><a href="outboundcalls/addContacts/'.$this->uri->segment(3).'"><span class="glyphicon glyphicon-plus-sign" title="Add Contact to Outboundcall Group">$nbsp;Add Contact</span></a></li>';
		$formFields = array();
		$cf=array('label'=>'<label for="groupname">Contact Name : </label>',
				  'field'=>form_input(array(
									  'name'        => 'name',
										'id'          => 'name',
										'value'       => $this->session->userdata('name'))));
						array_push($formFields,$cf);
		$cf=array('label'=>'<label for="groupname">Email : </label>',
				  'field'=>form_input(array(
									  'name'        => 'email',
										'id'          => 'email',
										'value'       => $this->session->userdata('email'))));
						array_push($formFields,$cf);
		$cf=array('label'=>'<label for="groupname">Contact Number: </label>',
				  'field'=>form_input(array(
									  'name'        => 'contact_no',
										'id'          => 'contact_no',
										'value'       => $this->session->userdata('contact_no'))));
						array_push($formFields,$cf);
		$data['links'] = $links;	
		$data['form'] = array(
			'open'=>form_open_multipart('outboundcalls/obc_grpcnt_list/'.$ogid,array('name'=>'listgrpcnt','class'=>'form','id'=>'listgrpcnt','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	
	function obcgrpcnt_disable($conid,$ogid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->outboundcallsmodel->obcgrpcnt_dis($conid,$ogid,$bid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', ($res)?'Outboundcalls Group Contact Enabled Succesfully':'Outboundcalls Group Contact Disabled Succesfully');
		redirect('outboundcalls/obc_grpcnt_list/'.$ogid);
	}
	
	function delete_grp_cnt($conid,$ogid){
		$res=$this->outboundcallsmodel->del_obcgrpcnt($conid,$ogid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg','Outboundcalls Group Contact Deleted Succesfully');
		redirect('outboundcalls/obc_grpcnt_list/'.$ogid);
	}
}
?>
