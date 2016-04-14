<script language="javascript" type="text/javascript">
   $(document).ready(function(){
	   
	   jQuery.validator.addMethod("numbers", function(value, element) {
		return this.optional(element) || value == value.match(/^[0-9]+$/);
		}, "Allows only Numeric"); 
	  $("#prinumber").validate({
		  rules:{
			 Prinumber:{
			  numbers:true
			},
			from:{
				numbers:true
			},
			 to:{
			  numbers:true
			}
		  },
		  messages:{
			 
		  },		  
		  errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			}
		  });
	  
   });
   </script>
<div id="box">
<h3><?php echo "Adding Prinumber";?></h3>
		<?php
			$attributes = array('class' => 'form', 'id' =>'prinumber','name'=>'prinumber');		
			 echo form_open('Masteradmin/addPrinumber',$attributes);
		?>
		<fieldset id="priseries">
				<legend><?php echo "Adding Prinumber";?></legend>
				<TABLE>
					<tr>
						<th><label>Prinumber Number :</label></th>
						<td><input type="text" name="Prinumber" id="Prinumber" class="required" /></td>	
						<td></td>	
					</tr>
					<tr>
						<th><label>from:</label></th>
						<td><input type="text" name="from" id="from" value="1"  class="required" maxlength="2" /></td>	
						<td></td>	
					</tr>
					<tr>
						<th><label>to:</label></th>
						<td><input type="text" name="to" id="to" value=""  class="required" maxlength="2"/></td>	
						<td></td>	
					</tr>
				
				</TABLE>
				
		</fieldset>
		<table><tr><td><center>
<input id="button1" type="submit" name="submit" value="<?=$this->lang->line('submit')?>" /> 
<input id="button2" type="reset" value="<?=$this->lang->line('reset')?>" />
</center></td></tr></table>
				<?php echo form_close();?>
</div>
		
		
		
				
