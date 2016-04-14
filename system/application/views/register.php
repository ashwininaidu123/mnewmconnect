<?php echo doctype('xhtml1-trans'); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $html['title']?></title>
<?php if(isset($html['meta'])) echo meta($html['meta']);?>
<?php foreach($html['links'] as $link)echo link_tag($link);?>
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="system/application/css/ie-sucks.css" />
<![endif]-->
<?php foreach($html['scripts'] as $script) echo script_tag($script);?>


<?php echo script_tag("system/application/js/jquery-1.5.2.js");?>
<?php echo script_tag("system/application/js/jquery.validate.js");?>
<script language="javascript" type="text/javascript">
   $(document).ready(function(){
	 $('#button2').click(function(){
		window.location.href="<?=base_url()?>user";
	 });
	
		jQuery.validator.addMethod("mobile", function(value, element) {
		return this.optional(element) || /^[7-9][0-9]{9}$/.test(value);
		}, "Should start with 7,8,9 and should have 10 digits");  
		jQuery.validator.addMethod("alpha", function(value, element) {
		return this.optional(element) || value == value.match(/^[a-z A-Z]+$/);
		},"Numeric is not allowed.");
		
         $(".session_message").show();
         $(".session_message").fadeOut(5000);
         $('#cemail').change(function(){
			// alert(this.val);
			 $('#login_username').val($('#cemail').val());
			// alert("ddD");
			check_username();
			return false;
			
		 });
		
	
		function check_username()
		{
			$.ajax({  
					type: "POST",  
					url: "<?php echo Base_url();?>user/check_username/",  
					data: "username="+$('#cemail').val(), 
					success: function(msg){ 
						if(msg=="1"){
							$('#error1').html($('#cemail').val() + " <?php echo $this->lang->line('err_username_err');?>");
							$('#cemail').val("");
							$('#cemail').focus();
							return false;	
						}else{
							$('#error1').html(" ");
							return true;
						}
						
					}
				});
			return false;
		}	
		 $("form").validate({
		
				rules:{
					cpassword:{
						equalTo: "#login_password"

					},
					waddress:{
						url:true
					},
					confirmemail:{
						equalTo: "#cemail"
					},
					login_username:{
						equalTo: "#cemail"
					},
					cphone:{
						mobile:true
					},
					bphone:{
						number:true	
					},
					zipcode:{
						number:true
					},locality:{
						alpha:true
					}
				},
				messages:{
					login_businessname:"Please enter Business name",
					login_username:"Please enter valid email address",
					cpassword:{
						equalTo:"Password and confirmpassword should be same"
					},
					cemail:{
						equalTo:"Employee Name and contact email should be same"
					},waddress:{
						url:"Please enter valid Url address"
					}
				},errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
					}
				
			});	
    
		
	
		
   });
</script>
</head>
<body>
	<div id="container">
    	<div id="header">
        	<h2>MQUBE</h2>
        	<br><br><br><br>
      </div>
      
          <div id="wrapper"><div id="topmenu" class="ddsmoothmenu"><ul></ul></div>

<div id="box">
<form id="form" name="form" method="POST" action="">
<fieldset id="priseries">
<legend>Regsiter</legend>

<div class="session_message <?php echo $this->session->flashdata('msgt');?>"><?php echo $this->session->flashdata('msg');?></div>
	<fieldset id="priseries"><legend>Business Info</legend>
<table width="100%">

	<tr>
		<th><label for="login_businessname"><?php echo $this->lang->line('level_businessname'); ?>: </label></th>
		<td><input name="login_businessname" id="login_businessname" type="text" size="15" class="required" value=""/></td>
		<td></td>	
	</tr>
	
	
	<tr>
		<th><label for="cname"><?php echo $this->lang->line('level_contactname'); ?>   : </label></th>
		<td><input name="cname" id="cname" type="text" size="15" class="required"/></td>
		<td></td>
	</tr>
	<tr>
		<th><label for="cemail"><?php echo $this->lang->line('level_contactemail'); ?> : </label></th>
		<td><input name="cemail" id="cemail" type="text" size="15" class="required email" value=""/></td>
		<td><div id="error1" style="color:red;"></div></td>
	</tr>
	<tr>
		<th><label for="confirmemail"><?php echo $this->lang->line('level_confirmemail'); ?> : </label></th>
		<td><input name="confirmemail" id="confirmemail" type="text" size="15" class="required email" value=""/></td>
		<td><div id="error1" style="color:red;"></div></td>
	</tr>
	<tr>
		<th><label for="cphone"><?php echo $this->lang->line('level_webaddress'); ?> : </label></th>
		<td><input name="waddress" id="waddress" type="text" size="15" /></td>
		<td></td>
	</tr>
	<tr>
		<th><label for="cphone"><?php echo $this->lang->line('level_contactphone'); ?> : </label></th>
		<td><input name="cphone" id="cphone" type="text" size="15" class="required"/></td>
		<td></td>
	</tr>
	<tr>
		<th><label for="bphone"><?php echo $this->lang->line('level_businessphone'); ?> : </label></th>
		<td><input name="bphone" id="bphone" type="text" size="15" class="required"/></td>
		<td></td>
	</tr>
	<tr>
		<th><label for="baddress"><?php echo $this->lang->line('level_businessaddress'); ?> : </label></th>
		<td><textarea name="baddress" id="baddress" class="required" ></textarea></td>
		<td></td>
	</tr>
	<tr>
		<th><label for="baddress"><?php echo $this->lang->line('level_businessaddress1'); ?> : </label></th>
		<td><textarea name="baddress1" id="baddress1"  ></textarea></td>
		<td></td>
	</tr>
	<tr>
		<th><label for="city"><?php echo $this->lang->line('level_city'); ?> : </label></th>
		<td><input name="city" id="city" type="text" size="15" class="required"/></td>
		<td></td>
	</tr>
	<tr>
		<th><label for="state"><?php echo $this->lang->line('level_state'); ?> : </label></th>
		<td><input name="state" id="state" type="text" size="15" class="required"/></td>
		<td></td>
	</tr>
	<tr>
		<th><label for="country"><?php echo $this->lang->line('level_country'); ?> : </label></th>
		<td><input name="country" id="country" type="text" size="15" class="required"/></td>
		<td></td>
	</tr>
	<tr>
		<th><label for="locality"><?php echo $this->lang->line('level_locality'); ?> : </label></th>
		<td><input name="locality" id="locality" type="text" size="15" /></td>
		<td></td>
	</tr>
	<tr>
		<th><label for="zipcode"><?php echo $this->lang->line('level_zipcode'); ?> : </label></th>
		<td><input name="zipcode" id="zipcode" type="text" size="15" class="required"/></td>
		<td></td>
	</tr>
	<tr>
		<th><label><?=$this->lang->line('level_language')?></label></th>
		<td>							
			<?php
				$js = 'id="language" class="required"';
				echo form_dropdown("language",$html['language'],'',$js);
			?>
		</td>
		<td></td>
	</tr>
</table>
</fieldset>
	<fieldset id="priseries" ><legend>Account Info</legend>
		<table width="100%" >
		<tr>
		<th><label for="login_username"><?php echo $this->lang->line('level_Username'); ?> : </label></th>
		<td><input name="login_username" id="login_username" type="text" size="15" class="required email" value=""/></td>
		<td></td>
	</tr>
		<tr>
		<th><label for="login_password"><?php echo $this->lang->line('level_password'); ?>  : </label></th>
		<td><input name="login_password" id="login_password" type="password" class="required" value=""/></td>
		<td></td>
	</tr>
	<tr>
		<th><label for="cpassword"><?php echo $this->lang->line('level_confirmpassword'); ?>  : </label></th>
		<td><input name="cpassword" id="cpassword" type="password" class="required" value=""/></td>
		<td></td>
	</tr>
		<input type='hidden' name="register_dummy" id="register_dummy" value="" />
		</table>
	
	
	



	</fieldset>
</fieldset>
<div align="right">
<input id="button1" type="submit" value="Register" name="submit" />
<input id="button2" type="button" value="Cancel" name="Cancel"/>

</div>
</form>
</div>

<? $this->load->view('footer');?>
