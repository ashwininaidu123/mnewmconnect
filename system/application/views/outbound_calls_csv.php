<script src="system/application/plugins/jquery-ui/jquery-ui-1.10.4.min.js"></script>
<script src="system/application/js/ui/jquery-ui-timepicker-addon.js"></script>
<script src="system/application/js/application.js"></script>
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4><?php echo 'Click to Connect Report';?></h4>
			</div>
			<div class="modal-body">
				<div class="row">					

					<div class="col-md-12 col-sm-12 col-xs-12">
						<?php
							$attributes = array('class' => 'form', 'id' =>'c2cdown','name'=>'c2cdown');		
							echo form_open('Report/outbound_calls/',$attributes);
						?>
						<input type="hidden" name="download" value="1" />
						<TABLE class="table table-striped">
							<tr>
								<th><label>Start Time :</label></th>
								<td><input type="text" name="starttimes" id="starttimes" class="datepicker form-control" value="<?=date('Y-m-d h:i:s');?>" /></td>
							</tr>
							<tr>
								<th><label>End Time :</label></th>
								<td><input type="text" name="endtimes" id="endtimes" class="datepicker form-control" value="<?=date('Y-m-d');?>"/></td>
							</tr>
							<tr>
								<th><label>Executive Number :</label></th>
								<td>
									<input type="text" name="exenumber" id="exenumber" class="form-control" />
								</td>
							</tr>
							<tr>
								<th><label>Customer Number :</label></th>
								<td>
									<input type="text" name="cusnumber" id="cusnumber" class="form-control" />
								</td>
							</tr>
						</TABLE>
						<TABLE class="table table-striped">
						<TR>
							<td colspan="3"><fieldset id="priseries">
								<h4><?php echo "Listing Fields";?></h4>
									<ul style="list-style:none;">
										<li><input type="checkbox" checked name="lisiting[executive]" id="executive" value="Executive"/>Executive Number</li>
										<li><input type="checkbox" checked name="lisiting[customer]" id="customer" value="Customer"/>Customer Number</li>
										<li><input type="checkbox" checked name="lisiting[starttime]" id="starttime" value="Start time"/>Start Time</li>
										<li><input type="checkbox" checked name="lisiting[endtime]" id="endtime" value="End time"/>End Time</li>
										<li><input type="checkbox" checked name="lisiting[status]" id="status" value="status"/>Status</li>
										<li><input type="checkbox" checked name="lisiting[source]" id="source" value="Source"/>Source</li>
										<li><input type="checkbox" checked name="lisiting[credit]" id="credit" value="Credit"/>Credit</li>
										<li><input type="checkbox" checked name="lisiting[filename]" id="filename" value="filename"/>Filename</li>
									</ul>
								</fieldset>	
							</td>
						</TR>
						</TABLE>
                       <table><tr><td><center>
						<input id="button1" type="submit" name="submit" class="btn btn-primary"	value="<?=$this->lang->line('submit')?>" /> 
						<input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
						</center></td></tr></table>
						<?php echo form_close();?>

					</div>
				</div>
			</div>
		</div>
</div>
