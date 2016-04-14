<?php
Class Emailmodel extends Model{
	
	function Emailmodel(){
		parent::Model();
		$this->load->model('profilemodel');
		//$this->load->library('session');
	}
	function get_busineeprofiledetails($bid){
		$sql=$this->db->query("select * from business where bid=".$bid);
		if($sql->num_rows()>0)
		{
			$res=$sql->result_array();
		}
		return $res;
	}
	function return_name($bid){
		$r=$this->get_busineeprofiledetails($bid);
		return $r[0]['contactname'];
	}
	function email_body2($empname,$bid){
		$r=$this->get_busineeprofiledetails($bid);
		$body='<br/>'.$empname.' As denied receiving calls on your behalf this person has been removed';
		
		return $body;	
	}
	function email_body1($empname,$email,$pass,$bid,$dnd){
		$r=$this->get_busineeprofiledetails($bid);
		if($pass!=""){
		$str='Access Your Account with<br/>
			  UserName: '.$email.'<br/>
			  Password: '.$pass.'<br/>
			  <br/>NOTE:Please change password after first login.';
		}else{
			$str='<br/>For Login Deatails contact your admin';
		}
		
			$body='<br/><br/><br/>You have been successfully Registered with MCube for '.$r[0]['businessname'].'<br>'.$str;
			if($dnd==1){
				$body.='<br/><br/><br/>Your mobile is registered in DNC registry. Please authorize VMC to call you from MCube, verify at '.base_url().'verifyYourNumber';
			}
		return $body;	
	}
	function get_site_url($domain_id){
		
		$sqls=$this->db->query("select * from partner where partner_id=".$domain_id);
		if($sqls->num_rows()>0){
			$res=$sqls->row();
		}
		return $res;
	}
	function new_emailbody($empname,$bid,$string){
		
		if($bid==""){
			$r=$this->profilemodel->get_profiledetails();
		}else{
			$r=$this->get_busineeprofiledetails($bid);
			$domain_name=$this->get_site_url($r[0]['domain_id']);
		}
		$body='
				Hi '.$empname.'
				<br/><br/>
				You have been Registered with MCube for '.$r[0]['businessname'].' to 
				Receive call on behalf of '.$r[0]['businessname'].'
				Please active your mobile number using the below code
				Code:'.$string;
		
	}
	function email_body($empname,$eid,$bid=''){
		$eid=base64_encode($eid);
		if($bid==""){
			$r=$this->profilemodel->get_profiledetails();
		}else{
			$r=$this->get_busineeprofiledetails($bid);
			$domain_name=$this->get_site_url($r[0]['domain_id']);
			
		}
		
		$bid=base64_encode($r[0]['bid']);
		$link='<a href="http://'.$domain_name->domain_name.'/user/deregister/'.$eid.'/'.$bid.'" style="color: #2f82de;text-decoration:none;font-weight:bold;">Click here</a>';
		$link1='<a href="http://'.$domain_name->domain_name.'/user/Employeeregister/'.$eid.'/'.$bid.'" style="color: #2f82de;text-decoration:none;font-weight:bold;">Click here</a>';
		
		
		$body='<br/>You have been Registered with MCube for '.$r[0]['businessname'].' to 
				Receive call on behalf of '.$r[0]['businessname'].' if you think 
				this is a mistake '.$link.' otherwise '.$link1.' to confirm</span>
				<br/><br/><br/><br/>';
		return $body;		
	}
	function email_header(){
		$path=base_url();
		//echo $path;exit;
		$message='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
					<html xmlns="http://www.w3.org/1999/xhtml"> 
					<head> 
					<base href="'.$path.'">';
		
		$message .='</head> 
					<body>
					<div id="header" style="background:#000000;width:100%;margin:0px auto;height:96px;background:url('.$path.'system/application/img/headbg.gif) repeat-x top;position:fixed;color:#ffffff;">
						<div class="pagecontent" style="width:950px;height:auto;margin:0px auto;padding:0px;">
						<div class="header1" style="width:100%;">

							<table style="border:none;margin:0px;padding:0px;width:100%;">
								<tr>
									<td style="float:left;background:url('.$path.'system/application/img/logo1.gif) no-repeat;margin-right:2px;width:120px;">
										<a href="'.$path.'"><img src='.$path.'system/application/img/logo1.gif width="120" height="96" border="0" /></a>
									</td>
									<td valign="bottom">
									<table style="border:none;margin:0px;padding:0px;width:100%;">
															<tr><td valign="bottom">
															
												
										</td>
										<td>
										<div style="float:right;background:url('.$path.'system/application/img/mcubeicnbeta.gif) no-repeat center top;margin-right:2px;margin-top:7px;clear:right;">
											<div><a href=""><img src="'.$path.'system/application/img/mcubeicnbeta.gif" width="125" height="40" border="0" /></a></div>
											<div style="float:left;color:#F90;font-size:18px;font-weight:bold;padding-left:15px;background:url('.$path.'system/application/img/phoneicon1.png) no-repeat;" >18004192202</div>
										</div>
										</td>
										</tr>

									</table>
									</td>
								</tr>
							</table>
							</div>
							</div>
					</div>
					<div style="width:100%;float:left;padding-top:96px;padding-bottom:10px;height:auto;">

					<div style="width:530px;margin:0px auto;padding-top:80px;" >';			
		
		return $message;
		
	}
	function email_footer(){
		$path=base_url();
		$footer='
				</div>
				</div><!-- end middle -->
				<div class="footerlinkscontainer" style="float:left;background-color:#e7e8e9;font-size:11px;width:100%;">
					<div class="pagecontent" style="width:950px;height:auto;margin:0px auto;padding:0px;">
						<div class="footerlinks" style="float:left;padding-bottom:50px;color:#333;margin-left:30px;">
							<ul>
								<li class="footerlinkshead" style="font-weight:bold;">Products</li>
								<li><a href="site/overview">Overview of Products</a></li>
								<li><a href="site/calltracking">Call Tracking</a></li>

								<li><a href="site/hostedivr">Hosted IVR</a></li>
								<li><a href="site/hostedivr">Voice Portal</a></li>
								<li><a href="site/lntfn">Local and Toll-Free Numbers</a></li>
								<li><a href="site/textmessaging">SMS Text Messaging</a></li>
								<li><a href="site/voicebroadcast">Voice Broadcast</a></li>
								<li><a href="site/qrtrack">QR Tracking</a></li>

							</ul> 
						</div> 

						<div  class="footerlinks" style="float:left;padding-bottom:50px;color:#333;margin-left:30px;">
							<ul>
							<li class="footerlinkshead">Industries</li>
							<li><a href="site/automobile">Automobile</a></li>
							<li><a href="site/education">Education</a></li>
							<li><a href="site/entertainment">Entertainment</a></li>

							<li><a href="site/healthcare">Healthcare</a></li>
							<li><a href="site/insurance">Insurance</a></li>
							<li><a href="site/marketing">Marketing</a></li>
							<li><a href="site/realestate">Real Estate</a></li>
							<li><a href="site/recruiting">Recruiting</a></li>
							</ul>

						</div>  

						<div  class="footerlinks" style="float:left;padding-bottom:50px;color:#333;margin-left:30px;">
							<ul>
							<li class="footerlinkshead">Learn More</li>
							<li><a href="site/pricing">Pricing</a></li>
							<li><a href="site/webinar">Webinars</a></li>
							<li><a href="site/termsofservice">Terms of Service</a></li>

							<li><a href="site/privacypolicy">Privacy Policy</a></li>
							<li><a href="site/faq">FAQ s</a></li>
							</ul>
						</div>  

						<div  class="footerlinks" style="float:left;padding-bottom:50px;color:#333;margin-left:30px;">
							<ul>
							<li class="footerlinkshead">Company</li>
							<li><a href="site/aboutus">About us</a></li>

							<li><a href="site/careers">Careers</a></li>
							<li><a href="site/contactus">Contact Us</a></li>
							</ul>
						</div> 
					</div>
				<div id="footer" style="width:100%;	height:32px;float:left;background-image:url('.$path.'system/application/img/footerbg.gif);">
				<div class="footertext" style="height:32px;vertical-align: middle;padding:5px;text-align:right;font-size:11px;font-weight:bold;color:#000000;white-space:nowrap;margin-right:10px;">Contact : <span class="phone">18004192202</span>
				&nbsp;&nbsp;<span class="phoneicon"><img src="'.$path.'images/spacer.gif" width="11" height="11" border="0"></span>
				 &nbsp;&bull;&nbsp; support@vmc.in &nbsp;&bull;&nbsp; sales@vmc.in &nbsp;&nbsp; 
				 <span class="footercopy" style="color:#666;font-size:9px;"> &copy;&nbsp;&nbsp;2011&nbsp;&nbsp; All rights reserved</span>

				 </div>
				</div>
				</div>

				</body> 
				</html>
				';
				return $footer;
		
	}
	function email_body_login($empname,$bid){
		$r=$this->get_busineeprofiledetails($bid);
		$body='<br/>Your Login Access has been disabled from the Business '.$r[0]['businessname']
				.'<br/>Please contact Adminstrator for Further details<br/>';
		return $body;	
	}
	function newEmailBody($message,$name){
		$content='
		<html><head>
		</head>
			<body style="margin:0 auto; padding: 0;width:600px;">
			<table style="margin:0px;width:100%;font-family: Helvetica Neue, Arial, Helvetica, Geneva, sans-serif;font-size:14px;" cellpadding="0" cellspacing="0">
			<tr><td style="background:#000; width:100%;height:80px;float:left;
			-webkit-border-top-left-radius: 10px;-webkit-border-top-right-radius: 10px;
			-moz-border-radius-topleft: 10px;-moz-border-radius-topright: 10px;
			border-top-left-radius: 10px;border-top-right-radius: 10px;overflow:hidden;
			color:#000;">
					<table cellpadding="0" cellspacing="0" border="0" style="width:100%">
					<tr>
						<td align="left"><img src="http://mcube.vmc.in/qrcode/company_logos/856265314vmclogo.gif" style="margin-top:-20px;"></td><td align="right"><img src="http://mcube.vmc.in/system/application/img/mcubeicn.jpg" style="margin-top:15px;margin-right:20px;"><br/><div style="color: #FF9900;font-size: 18px;font-weight: bold;margin-right:10px;"><img src="http://mcube.vmc.in/system/application/img/phoneicon.gif" >1800-419-2202</div></td>		
					</tr>
					</table>
			
			
			</td></tr>
			<tr><td style="background:#FFF;width:100%;color:#000;border:1px solid #000;height:300px;vertical-align:top;padding:10px;">
				<p>&nbsp;</p>
				<p style="font-size: 18px; line-height:24px; color: #b0b0b0; font-weight:bold; margin-top:0px; margin-bottom:18px; font-family: Helvetica Neue, Arial, Helvetica, Geneva, sans-serif;text-indent:0.5cm;" align="left"><singleline label="Title">Hi '.$name.'</singleline></p>
				<div style="font-size: 13px; line-height: 18px; color: #444444; margin-top: 0px; margin-bottom: 18px; font-family: Helvetica Neue, Arial, Helvetica, Geneva, sans-serif; text-indent:1cm;" align="left">
													
													<multiline label="Description">'.$message.'
					</div>
				<p style="font-size: 18px; line-height:24px; color: #0004; font-weight:bold; margin-top:0px; margin-bottom:18px; font-family: Helvetica Neue, Arial, Helvetica, Geneva, sans-serif;text-indent:0.5cm;"><singleline label="Title">Regards, </singleline></p>									
				<p style="font-size: 18px; line-height:24px; color: #0004; font-weight:bold; margin-top:0px; margin-bottom:18px; font-family: Helvetica Neue, Arial, Helvetica, Geneva, sans-serif;text-indent:0.5cm;"><singleline label="Title">Mcube Team </singleline></p>									
												

			</td></tr>
			<tr><td style="background:#000;width:100%;height:40px;
			-webkit-border-bottom-left-radius: 10px;-webkit-border-bottom-right-radius: 10px;
			-moz-border-radius-bottomleft: 10px;-moz-border-radius-bottomright: 10px;
			border-bottom-left-radius: 10px;border-bottom-right-radius: 10px;
			color:#fff;padding:10px;font-size:12px;font-weight:bold;text-align:right;"><a href="http://mcube.vmc.in/" style="color:#FFF;text-decoration:none;">VMC Technologies</a></td></tr>
			</table>
			</body></html>';
			return $content;
		
	}
	
}



/* end of model */
