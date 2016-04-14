<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$empDetail = $this->configmodel->getDetail('2',$this->session->userdata('eid'),'',$this->session->userdata('bid'));
$empName = $empDetail['empname'];
?>

    <script>
      $.widget.bridge('uibutton', $.ui.button);
    </script>

  <body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

      <header class="main-header">

        <!-- Logo -->
        <a href="Home" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini"><b>M</b>C</span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg"><b>M</b>Connect</span>
        </a>
 <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
 
              <!-- User Account: style can be found in dropdown.less -->
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                 <!-- <img src="system/application/dist/img/mconnect1.png" class="user-image" alt="User Image">-->
                  <span class="hidden-xs"><?=ucfirst($empName)?></span>
                </a>
                 <ul class="dropdown-menu">
             
                  <!-- User image -->
              <li class="user-header">
                    <img src="system/application/dist/img/mconnect1.png" class="img-circle" alt="User Image">
                    <p>
                      <?=ucfirst($empName)?>
<!--
                      <small>Member since Nov. 2012</small>
-->
                    </p>
                  </li>
                  <!-- Menu Body -->
<!--
                  <li class="user-body">
                    <div class="col-xs-4 text-center">
                      <a href="#">Followers</a>
                    </div>
                    <div class="col-xs-4 text-center">
                      <a href="#">Sales</a>
                    </div>
                    <div class="col-xs-4 text-center">
                      <a href="#">Friends</a>
                    </div>
                  </li>
-->
                  <!-- Menu Footer-->
                  <li class="user-footer">
                    <div class="pull-left">

                      	<?php 
								if($selfdis == 0)
                      echo '<a href="user/selfdisable/'.$selfdis.'" class="btn btn-default btn-flat">online</a>';
                      else
					  echo '<a href="user/selfdisable/'.$selfdis.'" class="btn btn-default btn-flat">offline</a>';

							?>
                    </div>
                    <div class="pull-right">
					        <a href="user/logout" class="btn btn-default btn-flat">Signout</a>
                    </div>
                  </li>
                </ul>
              </li>
              <!-- Control Sidebar Toggle Button -->
        
            </ul>
          </div>
        </nav>
      </header>
    <!-- END TOP MENU -->
    <!-- BEGIN WRAPPER -->
        <!-- BEGIN MAIN SIDEBAR -->
        <?php
		$menubar = $this->lang->language['menu'];
		$URI = $this->uri->segments[1];
		$key = array_search($URI, $menubar);
		 //print_r($menubar);exit;
		//~ echo $URI; 
		//~ echo $key; exit;
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
		<aside class="main-sidebar">
        <section class="sidebar">
 <!--        <div class="user-panel">
            <div class="pull-left image">
              <img src="system/application/dist/img/mconnect1.png" class="img-circle" alt="User Image">
            </div>

            <div class="pull-left info">
              <p><?//=ucfirst($empName)?></p>
              <a href="#"><?php 
								//~ if($selfdis == 0)
                      //~ echo '<i class="fa fa-circle text-success"></i> Online';
                      //~ else
					  //~ echo '<i class="fa fa-circle text-failure"></i> Offline';

							?></a>
            </div>

          </div>
  -->
                <ul class="sidebar-menu">
                    <li class="<?=$cls1[0];?> treeview">
                        <a href="#"><i class="fa fa-fw fa-dashboard"></i><span><?php echo "Dashboard";?></span><i class="fa fa-angle-left pull-right"></i></a>
						<ul class="treeview-menu">				
				     		<li class="<?=$cls2[0];?>">
				     		<a href="<?php echo site_url($menubar[0]);?>"><i class="fa fa-circle-o"></i><?php echo "Dashboard"; ?></a>
				     		</li>
						</ul>
                    </li>   
                    <li class="<?=$cls1[9].$cls1[10].$cls1[11];?> treeview">
				   <a href="#" title="Property"><i class="fa fa-fw fa-building"></i><span><?php  echo $this->lang->line('label_property');?></span><i class="fa fa-angle-left pull-right"></i></a>
								<ul class="treeview-menu">
									<?php if($this->session->userdata('roleid')==1){?>
									<li class="<?=$cls2[9];?>"><a href="<?php echo site_url($menubar[9]);?>"><i class="fa fa-circle-o"></i><?php echo $this->lang->line('label_addprop');?></a></li>
									<?php } ?>
									<li class="<?=$cls2[10]?>"><a href="<?php echo site_url($menubar[10].'/0');?>"><i class="fa fa-circle-o"></i><?php echo $this->lang->line('label_listprop');?></a></li>
								    <?php if($this->session->userdata('roleid')==1){?>
									<li class="<?=$cls2[11];?>"><a href="<?php echo site_url($menubar[11].'/0');?>"><i class="fa fa-circle-o"></i><?php echo $this->lang->line('label_delprop');?></a></li>
									<?php } ?>
								</ul>
					 </li>	
					<li class="<?=$cls1[2].$cls1[3];?> treeview">
					<a href="#" title="Site"><i class="fa fa-fw fa-home"></i><span><?php echo $this->lang->line('label_lsite');?></span><i class="fa fa-angle-left pull-right"></i></a>
							<ul class="treeview-menu">
<!--
										<li class="<?=$cls2[1];?>"><a href="<?php echo site_url($menubar[1]);?>"><i class="fa fa-circle-o"></i><?php echo $this->lang->line('label_addsite');?></a></li>
-->
										<li class="<?=$cls2[2]?>"><a href="<?php echo site_url($menubar[2].'/0');?>"><i class="fa fa-circle-o"></i><?php echo $this->lang->line('label_listsite');?></a></li>
										<li class="<?=$cls2[3];?>"><a href="<?php echo site_url($menubar[3].'/0');?>"><i class="fa fa-circle-o"></i><?php echo $this->lang->line('label_delsite');?></a></li>
							</ul>
					 </li>
<!--
				  <li class="<?=$cls1[12].$cls1[13];?> treeview">
					<a href="#" title="Location"><i class="fa fa-fw fa-home"></i><span><?php //echo $this->lang->line('label_loc');?></span><i class="fa fa-angle-left pull-right"></i></a>
							<ul class="treeview-menu">
										<li class="<?=$cls2[12]?>"><a href="<?php //echo site_url($menubar[12].'/0');?>"><i class="fa fa-circle-o"></i><?php //echo $this->lang->line('label_listloc');?></a></li>
										<li class="<?=$cls2[13];?>"><a href="<?php//echo site_url($menubar[13].'/0');?>"><i class="fa fa-circle-o"></i><?php// echo $this->lang->line('label_delloc');?></a></li>
							</ul>
					 </li>
-->
					<li class="<?=$cls1[4].$cls1[5];?> treeview">
					<a href="#" title="Reports"><i class="fa fa-fw fa-file-text-o"></i><span><?php echo $this->lang->line('label_siterep'   );?></span><i class="fa fa-angle-left pull-right"></i></a>
								<ul class="treeview-menu">
										<li class="<?=$cls2[4];?>"><a href="<?php echo site_url($menubar[4].'/0');?>"><i class="fa fa-circle-o"></i><?php echo $this->lang->line('label_sitevis');?></a></li>
										<li class="<?=$cls2[5]?>"><a href="<?php echo site_url($menubar[5].'/0');?>"><i class="fa fa-circle-o"></i><?php echo $this->lang->line('label_siteref');?></a></li>
									</ul>
					 </li>	
					<li class="<?=$cls1[7].$cls1[8];?> treeview">
				   <a href="#" title="Offers"><i class="fa fa-fw fa-gift"></i><span><?php  echo $this->lang->line('label_siteoff');?></span><i class="fa fa-angle-left pull-right"></i></a>
								<ul class="treeview-menu">
									<?php if($this->session->userdata('roleid')==1){?>
									<li class="<?=$cls2[7];?>"><a href="<?php echo site_url($menubar[7].'/0');?>"><i class="fa fa-circle-o"></i><?php echo $this->lang->line('label_addsiteoff');?></a></li>
									<?php } ?>
									<li class="<?=$cls2[8]?>"><a href="<?php echo site_url($menubar[8].'/0');?>"><i class="fa fa-circle-o"></i><?php echo $this->lang->line('label_listsiteoff');?></a></li>
								</ul>
					 </li>	
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
            </ul>
              </section>
             </aside>
        <!-- END MAIN SIDEBAR -->
