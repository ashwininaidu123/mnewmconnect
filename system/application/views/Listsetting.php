<script language="javascript" type="checkbox/javascript">
   $(document).ready(function(){
         $(".session_message").show();
         $(".session_message").fadeOut(5000);
	});	
</script>
<div id="content">
   <div class="session_message"><?php echo $this->session->flashdata('item');?></div>
 	<div id="box">
		<h3><?php echo $this->lang->line('level_System_listing_label');?></h3>	
		<?php
		$attributes = array('class' => 'email', 'id' => 'form','name'=>'form');		
		 echo form_open('customfield/Listsetting',$attributes);
		?>	
		<fieldset id="priseries">
			<legend><?php echo $this->lang->line('level_custom_profile');?></legend>
			<table>
				<?php
				
				if(isset($system['p'])){
					$arrs=array_keys($system['p']);
					for($i=0;$i<sizeof($arrs);$i++)
					{
						
					 ?>
					<tr>
					<td>
					<input type="checkbox" name="p[s][<?php echo $arrs[$i];?>]" value="<?=isset($system['p'][$arrs[$i]])?$system['p'][$arrs[$i]]:$this->lang->line('p_'.$arrs[$i])?>" class="required" <?php
					if(in_array($arrs[$i],$check_val,true)){
							?> checked="true"
					<?php
						}
					?>>
					<?=isset($system['p'][$arrs[$i]])?$system['p'][$arrs[$i]]:$this->lang->line('p_'.$arrs[$i])?>
					</td>
					<td></td>
					</tr>		

				<?php	
						
					}		
										
				}
				?>	

				<?php
				
				if(isset($custom['p'])){
					$arr=array_keys($custom['p']);
					for($i=0;$i<sizeof($arr);$i++)
					{
					
				  ?>
					<tr>
					<td>
					<input type="checkbox" name="p[c][<?php echo $arr[$i];?>]" value="<?php echo $arr[$i];?>" class="required" <?php
					if(in_array($arr[$i],$check_val)){
							?> checked="true"
					<?php
						}
					?>>
					<?php echo $custom['p'][$arr[$i]];?>	
					</td>
					<td></td>
					</tr>		

				<?php
					}					
				}
				?>
	</table>

		</fieldset>
		<fieldset id="priseries">
			<legend><?php echo $this->lang->line('level_custom_group');?></legend>
			<table>
				<?php
				
				if(isset($system['g'])){
					$arrs=array_keys($system['g']);
					for($i=0;$i<sizeof($arrs);$i++)
					{
					 ?>
					<tr>
					<td>
					<input type="checkbox" name="g[s][<?php echo $arrs[$i];?>]" value="<?=isset($system['g'][$arrs[$i]])?$system['g'][$arrs[$i]]:$this->lang->line('g_'.$arrs[$i])?>" class="required"  <?php
					if(in_array($arrs[$i],$check_val)){
							?> checked="true"
					<?php
						}
					?>>
					<?=isset($system['g'][$arrs[$i]])?$system['g'][$arrs[$i]]:$this->lang->line('g_'.$arrs[$i])?>
					</td>
					<td></td>
					</tr>		

				<?php	
						
					}		
										
				}
				?>
				<?php
				if(isset($custom['g'])){
					$arr=array_keys($custom['g']);
					for($i=0;$i<sizeof($arr);$i++)
					{
				  ?>
					<tr>
					<td>
					<input type="checkbox" name="g[c][<?php echo $arr[$i];?>]" value="<?php echo $arr[$i];?>"  <?php
					if(in_array($arr[$i],$check_val)){
							?> checked="true"
					<?php
						}
					?>>
					<?php echo $custom['g'][$arr[$i]];?>	
					</td>
					<td></td>
					</tr>		

				<?php
					}					
				}
				?>
				
			</table>

		</fieldset>
		<fieldset id="priseries">
			<legend><?php echo $this->lang->line('level_custom_Employee');?></legend>
			<table>
				<?php
				
				if(isset($system['e'])){
					$arrs=array_keys($system['e']);
					for($i=0;$i<sizeof($arrs);$i++)
					{
					 ?>
					<tr>
					<td>
					<input type="checkbox" name="e[s][<?php echo $arrs[$i];?>]" value="<?=isset($system['e'][$arrs[$i]])?$system['e'][$arrs[$i]]:$this->lang->line('e_'.$arrs[$i])?>" class="required" <?php
					if(in_array($arrs[$i],$check_val)){
							?> checked="true"
					<?php
						}
					?>>
					<?=isset($system['e'][$arrs[$i]])?$system['e'][$arrs[$i]]:$this->lang->line('e_'.$arrs[$i])?>
					</td>
					<td></td>
					</tr>		

				<?php	 
					}		
										
				}
				?>
				<?php
				if(isset($custom['e'])){
					$arr=array_keys($custom['e']);
					for($i=0;$i<sizeof($arr);$i++)
					{
				  ?>
					<tr>
					<td>
					<input type="checkbox" name="e[c][<?php echo $arr[$i];?>]" value="<?php echo $arr[$i];?>" class="required"
					<?php
					if(in_array($arr[$i],$check_val)){
							?> checked="true"
					<?php
						}
					?>
					>
					<?php echo $custom['e'][$arr[$i]];?>	
					</td>
					<td></td>
					</tr>		

				<?php
					}					
				}
				?>
				
			</table>

		</fieldset>
		<table>	
			<tr>
				<td>
				<?php echo form_submit('Updatefield', 'Updatefield');?>
				</td>
			</tr>		
			</table>
		<?php echo form_close();?>
		</div>	
</div>