<?php
class Activity extends Controller {
	var $data,$roleDetail;
	function Activity(){	
		parent::controller();
		//if(!$this->session->userdata('logged_in'))redirect('/user');
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('systemmodel');
		$this->load->model('groupmodel');
		$this->load->model('configmodel');
		$this->load->model('activitymodel');
		$this->load->model('empmodel');
		$this->roleDetail = $this->sysconfmodel->data['roleDetail'];
		
	}
	function index(){
		echo "Activity Controller";
	}
	function addactivity($id=''){
		if(!$this->feature_access(12))redirect('Employee/access_denied');
		
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('groupname', 'Group Name', 'required|min_length[4]|max_length[50]|alpha_numeric');
			$this->form_validation->set_rules('number', 'Landing Number', 'required|is_natural_no_zero');
			if(!$this->form_validation->run() == FALSE)
			{
				if($id!=""){
					$res=$this->activitymodel->create_activitygroup($id);
					if($res){	
						$this->session->set_flashdata('msgt', 'success');
						$this->session->set_flashdata('msg', "Updated Successfully");
						redirect('activity/listactivity');
					}
				}else{
						$res=$this->activitymodel->create_activitygroup();
						if($res){	
							$this->session->set_flashdata('msgt', 'success');
							$this->session->set_flashdata('msg', "Inserted Successfully");
							//redirect('group/add_group');
							redirect('activity/listactivity');
						}
				}
			}
		}
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_activityfrm');
		$data['module']['title'] = $this->lang->line('label_activityfrm');
		$fieldset = $this->configmodel->getFields('31',$bid,'1');
		$formFields = array();
		$itemDetail = ($id!='') ? $this->configmodel->getDetail('31',$id,'',$bid) : array();
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['31']['opt_add']) redirect('Employee/access_denied');
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					$enabled = "";
					if($checked){
						$cf = array('label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>',
									'field'=>($field['fieldname']=="groupname")?
												form_input(array(
														  'name'      => $field['fieldname']
														  ,'id'        => $field['fieldname']
														  ,'class'        => 'required'
														  ,'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname']))
													  )
									
												:
												form_dropdown('number',$this->systemmodel->getPriList(isset($itemDetail['number'])?$itemDetail['number']:'','4'),(isset($itemDetail['number']) && $itemDetail['number']>20000)?$itemDetail['number']:(($id!="")?'0':''),"id='number' class='required '")
												
												);
							array_push($formFields,$cf);		
						
					}
				
				
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$cf = array('label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,
											isset($itemDetail['custom['.$field['fieldid'].']'])?
											$itemDetail['custom['.$field['fieldid'].']']:'')
						);
					array_push($formFields,$cf);
				
				}
			}
		}
		$formFields1=array();
		$fromAction = 'activity/addactivity/'.$id;
		$data['form'] = array(
		            'form_attr'=>array('action'=>'$fromAction','name'=>'addActivityG'),
					//~ 'open'=>form_open_multipart($fromAction
											//~ ,array('name'=>'form','class'=>'form','id'=>'addActivityG','method'=>'post')
											//~ ,array('gid'=>$id,
												//~ 'oldprinumber'=>isset($itemDetail['number'])?$itemDetail['number']:""
												//~ 
											//~ )
											//~ ),
					'fields'=>$formFields,
					'fields1'=>$formFields1,
					'parentids'=>($id=='')?$parentbids:'',
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close()
				);
				$this->sysconfmodel->viewLayout('form_view',$data);
		
	}
	function listactivity(){
		if(!$this->feature_access(12))redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['31']['opt_view']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '20';
		$data['itemlist'] = $this->activitymodel->activityList($bid,$ofset,$limit,1);
		
		$data['module']['title'] = $this->lang->line('label_listactivity') . "[".$data['itemlist']['count']."]";
		$this->pagination->initialize(array(
						 'base_url'=>site_url('activity/listactivity/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_groupmanage');
		$this->sysconfmodel->data['links'] = '<a href="activity/addactivity"><span class="glyphicon glyphicon-plus-sign" title="Add Group"></span></a>';
		$fieldset = $this->configmodel->getFields('31',$bid);
		
		$formFields = array();
		$formFields[] = array('label'=>'<label for="filename">Group Name: </label>',
						   'field'=>form_input(array(
										'name'      => 'gname',
										'id'        => 'gname',
										'class'     => '',
										'type'	  => 'text')
										)
							);
		
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		
		$data['form'] = array(
			'open'=>form_open_multipart('activity/listactivity/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'parentids'=>$parentbids,
			'busid'=>$bid,
			'pid'=>$this->session->userdata('pid'),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
			
		$this->sysconfmodel->viewLayout('list_view',$data);

	}
	function feature_access($access){
		$show=0;
		$checklist=$this->systemmodel->checked_featuremanage();
		if(in_array($access,$checklist)){
			$show=1;
			}
		return $show;
	}
	function custom_activity($id){
		
		$data['module']['title'] = "Custom Fields";
		$formFields = array();$formFields1 = array();
		$cf=array('label'=>"<label><div id='FrmError'></div></label>",
				  'field'=>'');
			array_push($formFields,$cf);
		$data['fieldtype']=array(""=>"Select Field Type"
								,"number"=>"Numeric"
								,"alphanumeric"=>"Alpha Numeric"
								,"alpha"=>"Alpha Only");
						
		//~ $cf=array('label'=>form_input(array(
									  //~ 'name'        => 'cust[0][lname]',
										//~ 'placeholder'       => 'Label Name',
										//~ 'value'       => '',
										//~ 'class'		=>'required',
										//~ 'readonly'=>true
				  			//~ ) ,'',"style='width:100px;'"
				  		//~ ).'&nbsp;&nbsp;'
				  		//~ ,
				  //~ 'field'=>form_dropdown('cust[0][ftype]',$fieldtype,'',' class="required",style="width:150px;"'
				  		//~ )."&nbsp;&nbsp;".
				  		//~ form_checkbox(array(
									  //~ 'name'        => 'emp[0][isreq]',
										//~ 'placeholder'       => 'Is Required',
							//~ ) ,'',"style='width:50px;'"
				  		//~ )." Is Required &nbsp;&nbsp;"
				  		//~ 
				  //~ );
		//~ array_push($formFields,$cf);
		$data['form'] = array(
				'open'=>form_open_multipart('activity/addCustom/'.$id,array('name'=>'configure','class'=>'form','id'=>'configure','method'=>'post'),array('gn'=>'','pn'=>'','eid'=>'')),
				'fields'=>$formFields,
				'fields1'=>$formFields1,
				'close'=>form_close()
			);
		$this->load->view('activityview',$data);
	}
	function addCustom($id,$actid=''){
		if($this->input->post('update_system')){
			$res=$this->activitymodel->AddCustom($id,$actid);
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', 'Custom FieldS Added Successfully for the Activity Group');
			if($actid!=''){
				redirect('activity/list_Activity/'.$id);
			}else{
				redirect('activity/listactivity/');
			}
		}
	}
	function actreport(){
		if(!$this->feature_access(12))redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['31']['opt_view']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '20';
		$data['itemlist'] = $this->activitymodel->activityList($bid,$ofset,$limit,0);
		
		$data['module']['title'] = 'Report '.$this->lang->line('label_listactivity') . "[".$data['itemlist']['count']."]";
		$this->pagination->initialize(array(
						 'base_url'=>site_url('activity/listactivity/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_groupmanage');
		$this->sysconfmodel->data['links'] = '';
		
		
		$formFields = array();
		$formFields[] = array(
					'label'=>'<label for="eid">Group Name : </label>',
					'field'=>form_input(array(
										'name'      => 'gname',
										'id'        => 'gname',
										'class'     => '',
										'type'	  => 'text')
										));
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		
		$data['form'] = array(
			'open'=>form_open_multipart('activity/actreport/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'parentids'=>$parentbids,
			'busid'=>$bid,
			'pid'=>$this->session->userdata('pid'),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
			
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function reportList($agid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;	
		if(!$this->feature_access(12))redirect('Employee/access_denied');
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		
		$header = array('#','Activity','keyword','Action');
		$data['itemlist']['header'] = $header;
		//$data['module']['title'] =$heading;
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '20';
		$emp_list = $this->activitymodel->activity_createdList($agid,$bid,$ofset,$limit);
		
		$data['module']['title'] = $this->lang->line('label_listactivity') . "[".$emp_list['count']."]";
		$rec = array();
		if(count($emp_list['count'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			$rec[] = array($i,$item['activity_name'],$item['keyword'],
						'<a href="'.base_url().'activity/act_Report/'.$agid.'/'.$item['acid'].'"><span title="List Option" class="fa fa-list-ul"></span></a>'
						
					);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('activity/reportList/'.$agid)
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		$data['paging'] = $this->pagination->create_links();
		$data['links']='';
		$formFields = array();
		$formFields[] = array(
					'label'=>'<label for="eid">Activity Name : </label>',
					'field'=>form_input(array(
										'name'      => 'acname',
										'id'        => 'acname',
										'class'     => '',
										'type'	  => 'text')
										));
		
		
		$data['form'] = array(
							'open'=>form_open_multipart('activity/reportList/'.$agid,array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->sysconfmodel->viewLayout('list_view',$data);
		
		
		
	}
	function activity_member($actid,$agid,$eid=''){
		if(!$this->feature_access(1))redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if($this->input->post('update_system')){
			$res=$this->activitymodel->Addactivity_member($actid,$agid);
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', 'Employees Added Successfully');
			redirect('activity/list_Activity/'.$actid);
			
		}
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('label_groupemp');
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '30';
		$header = array($this->lang->line('level_empname')
						,$this->lang->line('level_group')
						,$this->lang->line('level_Action')
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->activitymodel->activityemplist($actid,$agid);
		$roleDetail = $this->roleDetail;
		$opt_add=$roleDetail['modules']['31']['opt_add']; 
		$opt_delete=$roleDetail['modules']['31']['opt_delete']; 
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$opt='';
			$opt.=($opt_delete)?'&nbsp;<a href="'.base_url().'activity/deleteactEmp/'.$item['eid']."/".$item['agid']."/".$item['actid'].'" class="AcEmp">
						<span title="Delete Employee from Group" class="glyphicon glyphicon-trash"></span>
				  </a>':'';
			$rec[] = array(
				$item['empname']
				,$item['groupname']
				,$opt
			);
		}

		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		
		
		$this->pagination->initialize(array(
						 'base_url'=>site_url('group/group_emp_list/'.$this->uri->segment(3))
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_groupemp');
		$data['links']='';
		$data['nosearch']=1;
		$formFields = array();
		$data['form'] = array(
			'open'=>form_open_multipart('group/manage_group/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->load->view('list_view',$data);
		
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['31']['opt_add']) redirect('Employee/access_denied');
		//print_r($gpDetail);
		if(count($_POST)>0){
			//$this->groupmodel->addemp_group();
			redirect($_SERVER['HTTP_REFERER']);
		}
		$detail=$this->activitymodel->getActivityMembers($actid,$agid);
		$gpEmp = (!empty($detail))?$detail:array();
		//print_r($this->groupmodel->employee_list());
		if(!empty($gpEmp)){
			$emplist=array_diff($this->groupmodel->employee_list(),$gpEmp);
		}else{
			$emplist=$this->groupmodel->employee_list();
		}
		$this->sysconfmodel->data['html']['title'] .= " | Add Employee To Activity Group";
		$data['module']['title'] = "Add Employee To Activity Group";
		$data['file'] = "system/application/js/group.js.php";
		$formFields = array();
		$formFields[] = array(
					'label'=>'<label for="eid">'.$this->lang->line('employee').' : </label>',
					'field'=>form_dropdown('eid[]',$emplist,'',' multiple="multiple" class="emplist" id=eid[]'));
							
		$data['form'] = array(
		           // 'form_attr'=>array('action'=>'$fromAction','name'=>'addActivityG'),
					'open'=>form_open_multipart(current_url()
								,array('name'=>'addemp','class'=>'form','id'=>'addemp','method'=>'post')
								,array('bid'=>$bid
								  ,'agid'=>$actid
								)),
					'fields'=>$formFields,
					'close'=>form_close()
				);
			
		$this->load->view('form_view',$data);
		
	}
	function listactivity_member($agid){
		
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('label_groupemp');
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '30';
		$header = array($this->lang->line('level_empname')
						,$this->lang->line('level_group')
						,$this->lang->line('level_Action')
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->activitymodel->activityemplist($agid,$ofset,$limit);
		$roleDetail = $this->roleDetail;
		$opt_add=$roleDetail['modules']['31']['opt_add']; 
		$opt_delete=$roleDetail['modules']['31']['opt_delete']; 
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$opt='';
			$opt.=($opt_delete)?'&nbsp;<a href="'.site_url('activity/deleteactEmp/'.$item['eid']."/".$item['agid']).'">
					<span title="Delete Employee from Group" class="glyphicon glyphicon-trash"></span>
				  </a>':'';
			$rec[] = array(
				$item['empname']
				,$item['groupname']
				,$opt
			);
		}

		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		
		
		$this->pagination->initialize(array(
						 'base_url'=>site_url('group/group_emp_list/'.$this->uri->segment(3))
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_groupemp');
		$data['links']='';
		$data['nosearch']=1;
		$formFields = array();
		$data['form'] = array(
			'open'=>form_open_multipart('group/manage_group/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->load->view('list_view',$data);
	
	}
	function deleteactEmp($empid,$agid,$actid){
		$res=$this->activitymodel->del_actemp($empid,$agid,$actid);
		redirect($_SERVER['HTTP_REFERER']);
	}
	function edit_activityReport($aid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['32']['opt_add']) redirect('Employee/access_denied');
		$itemDetail = $this->configmodel->getDetail('32',$aid,'',$bid);
		if($this->input->post('update_system')){
			$res=$this->activitymodel->editReportAct($aid);
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', "Updated Successfully");
			redirect('activity/reportList/'.$itemDetail['agid']);
		}
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$data['module']['title'] = 'Edit Activity Report';
		$fieldset = $this->configmodel->getFields('32');
		$formFields = array();
		
		foreach($fieldset as $field){
			
			$checked=false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
						$cf = array('label'=>'<label>'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).' : </label>',
									'field'=>(($field['fieldname']=="aeid")?$itemDetail['empname']:(($field['fieldname']=="agid")?$itemDetail['groupname']:(($field['fieldname']=="actid")?$itemDetail['activity_name']:$itemDetail['datetime'])))
									
									);
										
								
							
						array_push($formFields,$cf);
				}
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$cf = array('label'=>'<label>'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,
											isset($itemDetail['custom['.$field['fieldid'].']'])?
											$itemDetail['custom['.$field['fieldid'].']']:'')
						);
					array_push($formFields,$cf);
				}
			}
		}
		$ActCustom=$this->activitymodel->getCustomFields($itemDetail['agid'],$itemDetail['actid']);
		$x = $this->activitymodel->getCustomFieldsVal($itemDetail['agid'],$aid,$itemDetail['actid']);
		if(!empty($ActCustom)){
			foreach($ActCustom as $field){
			$cf = array('label'=>'<label>'.$field['fieldname'].' : </label>',
									'field'=>form_input(
													array(
														  'name'      => 'cus['.$field['fieldid'].'][]'
														  ,'id'        =>$field['fieldid']
														  ,'class'        => (($field['is_required'])?'required ':'').$field['vtype']
														  ,'value'     => (isset($x['cust['.$field['fieldid'].']']))?$x['cust['.$field['fieldid'].']']:''
														  )));
				array_push($formFields,$cf);
			}
		}	
		$data['form'] = array(
		            'form_attr'=>array('action'=>'activity/edit_activityReport/'.$aid,'name'=>'edit_actreport'),
					//'open'=>form_open_multipart('activity/edit_activityReport/'.$aid,array('name'=>'edit_actreport','id'=>'edit_actreport','class'=>'form','method'=>'post'),array('agid'=>$itemDetail['agid'],'actid'=>$itemDetail['actid'])),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
  }
  function downloadact_csv($agid,$actid){
	  
	  $cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data=array('systemfields'=>$this->configmodel->getFields('32',$bid),
					'roleDetail'=>$this->roleDetail,
					'customfields'=>$ActCustom=$this->activitymodel->getCustomFields($agid,$actid),
					'agid'=>$agid,
					'actid'=>$actid
					 );
		$this->load->view('activityCSV',$data);
	}
	function list_Activity($agid){
		 $cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if(!$this->feature_access(12))redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['3']['opt_view']) redirect('Employee/access_denied');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$header = array('#','Activity','keyword','Action');
		$data['itemlist']['header'] = $header;
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '20';
		$emp_list = $this->activitymodel->activity_createdList($agid,$bid,$ofset,$limit);
		
		$data['module']['title'] = $this->lang->line('label_listactivity') . "[".$emp_list['count']."]";
		$rec = array();
		if(count($emp_list['count'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			$rec[] = array($i,$item['activity_name'],$item['keyword'],
						'<a href="'.base_url().'activity/acCustom/'.$agid.'/'.$item['acid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="fa fa-file-text"></span></a>&nbsp;&nbsp;<a href="'.base_url().'activity/activity_member/'.$agid.'/'.$item['acid'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><img src="system/application/img/icons/addtogroup.png" /></a>'
						
					);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('activity/list_Activity/'.$agid)
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		$data['paging'] = $this->pagination->create_links();
		$data['links']='';
		$formFields = array();
		
		$formFields[] = array('label'=>'<label for="filename">Activity Name: </label>',
						   'field'=>form_input(array(
										'name'      => 'acname',
										'id'        => 'acname',
										'class'     => '',
										'type'	  => 'text')
										)
							);
		$data['form'] = array(
							'open'=>form_open_multipart('activity/list_Activity/'.$agid,array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function acCustom($aigd,$acid){
	   $cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		
		if(!$this->feature_access(12))redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['3']['opt_view']) redirect('Employee/access_denied');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$header = array('#','Fields','Order','Field Type','Action');
		$data['itemlist']['header'] = $header;
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '20';
		$emp_list = $this->activitymodel->activityCustom($acid,$bid,$ofset,$limit);
		
		$data['module']['title'] = "Custom Fields";
		$rec = array();
		if(count($emp_list['count'])>0)
		$i=1;
		foreach ($emp_list['data'] as $item){
			$rec[] = array($i,$item['fieldname'],$item['order'],$item['vtype'],'<a href="'.base_url().'activity/del_cust/'.$acid.'/'.$item['fieldid'].'" class="delC" rel="'.$aigd.'"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>'
					);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('activity/list_Activity/'.$acid)
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4					
				));
		$data['paging'] = $this->pagination->create_links();
		$data['file'] = "system/application/js/group.js.php";
		$data['links']='';
		$data['nosearch']=1;
		$formFields = array();
		
		$data['form'] = array(
							'open'=>form_open_multipart('activity/list_Activity/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->load->view('list_view',$data);
		$data['module']['title'] = "Custom Fields";
		$formFields = array();$formFields1 = array();
		$cf=array('label'=>"<label><div id='FrmError'></div></label>",
				  'field'=>'');
			array_push($formFields,$cf);
		$data['fieldtype']=array(""=>"Select Field Type"
								,"number"=>"Numeric"
								,"alphanumeric"=>"Alpha Numeric"
								,"alpha"=>"Alpha Only");
		$data['noshow']=1;
		$data['form'] = array(
				'open'=>form_open_multipart('activity/addCustom/'.$aigd.'/'.$acid,array('name'=>'configure','class'=>'form','id'=>'configure','method'=>'post'),array('gn'=>'','pn'=>'','eid'=>'')),
				'fields'=>$formFields,
				'fields1'=>$formFields1,
				'close'=>form_close()
			);
		$this->load->view('activityview',$data);
		
	}
	function del_cust($actid,$field_id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->activitymodel->delcust($actid,$field_id,$bid);
		return true;
	}
	function act_Report($agid,$actid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;	
		if(!$this->feature_access(12))redirect('Employee/access_denied');
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
			$data['module']['title'] = "Activity Report";
		$ofset = ($this->uri->segment(5)!=null)?$this->uri->segment(5):0;
		$limit = '20';
		$data['itemlist'] = $this->activitymodel->activityReport($bid,$ofset,$limit,$agid,$actid);
		if($this->input->post('download')){
			$filename = $this->activitymodel->downloadact_Csvs($agid,$actid);
			$dlink =  "<a href='".$this->config->item('reports_path').$filename.".zip"."' target='_blank' style='color:#000;'>Start Download</a>  ";
		}else{
			$dlink = "";
		}
		$csv_link=$agid.'/'.$actid;
		$links =array();
		$links[]=($roleDetail['modules']['32']['opt_download']!=0)?'<li><a href="activity/downloadact_csv/'.$csv_link.'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Download" class="glyphicon glyphicon-download-alt">$nbsp;Download All</span></a></li>':'';
		$formFields = array();
		$fieldset = $this->configmodel->getFields('32',$bid);
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],
						array('groupname'
								,'number'
								))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>form_input(array(
													'name'      => $field['fieldname'],
													'id'        => $field['fieldname'],
													'value'     => '')));
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
		
		$ActCustom=$this->activitymodel->getCustomFields($agid,$actid);
		if(!empty($ActCustom)){
			foreach($ActCustom as $field){
			$cf = array('label'=>'<label>'.$field['fieldname'].' : </label>',
									'field'=>form_input(
													array(
														  'name'      => 'cus['.$field['fieldid'].']'
														  ,'id'        =>'cus['.$field['fieldid'].']'
														  ,'value'     => ''
														  )));
				array_push($formFields,$cf);
			}
		}
		$data['downlink'] = $dlink;	
		$data['links'] = $links;	
		$data['form'] = array(
					'open'=>form_open_multipart(site_url('activity/act_Report/'.$agid.'/'.$actid),array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
					'form_field'=>$formFields,
					'parentids'=>$parentbids,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close(),
					'title'=>$this->lang->line('level_search')
					);
							
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('level_report');
		$this->sysconfmodel->viewLayout('list_view',$data);
		
		
	}
}
/* end of Activity controller */
