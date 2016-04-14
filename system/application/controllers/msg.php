<?php
class Msg extends Controller {
	var $data,$roleDetail;
	function Msg(){
		parent::Controller();
		//if(!$this->session->userdata('logged_in'))redirect('/user');
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('configmodel');
		$this->load->model('empmodel');
		$this->load->model('msgmodel');
		$this->roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$this->sysconfmodel->data['file'] = "system/application/js/msg.js.php";
		$this->load->model('systemmodel');
		if(!$this->feature_access())redirect('Employee/access_denied');
	}
	function feature_access(){
		$show=0;
		$checklist=$this->systemmodel->checked_featuremanage();
		if(in_array(3,$checklist)){
			$show=1;
			}
		return $show;
	}
	function index(){
		//echo "Credit List To be Shown";
	}
	///////////////////////////////// Phone Book /////////////////////////////////////////////
	
	function PBFrm(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['10']['opt_add']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_phonebookadd');
		
		$data['module']['title'] = $this->lang->line('label_phonebookadd');
		$fieldset = $this->configmodel->getFields('10');
		$formFields = array();
		$itemId = ($this->uri->segment(3)!=null)?$this->uri->segment(3):"";
		$itemDetail = $this->configmodel->getDetail('10',$itemId);
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='datetime' && $field['fieldname']!='createby'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
								'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											?$field['customlabel']
											:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp:</label>',
								'field'=>(form_input(array(
											  'name'      => $field['fieldname'],
											  'id'        => $field['fieldname'],
											  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname']),
											  'type'	  => ($field['fieldname']=='filename')?'file':'text'
											))
									));
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked)$formFields[] = array(
								'label'=>'<label for="'.$field['fieldname'].'">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,isset($itemDetail['custom['.$field['fieldid'].']'])?$itemDetail['custom['.$field['fieldid'].']']:""));
			}
		}
		$data['form'] = array(
					'open'=>form_open_multipart('msg/pbadd'
								,array('name'=>'pbadd','class'=>'form','id'=>'pbadd','method'=>'post')
								,array('bid'=>$this->session->userdata('bid')
								  ,'pbid'=>isset($itemDetail['pbid'])?$itemDetail['pbid']:''
								  ,'modid'=>'10'
								)),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		return $data;
	}
	function pb(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['10']['opt_add']) redirect('Employee/access_denied');
		$this->sysconfmodel->viewLayout('form_view',$this->PBFrm());
	}
	function bppopup(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['10']['opt_add']) redirect('Employee/access_denied');
		$this->load->view('form_view',$this->PBFrm());
	}
	function pbadd(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['10']['opt_add']) redirect('Employee/access_denied');
		$this->form_validation->set_rules('pbname', 'PhoneBook Name', 'required|min_length[4]|max_length[32]|alpha_numeric');
		if($this->form_validation->run() == FALSE)
			{
				
				$this->pb();
			}else{
				if($_FILES['filename']['error']!=0 || $_FILES['filename']['type']!='text/csv'|| $_FILES['filename']['size']=='0'){
						$this->session->set_flashdata(array('msgt' => 'error','msg' => $this->lang->line('ivrs_errorfile')));
						redirect($_SERVER['HTTP_REFERER']);
					}
				$this->msgmodel->addPhonebook();
				redirect($_SERVER['HTTP_REFERER']);
			}
	}
	function delpb($pbid=''){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['10']['opt_delete']) redirect('Employee/access_denied');
		$this->msgmodel->deletePhonebook($pbid);
		redirect("msg/pblist");
	}
	function pblist(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['10']['opt_view']) redirect('Employee/access_denied');
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('label_phonebookmanage');
		$this->sysconfmodel->data['links'] = '<a href="msg/pb"><span class="glyphicon glyphicon-plus-sign" title="Add Phonebook"></span></a>';
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '20';
		$data['itemlist'] = $this->msgmodel->getPhonebookList($bid,$ofset,$limit);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('msg/pblist')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('level_phonebook');
		$fieldset = $this->configmodel->getFields('10');
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='filename'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>(form_input(array(
											'name'      => $field['fieldname']
											,'id'       => $field['fieldname']
											,'class'    => ($field['fieldname']=='datetime')?'datepicker':''))
											)." <img src='system/application/img/icons/help.png' title='".$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname']."'>");
			}
		}
		
		$data['form'] = array(
					'open'=>form_open('msg/pblist',array('name'=>'search','class'=>'form','id'=>'search','method'=>'post')),
					'title'=>'PhoneBook Search',
					'form_field'=>$formFields,
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	////////////////////////////////////////////////////////////////////////////////////////////
	
	///////////////////////////////////SMS Template ////////////////////////////////////////////
	
	function smstmplFrm(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['8']['opt_add']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_sms');
		
		$data['module']['title'] = $this->lang->line('label_sms');
		$fieldset = $this->configmodel->getFields('8');
		$formFields = array();
		$itemId = ($this->uri->segment(3)!=null)?$this->uri->segment(3):"";
		$itemDetail = $this->configmodel->getDetail('8',$itemId);
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='datetime'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
								'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											?$field['customlabel']
											:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp:</label>',
								'field'=>(($field['fieldname']=='content')?form_textarea(array(
											  'name'      => $field['fieldname'],
											  'id'        => $field['fieldname'],
											  'class'	  => 'word_count',
											  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname']),
									)):form_input(array(
											  'name'      => $field['fieldname'],
											  'id'        => $field['fieldname'],
											  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname']),
									)))
						);
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
										'label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
										'field'=>$this->configmodel->createField($field,isset($itemDetail['custom['.$field['fieldid'].']'])?$itemDetail['custom['.$field['fieldid'].']']:"")
						);
			}
		}
		$data['form'] = array(
					'open'=>form_open_multipart('msg/smstmpladd'
								,array('name'=>'smstmplfrm','class'=>'form','id'=>'smstmplfrm','method'=>'post')
								,array('bid'=>$this->session->userdata('bid')
								  ,'templateid'=>isset($itemDetail['templateid'])?$itemDetail['templateid']:''
								  ,'modid'=>'8'
								)),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		return $data;
	}
	function smstmpl(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['8']['opt_add']) redirect('Employee/access_denied');
		$this->sysconfmodel->viewLayout('form_view',$this->smstmplFrm());
	}
	function smstmplpopup(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['8']['opt_add']) redirect('Employee/access_denied');
		$this->load->view('form_view',$this->smstmplFrm());
	}
	function smstmpladd(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['8']['opt_add']) redirect('Employee/access_denied');
		$this->form_validation->set_rules('title', 'title', 'required|min_length[4]|max_length[32]|alpha_numeric');
		$this->form_validation->set_rules('content', 'content', 'required|min_length[4]|max_length[480]');
		if($this->form_validation->run() == FALSE){
				$this->smstmpl();
			}else{
			$this->msgmodel->addSmsTemplate();
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
	function delsmstmlp($impid=''){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['8']['opt_delete']) redirect('Employee/access_denied');
		$this->msgmodel->deleteSMSTemplate($impid);
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	function tmpl($id=''){
		$roleDetail = $this->roleDetail;
		if($roleDetail['modules']['8']['opt_view'] && $id!=''){
			$itemDetail = $this->configmodel->getDetail('8',$id);
			echo trim($itemDetail['content']);
		}else{
			echo "NA";
		}
	}
	function smstmpllist(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['8']['opt_view']) redirect('Employee/access_denied');
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('label_sms');
		$this->sysconfmodel->data['links'] = '<a href="msg/smstmpl"><span class="glyphicon glyphicon-plus-sign" title="Add SMS Template"></span></a>';
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '20';
		$data['itemlist'] = $this->msgmodel->getSMSTmplList($bid,$ofset,$limit);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('msg/smstmpllist')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_sms');
		$fieldset = $this->configmodel->getFields('8');
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>form_input(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname'],
											'class'		=> ($field['fieldname']=='datetime')?'datepicker':''
												))
											);
			}
		}
		
		$data['form'] = array(
					'open'=>form_open('msg/smstmpllist',array('name'=>'search','class'=>'form','id'=>'search','method'=>'post')),
					'title'=>'SMS Template Search',
					'form_field'=>$formFields,
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	
	//////////////////////////////////////////////////////////////////////////////////////
	
	
	////////////////////////////////Voice Tempalte ///////////////////////////////////////
	
	function voivetmplFrm(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['9']['opt_add']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_voiceadd');
		
		$data['module']['title'] = $this->lang->line('label_voiceadd');
		$fieldset = $this->configmodel->getFields('9');
		$formFields = array();
		$itemId = ($this->uri->segment(3)!=null)?$this->uri->segment(3):"";
		$itemDetail = $this->configmodel->getDetail('9',$itemId);
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='datetime'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked && $field['fieldname']!='duration') $formFields[] = array(
								'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											?$field['customlabel']
											:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp:</label>',
								'field'=>(form_input(array(
											  'name'      => $field['fieldname'],
											  'id'        => $field['fieldname'],
											  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname']),
											  'type'	  => ($field['fieldname']=='soundfile')? 'file':'text'
									)))
						);
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
										'label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
										'field'=>$this->configmodel->createField($field,isset($itemDetail['custom['.$field['fieldid'].']'])?$itemDetail['custom['.$field['fieldid'].']']:"")
						);
			}
		}
		$data['form'] = array(
					'open'=>form_open_multipart('msg/voivetmpladd'
								,array('name'=>'voiceadd','class'=>'form','id'=>'voiceadd','method'=>'post')
								,array('bid'=>$this->session->userdata('bid')
								  ,'soundid'=>isset($itemDetail['soundid'])?$itemDetail['soundid']:''
								  ,'modid'=>'9'
								)),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		return $data;
	}
	function voivetmpl(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['9']['opt_add']) redirect('Employee/access_denied');
		$this->sysconfmodel->viewLayout('form_view',$this->voivetmplFrm());
	}
	function voivetmplpopup(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['9']['opt_add']) redirect('Employee/access_denied');
		$this->load->view('form_view',$this->voivetmplFrm());
	}
	function voivetmpladd(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['9']['opt_add']) redirect('Employee/access_denied');
		$this->form_validation->set_rules('title', 'Title', 'required|min_length[4]|max_length[32]|alpha_numeric');
		if($this->form_validation->run() == FALSE)
			{
				$this->voivetmpl();
			}else{
				
				if($_FILES['soundfile']['error']!=0 
				//|| $_FILES['soundfile']['type']!='audio/wav'
				//||$_FILES['soundfile']['type']!='audio/x-wav' 
				||$_FILES['soundfile']['size']=='0'){
					//print_r($_FILES);exit;
						$this->session->set_flashdata(array('msgt' => 'error','msg' => $this->lang->line('ivrs_errorfile')));
						redirect($_SERVER['HTTP_REFERER']);
					}
				$this->msgmodel->addVoiceTemplate();
				redirect($_SERVER['HTTP_REFERER']);
		
			}
	}
	function voivetmpllist(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['9']['opt_view']) redirect('Employee/access_denied');
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('label_voice');
		$this->sysconfmodel->data['links'] = '<a href="msg/smstmpl"><span class="glyphicon glyphicon-plus-sign" title="Add Voice Template"></span></a>';
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '20';
		$data['itemlist'] = $this->msgmodel->getVoiceTmplList($bid,$ofset,$limit);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('msg/voivetmpllist')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_voice');
		$fieldset = $this->configmodel->getFields('9');
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='soundfile'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>form_input(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname'],
											'class'		=> ($field['fieldname']=='datetime')?'datepicker':''
												))
											);
			}
		}
		
		$data['form'] = array(
					'open'=>form_open('msg/voivetmpllist',array('name'=>'search','class'=>'form','id'=>'search','method'=>'post')),
					'title'=>'Voice Template Search',
					'form_field'=>$formFields,
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function delvoivetmpl($id=''){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['9']['opt_delete']) redirect('Employee/access_denied');
		$this->msgmodel->deleteVoice($id);
		redirect($_SERVER['HTTP_REFERER']);
	}
	//////////////////////////////////////////////////////////////////////////////////////
	
	
	//////////////////////////////// SEND SMS ////////////////////////////////////////////
	
	function smsFrm(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['11']['opt_add']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_Sendsms');
		
		$data['module']['title'] = $this->lang->line('label_Sendsms');
		$fieldset = $this->configmodel->getFields('11');
		$formFields = array();
		$itemId = ($this->uri->segment(3)!=null)?$this->uri->segment(3):"";
		$itemDetail = $this->configmodel->getDetail('11',$itemId);
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='datetime'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
								'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											?$field['customlabel']
											:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp:</label>',
								'field'=>(($field['fieldname']=='content')?form_textarea(array(
											  'name'      => $field['fieldname'],
											  'id'        => $field['fieldname'],
											  'class'	  => 'word_count',
											  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname']),
									)):(($field['fieldname']=='filename')?
									/*'<a href="msg/upload" class="callPopup">Upload</a>'
									.form_hidden(array(
											  'name'      => $field['fieldname'],
											  'id'        => $field['fieldname'],
									))*/
									form_input(array(
											  'name'      => $field['fieldname'],
											  'id'        => $field['fieldname'],
											  'type'     => 'file',
											  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname']),
									)):(($field['fieldname']=='senderid')?
									form_dropdown($field['fieldname'],$this->msgmodel->getSenderidList(),set_value($field['fieldname']),'id="'.$field['fieldname'].'"'
									).'<a href="msg/senderadd"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="glyphicon glyphicon-plus-sign" title=""></span></a>':(($field['fieldname']=='pbid')?
									form_dropdown($field['fieldname'],$this->msgmodel->getPBList(),set_value($field['fieldname']),'id="'.$field['fieldname'].'"'
									):(($field['fieldname']=='template')?
									form_dropdown($field['fieldname'],$this->msgmodel->getTmplList(),set_value($field['fieldname']),'id="'.$field['fieldname'].'"'
									):form_input(array(
											  'name'      => $field['fieldname'],
											  'id'        => $field['fieldname'],
											  'class'     => ($field['fieldname']=='scheduleat')?'datepicker':'',
											  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname']),
									)))))))
						);
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
										'label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
										'field'=>$this->configmodel->createField($field,isset($itemDetail['custom['.$field['fieldid'].']'])?$itemDetail['custom['.$field['fieldid'].']']:"")
						);
			}
		}
		$data['form'] = array(
					'open'=>form_open_multipart('msg/sendsms'
								,array('name'=>'smsfrm','class'=>'form','id'=>'smsfrm','method'=>'post')
								,array('bid'=>$this->session->userdata('bid')
								  ,'modid'=>'11'
								)),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		return $data;
	}
	function sms(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['11']['opt_add']) redirect('Employee/access_denied');
		$this->sysconfmodel->viewLayout('form_view',$this->smsFrm());
	}
	function sendsms(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['11']['opt_add']) redirect('Employee/access_denied');
		$this->form_validation->set_rules('senderid', 'Sender Id', 'required');
		//$this->form_validation->set_rules('template', 'Template', 'required');
		//$this->form_validation->set_rules('content', 'content', 'required|min_length[4]|max_length[480]');
		//$this->form_validation->set_rules('to', 'to', 'required|min_length[10]|max_length[10]|numeric');
		//$this->form_validation->set_rules('pbid', 'Phone Book', 'required');
		$this->form_validation->set_rules('scheduleat', 'Shedule At', 'required');
		if($this->form_validation->run() == FALSE){
				$this->sms();
		}else{
			if(($_POST['to']=='' && $_POST['pbid']=='') && 
				($_FILES['filename']['error']!=0	
				//|| $_FILES['filename']['type']!='text/csv' 
				|| $_FILES['filename']['size']=='0')){
					$this->session->set_flashdata(array('msgt' => 'error','msg' => $this->lang->line('ivrs_errorfile')));
					redirect($_SERVER['HTTP_REFERER']);
				}
			$this->msgmodel->sendSMS();
			//$this->session->set_flashdata(array('msgt' => 'success','msg' => 'Message send successfull'));
			redirect($_SERVER['HTTP_REFERER']);
		}
		
	}
	//////////////////////////////////////////////////////////////////////
	
	
	///////////////////////////Voice Broudcust ////////////////////////////
	function vioceFrm(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['12']['opt_add']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_viocebroadcast');
		$data['module']['title'] = $this->lang->line('label_viocebroadcast');
		
		$fieldset = $this->configmodel->getFields('12');
		$formFields = array();
		$itemId = ($this->uri->segment(3)!=null)?$this->uri->segment(3):"";
		$itemDetail = $this->configmodel->getDetail('12',$itemId);
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='datetime' && $field['fieldname']!='uid' && $field['fieldname']!='duration' && $field['fieldname']!='count' && $field['fieldname']!='type'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
								'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											?$field['customlabel']
											:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp:</label>',
								'field'=>(($field['fieldname']=='soundid')
								?form_dropdown($field['fieldname'],$this->msgmodel->getSoundList(),'','id="'.$field['fieldname'].'"')
								:(($field['fieldname']=='pbid')
									?form_dropdown($field['fieldname'],$this->msgmodel->getPBList(),'','id="'.$field['fieldname'].'"')
									:form_input(array(
											  'name'      => $field['fieldname'],
											  'id'        => $field['fieldname'],
											  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'',
											  'class'     => ($field['fieldname']=='scheduleat')?'datepicker':'',
											  'type'     => ($field['fieldname']=='brfile')?'file':'text'
										))
								 ))
						);
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
										'label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
										'field'=>$this->configmodel->createField($field,isset($itemDetail['custom['.$field['fieldid'].']'])?$itemDetail['custom['.$field['fieldid'].']']:"")
						);
			}
		}
		$data['form'] = array(
					'open'=>form_open_multipart('msg/viocebroadcast'
								,array('name'=>'viocebroadcast','class'=>'form','id'=>'viocebroadcast','method'=>'post')
								,array('bid'=>$this->session->userdata('bid')
								  ,'modid'=>'12'
								)),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		return $data;
	}
	function vioce(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['12']['opt_add']) redirect('Employee/access_denied');
		$this->sysconfmodel->viewLayout('form_view',$this->vioceFrm());
	}
	function viocebroadcast(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['12']['opt_add']) redirect('Employee/access_denied');
		$this->msgmodel->addBroadcast();
		redirect($_SERVER['HTTP_REFERER']);
	}
	////////////////////////////////////////////////////////////////////////////
	
	
	//////////////////////////// SMS SenderId //////////////////////////////////
	
	function senderid(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['14']['opt_add']) redirect('Employee/access_denied');
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('label_SenderId');
		$this->sysconfmodel->data['links'] = '<a href="msg/senderadd"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="glyphicon glyphicon-plus-sign" title="Add Sender Id"></span></a>';
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '20';
		$data['itemlist'] = $this->msgmodel->getSMSSenderIdList($bid,$ofset,$limit);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('msg/smstmpllist')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_SenderId');
		$fieldset = $this->configmodel->getFields('14');
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='datetime' && $field['fieldname']!='status'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>form_input(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname']
												))
											);
			}
		}
		
		$data['form'] = array(
					'open'=>form_open('msg/senderid',array('name'=>'search','class'=>'form','id'=>'search','method'=>'post')),
					'title'=>'SMS SenderId Search',
					'form_field'=>$formFields,
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	
	function senderFrm(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['14']['opt_add']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_SenderIdadd');
		$data['module']['title'] = $this->lang->line('label_SenderIdadd');
		$fieldset = $this->configmodel->getFields('14');
		$formFields = array();
		$itemId = ($this->uri->segment(3)!=null)?$this->uri->segment(3):"";
		$itemDetail = $this->configmodel->getDetail('14',$itemId);
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='datetime' && $field['fieldname']!='status'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
								'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											?$field['customlabel']
											:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp:</label>',
								'field'=>form_input(array(
											  'name'      => $field['fieldname'],
											  'id'        => $field['fieldname'],
											  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'',
									))
						);
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
										'label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
										'field'=>$this->configmodel->createField($field,isset($itemDetail['custom['.$field['fieldid'].']'])?$itemDetail['custom['.$field['fieldid'].']']:"")
						);
			}
		}
		$data['form'] = array(
					'open'=>form_open('msg/senderadd'
								,array('name'=>'senderadd','class'=>'form','id'=>'senderadd','method'=>'post')
								,array('bid'=>$this->session->userdata('bid')
								  ,'snid'=>isset($itemDetail['snid'])?$itemDetail['snid']:''
								  ,'modid'=>'14'
								)),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		return $data;
	}
	function senderadd($id=''){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['14']['opt_add']) redirect('Employee/access_denied');
		if(isset($_POST['update_system'])){
			$this->msgmodel->addSenderId();
			redirect($_SERVER['HTTP_REFERER']);
		}
		$this->load->view('form_view',$this->senderFrm());
	}
	function delsender($snid=''){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['14']['opt_delete']) redirect('Employee/access_denied');
		$this->msgmodel->deleteSender($snid);
		redirect($_SERVER['HTTP_REFERER']);
	}
	//////////////////////////////////////////////////////////////////////
	
	function upload(){
		//echo "Tapan";exit;
$THIS_VERSION = '3.1';
require_once 'uber/ubr_ini.php';
require_once 'uber/ubr_lib.php';
if($_INI['php_error_reporting']){ error_reporting(E_ALL); }
$config_file = $_INI['default_config'];
require_once $config_file;
if($_INI['debug_php']){ phpinfo(); exit(); }
elseif($_INI['debug_config']){ debug($_CONFIG['config_file_name'], $_CONFIG); exit(); }
elseif(isset($_GET['about'])){
	kak("<u><b>UBER UPLOADER FILE UPLOAD</b></u><br>UBER UPLOADER VERSION =  <b>" . $_INI['uber_version'] . "</b><br>UBR_FILE_UPLOAD = <b>" . $THIS_VERSION . "</b><br>\n", 1, __LINE__, $_INI['path_to_css_file']);
}
$x = '
<link rel="stylesheet" type="text/css" href="'.$_INI['path_to_css_file'].'">
<script language="JavaScript" type="text/JavaScript" src="'.$_INI['path_to_jquery'].'"></script>
<script language="javascript" type="text/javascript" src="'.$_INI['path_to_js_script'].'"></script>
<script language="javascript" type="text/javascript">
UberUpload.path_to_link_script = "'.$_INI['path_to_link_script'].'";
UberUpload.path_to_set_progress_script = "'.$_INI['path_to_set_progress_script'].'";
UberUpload.path_to_get_progress_script = "'.$_INI['path_to_get_progress_script'].'";
UberUpload.path_to_upload_script = "'.$_INI['path_to_upload_script'].'";
UberUpload.check_allow_extensions_on_client = '.$_CONFIG['check_allow_extensions_on_client'].';
UberUpload.check_disallow_extensions_on_client = '.$_CONFIG['check_disallow_extensions_on_client'].';';
if($_CONFIG['check_allow_extensions_on_client']){ $x.= "UberUpload.allow_extensions = /" . $_CONFIG['allow_extensions'] . "$/i;\n"; }
if($_CONFIG['check_disallow_extensions_on_client']){ $x.= "UberUpload.disallow_extensions = /" . $_CONFIG['disallow_extensions'] . "$/i;\n"; }
$x.= 'UberUpload.check_file_name_format = '.$_CONFIG['check_file_name_format'].';';
if($_CONFIG['check_file_name_format']){ $x.= "UberUpload.check_file_name_regex = /" . $_CONFIG['check_file_name_regex'] . "/;\n"; }
if($_CONFIG['check_file_name_format']){ $x.= "UberUpload.check_file_name_error_message = '" . $_CONFIG['check_file_name_error_message'] . "';\n"; }
if($_CONFIG['check_file_name_format']){ $x.= "UberUpload.max_file_name_chars = " . $_CONFIG['max_file_name_chars'] . ";\n"; }
if($_CONFIG['check_file_name_format']){ $x.= "UberUpload.min_file_name_chars = " . $_CONFIG['min_file_name_chars'] . ";\n"; }
$x.= 'UberUpload.check_null_file_count = '.$_CONFIG['check_null_file_count'].';
UberUpload.check_duplicate_file_count = '.$_CONFIG['check_duplicate_file_count'].';
UberUpload.max_upload_slots = '.$_CONFIG['max_upload_slots'].';
UberUpload.cedric_progress_bar = '.$_CONFIG['cedric_progress_bar'].';
UberUpload.cedric_hold_to_sync = '.$_CONFIG['cedric_hold_to_sync'].';
UberUpload.bucket_progress_bar = '.$_CONFIG['bucket_progress_bar'].';
UberUpload.progress_bar_width = '.$_INI['progress_bar_width'].';
UberUpload.show_percent_complete = '.$_CONFIG['show_percent_complete'].';
UberUpload.show_files_uploaded = '.$_CONFIG['show_files_uploaded'].';
UberUpload.show_current_position = '.$_CONFIG['show_current_position'].';
UberUpload.show_current_file = '. (($_INI['cgi_upload_hook'] && $_CONFIG['show_current_file'])? "1":"0") .';
UberUpload.show_elapsed_time = '.$_CONFIG['show_elapsed_time'].';
UberUpload.show_est_time_left = '.$_CONFIG['show_est_time_left'].';
UberUpload.show_est_speed = '.$_CONFIG['show_est_speed'].';
var JQ = jQuery.noConflict();

JQ(document).ready(function(){
	UberUpload.resetFileUploadPage();
	JQ("#upload_button").bind("click", function(e){ UberUpload.linkUpload(); });
	JQ("#reset_button").bind("click", function(e){ UberUpload.resetFileUploadPage(); });
	JQ("#progress_bar_background").css("width", UberUpload.progress_bar_width);

	if(UberUpload.show_files_uploaded || UberUpload.show_current_position || UberUpload.show_elapsed_time || UberUpload.show_est_time_left || UberUpload.show_est_speed){
		JQ("#upload_stats_toggle").bind("click", function(e){ UberUpload.toggleUploadStats(); });
		JQ("#upload_stats_toggle").html("[+]");
		JQ("#upload_stats_toggle").attr("title", "Toggle Upload Statistics");
	}
});
</script>

<div id="main_container">';
if($_INI['debug_ajax']){ $x.= "<div id='ubr_debug'></div>"; }
$x.= '<div id="ubr_alert"></div>

<!-- Progress Bar -->
<div id="progress_bar_container">
	<div id="upload_stats_toggle">&nbsp;</div>
	<div id="progress_bar_background">
		<div id="progress_bar"></div>
	</div>
	<div id="percent_complete">&nbsp;</div>
</div>

<br clear="all"/>

<!-- Upload Stats -->';
if($_CONFIG['show_files_uploaded'] || $_CONFIG['show_current_position'] 
	|| $_CONFIG['show_elapsed_time'] || $_CONFIG['show_est_time_left'] || $_CONFIG['show_est_speed']){ 
$x.= '	<div id="upload_stats_container">';
		if($_CONFIG['show_files_uploaded']){ 
		$x.= "<div class='upload_stats_label'>&nbsp;Files Uploaded:</div>
		<div class='upload_stats_data'><span id=\"files_uploaded\">0</span> of <span id=\"total_uploads\">0</span></div>";
		}if($_CONFIG['show_current_position']){
		$x.= "<div class='upload_stats_label'>&nbsp;Current Position:</div>
		<div class='upload_stats_data'><span id=\"current_position\">0</span> / <span id=\"total_kbytes\">0</span> KBytes</div>";
		}if($_INI['cgi_upload_hook'] && $_CONFIG['show_current_file']){
		$x.= "<div class='upload_stats_label'>&nbsp;Current File Uploading:</div>
		<div class='upload_stats_data'><span id=\"current_file\"></span></div>";
		}if($_CONFIG['show_elapsed_time']){
		$x.= "<div class='upload_stats_label'>&nbsp;Elapsed Time:</div>
		<div class='upload_stats_data'><span id=\"elapsed_time\">0</span></div>";
		}if($_CONFIG['show_est_time_left']){
		$x.= "<div class='upload_stats_label'>&nbsp;Est Time Left:</div>
		<div class='upload_stats_data'><span id=\"est_time_left\">0</span></div>";
		}if($_CONFIG['show_est_speed']){
		$x.= "<div class='upload_stats_label'>&nbsp;Est Speed:</div>
		<div class='upload_stats_data'><span id=\"est_speed\">0</span> KB/s.</div>";
		}
	$x.= "</div>
	<br clear=\"all\"/>";
}

$x.= "<!-- Container for upload iframe -->
<div id=\"upload_container\"></div>

<!-- Start Upload Form -->
<form id=\"uu_upload\" name=\"uu_upload\" method=\"post\" enctype=\"multipart/form-data\" action=\"#\" style=\"margin:0px; padding:0px\">
	<noscript><span class=\"ubrError\">ERROR</span>: Javascript must be enabled to use Uber-Uploader.<br><br></noscript>
	<div id=\"file_picker_container\"></div>
	<div id=\"upload_slots_container\"></div>
	<!-- Add Your Form Values Here -->
	<div id=\"upload_buttons_container\"><input type=\"button\" id=\"reset_button\" name=\"reset_button\" value=\"Reset\">&nbsp;&nbsp;&nbsp;<input type=\"button\" id=\"upload_button\" name=\"upload_button\" value=\"Upload\"></div>
</form>
</div>
<br clear=\"all\"/>";
echo $x;
	}
}
?>
