<div id="main-content">
	<div class="page-title"> <i class="icon-custom-left"></i>
		<h4><?php echo $this->lang->line('manage_role');?></h4>
	</div>
	<!-- BEGIN ERROR BOX -->
	<?php 
	if($this->session->flashdata('msgt')){ $error1 = $this->session->flashdata('msgt'); }
	$error = validation_errors();
	if((isset($error) &&$error != '') || isset($error1)){
		$display = '';
	}else{
		$display = 'hide';
	}
	?>
	<div class="alert <?=($this->session->flashdata('msgt'))?$this->session->flashdata('msgt'):'error'?> <?=$display;?>" >
		<button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
		<?php echo validation_errors(); ?>
		<?php echo $this->session->flashdata('msg');?>
	</div>
	<!-- END ERROR BOX -->
	<div class="row">
		<div class="col-md-12">
			<div class="panel-body">
				<div class="row">

					<div class="col-md-12 col-sm-12 col-xs-12">
						<table class='table table-striped'>
							<thead>
						  <tr>
							 <th><a href="#"><?php echo $this->lang->line('level_sno');?></a></th>
							 <th><a href="#"><?php echo $this->lang->line('level_role_name');?></a></th>	
							 <th><a href="#"><?php echo $this->lang->line('level_thrreshold');?></a></th>
							 <th><a href="#"><?php echo "Is Admin";?></a></th>
							 <th><a href="#"><?php echo $this->lang->line('level_Action');?></a></th>	
						</tr>
						 </thead>
						 <?php
							for($i=0;$i<sizeof($rolelist);$i++){
						 ?>
							<tr>
								<td><?=$rolelist[$i]['roleid']?></td>
								<td><?=$rolelist[$i]['rolename']?></td>
								<td><?=$rolelist[$i]['recordlimit']?></td>
								<td><? echo ($rolelist[$i]['admin']!=1)?"No":"Yes";?></td>
								<td><a href="AddRole/<?=$rolelist[$i]['roleid']?>"><span title="Edit" class="fa fa-edit"></span></td>
							</tr>
						 <?php
							}
						 ?>
						</table>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>
