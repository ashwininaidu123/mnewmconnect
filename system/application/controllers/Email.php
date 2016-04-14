<?php
class Email extends Controller {
	var $data,$roleDetail,$access;
	function Email(){
		parent::controller();
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('empmodel');
		$this->load->model('systemmodel');
		$this->load->model('mconnectmodel');
		$this->load->model('empmodel');
		$this->load->model('groupmodel');
		$this->load->model('configmodel');
		$this->load->model('ivrsmodel');
		$this->roleDetail=$this->empmodel->getRoledetail($this->session->userdata('roleid'));
		$this->data['ckeditor'] = array(
			//ID of the textarea that will be replaced
			'id' 	=> 	'content',
			'path'	=>	'system/application/js/ckeditor',
			//Optionnal values
			'config' => array(
				'toolbar' 	=> 	"Full", 	//Using the Full toolbar
				'width' 	=> 	"800px",	//Setting a custom width
				'height' 	=> 	'200px',	//Setting a custom height
			),
			//Replacing styles from the "Styles tool"
			'styles' => array(
				//Creating a new style named "style 1"
				'style 1' => array (
					'name' 		=> 	'Blue Title',
					'element' 	=> 	'h2',
					'styles' => array(
						'color' 	=> 	'Blue',
						'font-weight' 	=> 	'bold'
					)
				),
				//Creating a new style named "style 2"
				'style 2' => array (
					'name' 	=> 	'Red Title',
					'element' 	=> 	'h2',
					'styles' => array(
						'color' 		=> 	'Red',
						'font-weight' 		=> 	'bold',
						'text-decoration'	=> 	'underline'
					)
				)
			)
		);
		
	}
	function index(){
		redirect('Email/Main');
	}
	function Main(){
		$data=array();
		$data['module']['title'] = "Email Panel";
		$limit=10;
		$data['emailcontent']=$this->emails();
		$data['method']=$this->EmailContent();
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Email/Main/')
						,'total_rows'=>sizeof($data['emailcontent'])
						,'per_page'=>$limit						
				));
		$data['links']='';
		$data['paging'] = $this->pagination->create_links();
		$data['form'] = array(
			'open'=>form_open_multipart('Email/Main/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'close'=>form_close(),
			);
		$this->sysconfmodel->viewLayout('emailform',$data);
	}
	function sent(){
		$data['module']['title'] ="Sent Mails";
		$ofset = ($this->uri->segment(2)!=null)?$this->uri->segment(2):0;
		$limit = '30';
		$header = array('Sent By',
						'To'
						,'Subject'
						,$this->lang->line('level_Action')
						);
		$data['itemlist']['header'] = $header;
		$bid=$this->session->userdata('bid');
		$emp_list=$this->empmodel->getSendList($bid,$ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$rec[] = array(
				 '<a href="Employee/activerecords/'.$item['eid'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$item['empname'].'</a>'	
				,$item['to']
				,$item['subject']
				,'<a href="'.base_url().'Email/deleteSent/'.$item['id'].'" class="sentDelete"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>'.'<a href="Email/viewEmail/'.$item['id'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span class="fa fa-file-text"  title="View"></span></a>');
		}

		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('SentEmails/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search"class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		
		$formFields = array();
		$advsearch = array();
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">To : </label>',
				  'field'=>form_input(array(
									  'name'        => 'to',
									  'class'        => 'form-control',
										'id'          => 'to',
										'value'       => $this->session->userdata('to'))));
						array_push($formFields,$cf);
        $advsearch['to']="To";							
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">Subject : </label>',
				  'field'=>form_input(array(
									  'name'        => 'subject',
									   'class'        => 'form-control',
										'id'          => 'subject',
										'value'       => $this->session->userdata('subject'))));
						array_push($formFields,$cf);
		$advsearch['subject']="Subject";			
		$this->sysconfmodel->data['links'] = '';
		$data['form'] = array(
			'open'=>form_open_multipart('SentEmails/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'save_search'=>3,
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
	    $data['links'] = $links;
	    if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function compose($item_id='',$source=''){
		$data = $this->data;
		if(!$this->feature_access(10))redirect('Employee/access_denied');
		$bid=$this->session->userdata('bid');
		$itemDetail = $this->configmodel->getDetail('27',$bid,'',$bid);
		$data['module']['title'] = "Email Panel";
		if(empty($itemDetail)){
			$data['error'] = '1' ;
		}
		$post=(isset($_POST['itds']))?$_POST['itds']:$item_id;
		$bid=$this->session->userdata('bid');
		$emp = $this->configmodel->getDetail('2',$this->session->userdata('eid'),'',$bid);
		$formFields = array();
		$formFields[]=array('label'=>'<label class="col-sm-4 text-right">From Name </label> ',
							'field'=>form_input(array(
													'name'      => 'fromName',
													'id'        => 'fromName',
													'value'     => $emp['empname'],
													'autocomplete'=>"off",
													'class'      => "form-control"  )));


		$formFields[]=array('label'=>'<label class="col-sm-4 text-right">From Email </label> ',
							'field'=>form_input(array(

													'name'      => 'fromEmail',
													'id'        => 'fromEmail',
													'value'     => $emp['empemail'],
													'autocomplete'=>"off",
													 'class'      => "form-control" )));
		$formFields[]=array('label'=>'<label class="col-sm-4 text-right">Subject </label> ',
							'field'=>form_input(array(

													'name'      => 'subject',
													'id'        => 'subject',
													'value'     => '',
													'autocomplete'=>"off",
													'class'      => "form-control" )));

		$templates = $this->configmodel->template_names();
		if(! empty($templates)){
			$formFields[]=array('label'=>'<label class="col-sm-4 text-right">Template  </label> ',
							'field'=>form_dropdown('tid',$templates,'',' id="tid" class="form-control"'));
		}
		$formFields[]=array('label'=>'<label class="col-sm-4 text-right">Message </label> ',
							'field'=>'<textarea name="content"  id="content" class="required form-control"></textarea>'
										.display_ckeditor($this->data));
		$data['form'] = array(
			'open'=>form_open_multipart('Email/SentMail/',array('name'=>'compose','class'=>'form','id'=>'compose','method'=>'post'),
			array('itds'=>$post,'source'=>$source)),
			'fields'=>$formFields,
			'close'=>form_close(),
			);
		$this->load->view('emailform_view',$data);
	}
	function SentMail(){
		$bid=$this->session->userdata('bid');
		$itemDetail = $this->configmodel->getDetail('27',$bid,'',$bid);
		$pieces = explode(",", $_POST['itds']);
		for($i=0;$i<sizeof($pieces);$i++){
			$config['protocol']    = 'smtp';
			$config['smtp_host']   = $itemDetail['smtp'];
			$config['smtp_port']   = $itemDetail['port'];
			$config['smtp_user']   = $itemDetail['email'];
			$config['smtp_pass']   = base64_decode($itemDetail['password']);
			$config['charset']     = 'utf-8';
			$config['newline']     = "\r\n";
			$config['mailtype']    = 'html'; // or html
			$config['validation']  = TRUE; // bool whether to validate email or not 
			$this->email->initialize($config);
			//$this->email->from($itemDetail['email'],$itemDetail['fname']);
			$this->email->from($this->input->post('fromEmail'),$this->input->post('fromName'));
			$itemDetail_d = $this->mconnectmodel->getuserdetail($pieces[$i]);
			$message=str_replace('&lt;','<',$this->input->post('content'));
			$message=str_replace('&gt;','>',$message);
			$email=$itemDetail_d['email'];
			$from=$this->input->post('fromEmail');
			$this->email->to($email); 
			$this->email->subject($this->input->post('subject'));
			$this->email->message($message); 
			$status=$this->email->send();
			$res=$this->configmodel->sentmails($bid,$from,$email,$status);
		}
		echo ($status!=1) ? 'Mail Has encountered an error while sending ' : 'Mail Sent Successfully';
	}
	function Config(){
		if(!$this->feature_access(10))redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('fname', 'From Name', 'required');
			$this->form_validation->set_rules('faddress', 'From Address', 'required|valid_email');
			$this->form_validation->set_rules('eprovider', 'Service Provider', 'required');
			$this->form_validation->set_rules('smtp', 'SMTP', 'required');
			$this->form_validation->set_rules('port', 'port', 'required|numeric');
			$this->form_validation->set_rules('email', 'Username', 'required|valid_email');
			$this->form_validation->set_rules('password', 'Password', 'required');
			if(!$this->form_validation->run() == FALSE){
				$res=$this->systemmodel->email_Settings();
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg','Email Configuration Updated Successfully');
				redirect('EmailConfig');
			}
		}
		$data=array();
		$roleDetail = $this->roleDetail;
		$data['module']['title'] = "Email Panel";
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$fieldset = $this->configmodel->getFields('27',$bid);
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('27',$bid,'',$bid);
		foreach($fieldset as $field){
			$checked=false;
			$array=array(''=>'select'
						,'gmail'=>'Gmail'
						,'yahoo'=>'Yahoo'
						,'other'=>'Other'
					);	
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
						$cf = array('label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>',
									'field'=>(($field['fieldname']=="eprovider")?
												form_dropdown('eprovider',$array,(isset($itemDetail['eprovider'])?$itemDetail['eprovider']:$this->input->post('eprovider')),'id="eprovider" class="required form-control"')
												:form_input(array(
											  'name'      => $field['fieldname'],
											  'id'        => $field['fieldname'],
											  'class'     => 'form-control',
											  'type'	  =>($field['fieldname']=='password')?'password':'text',
											  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname'])
										))));
						array_push($formFields,$cf);
				}
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$cf = array('label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,
											isset($itemDetail['custom['.$field['fieldid'].']'])?
											$itemDetail['custom['.$field['fieldid'].']']:'')
						);
					array_push($formFields,$cf);
				}
			}
		}
		$data['form'] = array(
		    'form_attr'=>array('action'=>'EmailConfig/','name'=>'eConfig','id'=>'eConfig','enctype'=>"multipart/form-data"),
			'fields'=>$formFields,
			'close'=>form_close(),
			
			);
		$this->sysconfmodel->viewLayout('form_view',$data);
		
	}
	function emails(){
		$inbox=$this->EmailContent();
		$emails = imap_search($inbox,'ALL');
		if($emails) {
		  rsort($emails);
		  return $emails;	
		}else{
			return array();
		}	
		
	}
	function EmailContent(){
		if(!$this->feature_access(10))redirect('Employee/access_denied');
		$itemDetail=$this->emailAuthentication();
		$hostname='';
		switch($itemDetail['eprovider']){
					case 'gmail':
							 $hostname='{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX';
							break;
					case 'yahoo':
							$hostname='{in.pop.mail.yahoo.com:995/pop3/ssl}INBOX';
							break;
					case 'other':
							$hostname='';
							break;
		}
		$username = $itemDetail['email'];
		$password = base64_decode($itemDetail['password']);
		$inbox = $this->emailConnection($hostname,$username,$password);
		return $inbox;
	}
	function feature_access($access){
		$show=0;
		$checklist=$this->systemmodel->checked_featuremanage();
		if(in_array($access,$checklist)){
			$show=1;
			}
		return $show;
	}
	function autoComplete(){
		$res=$this->systemmodel->getEmailContacts();
	}
	function emailConnection($hostname,$username,$password){
		$inbox=imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());
		return $inbox;
	}
	function emailAuthentication(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$itemDetail=$this->configmodel->getDetail('27',$bid,'',$bid);
		return $itemDetail;
	}
	function deleteSent($id){
		$res=$this->configmodel->deleteSentEmail($id);
		echo "1";
	}
	function listemplate(){
		$data['module']['title'] ="List Template";
		$ofset = ($this->uri->segment(2)!=null)?$this->uri->segment(2):0;
		$limit = '30';
		$header = array('#'
						,'Template Name'
						,$this->lang->line('level_Action')
						);
		$data['itemlist']['header'] = $header;
		$bid=$this->session->userdata('bid');
		$emp_list=$this->empmodel->getEmailTemplate($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$rec[] = array(
				$item['template_id']
				,$item['template_name']
				,'<a href="'.base_url().'AddEmailTemplate/'.$item['template_id'].'"><span title="Edit" class="fa fa-edit"></span></a><a href="'.base_url().'Email/deletetemplate/'.$item['template_id'].'" class="EmailTemp"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>');
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('ManageEmailTemplate/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>2					
				));
		$data['paging'] = $this->pagination->create_links();
		$links = array(); 
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$formFields = array();
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">Template Name : </label>',
				  'field'=>form_input(array(
										'name'        => 'tname',
										'id'          => 'tname',
										'class'       => 'form-control',
										'value'       => $this->session->userdata('tname'))));
		array_push($formFields,$cf);
	    $data['links'] = $links;
		$data['form'] = array(
			'open'=>form_open_multipart('ManageEmailTemplate/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function list_smsemplate(){
		$data['module']['title'] ="List SMS Template";
		$ofset = ($this->uri->segment(2)!=null)?$this->uri->segment(2):0;
		$limit = '30';
		$header = array('#'
						,'Template Name'
						,$this->lang->line('level_Action')
						);
		$data['itemlist']['header'] = $header;
		$bid=$this->session->userdata('bid');
		$emp_list=$this->empmodel->getSMSTemplate($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$rec[] = array(
				$item['template_id']
				,$item['template_name']
				,'<a href="'.base_url().'AddSMSTemplate/'.$item['template_id'].'"><span title="Edit" class="fa fa-edit"></span></a><a href="'.base_url().'Email/deleteSMStemplate/'.$item['template_id'].'" class="EmailTemp"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>');
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('ManageSMSTemplate/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));	
		$data['paging'] = $this->pagination->create_links();
		$formFields = array();
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">Template Name : </label>',
				  'field'=>form_input(array(
									  'name'        => 'tname',
										'id'          => 'tname',
										'class'       => 'form-control',
										'value'       => $this->session->userdata('tname'))));
		array_push($formFields,$cf);
		$links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$data['links'] = $links;
		$data['form'] = array(
			'open'=>form_open_multipart('ManageSMSTemplate',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function template($id=''){
		if(!$this->feature_access(10))redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			$id = ($id != '') ? $id : $this->uri->segment(2);
			$res=$this->configmodel->etemplate($id);
			if($id!=''){
				$this->session->set_flashdata('msg','Update Successfully');
			}else{
				$this->session->set_flashdata('msg',' Insert Successfully');
			}
			$this->session->set_flashdata('msgt','success');
			redirect('ManageEmailTemplate');
		}
		$data=array();
		$bid=$this->session->userdata('bid');
		$itemDetail = $this->empmodel->temp_details($id);
		$data['module']['title'] = "Email Template";
		$formFields = array();
		$formFields[]=array('label'=>'<label class="col-sm-4 text-right">Template Name :</label> ',
							'field'=>form_input(array(
													'name'      => 'tname',
													'id'        => 'tname',
													'value'     => isset($itemDetail['template_name'])?$itemDetail['template_name']:'',
													'class'		=>'required form-control',
													'autocomplete'=>"off")));
		$tval=isset($itemDetail['content'])?$itemDetail['content']:'';											
		$formFields[]=array('label'=>'<label class="col-sm-4 text-right">Message :</label> ',
							'field'=>'<textarea name="content" id="content" class="form-control">'.$tval.'</textarea>'
										.display_ckeditor($this->data));
		$data['form'] = array(
		    'form_attr'=>array('action'=>'AddEmailTemplate/0','name'=>'Etemp','id'=>'Etemp','enctype'=>"multipart/form-data"),
			'fields'=>$formFields,
			'close'=>form_close(),
			);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function smsContent($tid){
		$message='';
		if($tid!=0){
			$itemDetail = $this->empmodel->SMStemp_details($tid);
			$message=trim($itemDetail['content']);
		}
		echo trim($message);
	}
	function Smstemplate($id=''){
		if(!$this->feature_access(10))redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			$id = ($id != '' ) ? $id : $this->uri->segment(2);
			$res = $this->configmodel->Setemplate($id);
			if($id!=''){
				$this->session->set_flashdata('msg','Template Updated Successfully');
			}else{
				$this->session->set_flashdata('msg','Template Inserted Successfully');
			}
			$this->session->set_flashdata('msgt','success');
			redirect('ManageSMSTemplate');
		}
		$data=array();
		$bid=$this->session->userdata('bid');
		$itemDetail = $this->empmodel->SMStemp_details($id);
		$data['module']['title'] = "SMS Template";
		$formFields = array();
		$formFields[]=array('label'=>'<label class="col-sm-4 text-right">Template Name :</label> ',
							'field'=>form_input(array(
													'name'      => 'tname',
													'id'        => 'smess',
													'value'     => isset($itemDetail['template_name'])?$itemDetail['template_name']:'',
													'class'		=>'required form-control',
													'autocomplete'=>"off")));
		$tval=isset($itemDetail['content'])?$itemDetail['content']:'';											
		$formFields[]=array('label'=>'<label class="col-sm-4 text-right">Message :</label> ',
							'field'=>'<textarea name="content" id="content" class="word_count required form-control">'.$tval.'</textarea>'
							);
		$data['form'] = array(
		    'form_attr'=>array('action'=>'AddSMSTemplate/0','name'=>'Etemp','id'=>'Etemp','enctype'=>"multipart/form-data"),
			'fields'=>$formFields,
			'close'=>form_close(),
			);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function deletetemplate($tid){
		$res=$this->configmodel->delete_Template($tid);
		echo "1";
	}
	function deleteSMStemplate($tid){
		$res=$this->configmodel->delete_SMSTemplate($tid);
		echo "1";
	}
	function TemplateC($id=''){
		if($id!=''){
			$itemDetail = $this->empmodel->temp_details($id);
			$message=str_replace('&lt;','<',$itemDetail['content']);
			$message=str_replace('&gt;','>',$message);
			echo $message;
		}else{
			echo '';
		}
	}
	function viewEmail($id){
		$itemDetail=$this->empmodel->getSendItem($id);
		$data['module']['title'] = "View Mail";
		$formFields= array();
		$formFields[]=array('label'=>'<label class="col-sm-4 text-right">Sent By :</label> ','field'=>$itemDetail['empemail']);
		$formFields[]=array('label'=>'<label class="col-sm-4 text-right">To :</label> ','field'=>$itemDetail['to']);
		$formFields[]=array('label'=>'<label class="col-sm-4 text-right">Subject :</label> ','field'=>$itemDetail['subject']);
		$formFields[]=array('label'=>'<label class="col-sm-4 text-right">Message :</label> ','field'=>$itemDetail['description']);
		$data['form'] = array(
					'open'=>form_open('Report/edit/'.$id,array('name'=>'editreport','id'=>'editreport','class'=>'form','method'=>'post')),
					'fields'=>$formFields,
					'close'=>form_close()
				);								
		$this->load->view('active_view',$data);
		
	}
	function smsBalance(){
		$bid=$this->session->userdata('bid');
		$balance=$this->configmodel->smsBalance($bid);
		echo $balance;exit;
	}
}
/* end */
