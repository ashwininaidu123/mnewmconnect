 	<script>
$(function() {
	$('.DeleteItem').click(function(){
		var url="<?php echo base_url();?>"+"group/Delete_group/"+this.id
		if(confirm("<?php echo $this->lang->line('level_groupdelete_msg');?>")){
			$.get(url, function(data){
				window.parent.location.href = window.parent.location.href;
			});
			
		}
	});
	
});
</script>
	<div id="box">
		<h3><?php echo $this->lang->line('level_Manage_Group');?></h3>
			<?php
			$attributes = array('class' => 'email', 'id' =>'forms','name'=>'forms');	
			 echo form_open('group/manage_group',$attributes);
 
			?><?php echo $this->pagination->create_links(); ?>
			<table>
			<tr>
				<td>
					<label><?php echo $this->lang->line('level_Prinumbers');?></label>
					
				</td>
				<td>
					<input type="text" name="prinumber" id="prinumber" value="<?php echo $this->session->userdata('prinumber');?>"size="6"/>
				</td>
				<td>
					<label><?php echo $this->lang->line('g_groupname');?></label>
				</td>	
				<td>
					<input type="text" name="groupnumber" id="groupnumber" value="<?php echo $this->session->userdata('groupnumber');?>"size="6"/>
				</td>
				<td><input type="submit" name="submit" id="submit" value="search"/></td>
			</tr>
			</table>
			<table width="100%">
				<thead>
				<tr>
				<?php
				if(isset($system['g'])){
					$arrs=array_keys($system['g']);
					for($i=0;$i<sizeof($arrs);$i++)
					{
					 ?>
					
					<?php
					if(in_array($arrs[$i],$check_val)){ ?>
					 <th><a href="#"><?=isset($system['g'][$arrs[$i]])?$system['g'][$arrs[$i]]:$this->lang->line('g_'.$arrs[$i])?></a></th>
						<?php
							}
						?>
					
				<?php	
					}		
				}
				?>
				<?php
				if(isset($custom['g'])){
					$arr=array_keys($custom['g']);
					for($i=0;$i<sizeof($arr);$i++)
					{
				  ?>	<?php 
						if(in_array($arr[$i],$check_val)){
							echo "<th><a href='#'>".$custom['g'][$arr[$i]]."</a></th>";
		     				 }
					?>	

				<?php
					}					
				}
				?>
				<th><a href="#"><?php echo $this->lang->line('level_Action');?></a></th>
			</tr>	
			</thead>	
				
				<?php 
					for($j=0;$j<sizeof($group_list);$j++)
					{
						?>
						<tr>
						<?php		
							if(isset($system['g'])){
							$arri=array_keys($system['g']);
							for($x=0;$x<sizeof($arri);$x++)
							{
// 								echo $arri[$x];
								if(in_array($arri[$x],$check_val)){ 
										
									echo "<td align='center'>".$group_list[$j][$arri[$x]]."</td>";
									//echo $arri[$x]."<br/>";
									//echo $group_list[$j][$arri[$x]]."<br/>";
										
								}
							}
							}
						?>
						<?php
							if(isset($custom['g'])){
							$arry=array_keys($custom['g']);
							//print_r($arry);
							for($y=0;$y<sizeof($arry);$y++)
							{
								if(in_array($arry[$y],$check_val)){ 
									
										if(isset($custom_group_list[$arry[$y]."~".$group_list[$j]['gid']])){
									echo "<td align='center'>".$custom_group_list[$arry[$y]."~".$group_list[$j]['gid']]."</td>";
											}
										else{
											echo "<td align='center'>NULL</td>";
											}
										
								}
							}

							

						?>
							
						<?php } ?>
						<td align="center">
										<a href="<?php echo site_url('group/edit/'.$group_list[$j]['gid']);?>"><img src="<?php echo site_url('system/application/img/icons/edit.png');?>" title="Edit" width="16" height="16" /></a>
										<span class="DeleteItem" id="<?php echo $group_list[$j]['gid'];?>" title="Delete" class="glyphicon glyphicon-trash"></span>
										<a href="<?php echo site_url('group/Addemp_group/'.$group_list[$j]['gid']);?>"><img src="<?php echo site_url('system/application/img/icons/addtogroup.png');?>" title="Add Employee in group" width="16" height="16" /></a>
										<a href="<?php echo site_url('group/group_emp_list/'.$group_list[$j]['gid']);?>"><img src="<?php echo site_url('system/application/img/icons/list.ico');?>" title="Add Employee in group" width="16" height="16" /></a>
									</td>
						</tr>
						<?php


					}
					


				?>

			


				



		

			</table>


			<?php echo form_close();?>


	</div>
