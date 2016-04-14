<?php
Class Commonmodel extends Model{
	function Commonmodel(){
		 parent::Model();
		 $this->load->model('auditlog');
		 $this->load->model('emailmodel');
		 $this->load->helper('mcube_helper');
	}
	function get_empDummy($id,$bid){
		$sql=$this->db->query("select * from ".$bid."_employee where eid =$id and status=3");
		return $sql->result_array();
	}
	function get_empDummy1($id,$bid){
		$sql=$this->db->query("select * from ".$bid."_employee where eid =$id and status=3");
		if($sql->num_rows()>0){
			return $sql->result_array();
		}else{
			return array();
		}	
	}
	function get_employee_custom($id,$bid){ 
		$res=array();
		$sql=$this->db->query("select * from ".$bid."_customfieldsvalue where dataid=$id and modid=2");
		 if($sql->num_rows()>0){
			 $res=$sql->result_array();
		 }
		return $res;
		
	}
	function get_busineeprofiledetails($bid){
		$sql=$this->db->query("select * from business where bid=$bid");
		return $sql->result_array();
		
		
	}
	function EmployeeDeregister($eid,$bid){
		$arr=$this->get_empDummy1($eid,$bid);
		//print_r($arr);exit;
		if(!empty($arr)){
			$get_custom=$this->get_employee_custom($eid,$bid);
			$r=$this->get_busineeprofiledetails($arr[0]['bid']);
			$body=$this->emailmodel->newEmailBody($this->emailmodel->email_body2($arr[0]['empname'],$arr[0]['bid']),$arr[0]['empname']);
			$to  = $r[0]['contactemail']; // note the comma
			$subject = 'Registered Employee Details';
			//~ $from='"MCube" <noreply@mcube.com>';
			 //~ 
			//~ $headers	 = 'MIME-Version: 1.0' . "\n".
						//~ 'Content-type: text/html; charset=iso-8859-1' . "\n".
						//~ 'From:'.$from. "\n" .
						//~ 'Reply-To:"MCube" <support@vmc.in>'."\n" .
						//~ 'X-Mailer: PHP/' . phpversion();
			 //~ mail($to, $subject, $body, $headers);
			 $this->load->library('email');
			 $this->email->from('noreply@mcube.com', 'MCube');
			 $this->email->to($to);
			 $this->email->subject($subject);
			 $this->email->message($body);
			 $this->email->send();	
			 
			 $s2=$this->db->query("Delete from ".$bid."_group_emp where eid=".$arr[0]['eid']." and bid=".$arr[0]['bid']);
			 $s=$this->db->query("Delete from user where eid=".$arr[0]['eid']." and bid=".$arr[0]['bid']);
			 $s=$this->db->query("Delete from ".$bid."_employee where eid=".$arr[0]['eid']." and bid=".$arr[0]['bid']);
			return true;	
		}
		else{
			return false;
		}
	}
	function EmployeeAdd($arr,$bid){
		if(!empty($arr)){
				$get_custom=$this->get_employee_custom($arr[0]['eid'],$bid);
				$news=array_keys($arr[0]);
				$dnd = (array)filter_dnd($arr[0]['empnumber']);	
				if($dnd['dnd']==0){
					$this->db->set('status','1');
				}else{
					$this->db->set('status','0');
				}
				$this->db->where('eid',$arr[0]['eid']);
				$this->db->update($arr[0]['bid']."_employee");
				if($dnd['dnd']==1){
					$vcode = "";for($i = 0; $i<=6 ; $i++){$vcode .= ($i%2==0)? chr(rand(65,90)) : rand(0,9);}
					$message = "\nPlease authorize VMC to call you from MCube ".base_url().'verifyYourNumber'." Verification code: ".$vcode;
					//$reply=sms_send($arr[0]['empnumber'],$message);
					$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
					$sms = $api."&to=".substr($arr[0]['empnumber'],-10,10)."&text=".urlencode($message);
					$sms = file($sms);
					$this->db->query("INSERT INTO verifiedemployee (`bid`,`eid`,`ename`,`email`,`number`,`vcode`,`request_date`,`status`) VALUES ('".$arr[0]['bid']."','".$arr[0]['eid']."','".$arr[0]['empname']."','".$arr[0]['empemail']."','".$arr[0]['empnumber']."','".$vcode."','".date('Y-m-d h:i:s')."','0')");
				}
				$password = "";for($i = 0; $i<=10 ; $i++){$password .= ($i%2==0)? chr(rand(97,122)) : rand(0,9);}
				if($arr[0]['login']==1){
					$user_auth=$this->db->query("select * from user where username='".$arr[0]['empemail']."' and bid='".$arr[0]['bid']."'")->row();
					if($dnd['dnd']==0){
						$this->db->set('status','1');
					}else{
						$this->db->set('status','0');
					}
					$this->db->set('password',md5($password));
					$this->db->where('username',$arr[0]['empemail']); 
					$this->db->where('bid',$arr[0]['bid']);
					$this->db->update('user'); 
					$body=$this->emailmodel->newEmailBody($this->emailmodel->email_body1($arr[0]['empname'],$arr[0]['empemail'],$password,$arr[0]['bid'],$dnd['dnd']),$arr[0]['empname']);
					
					$to  = $arr[0]['empemail']; // note the comma
					$subject = 'Registered Employee Details';
					//~ $from='"MCube" <noreply@mcube.com>';
					//~ $headers	 = 'MIME-Version: 1.0' . "\n".
							//~ 'Content-type: text/html; charset=iso-8859-1' . "\n".
							//~ 'From:'.$from. "\n" .
							//~ 'Reply-To:"MCube" <support@vmc.in>'."\n" .
							//~ 'X-Mailer: PHP/' . phpversion();
				 //~ mail($to, $subject, $body, $headers);
				 $this->load->library('email');
				 $this->email->from('noreply@mcube.com', 'MCube');
				 $this->email->to($to);
				 $this->email->subject($subject);
				 $this->email->message($body);
				 $this->email->send();	
				if($dnd['dnd']==1){
						return 2;
				}else{
						return 1;
				}
			}else{
				if($dnd['dnd']==1){
						return 2;
				}else{
						return 1;
				}
			}
		}else{
			 return 0;
		}
	}
	function careers(){		
		$newName = "career".date('YmdHis').$_FILES['resume']['name'];
		move_uploaded_file($_FILES['resume']['tmp_name'],$this->config->item('career_path').$newName);
		$id=$this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM `mc_careers`")->row()->id;
		$this->db->set('id', $id); 
		$this->db->set('firstname', $this->input->post('frstname')); 
		$this->db->set('lastname', $this->input->post('lstname')); 
		$this->db->set('mobilenumber', $this->input->post('mobnumber')); 
		$this->db->set('landline', $this->input->post('landline')); 
		$this->db->set('message', $this->input->post('messag')); 
		$this->db->set('email', $this->input->post('cemail')); 
		$this->db->set('expertise', $this->input->post('expertise')); 
		$this->db->set('resume', $newName); 
		$this->db->insert('mc_careers');
		return $newName;
	}

	function register_dummy(){
		$email = $this->db->query("SELECT count(bid) as cnt FROM business_dummy WHERE contactemail='".$this->input->post('cemail')."'")->row()->cnt;
		if($email=='0'){
			$bid=$this->db->query("SELECT COALESCE(MAX(`bid`),0)+1 as id FROM `business_dummy`")->row()->id;
			$this->db->set('bid', $bid); 
			$this->db->set('businessname', $this->input->post('login_businessname')); 
			$this->db->set('businessemail', $this->input->post('login_username')); 
			$this->db->set('contactemail', $this->input->post('cemail')); 
			$this->db->set('contactname', $this->input->post('cname')); 
			$this->db->set('contactphone', $this->input->post('cphone')); 
			$this->db->set('businessphone', $this->input->post('bphone')); 
			$this->db->set('businessaddress', htmlentities(mysql_escape_string($this->input->post('baddress')))); 
			$this->db->set('webaddress', $this->input->post('waddress')); 
			$this->db->set('businessaddress1', htmlentities(mysql_escape_string($this->input->post('baddress1'))));
			$this->db->set('city', $this->input->post('city')); 
			$this->db->set('state', $this->input->post('state')); 
			$this->db->set('zipcode', $this->input->post('zipcode')); 
			$this->db->set('country', $this->input->post('country')); 
			$this->db->set('locality', $this->input->post('locality')); 
			$this->db->set('language', $this->input->post('language')); 
			$this->db->insert('business_dummy');
			return true;
		}
		return false;
	}
	Function Check_Domain(){
		$domain=$_SERVER['HTTP_HOST'];
		$sql=$this->db->query("SELECT * FROM partner WHERE domain_name='".$domain."' and status=1");
		if($sql->num_rows()>0){
			$res=$sql->row();
			 return $res->partner_id;
		}else{
			return 0;
		}
	}
	function register($partner_id='',$st=''){
		$bid=$this->db->query("SELECT COALESCE(MAX(`bid`),0)+1 as id FROM `business`")->row()->id;
		$this->db->set('bid', $bid); 
		$this->db->set('businessname', $this->input->post('login_businessname')); 
		$this->db->set('businessemail', $this->input->post('login_username')); 
		$this->db->set('contactemail', $this->input->post('cemail')); 
		$this->db->set('contactname', $this->input->post('cname')); 
		$this->db->set('contactphone', $this->input->post('cphone')); 
		$this->db->set('businessphone', $this->input->post('bphone')); 
		$this->db->set('businessaddress', htmlentities($this->input->post('baddress'))); 
		$this->db->set('webaddress', $this->input->post('waddress')); 
		$this->db->set('validity', $this->input->post('svdate')); 
		$this->db->set('businessaddress1', htmlentities($this->input->post('baddress1')));
		$this->db->set('city', $this->input->post('city')); 
		if($this->input->post('parents')!=""){
		   $this->db->set('pid', $this->input->post('pids')); 
		}
		$this->db->set('state', $this->input->post('state')); 
		$this->db->set('zipcode', $this->input->post('zipcode')); 
		$this->db->set('country', $this->input->post('country')); 
		$this->db->set('locality', $this->input->post('locality')); 
		$this->db->set('language', $this->input->post('language')); 
		$this->db->set('act', $this->input->post('act')); 
		$this->db->set('domain_id',($partner_id!="")?$partner_id:'1'); 
		$this->db->set('relatedto',($this->input->post('relatedto')!="")?$this->input->post('relatedto'):'1'); 
		$this->db->set('employee',($this->input->post('emplp')!="")?$this->input->post('emplp'):'1'); 
		$this->db->set('description',($this->input->post('desc')!='')?$this->input->post('desc'):''); 
		$this->db->set('followups','1');
		/*if($st!=''){
			$this->db->set('act','1');
		}*/
		$this->db->insert('business');
		$this->insert_tables($bid);
		
		$fs=$this->db->query("SELECT * FROM partner_feature where partner_id='".(($this->input->post('relatedto')!="")?$this->input->post('relatedto'):'1')."'");
		if($fs->num_rows()>0){
			$rs=$fs->result_array();
			foreach($rs as $r){
				$this->db->query("INSERT INTO `business_feature` (`bid`, `feature_id`) VALUES (".$bid.", ".$r['feature_id'].")");
				
			}
			
			
		}
		
		//$this->db->query("INSERT INTO `business_feature` (`bid`, `feature_id`) VALUES (".$bid.", 1),(".$bid.", 2),(".$bid.", 3)");
		
		$this->db->set('eid', '1');
		$this->db->set('bid', $bid);
		$this->db->set('roleid','1');
		$this->db->set('empname', $this->input->post('cname'));
		$this->db->set('empnumber', $this->input->post('cphone'));
		$this->db->set('empemail', $this->input->post('login_username'));
		$this->db->set('login', 1);
		$this->db->set('status', 1);
		$this->db->insert($bid.'_employee');
		
			
		$eid=$this->db->insert_id();
		
		$uid=$this->db->query("SELECT COALESCE(MAX(`uid`),0)+1 as id FROM `user`")->row()->id;
		$this->db->set('uid', $uid); 
		$this->db->set('bid', $bid); 
		$this->db->set('username', $this->input->post('login_username')); 
		$this->db->set('password', md5($this->input->post('login_password'))); 
		//$this->db->set('type', '1');
		$this->db->set('eid', $eid);
		$this->db->insert('user');
		$body='';
		 if($st!=''){
//~ 
			$number=$this->db->query("SELECT p.number FROM `prinumbers` p
								  LEFT JOIN landingnumbers l on p.landingnumber=l.number
								  WHERE p.status=0 AND p.bid=0 AND l.module_id=1 limit 0,1")->row()->number;
			$this->db->set('bid',$bid);	
			$this->db->where('number',$number);
			$this->db->update('prinumbers');
			
			$this->db->set('bid',$bid);	
			$this->db->where('number',$number);
			$this->db->update('package_activate');
				
			$this->db->set('bid',$bid);	
			$this->db->where('number',$number);
			$this->db->update('business_packageaddons');
			
			$number=$this->db->query("SELECT p.number FROM `prinumbers` p
								  LEFT JOIN landingnumbers l on p.landingnumber=l.number
								  WHERE p.status=0 AND p.bid=0 AND l.module_id=2 limit 0,1")->row()->number;
			$this->db->set('bid',$bid);	
			$this->db->set('type','2');	
			$this->db->where('number',$number);
			$this->db->update('prinumbers');
			
			$this->db->set('bid',$bid);	
			$this->db->where('number',$number);
			$this->db->update('package_activate');
			
			$this->db->set('bid',$bid);	
			$this->db->where('number',$number);
			$this->db->update('business_packageaddons');
			$data=array();
			$name=$this->input->post('cname');
			$message='Your Demo Account has been successfully created with MCube for '.$this->input->post('login_businessname').'.<br/><br/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Please find Login Details
				<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;URL : '.base_url().'site/login
				<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Username:'.$this->input->post('login_username').
				'<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Password:'.$this->input->post('login_password').'';
				$body=$this->emailmodel->newEmailBody($message,$name);
				
			}else{
				
				$data=array();
			$name=$this->input->post('cname');
			$message='Your Account has been successfully created with MCube for '.$this->input->post('login_businessname').'.<br/><br/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Please find Login Details'
			.'<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;URL : '.base_url().'site/login'
			.'<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Username : '.$this->input->post('login_username')
			.'<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Password:'.$this->input->post('login_password')
			.'<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				Note :Please change your password after first login<br/>
				';
				$body=$this->emailmodel->newEmailBody($message,$name);
				
			}	
			 
		 	$config['charset']    = 'utf-8';
			$config['newline']    = "\r\n";
			$config['mailtype'] = 'html'; // or html
			$config['validation'] = TRUE; // bool whether to validate email or not      
			$this->email->initialize($config);
			$this->email->from('<noreply@mcube.com>','Mcube');
			$this->email->to($this->input->post('login_username'));
			$this->email->bcc('tapan.chatterjee@vmc.in,accounts@vmc.in'); 
			$this->email->subject("Mcube Registration Details");
			$this->email->message($body);  
			$this->email->send();
			 
			return 1;
	}
	function MiddleMessage(){
		$content='<table align="center" cellpadding="5" cellspacing="5" border="0" width="1024px">
			<tr><td>Business Name :</td><td>'.$this->input->post('login_businessname').'</td></tr>
			<tr><td>Email :</td><td>'.$this->input->post('cemail').'</td></tr>
			<tr><td>Phone  :</td><td>'.$this->input->post('cphone').'</td></tr>
			<tr><td>City  :</td><td>'.$this->input->post('city').'</td></tr>
			<tr><td>State  :</td><td>'.$this->input->post('state').'</td></tr>
			<tr><td>Country  :</td><td>'.$this->input->post('country').'</td></tr>
			<tr><td>Domain Name  :</td><td>http://'.$_SERVER['HTTP_HOST'].'</td></tr>
		</table>';
		return $content;
	}
	function MailMiddleMessage(){
		$content='<table align="center" cellpadding="5" cellspacing="5" border="0" width="1024px">
			<tr><td>Name :</td><td>'.$this->input->post('frstname').'&nbsp;'.$this->input->post('lstname').'</td></tr>
			<tr><td>Email :</td><td>'.$this->input->post('cemail').'</td></tr>
			<tr><td>Contact  :</td><td>'.$this->input->post('mobnumber').'</td></tr>
			<tr><td>Address  :</td><td>'.$this->input->post('messag').'</td></tr>
		</table>';
		return $content;
	}
	function assistance_insert(){
		$bid=$this->db->query("SELECT COALESCE(MAX(`id`),0)+1 as id FROM `query_assistance`")->row()->id;
		$this->db->set('id', $bid); 
		$this->db->set('firstname', $this->input->post('firstname')); 
		$this->db->set('lastname', $this->input->post('lastname')); 
		$this->db->set('email', $this->input->post('email')); 
		$this->db->set('company', $this->input->post('company')); 
		$this->db->set('phone', $this->input->post('phone')); 
		$this->db->set('zipcode', $this->input->post('zipcode')); 
		$this->db->set('jobtitle', htmlentities(mysql_escape_string($this->input->post('jtitle')))); 
		$this->db->set('products', implode(",",$this->input->post('product'))); 
		$this->db->set('query', htmlentities(mysql_escape_string($this->input->post('Questions'))));
		$this->db->set('no_of_employees', $this->input->post('employee')); 
		$this->db->insert('query_assistance');
		$content='<table align="center" cellpadding="5" cellspacing="5" border="0" width="1024px">
			<tr><td>Name :</td><td>'.$this->input->post('firstname')." ".$this->input->post('lastname').'</td></tr>
			<tr><td>Email :</td><td>'.$this->input->post('email').'</td></tr>
			<tr><td>Jobtitle  :</td><td>'.$this->input->post('jtitle').'</td></tr>
			<tr><td>Phone  :</td><td>'.$this->input->post('phone').'</td></tr>
			<tr><td>Company  :</td><td>'.$this->input->post('company').'</td></tr>
			<tr><td>No Of Employees  :</td><td>'.$this->input->post('employee').'</td></tr>
			<tr><td>Zipcode  :</td><td>'.$this->input->post('zipcode').'</td></tr>
			<tr><td>Products  :</td><td>'.implode(",",$this->input->post('product')).'</td></tr>
			<tr><td>Query  :</td><td>'.$this->input->post('Questions').'</td></tr>
		</table>';
		return $content;
	}
	function Email_Header(){
		
		$email_header='
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml"> 
			<head>
			
		<link rel="shortcut icon" type="image/x-icon" href="system/application/img/icons/favicon.ico">
		<link rel="stylesheet" type="text/css" href="'.base_url().'system/application/css/theme5.css" />
		<link rel="stylesheet" type="text/css" href="'.base_url().'system/application/css/style.css" />	
		<link rel="stylesheet" type="text/css" href="'.base_url().'system/application/css/site.css" />
		</head>
		<body>
	<div id="header">
	<div class="pagecontent">
		<div class="header1">
		<div class="vmc"><a href="'.site_url().'"><img src="'.base_url().'images/spacer.gif" width="121" height="88" border="0" /></a></div>
		
		<div class="mcube">
			<div><a href="Home"><img src="'.base_url().'images/spacer.gif" width="125" height="40" border="0" /></a></div>
			<div class="navtollfree"><span class="phoneicon1"></span>18004192202</div>
		</div>
		

		</div>
	</div>
</div>
<div id="middle"><div class="pagecontent">';
		
	return $email_header;	
	}
	function Email_Footer(){		
		$footer='</div>
		</div><!-- end middle -->
		<div class="footerlinkscontainer">
			<div class="pagecontent">
				<div class="footerlinks">
					<ul>
					<li class="footerlinkshead">Products</li>
					<li><a href="#">Overview of Products</a></li>
					<li><a href="#">MCube Track</a></li>
					<li><a href="#">MCube IVRS</a></li>
					<li><a href="#">Local and Toll-Free Numbers</a></li>
					<!-- <li><a href="#">APIs</a></li> -->
					</ul> 
				</div> 

				<div  class="footerlinks">
					<ul>
					<li class="footerlinkshead">Industries</li>
					<li><a href="#">Automobile</a></li>
					<li><a href="#">Education</a></li>
					<li><a href="#">Entertainment</a></li>
					<li><a href="#">Healthcare</a></li>
					<li><a href="#">Insurance</a></li>
					<li><a href="#">Marketing</a></li>
					<li><a href="#">Real Estate</a></li>
					<li><a href="#">Recruiting</a></li>
					</ul>
				</div>  

				<div  class="footerlinks">
					<ul>
					<li class="footerlinkshead">Learn More</li>
					<li><a href="#">Pricing</a></li>
					<li><a href="#">Webinars</a></li>
					<li><a href="#">Terms of Service</a></li>
					<li><a href="#">Privacy Policy</a></li>
					<li><a href="#">FAQ s</a></li>
					</ul>
				</div>  

				<div  class="footerlinks">
					<ul>
					<li class="footerlinkshead">Company</li>
					<li><a href="#">About us</a></li>
					<li><a href="#">Jobs</a></li>
					<li><a href="#">Contact Us</a></li>
					</ul>
				</div> 
			</div>
		</div>
		<div id="footer">
		<div class="footertext">Contact : <span class="phone">18004192202</span>
		&nbsp;&nbsp;<span class="phoneicon"><img src="'.base_url().'images/spacer.gif" width="11" height="11" border="0"></span>
		 &nbsp;&bull;&nbsp; support@vmc.in &nbsp;&bull;&nbsp; sales@vmc.in &nbsp;&nbsp; 
		 <span class="footercopy"> &copy;&nbsp;&nbsp;'.date('Y').'&nbsp;&nbsp; All rights reserved</span>
		 </div>
		</div>
		</body>';
		return $footer;
	}
	function insert_tables($bid){
		
		$this->db->query('CREATE TABLE IF NOT EXISTS `' .$bid.'_savesearch` (
  `search_id` int(11) NOT NULL AUTO_INCREMENT,
  `search_name` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `eid` int(11) NOT NULL,
  `modid` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`search_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');

	 $this->db->query("CREATE TABLE IF NOT EXISTS `".$bid."_site` (
					  `siteid` int(11) NOT NULL ,
					  `bid` int(11) NOT NULL,
					  `sitename` varchar(100) NOT NULL,
					  `siteicon` varchar(100) NOT NULL,
					  `siteinterest_opt	` text NOT NULL,
					  `sitedesc` text NOT NULL,
					  `sitevideo` varchar(100) NOT NULL,
					  `siteimg` varchar(100) NOT NULL,
                                          `tracknum` varchar(15) NOT NULL,
                                           `email` varchar(100) NOT NULL,
					  PRIMARY KEY (`siteid`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
					
	$this->db->query("CREATE TABLE IF NOT EXISTS `1_property` (
      `propertyid` varchar(25) NOT NULL,
      `bid` int(11) NOT NULL,
      `propertyname` varchar(100) NOT NULL,
	  `propertyicon` varchar(100) NOT NULL,
	      `status`  tinyint(2)  NOT NULL,
      PRIMARY KEY (`propertyid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8?");
	
	}
	function get_usertype(){
		$res=array();
		$query = $this->db->get('usertype');
		$ress=$query->result_array();
		$res['']=$this->lang->line('level_select');
		foreach($ress as $rec)
				$res[$rec['typeid']] = $rec['typename'];
		return $res;
	}		
	function insert_user(){
		if($this->input->post('usertype')==1){
			$g='';
			$emplist='';
			$usertype="Admin";
		}
		if($this->input->post('usertype')==2){
			$g=$this->input->post('groups');
			$emplist=$this->input->post('emplist');
			$usertype="Group Owner";
		}
		if($this->input->post('usertype')==3){
			$g='';
			$emplist=$this->input->post('emplist');
			$usertype="Employee";
		}
		$uid=$this->db->query("SELECT COALESCE(MAX(`uid`),0)+1 as id FROM `user`")->row()->id;
		$this->db->set('uid', $uid);
		$this->db->set('bid', $this->session->userdata('bid'));
		//$this->db->set('type', $this->input->post('usertype')); 
		$this->db->set('username',$this->input->post('username')); 
		$this->db->set('password',md5($this->input->post('password'))); 
		//$this->db->set('gid',$g); 
		$this->db->set('eid',$emplist); 
		$this->db->insert('user'); 
		$this->auditlog->auditlog_info($this->lang->line('level_module_user'),"New $usertype is Added");
		return $this->db->insert_id();
	}
	function check_userexistence(){
		$username=$this->input->post('username');
		$sql=$this->db->query("select * from user where username='$username'");
		return $sql->num_rows();
	}
	function userexists($str){
		$condition=($this->uri->segment('3')!="") ? " username='".$str ."' AND eid!='".$this->uri->segment('2')."'" : "username='".$str."'";
		$sql=$this->db->query("SELECT * FROM user WHERE ".$condition);
		if($sql->num_rows()>0){
			return "user exists";
		}else{
			return "available";
		}
	}
	function get_users_details(){
		$res=array();
		$s=$this->db->query("SELECT a.*,b.typename FROM user a,usertype b WHERE a.bid=".$this->session->userdata('bid')." AND a.uid!=".$this->session->userdata('uid')." AND a.type=b.typeid AND (a.status=1 or a.status=0)");
		if($s->num_rows()>0){
			$res=$s->result_array();
		}
		return $res;
	}
	function delete_user($id){
		$sql=$this->db->query("UPDATE user SET status=2 WHERE uid=$id");
		return 1;

	}
	function user_info($id){
		$sql=$this->db->query("SELECT * FROM user WHERE uid=".$id);
		return $sql->row();

	}	
	function status_change($id){
		$res=$this->user_info($id);
		if($res->status=="1"){
			$status=0;
		}else{
			$status=1;
		}
		$sql=$this->db->query("UPDATE user SET status=$status WHERE uid=$id");
		return 1;
	}
	function customfield_exists($fid){
		$sql=$this->db->query("SELECT * FROM ".$this->session->userdata('bid')."_customfieldsvalue WHERE fieldid=$fid");
		if($sql->num_rows()>0){
			return 1;
		}else{
			return 0;
		}
	}
	function get_packages(){
		$sql=$this->db->query("SELECT * FROM products");
		return $sql->result_array();
	}
	function delete_record($id){
		$sql=$this->db->query("delete from register_dummy where business_id=$id");
		return true; 	
	}
	function update_registerinfo($id){
		if(isset($_REQUEST['item_number'])){
			$sql=$this->db->query("update register_dummy set payment_status='completed',packageid=".$_REQUEST['item_number']." where business_id=$id");
		}
		$sqls=$this->db->query("select * from register_dummy where business_id=$id");
		return $sqls->row();
	}
	function getUserByEmail($email){
		$sql = "SELECT * FROM user WHERE username = '".$email."' AND status='1'";
		$rst = $this->db->query($sql);
		if($rst->num_rows()==1){
			return $rst->row_array();
		}else{
			return 0;
		}
	}
	function check_mobilecode(){
		$res=$this->db->query("SELECT * FROM user where userid='".$this->input->post('vcode')."'");
		if($res->num_rows()>0){
			$row=$res->row();
			$e_id=$this->db->query("SELECT * FROM ".$row->bid."_employee where eid=".$row->eid." and status=3 and empnumber='".$this->input->post('mobilenumber')."'");
			if($e_id->num_rows()>0){
				$row1=$e_id->row();
				$this->db->query("update ".$row->bid."_employee set status=1 where eid=".$row1->eid);
				if($row1->login!=0){
						$password = "";for($i = 0; $i<=10 ; $i++){$password .= ($i%2==0)? chr(rand(97,122)) : rand(0,9);}	
						$new_pass=md5($password);
						$this->db->query("update user set status=1,password='".$new_pass."' where eid=".$row1->eid." and uid=".$row->uid);
						$message_body=$this->emailmodel->email_body1($row1->empname,$row1->empemail,$password,$row->bid);
						$to  = $row1->empemail; // note the comma
						$subject = 'Registered Employee Details';
						$from='"MCube" <noreply@mcube.com>';
						 $message = $this->emailmodel->email_header().$message_body.$this->emailmodel->email_footer();
						$headers	 = 'MIME-Version: 1.0' . "\n".
							'Content-type: text/html; charset=iso-8859-1' . "\n".
							'From:'.$from. "\n" .
							'Reply-To:"MCube" <support@vmc.in>'."\n" .
							'X-Mailer: PHP/' . phpversion();
						
						$mess="Hi".$row1->empname."\nLog on to MCube using these cretials\nUsername:".$row1->empemail."\nPassword:".$password;
					$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
					$reply = $api."&to=".substr($row1->empnumber,-10,10)."&text=".urlencode($mess."\nPowered by MCube");
					file($reply);
					//~ mail($to, $subject, $message, $headers);
					$this->load->library('email');
					$this->email->from('noreply@mcube.com', 'MCube');
					$this->email->to($to);
					$this->email->subject($subject);
					$this->email->message($message);
					$this->email->send();	
					
					
					return true;
				}else{
					$mess="Hi".$row1->empname."\n Your mobile Verification is done";
					$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
					$reply = $api."&to=".substr($row1->empnumber,-10,10)."&text=".urlencode($mess."\nPowered by MCube");
					file($reply);
					return true;
				}
				
			}else{
				return false;
			}
			
		}else{
			return false;
			
		}
	}
	function get_executives(){
		$sql=$this->db->query("SELECT * FROM salesemp WHERE status=1");
		return $sql->result_array();
	}
	function get_partner(){
		$sql=$this->db->query("SELECT * FROM  partner where status=1");
		return $sql->result_array();
	}
	function getFCatDetails($cat){
		$resu = $this->db->query("SELECT  subcategory,type,label FROM  feedback where category like '".$cat."'")->result_array();
		return $resu;
	}
	function fCategoryData(){
		$dat = $_POST;
		foreach($dat as $k=>$v){
			if($k == 'category')
				($v != 'Select') ? $this->db->set($k,$v) : '';
			else
				$this->db->set($k,$v);
		}
		$this->db->set('status',1);
		$this->db->insert("feedbackData");
		return 1;
	}
	function cMVerify(){
		$email=$this->input->post('cemail');
		$number=$this->input->post('mobnumber');
		$sql=$this->db->query("SELECT * from verifiedemployee where number='".$number."' and email='".$email."' and status=0");
		if($sql->num_rows()>0){
				$res=$sql->row();
				if($res->vcode==$this->input->post('vcode')){
					return '1';
				}else{
					return '0';
				}
		}else{
			return '0';
		}
	}
	function check_Mverification(){
		$email=$this->input->post('cemail');
		$number=$this->input->post('mobnumber');
		$sql=$this->db->query("SELECT * from verifiedemployee where number='".$number."' and email='".$email."' and status=0");
		if($sql->num_rows()>0){
				$res=$sql->row();
				$api = "http://115.249.28.90/sms/sendSMS.php?from=vmc.in";
				$message = "\nPlease authorize VMC to call you from MCube ".base_url().'verifyYourNumber'." Verification code: ".$res->vcode;
				$sms = $api."&to=".substr($res->number,-10,10)."&text=".urlencode($message);
				$sms = file($sms);
			return '1';
		}else{
			return '0';
		}
	}
	function Update_m_verification($res){
		$email=$this->input->post('cemail');
		$number=$this->input->post('mobnumber');
		$sql=$this->db->query("SELECT * from verifiedemployee where number='".$number."' and email='".$email."'");
		$res=$sql->row();
		
		
		$this->db->set('status','1');
		$this->db->set('verified_date',date('Y-m-d h:i:s'));
		$this->db->where('email',$this->input->post('cemail'));
		$this->db->where('number',$this->input->post('mobnumber'));
		$this->db->update('verifiedemployee');
		
		$r=$this->db->query("SELECT * FROM ".$res->bid."_employee where eid='".$res->eid."'");
		$rs=$r->row();
		if($rs->status==0 || $rs->status==3){
			$this->db->set('status','1');
		}
		$this->db->set('verify','1');
		$this->db->where('eid',$res->eid);
		$this->db->update($res->bid."_employee");
		if($rs->login==1){
			$this->db->set('status','1');
			$this->db->where('eid',$res->eid);
			$this->db->where('bid',$res->bid);
			$this->db->update("user");
		}	
		
		$this->db->set('bid',$res->bid);
		$this->db->set('eid',$res->eid);
		$this->db->set('ename',$this->input->post('firstname').$this->input->post('lastname'));
		$this->db->set('email',$this->input->post('cemail'));
		$this->db->set('number',$this->input->post('mobnumber'));
		$this->db->insert('verifiedemp');
		
	}
	
}
/* end of model */
