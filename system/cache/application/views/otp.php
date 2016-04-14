<div class="pagecontent">
<link rel="stylesheet" href="<?=base_url();?>css/validationEngine.jquery.css" type="text/css"/>
<script src="<?=base_url();?>js/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script>
<script src="<?=base_url();?>js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
 <script>
jQuery(document).ready(function(){
	jQuery("#form").validationEngine();
	$("#reload").click(function(){
		$("#cimg").attr('src','captcha.php?width=140&height=30&characters=7&rand='+Math.random());
	});
});
</script>
<?php if($this->session->flashdata('msgt')){?>
<script language="javascript" type="text/javascript">
   $(document).ready(function(){
         $(".session_message").show();
         $(".session_message").fadeOut(5000);
   });
</script>
<? }?>
 <!-- BEGIN LOGIN BOX -->
    <div class="container" id="login-block">
        <div class="row">
            <div class="col-sm-6 col-md-4 col-sm-offset-3 col-md-offset-4">
                <div class="login-box clearfix animated flipInY">
                    <div class="page-icon animated bounceInDown">
                        <img src="system/application/img/logo2.png" alt="Key icon" width="40" height="46" >
                    </div>
                    <div class="login-logo">
                        <a href="#?login-theme-3">
                            
                        </a>
                    </div>
	  <!-- BEGIN ERROR BOX -->
                        <div class="alert alert-danger <?=($this->session->flashdata('msgt'))?'':'hide'?>">
                            <button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
                            <h4>Error!</h4>
							<?php echo validation_errors(); ?>
                            <?php echo $this->session->flashdata('msg');?>
                        </div>
                        <!-- END ERROR BOX -->
<form id="form" name="otp" method="POST" action="user/login" autocomplete="off">


<input type="hidden" name="login_username" value="<?=$this->session->userdata('u')?>" />
<input type="hidden" name="login_password" value="<?=$this->session->userdata('p')?>" />
<? //echo $this->session->userdata('otp')?>
<div style="width:100%;margin:0 auto;float:left;">
								<div style="width:70%;margin:0 auto;">
									<div style="width:100%;margin:0 auto;float:left;">
			<div style="  width: 100%;margin: 0 auto;padding: 15px 2px;float: left;text-align: center;">					
              Enter OTP sent to your mobile as SMS and your email.
         	</div>
		<input name="otp" id="otp" placeholder="One Time Password" type="text" class="validate[required] form-control" size="30" />
		<div style="width:100%;margin:0 auto;float:left;">
			  <button type="submit" id="button1" name="submit" class="btn btn-login">Submit</button>
		</div>
	

<div class="loginfooter"></div>
</div>
</form>
</div>
</div>
