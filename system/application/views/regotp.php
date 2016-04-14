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
<div class="loginblock">
<form id="form" name="otp" method="POST" action="user/register" autocomplete="off">
<div class="login">
<div class="logintitle"><span class="loginkey"><img src="images/loginkey.png" width="39" height="54" ></span> <span>Request OTP</span></div>
<div><img src="images/spacer.gif" height="5" width="400"></div>
<div class="sterror"><?php echo validation_errors(); ?></div>
<?php if($this->session->flashdata('msgt')){?>
<div <?=($this->session->flashdata('msgt'))?'style="display:inline;"':''?> class="session_message <?php echo $this->session->flashdata('msgt');?>"><span><?php echo $this->session->flashdata('msg');?></span></div>
<? }?>
<table>
	<tr>
		<td colspan="2" style="color:red;">One time password has been sent to your mobile as SMS and your email. Please check and enter here.</td>
	</tr>
	<tr>
		<?php echo $this->session->userdata('otp');?>
		<th><label>One Time Password : </label></th>
		<td><input name="otp" id="otp" type="text" class="validate[required]" size="30" /></td>
	</tr>
	<tr>
		<th></th>
		<td><input id="button1" type="submit" name="submit" value="Submit" /></td>
	</tr>
</table>
<div class="loginfooter"></div>
</div>
</form>
</div>
</div>
