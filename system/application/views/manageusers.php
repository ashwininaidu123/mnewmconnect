 	<script>
$(function() {
	$('.DeleteItem').click(function(){
		if(confirm("<?php echo $this->lang->line('level_groupdelete_msg');?>")){
			var url="<?php echo base_url();?>"+"user/delete_user/"+this.id
			$.get(url, function(data){
				window.parent.location.href = window.parent.location.href;
			});	
		}
	});
	
});
</script>
<div id="box">
		<h3><?php echo $this->lang->line('level_Manage_user');?></h3>
		<table width="100%">
		   <thead>
		      <tr>
				 <th><a href="#"><?php echo $this->lang->line('level_userid');?></a></th>
				 <th><a href="#"><?php echo $this->lang->line('level_Username');?></a></th>	
				 <th><a href="#"><?php echo $this->lang->line('level_usertype');?></a></th>
				 <th><a href="#"><?php echo $this->lang->line('level_Action');?></a></th>	
		    </tr>
			<?php
				for($i=0;$i<sizeof($users);$i++)
				{
				?>
					<tr>
						<td align="center"><?php echo $users[$i]['uid'];?></td>
						<td align="center"><?php echo $users[$i]['username'];?></td>
						<td align="center"><?php echo $users[$i]['typename'];?></td>
						<td align="center">
							<!--<a href="<?php echo site_url('user/edit/'.$users[$i]['uid']);?>"><span title="Edit" class="fa fa-edit"></span></a>-->
						  <span class="DeleteItem" id="<?php echo $users[$i]['uid'];?>" title="Delete" class="glyphicon glyphicon-trash"></span>
							<a href="<?php echo site_url('user/status_change/'.$users[$i]['uid']);?>">
								<?php if($users[$i]['status']=="1"){
									?>
									 <span class="fa fa-unlock ChangeStatus"  id="<?php echo $users[$i]['uid'];?>" title="Disable"></span>
						
									<?php
									}
									else{
									?>
									<span class="fa fa-lock ChangeStatus"  id="<?php echo $users[$i]['uid'];?>" title="Enable"></span>
									<?php } ?>
										</a>
						</td>


					</tr>
				<?php
				}
			?>
		 </thead>
		</table>

</div>
