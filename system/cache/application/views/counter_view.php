<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php echo doctype('xhtml1-trans'); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<base href="<?=base_url()?>">
<title><?php echo $html['title']?></title>
    <!-- BEGIN MANDATORY STYLE -->
    <link rel="stylesheet" type="text/css" href="system/application/css/icons/icons.min.css" />
	<link rel="stylesheet" type="text/css" href="system/application/css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="system/application/css/plugins.min.css" />
	<link rel="stylesheet" type="text/css" href="system/application/css/style.min.css" />
	<link rel="stylesheet" type="text/css" href="system/application/css/custom.css" />
	<link rel="stylesheet" type="text/css" href="system/application/css/jquery.alerts.css" />
	<link rel="stylesheet" type="text/css" href="system/application/css/jquery.ui.datepicker.css" />
	<link rel="stylesheet" type="text/css" href="system/application/css/jquery.ui.theme.css" />
    <!-- END  MANDATORY STYLE -->
    <script src="system/application/plugins/modernizr/modernizr-2.6.2-respond-1.1.0.min.js"></script>
	<? if(isset($file) && file_exists($file))require_once($file);?>
<link rel="shortcut icon" type="image/x-icon" href="system/application/img/icons/favicon.ico">
    <!-- BEGIN MANDATORY SCRIPTS -->
    <script src="system/application/plugins/jquery-1.11.js"></script>
    <script src="system/application/plugins/jquery-migrate-1.2.1.js"></script>
    <script src="system/application/plugins/jquery-ui/jquery-ui-1.10.4.min.js"></script>
	<script src="system/application/js/ui/jquery-ui-timepicker-addon.js"></script>
	<script src="system/application/js/application.js"></script>
    <script src="system/application/plugins/bootstrap/bootstrap.min.js"></script>
    <script src="system/application/plugins/bootstrap-dropdown/bootstrap-hover-dropdown.min.js"></script>
    <script src="system/application/plugins/icheck/icheck.js"></script>
    <script src="system/application/plugins/mcustom-scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="system/application/plugins/mmenu/js/jquery.mmenu.min.all.js"></script>
    <script src="system/application/plugins/nprogress/nprogress.js"></script>
    <script src="system/application/plugins/charts-sparkline/sparkline.min.js"></script>
    <script src="system/application/plugins/breakpoints/breakpoints.js"></script>
    <script src="system/application/plugins/numerator/jquery-numerator.js"></script>
    <!-- END MANDATORY SCRIPTS -->
    <script src="system/application/js/application.js"></script>
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="system/application/js/jquery.alerts.js"></script>
    <script src="system/application/js/jquery.easy-confirm-dialog.js"></script>
    <script src="system/application/js/jquery.blockUI.js"></script>

    <!-- END PAGE LEVEL SCRIPTS -->
<? if(isset($file) && file_exists($file))require_once($file);?>
<!--<body oncontextmenu="return false;">-->


<body>
	<div id="topmenu" style="display:none"><ul></ul></div>
	<div id="topmenu1" style="display:none"><ul></ul></div>

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
	
<? //print_r($data);exit;
if(isset($file) && file_exists($file))require_once($file);?>
<div id="main-content">
			<!-- BEGIN ERROR BOX -->
            <?php 
            if($this->session->flashdata('msgt')){ $error1 = $this->session->flashdata('msgt'); }
			$error = validation_errors();
            if((isset($error) &&$error != '') || isset($error1)){
				$display = '';
			}else{
				$display = 'hide';
			}
			?>
			<div class="alert <?=($this->session->flashdata('msgt'))?$this->session->flashdata('msgt'):'error'?> <?=$display;?>" >
				<button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
				<?php echo validation_errors(); ?>
				<?php echo $this->session->flashdata('msg');?>
			</div>
			<!-- END ERROR BOX -->
             <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading bg-red">
                            <h3 class="panel-title"><?php echo $module['title']; ?></h3>
                            <input type="hidden" value="0" id="mod_Id" name="mod_Id">
							<input type="hidden" value="" id="fsizec" name="fsizec">
							<div style="float:right;">
							<?php echo $links;?>
							<? if(!isset($nosearch)){?>
							<a id="SearchButton"><span title="Search" class="glyphicon glyphicon-search"></span></a>
							<? }?>
							</div>
                        </div>
                        <div class="panel-body">
							
							<div class="row">


<? if(!isset($nosearch)){?><br>
<div class="searchBox"><?=$form['open']?>


                            <div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
                                 <table class="table table-striped table-hover">
                                      
	<?php
	foreach($form['form_field'] as $field1){?>
		<tr>
			<th><?=$field1['label']?></th>
			<td><?=$field1['field']?></td>
			<td></td>
		</tr>
	<? }?>
</table>
<center><input id="button1" type="submit" class="btn btn-primary" name="submit" value="<?=$this->lang->line('level_search')?>" /></center>

<?=$form['close']?>
</div>
<? }?>

<div class="pagination"><? echo $paging;?></div>


   <div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
              <table class="table table-striped table-hover">
<thead class="no-bd">
	<tr>
<? foreach($itemlist['header'] as $hd){ ?>
		<th><?=$hd?></th>
<? }?>
	</tr>
</thead>
<tbody class="no-bd-y"> 
<?php
$i=0;
foreach($itemlist['rec'] as $item){ ?>
	<tr class="<?=($i%2==0)?'even':''?>">
	<? foreach($item as $it){?><td><?=$it?></td><? }?>
	</tr>
<? $i++;}?>
</tbody>
</table>
</div>

<div class="pagination"><? echo $paging;?></div>
</div>
<div class="modal fade" id="modal-responsive" aria-hidden="true"></div>
<div class="modal fade" id="modal-empl" aria-hidden="true"></div>
