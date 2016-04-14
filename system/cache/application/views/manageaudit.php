<div id="box">
		<h3><?php echo $this->lang->line('level_audit_report');?></h3>
		<div class="pagination" align="left">
			<?php echo $this->pagination->create_links();?>    
		</div>
		<?php 
			$attributes = array('class' => 'email', 'id' =>'forms','name'=>'forms');	
			 echo form_open('Report/auditlog',$attributes);
			//print_r($custom_group_list);
			?>
			
			<table>
			<tr>
				<td>
					<label><?php echo $this->lang->line('level_Username');?></label>
					
				</td>
				<td>
					<input type="text" name="username" id="username" value="<?php echo $this->session->userdata('usernames');?>"size="6"/>
				</td>
				<td>
					<label><?php echo $this->lang->line('level_Group');?></label>
				</td>	
				<td>
					<input type="text" name="group" id="group" value="<?php echo $this->session->userdata('group');?>"size="6"/>
				</td>
				<td><input type="submit" name="submit" id="submit" value="search"/></td>
			</tr>
			</table>		
			<?php echo form_close();?>
		<table width="100%">
		   <thead>
		      <tr>
				 <th><a href="#"><?php echo $this->lang->line('level_sno');?></a></th>
				 <th><a href="#"><?php echo $this->lang->line('level_Username');?></a></th>	
				 <th><a href="#"><?php echo $this->lang->line('level_modulename');?></a></th>
				 <th><a href="#"><?php echo $this->lang->line('level_Action');?></a></th>	
				 <th><a href="#"><?php echo $this->lang->line('level_datetime');?></a></th>
		    </tr>
		     </thead>
			<?php
				for($i=0;$i<sizeof($list);$i++)
				{
				?>
				<tr>
						<td align="center"><?php echo $list[$i]['sno'];?></td>
						<td align="center"><?php echo $list[$i]['username'];?></td>
						<td align="center"><?php echo $list[$i]['module_name'];?></td>
						<td align="center"><?php echo $list[$i]['action'];?></td>
						<td align="center"><?php echo $list[$i]['date_time'];?></td>
				</tr>
				<?php }?>
		
		</table>

</div>
