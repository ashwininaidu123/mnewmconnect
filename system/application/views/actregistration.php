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
	  $('#parents').live('change',function(event){
		 $('#pids').addClass("validate[required]");
	  });
	  $('#relatedto').live('change',function(event){
		  var r=$('#relatedto').val();
        	$.ajax({  
						type: "POST",  
						url: "user/get_relemps/"+r,  
						data:'package=allmodules', 
						success: function(msg){ 	
							
							$('#emplp option').each(function(i, option){ $(option).remove(); });
							$('#emplp').append(msg);
							
							}
						});
				
        	
        	
        	
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
<div class="columnblock">
<div id="box" style="margin:10px auto;width:970px;">
<form id="form" name="form" method="POST" action="user/actregister" autocomplete="off">
<div class="width50">
<h3>Business Info</h3>
<div class="sterror"><?php echo validation_errors(); ?></div>
<?php if($this->session->flashdata('msgt')){?>
<div <?=($this->session->flashdata('msgt'))?'style="display:inline;"':''?> class="session_message <?php echo $this->session->flashdata('msgt');?>"><span><?php echo $this->session->flashdata('msg');?></span></div>
<? }?>
	<table width="100%">
		<tr>
			<th><label for="login_businessname">Business Name : </label></th>
			<td><input name="login_businessname" id="login_businessname" type="text" size="15" class="validate[required]" value="<?php echo set_value('login_businessname');?>"/></td>
			<td></td>	
		</tr>
		<tr>
			<th><label for="cname">Contact Name : </label></th>
			<td><input name="cname" id="cname" type="text" size="15" class="validate[required]" value="<?php echo set_value('cname');?>"/></td>
			<td></td>
		</tr>
		<tr>
			<th><label for="cemail">Contact Email : </label></th>
			<td><input name="cemail" id="cemail" type="text" size="15" class="validate[required,custom[email],ajax[ajaxuseremail]]" value="<?php echo set_value('cemail');?>"/></td>
			<td></td>
		</tr>
		<tr>
			<th><label for="confirmemail">Confirm Email : </label></th>
			<td><input name="login_username" id="login_username" type="text" size="15" class="validate[required,custom[email],equals[cemail]]" value="<?php echo set_value('confirmemail');?>"/></td>
			<td></td>
		</tr>
		<tr>
			<th><label for="cphone">Web Address : </label></th>
			<td><input name="waddress" id="waddress" class="validate[custom[url]]" type="text" size="15" value="<?php echo set_value('waddress');?>" /></td>
			<td></td>
		</tr>
		<tr>
			<th><label for="cphone">Contact Phone : </label></th>
			<td><input name="cphone" id="cphone" type="text" size="15" class="validate[required]" value="<?php echo set_value('cphone');?>"/></td>
			<td></td>
		</tr>
		<tr>
			<th><label for="bphone">Business Phone : </label></th>
			<td><input name="bphone" id="bphone" type="text" size="15" class="validate[required]" value="<?php echo set_value('bphone');?>"/></td>
			<td></td>
		</tr>
		<tr>
			<th><label for="baddress">Business Address : </label></th>
			<td><textarea name="baddress" id="baddress" class="validate[required]" ><?php echo set_value('baddress');?></textarea></td>
			<td></td>
		</tr>
		<tr>
			<th><label for="baddress">Business Address1 : </label></th>
			<td><textarea name="baddress1" id="baddress1"  ><?php echo set_value('baddress1');?></textarea></td>
			<td></td>
		</tr>
		<tr>
			<th><label for="baddress">Parent : </label></th>
			<td><select name="parents" id="parents">
					<option value="">select</option>
					<option value="parent">Parent</option>
				</select></td>
			<td></td>
		</tr>
		<tr>
			<th><label for="baddress">Select Parent Business : </label></th>
			<td>
				<?php
					$js1 = 'id="pids" class=""';
					echo form_dropdown("pids",$this->profilemodel->getParentBusiness(),set_value('pids'),$js1);
				?>
			</td>
			<td></td>
		</tr>
		<tr>
			<th><label for="city">City : </label></th>
			<td><input name="city" id="city" type="text" size="15" class="validate[required]" value="<?php echo set_value('city');?>"/></td>
			<td></td>
		</tr>
		<tr>
			<th><label for="state">State : </label></th>
			<td><input name="state" id="state" type="text" size="15" class="validate[required]" value="<?php echo set_value('state');?>"/></td>
			<td></td>
		</tr>
		<tr>
			<th><label for="country">Country : </label></th>
			<td><input name="country" id="country" type="text" size="15" class="validate[required]" value="<?php echo set_value('country');?>"/></td>
			<td></td>
		</tr>
		<tr>
			<th><label for="locality">Locality : </label></th>
			<td><input name="locality" id="locality" type="text" size="15" value="<?php echo set_value('locality');?>"/></td>
			<td></td>
		</tr>
		<tr>
			<th><label for="zipcode">Zip Code : </label></th>
			<td><input name="zipcode" id="zipcode" type="text" size="15" class="validate[required]" value="<?php echo set_value('zipcode');?>"/></td>
			<td></td>
		</tr>
		<tr>
			<th><label>Language : </label></th>
			<td>					
				<?php
					$js = 'id="language" class="validate[required]"';
					echo form_dropdown("language",$this->profilemodel->get_languages(),set_value('language'),$js);
				?>
			</td>
			<td></td>
		</tr>
		<!--<tr>
			<th><label for="login_username">Login Email : </label></th>
			<td><input name="login_username" id="login_username" type="text" size="15" class="validate[required,custom[email]]" value="<?php echo set_value('login_username');?>"/></td>
			<td></td>
		</tr>-->
			<tr>
			<th><label for="login_password">Password : </label></th>
			<td><input name="login_password" id="login_password" type="password" class="validate[required]" value=""/></td>
			<td></td>
		</tr>
		<tr>
			<th><label for="cpassword">Confirm Password : </label></th>
			<td><input name="cpassword" id="cpassword" type="password" class="validate[required,equals[login_password]]" value=""/></td>
			<td></td>
		</tr>
		<tr>
			<th><label for="emp">Introducer  :</label></th>
			<td><select name="relatedto" id="relatedto" class="validate[required]">
					<option value="">----Select------</option>
					<option value="1">Partner</option>
					<option value="2">Executive</option>
				</select></td>
			<td></td>
		
		</tr>
		<tr>
			<th><label for="emp">Introduce By :</label></th>
			<td><select name="emplp" id="emplp" class="validate[required]">
					<option value="">----Select------</option>
					
				</select></td>
			<td></td>
		
		</tr>
		
		<tr>
			<th><label for="emp">Description :</label></th>
			<td><textarea name="desc" id="desc"></textarea></td>
			<td></td>
		
		</tr>
		
		<tr>
			<th><label for="validator">Captcha : </label></th>
			<td><input type="text" name="validator" class="validate[required,ajax[ajaxCaptchaCall]]" id="validator" style="width:80px" />
				<div style="float:right;text-align:left;width:160px">
					<img src="captcha.php?width=140&height=30&characters=7" id="cimg" />
					<img src="images/reload.png" id="reload" title="Click to reload" style="cursor:pointer;">
				</div>
			</td>
			<td></td>
		</tr>
		<tr>
		<th></th>
		<td><input id="button1" type="submit" value="Register" name="submit" />
			<a href="site/login">Login</a>
		</td>
		<td></td>
		</tr>
	</table>
</div>
<div class="width50">
<h2 class="tabletitle">Full Access To All VMC Features</h2>
<table width="100%" border="0" class="sinupicon">
	<tr>
		<td align="center"><a href="site/calltracking"><img src="system/application/img/ct_icon.gif" width="187" height="164" alt="tracking"></a></td>
		<td align="center"><a href="site/hostedivr"><img src="system/application/img/ivr_icon.gif" width="187" height="164" alt="IVRS"></a></td>
	</tr>
	<tr>
		<td align="center"><a href="site/textmessaging"><img src="system/application/img/sms_icon.gif" width="187" height="164" alt="SMS"></a></td>
		<td align="center"><a href="site/qrtrack"><img src="system/application/img/voice_icon.gif" width="187" height="164" alt="QR Track"></a></td>
	</tr>
</table>
</div>
</form>
</div>
</div>
</div>
