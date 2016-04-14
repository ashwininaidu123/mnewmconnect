<script>
	$("#showtext").hide();
	</script>
<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
			<h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('level_custom_header');?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
				<?php
				$url=base_url()."customfield/Addcustom/";
				?>
				<form id="customfieldforms" name="customfieldforms" parsley-validate class="form-horizontal form icon-validation" method ="post" >
				<input type="hidden" name="grouptype" id="grouptype" value="<?php echo $str;?>" />
				<div class="form-group">
			<label   class="col-sm-4 text-right"><?php echo $this->lang->line('level_labelname');?> : </label>
			 <div class="col-sm-6 input-icon right" >          
				<input type="text" name="label_name" id="label_name" class="required form-control" />
			   </div>
			</div>
				<div class="form-group">
					<label  class="col-sm-4 text-right"><?php echo $this->lang->line('level_labeltype');?> : </label>
					<div class="col-sm-6 input-icon right" >          
						<select id="label_list" name="label_list" class="required form-control">
							<option value=""><?php echo $this->lang->line('level_select');?></option>
							<option value="text">Text</option>
							<option value="textarea">Textarea</option>
							<option value="radio">radio</option>
							<option value="checkbox">checkbox</option>
							<option value="dropdown">Dropdown</option>
							<option value="datetime">Date & Time</option>
						</select>
					  </div>
				</div>	
				<div class="form-group">
					<div id="showtext">
						<label  class="col-sm-4 text-right"><?php echo $this->lang->line('level_labeloptions');?> : </label>
						<div class="col-sm-6 input-icon right" >          
							<textarea class="form-control" name="labeloptions" id="labeloptions"></textarea>
							<div class='varlist'><a title='Click to insert action as URL to post the entire value of the current record' class='addVar' rel='|' style="cursor:pointer;" > Add Action</a></div>
						 </div>
					</div>
				</div>	
				<div class="form-group">
					<label  class="col-sm-4 text-right"><?php echo $this->lang->line('level_labeldefaultvalue');?> : </label>
					<div class="col-sm-6 input-icon right" >          
						<input type="text" name="labeldefaultval" id="labeldefaultval" class="form-control"/>	
						<div id="err1" style="color:red;"></div>
					 </div>
				</div>	
				<div class="form-group">
					<label  class="col-sm-4 text-right"><?php echo $this->lang->line('level_isrequired');?> : </label>
					<div class="col-sm-6 input-icon right" >          
							<?php echo form_checkbox(array('name'=>'isrequired','id'=>'isrequired'),'1');?>
					</div>
				</div>	
				<div class="form-group text-center">
					<input type="submit" name="submit" id="submit" class="btn btn-primary" onclick="javascript:$('#customfieldforms').parsley('validate')" value="<?php echo $this->lang->line('level_custom_header');?>" />
				</div>
				</div>
			</div>
		</div>
	</div>
</div>
