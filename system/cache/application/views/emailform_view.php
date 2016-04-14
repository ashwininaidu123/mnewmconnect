<html>
<head>
<?php //foreach($html['links'] as $link)echo link_tag($link);?>
<link href="/system/application/css/style.css" rel="stylesheet" type="text/css" />
<?php //foreach($html['scripts'] as $script) echo script_tag($script);?>
<!-- <script type="text/javascript" src="system/application/js/layout.js?ver=1.0.2"></script> -->
 <base href="<?=base_url()?>">
<!-- BEGIN MANDATORY STYLE -->
<link rel="stylesheet" type="text/css" href="system/application/css/icons/icons.min.css" />
<link rel="stylesheet" type="text/css" href="system/application/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="system/application/css/plugins.min.css" />
<link rel="stylesheet" type="text/css" href="system/application/css/style.min.css" />
<link rel="stylesheet" type="text/css" href="system/application/css/custom.css" />
<link rel="stylesheet" type="text/css" href="system/application/css/jquery.alerts.css" />
<!-- END  MANDATORY STYLE -->
</head>
<body>
	<div id="topmenu" style="display:none"><ul></ul></div>
	<div id="topmenu1" style="display:none"><ul></ul></div>
	<div id="main-content">
             <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading bg-red">
                            <h3 class="panel-title"><?php echo $module['title']; ?></h3>
                           
                        </div>
                        <div class="panel-body">
							 <div class="sterror"><?php echo validation_errors(); ?></div>
							<div class="row">
								<?=$form['open']?>
                                <div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
								<?php if(isset($error) && $error=='1'){
									echo "<h4>To Send email your account have to configure outgoing server. Please contact your Admin</h4>";
								} else {?>
                                    <table class="" style="font-size:13px;">
                                        <?php foreach($form['fields'] as $field){ ?>
									
											<tr>
												<th><?=$field['label']?></th>
									
												<td>&nbsp;<?=$field['field']?></td>
											</tr>
								
											<tr height="10"></tr>
										<?php }
										if(isset($form['fields1'])){
											foreach($form['fields1'] as $field){?>
										
											<tr>
												<th valign="top"><?=$field['label']?></th>
								
												<td valign="top"><?=$field['field']?></td>
												<td valign="top"></td>
											</tr>
									
										<?php
											}
										} 	
										?>
                                    </table>
									<table align="center"><tr><td><center>
									<input id="compose" type="submit" name="update_system" class="btn btn-primary" value="<?=$this->lang->line('submit')?>" /> 
									<input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
									</center></td></tr></table>
									<?=$form['close']?>
									<? } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END MAIN CONTENT -->
    </div>
    <!-- END WRAPPER -->
	<script src="system/application/plugins/jquery-1.11.js"></script>
    <script src="system/application/plugins/jquery-migrate-1.2.1.js"></script>
    <script src="system/application/plugins/jquery-ui/jquery-ui-1.10.4.min.js"></script>
	<script src="system/application/js/ui/jquery-ui-timepicker-addon.js"></script>
	<script src="system/application/js/application.js"></script>
    <script src="system/application/plugins/bootstrap/bootstrap.min.js"></script>
    <script src="system/application/plugins/bootstrap-dropdown/bootstrap-hover-dropdown.min.js"></script>
    <script src="system/application/plugins/bootstrap-select/bootstrap-select.js"></script>
    <script src="system/application/plugins/mcustom-scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="system/application/plugins/mmenu/js/jquery.mmenu.min.all.js"></script>
    <script src="system/application/plugins/breakpoints/breakpoints.js"></script>
    <script src="system/application/js/jquery.timepicker.js"></script>
    <script src="system/application/js/application.js"></script>
    <script src="system/application/plugins/parsley/parsley.js"></script>
    <script src="system/application/plugins/parsley/parsley.extend.js"></script>
    <script src="system/application/js/jquery.alerts.js"></script>
    <script src="system/application/js/jquery.blockUI.js"></script>
</body>

</html>
