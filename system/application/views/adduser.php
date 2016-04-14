<?php echo script_tag("system/application/js/jquery.validate.js");?>
<script language="javascript" type="text/javascript">
   $(document).ready(function(){
		$('#emplist').hide();
		$('#grplist').hide();
		$('#usertype').change(function(){
			
				if($('#usertype').val()!=1){
					if($('#usertype').val()==2){
						$('#grplist').show();
						$('#emplist').show();
					}
					if($('#usertype').val()==3){
						$('#emplist').show();
						$('#grplist').hide();
					}	
				}else{
						$('#emplist').hide();
						$('#grplist').hide();
				}	
			
		});
	 $("form").validate({
		
		errorPlacement: function(error, element) {
		error.appendTo( element.parent().next() );
		}	
		});
	$('#username').blur(function(){

		check_username();
		return false;	
	});
	function check_username()
	{
		$.ajax({  
				type: "POST",  
				url: "<?php echo Base_url();?>user/check_username/",  
				data: "username="+$('#username').val(), 
				success: function(msg){ 
					if(msg=="1")
					{
					 	$('#error1').html("<?php echo $this->lang->line('err_username_err');?>");
						return false;	
					}
					
				}
			});

	}	
	
	$('#Adduser').click(function(){
	check_username();
	return false;	
	});	
});	
</script>
<div id="box">
		<h3><?php if(isset($profile_details)){ echo $this->lang->line('level_Edit_user'); $action="user/adduser/".$profile_details[0]['uid'];}else{ echo $this->lang->line('level_Add_user'); $action='user/adduser';}?></h3>
			<?php
			$attributes = array('class' => 'email', 'id' =>'form','name'=>'form');		
			 echo form_open($action,$attributes);
			 echo "<pre>";
			//print_r($groups); 
			// print_r($employees);
			 echo "</pre>";
			
			?>
			<fieldset id="priseries">
				<legend><?php echo $this->lang->line('level_userdetails');?></legend>
				<TABLE>
						
						<tr>
						<th><label for="username"><?php echo $this->lang->line('level_Username');?> : </label></th>
						<td><input name="username" id="username" type="text" class="required" /></td>
						<td><div id="error1" class="error" style="color:red;"></div></td>
					</tr>
					<tr>
						<th><label for="password"><?php echo $this->lang->line('level_password');?> : </label></th>
						<td><input name="password" id="password" type="password" class="required"  /></td>
						<td></td>
					</tr>
					<tr>
						<th><label for="cpassword"><?php echo $this->lang->line('level_confirmpassword');?>: </label></th>
						<td><input name="cpassword" id="cpassword" type="password" class="required" /></td>
						<td></td>
					</tr>
					<tr>
						<th><label for="cpassword"><?php echo $this->lang->line('level_usertype');?>: </label></th>
						<td>
							
						:  <?php
							 $js = 'id="usertype" class="required"';
							if(isset($geditlist)){ $gid=$geditlist[0]['bid'];}else{ $gid='';}
							 echo form_dropdown('usertype', $usertypes, $gid,$js);?>


						</td>	
						<td></td>

					</tr>
					<tr id="grplist">
						<th><label for="cpassword"><?php echo $this->lang->line('level_Group');?>: </label></th>
						<td>
							
						:  <?php
							 $js1 = 'id="groups"';
						//	if(isset($employees)){ $gid=$geditlist[0]['bid'];}else{ $gid='';}
							 echo form_dropdown('groups', $groups,'',$js1);?>
					</td>	
						<td></td>
					</tr>
					<tr id="emplist">
						<th><label for="cpassword"><?php echo $this->lang->line('level_Employee');?>: </label></th>
						<td>
							
						:  <?php
							 $js = 'id="emplist"';
						//	if(isset($employees)){ $gid=$geditlist[0]['bid'];}else{ $gid='';}
							 echo form_dropdown('emplist', $employees,'',$js);?>


						</td>	
						<td></td>
					</tr>
					
					</TABLE>


			</fieldset>
					<table><TR><TD colspan="3" align="center">
					<?php if(isset($geditlist)){ echo form_submit('UpdateGroup', 'UpdateGroup');}else{  echo form_submit('Adduser', 'Adduser');}
					
					?>

					</TD></TR></table>
			<?php echo form_close();?>

</div>
