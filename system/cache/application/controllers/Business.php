<?php
class Business extends Controller {
	var $data,$roleDetail;
	function Business()
	{
		parent::controller();
		//if(!$this->session->userdata('logged_in'))redirect('/user');
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('systemmodel');
		$this->load->model('groupmodel');
		$this->load->model('empmodel');
		$this->load->model('profilemodel');
		$this->load->model('configmodel');
		$this->roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
	}
	function index(){
		$this->profile_edit();
	}
	function profile_edit($bid=''){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['1']['opt_add']) redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			//$this->form_validation->set_rules('businessname', 'Business Name', 'required|min_length[4]|max_length[50]|alpha_numeric');
			$this->form_validation->set_rules('contactname', 'Contact Name', 'required|min_length[4]|max_length[32]|alpha_numeric');
			$this->form_validation->set_rules('businessemail', 'Business Email', 'required|min_length[4]|max_length[50]|valid_email');
			$this->form_validation->set_rules('contactemail', 'Contact Email', 'required|min_length[4]|max_length[50]|valid_email');
			$this->form_validation->set_rules('contactphone', 'Contact Phone', 'required|min_length[10]|max_length[50]|numeric');
			$this->form_validation->set_rules('businessphone', 'Business Phone', 'required|min_length[10]|max_length[50]|numeric');
			$this->form_validation->set_rules('businessaddress', 'Business Address', 'required|min_length[4]|max_length[150]');
			$this->form_validation->set_rules('businessaddress1', 'Business Address1', 'min_length[4]|max_length[150]');
			$this->form_validation->set_rules('language', 'Language', 'required');
			$this->form_validation->set_rules('city', 'city', 'required|min_length[4]|max_length[50]|alpha');
			$this->form_validation->set_rules('state', 'state', 'required|min_length[4]|max_length[50]|alpha');
			$this->form_validation->set_rules('country', 'country', 'required|min_length[4]|max_length[50]|alpha');
			//$this->form_validation->set_rules('locality', 'locality', 'required|min_length[3]|max_length[50]|alpha');
			$this->form_validation->set_rules('zipcode', 'zipcode', 'required|min_length[6]|max_length[10]|numeric');
			$this->form_validation->set_rules('apisecret', 'API Key', 'required');
			$this->form_validation->set_rules('webaddress', 'Website Address', 'valid_url');
			if(!$this->form_validation->run() == FALSE)	{	
				$res=$this->profilemodel->update_profile();
				if($res){
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', $this->lang->line('error_profilesuccmsg'));
					redirect('BusinessProfile');
				}
			}		
		}
		$roleDetail = $this->roleDetail;
		//$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$data['module']['title'] = $this->lang->line('level_business_details');
		$fieldset = $this->configmodel->getFields('1');
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('1',$this->session->userdata('bid'));
		(sizeof($itemDetail)>0)?$x='edit':$x='add';
		//$authenticate=$this->configmodel->check_authenticate($roleDetail,1,$x);
		//if($authenticate){
		foreach($fieldset as $field){
			$checked=false;
			$refresh = '<img src="images/reload.png" id="api_reload" title="Click to reload" style="cursor:pointer;">';
			if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
						$cf = array('label'=>'<label class="col-sm-4 text-right">'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).' : </label>',
									'field'=>($field['fieldname']=="businessaddress"||$field['fieldname']=="businessaddress1")?
									form_textarea(array(
												  'name'      => $field['fieldname'],
												  'id'        => $field['fieldname'],
												   'class'     => 'form-control',
												  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:''
										)):
										(($field['fieldname']=="language")?
										form_dropdown('language',$this->systemmodel->get_all_languages(),isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'','id="language" class="form-control"'):					
										(($field['fieldname']=="apisecret")?
										form_input(array(
												  'name'      => $field['fieldname'],
												  'id'        => $field['fieldname'],
												  'readonly'  => 'readonly', 
												  'class'     => 'form-control required',
												  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:''
										)).$refresh
											:form_input(array(
												  'name'      => $field['fieldname'],
												  'id'        => $field['fieldname'],
												  'class'     => 'form-control',
												  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:''
										)))
								)
							);
						array_push($formFields,$cf);
				}
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$cf = array('label'=>'<label class="col-sm-4 text-right">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,
											isset($itemDetail['custom['.$field['fieldid'].']'])?
											$itemDetail['custom['.$field['fieldid'].']']:'')
						);
					array_push($formFields,$cf);
				}
			}
		}
		$cf = array('label'=>'<label class="col-sm-4 text-right">OTP : </label>',
								'field'=>form_checkbox(array('id'=>'otp'
										 ,'name'=>'otp'
										,'value'=>'0',
										'checked'=>($itemDetail['otp']==1)?true:false
										))
						);
					array_push($formFields,$cf);
		$data['form'] = array(
		            'form_attr'=>array('action'=>'BusinessProfile','id'=>'businessprofile','name'=>'businessprofile','enctype'=>"multipart/form-data"),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}	
	function RelatedBusiness(){
		$data['module']['title'] ="Related Business";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,$this->lang->line('level_businessname')
						,$this->lang->line('level_contactname')
						,$this->lang->line('level_contactemail')
						,$this->lang->line('level_contactphone')
						
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->profilemodel->getBusinesslist($ofset,$limit);
		//print_r($emp_list);exit;
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$related=($item['relatedto']!=2)?'Partner':'Sales Executive';
			$Emp=($item['relatedto']!=2)?$this->profilemodel->get_Partnerval($item['employee'])->firstname
			:$this->profilemodel->get_salesEmp($item['employee'])->empname;
			//~ $migrate_regular=($item['act']!="0")?'<a href="Masteradmin/migrate_user/'.$item['bid'].'"><img src="system/application/img/icons/migrate.jpg" /></a>':'';
			
			$rec[] = array(
				$item['bid']
				,$item['businessname']
				,$item['contactname']
				,$item['contactemail']
				,$item['contactphone']
				
			);
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Business/RelatedBusiness')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | Manage PriNumbers";
		$data['links']='';
		$formFields1 = array();
		$cf=array('label'=>'<label for="groupname">'.$this->lang->line('level_groupname').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'groupname',
										'id'          => 'groupname',
										'value'       => $this->session->userdata('groupname'))));
						array_push($formFields1,$cf);
						$data['form'] = array(
							'open'=>form_open_multipart('group/manage_group/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields1,
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->sysconfmodel->data['links'] = '<a href="group/add_group"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		//$fieldset = $this->configmodel->getFields('3');
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label for="f">Business Name: </label>',
				'field'=>form_input(array(
						'name'      => 'businessname',
						'id'        => 'businessname',
						'value'     => $this->session->userdata('businessname')
						))
						);
		$formFields[] = array(
				'label'=>'<label for="f">Business Email: </label>',
				'field'=>form_input(array(
						'name'      => 'bemail',
						'id'        => 'bemail',
						'value'     => $this->session->userdata('bemail')
						))
						);
		$formFields[] = array(
				'label'=>'<label for="f">Phonenumber: </label>',
				'field'=>form_input(array(
						'name'      => 'phnumber',
						'id'        => 'phnumber',
						'value'     => $this->session->userdata('phnumber')
						))
						);
		$formFields[] = array(
				'label'=>'<label for="f">Contact Name: </label>',
				'field'=>form_input(array(
						'name'      => 'cname',
						'id'        => 'cname',
						'value'     => $this->session->userdata('cname')
						))
						);
			
		$formFields[] = array(
				'label'=>'<label for="f">City: </label>',
				'field'=>form_input(array(
						'name'      => 'city',
						'id'        => 'city',
						'value'     => $this->session->userdata('city')
						))
						);
		$formFields[] = array(
				'label'=>'<label for="f">State: </label>',
				'field'=>form_input(array(
						'name'      => 'state',
						'id'        => 'state',
						'value'     => $this->session->userdata('state')
						))
						);
		$droparray=array(""=>"select","0"=>"Regular","1"=>"Demo");			
		$formFields[] = array(
				'label'=>'<label for="f">Type : </label>',
				'field'=>form_dropdown('btype',$droparray)
						);
			
		$data['form'] = array(
			'open'=>form_open_multipart('Masteradmin/Businesslist/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->sysconfmodel->viewLayout('list_view',$data);
		
		
	}
	function businessview($bid){
		
		$this->load->model('configmodel');
		$data['module']['title'] = $this->lang->line('level_business_details');
		$fieldset = $this->configmodel->getFields('1',$bid);
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('1',$bid,'',$bid);
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
						$cf = array('label'=>'<label>'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).' : </label>',
									'field'=>isset($itemDetail[$field['fieldname']]
									)?(($field['fieldname']=='language') ? $itemDetail['lang'] :$itemDetail[$field['fieldname']]):''
								);
						array_push($formFields,$cf);
				
			}
		}
		$data['form'] = array(
		            'form_attr'=>array('action'=>'Mastermodel/businessview','name'=>'businessprofile'),
					//~ 'open'=>form_open_multipart('Mastermodel/businessview',array('name'=>'businessprofile','id'=>'businessprofile','class'=>'form','method'=>'post')),
					'fields'=>$formFields,
					'submit'=>'1',
					'close'=>form_close()
				);
		$this->load->view('form_view',$data);
		
	}
	function gen_apisecret(){
		$apisecret = $this->api_rand();
		while(!$this->profilemodel->check_apisecret($apisecret)){
			$apisecret = $this->api_rand();
		}
		echo $apisecret ;
		
	}
	function api_rand(){
		//$apisecret = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
		$apisecret = md5(uniqid(rand(), true));
		return $apisecret;
	}
}

/* end of business controller */
