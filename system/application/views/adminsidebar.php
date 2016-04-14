<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$empDetail = $this->configmodel->getDetail('2',$this->session->userdata('eid'),'',$this->session->userdata('bid'));
$empName = $empDetail['empname'];
?>
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
                <a class="navbar-brand" href="Home">
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
							<a href="user/changepassword" class="toggle_fullscreen" title="ChangePassword">
								<i class="glyphicon glyphicon-wrench"></i>
							</a>
							<?php 
								if($selfdis == 0)
									echo '<a href="user/selfdisable/'.$selfdis.'" title="Offline"><i class="glyphicon glyphicon-eye-open"></i></a>';
								else
									echo '<a href="user/selfdisable/'.$selfdis.'" title="Online"><i class="glyphicon glyphicon-eye-close"></i></a>';
							?>
							<a href="user/logout" title="Logout"><i class="glyphicon glyphicon-off"></i></a>
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
        <!-- BEGIN MAIN SIDEBAR -->
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
		?>
		<nav id="sidebar">
            <div id="main-menu">
                <ul class="sidebar-nav">
                    <li class="<?=$cls1[0].$cls1[1].$cls1[84].$cls1[85].$cls1[86].$cls1[87].$cls1[88];?>">
                        <a href="#" title="Dashboard"><i class="fa fa-plus-square"></i><span class="sidebar-text">Dashboard</span><span class="fa arrow"></span></a>
                       <ul class="submenu collapse" >
							<li class="<?=$cls2[0].$cls2[84].$cls2[85].$cls2[86].$cls2[87].$cls2[88];?>">
								<a href="<?php echo site_url($menubar[0]);?>"><span class="sidebar-text"><?php echo "Home";?></span></a>
							</li>
							<li class="<?=$cls2[1];?>">
								<a href="<?php echo site_url($menubar[1]);?>"><span class="sidebar-text"><?php echo $this->lang->line('blocknumbers');?></span></a>
							</li>
						</ul>
                    </li>   
                    <li class="cls <?=$cls1[2].$cls1[3].$cls1[4].$cls1[5].$cls1[6].$cls1[7].$cls1[58].$cls1[59];?>">
						  <a href="#" title="Mcube Track"><i class="fa fa-plus-square"></i><span class="sidebar-text"><?php echo $this->lang->line('label_calltrack');?></span><span class="fa arrow"></span></a>
                			 <ul class="submenu collapse">
							    <li>
                			      <a href="#"><span class="sidebar-text"><b><?=$this->lang->line('label_group')?></b></span><span class="fa arrow"></span></a>
									 <ul class="menu2 submenu collapse">
											<li class="<?=$cls2[3];?>"><a href="<?php echo site_url($menubar[3]);?>"><span class="sidebar-text"><?php echo $this->lang->line('label_groupadd');?></span></a></li>
											<li class="<?=$cls2[2].$cls2[58].$cls2[59];?>"><a href="<?php echo site_url($menubar[2]);?>/0"><span class="sidebar-text"><?php echo $this->lang->line('label_groupmanage');?></span></a></li>
											<li class="<?=$cls2[5];?>"><a href="<?php echo site_url($menubar[5]);?>/0"><span class="sidebar-text"><?php echo $this->lang->line('deletedgroup');?></span></a></li>
							        </ul>
							   </li>
							   <li><a href="#"><span class="sidebar-text"><b>Contacts</b></span><span class="fa arrow"></span></a>
									<ul class="menu2 submenu collapse">
										<li class="<?=$cls2[6];?>"><a href="<?php echo site_url($menubar[6]);?>" /><span class="sidebar-text"><?php echo "Add Contact";?></span></a></li>
										<li class="<?=$cls2[7];?>"><a href="<?php echo site_url($menubar[7].'/0');?>"/><span class="sidebar-text"><?php echo "Contacts List";?></span></a></li>
									</ul>
								</li>	
								<li><a href="#"><span class="sidebar-text"><b><?php echo "Reports";?></b><span class="fa arrow"></span></a>
								<ul class="menu2 submenu collapse">
									<li class="<?=$cls2[46];?>"><a href="<?php echo site_url($menubar[46].'/all');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_incoming');?></span></a></li>
									<li class="<?=$cls2[66];?>"><a href="<?php echo site_url($menubar[66].'/all');?>"><span class="sidebar-text"><?php echo $this->lang->line('level_AttendCalls');?></span></a></li>
									<li class="<?=$cls2[65];?>"><a href="<?php echo site_url($menubar[65].'/all');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_incoming_missed');?></span></a></li>
									<li class="<?=$cls2[67];?>"><a href="<?php echo site_url($menubar[67].'/all');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_incoming_qualified');?></span></a></li>
									<li class="<?=$cls2[68];?>"><a href="<?php echo site_url($menubar[68].'/all');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_incoming_unqualified');?></span></a></li>
									<li class="<?=$cls2[80];?>"><a href="<?php echo site_url($menubar[80].'/0');?>"><span class="sidebar-text"><?php echo "Connect to Group";?></span></a></li>
									<li class="<?=$cls2[77];?>"><a href="<?php echo site_url($menubar[77].'/0/0');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_callarchive');?></span></a></li>
									<?php if($this->session->userdata('roleid')==1){?>
									<li class="<?=$cls2[79];?>"><a href="<?php echo site_url($menubar[79]);?>"><span class="sidebar-text"><?php echo "Deleted Calls";?></span></a></li>
									<li class="<?=$cls2[78];?>"><a href="<?php echo site_url($menubar[78]);?>"><span class="sidebar-text"><?php echo "Deleted Connect to Group";?></span></a></li>
									<li class="<?=$cls2[76];?>"><a href="<?php echo site_url($menubar[76].'/0');?>"><span class="sidebar-text">Call Summary</span></a></li>
									<?php } ?>
								</ul>
							</li>
							</ul>		
                    </li>
                    <li class="cls <?=$cls1[8].$cls1[9].$cls1[10].$cls1[61].$cls1[94]?>">
						<a href="#" title="MCube X"><i class="fa fa-plus-square"></i><span class="sidebar-text"><?=$this->lang->line('label_pbx')?></span><span class="fa arrow"></span></a>
						<ul class="submenu collapse">
							<li class="<?=$cls2[8];?>"><a href="<?=site_url($menubar[8].'/0')?>"><span class="sidebar-text"><?=$this->lang->line('label_pbxconfigure')?></span></a></li>
							<li class="<?=$cls2[9].$cls1[61];?>"><a href="<?=site_url($menubar[9].'/0')?>"><span class="sidebar-text"><?=$this->lang->line('label_pbxlist')?></span></a></li>
							<li class="<?=$cls2[10];?>"><a href="<?=site_url($menubar[10].'/0')?>"><span class="sidebar-text"><?=$this->lang->line('label_pbxdeleted')?></span></a></li>
							<li class="<?=$cls2[60].$cls2[94];?>"><a href="<?php echo site_url($menubar[60].'/all/0');?>"><span class="sidebar-text"><?php echo "Reports";?></span></a></li>
						</ul>
                    </li>
                    <li class="<?=$cls1[13].$cls1[14].$cls1[15].$cls1[16].$cls1[17].$cls1[62].$cls1[93]?>">
						<a href="#" title="IVRS"><i class="fa fa-plus-square"></i><span class="sidebar-text"><?php echo $this->lang->line('label_ivrs');?></span><span class="fa arrow"></span></a>
						<ul class="submenu collapse">
							<li class="<?=$cls2[13];?>"><a href="<?=site_url($menubar[13])?>"><span class="sidebar-text"><?php echo $this->lang->line('label_ivrsconfig');?></span></a></li>
							<li><a href="#"><span class="sidebar-text"><b><?php echo "IVRS Reference";?></b></span><span class="fa arrow"></span></a>
								<ul class="menu2 submenu collapse">
									<li class="<?=$cls2[16];?>"><a href="<?=site_url($menubar[16])?>"><span class="sidebar-text"><?php echo "Add Reference";?></span></a></li>
									<li class="<?=$cls2[17];?>"><a href="<?=site_url($menubar[17])?>"><span class="sidebar-text"><?php echo "List Reference";?></span></a></li>
								</ul>
							</li>
							<li class="<?=$cls2[14];?>"><a href="<?=site_url($menubar[14])?>"><span class="sidebar-text"><?php echo $this->lang->line('label_ivrsmanage');?></span></a></li>
							<li class="<?=$cls2[15];?>"><a href="<?=site_url($menubar[15])?>"><span class="sidebar-text"><?php echo $this->lang->line('label_ivrsdeleted');?></span></a></li>
							<li class="<?=$cls2[63].$cls2[93];?>"><a href="<?php echo site_url($menubar[63].'/all');?>"><span class="sidebar-text"><?php echo "Reports";?></span></a></li>
						</ul>
                    </li>
                    <li class="<?=$cls1[18].$cls1[19].$cls1[20].$cls1[21]?>">
						<a href="#" title="Employee"><i class="fa fa-plus-square"></i><span class="sidebar-text"><?php echo $this->lang->line('label_Employee');?></span><span class="fa arrow"></span></a>
						<ul class="submenu collapse">
							<li class="<?=$cls2[18];?>"><a href="<?=site_url($menubar[18].'/0')?>"><span class="sidebar-text"><?php echo $this->lang->line('label_Employeeadd');?></span></a></li>
							<li class="<?=$cls2[19];?>"><a href="<?=site_url($menubar[19].'/0')?>"><span class="sidebar-text"><?php echo $this->lang->line('label_Employeemanage');?></span></a></li>
							<li class="<?=$cls2[20];?>"><a href="<?=site_url($menubar[20])?>"><span class="sidebar-text"><?php echo $this->lang->line('label_Employee_delemp');?></span></a></li>
							<li class="<?=$cls2[21];?>"><a href="<?=site_url($menubar[21])?>"><span class="sidebar-text"><?php echo $this->lang->line('label_Employee_unconfirm');?></span></a></li>
						</ul>
					</li>
					<?php if($roleDetail['role']['admin']=='1'){?>
				    <li class="<?=$cls1[22].$cls1[23].$cls1[24].$cls1[25].$cls1[26].$cls1[27].$cls1[28].$cls1[29].$cls1[30].$cls1[31].$cls1[32].$cls1[33].$cls1[34].$cls1[35].$cls1[36].$cls1[64].$cls1[81].$cls1[82].$cls1[83]?>">
						<a href="#" title="Admin"><i class="fa fa-plus-square"></i><span class="sidebar-text"><?php echo $this->lang->line('level_Settings');?></span><span class="fa arrow"></span></a>
						<ul class="submenu collapse">
							<li class="<?=$cls2[22];?>"><a href="<?=site_url($menubar[22])?>"><span class="sidebar-text"><?php echo $this->lang->line('level_edit_profile');?></span></a>
								<?php //if($this->systemmodel->countOFChild()>0){ ?><!--
									<ul>
										<li><a href='Business/RelatedBusiness'><span class="sidebar-text">Related Business</span></a></li>
									</ul>-->
								<?php //} ?>
							<li class="<?=$cls2[23];?>"><a href="<?php echo site_url($menubar[23]);?>"><span class="sidebar-text"><?php echo $this->lang->line('level_manage_customlabel');?></span></a></li>
							<li class="<?=$cls2[24];?>"><a href="<?php echo site_url($menubar[24].'/0');?>"><span class="sidebar-text"><?php echo $this->lang->line('emprole');?></span></a></li>
							<li class="<?=$cls2[25];?>"><a href="<?php echo site_url($menubar[25]);?>"><span class="sidebar-text"><?php echo $this->lang->line('manage_role');?></span></a></li>
							<li><a href="#"><span class="sidebar-text"><b><?php echo $this->lang->line('holiday');?></b></span><span class="fa arrow"></span></a>
								<ul class="menu2 submenu collapse">
									<li  class="<?=$cls2[26];?>"><a href="<?php echo site_url($menubar[26]);?>"><span class="sidebar-text"><?php echo $this->lang->line('add_holiday');?></span></a></li>
									<li  class="<?=$cls2[27].$cls2[64];?>"><a href="<?php echo site_url($menubar[27].'/0');?>"><span class="sidebar-text"><?php echo $this->lang->line('list_holiday');?></span></a></li>
								</ul>
							</li>
							<li class="<?=$cls2[28];?>"><a href="<?php echo site_url($menubar[28].'/0');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_auditlog');?></span></a></li>
							<li class="<?=$cls2[29].$cls2[81].$cls2[82].$cls2[83];?>"><a href="<?php echo site_url($menubar[29]);?>"><span class="sidebar-text"><?php echo $this->lang->line('label_manageregion');?></span></a></li>
							<li class="<?=$cls2[30];?>"><a href="<?php echo site_url($menubar[30]);?>"><span class="sidebar-text"><?php echo $this->lang->line('blocklist');?></span></a></li>
							<li><a href="#"><span class="sidebar-text"><b><?php echo "Email Templates";?></b></span><span class="fa arrow"></span></a>
								<ul class="menu2 submenu collapse">
									<li class="<?=$cls2[31];?>"><a href="<?php echo site_url($menubar[31].'/0');?>"><span class="sidebar-text"><?php echo "Add Template";?></span></a></li>
									<li class="<?=$cls2[32];?>"><a href="<?php echo site_url($menubar[32]);?>"><span class="sidebar-text"><?php echo "List Template";?></span></a></li>
								</ul>
							<li><a href="#"><span class="sidebar-text"><b><?php echo "SMS Templates";?></b></span><span class="fa arrow"></span></a>
								<ul class="menu2 submenu collapse">
									<li class="<?=$cls2[33];?>"><a href="<?php echo site_url($menubar[33].'/0');?>"><span class="sidebar-text"><?php echo "Add Template";?></span></a></li>
									<li class="<?=$cls2[34];?>"><a href="<?php echo site_url($menubar[34]);?>"><span class="sidebar-text"><?php echo "List Template";?></span></a></li>
								</ul>
							</li>
							<li class="<?=$cls2[35];?>"><a href="<?php echo site_url($menubar[35]);?>"><span class="sidebar-text"><?php echo "Email Configuration";?></span></a></li>
							<li class="<?=$cls2[36];?>"><a href="<?php echo site_url($menubar[36]);?>"><span class="sidebar-text"><?php echo "Account Settings";?></span></a></li>
						</ul>
					</li>
				<?php } ?>
				<?php if($lead_access == 1){?>
				<li class="<?=$cls1[37].$cls1[38].$cls1[39].$cls1[40].$cls1[41].$cls1[42].$cls1[43].$cls1[44].$cls1[45].$cls1[54].$cls1[55].$cls1[89].$cls1[90].$cls1[91];?>">
					<a href="#" title="Leads"><i class="fa fa-plus-square"></i><span class="sidebar-text"><?php echo "Lead Management";?></span><span class="fa arrow"></span></a>
						<ul class="submenu collapse">
							<li><a href="#"><span class="sidebar-text"><b><?php echo $this->lang->line('label_leadGroup');?></b></span><span class="fa arrow"></span></a>
								<ul class="menu2 submenu collapse">
									<li class="<?=$cls2[37];?>"><a href="<?php echo site_url($menubar[37]);?>"><span class="sidebar-text"><?php echo $this->lang->line('label_addleadgrp');?></span></a></li>
									<li class="<?=$cls2[38].$cls2[54].$cls2[55].$cls2[91];?>"><a href="<?php echo site_url($menubar[38].'/0');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_listleadgrp');?></span></a></li>
									<li class="<?=$cls2[39];?>"><a href="<?php echo site_url($menubar[39].'/0');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_delLeadgrp');?></span></a></li>
								</ul>
							</li>
							<?php if($leadView == 1 || $leadView == 3){?>
							<li><a href="#"><span class="sidebar-text"><b><?php echo $this->lang->line('label_leads');?></b></span><span class="fa arrow"></span></a>
								<ul class="menu2 submenu collapse">
									<li class="<?=$cls2[43];?>"><a href="<?php echo site_url($menubar[43].'/0');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_addleads');?></span></a></li>
									<li class="<?=$cls2[44];?>"><a href="<?php echo site_url($menubar[44].'/0');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_listleads');?></span></a></li>
									<li class="<?=$cls2[45];?>"><a href="<?php echo site_url($menubar[45].'/0');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_deleteleads');?></span></a></li>
								</ul>
							</li>
							<?php } else { ?>
							<li><a href="#"><span class="sidebar-text"><b><?php echo $leadstatus[1];?></b></span><span class="fa arrow"></span></a>
								<ul class="menu2 submenu collapse">
									<li class="<?=$cls2[43];?>"><a href="<?php echo site_url($menubar[43].'/1');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_addleads');?></span></a></li>
									<li class="<?=$cls2[44].$cls2[89].$cls2[90];?>"><a href="<?php echo site_url($menubar[44].'/1');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_listleads');?></span></a></li>
									<li class="<?=$cls2[45];?>"><a href="<?php echo site_url($menubar[45].'/1');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_deleteleads');?></span></a></li>
								</ul>
							</li>
							<li><a href="#"><span class="sidebar-text"><b><?php echo $this->lang->line('label_leads');?></b></span><span class="fa arrow"></span></a>
								<ul class="menu2 submenu collapse">
									<li class="<?=$cls2[43];?>"><a href="<?php echo site_url($menubar[43].'/2');?>"><span class="sidebar-text"><?php echo "Add Leads";?></span></a></li>
									<?php
									for($k=2;$k<=count($leadstatus);$k++){
									?>
									<li><a href="<?php echo site_url($menubar[44].'/'.$k);?>"><span class="sidebar-text"><?php echo $leadstatus[$k];?></span></a></li>
									<?php
									}
									?>
									<li class="<?=$cls2[45];?>"><a href="<?php echo site_url($menubar[45].'/0');?>"><span class="sidebar-text"><?php echo "Deleted Leads";?></span></a></li>
								</ul>
							</li>
							<?php } ?>
						</ul>
					</li>
					<?php }?>
					<?php if($support_access == 1){?>
				<li class="<?=$cls1[47].$cls1[48].$cls1[49].$cls1[50].$cls1[51].$cls1[52].$cls1[53].$cls1[56].$cls1[57].$cls1[92];?>">
					<a href="#" title="Support"><i class="fa fa-plus-square"></i><span class="sidebar-text"><?php echo "Support Management";?></span><span class="fa arrow"></span></a>
						<ul class="submenu collapse">
							<li><a href="#"><span class="sidebar-text"><b><?php echo $this->lang->line('label_supGroup');?></b></span></a>
								<ul class="menu2 submenu collapse">
									<li class="<?=$cls2[47];?>"><a href="<?php echo site_url($menubar[47]);?>"><span class="sidebar-text"><?php echo $this->lang->line('label_addsupgrp');?></span></a></li>
									<li class="<?=$cls2[48].$cls2[56].$cls2[57];?>"><a href="<?php echo site_url($menubar[48].'/0');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_listsupgrp');?></span></a></li>
									<li class="<?=$cls2[49];?>"><a href="<?php echo site_url($menubar[49].'/0');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_delSupgrp');?></span></a></li>
								</ul>
							</li>

							<li><a href="#"><span class="sidebar-text"><b><?php echo $this->lang->line('label_tkts');?></b></span></a>
								<ul class="menu2 submenu collapse">
									<li class="<?=$cls2[50];?>"><a href="<?php echo site_url($menubar[50].'/0');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_addtkt');?></span></a></li>
									<li class="<?=$cls2[51].$cls2[92];?>"><a href="<?php echo site_url($menubar[51].'/0');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_listtkt');?></span></a></li>
									<li class="<?=$cls2[52];?>"><a href="<?php echo site_url($menubar[52]);?>"><span class="sidebar-text"><?php echo $this->lang->line('label_deletetkt');?></span></a></li>
								</ul>
							</li>
							<?php if($this->session->userdata('roleid')==1){?>
							<li><a href="#"><span class="sidebar-text"><b><?php echo "Support Configuration";?></b></span></a>
								<ul class="menu2 submenu collapse">
									<li class="<?=$cls2[53];?>"><a href="<?php echo site_url($menubar[53]);?>"><span class="sidebar-text"><?php echo "Followup Settings";?></span></a></li>
								</ul>
							</li>
							<?php } ?>
						</ul>
					</li>
					<?php }?>
				<li class="<?=$cls1[46].$cls1[65].$cls1[66].$cls1[67].$cls1[68].$cls1[63].$cls1[60].$cls1[69].$cls1[70].$cls1[71].$cls1[72].$cls1[73].$cls1[74].$cls1[75].$cls1[76].$cls1[77].$cls1[78].$cls1[79].$cls1[80]?>">
					<a href="#" title="Reports"><i class="fa fa-plus-square"></i><span class="sidebar-text"><?php echo $this->lang->line('label_report');?></span><span class="fa arrow"></span></a>
						<ul class="submenu collapse">
							<li><a href="#"><span class="sidebar-text"><b><?php echo $this->lang->line('label_incommingcall');?></b><span class="fa arrow"></span></a>
								<ul class="menu2 submenu collapse">
									<li class="<?=$cls2[46];?>"><a href="<?php echo site_url($menubar[46].'/all');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_incoming');?></span></a></li>
									<li class="<?=$cls2[66];?>"><a href="<?php echo site_url($menubar[66].'/all');?>"><span class="sidebar-text"><?php echo $this->lang->line('level_AttendCalls');?></span></a></li>
									<li class="<?=$cls2[65];?>"><a href="<?php echo site_url($menubar[65].'/all');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_incoming_missed');?></span></a></li>
									<li class="<?=$cls2[67];?>"><a href="<?php echo site_url($menubar[67].'/all');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_incoming_qualified');?></span></a></li>
									<li class="<?=$cls2[68];?>"><a href="<?php echo site_url($menubar[68].'/all');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_incoming_unqualified');?></span></a></li>
									<li class="<?=$cls2[80];?>"><a href="<?php echo site_url($menubar[80].'/0');?>"><span class="sidebar-text"><?php echo "Connect to Group";?></span></a></li>
									<?php if($this->session->userdata('roleid')==1){?>
									<li class="<?=$cls2[79];?>"><a href="<?php echo site_url($menubar[79]);?>"><span class="sidebar-text"><?php echo "Deleted Calls";?></span></a></li>
									<li class="<?=$cls2[78];?>"><a href="<?php echo site_url($menubar[78]);?>"><span class="sidebar-text"><?php echo "Deleted Connect to Group";?></span></a></li>
									<?php } ?>
									<li class="<?=$cls2[77];?>"><a href="<?php echo site_url($menubar[77].'/0/0');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_callarchive');?></span></a></li>
									<?php if($this->session->userdata('roleid')==1){?>
									<li class="<?=$cls2[76];?>"><a href="<?php echo site_url($menubar[76].'/0');?>"><span class="sidebar-text">Call Summary</span></a></li>
									<?php } ?>
								</ul>
							</li>
							<li class="<?=$cls2[63];?>"><a href="<?php echo site_url($menubar[63].'/all');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_ivrsreport');?></span></a></li>
							<li class="<?=$cls2[60];?>"><a href="<?php echo site_url($menubar[60].'/all/0');?>"><span class="sidebar-text"><?php echo $this->lang->line('label_pbxreport');?></span></a></li>
							<li class="<?=$cls2[69];?>"><a href="<?php echo site_url($menubar[69]);?>"><span class="sidebar-text"><?php echo "Sent Emails";?></span></a></li>
							<li class="<?=$cls2[70];?>"><a href="<?php echo site_url($menubar[70].'/0');?>"><span class="sidebar-text"><?php echo "Click2Connect";?></span></a></li>
							<li class="<?=$cls2[71];?>"><a href="<?php echo site_url($menubar[71]);?>"><span class="sidebar-text"><?php echo "SMS Report";?></span></a></li>
							<li class="<?=$cls2[72];?>"><a href="<?php echo site_url($menubar[72]);?>"><span class="sidebar-text"><?php echo "Follow ups Report";?></span></a></li>
							<li class="<?=$cls2[73];?>"><a href="<?php echo site_url($menubar[73]);?>"><span class="sidebar-text"><?php echo "Call Analytics";?></span></a></li>
							<li class="<?=$cls2[74];?>"><a href="<?php echo site_url($menubar[74].'/0');?>"><span class="sidebar-text"><?php echo "Break History";?></span></a></li>
							<li class="<?=$cls2[75];?>"><a href="<?php echo site_url($menubar[75]);?>"><span class="sidebar-text"><?php echo "Outbound Calls";?></span></a></li>
						</ul>
				</li>
				<li>
                     	<a href="user/logout" title="Logout"><i class="fa fa-power-off"></i><span>Logout</span></a>
							
							
                    </li>   
				<!-- for spacing -->
				<li>&nbsp;</li>
				<li>&nbsp;</li>
				<li>&nbsp;</li>
				<li>&nbsp;</li>
                </ul>
            </div>
        </nav>
        <!-- END MAIN SIDEBAR -->

