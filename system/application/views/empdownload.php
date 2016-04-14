	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4><?php echo $this->lang->line('level_callreport');?></h4>
			</div>
			<div class="modal-body">
				<div class="row">					
					<div class="col-md-12 col-sm-12 col-xs-12">
						<?php
							$attributes = array('class' => 'form', 'id' =>'landingnumber','name'=>'landingnumber');
							echo form_open('ManageEmployee/0',$attributes);
						?>
					<input type="hidden" name="download" value="1" />
				
				<TABLE class="table table-striped">
					<TR>
						<TD><h4><?php echo "Listing Fields";?></h4></TD>
					</TR>
				<TR>
				<td colspan="3">
						<ul style="list-style:none;">
						<?php
						foreach($systemfields as $field){
							$checked = false;
							if($field['type']=='s' && $field['show']){
								foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
								if($checked) 
								?><li><input type="checkbox" checked name="lisiting[<?=$field['fieldname']?>]" value="<?=(($field['customlabel']!="")
															 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname'])?>" /><?=(($field['customlabel']!="")
															 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname'])?></li><?
								
							}elseif($field['type']=='c' && $field['show']){
									foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
									if($checked)
									?><li><input type="checkbox" checked name="lisiting[<?=$field['fieldKey']?>]" value="<?=$field['fieldname']?>" /><?=$field['customlabel']?></li><?php
							}
						}
						?>
						</ul>
				</td>
			</TR>
			</TABLE>
			<table><tr><td><center>
				<input id="button1" type="submit" name="update_system" class="btn btn-primary" value="<?=$this->lang->line('submit')?>" /> 
				<input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
				</center></td></tr></table>
				 <?php echo form_close();?>
					</div>
				</div>
			</div>
		</div>
	</div>
