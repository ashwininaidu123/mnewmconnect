<script language="javascript" type="text/javascript">
   $(document).ready(function(){
	   jQuery.validator.addMethod("numeric", function(value, element) {
		return this.optional(element) || value == value.match(/^[0-9]+$/);
		}, "Allows only numeric values"); 
	  $("#productconfig").validate({
		 	  
		  errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			}
		  });
	  
   });
   </script>
<div id="box">
		<?php
			$attributes = array('class' => 'form', 'id' =>'productconfig','name'=>'productconfig');		
			 echo form_open($faction,$attributes);
		?>
		<fieldset id="priseries">
				<legend><?php echo "Product Config";?></legend>
				<TABLE>
					<?php
						for($i=0;$i<sizeof($res);$i++)
						{
					?>
						 	<tr>
								<!--<td><?//?></td>-->
								<th><label><?=$res[$i]['productname']?></label></th>
								<td><input type="text" name="product[<?=$res[$i]['product_id']?>]" id="product[<?=$res[$i]['product_id']?>]" value="<?=$res[$i]['rate']?>" class="required number" min="0"/></td>
								<td></td>
						 	</tr>	
							
							
							
					<?php
							
						}
					
					
					?>	
					
				
				</TABLE>
				
				
				
		</fieldset>
		<table><tr><td><center>
<input id="button1" type="submit" name="submit" value="<?=$this->lang->line('submit')?>" /> 
<input id="button2" type="reset" value="<?=$this->lang->line('reset')?>" />
</center></td></tr></table>
		<?php echo form_close();?>
</div>

