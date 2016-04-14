<script src="system/application/plugins/jquery-ui/jquery-ui-1.10.4.min.js"></script>
<script src="system/application/js/ui/jquery-ui-timepicker-addon.js"></script>
<script src="system/application/js/application.js"></script>
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title" id="myModalLabel"><?php echo "Audit Log Download";?></h4>
			</div>
			<div class="modal-body">
		<?php
			$attributes = array('class' => 'form', 'id' =>'landingnumber','name'=>'landingnumber');		
			 echo form_open('AuditTrail/',$attributes);
		?>
		<input type="hidden" name="download" value="1" />
		       <div class="form-group">
						<label  class="col-sm-4 text-right">Start Time :</label>
						 <div class="col-sm-6 input-icon right">
						<input type="text" name="starttimes" id="starttimes" class="datepicker form-control" />
					</div>
				 </div>
		     <div class="form-group">
						<label class="col-sm-4 text-right">End Time :</label>
						 <div class="col-sm-6 input-icon right">
						<input type="text" name="endtimes" id="endtimes" class="datepicker form-control"/>
					</div>
				 </div>
				
				
			<TABLE>
			<TR>
				<td colspan="3">
					<fieldset id="priseries">
					<legend>Lising Fields</legend>
						<ul style="list-style:none;">
						<li><input type="checkbox" name="lisiting[username]" id="username" value="username" checked="checked" />Username</li>
						<li><input type="checkbox" name="lisiting[module]" id="module" value="module" checked="checked" />Module Name</li>
						<li><input type="checkbox" name="lisiting[action]" id="action" value="action" checked="checked" />Action</li>
						<li><input type="checkbox" name="lisiting[datetime]" id="datetime" value="datetime" checked="checked" />Date & Time</li>
						</ul>
			         </fieldset>
				</td>
			</TR>
			</TABLE>
			 <div class="form-group text-center">
				   <input id="button1" type="submit" class="btn btn-primary" name="update_system" value="<?=$this->lang->line('submit') ?>" /> 
                   <input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
              </div>
				<?php echo form_close();?>

</div>				

