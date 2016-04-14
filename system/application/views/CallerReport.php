<div id="box">
		<h3><?php echo $this->lang->line('level_Edit_callerinfo');?></h3>
			<?php
			$attributes = array('class' => 'email', 'id' =>'form','name'=>'form');	
			 echo form_open('EditTrackReport/'.$call_list[0]['callid'],$attributes);
			?>
			<fieldset id="priseries">
				<legend><?php echo $this->lang->line('level_callerdetail');?></legend>
				<table width="100%">
					<tr>
						<th><label><?php echo $this->lang->line('g_groupname');?></label></th>
						<td>: <?php echo $call_list[0]['groupname'];?></td>
						<td></td>

					</tr>
					<tr>
						<th><label><?php echo $this->lang->line('e_employeename');?></label></th>
						<td>: <?php echo $call_list[0]['empname'];?></td>
						<td></td>

					</tr>
					<tr>
						<th><label><?php echo $this->lang->line('level_callfrom');?></label></th>
						<td>: <?php echo $call_list[0]['callfrom'];?></td>
						<td></td>

					</tr>
					<tr>
						<th><label><?php echo $this->lang->line('level_callername');?></label></th>
						<td>: <input type="text" name="callername" id="callername" /></td>
						<td></td>

					</tr>
					<tr>
						<th><label><?php echo $this->lang->line('level_calleraddress');?></label></th>
						<td>: <textarea name="calleraddress" id="calleraddress"></textarea></td>
						<td></td>

					</tr>
					
					<tr>
						<th><label><?php echo $this->lang->line('level_callerremarks');?></label></th>
						<td>: <textarea name="remarks" id="remarks"></textarea></td>
						<td></td>

					</tr>
					<tr>
						<th><label><?php echo $this->lang->line('level_starttime');?></label></th>
						<td>: <?php echo $call_list[0]['starttime'];?></td>
						<td></td>

					</tr>
					<tr>
						<th><label><?php echo $this->lang->line('level_endtime');?></label></th>
						<td>:<?php echo $call_list[0]['endtime'];?></td>
						<td></td>

					</tr>
				</table>
				<table>	<tr>
					<td colspan="3" align="center">
					  <?php echo form_submit('UpdateCaller', 'UpdateCaller');?>
					
					
					</td>

				</tr>		
			</table>	

			</fieldset>
	
			<?php echo form_close();?>
</div>
