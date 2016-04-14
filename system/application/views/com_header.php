<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php echo doctype('xhtml1-trans'); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<base href="<?=base_url()?>">
<title><?php echo $html['title']?></title>
<link rel="shortcut icon" type="image/x-icon" href="system/application/img/icons/favicon.ico">
<?php if(isset($html['meta'])) echo meta($html['meta']);?>
<?php foreach($html['links'] as $link)echo link_tag($link);?>
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="system/application/css/ie-sucks.css" />
<![endif]-->
<link rel="stylesheet" type="text/css" href="system/application/css/contactable.css"  />
<?php foreach($html['scripts'] as $script) echo script_tag($script);?>
<? if(isset($file) && file_exists($file))require_once($file);?>

<script type="text/javascript" src="system/application/js/jquery.contactable.js"></script>
<script language="javascript" type="text/javascript">
   $(document).ready(function(){
		$('#my-contact-div').contactable(
        {
            subject: 'feedback URL:'+location.href,
			url: '/user/feedbackData',
            name: 'Name',
            email: 'Email',
            phone: 'Phone',
            dropdownTitle: 'Category',
            dropdownOptions: [<?php
            echo '"Select",';
           for($i=0,$j=1;$i<count($fcategories);$i++,$j++){
			   echo ($fcategories[$i]['category'] != '') ? '"'.$fcategories[$i]['category'].'"' : "";
			   echo ($j == count($fcategories)) ? '' :',' ;
		   }
           ?>],
            message : 'Message',
            submit : 'SEND',
            recievedMsg : 'Thank you for your message',
            notRecievedMsg : 'Sorry but your message could not be sent, try again later',
           /* disclaimer: 'Please feel free to get in touch, we value your feedback',*/
            disclaimer:'',
            hideOnSubmit: true
        });
	  });
</script>
</head>
<!--<body oncontextmenu="return false;">-->
<body>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-26443929-1']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type =
'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' :
'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0];
s.parentNode.insertBefore(ga, s);
  })();
</script>
<meta name="google-site-verification" content="ap-4Vcs5Y1bI5OdR6dI5rKITbpnCmtdhojnSVvoZ_-U" />
<div id="my-contact-div"><!-- contactable html placeholder --></div>
<div id="header">
	<div class="pagecontent">
		<table class="headertable">
			<tr>
				<td class="vmc">
					<a href="<?=site_url()?>">
					<?php if($this->data['html']['CLogo']!=""){ echo img(Base_url()."qrcode/company_logos/".$this->data['html']['CLogo'] ,true)?>
					<?php }else{ ?>
					<img src="system/application/img/logo.gif" width="120" height="95" border="0" /><?php } ?>
					</a>
				</td>
				<td>
				<table class="headertable">
					<tr><td colspan="2">
					<div class="toplinks">
						<div class="toplinksL"><img src="images/topnavl.gif"></div>
						<div id="topmenu1" class="toplinksM">
							<ul>
								<li><a class="navtabup">Products</a>
									<ul>
										<li><a href="site/overview">Overview of Products</a></li>
										<li><a href="site/hostedpbx">MCube X</a></li>
										<li><a href="site/calltracking">MCube Track</a></li> 
										<li><a href="site/hostedivr">MCube IVRS</a></li>
										<li><a href="site/lntfn">Local & Toll-Free numbers</a></li>
										
									</ul> 
								</li>
								<li><a>Pricing</a>
									<ul>
										<li><a href="site/pricing">Rate Card</a></li>
									</ul>
								</li>
								<li><a>Training</a>
									<ul>
										<li><a href="site/webinar">Webinar</a></li>
										<li><a href="site/faq">FAQ</a></li>
									</ul>
								</li>
								<li><a>Industries</a>
									<ul>
										<li><a href="site/automobile">Automobile</a></li>
										<li><a href="site/education">Education</a></li>
										<li><a href="site/entertainment">Entertainment</a></li>
										<li><a href="site/healthcare">Healthcare</a></li>
										<li><a href="site/insurance">Insurance</a></li>
										<li><a href="site/marketing">Marketing</a></li>
										<li><a href="site/realestate">Real Estate</a></li> 
										<li><a href="site/recruiting">Recruiting</a></li>
									</ul>
								</li>
								<li><a href="site/careers">Careers</a></li> 
								<li><a href="site/aboutus">About Us</a></li> 
								<li><a href="site/contactus">Contact Us</a></li>
								<li><a href="<?php echo site_url('support');?>">Support</a></li>
								<li><a><?=ucfirst($this->session->userdata('username'))?></a>
									<ul>
										<li><a href="user/changepassword"><?php echo $this->lang->line('label_changepassword');?></a></li>
										<li><a href="user/logout">Signout</a></li>
									</ul>
								</li>
							</ul>
						</div>
						<div class="toplinksR"><img src="images/topnavr.gif"></div>
					</div>
					</td></tr>
					<tr><td valign="bottom">
					
					<div id="topmenu" class="ddsmoothmenu" style="padding-top:0px">
						<ul>
							<li><a href="dashboard"><?php echo $this->lang->line('label_home');?></a></li>
							<li><a href="ManageGroup"><?=$this->lang->line('label_group')?></a>
								<ul>
									<li><a href="comGroup/add_group"><?php echo $this->lang->line('label_groupadd');?></a></li>
									<li><a href="ManageGroup"><?php echo $this->lang->line('label_groupmanage');?></a></li>
									<li><a href="DeleteGroup"><?php echo $this->lang->line('deletedgroup');?></a></li>
								</ul>
							</li>
							<li><a><?php echo $this->lang->line('label_ivrs');?></a>
							<ul>
								<li><a href="<?=site_url('ivrs/add')?>"><?php echo $this->lang->line('label_ivrsconfig');?></a></li>
								<li><a href="<?=site_url('ivrs/manage')?>"><?php echo $this->lang->line('label_ivrsmanage');?></a></li>
								<li><a href="<?=site_url('ivrs/deleted')?>"><?php echo $this->lang->line('label_ivrsdeleted');?></a></li>
							</ul>
							</li>
							<li><a><?=$this->lang->line('label_pbx')?></a>
							<ul>
								<li><a href="<?=site_url('pbx/configure')?>"><?=$this->lang->line('label_pbxconfigure')?></a></li>
								<li><a href="<?=site_url('pbx/manage')?>"><?=$this->lang->line('label_pbxlist')?></a></li>
								<li><a href="<?=site_url('pbx/deleted')?>"><?=$this->lang->line('label_pbxdeleted')?></a></li>
							</ul>
							</li>
							<li><a><?php echo $this->lang->line('label_Employee');?></a>
							<ul>
								<li><a href="AddEmployee"><?php echo $this->lang->line('label_Employeeadd');?></a></li>
								<li><a href="ManageEmployee"><?php echo $this->lang->line('label_Employeemanage');?></a></li>
								<li><a href="DeletedEmployee"><?php echo $this->lang->line('label_Employee_delemp');?></a></li>
								<li><a href="UnconfirmEmployees"><?php echo $this->lang->line('label_Employee_unconfirm');?></a></li>
							</ul>
							</li>
							<li><a href="<?php echo site_url('user/logout');?>" class="signout"><span class="signinout">Signout</span></a></li>
						</ul>
					</div> 
					</td>
					<td>
					<div class="mcube">
						<div><a href="dashboard"><img src="images/spacer.gif" width="125" height="40" border="0" /></a></div>
						<div class="navtollfree">18004192202</div>
					</div>
					</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		
		</div>
</div>
<div id="middle">
<div class="pagecontent" style="padding-top:10px;">
<?php if($this->session->flashdata('msgt')){?>
<script language="javascript" type="text/javascript">
   $(document).ready(function(){
	    
         $(".session_message").show();
         $(".session_message").fadeOut(5000);
   });
</script>
<div <?=($this->session->flashdata('msgt'))?'style="display:inline;"':''?> class="session_message <?php echo $this->session->flashdata('msgt');?>"><span><?php echo $this->session->flashdata('msg');?></span></div>
<? }?>
<div id="popupDiv"><!-- This Div is use to call popup --></div>
