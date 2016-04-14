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
	//~ function home(){
		//~ $data = array(
			//~ 'title' 		=> "Virtual Communications solution: Hosted PBX, Virtual PBX, Call Tracking, IVRS.",
			//~ 'keywords'		=> "Hosted IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Toll free phone numbers, Application Voice integration, Voice Portal,  Lead Tracking, Call Qualification, Missed Calls",
			//~ 'description'	=> "MCube is a Hosted Virtual Communication solution, This contains CallTrack, Virtual PBX, Hosted IVRS and Hosted Messaging solutions (Voice and Text)."
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('sitehome');
		//~ $this->load->view('footer');
	//~ }
	
	function login(){
		if($this->session->userdata('logged_in'))redirect('/dashboard');
		$data = array(
			'title' 		=> "Virtual Communications solution: Hosted PBX, Virtual PBX, call tracking, IVRS.",
			'keywords'		=> "Hosted IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Toll free phone numbers, Application Voice integration, Voice Portal,  Lead Tracking, Call Qualification, Missed Calls",
			'description'	=> "MCube is a Hosted Virtual Communication solution, This contains CallTrack, Virtual PBX, Hosted IVRS and Hosted Messaging solutions (Voice and Text)."
		);
		$this->load->view('siteheader1',$data);
		$this->load->view('login',$data);
		//$this->load->view('footer');
	}
	function otp(){
		if($this->session->userdata('logged_in'))redirect('/dashboard');
		$data = array(
			'title' 		=> "Virtual Communications solution: Hosted PBX, Virtual PBX, call tracking, IVRS.",
			'keywords'		=> "Hosted IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Toll free phone numbers, Application Voice integration, Voice Portal,  Lead Tracking, Call Qualification, Missed Calls",
			'description'	=> "MCube is a Hosted Virtual Communication solution, This contains CallTrack, Virtual PBX, Hosted IVRS and Hosted Messaging solutions (Voice and Text)."
		);
	    $this->load->view('siteheader1',$data);
		$this->load->view('otp');
	}
	function regotp(){
		if($this->session->userdata('logged_in'))redirect('/dashboard');
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
		if($this->session->userdata('logged_in'))redirect('/dashboard');
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
	
	function registration(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "Virtual Communications solution: Hosted PBX, Virtual PBX, call tracking, IVRS.",
			//~ 'keywords'		=> "Hosted IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Toll free phone numbers, Application Voice integration, Voice Portal,  Lead Tracking, Call Qualification, Missed Calls",
			//~ 'description'	=> "MCube is a Hosted Virtual Communication solution, This contains CallTrack, Virtual PBX, Hosted IVRS and Hosted Messaging solutions (Voice and Text)."
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('registration');
		//~ $this->load->view('footer');
	}
	
	function calltracking(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube's State of the art hosted call tracking solution.",
			//~ 'keywords'		=> "Call tracking, call logging, missed calls, busy phones, switched off phones, regional forwarding, lead tracking, toll-free local phone numbers, improved ROI from Advertisement spend",
			//~ 'description'	=> "Call Tracking - Buy local or toll-free phone numbers to be forwarded to IVRS or your or one of many numbers in your organization and track effective advertising, leads or just calls. Virtual Call Tracking gives important data to track effectiveness of ad campaigns, sales executive. Do not worry about missed calls or busy calls or loosing leads."			
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('calltracking');
		//~ $this->load->view('footer');
	}
	function hostedivr(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube's Hosted IVRS",
			//~ 'keywords'		=> "IVR, IVRS, Hosted IVR, Hosted IVRS, Voice Portal, toll-free local phone numbers",
			//~ 'description'	=> "Hosted IVR & Voice Portal- Build inbound or outbound custom IVRS messages. Track incoming calls or leads. No set up fees. Low monthly charges."			
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('hostedivr');
		//~ $this->load->view('footer');
	}
	function lntfn(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "Local & Toll Free numbers with Call Tracking and IVRS.",
			//~ 'keywords'		=> "toll-free phone numbers, local phone numbers, local & toll free numbers, virtual call tracking, IVRS",
			//~ 'description'	=> "Local & Toll-Free Numbers - Buy local or toll-free phone numbers to be forwarded to IVRS or your or one of many numbers in your organization and track effective advertising, leads or just calls."			
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('lntfn');
		//~ $this->load->view('footer');
	}
	function textmessaging(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube SMS Messaging, Reach thousands",
			//~ 'keywords'		=> "SMS, messaging, text messages, broadcast messaging, shortcodes, longcodes, bulk SMS, service SMS, most cost effective marketing solution",
			//~ 'description'	=> "SMS Broadcast - Most cost effective solution to reach potential clients. Send SMS text to thousands at low per message cost."			
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('textmessaging');
		//~ $this->load->view('footer');
	}
	function voicebroadcast(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube Voice Broadcast",
			//~ 'keywords'		=> "Voice Broadcast",
			//~ 'description'	=> "Voice Broadcast - Hosted voice solution to help you quickly and easily send thousands of messages for low per minute per message rate."			
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('voicebroadcast');
		//~ $this->load->view('footer');
	}
	
	function marketing(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS.",
			//~ 'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
		 	//~ 'description'	=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients."			
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('marketing');
		//~ $this->load->view('footer');
	}
	function overview(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube's State of the art hosted call tracking solution.",
			//~ 'keywords'		=> "Hosted PBX, Virtual PBX, Call tracking, call logging, missed calls, busy phones, switched off phones, regional forwarding, lead tracking, toll-free local phone numbers, improved ROI from Advertisement spend",
			//~ 'description'	=> "Call Tracking - Buy local or toll-free phone numbers to be forwarded to IVRS or your or one of many numbers in your organization and track effective advertising, leads or just calls. Virtual Call Tracking gives important data to track effectiveness of ad campaigns, sales executive. Do not worry about missed calls or busy calls or loosing leads."			
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('overview');
		//~ $this->load->view('footer');
	}
	function contactus(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS.",
			//~ 'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			//~ 'description'	=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients."			
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('contactus');
		//~ $this->load->view('footer');
	}
	function qrtrack(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "Dynamic QR Tracking",
			//~ 'keywords'		=> "QR code, QR generator, dynamic QR",
			//~ 'description'	=> "Track your QR Codes and get real time statistics."			
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('qrtrack');
		//~ $this->load->view('footer');
	}
	function pricing(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			//~ 'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			//~ 'description'	=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients."			
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('pricing');
		//~ $this->load->view('footer');
	}
	function aboutus(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			//~ 'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			//~ 'description'	=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients."			
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('aboutus');
		//~ $this->load->view('footer');
	}
	function careers(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			//~ 'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			//~ 'description'	=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients."			
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('careers');
		//~ $this->load->view('footer');
	}
	function automobile(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			//~ 'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			//~ 'description'	=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients."			
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('automobile');
		//~ $this->load->view('footer');
	}
	function education(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			//~ 'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			//~ 'description'	=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX,marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients."			
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('education');
		//~ $this->load->view('footer');
	}
	function entertainment(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			//~ 'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			//~ 'description'	=> "MCube is hosted, state of the art marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients."			
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('entertainment');
		//~ $this->load->view('footer');
	}
	function healthcare(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			//~ 'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			//~ 'description'	=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX,marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients."			
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('healthcare');
		//~ $this->load->view('footer');
	}
	function insurance(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			//~ 'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			//~ 'description'	=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients."			
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('insurance');
		//~ $this->load->view('footer');
	}
	function realestate(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			//~ 'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			//~ 'description'	=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients."			
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('realestate');
		//~ $this->load->view('footer');
	}
	function recruiting(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			//~ 'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			//~ 'description'	=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients."			
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('recruiting');
		//~ $this->load->view('footer');
	}
	function webinar(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			//~ 'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			//~ 'description'	=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients."			
	//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('webinar');
		//~ $this->load->view('footer');
	}
	function faq(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			//~ 'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			//~ 'description'	=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients."			
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('faq');
		//~ $this->load->view('footer');
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
	function termsofservice(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			//~ 'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			//~ 'description'	=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients."			
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('termsofservice');
		//~ $this->load->view('footer');
	}
	function privacypolicy(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, call tracking, IVRS solution.",
			//~ 'keywords'		=> "Cloud, IVR, Virtual PBX, Hosted PBX, Virtual Call Tracking, Call Tracking, Voice Broadcast , SMS, Text Broadcast, Auto Dialer, Toll free phone numbers, local phone numbers, Voice API, SMS API, Application Voice integration, Voice Portal, Marketing, Lead Tracking, Call Qualification, Missed Calls ",
			//~ 'description'	=> "MCube is hosted, state of the art Business Communications system offering :Hosted PBX, Virtual PBX, marketing, call tracking, IVRS solution. This includes best in class virtual CallTrack, Messaging solutions (Voice and Text), Hosted IVRS. The APIs from the solutions also allow applications to communicate and engage with the clients."			
		//~ );
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('privacypolicy');
		//~ $this->load->view('footer');
	}
	function hostedpbx(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube's State of the art hosted PBX|Virtual PBX.",
			//~ 'keywords'		=> "PBX, Virtual PBX, Hosted PBX, Hosted Business Communication, fixed to mobile convergence, phone extensions",
			//~ 'description'	=> "Virtual PBX - Buy local or toll-free phone numbers to be used to setup professoinal office experience. Gives first big impressions."			
		//~ );
//~ 
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('hostedpbx');
		//~ $this->load->view('footer');
	}
	function hostedpbx2(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube's State of the art hosted PBX|Virtual PBX.",
			//~ 'keywords'		=> "PBX, Virtual PBX, Hosted PBX, Hosted Business Communication, fixed to mobile convergence, phone extensions",
			//~ 'description'	=> "Virtual PBX - Buy local or toll-free phone numbers to be used to setup professoinal office experience. Gives first big impressions."			
		//~ );
//~ 
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('hostedpbx2');
		//~ $this->load->view('footer');
	}
	function autodialer(){
		$this->login();
		//~ $data = array(
			//~ 'title' 		=> "MCube's State of the art hosted PBX|Virtual PBX.",
			//~ 'keywords'		=> "PBX, Virtual PBX, Hosted PBX, Hosted Business Communication, fixed to mobile convergence, phone extensions",
			//~ 'description'	=> "Virtual PBX - Buy local or toll-free phone numbers to be used to setup professoinal office experience. Gives first big impressions."			
		//~ );
//~ 
		//~ $this->load->view('siteheader',$data);
		//~ $this->load->view('autodialer');
		//~ $this->load->view('footer');
	}
	function support(){
		$data = array(
			'title' 		=> "MCube's State of the art hosted PBX|Virtual PBX.",
			'keywords'		=> "PBX, Virtual PBX, Hosted PBX, Hosted Business Communication, fixed to mobile convergence, phone extensions",
			'description'	=> "Virtual PBX - Buy local or toll-free phone numbers to be used to setup professoinal office experience. Gives first big impressions."			
		);
		if($this->input->post('sendemail')){
			$this->form_validation->set_rules('name', 'name', 'required|min_length[4]|max_length[50]');
			$this->form_validation->set_rules('email', 'Email', 'required|min_length[4]|max_length[50]|valid_email');
			$this->form_validation->set_rules('mobile', 'mobile', 'required|min_length[10]|numeric');
			$this->form_validation->set_rules('mnumber', 'Group Number', 'min_length[10]|numeric');
			$this->form_validation->set_rules('attachments', 'Attachments', 'callback_file_extensions');
			$this->form_validation->set_rules('captchas', 'captcha', 'required|callback_check_captcha');
			if (!$this->form_validation->run() == FALSE){
				$filepath = '';
				if($_FILES['attachments']['error']==0 ){
					$config['upload_path'] = dirname($_SERVER["SCRIPT_FILENAME"])."/support_attachment/";
					$config['allowed_types'] = 'gif|jpg|png|jpeg|pdf|doc|xml';
					$this->load->library('upload', $config);
					$this->upload->initialize($config);
					if($this->upload->do_upload('attachments')){
						$data = $this->upload->data();
						$filepath = $this->config->item('support').$data['file_name'];
					}else{
						$error = array('error' => $this->upload->display_errors());
					}
				}
				$this->db->query("INSERT INTO `support_request` (`id`, `name`, `email`,`number`, `company`, `group`,`landingnumber`, `subject`,`issue`, `screen_path`) VALUES (NULL,'".$_POST['name']."','".$_POST['email']."','".$_POST['mobile']."','".$_POST['company']."','".$_POST['mgroup']."','".$_POST['mnumber']."','".$_POST['subject']."','".$_POST['description']."','".$filepath."')");
				$from = $this->input->post('email');
				$to = "support@vmc.in";
				$message = "Dear Support Team,<br/><br/>Please follow through the Request";
				$message .= "<br/>Name:".$this->input->post('name');
				$message .= "<br/>Email:".$this->input->post('email');
				$message .= "<br/>Number:".$this->input->post('mobile');
				$message .= "<br/>Your Company:".$this->input->post('company');
				$message .= "<br/>Mcube Group:".$this->input->post('mgroup');
				$message .= "<br/>MCube Group Phone Number:".$this->input->post('mnumber');
				$message .= "<br/>Subject:".$this->input->post('subject');
				$message .= "<br/>Description:".$this->input->post('description')." <br/>";
				$subject = "Support Needed to the User";
				$this->load->library('email');
				$this->email->from($from);
				$this->email->to($to);
				$this->email->subject($subject);
				$this->email->message($message);
				if($filepath != '' ) $this->email->attach($filepath);
				//$this->email->message($message);
				$this->email->send();
				$name = $this->input->post('name');
				$mobile = $this->input->post('mobile');
				$description = $this->input->post('description');
				$email = $this->input->post('email');
				$leadAPI = "https://mcube.vmc.in/api/prospects_api?username=".urlencode('leads@vmc.in')."&password=vmc123&group=".urlencode('Webenquires')."&assignto=auto&source=".urlencode('Support')."&name=".urlencode($name)."&number=".$mobile."&email=".urlencode($email)."&remark=".urlencode($description)."&duplicate=FALSE";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL,$leadAPI);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$data = curl_exec($ch);
				curl_close($ch);
				/* END */
				/* SMS */
				//~ $business=($this->input->post('company')!="")?" from the business ".$this->input->post('company'):'';
				//~ $mobiles=array('8884976982','9739097139');
				//~ $mobiles=array('8197314282');
				//~ $mess="Support Query from ".$this->input->post('name')." ".$this->input->post('mobile')." ".$business;
				//~ $api = "http://180.179.200.180/getservice.php?from=vmc.in";
				//~ foreach($mobiles as $m){
					//~ $reply = $api."&to=".$m."&text=".urlencode($mess);
					//~ file($reply);
				//~ }
				/*  END */
				@unlink($filepath);
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('smsg', "We Will Process Your Request");
				redirect('/sthanks');
			} 	
		}
		$this->load->view('siteheader',$data);
		$this->load->view('support');
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
