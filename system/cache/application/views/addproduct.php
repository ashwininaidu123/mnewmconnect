<script language="javascript" type="text/javascript">
   $(document).ready(function(){
		
		jQuery.validator.addMethod("alphanumeric", function(value, element) {
		return this.optional(element) || value == value.match(/^[a-z0-9 A-Z]+$/);
		},"Only Characters, Numbers Allowed.");
		jQuery.validator.addMethod("mobile", function(value, element) {
		return this.optional(element) || /^[7-9][0-9]{9}$/.test(value);
		}, "Should start with 7 - 9 and 10 digits");  
		jQuery.validator.addMethod("numeric", function(value, element) {
		return this.optional(element) ||value == value.match(/^[0-9]+$/);
		}, "Numeric only allowed"); 
	 $("form").validate({
		
			
					errorPlacement: function(error, element) {
						error.appendTo( element.parent().next() );
					},
					
				
			});
						
					//return false;
			
			
			});	
</script>
<div id="box">
		<h3><?php 
				
			if(!empty($product)){ echo $this->lang->line('level_Edit_product'); $action="Masteradmin/add_product/".$product->product_id;}else{ echo $this->lang->line('level_Add_Product'); $action='Masteradmin/add_product';}?></h3>
			<?php
			$attributes = array('class' => 'email', 'id' =>'form','name'=>'form');		
			 echo form_open($action,$attributes);
			?>
			<fieldset id="priseries">
			<legend><?php echo $this->lang->line('Add_product');?></legend>
				<TABLE>
				<tr>
					<th><label><?php echo $this->lang->line('Product_name');?>:</label></th>
					<td><?php
							if(!empty($product)){ $pname=$product->product_name;}else{ $pname='';}
							$data = array(
									'name'        => 'productname',
									'id'          => 'productname',
									'value'       => $pname,
									'class'       => 'required alphanumeric',
									);
							echo form_input($data);?>
					
						</td>
					<td></td>
				</tr>
				<tr>
					<th><label><?php echo $this->lang->line('Product_rate');?>:</label></th>
					<td><?php
							if(!empty($product)){ $prate=$product->rate;}else{ $prate='';}
							$data = array(
									'name'        => 'Product_rate',
									'id'          => 'Product_rate',
									'value'       => $prate,
									'class'       => 'required ',
									);
							echo form_input($data);?>
					
						</td>
					<td></td>
				</tr>
				<tr>
					<th><label><?php echo $this->lang->line('Product_ratetype');?>:</label></th>
					<td>
					<?php
							if(!empty($product)){ $pratetype=$product->rate_type;}else{ $pratetype='';}
							
							?>
							<select name="Product_ratetype" id="Product_ratetype" class="required">
								<option value="">--Select</option>
								<option value="1" <?=(($pratetype==1)?'selected':'');?>>1 Month</option>
								<option value="2" <?=(($pratetype==2)?'selected':'');?>>2 Months</option>
								<option value="3" <?=(($pratetype==3)?'selected':'');?>>3 Months</option>
								<option value="4" <?=(($pratetype==4)?'selected':'');?>>4 Months</option>
								<option value="5"<?=(($pratetype==5)?'selected':'');?>>5 Months</option>
								<option value="6" <?=(($pratetype==6)?'selected':'');?>>6 Months</option>
								<option value="7" <?=(($pratetype==7)?'selected':'');?>>7 Months</option>
								<option value="8" <?=(($pratetype==8)?'selected':'');?>>8 Months</option>
								<option value="9" <?=(($pratetype==9)?'selected':'');?>>9 Months</option>
								<option value="10"<?=(($pratetype==10)?'selected':'');?>>10 Months</option>
								<option value="11"<?=(($pratetype==11)?'selected':'');?>>11 Months</option>
								<option value="12" <?=(($pratetype==12)?'selected':'');?>>12 Months</option>
							
							</select>
						
							
					
						</td>
					<td></td>
				</tr>
				<tr>
					<?php if(!empty($product)){ $drate=$product->discount;}else{ $drate='';} ?>
					<th><label>Discount Rate</label></th>
					<td><input type="text" name="discount_rate" id="discount_rate"  class="required numeric" value="<?=$drate?>"/></td>
					<td></td>
				
				</tr>
				<tr>
					<?php if(!empty($product)){ $ddate=$product->discount_date;}else{ $ddate='';} ?>
					<th><label>Discount Rate</label></th>
					<td><input type="text" class="datepicker required" name="discount_date" id="discount_date" value="<?=(($ddate!="")?$ddate:date('Y-m-d'));?>"/> </td>
					<td></td>
				
				</tr>
				
				
				</TABLE>
			</fieldset>
			<table><tr><td><center>
<input id="button1" type="submit" name="update_system" value="<?=$this->lang->line('submit')?>" /> 
<input id="button2" type="reset" value="<?=$this->lang->line('reset')?>" />
</center></td></tr></table>
			<?php echo form_close();?>
			
			</div>
