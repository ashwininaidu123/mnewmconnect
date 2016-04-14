<div id="box">
		<h3><?php echo $this->lang->line('level_subkeyword');?></h3>
		<table width="100%">
		   <thead>
		      <tr>
				 <th><a href="#"><?php echo $this->lang->line('level_subkeyword');?></a></th>
				 <th><a href="#"><?php echo $this->lang->line('level_shortcode');?></a></th>	
				 <th><a href="#"><?php echo $this->lang->line('level_custolevel');?></a></th>
				 <th><a href="#"><?php echo $this->lang->line('level_replymsg');?></a></th>	
		    </tr>
		     </thead>
		    <?php
					if(sizeof($subkeywordlist)>0){
					for($i=0;$i<sizeof($subkeywordlist);$i++)
					{
				?>
				<tr>
					<td align="center"><?php echo $subkeywordlist[$i]['subkeyword'];?></td>
					<td align="center"><?php echo $subkeywordlist[$i]['code'];?></td>
					<td align="center"><?php echo $subkeywordlist[$i]['customvalue'];?></td>
						<td align="center"><?php echo $subkeywordlist[$i]['replymsg'];?></td>
			   </tr>
				<?php }}else{?>
				<tr>
					<td colspan="3" align="center"><?php echo $this->lang->line('error_norecords');?></td>
				
				</tr>
				<?php } ?>
		   </table>

</div>
