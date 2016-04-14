<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$empDetail = $this->configmodel->getDetail('2',$this->session->userdata('eid'),'',$this->session->userdata('bid'));
$empName = $empDetail['empname'];
?>
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
<body data-page="forms" class="breakpoint-1200">
<!-- <div id="my-contact-div"><!-- contactable html placeholder </div>-->
<!-- BEGIN TOP MENU -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#sidebar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a id="menu-medium" class="sidebar-toggle tooltips">
                    <i class="fa fa-outdent"></i>
                </a>
                 <a class="navbar-brand" href="dashboard">
                    <img src="system/application/img/mcube-logo.png" alt="logo">
                </a>
            </div>
            <div class="navbar-center"><?php echo $html['title'];?></div>
            <div class="navbar-collapse collapse">
                <!-- BEGIN TOP NAVIGATION MENU -->
                <ul class="nav navbar-nav pull-right header-menu">
                    <!-- BEGIN USER DROPDOWN -->
                    <li class="dropdown" id="user-header">
                        <a href="#" class="dropdown-toggle c-white" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <span class="username"><?=ucfirst($empName)?></span>
                            <i class="fa fa-angle-down p-r-10"></i>
                        </a>
                     <ul class="dropdown-menu">
                          <li class="dropdown-footer clearfix">
							<a href="user/changepassword" class="toggle_fullscreen" title="Change Password">
								<i class="glyph-icon flaticon-fullscreen3"></i>
							</a>
							<a href="user/selfdisable/<?php echo $selfdis;?>" title="<?php if($selfdis == 0) echo "Offline"; else echo "Online";?>">
								<i class="glyph-icon flaticon-padlock23"></i>
							</a>
							<a href="user/logout" title="Logout">
								<i class="fa fa-power-off"></i>
							</a>
						      
                      </li>
                     </ul>
				 </li>	
                    <!-- END USER DROPDOWN -->
                </ul>
                <!-- END TOP NAVIGATION MENU -->
                
            </div>
        </div>
    </nav>
    <!-- END TOP MENU -->
     <!-- BEGIN WRAPPER -->
    <div id="wrapper">
		<?php
		$menubar = $this->lang->language['menu'];
		$URI = $this->uri->segments[1];
		$key = array_search($URI, $menubar);
		for($k=0;$k<count($menubar);$k++){
			if($k == $key){
				$cls1[$k] = "current active hasSub";
				$cls2[$k] = "current";
			}else{
				$cls1[$k] = '';
				$cls2[$k] = '';
			}
		}
		//echo "<pre>";print_r($cls1);print_r($cls2);exit; 
		?>
        <!-- BEGIN MAIN SIDEBAR -->
        <nav id="sidebar">
            <div id="main-menu">
                <ul class="sidebar-nav">
                    <li class="<?=$cls1[0];?><?=$cls1[1];?>">
                        <a href="#" title="Dashboard"><i class="fa fa-plus-square"></i><span class="sidebar-text">Dashboard</span><span class="fa arrow"></span></a>
                       <ul class="submenu collapse" >
							<li class="<?=$cls2[0];?>">
								<a href="<?php echo site_url($menubar[0]);?>"><span class="sidebar-text"><?php echo "Home";?></span></a>
							</li>
							<li class="<?=$cls2[1];?>">
								<a href="<?php echo site_url($menubar[1]);?>"><span class="sidebar-text"><?php echo $this->lang->line('blocknumbers');?></span></a>
							</li>
						</ul>
                    </li>   
                    <li class="cls <?=$cls1[2].$cls1[3].$cls1[4].$cls1[5].$cls1[6].$cls1[7];?>">
						  <a href="#" title="Mcube Track"><i class="fa fa-plus-square"></i><span class="sidebar-text"><?php echo $this->lang->line('label_calltrack');?></span><span class="fa arrow"></span></a>
                			 <ul class="submenu collapse">
							    <li>
                			      <a href="#"><span class="sidebar-text"><b><?=$this->lang->line('label_group')?></b></span><span class="fa arrow"></span></a>
									 <ul class="submenu collapse">
											<li class="<?=$cls2[3];?>"><a href="<?php echo site_url($menubar[3]);?>"><span class="sidebar-text"><?php echo $this->lang->line('label_groupadd');?></span></a></li>
											<li class="<?=$cls2[2];?>"><a href="<?php echo site_url($menubar[2]);?>/0"><span class="sidebar-text"><?php echo $this->lang->line('label_groupmanage');?></span></a></li>
											<li class="<?=$cls2[5];?>"><a href="<?php echo site_url($menubar[5]);?>/0"><span class="sidebar-text"><?php echo $this->lang->line('deletedgroup');?></span></a></li>
							        </ul>
							   </li>
							   <li><a href="#"><span class="sidebar-text"><b>Contacts</b></span><span class="fa arrow"></span></a>
									<ul class="submenu collapse">
										<li class="<?=$cls2[6];?>"><a href="<?php echo site_url($menubar[6]);?>" /><span class="sidebar-text"><?php echo "Add Contact";?></span></a></li>
										<li class="<?=$cls2[7];?>"><a href="<?php echo site_url($menubar[7]);?>"/><span class="sidebar-text"><?php echo "Contacts List";?></span></a></li>
									</ul>
								</li>	
								<li><a href= "#"><span class="sidebar-text"><b><?php echo $this->lang->line('label_incommingcall');?></b><span class="fa arrow"></span></a>
									<ul>
										<li><a href="Report/call"><span class="sidebar-text"><?php echo $this->lang->line('label_incoming');?></span></a></li>
										<li><a href="Report/call/at"><span class="sidebar-text"><?php echo $this->lang->line('level_AttendCalls');?></span></a></li>
										<li><a href="Report/call/m"><span class="sidebar-text"><?php echo $this->lang->line('label_incoming_missed');?></span></a></li>
										<li><a href="Report/call/q"><span class="sidebar-text"><?php echo $this->lang->line('label_incoming_qualified');?></span></a></li>
										<li><a href="Report/call/u"><span class="sidebar-text"><?php echo $this->lang->line('label_incoming_unqualified');?></span></a></li>
										<li><a href="Report/click2call"><span class="sidebar-text"><?php echo "Connect to Group";?></span></a></li>
										<?php if($this->session->userdata('roleid')==1){?>
										<li><a href="Report/undeleteCalls"><span class="sidebar-text"><?php echo "Deleted Calls";?></span></a></li>
										<li><a href="Report/undeleteC2Calls"><span class="sidebar-text"><?php echo "Deleted Connect to Group";?></span></a></li>
										<?php } ?>
										<li><a href="Report/callanalytics"><span class="sidebar-text"><?php echo "Call Analytics";?></span></a></li>
										<li><a href="Report/callarchive"><span class="sidebar-text"><?php echo $this->lang->line('label_callarchive');?></span></a></li>
										<?php if($this->session->userdata('roleid')==1){?>
											<li><a href="Report/callsummary"><span class="sidebar-text">Call Summary</span></a></li>
										<?php } ?>
									</ul>
								</li>	
							</ul>		
                    </li>
                    <li class="cls <?=$cls1[8].$cls1[9].$cls1[10]?>">
						  <a href="#" title="Mcube X"><i class="fa fa-plus-square"></i><span class="sidebar-text"><?=$this->lang->line('label_pbx')?></span><span class="fa arrow"></span></a>
								<ul class="submenu collapse">
									<li class="<?=$cls2[8];?>"><a href="<?=site_url($menubar[8])?>"><span class="sidebar-text"><?=$this->lang->line('label_pbxconfigure')?></span></a></li>
									<li class="<?=$cls2[9];?>"><a href="<?=site_url($menubar[9])?>"><span class="sidebar-text"><?=$this->lang->line('label_pbxlist')?></span></a></li>
									<li class="<?=$cls2[10];?>"><a href="<?=site_url($menubar[10])?>"><span class="sidebar-text"><?=$this->lang->line('label_pbxdeleted')?></span></a></li>
									<li><a href="<?php echo site_url('pbx/report');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_pbxreport');?></span></a></li>
								</ul>
                    </li>
                    <li class="<?=$cls1[13].$cls1[14].$cls1[15].$cls1[16].$cls1[17]?>">
						<a href="#" title="Ivrs"><i class="fa fa-plus-square"></i><span class="sidebar-text"><?php echo $this->lang->line('label_ivrs');?></span><span class="fa arrow"></span></a>
						<ul class="submenu collapse">
							<li class="<?=$cls2[13];?>"><a href="<?=site_url($menubar[13])?>"><span class="sidebar-text"><?php echo $this->lang->line('label_ivrsconfig');?></span></a></li>
							<li><a href="#"><span class="sidebar-text"><b><?php echo "IVRS Reference";?></b></span><span class="fa arrow"></span></a>
								<ul class="submenu collapse">
									<li class="<?=$cls2[16];?>"><a href="<?=site_url($menubar[16])?>"><span class="sidebar-text"><?php echo "Add Reference";?></span></a></li>
									<li class="<?=$cls2[17];?>"><a href="<?=site_url($menubar[17])?>"><span class="sidebar-text"><?php echo "List Reference";?></span></a></li>
								</ul>
							</li>
							<li class="<?=$cls2[14];?>"><a href="<?=site_url($menubar[14])?>"><span class="sidebar-text"><?php echo $this->lang->line('label_ivrsmanage');?></span></a></li>
							<li class="<?=$cls2[15];?>"><a href="<?=site_url($menubar[15])?>"><span class="sidebar-text"><?php echo $this->lang->line('label_ivrsdeleted');?></span></a></li>
							<li><a href="<?php echo site_url('ivrs/report');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_ivrsreport');?></span></a></li>
						</ul>
                    </li>
                    <li class="<?=$cls1[18].$cls1[19].$cls1[20].$cls1[21]?>">
						<a href="#" title="Employee"><i class="fa fa-plus-square"></i><span class="sidebar-text"><?php echo $this->lang->line('label_Employee');?></span><span class="fa arrow"></span></a>
						<ul class="submenu collapse">
							<li class="<?=$cls2[18];?>"><a href="<?=site_url($menubar[18])?>"><span class="sidebar-text"><?php echo $this->lang->line('label_Employeeadd');?></span></a></li>
							<li class="<?=$cls2[19];?>"><a href="<?=site_url($menubar[19])?>"><span class="sidebar-text"><?php echo $this->lang->line('label_Employeemanage');?></span></a></li>
							<li class="<?=$cls2[20];?>"><a href="<?=site_url($menubar[20])?>"><span class="sidebar-text"><?php echo $this->lang->line('label_Employee_delemp');?></span></a></li>
							<li class="<?=$cls2[21];?>"><a href="<?=site_url($menubar[21])?>"><span class="sidebar-text"><?php echo $this->lang->line('label_Employee_unconfirm');?></span></a></li>
						</ul>
					</li>
					<?php if($roleDetail['role']['admin']=='1'){?>
				    <li class="<?=$cls1[23].$cls1[24].$cls1[25].$cls1[26].$cls1[27].$cls1[28].$cls1[29].$cls1[30].$cls1[31].$cls1[32].$cls1[33].$cls1[34].$cls1[35].$cls1[36]?>">
						<a href="#" title="Admin"><i class="fa fa-plus-square"></i><span class="sidebar-text"><?php echo $this->lang->line('level_Settings');?></span><span class="fa arrow"></span></a>
						<ul class="submenu collapse">
							<li class="<?=$cls2[22];?>"><a href="<?=site_url($menubar[22])?>"><span class="sidebar-text"><?php echo $this->lang->line('level_edit_profile');?></span></a>
								<?php //if($this->systemmodel->countOFChild()>0){ ?><!--
									<ul>
										<li><a href='Business/RelatedBusiness'><span class="sidebar-text">Related Business</span></a></li>
									</ul>-->
								<?php //} ?>
							<li class="<?=$cls2[23];?>"><a href="<?php echo site_url($menubar[23]);?>"><span class="sidebar-text"><?php echo $this->lang->line('level_manage_customlabel');?></span></a></li>
							<li class="<?=$cls2[24];?>"><a href="<?php echo site_url($menubar[24]);?>"><span class="sidebar-text"><?php echo $this->lang->line('emprole');?></span></a></li>
							<li class="<?=$cls2[25];?>"><a href="<?php echo site_url($menubar[25]);?>"><span class="sidebar-text"><?php echo $this->lang->line('manage_role');?></span></a></li>
							<li><a href="#"><span class="sidebar-text"><b><?php echo $this->lang->line('holiday');?></b></span><span class="fa arrow"></span></a>
								<ul>
									<li  class="<?=$cls2[26];?>"><a href="<?php echo site_url($menubar[26]);?>"><span class="sidebar-text"><?php echo $this->lang->line('add_holiday');?></span></a></li>
									<li  class="<?=$cls2[27];?>"><a href="<?php echo site_url($menubar[27]);?>"><span class="sidebar-text"><?php echo $this->lang->line('list_holiday');?></span></a></li>
								</ul>
							</li>
							<li class="<?=$cls2[28];?>"><a href="<?php echo site_url($menubar[28]);?>"><span class="sidebar-text"><?php echo $this->lang->line('label_auditlog');?></span></a></li>
							<li class="<?=$cls2[29];?>"><a href="<?php echo site_url($menubar[29]);?>"><span class="sidebar-text"><?php echo $this->lang->line('label_manageregion');?></span></a></li>
							<li class="<?=$cls2[30];?>"><a href="<?php echo site_url($menubar[30]);?>"><span class="sidebar-text"><?php echo $this->lang->line('blocklist');?></span></a></li>
							<li><a href="#"><span class="sidebar-text"><b><?php echo "Email Templates";?></b></span><span class="fa arrow"></span></a>
								<ul>
									<li class="<?=$cls2[31];?>"><a href="<?php echo site_url($menubar[31]);?>"><span class="sidebar-text"><?php echo "Add Template";?></span></a></li>
									<li class="<?=$cls2[32];?>"><a href="<?php echo site_url($menubar[32]);?>"><span class="sidebar-text"><?php echo "List Template";?></span></a></li>
								</ul>
							<li><a href="#"><span class="sidebar-text"><b><?php echo "SMS Templates";?></b></span><span class="fa arrow"></span></a>
								<ul>
									<li class="<?=$cls2[33];?>"><a href="<?php echo site_url($menubar[33]);?>"><span class="sidebar-text"><?php echo "Add Template";?></span></a></li>
									<li class="<?=$cls2[34];?>"><a href="<?php echo site_url($menubar[34]);?>"><span class="sidebar-text"><?php echo "List Template";?></span></a></li>
								</ul>
							</li>
							<li class="<?=$cls2[35];?>"><a href="<?php echo site_url($menubar[35]);?>"><span class="sidebar-text"><?php echo "Email Configuration";?></span></a></li>
							<li class="<?=$cls2[36];?>"><a href="<?php echo site_url($menubar[35]);?>"><span class="sidebar-text"><?php echo "Account Settings";?></span></a></li>
						</ul>
					</li>
				<?php } ?>
				<?php if($lead_access == 1){?>
				<li>
					<a href="#" title="Leads"><i class="fa fa-plus-square"></i><span class="sidebar-text"><?php echo "Lead Management";?></span><span class="fa arrow"></span></a>
						<ul class="submenu collapse">
							<li><a href="#"><span class="sidebar-text"><?php echo $this->lang->line('label_leadGroup');?></span></a>
								<ul>
									<li><a href="<?php echo site_url('leads/lead_grp_add');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_addleadgrp');?></span></a></li>
									<li><a href="<?php echo site_url('leads/lead_grp_list');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_listleadgrp');?></span></a></li>
									<li><a href="<?php echo site_url('leads/lead_grp_list/del');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_delLeadgrp');?></span></a></li>
								</ul>
							</li>
							<?php if($leadView == 1 || $leadView == 3){?>
							<li><a href="#"><span class="sidebar-text"><?php echo $this->lang->line('label_prospects'); ?></span></a>
								<ul>
									<li><a href="<?php echo site_url('leads/addLead/0');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_addleads');?></span></a></li>
									<li><a href="<?php echo site_url('leads/index/0');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_listleads');?></span></a></li>
									<li><a href="<?php echo site_url('leads/deleteList/0');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_deleteleads');?></span></a></li>
								</ul>
							</li>
							<?php } else { ?>
							<li><a href="#"><?php echo $leadstatus[1];?></a>
								<ul>
									<li><a href="<?php echo site_url('leads/addLead/1');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_addleads');?></span></a></li>
									<li><a href="<?php echo site_url('leads/index/1');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_listleads');?></span></a></li>
									<li><a href="<?php echo site_url('leads/deleteList/1');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_deleteleads');?></span></a></li>
								</ul>
							</li>
							<li><a href="#"><span class="sidebar-text"><?php echo $this->lang->line('label_leads');?></span></a>
								<ul>
									<li><a href="<?php echo site_url('leads/addLead/2');?>"><span class="sidebar-text"><?php echo "Add Leads";?></span></a></li>
									<?php
									for($k=2;$k<=count($leadstatus);$k++){
									?>
									<li><a href="<?php echo site_url('leads/index/'.$k);?>"><span class="sidebar-text"><?php echo $leadstatus[$k];?></span></a></li>
									<?php
									}
									?>
									<li><a href="<?php echo site_url('leads/deleteList');?>"><span class="sidebar-text"><?php echo "Deleted Leads";?></span></a></li>
								</ul>
							</li>
							<?php } ?>
						</ul>
					</li>
					<?php }?>
				<li>
					<a href="#" title="Reports"><i class="fa fa-plus-square"></i><span class="sidebar-text"><?php echo $this->lang->line('label_report');?></span><span class="fa arrow"></span></a>
						<ul class="submenu collapse">
							<li><a href="#"><span class="sidebar-text"><?php echo $this->lang->line('label_incommingcall');?><span class="fa arrow"></span></a>
								<ul>
									<li><a href="Report/call"><span class="sidebar-text"><?php echo $this->lang->line('label_incoming');?></span></a></li>
									<li><a href="Report/call/at"><span class="sidebar-text"><?php echo $this->lang->line('level_AttendCalls');?></span></a></li>
									<li><a href="Report/call/m"><span class="sidebar-text"><?php echo $this->lang->line('label_incoming_missed');?></span></a></li>
									<li><a href="Report/call/q"><span class="sidebar-text"><?php echo $this->lang->line('label_incoming_qualified');?></span></a></li>
									<li><a href="Report/call/u"><span class="sidebar-text"><?php echo $this->lang->line('label_incoming_unqualified');?></span></a></li>
									<li><a href="Report/click2call"><span class="sidebar-text"><?php echo "Connect to Group";?></span></a></li>
									<?php if($this->session->userdata('roleid')==1){?>
									<li><a href="Report/undeleteCalls"><span class="sidebar-text"><?php echo "Deleted Calls";?></span></a></li>
									<li><a href="Report/undeleteC2Calls"><span class="sidebar-text"><?php echo "Deleted Connect to Group";?></span></a></li>
									<?php } ?>
									<li><a href="Report/callarchive"><span class="sidebar-text"><?php echo $this->lang->line('label_callarchive');?></span></a></li>
									<?php if($this->session->userdata('roleid')==1){?>
										<li><a href="Report/callsummary"><span class="sidebar-text">Call Summary</span></a></li>
									<?php } ?>
								</ul>
							</li>
							<li><a href="<?php echo site_url('ivrs/report');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_ivrsreport');?></span></a></li>
							<li><a href="<?php echo site_url('pbx/report');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_pbxreport');?></span></a></li>
							<li><a href="<?php echo site_url('Email/sent');?>"><span class="sidebar-text"><?php echo "Sent Emails";?></span></a></li>
							<li><a href="<?php echo site_url('Report/outbound_calls');?>"><span class="sidebar-text"><?php echo "Click2Connect";?></span></a></li>
							<li><a href="<?php echo site_url('Report/smsreport');?>"><span class="sidebar-text"><?php echo "SMS Report";?></span></a></li>
							<li><a href="<?php echo site_url('Report/followupreport');?>"><span class="sidebar-text"><?php echo "Follow ups Report";?></span></a></li>
							<li><a href="Report/callanalytics"><span class="sidebar-text"><?php echo "Call Analytics";?></span></a></li>
							<li><a href="Report/empBreakHis"><span class="sidebar-text"><?php echo "Break History";?></span></a></li>
							<li><a href="Report/outbound"><span class="sidebar-text"><?php echo "Outbound Calls";?></span></a></li>
						</ul>
				</li>
				
					<?php if($support_access == 1){?>
				<li>
					<a href="#" title="Support"><i class="fa fa-plus-square"></i><span class="sidebar-text"><?php echo "Support";?></span><span class="fa arrow"></span></a>
								<ul class="submenu collapse">
									<li><a href="#"><span class="sidebar-text"><?php echo "Support Groups";?></span></a>
										<ul>
											<li><a href="<?php echo site_url('support/addSupportGrp');?>"><span class="sidebar-text"><?php echo "Add Support Groups ";?></span></a></li>
											<li><a href="<?php echo site_url('support/listSupportGrp');?>"><span class="sidebar-text"><?php echo "List Support Groups ";?></span></a></li>
											<li><a href="<?php echo site_url('support/listSupportGrp/del');?>"><span class="sidebar-text"><?php echo "Deleted List Support Groups ";?></span></a></li>
										</ul>
									</li>
									<li><a href="#"><span class="sidebar-text"><?php echo "Support Tickets";?></span></a>
										<ul>
											<li><a href="<?php echo site_url('support/addSupportTkt');?>"><span class="sidebar-text"><?php echo "Add Ticket";?></span></a></li>
											<li><a href="<?php echo site_url('support/listSupportTkt');?>"><span class="sidebar-text"><?php echo "List Tickets";?></span></a></li>
											<li><a href="<?php echo site_url('support/delSupTktList');?>"><span class="sidebar-text"><?php echo "Deleted Tickets";?></span></a></li>
										</ul>
									</li>
									<?php if($this->session->userdata('roleid')==1){?>
									<li><a href="#"><span class="sidebar-text"><?php echo "Support Configuration";?></span></a>
										<ul>
											<li><a href="<?php echo site_url('support/followupsetup');?>"><span class="sidebar-text"><?php echo "Followup Settings";?></span></a></li>
										</ul>
									</li>
									<?php } ?>
								</ul>
					</li>
					<?php }?>
                </ul>
            </div>
        </nav>
        <!-- END MAIN SIDEBAR -->
