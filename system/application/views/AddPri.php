<script language="javascript" type="text/javascript">
$(document).ready(function(){
$('#hiderow').hide();
$('#hiderow1').hide();
});	
</script>
		<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title" id="myModalLabel">Add Pri</h4>
			</div>
			<div class="modal-body">


		<?php
		    
			$attributes = array('class' => 'form', 'id' =>'priadd','name'=>'priadd');		
			 echo form_open('',$attributes);
			 $pri='';$number='';$region='';
			//echo base_url();exit;
				//echo $pri."==>".$number."===>".$region;
		?>
				<TABLE>
					<tr>
						<td colspan="3"><div id="errors" style="color:red;text-align=center;"></div></td>
					</tr>
					<tr>
					<div class="form-group"><label class="col-sm-4 text-right">Pri Number :</label>
						<div class="col-sm-6 input-icon right">
							<input type="text" name="prinum" id="prinum" class="required number form-control" value=""/>
							   </div>
					</div>
					</tr>
					<tr>
						<div class="form-group"><label class="col-sm-4 text-right">From :</label>
						<div class="col-sm-6 input-icon right">
						<input type="text" name="from" id="from" class="number form-control" maxlength="2">
							   </div>
					</div>
					</tr>
					<tr>
						<div class="form-group"><label class="col-sm-4 text-right">To :</label>
						<div class="col-sm-6 input-icon right">
						<input type="text" name="to" id="to" class="number form-control" maxlength="2">
								   </div>
					</div>
					</tr>
					
					<tr id="hiderow">
						<div class="form-group"><label class="col-sm-4 text-right">Package :</label>
						<div class="col-sm-6 input-icon right">
						<select name="package" id="package" class="form-control">
								<?php foreach($this->mastermodel->get_allpackages() as $pack){
									echo "<option  value='".$pack['package_id']."'>".$pack['packagename']."</option>";
										
								}
								?> 
						
						</select>	
							   </div>
					</div>
					</tr>
					<tr id="hiderow1">
						<div class="form-group"><label class="col-sm-4 text-right">Associated with :</label>
						<div class="col-sm-6 input-icon right">
						    <input type="radio" name="calltrack" id="calltrack" value="0" checked>Call track
							<input type="radio" name="calltrack" id="calltrack" value="1">PBX
							   </div>
					</div>
					</tr>
					<tr>
						<div class="form-group"><label class="col-sm-4 text-right">Provider :</label>
						<div class="col-sm-6 input-icon right">
						<select name="provider" id="provider" class="form-control">
									<option value="">----Select----</option>
									<? foreach($this->mastermodel->Provider() as $key=>$num){?>
								
								<option value="<?=$num?>" <?=($num==$pri)?'selected':''?>><?=$num?></option>
							<? }?>
									
							
						</select>	
							   </div>
					</div>
					</tr>
					<tr>
						<div class="form-group"><label class="col-sm-4 text-right">Demo Creating Users :</label>
						<div class="col-sm-6 input-icon right">
						<input type="checkbox" name="demo" id="demo" class="form-control" value="1">	
						   </div>
					</div>
					</tr>
					<tr>
						<td colspan="3" Align="left">Note : Demo creating users checked it will take landing number as pri </td>
					
					</tr>
				<div class="form-group col-sm-12 text-center">
				   <input id="button1" type="submit" class="btn btn-primary" name="update_system" value="<?=$this->lang->line('submit') ?>" /> 
                   <input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
              </div>
				</TABLE>

	       
		<?php echo form_close();?>
</div>
