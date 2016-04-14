<script src="system/application/plugins/jquery-ui/jquery-ui-1.10.4.min.js"></script>
<script src="system/application/js/application.js"></script>
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4><?php echo "Call Summary Report"; ?></h4>
			</div>
			<div class="modal-body">
				<div class="row">					
					<div class="col-md-12 col-sm-12 col-xs-12">
		<?php
			 echo form_open($URL,$attributes);
		?>
		<input type="hidden" name="download" value="1" />
				<TABLE class="table table-striped">
					<tr>
						<th><label>Start Time :</label></th>
						<td><input type="text" name="starttimes" id="starttimes" class="datepicker form-control" value="<?=date('Y-m-d');?>" /></td>
					</tr>
					<tr>
						<th><label>End Time :</label></th>
						<td><input type="text" name="endtimes" id="endtimes" class="datepicker form-control" value="<?=date('Y-m-d');?>" /></td>
					</tr>
					<tr>
						<th><label>Group :</label></th>
						<td>
							<?php 
								$js = 'id="groupname" multiple class="form-control"';
								echo form_dropdown('groupname[]',$this->systemmodel->get_groups(),'',$js);
							?>
						</td>
					</tr>
				</TABLE>
			<TABLE class="table table-striped">
			<TR>
				<td colspan="3">
					<legend>Listing Fields</legend>
						<ul style="list-style:none;">
						<li><input type="checkbox" name="lisiting[groupname]" id="groupname" value="Groupname" checked="checked" />Group Name</li>
						<li><input type="checkbox" name="lisiting[keyword]" id="keyword" value="Keyword" checked="checked" />Keyword</li>
						<li><input type="checkbox" name="lisiting[total]" id="total" value="Total" checked="checked" />Total Calls</li>
						<li><input type="checkbox" name="lisiting[uniquecall]" id="uniquecall" value="Uniquecall" checked="checked" />Unique Calls</li>
						<li><input type="checkbox" name="lisiting[answeredcall]" id="answeredcall" value="Answeredcall" checked="checked" />Answered Calls</li>
						<li><input type="checkbox" name="lisiting[missedcall]" id="missedcall" value="Missedcall" checked="checked" />Missed Calls</li>
						</ul>
				</td>
			</TR>
			</TABLE>
				<table><tr><td><center>
<input id="module" type="hidden" name="module"  value="outbound_call" /> 
<input id="button1" type="submit" name="submit" class="btn btn-primary"	value="<?=$this->lang->line('submit')?>" /> 
<input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
</center></td></tr></table>
				<?php echo form_close();?>
					</div>
				</div>
			</div>
		</div>
</div>
