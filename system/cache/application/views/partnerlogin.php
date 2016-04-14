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
 <script>
            jQuery(document).ready(function(){
                // binds form submission and fields to the validation engine
                //alert("dd");
                  $(".session_message").show();
					$(".session_message").fadeOut(5000);
                jQuery("#form").validationEngine();
            });
            
          </script>
<div class="loginblock">
<form id="form" name="login" method="POST" action="<?=site_url('partner/index/')?>" autocomplete="off">
<div class="login">
<div class="logintitle"><span class="loginkey"><img src="images/loginkey.png" width="39" height="54" ></span> <span>Partner Login</span></div>
<div><img src="images/spacer.gif" height="5" width="400"></div>
<div class="sterror"><?php echo validation_errors(); ?></div>
<?php if($this->session->flashdata('msgt')){?>
<div <?=($this->session->flashdata('msgt'))?'style="display:inline;"':''?> class="session_message <?php echo $this->session->flashdata('msgt');?>"><span><?php echo $this->session->flashdata('msg');?></span></div>
<? }?>

<table>
	<tr><th><label>User Name : </label></th><td><input name="login_username" id="login_username" type="text"  class="validate[required,custom[email]]" size="30" /></td></tr>
	<tr><th><label>Password : </label></th><td><input name="login_password" id="login_password" type="password" class="validate[required]" size="30" /></td></tr>
	<tr><th><label for="validator">Captcha : </label></th><td><input type="text" name="validator" class="validate[required,ajax[ajaxCaptchaCall]]" id="validator" style="width:100px" />
			<div style="float:right;text-align:left;width:180px">
				<img src="captcha.php?width=140&height=30&characters=7" id="cimg" rel='1'  />
				<img src="images/reload.png" id="reload" title="Click to reload" style="cursor:pointer;"></div>
			</td></tr>
	
	<tr><th></th><td>
					<input id="button1" type="submit" name="submit" value="Login" />
					<a href="#">Forgot your password?</a>
				 </td></tr>
	</tr>
</table>
<div class="loginfooter"></div>
</div>
</form>
</div>

