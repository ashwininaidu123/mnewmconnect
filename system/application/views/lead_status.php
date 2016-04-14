	<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
			<h4 class="modal-title" id="myModalLabel"><?php echo "Lead Types" ;?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">	
		<?php
		$url=base_url()."customfield/edit_lead_status/".$id."/".$modid;
		?>
		<form action="<?=$url;?>" id="leadstatforms" name="leadstatforms" role="form" parsley-validate class="form-horizontal form icon-validation" method ="post" >	
			<table width="100%" id="leadstTable" align="center" class="table table-striped table-hover">
				<?php $i = 1;foreach($fields as $k=>$v){ ?>
				<tr>
					<th><label><?php echo $label_name[$k];?> : </label></th>
					<td><input type="text" name="stat_<?php echo $k;?>" id="stat_<?php echo $k;?>" class="required" value="<?php echo $v;?>"/>
					</td>
					<td><div id="err1" style="color:red"></div></td>
				</tr>
				<?php 
					$i = $k;
					}
				?>
				<input type='hidden' name='statcnt' value='<?php echo $i;?>' />
				<!--<tr>
					<th></th>
					<td><a href="javascript:void(0)" id="moreclick">Add More</a></td>
					<td></td>
				</tr>-->
				</table>
				<input type='hidden'name='hidvals' id="hidvals" value="<?php echo $id.'~'.$modid;?>"/>
			<table><tr><td><center>
				<input id="Update_leadStatus" type="submit" name='Update_leadStatus' class="btn btn-primary" value="Submit" />
			</center></td></tr></table>
			<?php echo form_close();?>
			</div>
				</div>
			</div>
		</div>
	</div>
