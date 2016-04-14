<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php echo doctype('xhtml1-trans'); ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js sidebar-large lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js sidebar-large lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js sidebar-large lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js sidebar-large"> <!--<![endif]-->

<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<head>
    <!-- BEGIN META SECTION -->
    <meta charset="utf-8">
	<title>
		<? if(isset($this->data['html']['title'])) { echo $this->data['html']['title'];} else { echo "MCube"; }
		   if(isset($title)) { echo " | ".$title;}
		?>
	</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content="themes-lab" name="author" />
    <meta name="robots" content="no-cache" /> 
		<meta name="robots" content="no-cache" /> 
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="<? if(isset($keywords)) echo $keywords;?>" />
	<meta name="description" content="<? if(isset($description)) echo $description;?>" />
	<link rel="shortcut icon" type="image/x-icon" href="system/application/img/icons/favicon.ico">
    <base href="<?=base_url()?>">
    <!-- END META SECTION -->
    <!-- BEGIN MANDATORY STYLE -->
    <link href="system/application/css/icons/icons.min.css" rel="stylesheet">
    <link href="system/application/css/bootstrap.min.css" rel="stylesheet">
    <link href="system/application/css/plugins.min.css" rel="stylesheet">
    <link href="system/application/css/style.min.css" rel="stylesheet">
    <!-- END  MANDATORY STYLE -->
    <!-- BEGIN PAGE LEVEL STYLE -->
    <link href="system/application/css/animate-custom.css" rel="stylesheet">
    <!-- END PAGE LEVEL STYLE -->
    <script src="system/application/plugins/modernizr/modernizr-2.6.2-respond-1.1.0.min.js"></script>
    <link rel="stylesheet" href="<?=base_url();?>css/validationEngine.jquery.css" type="text/css"/>
<!--
	<script src="<? //base_url();?>js/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script>
	<script src="<? //base_url();?>js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
	<script>
	jQuery(document).ready(function(){
		jQuery("#form").validationEngine();
		$("#reload").click(function(){
			$("#cimg").attr('src','captcha.php?width=140&height=30&characters=7&rand='+Math.random());
		});
	</script>
-->
</head>
<body class="login fade-in" data-page="login">
