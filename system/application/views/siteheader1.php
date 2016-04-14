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
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="system/application/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="system/application/dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="system/application/plugins/iCheck/square/blue.css">
    <!-- END  MANDATORY STYLE -->
    <!-- END PAGE LEVEL STYLE -->
       <script src="system/application/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <script src="system/application/bootstrap/js/bootstrap.min.js"></script>
    <script src="system/application/plugins/iCheck/icheck.min.js"></script>
    <script>
      $(function () {
        $('input').iCheck({
          checkboxClass: 'icheckbox_square-blue',
          radioClass: 'iradio_square-blue',
          increaseArea: '20%' // optional
        });
      });
    </script>
</head>
<body class="hold-transition login-page">
