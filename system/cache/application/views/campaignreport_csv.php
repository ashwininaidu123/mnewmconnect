<script type="text/javascript" src="system/application/js/jquery.custom.js"></script>
<div id="box">
<h3><?php echo $this->lang->line('label_Campaignreport');?></h3>
		<?php
			$attributes = array('class' => 'form', 'id' =>'landingnumber','name'=>'landingnumber');		
			 echo form_open('campaign/campaignReport/'.$eid,$attributes);
		?>
		<input type="hidden" name="download" value="1" />
		<fieldset id="priseries">
				<legend><?php echo $this->lang->line('label_Campaignreport');?></legend>
				<TABLE>
					<tr>
						<th><label>Start Time :</label></th>
						<td><input type="text" name="starttimes" id="starttimes" class="datepicker" /></td>
						<td></td>
					</tr>
					<tr>
						<th><label>End Time :</label></th>
						<td><input type="text" name="endtimes" id="endtimes" class="datepicker" value="<?=date('Y-m-d');?>"/></td>
						<td></td>
					</tr>
					<!--<tr>
						<th><label>Group :</label></th>
						<td><!--<input type="text" name="groupname" id="groupname" />
							<?php 
								//$shirts_on_sale = array('multiple');
								//$js = 'id="groupname" multiple';
							//echo form_dropdown('groupname[]',$this->systemmodel->get_groups(),'',$js);?>
						</td>
						<td></td>
					</tr>
					<tr>
						<th><label>Employee :</label></th>
						<td><!--<input type="text" name="empname" id="empname" />
							<?php 
								//$shirts_on_sale = array('multiple');
								//$js1 = 'id="empname" multiple';
							//echo form_dropdown('empname[]',$this->groupmodel->employee_list(),'',$js1);?>
						</td>
						<td></td>
					</tr>-->
				</TABLE>
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
						
						?>
						<li><input type="checkbox" checked name="lisiting[filename]" id="filename" value="filename"/>Filename</li>
						</ul>
					</fieldset>	
				</td>
			</TR>
			</TABLE>
				<table><tr><td><center>
<input id="button1" type="submit" name="submit" value="<?=$this->lang->line('submit')?>" /> 
<input id="button2" type="reset" value="<?=$this->lang->line('reset')?>" />
</center></td></tr></table>
				<?php echo form_close();?>
		</fieldset>
</div>
