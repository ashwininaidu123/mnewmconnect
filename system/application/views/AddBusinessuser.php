<?php echo script_tag("system/application/js/jquery.validate.js");?>
<script language="javascript" type="text/javascript">
   $(document).ready(function(){
	 jQuery.validator.addMethod("alpha", function(value, element) {
		return this.optional(element) || value == value.match(/^[a-z A-Z]+$/);
		},"Characters only allowed.");
		jQuery.validator.addMethod("alphanumeric", function(value, element) {
		return this.optional(element) || value == value.match(/^[a-z0-9A-Z]+$/);
		},"Only Characters, Numbers Allowed.");
		jQuery.validator.addMethod("mobile", function(value, element) {
		return this.optional(element) || /^[7-9][0-9]{9}$/.test(value);
		}, "Should start with 7 - 9 and 10 digits"); 
		jQuery.validator.addMethod("numeric", function(value, element) {
		return this.optional(element) || value == value.match(/^[0-9]+$/);
		}, "Should start with 7 - 9 and 10 digits"); 
		 $("form").validate({
			rules:{
				businessname:{
					alpha:true
				},
				contactname:{
					alpha:true
				},
				contactemail:{
					email:true
				},
				contactphone:{
					mobile:true
				},
				businessemail:{
					email:true
				},
				businessphone:{
					mobile:true
				},
				locality:{
					alpha:true
				},
				country:{
					alpha:true
				},state:{
					alpha:true
				},city:{
					alpha:true
				},postalcode:{
					numeric:true
				}
				
				
			},messages:{
				businessname:{
					required:"<?php echo $this->lang->line('err_bname');?>",
					alpha:"<?php echo $this->lang->line('err_alpha');?>"
				},
				contactname:{
					required:"<?php echo $this->lang->line('err_cname');?>",
					alpha:"<?php echo $this->lang->line('err_alpha');?>"
					},
					contactemail:{
						required:"<?php echo $this->lang->line('err_cemail');?>"
					},
					contactphone:{
						required:"<?php echo $this->lang->line('err_cphone');?>",
						mobile:"<?php echo $this->lang->line('err_mobile');?>"
					},
					businessemail:{
						required:"<?php echo $this->lang->line('err_bemail');?>"
					},
					businessphone:{
						required:"<?php echo $this->lang->line('err_bphone');?>",
						mobile:"<?php echo $this->lang->line('err_mobile');?>"
					},
					businessaddress:{
						required:"<?php echo $this->lang->line('err_badd');?>"
						
					},
					businessaddress1:{
						required:"<?php echo $this->lang->line('err_badd1');?>"
						
					},locality:{
						required:"<?php echo $this->lang->line('err_locality');?>",
						alpha:"<?php echo $this->lang->line('err_alpha');?>"
					},
					country:{
						required:"<?php echo $this->lang->line('err_country');?>",
						alpha:"<?php echo $this->lang->line('err_alpha');?>"
					},state:{
						required:"<?php echo $this->lang->line('err_state');?>",
						alpha:"<?php echo $this->lang->line('err_alpha');?>"
					},city:{
						required:"<?php echo $this->lang->line('err_city');?>",
						alpha:"<?php echo $this->lang->line('err_alpha');?>"
					},
					postalcode:{
						required:"<?php echo $this->lang->line('err_Zipcode');?>",
						numeric:"<?php echo $this->lang->line('err_Ziperr');?>"
					},
					
			},
			errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			}	
				});
	});	
	</script>		
		
<div id="box">
		<h3><?php if(isset($profile_details)){ echo $this->lang->line('level_Edit_Group'); $action="Business/profile_edit/".$profile_details[0]['bid'];}else{ echo $this->lang->line('level_addbusiness_details'); $action='Business/profile_edit';}?></h3>
			<?php
			$attributes = array('class' => 'email', 'id' =>'form','name'=>'form');		
			 echo form_open($action,$attributes);
			 echo "<pre>";
				
				$sys=array_keys($system['p']);
				
			echo "</pre>";	
			?>
			<fieldset id="priseries">
				<legend><?php echo $this->lang->line('level_business_details');?></legend>
					<TABLE>
							<?php if(in_array($sys[10],$list_types)){?>
						<tr>
							<th>
								<label><?=$this->lang->line('p_businessname')?></label>
							</th>
							<td>
								<input type="text" id="businessname" name="businessname" value="<?=isset($profile_details[0]['businessname'])?$profile_details[0]['businessname']:''?>" class="required">
							</td>
							<td></td>
						</tr>
						<?php  } ?>
					
							<?php if(in_array($sys[6],$list_types)){?>
						<tr>
							<th>
								<label><?=$this->lang->line('p_contactname')?></label>
							</th>
							<td>
								<input type="text" id="contactname" name="contactname" value="<?=isset($profile_details[0]['contactname'])?$profile_details[0]['contactname']:''?>" class="required">
							</td>
							<td></td>
						</tr>
						<?php  } ?>
						<?php if(in_array($sys[7],$list_types)){?>
						<tr>
							<th>
								<label><?=$this->lang->line('p_contactemail')?></label>
							</th>
							<td>
								<input type="text" name="contactemail" id="contactemail" value="<?=isset($profile_details[0]['contactemail'])?$profile_details[0]['contactemail']:''?>" class="required">
							</td>
							<td></td>
						</tr>
						<?php  } ?>
						<?php if(in_array($sys[5],$list_types)){?>
						<tr>
							<th>
								<label><?=$this->lang->line('p_contactphone')?></label>
							</th>
							 <td>
								<input type="text" name="contactphone" id="contactphone" value="<?=isset($profile_details[0]['contactphone'])?$profile_details[0]['contactphone']:''?>" class="required">
							</td>
							<td></td>
						</tr>
						<?php } ?>
						 <?php if(in_array($sys[11],$list_types)){?>         
						<tr>
							<th>
								<label><?=$this->lang->line('p_businessemail')?></label>
							</th>
							<td>
								<input type="text" name="businessemail" id="businessemail" value="<?=isset($profile_details[0]['businessemail'])?$profile_details[0]['businessemail']:''?>" class="required">
							</td>
							<td></td>	
						</tr>
						<?php } ?>
						 <?php if(in_array($sys[9],$list_types)){?>    
						<tr>
							<th>
								<label><?=$this->lang->line('p_businessphone')?></label>
							</th>
							<td>
								<input type="text" name="businessphone" id="businessphone" value="<?=isset($profile_details[0]['businessphone'])?$profile_details[0]['businessphone']:''?>" class="required">
							</td>
							<td></td>	
						</tr>
						<?php } ?>
						<?php if(in_array($sys[13],$list_types)){?>    
						<tr>
							<th>
								<label><?=$this->lang->line('p_businessaddress')?></label>
							</th>
							<td>
								<textarea name="businessaddress" id="businessaddress" class="required"><?=isset($profile_details[0]['businessaddress'])?$profile_details[0]['businessaddress']:''?></textarea>
							</td>
							<td></td>	
						</tr>
						<?php } ?>
						<?php if(in_array($sys[12],$list_types)){?>    
						<tr>
							<th>
								<label><?=$this->lang->line('p_businessaddress1')?></label>
							</th>
							<td>
								<textarea name="businessaddress1" class="required"><?=isset($profile_details[0]['businessaddress1'])?$profile_details[0]['businessaddress1']:''?></textarea>
							</td>
							<td></td>	
						</tr>
						<?php } ?>
						<?php if(in_array($sys[2],$list_types)){?>    
						<tr>
							<th>
								<label><?=$this->lang->line('p_locality')?></label>
							</th>
							<td>
								<input type="text" name="locality" id="locality" class="required" value="<?=isset($profile_details[0]['locality'])?$profile_details[0]['locality']:''?>">
								
							</td>
							<td></td>	
						</tr>
						<?php } ?>
						<?php if(in_array($sys[4],$list_types)){?>    
						<tr>
							<th>
								<label><?=$this->lang->line('p_country')?></label>
							</th>
							<td>
								<input type="text" name="country" id="country" class="required" value="<?=isset($profile_details[0]['country'])?$profile_details[0]['country']:''?>">
							</td>
							<td></td>	
						</tr>
						<?php } ?>
						<?php if(in_array($sys[0],$list_types)){?>    
						<tr>
							<th>
								<label><?=$this->lang->line('p_state')?></label>
							</th>
							<td>
								<input type="text" name="state" id="state" class="required" value="<?=isset($profile_details[0]['state'])?$profile_details[0]['state']:''?>">
								
							</td>
							<td></td>	
						</tr>
						<?php } ?>
						<?php if(in_array($sys[8],$list_types)){?>    
						<tr>
							<th>
								<label><?=$this->lang->line('p_city')?></label>
							</th>
							<td>
								<input type="text" name="city" id="city" class="required" value="<?=isset($profile_details[0]['city'])?$profile_details[0]['city']:''?>">
								
							</td>
							<td></td>	
						</tr>
						<?php } ?>
						<?php if(in_array($sys[3],$list_types)){?>    
						<tr>
							<th>
								<label><?=$this->lang->line('p_postalcode')?></label>
							</th>
							<td>
								<input type="text" name="postalcode" id="postalcode" class="required" value="<?=isset($profile_details[0]['zipcode'])?$profile_details[0]['zipcode']:''?>">
								
							</td>
							<td></td>	
						</tr>
						<?php } ?>
						<?php if(in_array($sys[1],$list_types)){?>  
						<tr>
							<th>
								<label><?=$this->lang->line('p_language')?></label>
							</th>
							<td>
								
								<?php
									$js = 'id="p[language]" class="required"';
									if(isset($profile_details[0]['language'])){
										$lan=$profile_details[0]['language'];
									}else{
										$lan='';
									}
									echo form_dropdown("language",$languages,$lan,$js);
								?>
							</td>
							<td></td>
						</tr>
						<?php } ?>
						
						<?php
						//print_r($custom);
						if(isset($custom['p'])){
						
						$arr=array_keys($custom['p']);
						
						for($i=0;$i<sizeof($arr);$i++)
						{
								
							
							$exs=explode("~",$custom['p'][$arr[$i]]);
							//print_r($exs);
							if(isset($exs)){ $cusval=$exs[1];}else{$cusval='';}
							$cusval = '';
							if(in_array($arr[$i],$list_types)){
						?>
						<tr>
							<th><label><?php echo $exs[0];?></label></th>
							<td>
								:  <?=$this->systemmodel->buildField($arr[$i],$cusval)?><!--<input type="text" value="<?php echo $cusval;?>" name="c[<?php echo $arr[$i]?>]" id="c[custom<?php echo $arr[$i]?>]" <?php if($exs[1]) { ?> class="required"<?php } ?>/>-->
							</td>
							<td></td>
						</tr>		
						<?php
							}
						}					
							}
				?>
						
					</TABLE>
				



			</fieldset>
				<table>	<tr>
					<td colspan="3" align="center">
					<?php if(isset($profile_details)){ echo form_submit('UpdateProfile', 'UpdateProfile');}else{  echo form_submit('Profile', 'Profile');}
					
					?>
					</td>

				</tr>		
			</table>
				
			<?php echo form_close();?>	
	</div>
	
