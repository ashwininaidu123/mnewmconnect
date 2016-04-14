<script language="javascript" type="text/javascript">
   $(document).ready(function(){
	   $('#addonhide').hide();
	   jQuery.validator.addMethod("numeric", function(value, element) {
		return this.optional(element) || value == value.match(/^[0-9]+$/);
		}, "Allowed only Numeric values"); 
		
			$("#landingnumbers").validate({
			  rules:{
					 landingnumber:{
					  numeric:true
					}, pri:{
					  numeric:true
					}
				},
				messages:{
				  },		  
				errorPlacement: function(error, element) {
					error.appendTo( element.parent().next() );
				}
		});
	  $('#package').live('change',function(event){
		  
			var packid=$('#package').val();
			var savied="<?php echo $selectedlist->package_id;?>";
			if(packid!=savied){
				$('#editpoint').hide();
			}
				$.ajax({  
						type: "POST",  
						url: "Masteradmin/getPackage_modules/"+packid,  
						data:'package=allmodules', 
						success: function(msg){ 	
							$('#module').html('');
							$('#module').append(msg);
							
							}
						});
				
		
		
	});
	$('#module').live('change',function(event){
		 
			var mod=$('#module').val();
			var packid=$('#package').val();
			if(mod!=""){
			
					$.ajax({  
								type: "POST",  
								url: "Masteradmin/getPackage_modules/"+packid+"/"+mod,  
								data:'package=allmodules', 
								success: function(msg){ 
										$('#editpoint').hide();
										$('#addul').html('');
										$('#addonhide').show();
										$('#addul').html(msg);
									}
								});
			}else{
					$('#addul').html('');
					$('#addonhide').hide();
			}
		
	});
		 
	  
   });
   </script>
<div id="box">
		<?php
			$attributes = array('class' => 'form', 'id' =>'landingnumbers','name'=>'landingnumbers');		
			 echo form_open($action.'/'.$selectedlist->number,$attributes);
		?>
		<fieldset id="priseries">
				<legend><?php echo "Number Config";?></legend>
				<TABLE>
					<tr>
						<th><label>Busniess User :</label></th>
						<td>
							<select name="businessuser" id="businessuser" class="required">
								<option value="">----Select----</option>
								<?php for($i=0;$i<sizeof($businessUsers);$i++){ ?>
									<option value="<?=$businessUsers[$i]['bid']?>" <?=(($businessUsers[$i]['bid']==$selectedlist->bid)?"selected":'')?>><?=$businessUsers[$i]['businessname']?></option>
								<?php } ?>
							</select>
							<input type="hidden" name="actualuser" id="actualuser" value="<?php echo $selectedlist->bid;?>"/>
						</td>	
						<td></td>	
					</tr>
				
						<tr>
						<th><label>Packages :</label></th>
						<td>
							<select name="package" id="package" class="required">
								<option value="">----Select----</option>
								<?php
									for($i=0;$i<sizeof($packages);$i++)
									{
										?>
										<option value="<?=$packages[$i]['package_id']?>" <?=(($packages[$i]['package_id']==$selectedlist->package_id)?"selected":'')?>><?=$packages[$i]['packagename']?></option>
										<?php
									}
								?>
							</select>
						</td>	
						<td></td>	
					</tr>
					<tr>
						<th><label>Module :</label></th>
						<td>
							<select name="module" id="module" class="required">
									<option value="">--select---</option>
								
								<?php
									for($i=0;$i<sizeof($modules);$i++)
									{
										?>
										<option value="<?=$modules[$i]['module_id']?>" <?=(($modules[$i]['module_id']==$landing_details)?'selected':'')?>><?=$modules[$i]['module_name']?></option>
										
										<?php
									}
								?>
							</select>
						</td>	
						<td></td>	
					</tr>
					<tr id="editpoint">
						<th><label>Addons :</label></th>
						<td id="editul">
								<ul style="list-style:none;" id="fids">
									<?php
										foreach($baddons as $bads){
											
											?>
											<li style="width:300;padding-right:40px;"><input type="checkbox" name="savfeatureids[]" id="savfeatureids[]" value="<?=$bads['feature_id']?>" checked/><?=$this->mastermodel->feature_name($bads['feature_id'])?></label></li>
										
										<?	 
										}
									
									
									?>
								</ul>
							
						</td>	
						<td></td>	
					</tr>
					<tr id="addonhide">
						<th><label>Addons :</label></th>
						<td id="addul">
								
						
						
						</td>	
						<td></td>	
					
					</tr>
					<tr>
						<th><label>Landing Number :</label></th>
						<td>
							<input type="text" name="landingnumber" id="landingnumber" value="<?=$selectedlist->landingnumber?>" class="required" /></td>	
							</td>	
						<td><div class="errors"></div></td>	
					</tr>
					<tr>
						<th><label>PriNumber :</label></th>
						<td><input type="text" name="pri" id="pri" value="<?=$selectedlist->pri?>" class="required" /></td>	
						<td></td>	
					</tr>
					<?php if($show==0){ ?>
						<tr>
						<th><label>Note :</label></th>
						<td><textarea name="cnote" id="cnote" ></textarea></td>	
						<td></td>	
						</tr>
					<?php } ?>
						
						
					
				
				</TABLE>
				
				
				
		</fieldset>
		<table><tr><td><center>
<input id="button1" type="submit" name="submit" value="<?=$this->lang->line('submit')?>" /> 
<input id="button2" type="reset" value="<?=$this->lang->line('reset')?>" />
</center></td></tr></table>
		<?php echo form_close();?>
</div>

