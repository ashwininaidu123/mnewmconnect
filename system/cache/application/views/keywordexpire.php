<script language="javascript" type="text/javascript">
   $(document).ready(function(){
	   jQuery.validator.addMethod("numeric", function(value, element) {
		return this.optional(element) || value == value.match(/^[0-9]+$/);
		}, "Allowed only Numeric values"); 
	  $("#landingnumber").validate({
		  rules:{
			 landingnumber:{
			  numeric:true
			},
			 pri:{
			  numeric:true
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
<h3>Keyword Config</h3>
		<?php
			$attributes = array('class' => 'form', 'id' =>'landingnumber','name'=>'landingnumber');		
			 echo form_open('Masteradmin/keyword_expire/'.$id,$attributes);
		?>
		<fieldset id="priseries">
				<legend><?php echo "Keyword Config";?></legend>
				<TABLE>
					
					<tr>
						<th><label>Expire Date :</label></th>
						<td><input type="text" name="exdate" id="exdate" class="required datepicker" /></td>	
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
