<div id="box">
		<h3><?php echo $this->lang->line('level_managekeyword');?></h3>
		<table width="100%">
		   <thead>
		      <tr>
				 <th><a href="#"><?php echo $this->lang->line('level_keyword');?></a></th>
				 <th><a href="#"><?php echo $this->lang->line('level_shortcode');?></a></th>	
				 <th><a href="#"><?php echo $this->lang->line('level_defaultmsg');?></a></th>
				 <th width="75"><a href="#"><?php echo $this->lang->line('level_Action');?></a></th>	
		    </tr>
		     </thead>
		    <?php
					for($i=0;$i<sizeof($keywordlist);$i++)
					{
				?>
				<tr>
					<td align="center"><?php echo $keywordlist[$i]['keyword'];?></td>
					<td align="center"><?php echo $keywordlist[$i]['code'];?></td>
					<td align="center"><?php echo $keywordlist[$i]['default_msg'];?></td>
					<td align="center">
							<a href="<?php echo site_url('keyword/showsubkeywords/'.$keywordlist[$i]['keyword_id']);?>" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><img src="<?php echo site_url('system/application/img/icons/list-go.png');?>" title="Edit" width="16" height="16" /></a>
							<a href="<?php echo site_url('keyword/editkeyword/'.$keywordlist[$i]['keyword_id']);?>" ><span title="Edit" class="fa fa-edit"></span></a>
							<a href="<?php echo site_url('keyword/addsubkeyword/'.$keywordlist[$i]['keyword_id']);?>" class="btn-danger" data-toggle="modal" data-target="#modal-responsive" ><span title="Add" class="glyphicon glyphicon-plus-sign"></span></a>
					</td>
			   </tr>
				<?php }?>
		   </table>

</div>

