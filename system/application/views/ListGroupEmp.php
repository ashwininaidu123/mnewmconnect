<div id="box">
<h3><?php echo $this->lang->line('level_List_group');?></h3>
			<?php
			$attributes = array('class' => 'email', 'id' =>'forms','name'=>'forms');	
			 echo form_open('',$attributes);
			echo $this->pagination->create_links(); 

			?>
			<table>
				<tr>
					 <th><label><?php echo $this->lang->line('level_empnumber');?></label></th>
					 <th><label><?php echo $this->lang->line('level_Empname');?></label></th>
 					<th><label><?php echo $this->lang->line('level_group');?></label></th>
 					<th><label><?php echo $this->lang->line('level_empweight');?></label></th>
					<th><label><?php echo $this->lang->line('level_starttime');?></label></th>
					<th><label><?php echo $this->lang->line('level_endtime');?></label></th>
					<th><label><?php echo $this->lang->line('level_Action');?></label></th>
				</tr>
				<?php
					$j=1;
					for($i=0;$i<sizeof($emp_list);$i++)
					{
						
					?>	<tr>
							
							<td align="center"><?php echo $emp_list[$i]['empnumber'];?></td>
							<td align="center"><a href="<?=site_url();?>Employee/activerecords/<?php echo $emp_list[$i]['eid'];?>" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"> <?php echo $emp_list[$i]['empname'];?></a></td>	
							<td align="center"><?php echo $emp_list[$i]['groupname'];?></td>	
							<td align="center"><?php echo $emp_list[$i]['empweight'];?></td>	
							<td align="center"><?php echo $emp_list[$i]['starttime'];?></td>	
							<td align="center"><?php echo $emp_list[$i]['endtime'];?></td>	
							
							<td align="center"><a href="<?php echo site_url('group/delete_grp_emp/'.$emp_list[$i]['eid']."/".$gid);?>">
							<span class="DeleteItem" id="<?php echo $call_list[$i]['callid'];?>" title="Delete Employee from Group" class="glyphicon glyphicon-trash"></span></a></td>	
						</tr>

						
							
					<?php
					$j++;
					}



				?>



			</table>
			
			<?php 
			echo form_close();

			?>
</div>
