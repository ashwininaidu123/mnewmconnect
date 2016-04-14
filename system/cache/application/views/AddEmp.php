<?php //echo script_tag("system/application/js/jquery.validate.js");?>
<script language="javascript" type="text/javascript">
   $(document).ready(function(){
		jQuery.validator.addMethod("alphanumeric", function(value, element) {
		return this.optional(element) || value == value.match(/^[a-z0-9A-Z]+$/);
		},"Only Characters, Numbers Allowed.");
		jQuery.validator.addMethod("mobile", function(value, element) {
		return this.optional(element) || /^[7-9][0-9]{9}$/.test(value);
		}, "Should start with 7 - 9 and 10 digits");  
		
	 $("form").validate({
		 
		 rules:{
					employeename:{
					alphanumeric:true
					},
					employeeid:{
					alphanumeric:true
					},
					mobile:{
						mobile:true
					},
					username:{
						equalTo:"#email"
					},
					cpassword:{
						equalTo:"#password"
					}
					
				},
			messages:{
				employeename:{
						required:"<?php echo $this->lang->line('err_empneed');?>",
						alphanumeric:"<?php echo $this->lang->line('err_aphanumeric');?>"
					},
				employeeid:{
						required:"<?php echo $this->lang->line('err_empeid');?>",
						alphanumeric:"<?php echo $this->lang->line('err_aphanumeric');?>"
					},
					email:{
						required:"<?php echo $this->lang->line('err_empemail');?>",
					},
					mobile:{
						required:"<?php echo $this->lang->line('err_empmobile');?>",
					},
					username:{
						required:"<?php echo $this->lang->line('err_empusername');?>",
					},
					password:{
						required:"<?php echo $this->lang->line('err_emppassword');?>",
					},
					cpassword:{
						required:"<?php echo $this->lang->line('err_empconfirmpassword');?>",
						equalTo:"<?php echo $this->lang->line('err_empequalspassword');?>"
					}
			},
		errorPlacement: function(error, element) {
		error.appendTo( element.parent().next() );
		}	
		});
	
	});
	
</script>
<script>
	$(function() {
		$("#businessname").change(function() {
			var url="<?php echo Base_url();?>group/get_prilist/"+$("#businessname").val();
			$.get(url , function(data){
				$("#prinumber").html(data); 	 
			});
		});
	});
	$(function() {
		$("#login").click(function() {
			if($('#login').attr('checked'))
			{
				$('#username').addClass('required email');
				$('#password').addClass('required');
				$('#cpassword').addClass('required');
				$("#username").val($("#email").val());
				$("#accdetail").toggle( 'blind', {}, 500 );
				
			}else{
				$('#username').removeClass();
				$('#password').removeClass();
				$('#cpassword').removeClass();
				$("#accdetail").toggle( 'blind', {}, 500 );
				
				return true;
				}
		});
	});
</script>

 	<div id="box">
		<h3><?php if(isset($geditlist)){ echo $this->lang->line('level_Edit_Group'); $action="Employee/edit/".$geditlist[0]['eid'];}else{ echo $this->lang->line('level_Add_Employee'); $action='Employee/add_employee';}?></h3>
			<?php
			$attributes = array('class' => 'email', 'id' =>'form','name'=>'form');		
			 echo form_open($action,$attributes);
			 //echo "<pre>";
			$sys=array_keys($system['e']);
		//	print_r($sys);
			// echo "</pre>";
			
			?>
			<fieldset id="priseries">
				<legend><?php echo $this->lang->line('level_Employeedetail');?></legend>
				<TABLE>
				
				<?php if(in_array($sys[2],$list_types)){?>
				<tr>
					<th><label><?=isset($system['e']['employeename'])?$system['e']['employeename']:$this->lang->line('e_employeename')?> : </label></th>
					<td>
						  <?php 
							if(isset($geditlist)){ $gname=$geditlist[0]['empname'];}else{ $gname='';}
							$data = array(
									'name'        => 'employeename',
									'id'          => 'employeename',
									'value'       => $gname,
									'class'       => 'required',
									);
							echo form_input($data);?>
					</td>
					<td></td>
				</tr>
				<?php  } ?>
				<?php if(in_array($sys[5],$list_types)){?>
				<tr>
					<th><label><?=isset($system['e']['employeeid'])?$system['e']['empid']:$this->lang->line('e_employeeid')?> : </label></th>
					<td>
						  <?php 
							if(isset($geditlist)){ $gname=$geditlist[0]['empid'];}else{ $gname='';}
							$data1 = array(
									'name'        => 'employeeid',
									'id'          => 'employeeid',
									'value'       => $gname,
									'class'       => 'required',
									);
							echo form_input($data1);?>
					</td>
					<td></td>
				</tr>
				<?php  } ?>
				<?php if(in_array($sys[4],$list_types)){?>
				<tr>
				
					<th><label><?=isset($system['e']['email'])?$system['e']['email']:$this->lang->line('e_email')?> : </label></th>
					<td>
						  <?php 
							if(isset($geditlist)){ $gname=$geditlist[0]['empemail'];}else{ $gname='';}
							$data2 = array(
									'name'        => 'email',
									'id'          => 'email',
									'value'       => $gname,
									'class'       => 'required email',
									);
							echo form_input($data2);?>
					</td>
					<td></td>
				</tr>
				<?php  } ?>
				<?php if(in_array($sys[1],$list_types)){?>
				<tr>
					<th><label><?=isset($system['e']['mobile'])?$system['e']['mobile']:$this->lang->line('e_mobile')?> : </label></th>
					<td>
						  <?php 
							if(isset($geditlist)){ $gname=$geditlist[0]['empnumber'];}else{ $gname='';}
							$data3 = array(
									'name'        => 'mobile',
									'id'          => 'mobile',
									'value'       => $gname,
									'class'       => 'required',
									);
							echo form_input($data3);?>
					</td>
					<td></td>
				</tr>
				<?php  } ?>
				
				<?php
				//print_r($custom['g']);
				if(isset($custom['e'])){
				$arr=array_keys($custom['e']);
				for($i=0;$i<sizeof($arr);$i++)
				  {
					$exs=explode("~",$custom['e'][$arr[$i]]);
					if(isset($editcustomlist)){ $cusval=$editcustomlist[$arr[$i]];}else{$cusval='';}	
					if(in_array($arr[$i],$list_types)){
				  ?>
				<tr>
					<th><label><?php echo $exs[0];?> : </label></th>
					<td>
						:  <?=$this->systemmodel->buildField($arr[$i],$cusval)?>
					</td>
					<td></td>
				</tr>		

				<?php
				}
				}					
							}
				?>
				<tr>
					<th><label><?=isset($system['e']['loginaccess'])?$system['e']['loginaccess']:$this->lang->line('e_loginaccess')?> : </label></th>
					<td>
						 <input name="login" id="login" type="checkbox" value="1" />
						
					</td>
					<td></td>
				</tr>
				</TABLE>
			</fieldset>
			<fieldset id="accdetail" style="display:none;">
				<legend><?php echo $this->lang->line('level_acc_detail');?></legend>
				<table>
					<tr>
						<th><label for="username"><?php echo $this->lang->line('level_Username');?> : </label></th>
						<?php if(isset($geditlist)){ $gname=$geditlist[0]['empemail'];}else{ $gname='';} ?>
						<td><input name="username" id="username" type="text" <?php if(isset($geditlist)) { ?>readonly <?php }?> /></td>
						<td></td>
					</tr>
					<tr>
						<th><label for="password"><?php echo $this->lang->line('level_password');?> : </label></th>
						<td><input name="password" id="password" type="password"/></td>
						<td></td>
					</tr>
					<tr>
						<th><label for="cpassword"><?php echo $this->lang->line('level_confirmpassword');?>: </label></th>
						<td><input name="cpassword" id="cpassword" type="password" /></td>
						<td></td>
					</tr>
					<tr>
						<th><label for="cpassword"><?php echo $this->lang->line('level_type');?>: </label></th>
						<td colspan="2">
									<input type="radio" name="usertype" id="usertype" value="1"><?php echo $this->lang->line('level_isadmin');?>
<!--
									<input type="radio" name="usertype" id="usertype" value="2"><?php echo $this->lang->line('level_groupowner');?>
-->									
									<input type="radio" name="usertype" id="usertype" value="3" checked="true"><?php echo $this->lang->line('level_employee');?>	
						</td>
					</tr>
				</table>
				</fieldset>
<table>	<tr>
					<td colspan="3" align="center">
					<?php if(isset($geditlist)){ echo form_submit('UpdateEmployee', 'UpdateEmployee');}else{  echo form_submit('AddEmployee', 'AddEmployee');}
					
					?>
					</td>
				</tr>		
			</table>
			<?php echo form_close();?>	
	</div>
	
