<script language="javascript" type="text/javascript">
$(document).ready(function(){
	$('#addonhide').hide();
	$('#rate').hide();
	$('#climit').hide();
	$('#rental').hide();
	$('#fmins').hide();
	jQuery.validator.addMethod("numeric", function(value, element) {
		return this.optional(element) || value == value.match(/^[0-9]+$/);
	}, "Allowed only Numeric values"); 
	$("#landingnumberFrm").validate({
		rules:{
			landingnumber:{
				numeric:true
			},
			pri:{
				numeric:true
			}
		},
		errorPlacement: function(error, element) {
			error.appendTo( element.parent().next() );
		}
	});
	
	
	
	$('#package').live('change',function(event){
			var packid=$('#package').val();
				$.ajax({  
						type: "POST",  
						url: "Masteradmin/getPackage_modules/"+packid,  
						data:'package=allmodules', 
						success: function(msg){ 	
							$('#module option').each(function(i, option){ $(option).remove(); });
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
							var str=msg.split('~');
								$('#addul').html('');
								$('#addonhide').show();
								$('#rate').show();
								$('#climit').show();
								$('#rental').show();
								$('#fmins').show();
								$('#addul').html(str[0]);
								$('#prate').val(str[4]);
								$('#pclimit').val(str[3]);
								$('#prental').val(str[1]);
								$('#pfmins').val(str[2]);
							}
						});
		   }else{
			   $('#addul').html('');
			   $('#prate').val('');
				$('#pclimit').val('');
				$('#prental').val('');
				$('#pfmins').val('');
			   $('#addonhide').hide();
			   $('#rate').hide();
				$('#climit').hide();
				$('#rental').hide();
				$('#fmins').hide();
			}
		
	});
	$('#button3').live('click',function(event){
		$("#landingnumberFrm1").validate({
			rules:{
				landingnumber:{
					numeric:true
				},
				pri:{
					numeric:true
				}
			},
			errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			},submitHandler: function(){
				
			$.ajax({  
						type: "POST",  
						url: "<?=$action1?>",  
						data:$("#landingnumberFrm1").serialize()+'&update_system=update_system', 
						success: function(msg){ 	
								if(jQuery.trim(msg)=="Error"){
								$('#errors').html("Landing Number is already in Use");
								return false;
								
							}else{
								$('#landingnumber').children().remove().end().append(msg) ;
								$('#popupDiv').html("");
								$.unblockUI();
							
								}
							}
						});
				
				
			}
			
		});
	
	});
	$('#landingnumber').live('change',function(event){
		$('#pri').val($('option:selected','#landingnumber').attr('rel'))
		$('#region').val($('option:selected','#landingnumber').attr('rel1'))
	});
	$('#businessuser').live('change',function(event){
		var r=$('#businessuser').val();
		$.ajax({  
		type: "POST",  
		url: "<?=base_url()?>Masteradmin/getPoolids/"+r,  
		success: function(msg){
			$('#poolid option').each(function(i, option){ $(option).remove(); });
			$('#poolid').append(msg);
			}
		});
	});
});
   </script>
<div id="box">
<h3>Number Config</h3>
		<?php
			$attributes = array('class' => 'form', 'id' =>'landingnumberFrm','name'=>'landingnumberFrm','enctype'=>'multipart/form-data');		
			 echo form_open($action,$attributes);
		?>
		<fieldset id="priseries">
				<legend><?php echo "Number Config";?></legend>
				<TABLE>
					<tr>
						<th><label>Busniess User :</label></th>
						<td>
							<select name="businessuser" id="businessuser" class="">
								<option value="">----Select----</option>
								<?php
									for($i=0;$i<sizeof($businessUsers);$i++)
									{
										?>
										<option value="<?=$businessUsers[$i]['bid']?>"><?=$businessUsers[$i]['businessname']?></option>
										<?php
									}
								?>
							</select>
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
										<option value="<?=$packages[$i]['package_id']?>"><?=$packages[$i]['packagename']?></option>
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
								
							</select>
							
						
						
						</td>	
						<td></td>	
					
					</tr>
					<tr id="addonhide">
						<th><label>Addons :</label></th>
						<td id="addul">
								
						
						
						</td>	
						<td></td>	
					
					</tr>
					
					<tr id="rental">
						<th><label>Rental :</label></th>
						<td><input type='text' name='prental' id='prental' value=''/></td>	
						<td></td>	
					
					</tr>
					<tr id="fmins">
						<th><label>Free Mins :</label></th>
						<td><input type='text' name='pfmins' id='pfmins' value=''/></td>	
						<td></td>	
					
					</tr>
					<tr id="climit">
						<th><label>Credit Limit :</label></th>
						<td><input type='text' name='pclimit' id='pclimit' value=''/></td>	
						<td></td>	
					
					</tr>
					<tr id="rate">
						<th><label>Rate:</label></th>
						<td><input type='text' name='prate' id='prate' value=''/></td>	
						<td></td>	
					
					</tr>
					<tr>
						<th><label>Service Activation Date :</label></th>
						<td><input type="text" name="svdate" value="" id="svdate" class="required datepicker"></td>	
						<td></td>	
					</tr>
					<tr>
						<th><label>Sim OwnerShip:</label></th>
						<td><input type="text" name="owner" value="" id="owner" class="required"></td>	
						<td></td>	
					</tr>
					<tr>
						<th><label>Payment Term:</label></th>
						<td>
							<select name="pterm" id="pterm" class="required auto">
								<option value="1">1 Month</option>
								<option value="2">2 Month</option>
								<option value="3">3 Month</option>
								<option value="4">4 Month</option>
								<option value="5">5 Month</option>
								<option value="6">6 Month</option>
								<option value="7">7 Month</option>
								<option value="8">8 Month</option>
								<option value="9">9 Month</option>
								<option value="10">10 Month</option>
								<option value="11">11 Month</option>
								<option value="12">12 Month</option>
							</select>
<!--
							<input type="text" name="pterm" value="" id="pterm" class="required">
-->
							
							</td>	
						<td></td>	
					</tr>
					<!--<tr>
						<th><label>Number Pool:</label></th>
						<td><select name="poolid" id="poolid" class="auto"><option value="0"> Select Pool</option></select></td>	
						<td></td>	
					</tr>-->
					<tr>
						<th><label>SMS Limit:</label></th>
						<td><input type="text" name="slimit" value="" id="slimit" class="required"></td>	
						<td></td>	
					</tr>
					<tr>
						<th><label>Parallel Calls Limit:</label></th>
						<td><input type="text" name="plimit" value="" id="plimit" class="required"></td>	
						<td></td>	
					</tr>
					<!--
					<tr>
						<th><label>Missed Call :</label></th>
						<td><input type="checkbox" name="ntype" value="1" id="ntype">
						</td>	
						<td></td>	
					</tr>-->
					<tr>
						<th><label>Number File :</label></th>
						<td><input type="file" name="numbers" id="numbers" />
						</td>	
						<td></td>	
					</tr>
				</TABLE>
		</fieldset>
		<table><tr><td><center>
<input id="button1" type="submit" class="btn btn-primary" name="submit" value="<?=$this->lang->line('submit')?>" /> 
<input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
</center></td></tr></table>
		<?php echo form_close();?>
</div>
