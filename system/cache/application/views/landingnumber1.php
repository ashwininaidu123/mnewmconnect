<script language="javascript" type="text/javascript">
$(document).ready(function(){
	
});
   </script>
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title" id="myModalLabel">Number Config</h4>
			</div>
			<div class="modal-body">
		<?php
			$attributes = array('class' => 'form', 'id' =>'landingnumberFrm1','name'=>'landingnumberFrm1');		
			 echo form_open('',$attributes);
			 $pri='';$number='';$region='';
			 if(!empty($arr)){
					$pri=$arr[0]['pri'];
					$number=$arr[0]['number'];
					$region=$arr[0]['region'];
				}
				//echo $pri."==>".$number."===>".$region;
		?>
	
				<TABLE>
					<tr>
						<td colspan="3"><div id="errors" style="color:red;text-align=center;"></div></td>
					</tr>
					<tr>
						<div class="form-group"><label  class="col-sm-4 text-right">Landing Number :</label>
								<div class="col-sm-6 input-icon right">
							<input type="text" name="landingnumber" id="landingnumber" class="required number form-control" value="<?php echo $number;?>"/>
							 </div>
					</div>
					</tr>
					<tr>
						<div class="form-group"><label  class="col-sm-4 text-right">PriNumber :</label>
								<div class="col-sm-6 input-icon right">
						<?php echo form_dropdown('pri',$dropdown,$pri,'id="pri" class="required form-control"');?>
							 </div>
					</div>
					</tr>
					<tr>
						<div class="form-group"><label  class="col-sm-4 text-right">Region :</label>
								<div class="col-sm-6 input-icon right">
						<input type="text" name="region" id="region" class="required form-control" value="<?php echo $region;?>">	
								 </div>
					</div>
					</tr>
			 <div class="form-group col-sm-12 text-center">
				   <input id="button1" type="submit" class="btn btn-primary" name="update_system" value="<?=$this->lang->line('submit') ?>" /> 
                   <input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
              </div>
				</TABLE>

		<?php echo form_close();?>
</div>
