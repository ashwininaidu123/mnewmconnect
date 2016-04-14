<div id="box">
<h3><?php echo $this->lang->line('level_smsreport');?></h3>
		<?php
			$attributes = array('class' => 'form', 'id' =>'landingnumber','name'=>'landingnumber');		
			 echo form_open('Report/sms_csv',$attributes);
		?>
		<fieldset id="priseries">
				<legend><?php echo $this->lang->line('level_smsreport');?></legend>
				<TABLE>
					<tr>
						<th><label>Date From :</label></th>
						<td><input type="text" name="datefrom" id="datefroms" class="datepicker" /></td>
						<td></td>
					</tr>
					<tr>
						<th><label>Date to :</label></th>
						<td><input type="text" name="dateto" id="datetos" class="datepicker" value="<?=date('Y-m-d');?>"/></td>
						<td></td>
					</tr>
					<tr>
						<th><label>Sender Id :</label></th>
						<td>
								<?php echo form_dropdown("senderid",$this->msgmodel->getSenderidList(),'',"senderid");?>
						
						</td>
						<td></td>
					</tr>
					<tr>
						<th><label>Status :</label></th>
						<td>
								<select name="sstatus" id="sstatus">
									<option value=" ">--Select</option>
										<option value="1">Delivered</option>
										<option value="2">Fail</option>
										<option value="3">Sent</option>
								</select>
						</td>
						<td></td>
					</tr>
				</TABLE>
				<TABLE>
					<TR>
						<td colspan="3"><fieldset id="priseries">
										<legend>Lising Fields</legend>
										
											<ul style="list-style:none;">
												<li><input type="checkbox" checked name="lisiting[senderid]" id="lisiting[senderid]" value="senderid" /><?=$this->lang->line('level_senderid')?></li>
												<li><input type="checkbox" checked name="lisiting[number]" id="lisiting[number]" value="number" /><?=$this->lang->line('level_number')?></li>
												<li><input type="checkbox" checked name="lisiting[content]" id="lisiting[content]" value="content" /><?=$this->lang->line('level_content')?></li>
												<li><input type="checkbox" checked name="lisiting[total]" id="lisiting[total]" value="total" /><?=$this->lang->line('level_total')?></li>
												<li><input type="checkbox" checked name="lisiting[datetime]" id="lisiting[datetime]" value="datetime" /><?=$this->lang->line('level_datetime')?></li>
												<li><input type="checkbox" checked name="lisiting[scheduleat]" id="lisiting[scheduleat]" value="scheduleat" /><?=$this->lang->line('level_sheduletime')?></li>
												
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
				
				
				
				
		</fieldset>
</div>				
