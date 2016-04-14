<script type="text/javascript" src="system/application/js/jquery.custom.js"></script>

<div id="box">
<h3><?php echo "Activity Report List";?></h3>
		<?php
			$attributes = array('class' => 'form', 'id' =>'landingnumber','name'=>'landingnumber');		
			 echo form_open('activity/act_Report/'.$agid.'/'.$actid,$attributes);
		?>
		<input type="hidden" name="download" value="1" />
			<TABLE>
			<TR>
				<td colspan="3"><fieldset id="priseries">
					<legend>Lising Fields</legend>
					
						<ul style="list-style:none;">
						
						<?
						foreach($systemfields as $field){
							$checked = false;
							if($field['type']=='s' && $field['show']){
								foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
								if($checked) 
								?><li><input type="checkbox" checked name="lisiting[<?=$field['fieldname']?>]" value="<?=(($field['customlabel']!="")
															 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname'])?>" /><?=(($field['customlabel']!="")
															 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname'])?></li><?
								
									}elseif($field['type']=='c' && $field['show']){
								//if($bid == 1 || $bid == 47  || $bid == 257){
									foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
									if($checked)
										//$fieldKey = $this->systemmodel->getFieldKey($field['fieldid'],$bid);
									?><li><input type="checkbox" checked name="lisiting[<?=$field['fieldKey']?>]" value="<?=$field['fieldname']?>" /><?=$field['customlabel']?></li><?php
								/*}else{
									foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
									if($checked)
									?><li><input type="checkbox" checked name="lisiting[custom][<?=$field['fieldid']?>]" value="<?=$field['fieldname']?>" /><?=$field['customlabel']?></li><?
								}*/
							}
						}
						if(!empty($customfields)){
							foreach($customfields as $field){
								?>
								<li><input type="checkbox" checked name="lisiting[cus][<?=$field['fieldid']?>]" value="<?=$field['fieldname']?>" /><?=$field['fieldname']?></li>
								<?php
							}
						}	
						
						?>
						</ul>
					</fieldset>	
				</td>
			</TR>
			
			
			</TABLE>
				<table><tr><td><center>
<input id="button1" type="submit" class="btn btn-primary" name="submit" value="<?=$this->lang->line('submit')?>" /> 
<input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
</center></td></tr></table>
				
				
				<?php echo form_close();?>
				
				
				
		
</div>				


