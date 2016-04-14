<?php
class Site extends Controller {
	function Site(){
		parent::controller();
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
	}
	
	function index(){
		$this->login();
	}

	function login(){
		if($this->session->userdata('logged_in'))redirect('/Home');
		$data = array(
			'title' 		=> "Virtual Communications solution: Hosted PBX, Virtual PBX, call tracking, IVRS.",
			'keywords'		=> "Hosted IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Toll free phone numbers, Application Voice integration, Voice Portal,  Lead Tracking, Call Qualification, Missed Calls",
			'description'	=> "MCube is a Hosted Virtual Communication solution, This contains CallTrack, Virtual PBX, Hosted IVRS and Hosted Messaging solutions (Voice and Text)."
		);
		$this->load->view('siteheader1',$data);
		$this->load->view('login',$data);
		//$this->load->view('footer');
	}

	function Accessdenied(){
		$data = array(
			'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			'description'	=> "MCube ishosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients.",			
			'message'		=>'Feature disabled for MCube'			
			
		);
        $this->load->view('siteheader1',$data); 
		$this->load->view('fdisabled');
	}
	function otp(){
	//if($this->session->userdata('logged_in'))redirect('/dashboard');
		$data = array(
			'title' 		=> "Virtual Communications solution: Hosted PBX, Virtual PBX, call tracking, IVRS.",
			'keywords'		=> "Hosted IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Toll free phone numbers, Application Voice integration, Voice Portal,  Lead Tracking, Call Qualification, Missed Calls",
			'description'	=> "MCube is a Hosted Virtual Communication solution, This contains CallTrack, Virtual PBX, Hosted IVRS and Hosted Messaging solutions (Voice and Text)."
		);
	
	    $this->load->view('siteheader1',$data);
		$this->load->view('otp');
	}
	function regotp(){
		if($this->session->userdata('logged_in'))redirect('/Home');
		$data = array(
			'title' 		=> "Virtual Communications solution: Hosted PBX, Virtual PBX, call tracking, IVRS.",
			'keywords'		=> "Hosted IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Toll free phone numbers, Application Voice integration, Voice Portal,  Lead Tracking, Call Qualification, Missed Calls",
			'description'	=> "MCube is a Hosted Virtual Communication solution, This contains CallTrack, Virtual PBX, Hosted IVRS and Hosted Messaging solutions (Voice and Text)."
		);
		$this->load->view('siteheader',$data);
		$this->load->view('regotp');
		$this->load->view('footer');
	}
	function mobile(){
		if($this->session->userdata('logged_in'))redirect('/Home');
		$data = array(
			'title' 		=> "Virtual Communications solution: Hosted PBX, Virtual PBX, call tracking, IVRS.",
			'keywords'		=> "Hosted IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Toll free phone numbers, Application Voice integration, Voice Portal,  Lead Tracking, Call Qualification, Missed Calls",
			'description'	=> "MCube is a Hosted Virtual Communication solution, This contains CallTrack, Virtual PBX, Hosted IVRS and Hosted Messaging solutions (Voice and Text)."
		);
		$this->load->view('siteheader',$data);
		$this->load->view('mobile');
		$this->load->view('footer');
		
	}
	function forgetpass(){
		$data = array(
			'title' 		=> "Virtual Communications solution: Hosted PBX, Virtual PBX, call tracking, IVRS.",
			'keywords'		=> "Hosted IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Toll free phone numbers, Application Voice integration, Voice Portal,  Lead Tracking, Call Qualification, Missed Calls",
			'description'	=> "MCube is a Hosted Virtual Communication solution, This contains CallTrack, Virtual PBX, Hosted IVRS and Hosted Messaging solutions (Voice and Text)."
		);
		$this->load->view('siteheader1',$data);
		$this->load->view('forgetpass');
		//$this->load->view('footer');
	}
	
	
	function thankspage(){
		$data = array(
			'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			'description'	=> "MCube ishosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients.",			
			'message'		=>'Thanks For Contacting Us ,Our Executive will get back to you  As soon as possible'			
			
		);
		$this->load->view('siteheader',$data);
		$this->load->view('thankyou');
		$this->load->view('footer');
	}
	function thanks(){
		$data = array(
			'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			'description'	=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients.",			
			'message'		=>'Thanks For confirming. Welcome to MCube'			
			
		);
		$this->load->view('siteheader',$data);
		$this->load->view('thankyou');
		$this->load->view('footer');
	}
	function pthanks(){
		$data = array(
			'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			'description'	=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients.",			
			'message'		=>'Your have been accept as a partner for MCube,Login Details will send to your Email'			
			
		);
		$this->load->view('siteheader',$data);
		$this->load->view('thankyou');
		$this->load->view('footer');
	}
	function pdenied(){
		$data = array(
			'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			'description'	=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients.",			
			'message'		=>'Thank You we will disable  from MCube '
			
		);
		$this->load->view('siteheader',$data);
		$this->load->view('thankyou');
		$this->load->view('footer');
		
	}
	function error(){
		$data = array(
			'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			'description'	=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients.",			
			'message'		=>'You have already accepted or Denied your account,Please contact your adminstrator'			
			
		);
		$this->load->view('siteheader',$data);
		$this->load->view('thankyou');
		$this->load->view('footer');
	}
	
	function deactivated($bname=''){
		$bname=base64_decode($bname);
		$data = array(
			'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			'description'	=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients.",			
			'message'		=>'Thank You we will disable you from this '.$bname			
			
		);
		$this->load->view('siteheader',$data);
		$this->load->view('thankyou');
		$this->load->view('footer');
	}
	
	
	function file_extensions($str){
		
		$allow_types=array("image/jpeg","image/jpg","image/png","image/gif","application/vnd.ms-excel","application/msword","application/pdf","application/octet-stream","text/plain");
		if($_FILES['attachments']['size']>0){
			if(!in_array($_FILES['attachments']['type'],$allow_types)){
				$this->form_validation->set_message('file_extensions', 'File Extension Not Allowed');
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return true;
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
	function support_thanks(){
		$data = array(
			'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			'description'	=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients.",			
			'message'		=>'Thank You for submitting your request <br/>The Request will be processed within 24-48 hours '
		);
		$this->load->view('siteheader',$data);
		$this->load->view('thankyou');
		$this->load->view('footer');
	}
	function umanual(){
		if(!$this->session->userdata('logged_in')) redirect('site/');
		$data = array(
			'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			'description'	=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients."			
		);
		$this->load->view('siteheader',$data);
		$this->load->view('usermanual');
		$this->load->view('footer');
	}
	function android(){
		$data = array(
			'title' 		=> "MCube's State of the art hosted PBX|Virtual PBX.",
			'keywords'		=> "PBX, Virtual PBX, Hosted PBX, Hosted Business Communication, fixed to mobile convergence, phone extensions",
			'description'	=> "Virtual PBX - Buy local or toll-free phone numbers to be used to setup professoinal office experience. Gives first big impressions."			
		);

		$this->load->view('siteheader',$data);
		$this->load->view('android');
		$this->load->view('footer');
	}
	function number_verify(){
		if($this->input->post('submit')){
			$this->form_validation->set_rules('firstname', 'First Name', 'required|min_length[4]|max_length[32]|alpha_numeric');
			$this->form_validation->set_rules('lastname', 'Last Name', 'required|min_length[4]|max_length[32]|alpha_numeric');
			$this->form_validation->set_rules('cemail', 'Email', 'required|min_length[4]|max_length[50]|valid_email');
			$this->form_validation->set_rules('vcode', 'Verification Code', 'required|min_length[4]');
			$this->form_validation->set_rules('city', 'City', 'required|min_length[2]|alpha_numeric');
			$this->form_validation->set_rules('state', 'State', 'required|min_length[1]|alpha_numeric');
			$this->form_validation->set_rules('pincode', 'Pincode', 'required|min_length[1]|numeric');
			if (!$this->form_validation->run() == FALSE){
				$res=$this->commonmodel->cMVerify();
				if($res!=1){
					$flashdata = array('msgt' => 'error', 'msg' => "Code Doesn't Match");
					$this->session->set_flashdata($flashdata);
					redirect("site/number_verify");
				}else{
					$res=$this->commonmodel->Update_m_verification($res);
					$flashdata = array('msgt' => 'success', 'msg' => "Your Mobile verification is done.");
					$this->session->set_flashdata($flashdata);
					redirect("site/number_verify");
					
				}
			}
		}
		$data = array(
			'title' 		=> "MCube's State of the art hosted PBX|Virtual PBX.",
			'keywords'		=> "PBX, Virtual PBX, Hosted PBX, Hosted Business Communication, fixed to mobile convergence, phone extensions",
			'description'	=> "Virtual PBX - Buy local or toll-free phone numbers to be used to setup professoinal office experience. Gives first big impressions."			
		);

		$this->load->view('siteheader',$data);
		$this->load->view('verifyNumber');
		$this->load->view('footer');
	}
}
?>
