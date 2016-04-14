<script type="text/javascript" src="system/application/js/jquery.custom.js"></script>

<div id="box">
<h3><?php echo $this->lang->line('level_voicereport');?></h3>
		<?php
			$attributes = array('class' => 'form', 'id' =>'landingnumber','name'=>'landingnumber');		
			 echo form_open('Report/voice_csv',$attributes);
		?>
		<fieldset id="priseries">
				<legend><?php echo $this->lang->line('level_voicereport');?></legend>
				<TABLE>
					<tr>
						<th><label>Shedule Date From :</label></th>
						<td><input type="text" name="sdatef" id="sdateff" class="datepicker" /></td>
						<td></td>
					</tr>
					<tr>
						<th><label>Shedule Date to :</label></th>
						<td><input type="text" name="sdatet" id="sdatett" class="datepicker" value="<?=date('Y-m-d');?>"/></td>
						<td></td>
					</tr>
					<tr>
						<th><label>Number:</label></th>
						<td><input type="text" name="vnumber" id="vnumber" /></td>
						<td></td>
					</tr>
					<tr>
						<th><label>Sound Id:</label></th>
						<td>
							<?php echo form_dropdown("soundid",$this->msgmodel->getSoundList(),'',"soundid");?>
						</td>
						<td></td>
					</tr>
					
				</TABLE>
				<TABLE>
					<TR>
						<td colspan="3"><fieldset id="priseries">
										<legend>Lising Fields</legend>
										
											<ul style="list-style:none;">
												<li><input type="checkbox" checked name="lisiting[number]" id="lisiting[number]" value="number" /><?=$this->lang->line('level_number')?></li>
												<li><input type="checkbox" checked name="lisiting[scheduletime]" id="lisiting[scheduletime]" value="scheduletime" /><?=$this->lang->line('level_sheduletime')?></li>
												<li><input type="checkbox" checked name="lisiting[starttime]" id="lisiting[starttime]" value="starttime" /><?=$this->lang->line('level_starttime')?></li>
												<li><input type="checkbox" checked name="lisiting[endtime]" id="lisiting[endtime]" value="endtime" /><?=$this->lang->line('level_endtime')?></li>
												<li><input type="checkbox" checked name="lisiting[dtmf]" id="lisiting[dtmf]" value="dtmf" /><?=$this->lang->line('level_dtmf')?></li>
												<li><input type="checkbox" checked name="lisiting[title]" id="lisiting[title]" value="dtmf" /><?=$this->lang->line('level_title')?></li>
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

