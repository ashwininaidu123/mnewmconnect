<?php
$empName =$this->session->userdata('name');

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
                            <span class="username"><?php echo $empName; ?></span>
                            <i class="fa fa-angle-down p-r-10"></i>
                        </a>
                        <ul class="dropdown-menu">
                          <li class="dropdown-footer clearfix">
							<a href="user/changepassword" title="ChangePassword"><i class="glyphicon glyphicon-wrench"></i></a>
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
                    <li>
                        <a href="<?=site_url()?>Masteradmin/Landingpage" title="Home"><i class="fa fa-plus-square"></i><span class="sidebar-text"><?php echo $this->lang->line('label_home');?></span></a>
                    </li>   
                    <li>
						  <a href="Masteradmin/managePriList" title="Landing Numbers"><i class="fa fa-plus-square"></i><span class="sidebar-text">Landing Numbers</span><span class="fa arrow"></span></a>
                			 <ul class="submenu collapse">
							   <li><a href="Masteradmin/managePriList"><span class="sidebar-text">List Numbers</a></li>
							   <li><a href="Masteradmin/number_data"><span class="sidebar-text">Number History</span></a></li>	
							   <li><a href="Masteradmin/numberConfig"><span class="sidebar-text">Assign To Business</a></li>
							   <li><a href="Masteradmin/AssignMobileNumber"><span class="sidebar-text">Number Mapping</a></li>
							   <li><a href="Masteradmin/ManageunassignedNumbers"><span class="sidebar-text">Unassigned Numbers</a></li>
							   <li><a href="Masteradmin/prihistory"><span class="sidebar-text">Pri History</a></li>
							  <li><a href="Masteradmin/lbsnumbers"><span class="sidebar-text"><b>LBS Number</b></span><span class="fa arrow"></span></a>
							   <ul class="menu2 submenu collapse">
										<li><a href="Masteradmin/Addlbsnumber"><span class="sidebar-text">Add LBS Number</span></a></li>
										<li><a href="Masteradmin/lbsnumbers"><span class="sidebar-text">List LBS Numbers</span></a></li>
							   </ul>
							   </li>
							</ul>		
                    </li>
                    <li>
						<a href="javascript:void(0)" title="Packages"><i class="fa fa-plus-square"></i><span class="sidebar-text"><?php echo "Packages";?></span><span class="fa arrow"></span></a>
						<ul class="submenu collapse">
							<li><a href="Masteradmin/listaddons"><span class="sidebar-text"><?php echo "List Addon";?></span></a></li>
							<li><a href="Masteradmin/addpackage"><span class="sidebar-text"><?php echo "Add Package";?></span></a></li>
							<li><a href="Masteradmin/listpackage"><span class="sidebar-text"><?php echo "List Package";?></span></a></li>
							
						</ul>
                    </li>
                    <li>
						<a href="Masteradmin/creditConfig" title="Credit Assign"><i class="fa fa-plus-square"></i><span class="sidebar-text"><?php echo $this->lang->line('credit_config');?></span><span class="fa arrow"></span></a>
						
						<ul class="submenu collapse">
							<li><a href="Masteradmin/creditConfig"><span class="sidebar-text"><b><?php echo $this->lang->line('credit_config');?></b></span><span class="fa arrow"></span></a>
								<ul class="menu2 submenu collapse">
									<li><a href="Masteradmin/creditConfig"><span class="sidebar-text"><?php echo "Call Credit";?></span></a></li>
									<li><a href="Masteradmin/smsCreditConfig"><span class="sidebar-text"><?php echo "SMS Credit";?></span></a></li>
									<li><a href="Masteradmin/leadscredit"><span class="sidebar-text"><?php echo "Lead Credit";?></span></a></li>
								</ul>
							</li>
						
							<li><a href="Masteradmin/creditlist"><span class="sidebar-text"><b><?php echo $this->lang->line('creditlist');?></b></span><span class="fa arrow"></span></a>
									<ul class="menu2 submenu collapse">
										<li><a href="Masteradmin/creditlist"><span class="sidebar-text"><?php echo "Call CreditList";?></span></a></li>
										<li><a href="Masteradmin/smsCredilist"><span class="sidebar-text"><?php echo "SMS CreditList";?></span></a></li>
										<li><a href="Masteradmin/leadsversions"><span class="sidebar-text"><?php echo "Lead Credit List";?></span></a></li>
									</ul>
							  </li>
						</ul>
                    </li>
				    <li>
						<a href="Masteradmin/Businesslist" title="Business"><i class="fa fa-plus-square"></i><span class="sidebar-text"><?php echo "Business"?></span><span class="fa arrow"></span></a>
						<ul class="submenu collapse">
							<li><a href="Masteradmin/addbusinessuser"><span class="sidebar-text">Add Business</span></a>
							<li><a href="Masteradmin/Businesslist"><span class="sidebar-text">Business List</span></a></li>
						</ul>
					</li>
					  <li>
						<a href="Masteradmin/BlockNumber" title="Block Numbers"><i class="fa fa-plus-square"></i><span class="sidebar-text"><?php echo $this->lang->line('blocknumbers');?></span><span class="fa arrow"></span></a>
						<ul class="submenu collapse">
							<li><a href="Masteradmin/BlockNumber"><span class="sidebar-text"><?php echo $this->lang->line('blocknumbers');?></span></a>
							<li><a href="Masteradmin/blocklistnumbers"><span class="sidebar-text"><?php echo $this->lang->line('blocklist');?></span></a></li>
						</ul>
					</li>
				<?php if($this->session->userdata('role')!=2){ ?>
				<li>
					<a href="#" title="Admin Operations"><i class="fa fa-plus-square"></i><span class="sidebar-text">Admin Operations</span><span class="fa arrow"></span></a>
						<ul class="submenu collapse">
							   <li><a href="Masteradmin/ListAdminusers"><span class="sidebar-text"><b>Admins</b></span><span class="fa arrow"></span></a>
								<ul class="menu2 submenu collapse">
										<li><a href="Masteradmin/addAdminuser"><span class="sidebar-text">Add Admin</span></a></li>
						                <li><a href="Masteradmin/ListAdminusers"><span class="sidebar-text">List Admins</span></a></li>
						                <li><a href="Masteradmin/adminroles"><span class="sidebar-text">Admin roles</span></a></li>
							            <li><a href="Masteradmin/AdminLog"><span class="sidebar-text"><?php echo $this->lang->line('adminlog');?></span></a></li>
							            <li><a href="Masteradmin/addFCategories"><span class="sidebar-text">Add Feedback Catgeories</span></a></li>
							            <li><a href="Masteradmin/ListFCategories"><span class="sidebar-text">List Feedback Catgeories</span></a></li>
							            <li><a href="Masteradmin/listFeedbackData"><span class="sidebar-text">List Feedback Data</span></a></li>
								</ul>
								   <li><a href="Masteradmin/salesemp"><span class="sidebar-text"><b>Sales Executives</b></span><span class="fa arrow"></span></a> 
								   	<ul class="menu2 submenu collapse">
								       <li><a href="Masteradmin/addSalesEmp"><span class="sidebar-text">Add Executive</span></a></li>
							            <li><a href="Masteradmin/salesemp"><span class="sidebar-text">List Executives</span></a></li>
							         </ul>
								    <li><a href="Masteradmin/DNDEmpStatus"><span class="sidebar-text">DND Emp Status</span></a></li>
							</li><?php } ?>
							
				<!-- for spacing -->
				<li>&nbsp;</li>
				<li>&nbsp;</li>
				<li>&nbsp;</li>
				<li>&nbsp;</li>
                </ul>
            </div>
        </nav>
        <!-- END MAIN SIDEBAR -->
