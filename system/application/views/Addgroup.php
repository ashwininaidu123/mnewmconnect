<?php echo script_tag("system/application/js/jquery.validate.js");?>
<script language="javascript" type="text/javascript">
   $(document).ready(function(){
		var size="<?php if(isset($custom['g'])) { $arr=array_keys($custom['g']); echo sizeof($arr); } ?>";
		jQuery.validator.addMethod("alphanumeric", function(value, element) {
		return this.optional(element) || value == value.match(/^[a-z0-9A-Z]+$/);
		},"Only Characters, Numbers Allowed.");
		jQuery.validator.addMethod("mobile", function(value, element) {
		return this.optional(element) || /^[7-9][0-9]{9}$/.test(value);
		}, "Should start with 7 - 9 and 10 digits");  
		jQuery.validator.addMethod("numeric", function(value, element) {
		return this.optional(element) ||value == value.match(/^[0-9]+$/);
		}, "Should start with 7 - 9 and 10 digits"); 
	 $("form").validate({
		
			rules:{
				
				addnumber:{
					numeric:true
				}
				
			},
			messages:{
				groupname:{
						required:"<?php echo $this->lang->line('err_groupneed');?>"
						
					},
				addnumber:{
					required:"<?php echo $this->lang->line('err_groupaddnumber');?>",
					numeric:"<?php echo $this->lang->line('err_mobile');?>"
				},
				rules:{
					required:"<?php echo $this->lang->line('err_grouprules'); ?>"
				},
				groupowner:{
					required:"<?php echo $this->lang->line('err_groupowner'); ?>"
				}
			},
		
		
		errorPlacement: function(error, element) {
		error.appendTo( element.parent().next() );
		}	
		});
		$("#AddEmployee").live('click',function(event) {
			 $("#forms").validate({
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
					},
					submitHandler: function(){
						$.ajax({  
							type: "POST",  
							url: "<?php echo base_url();?>group/add_addgroupowner/",  
							data:$("#forms").serialize(), 
							success: function(msg){ 
								$('#groupowner').children().remove().end().append(msg) ;
								$('#popupDiv').html("");
								$.unblockUI();
							}
						});
					}
				
			});
						
					//return false;
			
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
</script>

 <div id="main-content">
            <div class="page-title"> <i class="icon-custom-left"></i>
                <h4><?php if(isset($geditlist)){ echo $this->lang->line('level_Edit_Group'); $action="group/edit/".$geditlist[0]['gid'];}else{ echo $this->lang->line('level_Add_Group'); $action='group/add_group';}?></h4>
            </div>
            <div class="row" style="padding-left: 10px;">
                <div class="col-md-12">

			<?php
			$attributes = array('class' => 'email', 'id' =>'form','name'=>'form');		
			 echo form_open($action,$attributes);
			$sys=array_keys($system['g']);
			?>

				<TABLE>
				<?php if(in_array($sys[3],$list_types)){?>
				<tr>
					<th><label><?=isset($system['g']['groupname'])?$system['g']['groupname']:$this->lang->line('g_groupname')?> : </label></th>
					<td><?php 
							if(isset($geditlist)){ $gname=$geditlist[0]['groupname'];}else{ $gname='';}
							$data = array(
									'name'        => 'groupname',
									'id'          => 'groupname',
									'value'       => $gname,
									'class'       => 'required',
									);
							echo form_input($data);?>
					</td>
					<td></td>
				</tr>
				<?php } ?>
				<?php if(in_array($sys[2],$list_types)){?>
				<tr>
					<th><label><?=isset($system['g']['addnumber'])?$system['g']['addnumber']:$this->lang->line('g_addnumber')?> : </label></th>
					<td><?php 
							if(isset($geditlist)){ $advnumber=$geditlist[0]['addnumber'];}else{ $advnumber='';}
							$data1 = array(
									'name'        => 'addnumber',
									'id'          => 'addnumber',
									'value'       => $advnumber,
									'class'       => 'required',
									);	

							echo form_input($data1);?>
					</td>
					<td></td>
				</tr>
				<?php } ?>
				
				<?php if(in_array($sys[4],$list_types)){?>
				<tr>
					<th><label><?=isset($system['g']['rules'])?$system['g']['rules']:$this->lang->line('g_rules')?> : </label></th>
					<td><?php 
							if(isset($geditlist)){ $rules=$geditlist[0]['rules'];}else{ $rules='';}	
								$js1 = 'id="rules" class="required"';	
							echo form_dropdown('rules', $ruleslist, $rules,$js1);?>
					</td>
					<td></td>
				</tr><?php } ?>
				
				<?php
				
				if(isset($custom['g'])){
				$arr=array_keys($custom['g']);
				for($i=0;$i<sizeof($arr);$i++)
				  {
					$exs=explode("~",$custom['g'][$arr[$i]]);
					if(isset($editcustomlist[$arr[$i]])){ $cusval=$editcustomlist[$arr[$i]];}else{$cusval='';}	
					if(in_array($arr[$i],$list_types)){
				  ?>
				  
				<tr>
					<th><label><?php echo $exs[0];?> : </label></th>
					<td><?=$this->systemmodel->buildField($arr[$i],$cusval)?>

					</td>
					<td></td>
				</tr>		
			<?php
					}
				}					
							}
				?>
				<tr>
					<th><label><?php echo $this->lang->line('level_group_owner');?> : </label></th>
					<td><?php
							//print_r($geditlist);
						if(isset($geditlist)){ $groupemp=$geditlist[0]['eid'];}else{ $groupemp='';}	
						$jss='class="required" id="groupowner"';
						echo form_dropdown('groupowner', $groupowner,$groupemp,$jss);?>	
						<a href="<?php echo base_url();?>group/addgroupowner/" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Add" class="glyphicon glyphicon-plus-sign"></span></a
					</td>
					<td></td>
					
				
				
				</tr>	
			</TABLE>
				<table>	<tr>
					<td colspan="3" align="center">
					<?php if(isset($geditlist)){ echo form_submit('UpdateGroup', 'UpdateGroup');}else{ 
						
						
						
						 echo form_submit('AddGroup', 'AddGroup');}
					
					?>
					</td>

				</tr>		
			</table>



			<?php echo form_close();?>	
	</div>
	
