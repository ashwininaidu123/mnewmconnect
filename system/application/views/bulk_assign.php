<div id="box">
<h3><?php echo $this->lang->line('level_leadsreport');?></h3>
		<?php
			$attributes = array('class' => 'form', 'id' =>'landingnumber','name'=>'landingnumber');	
			$URL = "leads/index/";	
			echo form_open($URL,$attributes);
		?>
		<fieldset id="priseries">
				<legend><?php echo $this->lang->line('level_leadsreport');?></legend>
				<TABLE>
					<tr>
						<th><label>Group :</label></th>
						<td><!--<input type="text" name="groupname" id="groupname" />-->
							<?php 
								$js = 'id="groupname" multiple';
								echo form_dropdown('groupname[]',$this->systemmodel->get_groups(),'',$js);
							?>
						</td>
						<td></td>
					</tr>
					<tr>
						<th> OR </th>
						<td></td>
					</tr>
					<tr>
						<th><label>Employee :</label></th>
						<td><!--<input type="text" name="empname" id="empname" />-->
							<?php 
								$js1 = 'id="empname" multiple';
							echo form_dropdown('empname[]',$this->groupmodel->employee_list(),'',$js1);?>
						</td>
						<td></td>
					</tr>
				</TABLE>
				<table><tr><td><center>
				<input id="button1" type="submit" class="btn btn-primary" name="submit" value="<?=$this->lang->line('submit')?>" /> 
				<input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
				</center></td></tr></table>
				<?php echo form_close();?>
		</fieldset>
</div>				


