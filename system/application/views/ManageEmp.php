<script>
$(function() {
	$('.DeleteItem').click(function(){
		if(confirm("Do you want to delete this items?")){
			var url="<?php echo base_url();?>"+"Employee/Delete_Employee/"+this.id
			$.get(url, function(data){
				
				window.parent.location.href = window.parent.location.href;
			});
		}
	});
	$('.ChangeStatus').click(function(){
		if(confirm("Do you want to change the status?")){
			var url="<?php echo base_url();?>"+"Employee/change_status/"+this.id
			$.get(url, function(data){
				window.parent.location.href = window.parent.location.href;
			});
		}
	});

});


</script>

<div id="box">
		<h3><?php echo $this->lang->line('level_Manage_Employee');?></h3>
			<?php
			$attributes = array('class' => 'email', 'id' =>'forms','name'=>'forms');	
			 echo form_open('Employee/manage_emp',$attributes);
			?><?php echo $this->pagination->create_links(); 
			//print_r($custom_group_list);
			?>
			<table>
			<tr>
				<td>
					<label><?php echo $this->lang->line('e_mobile');?></label>
					
				</td>
				<td>
					<input type="text" name="mobilenumber" id="mobilenumber" value="<?php echo $this->session->userdata('mobilenumber');?>"size="6"/>
				</td>
				<td>
					<label><?php echo $this->lang->line('e_employeename');?></label>
				</td>	
				<td>
					<input type="text" name="ename" id="ename" value="<?php echo $this->session->userdata('ename');?>"size="6"/>
				</td>
				<td><input type="submit" name="submit" id="submit" value="search"/></td>
			</tr>
			</table>		
			<table width="100%">
				<thead>
				<tr>
				<?php
				echo "<pre>";
				//print_r($check_val);
				echo "</pre>";
				if(isset($system['e'])){
					$arrs=array_keys($system['e']);
					for($i=0;$i<sizeof($arrs);$i++)
					{
					 ?>
					
					<?php
							/*if($arrs[$i]!="e_busninessname"){*/

					if(in_array($arrs[$i],$check_val)){
						if($arrs[$i]!="e_busninessname"){
						
						 ?>
					 <th><a href="#"><?=isset($system['e'][$arrs[$i]])?$system['e'][$arrs[$i]]:$this->lang->line('e_'.$arrs[$i])?></a></th>
						<?php
						}
							}
						}
						?>
					
				<?php	
					/*}		*/
				}
				?>
				<?php
				if(isset($custom['e'])){
					$arr=array_keys($custom['e']);
					for($i=0;$i<sizeof($arr);$i++)
					{
				  ?>
					
						<?php 

							if(in_array($arr[$i],$check_val)){
								
							echo "<th><a href='#'>".$custom['e'][$arr[$i]]."</a></th>";
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
					echo "<pre>";
				//	print_r($emp_list);				
					echo "</pre>";
					for($j=0;$j<sizeof($emp_list);$j++)
					{
						?>
						<tr>
						<?php
									if($emp_list[$j]!="e_busninessname"){

								if(isset($system['e'])){	
							$arri=array_keys($system['e']);
							for($x=0;$x<sizeof($arri);$x++)
							{
								if(in_array($arri[$x],$check_val)){ 
										if($arri[$x]!="e_busninessname"){
									echo "<td align='center'>".$emp_list[$j][$arri[$x]]."</td>";	
								}
										
								}
							}
							}
							}
					?>
						<?php
							if(isset($custom_group_list)){
							if(isset($custom['e'])){
								$arry=array_keys($custom['e']);
								//print_r($custom_group_list);
								//print_r($custom['e']);
								for($y=0;$y<sizeof($arry);$y++)
								{
									if(in_array($arry[$y],$check_val)){ 
										//echo $arry[$y];
										echo "<td align='center'>".$custom_group_list[$arry[$y]."~".$emp_list[$j]['eid']]."</td>";
											
									}
								}
							}

							

						?>
							
						<?php } ?>
						<td align="center">
										<a href="<?php echo site_url('Employee/edit/'.$emp_list[$j]['eid']);?>"><span title="Edit" class="fa fa-edit"></span></a>
											<span  class="DeleteItem" id="<?php echo $emp_list[$j]['eid'];?>"  title="Delete" class="glyphicon glyphicon-trash"></span>
										
											<?php if($emp_list[$j]['status']=="1"){
												?>
												<span class="fa fa-unlock ChangeStatus"  id="<?php echo $emp_list[$j]['eid'];?>"  title="Disable"></span>
												<?php
												}
												else{
												?>
												<span class="fa fa-lock ChangeStatus" id="<?php echo $emp_list[$j]['eid'];?>" title="Enable"></span>
												<?php } ?>
										</a>
									</td>
						</tr>
						<?php


					}
					


				?>

			
				

				



		

			</table>


			<?php echo form_close();?>


	</div>
