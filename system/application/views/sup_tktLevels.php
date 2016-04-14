	<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
			<h4 class="modal-title" id="myModalLabel"><?php echo "Support Ticket Levels" ;?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">	
		<?php
		$url=base_url()."customfield/edit_ticket_level/".$id."/".$modid;
		?>
		<form action="<?=$url;?>" id="supportforms" name="supportforms" role="form" parsley-validate class="form-horizontal form icon-validation" method ="post" >
		<table id="suptktLeveltab" class="table table-striped table-hover">
				<tr>
					<td colspan="3"><div id="err" style="color:red"></div></td>
				</tr>
				<?php for($i=0;$i<count($fields);$i++){
					$j = $i+1; ?>
				<tr>
					<th><label name="label_<?php echo $j;?>" id="label_<?php echo $j;?>"><?php echo $fields[$i]['syslabel']?> : </label></th>
					<td><input type="text" title ="Ticket Levels" name="level_<?php echo $j;?>" id="level_<?php echo $j;?>" class="required" style="width:100px!important" value="<?php echo $fields[$i]['level'];?>"/></td>
					<?php if(isset($escalation)){?>
					<td><input type="text" title="Escalation Time in Hours" name="time_<?php echo $j;?>" id="time_<?php echo $j;?>" style="width:100px!important" class="required" value="<?php echo $times[$fields[$i]['id']];?>"/></td>
					<?php } ?>
					<td><div id="err1" style="color:red"></div></td>
				</tr>
				<?php 
					}
				?>
				<input type='hidden' name='statcnt' id="statcnt" value='<?php echo $i;?>' />
				<tr>
					<th></th>
					<td><a href="javascript:void(0)" id="addmorelevel">Add More</a></td>
					<td></td>
				</tr>
				</table>
				<input type='hidden'name='hidvals' id="hidvals" value="<?php echo $id.'~'.$modid;?>"/>
			<table><tr><td><center>
			<input id="Update_supportLevel" type="submit" name='Update_supportLevel' class="btn btn-primary" value="Submit" />
</center></td></tr></table>
		<?php echo form_close();?>
					</div>
				</div>
			</div>
		</div>
	</div>
