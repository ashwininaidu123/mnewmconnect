<link rel="stylesheet" href="<?=base_url();?>css/validationEngine.jquery.css" type="text/css"/>
<script src="<?=base_url();?>js/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script>
<script src="<?=base_url();?>js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
 <script>
jQuery(document).ready(function(){
	jQuery("#form").validationEngine();
	$("#reload").click(function(){
		$("#cimg").attr('src','captcha.php?width=120&height=28&characters=7&rand='+Math.random());
	});
	$('#send_me').live('click',function(event){
		
		if($('#cemail').val()!="" && $('#mobnumber').val()!=""){
			$('.sterror').html("");
			 $.ajax({
			  type: "POST",
			   url: "user/verification_resend/",
			 data: "cemail="+$('#cemail').val()+"&mobnumber="+$('#mobnumber').val(), 
			  dataType:"text",
		   success: function(msg){
			   var s=$.trim(msg);
				if(s==0){
					$('.sterror').html("Code doesnot Exist");
					return false;
				}else{
					$('.sterror').html("Verification code sent to your mobile");
					return false;
				}
		   }
		 })
		 return false;
		}else{
			$('.sterror').html("Please enter Email and Mobile Number");
			return false;
		}
		
	});
	
	
	
});

</script>
<div class="pagecontent">
<div class="columnblock">
<div class="column1">
	<div class="verifybanner">Verify Number</div>
	<div class="column1block">
		<p class="contenthead">Please Verify Your Number</p>
		<form name="form" id="form" method="post" action="site/number_verify" enctype="multipart/form-data">

		<table>
			<tr><td colspan="2"><div class="sterror"><?php echo validation_errors(); ?>
									
									<?php if($this->session->flashdata('msgt')){?>
<div <?=($this->session->flashdata('msgt'))?'style="display:inline;"':''?> class="session_message <?php echo $this->session->flashdata('msgt');?>"><span><?php echo $this->session->flashdata('msg');?></span></div>
<? }?>
						</div></td></tr>
						<tr><td>Verification Code</td><td><input type="text" size="15" class="formfield validate[required]" name="vcode" id="vcode" value="<?=set_value('vcode')?>"></td></tr>
			<tr><td>First Name</td><td><input type="text" size="15" class="formfield validate[required]" name="firstname" id="firstname" value="<?=set_value('firstname')?>"></td></tr>
			<tr><td>Last Name</td><td><input type="text" size="15" class="formfield validate[required]" name="lastname" id="lastname"value="<?=set_value('lastname')?>"></td></tr>
			<tr><td>Mobile Number</td><td><input type="text" size="15" class="formfield validate[required,custom[phone]]" name="mobnumber" id="mobnumber" value="<?=set_value('mobnumber')?>"></td></tr>
			<tr><td>Email</td><td><input type="text" size="15" class="formfield validate[required,custom[email]]" name="cemail" id="cemail"value="<?=set_value('cemail')?>"></td></tr>
			
			<tr><td>Address</td><td><textarea size="15" name="address" id="address"><?=set_value('address')?></textarea></td></tr>
			<tr><td>City</td><td><input type="text" size="15" class="formfield validate[required]" name="city" id="city" value="<?=set_value('city')?>"></td></tr>
			<tr><td>State</td><td><input type="text" size="15" class="formfield validate[required]" name="state" id="state" value="<?=set_value('state')?>"></td></tr>
			<tr><td>Pincode</td><td><input type="text" size="15" class="formfield validate" name="pincode" id="pincode" value="<?=set_value('pincode')?>"></td></tr>
			<tr><td>Captcha</td><td>
				<input type="text" name="validator" class="validate[required,ajax[ajaxCaptchaCall]]" id="validator" style="width:100px;float:left;" />
			<div style="float:left;text-align:left;width:140px;margin-left:10px;">
				<img src="captcha.php?width=120&height=28&characters=7" id="cimg" />
				<img src="images/reload.png" id="reload" title="Click to reload" style="cursor:pointer;">
			</div>
		</td>
			</td></tr>
			<tr>
				<td colspan="2">By entering your verification code sent to your mobile you authorize VMC to call you using MCube cloud telephony<br/> services. If you do not wish to receive calls from MCube then do not enter your verification code. You also agree to <br/> MCube <a href="http://www.mcube.com/termsofservice.html">Terms and Conditions.</a></td>
			
			</tr>
			<tr><td></td><td><input class="btn" id="submit" name="submit" type="submit" value="Submit">
						<input class="btn" name="send_me" type="button" id="send_me" value="Send Again">	
						&nbsp;&nbsp;</td> </tr>
		</table>
		</form>
	</div>
 </div>

<div class="column2"> 

</div>
</div>
</div>
