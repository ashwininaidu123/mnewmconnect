<script language="javascript" type="text/javascript">
$(document).ready(function(){
	$('#button1').live('click',function(event){
			var selector_checked = $("input[@id=eids]:checked").length; 
			if(selector_checked==0){
				$('#error')	.html("Please select Atleast One Email");
				return false;	
			}else{
				$('#error')	.html(" ");
			}
			
	});
	
	
});
   </script>
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title" id="myModalLabel"><?php echo "Unconfirmed Employees";?></h4>
			</div>
			<div class="modal-body">
		<?php
			$attributes = array('class' => 'form', 'id' =>'landingnumberFrm','name'=>'landingnumberFrm');		
			 echo form_open($faction,$attributes);
			//print_r($res);
				//echo $pri."==>".$number."===>".$region;
		?>

				<table>
					<tr><td colspan="3"><div id="error" style="color:red;text-align:center;"></div></td></tr>
					<tr>
							<?php
								for($i=0;$i<sizeof($res);$i++){
									if($i%3!=0){
										?>
											<td><label><input type="checkbox" name="eids[]" id="eids" value="<?php echo $res[$i]['eid'];?>"/><?php echo $res[$i]['empemail'];?></label></td>
										<?php 
									}else{
										?>
											</tr><td><label><input type="checkbox" name="eids[]" id="eids" value="<?php echo $res[$i]['eid'];?>"/><?php echo $res[$i]['empemail'];?></label></td>
										<?php
									}
								}
							?>
					</tr>
				</table>

		<table><tr><td><center>
<input id="button1" type="submit" name="submit" class="btn btn-primary"  value="<?=$this->lang->line('submit')?>" /> 
<input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
</center></td></tr></table>
		<?php echo form_close();?>
</div>
