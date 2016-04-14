<?php
class User extends Controller {
	var $data;
	function User(){
		parent::Controller();
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('systemmodel');
		$this->load->model('commonmodel');
		$this->load->model('empmodel');
		$this->load->model('profilemodel');
		$this->load->helper('url');
		$this->load->library('validation');
		$this->load->library('form_validation');
	}
	function index(){
		redirect('/');
	}
	function get_relemps($id){
		$option='';
			$option.='<option value="">--Select--</option>';
		if($id!=1){
			$resS=$this->commonmodel->get_executives();
			foreach($resS as $res){
				$option.='<option value="'.$res['id'].'">'.$res['empname'].'</option>';
			}
		}else{
			$resS=$this->commonmodel->get_partner();
			foreach($resS as $res){
				$option.='<option value="'.$res['partner_id'].'">'.$res['firstname'].'</option>';
			}
		}
		echo $option;
	}
	function listlan(){
		$lang = $this->profilemodel->get_languages();
		$select = '<select name="language" id="language" class="required formfield">';
		foreach($lang as $k => $v) $select .= '<option value="'.$k.'">'.$v	.'</option>';
		$select .= '</select>';
		echo $select;
	}
	function login(){
		$data = array(
			'title' 		=> "MCube is hosted, state of the art marketing, call tracking, IVRS, QR Tracking solution.",
			'keywords'		=> "Cloud, IVR, Virtual Call Tracking, Call Tracking, QR Codes, QR Tracking, Dynamic QR, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			'description'	=> "MCube is hosted, state of the art marketing, call tracking, IVRS, QR Tracking solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS, Trackable QR Codes and reporting & analytics. The APIs from the solutions also allow applications to communicate and engage with the clients."			
		);
		$this->form_validation->set_rules('login_username', 'username', 'required|min_length[4]|max_length[256]|valid_email');
		$this->form_validation->set_rules('login_password', 'Password', 'required|min_length[4]|max_length[200]');
		$this->form_validation->set_rules('otp', 'OTP', 'required|callback_check_otp');
		//$this->form_validation->set_rules('validator', 'captcha', 'required|callback_check_captcha');
		if ($this->form_validation->run() == FALSE)	{
			$flashdata = array('msgt' => 'error', 'msg' => validation_errors());
			$this->session->set_flashdata($flashdata);
			redirect('/site/otp');
		}else{
			$rs=$this->simplelogin->login($this->input->post('login_username'), $this->input->post('login_password'));
			if($rs) {
				$this->session->set_userdata('roleid',$this->empmodel->getRoleid($this->session->userdata('eid')));
				redirect('Home');	
			} else {
				if($this->session->userdata('flash:new:msg')==""){
					$flashdata = array('msgt' => 'error', 'msg' => 'Invaild Username/Password');
					$this->session->set_flashdata($flashdata);		
					redirect('/site/login');
				}else{
					redirect('/site/login');
				}
			}	
			
		}
	}
	function otp(){
		$data = array(
			'title' 		=> "MCube is hosted, state of the art marketing, call tracking, IVRS, QR Tracking solution.",
			'keywords'		=> "Cloud, IVR, Virtual Call Tracking, Call Tracking, QR Codes, QR Tracking, Dynamic QR, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			'description'	=> "MCube is hosted, state of the art marketing, call tracking, IVRS, QR Tracking solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS, Trackable QR Codes and reporting & analytics. The APIs from the solutions also allow applications to communicate and engage with the clients."			
		);
		$this->form_validation->set_rules('login_username', 'username', 'required|min_length[4]|max_length[256]|valid_email');
		$this->form_validation->set_rules('login_password', 'Password', 'required|min_length[4]|max_length[200]');
		$this->form_validation->set_rules('validator', 'captcha', 'required|callback_check_captcha');
		if ($this->form_validation->run() == FALSE)	{
			$flashdata = array('msgt' => 'error', 'msg' => validation_errors());
			$this->session->set_flashdata($flashdata);
			redirect('/site/login');
		}else{
			if($this->simplelogin->login($this->input->post('login_username'), $this->input->post('login_password'))) {
				$sql = "SELECT * FROM business WHERE bid='".$this->session->userdata('bid')."'" ;
				$sql1 = "SELECT  * FROM  `business_feature` WHERE feature_id =17 AND bid='".$this->session->userdata('bid')."'";
			
				$feature_id = $this->db->query($sql1)->row()->feature_id;
				$otpType = $this->db->query($sql)->row()->otp;
		  if($feature_id == '17'){
				if($otpType=='0'){ 
					$this->session->set_userdata('roleid',$this->empmodel->getRoleid($this->session->userdata('eid')));
					redirect('Home');
				}
				$bid = $this->session->userdata('bid');
				$eid = $this->session->userdata('eid');
				$this->simplelogin->logout();
				$this->session->sess_create();
				$this->session->set_userdata('u',$this->input->post('login_username'));
				$this->session->set_userdata('p',$this->input->post('login_password'));
				$sql = "SELECT * FROM ".$bid."_employee WHERE eid='".$eid."'";
				$emp = $this->db->query($sql)->row_array();
				$otp = "";for($i = 0; $i<=6 ; $i++){$otp .= ($i%2==0)? strtoupper(chr(rand(97,122))) : rand(0,9);}
				$this->session->set_userdata('otp',$otp);
				$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
				$message = "\nYour one time passwod for MCube is: ".$otp;
				$sms = $api."&to=".substr($emp['empnumber'],-10,10)."&text=".urlencode($message);
				$sms = file($sms);
				$body=$this->emailmodel->newEmailBody($message,$emp['empname']);
				$to = $emp['empemail'];
				$subject = "MCube Login OTP";
				$this->load->library('email');
				$this->email->from('noreply@mcube.com', 'MCube');
				$this->email->to($to);
				$this->email->subject($subject);
				$this->email->message($body);
				$this->email->send();
				MCubeMail($to,$subject,$body);
				redirect('/site/otp');
		
				}else {
				if($this->session->userdata('flash:new:msg')==""){
					$flashdata = array('msgt' => 'error', 'msg' => 'Invalid Username/Password');
					$this->session->set_flashdata($flashdata);		
					redirect('/site/Accessdenied');
					
				}
			}
			}else {
				if($this->session->userdata('flash:new:msg')==""){
					$flashdata = array('msgt' => 'error', 'msg' => 'Invalid Username/Password');
					$this->session->set_flashdata($flashdata);		
					redirect('/site/login');
				}
			}	
		}
	}
	function logout(){
		$this->simplelogin->logout();
		redirect('/');
	}
	function deregister($id,$bid){
		$enc_id=base64_decode($id);
		$bid=base64_decode($bid);
		if($this->commonmodel->EmployeeDeregister($enc_id,$bid)){
				$r=$this->commonmodel->get_busineeprofiledetails($bid);
				$bname=base64_encode($r[0]['businessname']);
				redirect('site/deactivated/'.$bname);
			}else{
				redirect('site/error');
			}
	}	
	function Employeeregister($id,$bid){
		$enc_id=base64_decode($id);
		$bid=base64_decode($bid);
		$emp_d=$this->commonmodel->get_empDummy($enc_id,$bid);
		$return=$this->commonmodel->EmployeeAdd($emp_d,$bid);
		if($return==2){
				redirect('verifyYourNumber');
		}else if($return==1){
			redirect('site/thanks');
		}else{
			redirect('site/error');
		}
	}
	function register()	{
		$data = array(
			'title' 		=> "MCube is hosted, state of the art marketing, call tracking, IVRS, QR Tracking solution.",
			'keywords'		=> "Cloud, IVR, Virtual Call Tracking, Call Tracking, QR Codes, QR Tracking, Dynamic QR, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			'description'	=> "MCube is hosted, state of the art marketing, call tracking, IVRS, QR Tracking solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS, Trackable QR Codes and reporting & analytics. The APIs from the solutions also allow applications to communicate and engage with the clients."			
		);
		$this->form_validation->set_rules('otp', 'OTP', 'required|callback_check_otp');
		if ($this->form_validation->run() == FALSE){
		//print_r($this->session->all_userdata());
			$this->load->view('siteheader',$data);
			//if($_SERVER['REMOTE_ADDR']=='122.167.120.138'){
				$this->load->view('regotp');
			//}
			$this->load->view('footer');
		}else{
			$check_domain=$this->commonmodel->Check_Domain();
			if($check_domain!=0){
				$_POST = $this->session->userdata('post');
				$res=$this->commonmodel->register($check_domain,'1');
				if($res){
					if($this->simplelogin->login($this->input->post('login_username'), $this->input->post('login_password'))) {
					$this->session->set_userdata('roleid',$this->empmodel->getRoleid($this->session->userdata('eid')));
					$message=$this->commonmodel->Email_Header();
					$message=$this->commonmodel->MiddleMessage();
					$config['protocol'] = 'mail';
					$config['wordwrap'] = FALSE;
					$config['mailtype'] = 'html';
					$this->email->initialize($config);
					$this->email->from('noreply@mcube.com','MCube');
					$this->email->to('support@vmc.in');
					//$this->email->to('sundeep.misra@vmc.in');
					$this->email->bcc('raj.m@vmc.in,tapan.chatterjee@vmc.in');
					$this->email->subject('MCube Registration Details');
					$data['mailing']=$message;
					$msg=$this->load->view('sendMail',$data,true);
					
					$this->email->message($this->outlookfilter($msg));
					$this->email->send();
					redirect('ManageCustom');	
					
					} else {
						redirect('/site/login');
					}	
				}				
			}else{
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', "Domian Not Availble");
				redirect('user/actregister');
			}
		}
	}
	function actregister()	{

		//if(!in_array($_SERVER['REMOTE_ADDR'],array('182.72.110.206','180.151.5.36'))) header("Location: /");
		$data = array(
			'title' 		=> "MCube is hosted, state of the art marketing, call tracking, IVRS, QR Tracking solution.",
			'keywords'		=> "Cloud, IVR, Virtual Call Tracking, Call Tracking, QR Codes, QR Tracking, Dynamic QR, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			'description'	=> "MCube is hosted, state of the art marketing, call tracking, IVRS, QR Tracking solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS, Trackable QR Codes and reporting & analytics. The APIs from the solutions also allow applications to communicate and engage with the clients."
		);
		$this->form_validation->set_rules('login_businessname', 'Business Name', 'required|min_length[4]|max_length[50]|alpha_numeric');
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
		$this->form_validation->set_rules('login_password', 'Password', 'required|min_length[5]|max_length[10]');
		$this->form_validation->set_rules('cpassword', 'Confirm Password', 'required|min_length[5]|max_length[10]|matches[login_password]');
		$this->form_validation->set_rules('waddress', 'Website Address', 'valid_url');
		$this->form_validation->set_rules('validator', 'captcha', 'required|callback_check_captcha');
		if ($this->form_validation->run() == FALSE){
			$this->load->view('siteheader',$data);
				$this->load->view('actregistration');
			$this->load->view('footer');
		}else{
			$check_domain=$this->commonmodel->Check_Domain();
			if($check_domain!=0){
				$res=$this->commonmodel->register($check_domain);
				if($res){
					if($this->simplelogin->login($this->input->post('login_username'), $this->input->post('login_password'))) {
						$this->session->unset_userdata('roleid');
						$this->session->set_userdata('roleid',$this->empmodel->getRoleid($this->session->userdata('eid')));
						redirect('ManageCustom');	
					} else {
						redirect('/site/login');
					}	
				}				
			}else{
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', "Domian Not Availble");
				redirect('user/actregister');
			}
		}
	}
	function adduser(){	
		if($this->input->post('Adduser')){
			//print_r($_POST);exit;
			$res=$this->commonmodel->insert_user();
			if($res){
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', $this->lang->line('error_usersuccmsg'));
				redirect('user/adduser');
				}
		}
		$user_type=$this->commonmodel->get_usertype();
		//print_r($user_type);
		$data=array('usertypes'=>$user_type,
					'groups'=> $this->profilemodel->get_groups(),
					 'employees'=>$this->profilemodel->get_employee_login()	
		
			);
		$this->sysconfmodel->viewLayout('adduser',$data);
	}
	function check_Domain(){
		echo "Domian";exit;
	}
	function username_check($str){
		if($this->commonmodel->userexists($str)=="user exists"){
		$this->form_validation->set_message('username_check', 'The '.$str.' email is not available');
			return FALSE;
		}else{
			return TRUE;
		}
		
	}
	function check_username(){
		$check=$this->commonmodel->check_userexistence();
		if($check){
			echo 1;
		}else{
			echo 0;
		}
	}
	
	function check_captcha($str){
		if($str!=$_SESSION['security_code']){
		$this->form_validation->set_message('check_captcha', 'The '.$str.' is not valid security code');
			return FALSE;
		}else{
			return true;
		}
	}
	
	function check_otp($str){
		//echo $str."===".$this->session->userdata('otp');exit;
		if($str!=$this->session->userdata('otp')){
		$this->form_validation->set_message('check_otp', '"'.$str.'" is not valid otp');
			return FALSE;
		}else{
			return true;
		}
	}
	
	function manage(){
		$users=$this->commonmodel->get_users_details();
		$data=array('users'=>$users);
		$this->sysconfmodel->viewLayout('manageusers',$data);
	}
	function status_change($id){
		$res=$this->commonmodel->status_change($id);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', $this->lang->line('err_up_msg'));
		redirect('user/manage');
	}
	function delete_user($id){
		$res=$this->commonmodel->delete_user($id);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', $this->lang->line('error_deletesuccmsg'));
		redirect('user/manage');
	}
	function register_process(){
		if($this->input->post('submit'))
		{
			$res=$this->commonmodel->dummy_register();
			
			redirect('user/process_next/'.$res);

		}
		$this->data['html']['title'] .= " | ".$this->lang->line('user_login');
		$this->load->view('registration_process',$this->data);
	}
	function process_next($id=''){
		if($id!=''){
			if($this->input->post('proceed')){
				$this->confirmation($id);
				exit;
			}			
			$this->data['html']['title'] .= " | ".$this->lang->line('user_login');
			$this->data['html']['packages']= $this->commonmodel->get_packages();
			$this->paypal_lib->add_field('business', 'chatte_1217057572_biz@gmail.com');
			$this->paypal_lib->add_field('return', site_url('user/success/'.$id));
			$this->paypal_lib->add_field('cancel_return', site_url('user/cancel/'.$id));
			$this->paypal_lib->image('button_01.gif');
			$this->data['id']=$id;
			$this->data['paypal_form'] = $this->paypal_lib->paypal_form();
			$this->load->view('package_process',$this->data);
		}
	}
	function confirmation($id){		
		$this->data['posted']=$_POST;
		$this->paypal_lib->add_field('business', 'chatte_1217057572_biz@gmail.com');
		$this->paypal_lib->add_field('return', site_url('user/success/'.$id));
		$this->paypal_lib->add_field('cancel_return', site_url('user/cancel/'.$id));
		$this->paypal_lib->image('button_01.gif');
		$this->data['id']=$id;
		$this->data['paypal_form'] = $this->paypal_lib->paypal_form();
		$this->load->view('confirmation',$this->data);
	}
	function cancel($id){
		$this->commonmodel->delete_record($id);
		redirect('user/register_process');
	}
	function success($id){
		echo "<pre>";
		print_r($_REQUEST);
		exit;
		if($this->input->post('submit')){
			$res=$this->commonmodel->register();
			if($res){
				if($this->simplelogin->login($this->input->post('login_username'), $this->input->post('login_password'))) {
					$this->session->set_userdata('roleid',$this->empmodel->getRoleid($this->session->userdata('eid')));
					redirect('ManageCustom');	
				} else {
				redirect('/user');			
				}		
			}
		}
		$res=$this->commonmodel->update_registerinfo($id);
		$this->data['html']['language']=$this->profilemodel->get_languages();
		array_push($this->data['html'],$this->data['html']['language']);
		$this->data['dummy_values']=$res;
		$this->data['package_id']=$id;
		$this->load->view('register',$this->data);
	}
	
	function forgetpass(){
		$this->form_validation->set_rules('login_username', 'Username', 'required|min_length[4]|max_length[250]|valid_email');
		$this->form_validation->set_rules('validator', 'Captcha', 'required|callback_check_captcha');
		if ($this->form_validation->run() == FALSE)	{
			redirect('/site/forgetpass');
		}else{
			$user = $this->commonmodel->getUserByEmail($_POST['login_username']);
			if($user['uid']>0){
			$emp = $this->db->query("SELECT * FROM ".$user['bid']."_employee WHERE eid='".$user['eid']."'")->row_array();
			
			if($user['uid']>0 && $emp['login']=='1' && $emp['status']=='1'){
				$newPass = "";for($i = 0; $i<=10 ; $i++){$newPass .= ($i%2==0)? chr(rand(97,122)) : rand(0,9);}
				$sql = "UPDATE user SET password='".md5($newPass)."' WHERE uid='".$user['uid']."'";
				$this->db->query($sql);
				
				$to = $_POST['login_username'];
				$subject = "MCube Password";
				$message = "\n\n\tYour Password has been reset.\n".
							"New Password is: ".$newPass;
				$body=$this->emailmodel->newEmailBody($message,$emp['empname']);		
							
				//~ $headers  = 'MIME-Version: 1.0' . "\n";
				//~ $headers .= 'Content-type: text/html; charset=UTF-8' . "\n";
				//~ $headers .= 'To: '.$emp['empname'].'<'.$_POST['login_username'].'>' . "\n";
				//~ $headers .= 'From: MCube <noreply@mcube.com>' . "\n";
				//~ $headers .= 'Bcc: tapan.chatterjee@indopia.com,amit.singh@vmc.in' . "\n";
				//~ $mail = mail($to, $subject, $body, $headers);
				$this->load->library('email');
				$this->email->from('noreply@mcube.com', 'MCube');
				$this->email->to($to);
				$this->email->subject($subject);
				$this->email->message($body);
				$this->email->send();
				
				$message="Dear ".$emp['empname'].$message;
				$message.="\n\nRegards\nMCube Team";
				
				$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
				$message = str_replace("\n"," ",str_replace("\t","",$message));
				$sms = $api."&to=".$emp['empnumber']."&text=".urlencode($message);
				$sms = file($sms);
				
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', 'Your password is reset and send to your email');
				redirect('site/login');
			}}else{
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', 'Invalid username contact your admin');
				redirect('site/login');
			}
		}
	}
	
	function validateCaptcha(){
		$capthaCode=$_POST['fieldValue'];
		$validateId=$_POST['fieldId'];
		$validateError= "Invalid Captcha Code";
		$validateSuccess= "Valid Captcha Code";
		/* RETURN VALUE */
		$arrayToJs = array();
		$arrayToJs[0] = $validateId;

		if($capthaCode ==$_SESSION['security_code']){		// validate??
			$arrayToJs[1] = true;			// RETURN TRUE
			echo json_encode($arrayToJs);			// RETURN ARRAY WITH success
		}else{
			for($x=0;$x<1000000;$x++){
				if($x == 990000){
					$arrayToJs[1] = false;
					echo json_encode($arrayToJs);		// RETURN ARRAY WITH ERROR
				}
			}
		}
	}
	function checkEmail(){
		$email=$_POST['fieldValue'];
		$validateId=$_POST['fieldId'];
		$arrayToJs = array();
		$arrayToJs[0] = $validateId;
		$clients=$this->db->query("select bid from business")->result_array();
		$ret = true;
		if(!empty($clients)){
			foreach($clients as $client){
				$sql = $this->db->query("SELECT * FROM ".$client['bid']."_employee WHERE empemail='".$email."'");
				if($sql->num_rows($sql)==0){
					$ret = false;
					break;
				}
			}
	
		}
		if(!$ret){
			$arrayToJs[1] = true;			// RETURN TRUE
			echo json_encode($arrayToJs);
		}else{
			for($x=0;$x<1000000;$x++){
				if($x == 990000){
					$arrayToJs[1] = false;
					echo json_encode($arrayToJs);		// RETURN ARRAY WITH ERROR
				}
			}
			
		}
	}
	function assistance(){
		$config['protocol'] = 'mail';
		$config['wordwrap'] = FALSE;
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from('noreply@mcube.com','MCube');
		$this->email->to('sundeep.misra@metrixindia.com');
		$this->email->to('sales@vmc.in');
		$this->email->to('support@vmc.in');
		$this->email->subject('MCube query ');
		$data['mailing']=$this->commonmodel->assistance_insert();
		$msg=$this->load->view('sendMail',$data,true);
		$this->email->message($this->outlookfilter($msg));
		$this->email->send();
		$leadAPI = "http://mcube.vmc.in/api/prospects_api?username=".urlencode('leads@vmc.in')."&password=vmc123&group=".urlencode('Webenquires')."&assignto=auto&source=".urlencode('Need Assistance')."&name=".urlencode($name)."&number=".$phone."&email=".urlencode($emailAddr)."&remark=".urlencode($comment)."&duplicate=FALSE";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$leadAPI);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($ch);
		curl_close($ch);
		redirect('site/thankspage');
	}
	function careers(){
		$this->form_validation->set_rules('frstname','First Name', 'required|min_length[4]|max_length[32]|alpha_numeric');
		$this->form_validation->set_rules('lstname','Last Name', 'required|min_length[4]|max_length[32]|alpha_numeric');
		$this->form_validation->set_rules('mobnumber','Mobile Number', 'required|min_length[4]|max_length[12]|numeric');
		$this->form_validation->set_rules('landline','LandLine Number', 'min_length[4]|max_length[12]|numeric');
		$this->form_validation->set_rules('cemail','Email Address', 'required|min_length[4]|max_length[50]|valid_email');
		$this->form_validation->set_rules('expertise','Expertise', 'required|min_length[4]|max_length[12]|alpha_numeric');
		if ($this->form_validation->run() == FALSE){
			$this->load->view('siteheader');
			$this->load->view('careers');
			$this->load->view('footer');
		}else{			
			if($_FILES['resume']['type']=="doc"||$_FILES['resume']['type']=="docx"||$_FILES['resume']['type']=="odt"||$_FILES['resume']['type']=="application/msword"){
				$res=$this->commonmodel->careers();
				$message=$this->commonmodel->MailMiddleMessage();
				$config['protocol'] = 'mail';
				$config['wordwrap'] = FALSE;
				$config['mailtype'] = 'html';
				$this->email->initialize($config);
				$this->email->from('noreply@mcube.com','MCube');
				$this->email->to('sundeep.misra@metrixindia.com');
				$this->email->subject('MCube Career Information');
				$data['mailing']=$message;
				$msg=$this->load->view('sendMail',$data,true);
				$this->email->attach($this->config->item('career_path').$res);
				$this->email->message($this->outlookfilter($msg));
				$this->email->send();
				redirect('site/careers');
				
			}else{
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', 'Upload file is invalid');
				redirect('site/careers');
			}
		}
	}
	function outlookfilter($text){    
        $text = str_replace("<br />","<br>",$text);
        $text = str_replace("&nbsp;","",$text);
        $text = str_replace("&#39;","'",$text);
        return $text;
	} 
	function changepassword(){
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('oldpass', 'Current Password', 'required|min_length[4]|max_length[32]');
			$this->form_validation->set_rules('newpass', 'New Password', 'required|min_length[4]|max_length[32]');
			$this->form_validation->set_rules('conpass', 'Confirm Password', 'required|min_length[4]|max_length[32]|matches[newpass]');
			if(!$this->form_validation->run()== FALSE){	
				$this->profilemodel->changePassword();
				$eid=$this->session->userdata('eid');
				$sql = "SELECT * FROM ".$this->session->userdata('bid')."_employee WHERE eid='".$eid."'";
				$emp = $this->db->query($sql)->row_array();
				$message="Your Password has Been Successfully Changed,Your New Password is ".$this->input->post('newpass')."\n if you have not changed,please contact adminstrator";
				$body=$this->emailmodel->newEmailBody($message,$emp['empname']);
				$to = $emp['empemail'];
				$subject = "MCube New Password";
				$this->load->library('email');
				$this->email->from('noreply@mcube.com', 'MCube');
				$this->email->to($to);
				$this->email->subject($subject);
				$this->email->message($body);
				$s=$this->email->send();
				redirect('user/changepassword');
			}		
		}
		$data['module']['title'] = $this->lang->line('label_changepassword');
		$formFields = array();
		$formFields[] = array('label'=>'<label  class="col-sm-4 text-right">Current Password : </label>',
					'field'=>form_input(array(
								  'name'      => 'oldpass',
								  'id'        => 'oldpass',
								  'class'	  => 'required form-control',	
								  'type'	  => 'password'
						)));
		$formFields[] = array('label'=>'<label  class="col-sm-4 text-right">New Password : </label>',
					'field'=>form_input(array(
								  'name'      => 'newpass',
								  'id'        => 'newpass',
								   'class'	  => 'required form-control',	
								  'type'	  => 'password'
						)));
		$formFields[] = array('label'=>'<label  class="col-sm-4 text-right">Confirm Password : </label>',
					'field'=>form_input(array(
								  'name'      => 'conpass',
								  'id'        => 'conpass',
								   'class'	  => 'required form-control',	
								  'type'	  => 'password'
						)));

		$data['form'] = array(
		            'form_attr'=>array('action'=>'user/changepassword','name'=>'changepassword','id'=>'changepassword','enctype'=>"multipart/form-data"),
					//~ 'open'=>form_open_multipart('user/changepassword',array('name'=>'changepassword','id'=>'changepassword','class'=>'form','method'=>'post')),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	} 	
	function mobile(){
		$data = array(
			'title' 		=> "MCube is hosted, state of the art marketing, call tracking, IVRS, QR Tracking solution.",
			'keywords'		=> "Cloud, IVR, Virtual Call Tracking, Call Tracking, QR Codes, QR Tracking, Dynamic QR, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			'description'	=> "MCube is hosted, state of the art marketing, call tracking, IVRS, QR Tracking solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS, Trackable QR Codes and reporting & analytics. The APIs from the solutions also allow applications to communicate and engage with the clients."			
		);
		$this->form_validation->set_rules('mobilenumber', 'Mobile Number', 'required|min_length[4]|max_length[256]');
		$this->form_validation->set_rules('vcode', 'Verification Code', 'required|min_length[4]|max_length[200]');
		if ($this->form_validation->run() == FALSE)	{
			$this->load->view('siteheader',$data);
			$this->load->view('mobile');
			$this->load->view('footer');	
		}else{
			$res=$this->commonmodel->check_mobilecode();
			if($res!=""){
				$flashdata = array('msgt' => 'success', 'msg' => 'Mobile verification done');
				$this->session->set_flashdata($flashdata);		
				redirect('/site/mobile');
				
			}else{
				$flashdata = array('msgt' => 'error', 'msg' => 'Invalid verification Code');
				$this->session->set_flashdata($flashdata);		
				redirect('/site/mobile');
				
			}
		}
	}
	function usertest(){
		$this->load->view('emailTemp');
	}
	function registerotp(){
		$data = array(
			'title' 		=> "MCube is hosted, state of the art marketing, call tracking, IVRS, QR Tracking solution.",
			'keywords'		=> "Cloud, IVR, Virtual Call Tracking, Call Tracking, QR Codes, QR Tracking, Dynamic QR, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			'description'	=> "MCube is hosted, state of the art marketing, call tracking, IVRS, QR Tracking solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS, Trackable QR Codes and reporting & analytics. The APIs from the solutions also allow applications to communicate and engage with the clients."			
		);
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
		$this->form_validation->set_rules('login_password', 'Password', 'required|min_length[5]|max_length[10]');
		$this->form_validation->set_rules('cpassword', 'Confirm Password', 'required|min_length[5]|max_length[10]|matches[login_password]');
		$this->form_validation->set_rules('waddress', 'Website Address', 'valid_url');
		$this->form_validation->set_rules('validator', 'captcha', 'required|callback_check_captcha');
		if ($this->form_validation->run() == FALSE){
			redirect('user/register');
		}else{
			$this->session->set_userdata('post',$_POST);
			
			$otp = "";for($i = 0; $i<=6 ; $i++){$otp .= ($i%2==0)? strtoupper(chr(rand(97,122))) : rand(0,9);}
			$this->session->set_userdata('otp',$otp);
			
			//print_r($rec);exit;
			$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
			$message = "\nYour one time passwod for MCube Registration is: ".$otp;
			$body=$this->emailmodel->newEmailBody($message,$_POST['cname']);
			$sms = $api."&to=".substr($_POST['cphone'],-10,10)."&text=".urlencode($message);
			$sms = file($sms);
			
			$to = $emp['empemail'];
			$subject = "MCube Registration OTP";
			//~ $headers  = 'MIME-Version: 1.0' . "\n";
			//~ $headers .= 'Content-type: text/html; charset=UTF-8' . "\n";
			//~ $headers .= 'To: '.$_POST['cname'].'<'.$_POST['cemail'].'>' . "\n";
			//~ $headers .= 'From: MCube <noreply@mcube.com>' . "\n";
			//~ //$headers .= 'Bcc: tapan.chatterjee@indopia.com' . "\n";
			//~ $mail = mail($to, $subject, $body, $headers);
			
			$this->load->library('email');
			$this->email->from('noreply@mcube.com', 'MCube');
			$this->email->to($to);
			$this->email->subject($subject);
			$this->email->message($body);
			$this->email->send();
			
			redirect('/site/regotp');
		}	
	}
	function getSubcats(){
		if( $this->input->post('category') != 'select' && $this->input->post('category') != ''){
			$category  = $this->input->post('category');
			$catDetails = $this->commonmodel->getFCatDetails($category);
			$dat = '';
			$cats = $catDetails[0];
			if($cats['type'] != '' && $cats['subcategory'] != ''){
				$type = $cats['type'];
				$label = $cats['label'];
				$subcats = @explode('<br />',$cats['subcategory']);
				$dat = '<label for="contactable-dropdown" style="font-size:10px;">'.$label.'<span class="contactable-green"> * </span> </label><br />';
				switch($type){
					case 'radio':
						for($k=0;$k<count($subcats);$k++){
							$dat .= "<input type='radio' name='subCategory' id='contactable-subcategory' value='".trim($subcats[$k])."' /> <span style='font-size:10px;vertical-align:top;padding-right:20px;'>".$subcats[$k]."</span>";
						}
					break;
					case 'dropdown':
						$dat .= '<select name="subCategory" id="contactable-subcategory" style="font-size:10px;">';
						for($k=0;$k<count($subcats);$k++){
							$dat .= '<option value="$subcats[$k]">'.$subcats[$k].'</option>';
						}
					break;
					case 'checkbox':
						for($k=0;$k<count($subcats);$k++){
							$dat .= "<input type='checkbox' name='subCategory' value='".$subcats[$k]."' /> <span style='font-size:10px;vertical-align:top;padding-right:20px;'>".$subcats[$k]."</span>";
						}
				}
			}
			echo $dat;
		}

	}
	function feedbackData(){
		$name = stripcslashes($_POST['name']);
		$emailAddr = stripcslashes($_POST['email']);
		$phone = (isset($_POST['phone'])) ? stripslashes($_POST['phone']) : '';
		$issue = (isset($_POST['category']) && $_POST['category'] != 'Select') ?  stripcslashes($_POST['category']) : '';
		$subcategory = (isset($_POST['subcategory'])) ? stripslashes($_POST['subcategory']) : '';
		$comment = stripcslashes($_POST['message']);
		$response=0;
		if($name!='' && $emailAddr!='' && $comment!=''){
			$subject = "Feedback ";
			$insertData = $this->commonmodel->fCategoryData();
			$to='support@vmc.in';
			$from='MCube <noreply@mcube.com>';
			$contactMessage =  
			"<div>
			<p><strong>Name:</strong> ".$name." <br />
			<strong>E-mail:</strong> ".$emailAddr." <br />
			<strong>Phone:</strong> ".$phone ."<br />
			<strong>Issue Catgeory:</strong> ".$issue ."<br />
			<strong>Issue Subcategory:</strong>". $subcategory ."</p>
			<p><strong>Message:</strong> ".$comment ."</p>
			<p><strong>Sending IP:</strong>". $_SERVER['REMOTE_ADDR']."<br />
			<strong>Sent via:</strong> ".$_SERVER['HTTP_HOST']."</p>
			</div>";
			$body=$this->emailmodel->newEmailBody($contactMessage," All");
			$this->load->library('email');
			$this->email->from('noreply@mcube.com', 'MCube');
			$this->email->to($to);
			$this->email->subject($subject);
			$this->email->message($body);
			$response = $this->email->send();
			$leadAPI = "http://mcube.vmc.in/api/prospects_api?username=".urlencode('leads@vmc.in')."&password=vmc123&group=".urlencode('Webenquires')."&assignto=auto&source=".urlencode('FeedBack')."&name=".urlencode($name)."&number=".$phone."&email=".urlencode($emailAddr)."&remark=".urlencode($comment)."&duplicate=FALSE";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$leadAPI);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$data = curl_exec($ch);
			curl_close($ch);
		}
		echo $response;
	}
	function verification_resend(){
		$res=$this->commonmodel->check_Mverification();
		echo $res;
	}
	function selfdisable($val,$eid=''){
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res = $this->empmodel->selfdisable($val,$eid);
		if($res ==  true){
			if($val == 0){
				$msg =  "You have disabled account, you will not receive the calls.";
				$auditMsg = " self disable - employee disabled By ";
			}else{
				$msg =  "You are available/online now";
				$auditMsg = "self disable - employee enabled  By ";
			}
			$flashdata = array('msgt' => 'success', 'msg' =>$msg);
			$this->session->set_flashdata($flashdata);	
			$this->auditlog->auditlog_info('Employee Module', $auditMsg." ".$this->session->userdata('username'));	
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$flashdata = array('msgt' => 'error', 'msg' => 'Invalid action ');
			$this->session->set_flashdata($flashdata);		
			redirect($_SERVER['HTTP_REFERER']);
		}
		
	}
}
?>
