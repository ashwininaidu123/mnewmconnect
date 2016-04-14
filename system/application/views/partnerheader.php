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
<body oncontextmenu="return false;">
<div id="header">
	<div class="pagecontent">
		<div class="vmc"><a href="<?=site_url()?>"><?php if($this->data['html']['CLogo']!=""){ echo img(Base_url()."qrcode/company_logos/".$this->data['html']['CLogo'] ,true)?><?php }else{ ?><img src="images/spacer.gif" width="120" height="96" border="0" /><?php } ?></a></div>
		<div class="toplinks">
			<div class="toplinksL"><img src="images/topnavl.gif"></div>
			<div id="topmenu1" class="toplinksM">
				<ul>
					<li><a href="partner/logout">Logout</a></li>
				</ul>
			</div>
			<div class="toplinksR"><img src="images/topnavr.gif"></div>
		</div>
		<div class="mcube">
			<div><a href="#"><img src="images/spacer.gif" width="125" height="40" border="0" /></a></div>
			<div class="navtollfree"><span class="phoneicon1"></span>18004192202</div>
		</div>
		
        	
    <div id="topmenu" class="ddsmoothmenu" style="padding-top:70px;">
			<ul>
			<li><a href="<?=site_url()?>partner/Businessusers"><?php echo $this->lang->line('label_home');?></a></li>
			
			<li><a href="partner/AddBusinessUser"><?php echo $this->lang->line('USER');?></a>
				<ul>
					<li><a href="partner/AddBusinessUser"><?php echo $this->lang->line('addpartner_user');?></a></li>
					<li><a href="partner/Userslist"><?php echo $this->lang->line('partner_user');?></a></li>
				</ul>
			</li>
			<li><a href="Masteradmin/numberConfig"><?php echo $this->lang->line('manage_number_config');?></a>
				<ul>
					<li><a href="partner/managePriList"><?php echo $this->lang->line('manage_config');?></a></li>
					<li><a href="partner/numberConfig"><?php echo $this->lang->line('manage_landingnumber');?></a></li>
					<li><a href="partner/AssignMobileNumber"><?php echo $this->lang->line('manage_mobilenumber');?></a></li>
					<li><a href="partner/ManageunassignedNumbers"><?php echo $this->lang->line('manage_Unassignednumber');?></a></li>
				</ul>
			</li>
			<li><a href="payment"><?php echo "Payments";?></a>
								<ul>
					<li><a href="payment/Billconfig"><?php echo "Bill Config";?></a></li>
					<li><a href="payment/bills"><?php echo "Bills";?></a></li>
					
				</ul>
			</li>
		
		</ul>
		</div> 
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
