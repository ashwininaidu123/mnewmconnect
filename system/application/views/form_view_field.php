<html>
<head>
<link href="/system/application/css/style.css" rel="stylesheet" type="text/css" />
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
							<div class="row">
								<?=$form['open']?>
                                <div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
								<? if(isset($error) && $error=='1'){
									echo "<h4>To Send email your account have to configure outgoing server. Please contact your Admin</h4>";
								} else {?>
                                    <table style="font-size:13px;">
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
                                    <? if(!isset($form['submit'])){ ?>	
									<table><tr><td><center>
									<input id="button1" type="submit" name="sendSMs" class="btn btn-primary SenDSMS" value="Send SMS" /> 
									<input id="button1" type="submit" name="sendEmail" class="btn btn-primary sendEmail" value="Send Email" /> 
									<input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
									</center></td></tr></table>
									<? } ?>
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
    <!--<script src="system/application/plugins/bootstrap-select/bootstrap-select.js"></script>-->
    <script src="system/application/js/application.js"></script>
    <script src="system/application/plugins/parsley/parsley.js"></script>
    <script src="system/application/plugins/parsley/parsley.extend.js"></script>
    <script src="system/application/js/jquery.alerts.js"></script>
    <script src="system/application/js/jquery.easy-confirm-dialog.js"></script>
    <script src="system/application/js/jquery.blockUI.js"></script>
</body>

</html>

