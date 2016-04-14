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
         $('#login_username').change(function(){
			check_username();
			return false;
			
		 });
		 function check_username()
		{
			$.ajax({  
					type: "POST",  
					url: "<?php echo Base_url();?>user/check_username/",  
					data: "username="+$('#login_username').val(), 
					success: function(msg){ 
						if(msg=="1")
						{
							$('#error1').html("<?php echo $this->lang->line('err_username_err');?>");
							return false;	
						}else{
							$('#error1').html(" ");
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
				},
				messages:{
					login_businessname:"Please enter Business name",
					login_username:"Please enter valid email address",
					cpassword:{
						equalTo:"Password and confirmpassword should be same"
					},
					
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
        <div id="wrapper">
<div id="box">
<div id="regBox">
<div id="box">
<form id="form" name="form" method="POST" action="">
<fieldset id="priseries">
<legend>Regsiter</legend>
<div class="session_message <?php echo $this->session->flashdata('msgt');?>"><?php echo $this->session->flashdata('msg');?></div>
	<fieldset id="priseries"><legend>Business Info</legend>
<table width="100%">
	<tr>
		<th><label for="login_businessname"><?php echo $this->lang->line('level_businessname'); ?>: </label></th>
		<td><input name="login_businessname" id="login_businessname" type="text"  class="required"/></td>
		<td></td>	
	</tr>
			<tr>
		<th><label for="login_username"><?php echo $this->lang->line('level_Username'); ?> : </label></th>
		<td><input name="login_username" id="login_username" type="text"  class="required email"/></td>
		<td><div id="error1" style="color:red;"></div></td>
	</tr>
		<tr>
		<th><label for="login_password"><?php echo $this->lang->line('level_password'); ?>  : </label></th>
		<td><input name="login_password" id="login_password" type="password" class="required"/></td>
		<td></td>
	</tr>
	<tr>
		<th><label for="cpassword"><?php echo $this->lang->line('level_confirmpassword'); ?>  : </label></th>
		<td><input name="cpassword" id="cpassword" type="password" class="required"/></td>
		<td></td>
	</tr>

	
</table>
</fieldset>

</fieldset>
<div align="right">
<input id="button1" type="submit" value="Proceed" name="submit" />
<input id="button2" type="button" value="Cancel" name="Cancel"/>

</div>
</form>
</div>
</div>
</div>
<? $this->load->view('footer');?>
