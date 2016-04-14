<?php
class partner extends controller
{
	var $data;
	function partner(){
		parent::controller();
		if(!$this->session->userdata('partnerlogged_in') && $this->uri->segment('2')!="index")redirect('/partner/index');
		$this->load->model('partnermodel','pmodel');
		$this->data = $this->pmodel->init();
		$this->load->model('commonmodel');
	}
	function index(){
		if($this->input->post('submit')){
				$this->load->helper('url');
				$this->load->library('validation');
				$rules['login_username']= "required|min_length[4]|max_length[100]|Email";
				$rules['login_password']= "required|min_length[4]|max_length[32]";		
				$rules['validator']			= "required|callback_check_captcha";		
				$this->validation->set_rules($rules);
				$fields['login_username'] = 'Username';
				$fields['login_password'] = 'Password';
				
				$this->validation->set_fields($fields);
						
				if ($this->validation->run() == false) {
					
					//If you are using OBSession you can uncomment these lines
					$flashdata = array('msgt' => 'error', 'msg' => 'Invaild Login');
					$this->session->set_flashdata($flashdata);
					
					redirect('/partner/index');
				} else {
					if($this->simplelogin->partnerLogin($this->input->post('login_username'), $this->input->post('login_password'))=='1'){
						redirect('/partner/Landingpage');
					}
					else{		
						if($this->session->userdata('flash:new:msg')!=""){
							redirect('/partner/index');
						}else{
							$flashdata = array('msgt' => 'error', 'msg' => 'Invaild Login');
							$this->session->set_flashdata($flashdata);
							redirect('/partner/index');
						}
					}
					
				}
		}
		$this->simplelogin->logout();
		$this->data['html']['title'] .= " | Partner Login";
		$this->load->view('siteheader',$this->data);
		$this->load->view('partnerlogin',$this->data);
		$this->load->view('footer',$this->data);
	}
	function check_captcha($str){
		if($str!=$_SESSION['security_code']){
		$this->form_validation->set_message('check_captcha', 'The '.$str.' is not valid security code');
			return FALSE;
		}else{
			return true;
		}
	}
	function Landingpage(){
		
		$this->Businessusers();
		
	}
	function Userslist(){
		$data['module']['title'] ="Business User";
		$header = array('#',
						'Business Name',
						'Business Email',
						'Contact Name',
						'Contact Number',
						'Action'
						);
		$data['itemlist']['header'] = $header;
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '20';
		$credit_info=$this->pmodel->GetBusinessusers($ofset,$limit);
		$rec = array();
		if(count($credit_info['data'])>0)
		$i=1;
		foreach ($credit_info['data'] as $item){
			($item['status']=="0")                                           
				?$s='<a href="partner/BusinessStatus/'.$item['bid'].'"><span class="fa fa-lock" id="'.$item['bid'].'"  title="Enable"></span></a>'
				:$s='<a href="partner/BusinessStatus/'.$item['bid'].'"><span class="fa fa-unlock" id="'.$item['bid'].'"  title="Disable"></span></a>';
				
			$rec[] = array(
					$item['bid'],
					$item['businessname'],
					$item['businessemail'],
					$item['contactname'],
					$item['contactphone'],
					'<a href="partner/AddBusinessUser/'.$item['bid'].'">
					<span title="Edit" class="fa fa-edit"></span>
					</a> '.$s.'<a  class="btn-danger" data-toggle="modal" data-target="#modal-responsive" href="partner/ProductConfigure/'.$item['bid'].'"><img id="'.$item['bid'].'" src="system/application/img/icons/cog.png" title="Configure Rate" width="16" height="16" /></a>'.'<a href="partner/ManageFeature/'.$item['bid'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><img  id="'.$item['bid'].'" src="system/application/img/icons/manage.png" title="Feature Manage" width="16" height="16" /></a>
				'.'<a href="partner/SendMail_UnConfirm/'.$item['bid'].'" 
				><img  id="'.$item['bid'].'" 
				src="system/application/img/icons/sendmail.png" 
				title="Send Mail to Unconfirm Employees" width="16" height="16" 
				/></a>'.' <a 
				href="partner/SendMail_UnConfirm_selected/'.$item['bid'].'" 
				 class="btn-danger" data-toggle="modal" data-target="#modal-responsive"
				><img  id="'.$item['bid'].'" 
				src="system/application/img/icons/sendmail1.png" 
				title="Send Mail to Unconfirm Employees" width="16" height="16" 
				/></a>'.' <a href="partner/AssignMobileNumber_business/'.$item['bid'].'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><img  id="'.$item['bid'].'" 
				src="system/application/img/icons/mobile-phone--pencil.png" 
				title="Landing Number Configuration" width="16" height="16" 
				/></a>'.' <a 
				href="partner/passwordreset/'.$item['bid'].'" 
				><img  id="'.$item['bid'].'" 
				src="system/application/img/icons/reset.png" 
				title="Reset" width="16" height="16" 
				/></a>'.' <a 
				href="payment/generateBill_byuser/'.$item['bid'].'" 
				><img  id="'.$item['bid'].'" 
				src="system/application/img/icons/payment.png" 
				title="Reset" width="16" height="16" 
				/></a>'
					
					);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $credit_info['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('partner/Userslist/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->pmodel->data['html']['title'] .= " | Business User";
		$formFields1 = array();
		$cf=array('label'=>'Business Name',
				  'field'=>form_input(array(
									  'name'        => 'businessname',
										'id'          => 'businessname',
										'value'       => $this->session->userdata('businessname'))));
						array_push($formFields1,$cf);
		$this->pmodel->data['links'] = '';
		$formFields = array();
		$formFields[] = array();
		$data['form'] = array(
							'open'=>form_open_multipart('partner/Userslist/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields1,
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->pmodel->viewLayout('list_view',$data);
	}
	function ProductConfigure($bid)
	{
		if($this->input->post('submit')){
			$this->pmodel->updateproduct_config($bid);
			$flashdata = array('msgt' => 'success', 'msg' => 'Product Configured Successfully');
			$this->session->set_flashdata($flashdata);
			redirect('partner/Userslist');
		}
		$data=array('res'=>$this->pmodel->businessproducts($bid),
					 'bid'=>$bid,
					 'faction'=>'partner/ProductConfigure/'.$bid);
		$this->load->view('businessproducts',$data);
		
	}
	function ManageFeature($bid){
		if($this->input->post('submit')){
			//print_r($_POST);exit;
			$res=$this->pmodel->update_featuremanage($bid);
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', $this->lang->line('error_featuresucc'));
			redirect('partner/Userslist');
		}
		$data=array('feature_list'=>$this->pmodel->feature_manage(),
					'subfeature_list'=>$this->pmodel->sub_featuremanage(),
					'checked_list'=>$this->pmodel->checked_featuremanage($bid),
					'partnerfeatures'=>$this->pmodel->partner_features(),
					'bid'=>$bid,
					'faction'=>'partner/ManageFeature/'.$bid
		);
		$this->load->view('feature_manage',$data);
	}
	function Businessusers(){
		$data['module']['title'] ="Business User";
		$header = array('#',
						'Business Name',
						'Business Email',
						);
		$data['itemlist']['header'] = $header;
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '20';
		$credit_info=$this->pmodel->GetBusinessusers($ofset,$limit);
		$rec = array();
		if(count($credit_info['data'])>0)
		$i=1;
		foreach ($credit_info['data'] as $item){
			$rec[] = array(
					$item['bid'],
					$item['businessname'],
					$item['contactemail'],
					);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $credit_info['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('partner/Businessusers/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->pmodel->data['html']['title'] .= " | Business User";
		$formFields1 = array();
		$cf=array('label'=>'Business Name',
				  'field'=>form_input(array(
									  'name'        => 'businessname',
										'id'          => 'businessname',
										'value'       => $this->session->userdata('businessname'))));
						array_push($formFields1,$cf);
		$this->pmodel->data['links'] = '';
		$formFields = array();
		$formFields[] = array();
		$data['form'] = array(
							'open'=>form_open_multipart('partner/Businessusers/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields1,
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->pmodel->viewLayout('list_view',$data);
	}
	function logout(){
		//Logout
		$this->simplelogin->logout();
		redirect('/partner');
	}
	function AddBusinessUser($id=''){
		if(isset($_POST)){
			$this->form_validation->set_rules('login_businessname', 'Business Name', 'required|min_length[4]|max_length[32]|alpha_numeric');
			$this->form_validation->set_rules('cname', 'Contact Name', 'required|min_length[4]|max_length[32]|alpha_numeric');
			$this->form_validation->set_rules('cemail', 'Contact Email', 'required|min_length[4]|max_length[50]|valid_email');
			$this->form_validation->set_rules('login_username', 'Confirm Email', 'required|min_length[4]|max_length[50]|valid_email|matches[cemail]');
			$this->form_validation->set_rules('cphone', 'Contact Phone', 'required|min_length[10]|max_length[50]|numeric');
			$this->form_validation->set_rules('bphone', 'Business Phone', 'required|min_length[10]|max_length[50]|numeric');
			$this->form_validation->set_rules('baddress', 'Business Address', 'required|min_length[4]|max_length[150]');
			$this->form_validation->set_rules('baddress1', 'Business Address1', 'min_length[4]|max_length[150]');
			$this->form_validation->set_rules('language', 'Language', 'required');
			$this->form_validation->set_rules('city', 'city', 'required|min_length[4]|max_length[50]|alpha');
			$this->form_validation->set_rules('state', 'state', 'required|min_length[2]|max_length[50]|alpha');
			$this->form_validation->set_rules('country', 'country', 'required|min_length[4]|max_length[50]|alpha');
			$this->form_validation->set_rules('locality', 'locality', 'required|min_length[3]|max_length[50]|alpha');
			$this->form_validation->set_rules('zipcode', 'zipcode', 'required|min_length[6]|max_length[10]|numeric');
			$this->form_validation->set_rules('login_username', 'Username', 'required|min_length[6]|max_length[50]|valid_email|matches[cemail]|callback_username_check');
			if($id==""){
			$this->form_validation->set_rules('login_password', 'Password', 'required|min_length[5]|max_length[10]');
			$this->form_validation->set_rules('cpassword', 'Confirm Password', 'required|min_length[5]|max_length[10]|matches[login_password]');
			}
			$this->form_validation->set_rules('waddress', 'Website Address', 'valid_url');
			if(!$this->form_validation->run() == FALSE){	
				
					if($id!=""){
						$update=$this->pmodel->AddBusinessUser($id);
						$this->session->set_flashdata('msgt', 'success');
						$this->session->set_flashdata('msg', $this->lang->line('updatesuccessmsg'));
						redirect('partner/Userslist');
						
					}else{	
						$rs=$this->pmodel->AddBusinessUser();
						$this->session->set_flashdata('msgt', 'success');
						$this->session->set_flashdata('msg', $this->lang->line('successmsg'));
						redirect('partner/Userslist');
					}
				}	
		}
		$this->pmodel->data['html']['title'] .= " | Add Business User";
		$data['module']['title'] ="Add Business User";
		$get_Fields=$this->pmodel->get_busValues($id);
		$formFields = array();$formFields1 = array();
		$cf=array('label'=>'Business Name',
				  'field'=>form_input(array(
									  'name'        => 'login_businessname',
										'id'          => 'login_businessname',
										'value'       =>  (isset($get_Fields[0]['businessname']))?$get_Fields[0]['businessname']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'Contact Name',
				  'field'=>form_input(array(
									  'name'        => 'cname',
										'id'          => 'cname',
										'value'       => (isset($get_Fields[0]['contactname']))?$get_Fields[0]['contactname']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);
					if($id!=""){
		$cf=array('label'=>'Contact Email',
				  'field'=>form_input(array(
									  'name'        => 'cemail',
										'id'          => 'cemail',
										'value'       =>(isset($get_Fields[0]['contactemail']))?$get_Fields[0]['contactemail']:'',
										'class'		=>'required',
										'readonly'=>'true')
									));
		}else{
		$cf=array('label'=>'Contact Email',
				  'field'=>form_input(array(
									  'name'        => 'cemail',
										'id'          => 'cemail',
										'value'       =>(isset($get_Fields[0]['contactemail']))?$get_Fields[0]['contactemail']:'',
										'class'		=>'required')
									));
								}
							array_push($formFields,$cf);
		$cf=array('label'=>'Confirm Email',
				  'field'=>form_input(array(
									  'name'        => 'login_username',
										'id'          => 'login_username',
										'value'       =>(isset($get_Fields[0]['contactemail']))?$get_Fields[0]['contactemail']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'Web Address',
				  'field'=>form_input(array(
									  'name'        => 'waddress',
										'id'          => 'waddress',
										'value'       => (isset($get_Fields[0]['webaddress']))?$get_Fields[0]['webaddress']:'',
										'class'		=>'')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'Contact Phone',
				  'field'=>form_input(array(
									  'name'        => 'cphone',
										'id'          => 'cphone',
										'value'       =>(isset($get_Fields[0]['contactphone']))?$get_Fields[0]['contactphone']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'Business Phone',
				  'field'=>form_input(array(
									  'name'        => 'bphone',
										'id'          => 'bphone',
										'value'       => (isset($get_Fields[0]['businessphone']))?$get_Fields[0]['businessphone']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);
		
		$cf=array('label'=>'Business Address',
				  'field'=>form_textarea(array(
									  'name'        => 'baddress',
										'id'          => 'baddress',
										'value'       => (isset($get_Fields[0]['businessaddress']))?$get_Fields[0]['businessaddress']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'Business Address1',
				  'field'=>form_textarea(array(
									  'name'        => 'baddress1',
										'id'          => 'baddress1',
										'value'       => (isset($get_Fields[0]['businessaddress1']))?$get_Fields[0]['businessaddress1']:'',
										'class'		=>'')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'City',
				  'field'=>form_input(array(
									  'name'        => 'city',
										'id'          => 'city',
										'value'       => (isset($get_Fields[0]['city']))?$get_Fields[0]['city']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'State',
				  'field'=>form_input(array(
									  'name'        => 'state',
										'id'          => 'state',
										'value'       => (isset($get_Fields[0]['state']))?$get_Fields[0]['state']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'Country',
				  'field'=>form_input(array(
									  'name'        => 'country',
										'id'          => 'country',
										'value'       => (isset($get_Fields[0]['country']))?$get_Fields[0]['country']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'Locality',
				  'field'=>form_input(array(
									  'name'        => 'locality',
										'id'          => 'locality',
										'value'       =>(isset($get_Fields[0]['locality']))?$get_Fields[0]['locality']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);
		$cf=array('label'=>'Zipcode',
				  'field'=>form_input(array(
									  'name'        => 'zipcode',
										'id'          => 'zipcode',
										'value'       => (isset($get_Fields[0]['zipcode']))?$get_Fields[0]['zipcode']:'',
										'class'		=>'required')
									));
							array_push($formFields,$cf);
							$js = 'id="language" class="required"';
		$cf=array('label'=>'Language',
				  'field'=>form_dropdown("language",$this->profilemodel->get_languages(),(isset($get_Fields[0]['language']))?$get_Fields[0]['language']:'',$js));
							array_push($formFields,$cf);
		$cf=array('label'=>'Password',
				  'field'=>form_password(array(
									  'name'        => 'login_password',
										'id'          => 'login_password',
										'value'       => '',
										'class'		=>'required')
									));
							($id=="")?array_push($formFields,$cf):'';
		$cf=array('label'=>'Confirm Password',
				  'field'=>form_password(array(
									  'name'        => 'cpassword',
										'id'          => 'cpassword',
										'value'       => '',
										'class'		=>'required')
									));
							($id=="")?array_push($formFields,$cf):'';
			$data['form'] = array(
			        'form_attr'=>array('action'=>'partner/AddBusinessUser/'.$id,'name'=>'AddBusinessUser'),
					//'open'=>form_open_multipart('partner/AddBusinessUser/'.$id,array('name'=>'AddBusinessUser','class'=>'form','id'=>'AddBusinessUser','method'=>'post'),array('bid'=>$id)),
					'fields'=>$formFields,
					'fields1'=>$formFields1,
					'close'=>form_close()
				);
		$this->pmodel->viewLayout('form_view',$data);
	}
	function checkBusinessuser(){
		$res=$this->pmodel->CheckBusinessUser();
		echo $res;
	}
	function BusinessStatus($bid){
		$res=$this->pmodel->ChangeBusinessStatus($bid);
		$flashdata = array('msgt' => 'success', 'msg' => 'status updated Successfully');
		$this->session->set_flashdata($flashdata);
		redirect('partner/Userslist');
		
	}
	function SendMail_UnConfirm($bid){
		//echo $bid;exit;
		
		$send=$this->pmodel->SendMails_Unconfirm($bid);
		if($send){
			$flashdata = array('msgt' => 'success', 'msg' => 'Mail send  
			Successfully to Unconfirmed Employees');
			$this->session->set_flashdata($flashdata);
			redirect('partner/Userslist');
		}else{
			$flashdata = array('msgt' => 'error', 'msg' => 'No 
			unconfirmed Employee Found');
			$this->session->set_flashdata($flashdata);
			redirect('partner/Userslist');
		}
	}
	function SendMail_UnConfirm_selected($bid){
		if($this->input->post('submit')){
			$res=$this->pmodel->Sendmail_unconfirm_Emp($bid);
			$flashdata = array('msgt' => 'success', 'msg' => 'Mail send  
			Successfully to Unconfirmed Employees');
			$this->session->set_flashdata($flashdata);
			redirect('partner/Userslist');
		}
		$r=$this->pmodel->get_unconfirmed_emp($bid);
		$data=array('bid'=>$bid,
					'res'=>$r,
					'faction'=>'partner/SendMail_UnConfirm_selected/'.$bid);
		$this->load->view('UnconfirmEmployee',$data);			
	}
	function managePriList($page='')
	{
		$data['module']['title'] ="Manage Pri Numbers";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,$this->lang->line('level_Prinumbers')
						,$this->lang->line('level_landingnumber')
						,$this->lang->line('level_businessname')
						,$this->lang->line('level_Action')
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->pmodel->managePriList($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			($item['landingnumber']!=0)?$cls=$item['number']." Assigned to group,If You want delete ":$cls="Are you sure to delete ".$item['number'];
			$rec[] = array(
				$i,
				$item['pri']
				,$item['landingnumber']
				,$item['businessname']
				,'<a href="'.site_url('partner/delete_pri/'.$item['number']).'" class="confirm" id="'.$item['number'].'" title="'.$cls.'">
						<span title="Delete" class="glyphicon glyphicon-trash"></span>
				  </a>
					<a href="'.site_url('partner/editpri/'.$item['number']).'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">
					<span title="Edit" class="fa fa-edit"></span>
				  </a>'
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		
		//$data['itemlist'] = $this->groupmodel->getgrouplist($bid,$ofset,$limit);
		
		$this->pagination->initialize(array(
						 'base_url'=>site_url('partner/managePriList/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		//$data['addlinks']="group/add_group";		
		$data['paging'] = $this->pagination->create_links();
		//$data['paging'] =  pagination($limit, $page, $start, $total_pages, $targetpage);
		$this->pmodel->data['html']['title'] .= " | Manage PriNumbers";
		$links =array();
		$links[] ='<li><a href="partner/addPrinumber/"><span class="glyphicon glyphicon-plus-sign" title="Add Number">Add Number</span></a></li>';
		$formFields1 = array();
		$cf=array('label'=>'<label for="groupname">'.$this->lang->line('level_groupname').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'groupname',
										'id'          => 'groupname',
										'value'       => $this->session->userdata('groupname'))));
						array_push($formFields1,$cf);
						
		$this->mastermodel->data['links'] = '<a href="group/add_group"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		//$fieldset = $this->configmodel->getFields('3');
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label for="f">PriNumber : </label>',
				'field'=>form_input(array(
						'name'      => 'prinumber',
						'id'        => 'prinumber',
						'value'     => $this->session->userdata('prinumber')
						))
						);
		$formFields[] = array(
				'label'=>'<label for="f">Landing Number : </label>',
				'field'=>form_input(array(
						'name'      => 'landing_number',
						'id'        => 'landing_number',
						'value'     => $this->session->userdata('landing_number')
						))
						);
		$formFields[] = array(
				'label'=>'<label for="f">Business Name : </label>',
				'field'=>form_input(array(
						'name'      => 'businessname',
						'id'        => 'businessname',
						'value'     => $this->session->userdata('businessname')
						))
						);
		$data['downlink'] = $dlink;	
		$data['links'] = $links;	
		$data['form'] = array(
							'open'=>form_open_multipart('partner/managePriList/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->pmodel->viewLayout('list_view',$data);
		
	}
	function numberConfig(){
		if($this->input->post('submit')){
			$res=$this->pmodel->numberConfig();
			if($res!=0){				
				$flashdata = array('msgt' => 'success', 'msg' => 'landingNumber Added Successfully');
				$this->session->set_flashdata($flashdata);
			redirect('partner/numberConfig');
			}else{
				$flashdata = array('msgt' => 'error', 'msg' => 'landingNumber Already exists');
				$this->session->set_flashdata($flashdata);
				redirect('partner/numberConfig');
			}
		}
		$data=array('businessUsers'=>$this->pmodel->get_businessusers(),
					'landingnumber'=>$this->pmodel->getLandingNumber(),
					 'action1'=>'partner/AddLandingNumber',
					 'action'=>'partner/numberConfig',
					 'popaction'=>'partner/AssignMobileNumberPopup'		
					);
		$this->pmodel->viewLayout('number_config',$data);
	}
	
	function AssignMobileNumberPopup($number=''){
		$data=array('arr'=>array(),'id'=>'','file' => "system/application/js/group.js.php",
					'dropdown'=>$this->pmodel->PriList_Auto()
		);
		$this->load->view('landingnumber1',$data);
	}
	function AddLandingNumber(){
		$res=$this->pmodel->MobilenumberConfig();
		if($res!=""){
			echo $this->pmodel->getLandingNumber1();
		}else{
			echo "Error";
		}
	}
	function editpri($pri)
	{
		
		if($this->input->post('submit')){
			$res=$this->pmodel->numberConfig($pri);
			
			if($res){
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', "Landing number updated successfully");
				redirect('partner/managePriList');
				
			}else{
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', "Landing number is already in use");
				redirect('partner/managePriList');
				
			}
			
		}
		
		$data=array('businessUsers'=>$this->pmodel->get_businessusers(),
					'selectedlist'=>$this->pmodel->getPridetails($pri),
					'action'=>'partner/editpri/');
		$this->load->view('number_config1',$data);
	}
	function delete_pri($pri){
		$err=$this->pmodel->Prinumber_del($pri);	
		
		if($err=="err"){
				$flashdata = array('msgt' => 'error', 'msg' => 'Error-Deleting PriNumber assigned to Business');
				$this->session->set_flashdata($flashdata);
				redirect('partner/managePriList');
		}else{
				$flashdata = array('msgt' => 'success', 'msg' => 'Prinumber Successfully Deleted');
				$this->session->set_flashdata($flashdata);
				redirect('partner/managePriList');
			
		}
	}
	function AssignMobileNumber($number=''){

		if($this->input->post('submit')){
			if($number!=''){
				$res=$this->pmodel->MobilenumberConfig($number);
				$flashdata = array('msgt' => 'success', 'msg' => 'landingNumber Updated Successfully');
						$this->session->set_flashdata($flashdata);
					redirect('partner/ManageunassignedNumbers');
			}else{	
					$res=$this->pmodel->MobilenumberConfig();
					if($res){				
						$flashdata = array('msgt' => 'success', 'msg' => 'landingNumber Added Successfully');
						$this->session->set_flashdata($flashdata);
					redirect('partner/AssignMobileNumber');
					}else{
						$flashdata = array('msgt' => 'error', 'msg' => 'landingNumber Already exists');
						$this->session->set_flashdata($flashdata);
						redirect('partner/AssignMobileNumber');
					}
				 }	
		}
		$data=array('arr'=>array(),'id'=>'','dropdown'=>$this->pmodel->PriList_Auto(),'add'=>'0','action'=>'partner/AssignMobileNumber/');
		$this->pmodel->viewLayout('landingnumber',$data);
	}
	function AssignMobileNumber_business($bid=''){
		if($this->input->post('submit')){
				$res=$this->pmodel->MobilenumberConfig();
				$res=$this->pmodel->numberConfig('',$bid);
			if($res!=0){				
				$flashdata = array('msgt' => 'success', 'msg' => 'landingNumber Added Successfully');
				$this->session->set_flashdata($flashdata);
			redirect('partner/numberConfig');
			}else{
				$flashdata = array('msgt' => 'error', 'msg' => 'landingNumber Already exists');
				$this->session->set_flashdata($flashdata);
				redirect('partner/numberConfig');
			}	
				
		}
		$data=array('arr'=>array(),'id'=>'','dropdown'=>$this->pmodel->PriList_Auto(),'add'=>'0','action'=>'partner/AssignMobileNumber_business/'.$bid);
		$this->load->view('landingnumber',$data);
	}
	function ManageunassignedNumbers($page='')
	{
		$data['module']['title'] ="Manage UnAssinged Numbers";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array('#'
						,$this->lang->line('level_Prinumbers')
						,$this->lang->line('level_landingnumber')
						,$this->lang->line('level_Action')
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->pmodel->manageUnassignedNumbers($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		($ofset!=0)?$i=$ofset+1:$i=1;
		foreach ($emp_list['data'] as $item){
			$rec[] = array(
				$i,
				$item['pri']
				,$item['number']
				,'<a href="'.site_url('partner/delete_unassigned/'.$item['number']).'" class="confirm" id="'.$item['number'].'" title="">
							<span title="Delete" class="glyphicon glyphicon-trash"></span>
				  </a>
					<a href="'.site_url('partner/EditUnassignedNumber/'.$item['number']).'"  class="btn-danger" data-toggle="modal" data-target="#modal-responsive">
						<span title="Edit" class="fa fa-edit"></span>
				  </a>'
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		
		//$data['itemlist'] = $this->groupmodel->getgrouplist($bid,$ofset,$limit);
		
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Masteradmin/ManageunassignedNumbers/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		//$data['addlinks']="group/add_group";		
		$data['paging'] = $this->pagination->create_links();
		//$data['paging'] =  pagination($limit, $page, $start, $total_pages, $targetpage);
		$this->pmodel->data['html']['title'] .= " | Manage UnAssinged Numbers";
	    $links =array();
		$links[]='<li><a href="partner/AssignMobileNumber/"><span class="glyphicon glyphicon-plus-sign" title="Add Number">Add Number</span></a></li>';
		$formFields1 = array();
		$cf=array('label'=>'<label for="groupname">'.$this->lang->line('level_groupname').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'groupname',
										'id'          => 'groupname',
										'value'       => $this->session->userdata('groupname'))));
						array_push($formFields1,$cf);
						
		$this->mastermodel->data['links'] = '<a href="group/add_group"><span class="glyphicon glyphicon-plus-sign" title="Add Number"></span></a>';
		//$fieldset = $this->configmodel->getFields('3');
		$formFields = array();
		$formFields[] = array(
				'label'=>'<label for="f">PriNumber : </label>',
				'field'=>form_input(array(
						'name'      => 'prinumber',
						'id'        => 'prinumber',
						'value'     => $this->session->userdata('prinumber')
						))
						);
		$formFields[] = array(
				'label'=>'<label for="f">Landing Number : </label>',
				'field'=>form_input(array(
						'name'      => 'landing_number',
						'id'        => 'landing_number',
						'value'     => $this->session->userdata('landing_number')
						))
						);
	    $data['downlink'] = $dlink;	
		$data['links'] = $links;
		$data['form'] = array(
							'open'=>form_open_multipart('partner/ManageunassignedNumbers/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
							'form_field'=>$formFields,
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		$this->pmodel->viewLayout('list_view',$data);
	}
	function EditUnassignedNumber($number){
		$data=array('arr'=>$this->pmodel->getUnassingedInfo($number),
					 'id'=>$number,'add'=>'0','action'=>'partner/AssignMobileNumber/','dropdown'=>$this->pmodel->PriList_Auto());
		$this->load->view('landingnumber',$data);
	}
	function delete_unassigned($number){
		$r=$this->pmodel->Delete_UnassignedNumbers($number);
		$flashdata = array('msgt' => 'success', 'msg' => 'Deleted Successfully');
			$this->session->set_flashdata($flashdata);
			redirect('partner/ManageunassignedNumbers');
		
	}
	function passwordreset($bid){
		$res=$this->pmodel->password_reset($bid);
		if($res){
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg',"Password reseted Successfully");
			redirect('partner/Userslist');
			
		}else{
			$this->session->set_flashdata('msgt', 'error');
			$this->session->set_flashdata('msg', "Error While Resting the Passsword");
			redirect('partner/Userslist');
			
		}
	}
}
/* end of master admin controller */

