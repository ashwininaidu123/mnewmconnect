<script>
$(function() {
	$('.DeleteItem').click(function(){
		if(confirm("Do you want to delete this items?")){
			var url="<?php echo base_url();?>"+"Report/Delete_callupte/"+this.id
			$.get(url, function(data){
				
				window.parent.location.href = window.parent.location.href;
			});
		}
	});
});
</script>
<div id="box">
		<h3><?php echo $this->lang->line('level_manageReport');?></h3>
			<?php
			$attributes = array('class' => 'email', 'id' =>'forms','name'=>'forms');	
			 echo form_open('group/add_group',$attributes);
				//print_r($call_list);
 			?>
			<table width="100%">
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
				<!--<a href="<?php echo site_url('Report/edit/'.$call_list[$i]['callid']);?>"><span title="Edit" class="fa fa-edit"></span></a>-->
				<span class="DeleteItem" id="<?php echo $call_list[$i]['callid'];?>"  title="Delete" class="glyphicon glyphicon-trash"></span>

				</td>
			</tr>		

			<?php

				}


			?>			




			</table>




			<?php echo form_close();?>
</div>
