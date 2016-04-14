<?php echo script_tag("system/application/js/jquery.validate.js");?>

<link rel="stylesheet" href="<?php echo site_url('system/application/css/general.css');?>" type="text/css" media="screen" />
<script language="javascript" type="text/javascript">
   $(document).ready(function(){
	 jQuery.validator.addMethod("alpha", function(value, element) {
		return this.optional(element) || value == value.match(/^[a-z A-Z]+$/);
		},"Characters only allowed.");
		jQuery.validator.addMethod("alphanumeric", function(value, element) {
		return this.optional(element) || value == value.match(/^[a-z0-9A-Z]+$/);
		},"Only Characters, Numbers Allowed.");
         $(".session_message").show();
         $(".session_message").fadeOut(5000);
	 $("form").validate({
		
	errorPlacement: function(error, element) {
		error.appendTo( element.parent().next() );
	}	
		});
		
		$('#submit').live('click',function(event) {
			$("#customfieldform").validate({
				
				rules:{
					label_name:{
						alpha:true
					},
					labeldefaultval:{
						alphanumeric:true
					}
					
				},messages:{
					label_name:{
						required:"<?php echo $this->lang->line('error_labelname');?>",
						alpha:"<?php echo $this->lang->line('err_alpha');?>"
					},
					label_list:{
						required:"<?php echo $this->lang->line('err_labellist');?>"
					},
					labeldefaultval:{
						required:"<?php echo $this->lang->line('err_labeldefault');?>",
						alphanumeric:"<?php echo $this->lang->line('err_aphanumeric');?>",
						
					}
				},
				errorPlacement: function(error, element) {
					error.appendTo( element.parent().next() );
					},
					submitHandler: function(){
						if($('#label_list').val()!="text"){
							var str=$('#labeloptions').val();
							var strs=str.split("\n");
							var x=jQuery.inArray($('#labeldefaultval').val(),strs);
							if(x!=-1){
								$.ajax({ 
									type: "POST",  
									url: "<?php echo Base_url();?>customfield/Addcustom/",  
									data: $('#customfieldform').serialize(), 
									success: function(msg){ 
									window.location.reload();
										}
									});
							}
							else{
								$('#err1').html("<?php echo $this->lang->line('err_notinlist'); ?>");
								return false;
							}
							
						}
						
						
					}	
				
			});
		});	
		
		$('#Update_CustomField').live('click',function(event) {
			$("#customfieldforms").validate({
				rules:{
					label_name:{
						alpha:true
					}
				},messages:{
					label_name:{
						required:"<?php echo $this->lang->line('error_labelname');?>",
						alpha:"<?php echo $this->lang->line('err_alpha');?>"
					}
				},
					errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			}
			});
			
		});
		
	});	


		
  
</script>
<div id="content">
	<div class="session_message"><?php echo $this->session->flashdata('item');?></div>
 	<div id="box">
		<h3><?php echo $this->lang->line('level_System_label');?></h3>
		<?php
		$attributes = array('class' => 'email', 'id' => 'form','name'=>'form');		
		 echo form_open('customfield/Managecustomfield',$attributes);
		// print_r($get_labels['p']);
		 $arrs=array_keys($get_labels['p']);
		 $arrs1=array_keys($get_labels['g']);
		 $arrs2=array_keys($get_labels['e']);
		
		?>	
		<fieldset id="priseries">
			<legend><?php echo $this->lang->line('level_custom_profile');?></legend>
			<table>
				<tr>
					<td colspan="3" align="right">
					
					
					<a href="<?php echo base_url();?>customfield/addcustomfields/p" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><?php echo $this->lang->line('level_addcustom_field');?></a>
					</td>
				<?php //echo $get_labels['p']['contactname'];?>
				</tr>
				<tr>
					<th><label><?=$this->lang->line('p_contactname')?></label></th>
					<td><input type="text" id="p[contactname]" name="p[contactname]" value="<?=isset($get_labels['p']['contactname'])?$get_labels['p']['contactname']:$this->lang->line('p_contactname')?>" class="required alpha">
					</td>
					<td></td>
					<td><input type='checkbox' name="p[ischecked][contactname]" id="systemlabel" <?php if(isset($check_val)){ if(isset($check_val['contactname'])){ if(in_array($check_val['contactname'],$arrs)){ echo "checked";} }} ?>/></td>
				</tr>
				<tr>
					<th><label><?=$this->lang->line('p_contactemail')?></label></th>
					<td><input type="text" name="p[contactemail]" value="<?=isset($get_labels['p']['contactemail'])?$get_labels['p']['contactemail']:$this->lang->line('p_contactemail')?>" class="required alpha">
					</td>
					<td></td>
					<td><input type='checkbox' name="p[ischecked][contactemail]" id="systemlabel" <?php if(isset($check_val)){ if(isset($check_val['contactemail'])){ if(in_array($check_val['contactemail'],$arrs)){ echo "checked";} }} ?> /></td>
				</tr>
				<tr>
					<th>
					<label>
					<?=$this->lang->line('p_contactphone')?></label></th><td>
					<input type="text" name="p[contactphone]" value="<?=isset($get_labels['p']['contactphone'])?$get_labels['p']['contactphone']:$this->lang->line('p_contactphone')?>" class="required alpha">
					</td>
					<td></td>
					<td><input type='checkbox' name="p[ischecked][contactphone]" id="systemlabel"  <?php if(isset($check_val)){ if(isset($check_val['contactphone'])) if(in_array($check_val['contactphone'],$arrs)){ echo "checked";}  } ?>/></td>
				</tr>
				<tr>
					<th>
					<label>
					<?=$this->lang->line('p_businessname')?></label></th><td>
					<input type="text" name="p[businessname]" value="<?=isset($get_labels['p']['businessname'])?$get_labels['p']['businessname']:$this->lang->line('p_businessname')?>" class="required alpha">
					</td>
					<td></td>
					<td><input type='checkbox' name="p[ischecked][businessname]" id="systemlabel"  <?php if(isset($check_val)){ if(isset($check_val['businessname'])) if(in_array($check_val['businessname'],$arrs)){ echo "checked";} } ?> /></td>
				</tr>
				<tr>
					<th>
					<label>
					<?=$this->lang->line('p_businessemail')?></label></th><td>
					<input type="text" name="p[businessemail]" value="<?=isset($get_labels['p']['businessemail'])?$get_labels['p']['businessemail']:$this->lang->line('p_businessemail')?>" class="required alpha">
					</td>
					<td></td>	
					<td><input type='checkbox' name="p[ischecked][businessemail]" id="systemlabel" <?php if(isset($check_val)){ if(isset($check_val['businessemail'])) if(in_array($check_val['businessemail'],$arrs)){ echo "checked";} } ?> /></td>
				</tr>
				<tr>
					<th>
					<label>
					
					<?=$this->lang->line('p_businessphone')?></label></th><td>
					<input type="text" name="p[businessphone]" value="<?=isset($get_labels['p']['businessphone'])?$get_labels['p']['businessphone']:$this->lang->line('p_businessphone')?>" class="required alpha">
					</td>
					<td></td>	
					<td><input type='checkbox' name="p[ischecked][businessphone]" id="systemlabel" <?php if(isset($check_val)){ if(isset($check_val['businessphone'])) if(in_array($check_val['businessphone'],$arrs)){ echo "checked";} } ?> /></td>
				</tr>
				<tr>
					<th>
					<label>
					<?=$this->lang->line('p_businessaddress1')?></label></th><td>
					<input type="text" name="p[businessaddress1]" value="<?=isset($get_labels['p']['businessaddress1'])?$get_labels['p']['businessaddress1']:$this->lang->line('p_businessaddress1')?>" class="required">
					</td>
					<td></td>	
					<td><input type='checkbox' name="p[ischecked][businessaddress1]" id="systemlabel" <?php if(isset($check_val)){ if(isset($check_val['businessaddress1'])) if(in_array($check_val['businessaddress1'],$arrs)){ echo "checked";} } ?> /></td>
				</tr>
				<tr>
					<th>
					<label>
					<?=$this->lang->line('p_state')?></label></th><td>
					<input type="text" name="p[state]" value="<?=isset($get_labels['p']['state'])?$get_labels['p']['state']:$this->lang->line('p_state')?>" class="required">
					</td>
					<td></td>	
					<td><input type='checkbox' name="p[ischecked][state]" id="systemlabel" <?php if(isset($check_val)){ if(isset($check_val['state'])) if(in_array($check_val['state'],$arrs)){ echo "checked";} } ?> /></td>
				</tr>
				<tr>
					<th>
					<label>
					<?=$this->lang->line('p_city')?></label></th><td>
					<input type="text" name="p[city]" value="<?=isset($get_labels['p']['city'])?$get_labels['p']['city']:$this->lang->line('p_city')?>" class="required">
					</td>
					<td></td>	
					<td><input type='checkbox' name="p[ischecked][city]" id="systemlabel" <?php if(isset($check_val)){ if(isset($check_val['city'])) if(in_array($check_val['city'],$arrs)){ echo "checked";} } ?> /></td>
				</tr>
				<tr>
					<th>
					<label>
					<?=$this->lang->line('p_locality')?></label></th><td>
					<input type="text" name="p[locality]" value="<?=isset($get_labels['p']['locality'])?$get_labels['p']['locality']:$this->lang->line('p_locality')?>" class="required">
					</td>
					<td></td>	
					<td><input type='checkbox' name="p[ischecked][locality]" id="systemlabel" <?php if(isset($check_val)){ if(isset($check_val['locality'])) if(in_array($check_val['locality'],$arrs)){ echo "checked";} } ?> /></td>
				</tr>
				<tr>
					<th>
					<label>
					<?=$this->lang->line('p_postalcode')?></label></th><td>
					<input type="text" name="p[postalcode]" value="<?=isset($get_labels['p']['postalcode'])?$get_labels['p']['postalcode']:$this->lang->line('p_postalcode')?>" class="required">
					</td>
					<td></td>	
					<td><input type='checkbox' name="p[ischecked][postalcode]" id="systemlabel" <?php if(isset($check_val)){ if(isset($check_val['postalcode'])) if(in_array($check_val['postalcode'],$arrs)){ echo "checked";} } ?> /></td>
				</tr>
				<tr>
					<th>
					<label>
					<?=$this->lang->line('p_country')?></label></th><td>
					<input type="text" name="p[country]" value="<?=isset($get_labels['p']['country'])?$get_labels['p']['country']:$this->lang->line('p_country')?>" class="required">
					</td>
					<td></td>	
					<td><input type='checkbox' name="p[ischecked][country]" id="systemlabel" <?php if(isset($check_val)){ if(isset($check_val['country'])) if(in_array($check_val['country'],$arrs)){ echo "checked";} } ?> /></td>
				</tr>
				<tr>
					<th>
					<label>
					<?=$this->lang->line('p_businessaddress')?></label></th><td>
					<input type="text" name="p[businessaddress]" value="<?=isset($get_labels['p']['businessaddress'])?$get_labels['p']['businessaddress']:$this->lang->line('p_businessaddress')?>" class="required alpha">
					</td>
					<td></td>	
					<td><input type='checkbox' name="p[ischecked][businessaddress]" id="systemlabel" <?php if(isset($check_val)){ if(isset($check_val['businessaddress'])) if(in_array($check_val['businessaddress'],$arrs)){ echo "checked";} } ?> /></td>
				</tr>
				<tr>
					<th>
					<label>
					<?=$this->lang->line('p_language')?></label></th><td>
					<input type="text" name="p[language]" value="<?=isset($get_labels['p']['language'])?$get_labels['p']['language']:$this->lang->line('p_language')?>" class="required alpha">
					</td>
					<td></td>
					<td><input type='checkbox' name="p[ischecked][language]" id="systemlabel" <?php if(isset($check_val)){ if(isset($check_val['language'])) if(in_array($check_val['language'],$arrs)){ echo "checked";} } ?> /></td>
				</tr>
				<?php
				//print_r($custom['p']);
				//print_r($check_val);
				if(isset($custom))
				{
					if(isset($custom['p'])){	
					$arr_keys=array_keys($custom['p']);
					
					for($i=0;$i<sizeof($arr_keys);$i++)
					{
							$x="custom".$arr_keys[$i];
						?>
						<tr>
							<th><label><?php echo $this->lang->line('level_customfield').$i;?></label></th>
							<td><input type="text" name="p[c][<?php echo $arr_keys[$i];?>]" value="<?php echo $custom['p'][$arr_keys[$i]];?>" class="required alpha"><a href="<?php echo site_url('customfield/delete_custom/'.$arr_keys[$i]);?>">x</a> <a href="customfield/edit_custom/<?php echo $arr_keys[$i]?>" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Edit" class="fa fa-edit"></span></a></td>
							<td></td>
							<td><input type="checkbox" name="p[ischecked][<?php echo $x;?>]" <?php if(isset($check_val)){ if(in_array($arr_keys[$i],$check_val)){ echo "checked";} } ?>/></td>
														
						
						</tr>
						
						
						<?php
									
					}
				}	
					
				}
				
				
				
				?>


			</table>

		</fieldset>
		<fieldset id="priseries">
			<legend><?php echo $this->lang->line('level_custom_group');?></legend>
			<table>
				<tr>
					<td colspan="3" align="right">
					
					<a href="<?php echo base_url();?>customfield/addcustomfields/g" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><?php echo $this->lang->line('level_addcustom_field');?></a>
					</td>
				
				</tr>
				<!--<tr>
					<th>
					<label>
					<?=$this->lang->line('g_busninessname')?></label></th><td>
					<input type="text" name="g[g_busninessname]" value="<?=isset($get_labels['g']['busninessname'])?$get_labels['g']['busninessname']:$this->lang->line('g_busninessname')?>" class="required alpha">
					</td>
					<td></td>
					<td><input type='checkbox' name="systemlabel" id="systemlabel" <?php if(isset($check_val)){ if(isset($check_val['busninessname'])) if(in_array($check_val['businessaddress'],$arrs)){ echo "checked";} } ?> /></td>
				</tr>-->
				
				<tr>
					<th>
					<label>
					<?=$this->lang->line('g_groupname')?></label></th><td>
					<input type="text" name="g[groupname]" value="<?=isset($get_labels['g']['groupname'])?$get_labels['g']['groupname']:$this->lang->line('g_groupname')?>" class="required alpha">
					</td>
					<td></td>
					<td><input type='checkbox' name="g[ischecked][groupname]" id="systemlabel" <?php if(isset($check_val)){ if(isset($check_val['groupname'])) if(in_array($check_val['groupname'],$arrs1)){ echo "checked";} } ?> /></td>
				</tr>
				<tr>
					<th>
					<label>
					<?=$this->lang->line('g_addnumber')?></label></th><td>
					<input type="text" name="g[addnumber]" value="<?=isset($get_labels['g']['addnumber'])?$get_labels['g']['addnumber']:$this->lang->line('g_addnumber')?>" class="required alpha">
					</td>
					<td></td>
					<td><input type='checkbox' name="g[ischecked][addnumber]" id="systemlabel"  <?php if(isset($check_val)){ if(isset($check_val['addnumber'])) if(in_array($check_val['addnumber'],$arrs1)){ echo "checked";} } ?>/></td>
				</tr>	
				<tr>
					<th>
					<label>
					<?=$this->lang->line('g_prinumber')?></label></th><td>
					<input type="text" name="g[prinumber]" value="<?=isset($get_labels['g']['prinumber'])?$get_labels['g']['prinumber']:$this->lang->line('g_prinumber')?>" class="required alpha">
					</td>
					<td></td>
					<td><input type='checkbox' name="g[ischecked][prinumber]" id="systemlabel"  <?php if(isset($check_val)){  if(isset($check_val['prinumber'])) if(in_array($check_val['prinumber'],$arrs1)){ echo "checked";} } ?>/></td>
				</tr>	
				<tr>
					<th>
					<label>
					<?=$this->lang->line('g_rules')?></label></th><td>
					<input type="text" name="g[rule]" value="<?=isset($get_labels['g']['rules'])?$get_labels['g']['rules']:$this->lang->line('g_rules')?>" class="required alpha">
					</td>
					<td></td>
					<td><input type='checkbox' name="g[ischecked][rule]" id="systemlabel" <?php if(isset($check_val)){ if(isset($check_val['rule'])) if(in_array($check_val['rule'],$arrs1)){ echo "checked";} } ?> /></td>
				</tr>
				<?php
				if(isset($custom))
				{
					if(isset($custom['g'])){	
					$arr_keys=array_keys($custom['g']);
					for($i=0;$i<sizeof($arr_keys);$i++)
					{
						
						?>
						<tr>
							<th><label><?php echo $this->lang->line('level_customfield').$i;?></label></th>
							<td><input type="text" name="g[c][<?php echo $arr_keys[$i];?>]" value="<?php echo $custom['g'][$arr_keys[$i]];?>" class="required alpha"><a href="<?php echo site_url('customfield/delete_custom/'.$arr_keys[$i]);?>">x</a> <a href="edit_custom/<?php echo $arr_keys[$i]?>" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Edit" class="fa fa-edit"></span></a></td>
							<td></td>
							<td><input type="checkbox" name="g[ischecked][<?php echo $arr_keys[$i]?>]" <?php if(isset($check_val)){ if(in_array($arr_keys[$i],$check_val)){ echo "checked";} } ?> /></td>
						
						</tr>
						
						
						<?php
									
					}
				}	
					
				}
				
				
				
				?>
				
				
			</table>

		</fieldset>
		<fieldset id="priseries">
			<legend><?php echo $this->lang->line('level_custom_Employee');?></legend>
			<table>	
				<tr>
					<td colspan="3" align="right">
					
					
					<a href="<?php echo base_url();?>customfield/addcustomfields/e" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><?php echo $this->lang->line('level_addcustom_field');?></a>
					</td>
				
				</tr>
				<!--<tr>
					<th>
					<label>
					<?=$this->lang->line('e_busninessname')?></label></th><td>
					<input type="text" name="e[e_busninessname]" value="<?=isset($get_labels['e']['e_busninessname'])?$get_labels['e']['e_busninessname']:$this->lang->line('e_busninessname')?>" class="required alpha">
					</td>
					<td></td>
					<td><input type='checkbox' name="systemlabel" id="systemlabel"  <?php if(isset($check_val)){ if(in_array($check_val['e_busninessname'],$arrs2)){ echo "checked";} } ?>/></td>
				</tr>-->
				
				<tr>
					<th>
					<label>
					<?=$this->lang->line('e_employeename')?></label></th><td>
					<input type="text" name="e[empname]" value="<?=isset($get_labels['e']['empname'])?$get_labels['e']['empname']:$this->lang->line('e_employeename')?>" class="required alpha">
					</td>
					<td></td>
					<td><input type='checkbox' name="e[ischecked][empname]" id="systemlabel" <?php if(isset($check_val)){ if(isset($check_val['empname'])) if(in_array($check_val['empname'],$arrs2)){ echo "checked";} } ?> /></td>
				</tr>
				<tr>
					<th>
					<label>
					<?=$this->lang->line('e_employeeid')?></label></th><td>
					<input type="text" name="e[empid]" value="<?=isset($get_labels['e']['employeeid'])?$get_labels['e']['employeeid']:$this->lang->line('e_employeeid')?>" class="required alpha">
					</td>
					<td></td>
					<td><input type='checkbox' name="e[ischecked][empid]" id="systemlabel" <?php if(isset($check_val)){ if(isset($check_val['empid'])) if(in_array($check_val['empid'],$arrs2)){ echo "checked";} } ?> /></td>
				</tr>	
				<tr>
					<th>
					<label>
					<?=$this->lang->line('e_email')?></label></th><td>
					<input type="text" name="e[empemail]" value="<?=isset($get_labels['e']['empemail'])?$get_labels['e']['empemail']:$this->lang->line('e_email')?>" class="required alpha">
					</td>
					<td></td>
					<td><input type='checkbox' name="e[ischecked][empemail]" id="systemlabel" <?php if(isset($check_val)){ if(isset($check_val['empemail'])) if(in_array($check_val['empemail'],$arrs2)){ echo "checked";} } ?> /></td>
				</tr>	
				<tr>
					<th>
					<label>
					<?=$this->lang->line('e_mobile')?></label></th><td>
					<input type="text" name="e[empnumber]" value="<?=isset($get_labels['e']['empnumber'])?$get_labels['e']['empnumber']:$this->lang->line('e_mobile')?>" class="required alpha">
					</td>
					<td></td>
					<td><input type='checkbox' name="e[ischecked][empnumber]" id="systemlabel" <?php if(isset($check_val)){ if(isset($check_val['empnumber'])) if(in_array($check_val['empnumber'],$arrs2)){ echo "checked";} } ?> /></td>
				</tr>
				<!--<tr>
					<th>
					<label>
					<?=$this->lang->line('e_loginaccess')?></label></th><td>
					<input type="text" name="e[login]" value="<?=isset($get_labels['e']['loginaccess'])?$get_labels['e']['loginaccess']:$this->lang->line('e_loginaccess')?>" class="required alpha">
					</td>
					<td></td>	
				<td><input type='checkbox' name="systemlabel" id="systemlabel" <?php if(isset($check_val)){ if(isset($check_val['loginaccess'])) if(in_array($check_val['businessaddress'],$arrs)){ echo "checked";} } ?> /></td>
				</tr>-->
				<?php
				if(isset($custom))
				{
					if(isset($custom['e'])){
					$arr_keys=array_keys($custom['e']);
					for($i=0;$i<sizeof($arr_keys);$i++)
					{
						
						?>
						<tr>
							<th><label><?php echo $this->lang->line('level_customfield').$i;?></label></th>
							<td><input type="text" name="e[c][<?php echo $arr_keys[$i];?>]" value="<?php echo $custom['e'][$arr_keys[$i]];?>" class="required alpha"><a href="<?php echo site_url('customfield/delete_custom/'.$arr_keys[$i]);?>">x</a> <a href="edit_custom/<?php echo $arr_keys[$i]?>" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Edit" class="fa fa-edit"></span></a></td>
							<td></td>
							<td><input type="checkbox" name="e[ischecked][<?php echo $arr_keys[$i]?>]" <?php if(isset($check_val)){ if(in_array($arr_keys[$i],$check_val)){ echo "checked";} } ?>/></td>
						
						</tr>
						
						
						<?php
						
						
					}
				}
					
				}
				
				
				
				?>
				
			</table>

		</fieldset>




			<table>	<tr>
					<td>
					<?php echo form_submit('Updatefield', 'Updatefield');?>

					</td>

				</tr>		
			</table>
		<?php echo form_close();?>

	</div>
</div>
<div id="popupContact" style="display:none;">
		<h2>Custom Fields<a id="popupContactClose">x</a></h2>
		<div id="pages" style="width:100%;overflow:scroll;height:375px;"></div>
	</div>

	<div id="backgroundPopup"></div>
