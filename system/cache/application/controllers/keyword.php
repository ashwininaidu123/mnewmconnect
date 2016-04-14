<?php
class Keyword extends controller
{
	var $data,$roleDetail;
	function Keyword()
	{
		parent::controller();
		//if(!$this->session->userdata('logged_in'))redirect('/user');
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('systemmodel');
		$this->load->model('groupmodel');
		$this->load->model('ivrsmodel');
		$this->load->model('keywordmodel');
		$this->load->model('configmodel');
		$this->load->model('empmodel');
		$this->roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		if(!$this->feature_access())redirect('Employee/access_denied');
	}
	function feature_access()
	{
		$show=0;
		$checklist=$this->systemmodel->checked_featuremanage();
		if(in_array(3,$checklist)){
			$show=1;
			}
		return $show;
	}
	function index(){
		$this->addkeyword();
		
	}
	function subkeyword($kid,$id=''){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['13']['opt_view']) redirect('Employee/access_denied');
		if($this->input->post('update_system'))
		{
			if($kid!="" && $id!=""){
				$res=$this->keywordmodel->updatesubkeyword($kid,$id);
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', $this->lang->line('level_updatesubkeywordupsuccmsg'));
				redirect('keyword/showsubkeywords/'.$kid);
				
			}else{
				$res=$this->keywordmodel->addsubkeyword($kid);
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', $this->lang->line('level_subkeywordupsuccmsg'));
				redirect('keyword/managekeyword');
			}
		}
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$data['module']['title'] = $this->lang->line('level_subkeyword');
		$fieldset = $this->configmodel->getFields('13');
		$formFields = array();
		if($id!=""){
		$itemDetail = $this->configmodel->getDetail('13',$id);
		}else{
			$itemDetail =array();
		}
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['fieldname']!='fowardto_type'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
						$cf = array('label'=>(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										),
									'field'=>($field['fieldname']=="replymsg")?
									form_textarea(array(
									
												  'name'      => $field['fieldname'],
												  'id'        => $field['fieldname'],
												  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'',
												  'onkeyup'=>"textCounter(this,'count_display',160)"
										))."<span id='count_display'>160</SPAN>" :
										(($field['fieldname']=="code_id")?
										form_dropdown('code_id',$this->keywordmodel->getshorcode(),isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'','id="code_id"')						
										
											:(($field['fieldname']=="keyword_use")?form_dropdown('keyword_use',$this->keywordmodel->keyworduse(),isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'','id="keyword_use"')						
											:form_input(array(
												  'name'      => $field['fieldname'],
												  'id'        => $field['fieldname'],
												  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:''
										)))
								)
							);
						array_push($formFields,$cf);
				}
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$cf = array('label'=>$field['customlabel'],
								'field'=>$this->configmodel->createField($field,
											isset($itemDetail['custom['.$field['fieldid'].']'])?
											$itemDetail['custom['.$field['fieldid'].']']:'')
						);
					array_push($formFields,$cf);
				}
			}
		}
				
		$data['form'] = array(
		            'form_attr'=>array('action'=>'keyword/subkeyword/'.$kid.'/'.$id,'name'=>'subkeyword'),
					//~ 'open'=>form_open_multipart('keyword/subkeyword/'.$kid.'/'.$id,array('name'=>'subkeyword','id'=>'subkeyword','class'=>'form','method'=>'post')),
					'fields'=>$formFields,
					'close'=>form_close()
				);
				if($id!=""){
					$this->sysconfmodel->viewLayout('form_view',$data);
				}else{
				$this->load->view('form_view',$data);
					}

	}
	function addkeyword($id='')
	{
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['7']['opt_add']) redirect('Employee/access_denied');
		if($this->input->post('update_system'))
		{
			$this->form_validation->set_rules('code_id', 'Code', 'required');
			$this->form_validation->set_rules('keyword', 'keyword', 'required|min_length[1]|max_length[32]|alpha_numeric');
			$this->form_validation->set_rules('default_msg', 'Default Message', 'required|min_length[4]|max_length[160]');
			$this->form_validation->set_rules('keyword_use', 'Keyword use', 'required');
			if(!$this->form_validation->run() == FALSE)
			{
			
				if($id!="")
				{
					$res=$this->keywordmodel->updatekeyword($id);
					
					
				}else{
					$res=$this->keywordmodel->addkeyword();
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', $this->lang->line('level_keywordsuccmsg'));
					if($this->input->post('keyword_use')!=3){
					redirect('keyword/addkeyword');}else{
						redirect('keyword/addussd/'.$res);
					}
				}
			}
		}
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$data['module']['title'] = $this->lang->line('level_addkeyword');
		$fieldset = $this->configmodel->getFields('7');
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('7',$id);
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['fieldname']!='fowardto_type'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
						$cf = array('label'=>'<label>'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).' : </label>',
									'field'=>($field['fieldname']=="default_msg")?
									form_textarea(array(
									
												  'name'      => $field['fieldname'],
												  'id'        => $field['fieldname'],
												  'class'	  => 'word_count',
												  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'',
												 
										)):
										(($field['fieldname']=="code_id")?
										form_dropdown('code_id',$this->keywordmodel->getshorcode(),$this->keywordmodel->shortcode_select(isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:''),'id="code_id"')						
										
											:(($field['fieldname']=="keyword_use")?form_dropdown('keyword_use',$this->keywordmodel->keyworduse(),$this->keywordmodel->keyworduse_select(isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:''),'id="keyword_use"')						
											:form_input(array(
												  'name'      => $field['fieldname'],
												  'id'        => $field['fieldname'],
												  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:''
										)))
								)
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
		$data['form'] = array(
		            'form_attr'=>array('action'=>'keyword/addkeyword/'.$id,'name'=>'keyword'),
					//~ 'open'=>form_open_multipart('keyword/addkeyword/'.$id,array('name'=>'keyword','id'=>'keyword','class'=>'form','method'=>'post')),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function managekeyword()
	{
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['7']['opt_view']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('level_Report');
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '20';
		$data['itemlist'] = $this->keywordmodel->managekeyword($bid,$ofset,$limit);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('keyword/manage_group')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('level_report');
		$this->sysconfmodel->data['links'] = '<a href="keyword/addkeyword"><span class="glyphicon glyphicon-plus-sign" title="Add Keyword"></span></a>';
		$fieldset = $this->configmodel->getFields('7');
				$formFields = array();
				foreach($fieldset as $field){
					$checked = false;
					if($field['type']=='s' && $field['show'] && $field['fieldname']!='fowardto_type' && $field['fieldname']!="default_msg"){
						foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
						if($checked) $formFields[] = array(
											'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
													 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
											'field'=>form_input(array(
													'name'      => $field['fieldname'],
													'id'        => $field['fieldname']))
													);
					}
				}
				$data['form'] = array(
									'open'=>form_open_multipart('keyword/managekeyword/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
									'form_field'=>$formFields,
									'close'=>form_close(),
									'title'=>$this->lang->line('level_search')
									);
		$this->sysconfmodel->viewLayout('list_view',$data);

		
	}
	function showsubmenulist($optioid){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['17']['opt_view']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('label_listussd');
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '20';
		$data['itemlist'] = $this->keywordmodel->ListsubUssd($optioid,$ofset,$limit);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('keyword/ListingUssd')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_listussd');
		$this->sysconfmodel->data['links'] = '';
		$fieldset = $this->configmodel->getFields('17');
				$formFields = array();
				foreach($fieldset as $field){
					$checked = false;
					if($field['type']=='s' && $field['show'] && $field['fieldname']!='fowardto_type' && $field['fieldname']!="default_msg"){
						foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
						if($checked) $formFields[] = array(
											'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
													 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
											'field'=>form_input(array(
													'name'      => $field['fieldname'],
													'id'        => $field['fieldname']))
													);
					}
				}
				$data['form'] = array(
									'open'=>form_open_multipart('keyword/ListingUssd/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
									'form_field'=>$formFields,
									'close'=>form_close(),
									'title'=>$this->lang->line('level_search')
									);
		$this->sysconfmodel->viewLayout('list_view',$data);
		
	}
	function ListingUssd($keyword,$parentid=0)
	{
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['17']['opt_view']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('label_listussd');
		$ofset = ($this->uri->segment(5)!=null)?$this->uri->segment(5):0;
		$limit = '20';
		$data['itemlist'] = $this->keywordmodel->ListUssd(array('keyword'=>$keyword,'parentid'=>$parentid),$ofset,$limit);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('keyword/ListingUssd')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_listussd');
		$this->sysconfmodel->data['links'] = '';
		$fieldset = $this->configmodel->getFields('17');
				$formFields = array();
				foreach($fieldset as $field){
					$checked = false;
					if($field['type']=='s' && $field['show'] && $field['fieldname']!='fowardto_type' && $field['fieldname']!="default_msg"){
						foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
						if($checked) $formFields[] = array(
											'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
													 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
											'field'=>form_input(array(
													'name'      => $field['fieldname'],
													'id'        => $field['fieldname']))
													);
					}
				}
				$data['form'] = array(
									'open'=>form_open_multipart('keyword/ListingUssd/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
									'form_field'=>$formFields,
									'close'=>form_close(),
									'title'=>$this->lang->line('level_search')
									);
		$this->sysconfmodel->viewLayout('list_view',$data);

		
	}
	function showsubkeywords($id)
	{
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['13']['opt_view']) redirect('Employee/access_denied');
		if($this->input->post('submit')){
			if($this->input->post('subkeyword')!=""){
				if($this->session->userdata('subkeyword')!=""){
						$this->session->unset_userdata('subkeyword ');
					}
					$this->session->set_userdata('subkeyword',$this->input->post('subkeyword'));
				}
				else{
					$this->session->unset_userdata('subkeyword');
					}		
			
		}
		
		
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('level_subkeyword');
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '20';
		$data['itemlist'] = $this->keywordmodel->managesubkeyword($bid,$ofset,$limit,$id);
	//	print_r($data['itemlist']);exit;
		$this->pagination->initialize(array(
						 'base_url'=>site_url('keyword/showsubkeywords/'.$id)
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		
		$formFields1=array();
		$data['links']="";
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('level_subkeyword');
		$formFields1[] = array(
				'label'=>'<label for="f">'.$this->lang->line('level_subkeyword').': </label>',
				'field'=>form_input(array(
						'name'      => 'subkeyword',
						'id'        => 'subkeyword',
						'value'     => $this->session->userdata('subkeyword')
						))
						);
		$data['form'] = array(
							'open'=>form_open_multipart('keyword/showsubkeywords/'.$id,array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields1,
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
					
		$this->sysconfmodel->viewLayout('list_view',$data);

	}
	function editkeyword($id)
	{
		if($this->input->post('Updatekeyword')){
			$res=$this->keywordmodel->updatekeyword($id);
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', $this->lang->line('level_keywordupsuccmsg'));
			redirect('keyword/managekeyword');
		}
		$ress=array();
		foreach($this->groupmodel->employee_list() as $re)
		$ress[$re['eid']]=$re['empname'];
		$data=array(
		'shortcode'=>$this->keywordmodel->getshorcode(),
		'keyworduse'=>$this->keywordmodel->keyworduse(),
		'group_list'=>$this->systemmodel->get_group_list(),
		'emplist'=>$ress,
		'keywordlist'=>$this->keywordmodel->get_keyword($id),
		'subkeyword'=>$this->keywordmodel->subkeywordlist($id),
		'keyid'=>$id
		);	
		$this->sysconfmodel->viewLayout('Addkeyword',$data);
	}
	function addsubkeyword($id)
	{
		if($this->input->post('Addsubkeyword')){
			$res=$this->keywordmodel->addsubkeyword($id);
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', $this->lang->line('level_subkeywordupsuccmsg'));
			redirect('keyword/managekeyword');
		}
		$data=array('id'=>$id);
		$this->load->view('addsubkeyword',$data);
	}
	function activerecords($id='')
	{
			$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['15']['opt_add']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$data['module']['title'] = $this->lang->line('level_business_details');
		$fieldset = $this->configmodel->getFields('15');
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('15',$id);
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['fieldname']!='fowardto_type'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
						$cf = array('label'=>(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										),
									'field'=>isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:''
									
								
							);
						array_push($formFields,$cf);
				}
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$cf = array('label'=>$field['customlabel'],
								'field'=>$this->configmodel->createField($field,
											isset($itemDetail['custom['.$field['fieldid'].']'])?
											$itemDetail['custom['.$field['fieldid'].']']:'')
						);
					array_push($formFields,$cf);
				}
			}
		}
		$data['form'] = array(
					'open'=>form_open_multipart('keyword/inboxmess/'.$id,array('name'=>'keyword','id'=>'keyword','class'=>'form','method'=>'post')),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->load->view('active_view',$data);
		
	}
	function inboxmess($id='')
	{
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['15']['opt_add']) redirect('Employee/access_denied');
		if($this->input->post('update_system'))
		{
			$res=$this->keywordmodel->updateincomingmessages($id);
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', $this->lang->line('err_up_msg'));
				redirect('keyword/incomingmessages');
		
		}
		
		
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$data['module']['title'] = $this->lang->line('level_business_details');
		$fieldset = $this->configmodel->getFields('15');
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('15',$id);
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['fieldname']!='fowardto_type'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
						$cf = array('label'=>(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										),
									'field'=>isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:''
									
								
							);
						array_push($formFields,$cf);
				}
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$cf = array('label'=>$field['customlabel'],
								'field'=>$this->configmodel->createField($field,
											isset($itemDetail['custom['.$field['fieldid'].']'])?
											$itemDetail['custom['.$field['fieldid'].']']:'')
						);
					array_push($formFields,$cf);
				}
			}
		}
		$data['form'] = array(
		            'form_attr'=>array('action'=>'keyword/inboxmess/'.$id,'name'=>'keyword'),
					//~ 'open'=>form_open_multipart('keyword/inboxmess/'.$id,array('name'=>'keyword','id'=>'keyword','class'=>'form','method'=>'post')),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	
	function incomingmessages($ofset='')
	{
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['15']['opt_view']) redirect('Employee/access_denied');
		
				$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
				$bid = $this->session->userdata('bid');
				$data['module']['title'] = $this->lang->line('level_incomingmessages');
				$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
				$limit = '20';
				$data['itemlist'] = $this->keywordmodel->get_recevied_messages($bid,$ofset,$limit);
				$this->pagination->initialize(array(
								 'base_url'=>site_url('keyword/incomingmessages')
								,'total_rows'=>$data['itemlist']['count']
								,'per_page'=>$limit						
						));
				$data['paging'] = $this->pagination->create_links();
				$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('level_messages');
				$data['links']="";
				$fieldset = $this->configmodel->getFields('15');
				$formFields = array();
				foreach($fieldset as $field){
					$checked = false;
					if($field['type']=='s' && $field['show'] && $field['fieldname']!='from' && $field['fieldname']!='eid'){
						foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
						if($checked) $formFields[] = array(
											'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
													 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
											'field'=>form_input(array(
													'name'      => $field['fieldname'],
													'id'        => $field['fieldname'],
													'value'     => $this->session->userdata($field['fieldname'])
													))
													);
					}
				}
				$data['form'] = array(
									'open'=>form_open_multipart('keyword/incomingmessages/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
									'form_field'=>$formFields,
									'close'=>form_close(),
									'title'=>$this->lang->line('level_search')
									);
				$this->sysconfmodel->viewLayout('list_view',$data);

	}
	function addussd($id='',$parent='')
	{
		$this->sysconfmodel->viewLayout('form_view',$this->addussdform($id,$parent,'addussd'));
		
	}
	function for_popup_addusd($id='',$parent=''){
		
		$this->load->view('form_view',$this->addussdform($id,$parent,'addussd'));
	}
	function addussd_popup($id='',$parent='')
	{
		$this->load->view('form_view',$this->addussdform($id,$parent,'addussdpopup'));
		
	}
	function addparentussd($id='',$parent='')
	{
		if($this->input->post('update_system'))
		{
			
				if($parent!=''){
					$res=$this->keywordmodel->addussd_parent($id,$parent);
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', $this->lang->line('level_Ussdaddsub'));
					redirect('keyword/addussd/'.$id.'/'.$parent);
					}
		}
		
	}
	function addussdform($id='',$parent='',$formname){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['17']['opt_add']) redirect('Employee/access_denied');
		if($this->input->post('update_system'))
		{
			$this->form_validation->set_rules('optioncode', 'Option Code', 'required|min_length[1]|max_length[32]|alpha_numeric');
			$this->form_validation->set_rules('optiontext', 'Option Text', 'required|min_length[4]|max_length[32]|alpha_numeric');
			
			if(!$this->form_validation->run() == FALSE)
			{
				if($parent!=''){
					$res=$this->keywordmodel->addussd($id,$parent);
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', $this->lang->line('level_Ussdupdatesucc'));
					redirect('keyword/addussd/'.$id.'/'.$parent);
					
				}else{
					$res=$this->keywordmodel->addussd($id);
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', $this->lang->line('level_Ussdsuccmsg'));
					redirect('keyword/addussd/'.$id);
				}
			}
		}
		$roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$data['file'] = "system/application/js/group.js.php";
		$data['module']['title'] = $this->lang->line('label_Ussd');
		$fieldset = $this->configmodel->getFields('17');
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('17',$parent,$formname);
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['fieldname']!='fowardto_type'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
						$cf = array('label'=>'<label>'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).' : </label>',
									'field'=>($field['fieldname']=="default_msg")?
									form_textarea(array(
									
												  'name'      => $field['fieldname'],
												  'id'        => $field['fieldname'],
												  'class'	  => 'word_count',
												  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'',
												 
										)):
										(($field['fieldname']=="code_id")?
										form_dropdown('code_id',$this->keywordmodel->getshorcode(),$this->keywordmodel->shortcode_select(isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:''),'id="code_id"')						
										
											:(($field['fieldname']=="keyword_use")?form_dropdown('keyword_use',$this->keywordmodel->keyworduse(),$this->keywordmodel->keyworduse_select(isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:''),'id="keyword_use"')						
											:form_input(array(
												  'name'      => $field['fieldname'],
												  'id'        => $field['fieldname'],
												  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:''
										)))
								)
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
		if($formname=='addussdpopup'){$action='keyword/addparentussd/'.$id.'/'.$parent;}else{ $action='keyword/addussd/'.$id.'/'.$parent;}
		$data['form'] = array(
					
			
					'open'=>form_open_multipart($action,array('name'=>$formname,'id'=>$formname,'class'=>'form','method'=>'post')),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		return $data;
	}
	
}
/* end of keyword controller */
