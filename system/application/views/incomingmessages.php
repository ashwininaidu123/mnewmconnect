<div id="box">
		<h3><?php echo $this->lang->line('level_incomingmessages');?></h3>
		<?php echo $this->pagination->create_links(); 
			$attributes = array('class' => 'email', 'id' =>'forms','name'=>'forms');	
			 echo form_open('keyword/incomingmessages',$attributes);
			?>
			<table>
			<tr>
				<td>
					<label><?php echo $this->lang->line('level_codeid');?></label>
				</td>
				<td>
					<input type="text" name="codeid" id="codeid" value="<?php echo $this->session->userdata('codeid');?>"size="6"/>
				</td>
				<td>
					<label><?php echo $this->lang->line('level_keyword');?></label>
				</td>	
				<td>
					<input type="text" name="keyword" id="keyword" value="<?php echo $this->session->userdata('keyword');?>"size="6"/>
				</td>
				<td>
					<label><?php echo $this->lang->line('level_subkeyword');?></label>
				</td>	
				<td>
					<input type="text" name="subkeyword" id="subkeyword" value="<?php echo $this->session->userdata('subkeyword');?>"size="6"/>
				</td>
				<td>
					<label><?php echo $this->lang->line('level_Employee');?></label>
				</td>	
				<td>
					<input type="text" name="employeename" id="employeename" value="<?php echo $this->session->userdata('employeename');?>"size="6"/>
				</td>
				<td><input type="submit" name="submit" id="submit" value="search"/></td>
			</tr>
			</table>		
			<?php echo form_close();?>
		<table width="100%">
		<thead>
		      <tr>
				 <th><a href="#"><?php echo $this->lang->line('level_sno');?></a></th>
				 <th><a href="#"><?php echo $this->lang->line('level_codeid');?></a></th>
				 <th><a href="#"><?php echo $this->lang->line('level_keyword');?></a></th>	
				 <th><a href="#"><?php echo $this->lang->line('level_subkeyword');?></a></th>
				 <th><a href="#"><?php echo $this->lang->line('level_from');?></a></th>	
				 <th><a href="#"><?php echo $this->lang->line('level_Employee');?></a></th>
				 <th><a href="#"><?php echo $this->lang->line('level_Date');?></a></th>
		    </tr>
		     </thead>
		    <?php
				for($i=0;$i<sizeof($res);$i++)
				{
		    ?>
				<tr>
					<td align="center"><?php echo $res[$i]['incid'];?></td>
					<td align="center"><?php echo $res[$i]['code'];?></td>
					<td align="center"><?php echo $res[$i]['keyword'];?></td>
					<td align="center"><?php echo $res[$i]['subkeyword'];?></td>
					<td align="center"><?php echo $res[$i]['from'];?></td>
					<td align="center"><?php echo $res[$i]['empname'];?></td>
					<td align="center"><?php echo $res[$i]['date_time'];?></td>
				</tr>
		    <?php
				}
		    ?>
		</table>

</div>

