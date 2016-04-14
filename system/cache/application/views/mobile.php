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
<form id="form" name="login" method="POST" action="user/mobile" autocomplete="off">
<div class="login">
<div class="logintitle"><span class="loginkey"><img src="images/loginkey.png" width="39" height="54" ></span> <span>Mobile Verification</span></div>
<div><img src="images/spacer.gif" height="5" width="400"></div>
<div class="sterror"><?php echo validation_errors(); ?></div>
<?php if($this->session->flashdata('msgt')){?>
<div <?=($this->session->flashdata('msgt'))?'style="display:inline;"':''?> class="session_message <?php echo $this->session->flashdata('msgt');?>"><span><?php echo $this->session->flashdata('msg');?></span></div>
<? }?>
<table>
	<tr>
		<th><label>Mobile Number : </label></th>
		<td><input name="mobilenumber" id="mobilenumber" type="text" class="validate[required,number]" size="30" value="<?=$this->input->post('login_username');?>" /></td>
	</tr>
	<tr>
		<th><label>Verfication Code : </label></th>
		<td><input name="vcode" id="vcode" type="text" class="validate[required]" size="30" /></td>
	</tr>
	
	<tr>
		<th></th>
		<td><input id="button1" type="submit" name="submit" value="Login" />
	
		</td>
	</tr>
	
</table>
<div class="loginfooter"></div>
</div>
</form>
</div>
</div>
