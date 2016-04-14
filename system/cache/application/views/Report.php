<script>
$(function() {
	$('.DeleteItem').click(function(){
		if(confirm("Do you want to delete this items?")){
			var url="<?php echo base_url();?>"+"Report/Delete_call/"+this.id
			$.get(url, function(data){
				window.parent.location.href = window.parent.location.href;
			});
		}
	});
});
</script>
<div id="box">
		<h3><?php echo $this->lang->line('level_Report');?></h3>
			<?php
			$attributes = array('class' => 'email', 'id' =>'forms','name'=>'forms');	
			 echo form_open('Report/index',$attributes);
				//print_r($call_list);
 			?>
 			
			
			<table width="100%">
			<tr>
			
				<td><?php echo $this->lang->line('g_groupname');?> : <input type="text" name="gname" id="gname" value="<?php echo $this->session->userdata('gname');?>" size="8"/></td>
				<td><?php echo $this->lang->line('e_employeename');?> : <input type="text" name="ename" id="ename" value="<?php echo $this->session->userdata('ename');?>" size="8"/></td>
				<td><?php echo $this->lang->line('level_callfrom');?> : <input type="text" name="callfrom" id="callfrom" value="<?php echo $this->session->userdata('callfrom');?>" size="8"/></td>
				<td><?php echo $this->lang->line('level_starttime');?> : <input type="text" name="starttime" id="starttime" value="<?php echo $this->session->userdata('starttime');?>" size="8" class="datepicker"/></td>
				<td><?php echo $this->lang->line('level_endtime');?> : <input type="text" name="endtime" id="endtime" value="<?php echo $this->session->userdata('endtime');?>" size="8" class="datepicker"/></td>
				<td></td>
					
			</tr>
			<tr>
				<td colspan="6" align="center"><input type="submit" name="search" id="search" value="search" /></td>
			
			</tr>
			
			
			
			<tr>
				<td align="right" colspan="6" height="40" ><a href="<?php echo base_url();?>Report/exportdata">Export data</a></td>
			</tr>
			<tr>
				<!--<th><a href="#"><?php echo $this->lang->line('p_businessname');?></a></th>-->
				<th><a href="#"><?php echo $this->lang->line('g_groupname');?></a></th>
				<th><a href="#"><?php echo $this->lang->line('e_employeename');?></a></th>
				<th><a href="#"><?php echo $this->lang->line('level_callfrom');?></a></th>
				<th><a href="#"><?php echo $this->lang->line('level_starttime');?></a></th>
				<th><a href="#"><?php echo $this->lang->line('level_endtime');?></a></th>
				<th><a href="#"><?php echo $this->lang->line('level_Action');?></a></th>

			</tr>
			<?php
				for($i=0;$i<sizeof($call_list);$i++)
				{
			?>	
			<tr>
				<!--<td align="center"><?php echo $call_list[$i]['businessname'];?></td>-->
				<td align="center"><?php echo $call_list[$i]['groupname'];?></td>
				<td align="center"><?php echo $call_list[$i]['empname'];?></td>
				<td align="center"><?php echo $call_list[$i]['callfrom'];?></td>
				<td align="center"><?php echo $call_list[$i]['starttime'];?></td>
				<td align="center"><?php echo $call_list[$i]['endtime'];?></td>
				<td align="center">
				<a href="<?php echo site_url('EditTrackReport/'.$call_list[$i]['callid']);?>"> <span title="Edit" class="fa fa-edit"></span></a>
				<span class="DeleteItem" id="<?php echo $call_list[$i]['callid'];?>"  title="Delete" class="glyphicon glyphicon-trash"></span>
				</td>
			</tr>		
			<?php
				}
			?>			
			</table>
			<?php echo form_close();?>
</div>
