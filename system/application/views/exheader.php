<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php echo doctype('xhtml1-trans'); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<base href="<?=base_url()?>">
<title><?php echo $html['title']?></title>
<?php if(isset($html['meta'])) echo meta($html['meta']);?>
<?php foreach($html['links'] as $link)echo link_tag($link);?>
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="system/application/css/ie-sucks.css" />
<![endif]-->
<?php foreach($html['scripts'] as $script) echo script_tag($script);?>
<? if(isset($file) && file_exists($file))require_once($file);?>
</head>
<body>
<div id="header">
	<div class="pagecontent">
		<table class="headertable">
			<tr>
				<td class="vmc"><a href="<?=base_url()?>Executive/index"><img src="system/application/img/logo.gif" width="120" height="95" border="0" /></a></td>
				<td>
				<table class="headertable">
					<tr><td colspan="2">
					<div class="toplinks">
						<div class="toplinksL"><img src="images/topnavl.gif"></div>
						<div id="topmenu1" class="toplinksM">
							<ul>
								<li><a href="Executive/logout">Logout</a></li>
							</ul>
						</div>
						<div class="toplinksR"><img src="images/topnavr.gif"></div>
					</div>
					</td></tr>
					<tr><td valign="bottom">
					
					<div id="topmenu" class="ddsmoothmenu" style="padding-top:0px">
						<ul>
							<li><a href="<?=site_url()?>Executive/available"><?php echo $this->lang->line('label_home');?></a></li>
							<li>
								<a href="<?=site_url()?>Executive/available">Numbers</a>
								<?php if($this->session->userdata('isadmin') == 1){?>
								<ul>
									<li><a href="<?=site_url()?>Executive/Addnumber">Add Number</a></li>
									<li><a href="<?=site_url()?>Executive/available">Numbers</a></li>
								
								</ul>
								<?PHP } ?>
							</li>
							<li><a href="<?=site_url()?>Executive/blockNumbers">Blocked Numbers</a>
							<?php if($this->session->userdata('isadmin') == 1){?>
								<ul>
									<li><a href="<?=site_url()?>Executive/blkrequest">Requested Block Numbers</li>
								
								</ul>
								<?php } ?>
							</li>
							<li><a href="<?=site_url()?>Executive/emailsetting">Email Configuration</a></li>
							<li><a href="<?=site_url()?>Executive/NotAvailble">Not Available</a></li>
							<?php if($this->session->userdata('isadmin') == 1){?>
							<li>
								<a href="<?=site_url()?>Executive/listExecutives">Executives</a>
								<ul>
									<li><a href="<?=site_url()?>Executive/Addexecutive">Add Executive</a></li>
									<li><a href="<?=site_url()?>Executive/listExecutives">List Executives</a></li>
								
								</ul>
							</li>
							<?php } ?>
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



