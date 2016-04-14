	<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
			<h4 class="modal-title" id="myModalLabel"><?php echo "Support Ticket Status" ;?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">	
		<?php
		$url=base_url()."customfield/edit_tkt_status/".$id."/".$modid;
		?>
		<form action="<?=$url;?>" id="tktstatforms" name="tktstatforms" role="form" parsley-validate class="form-horizontal form icon-validation" method ="post" >
			<table width="100%" id="suptktTable" align="center" class="table table-striped table-hover">
				<?php
				for($i=0;$i<count($fields);$i++){
					$j = $i+1;
				?>
				<tr>
					<th><label name="syslabel_<?php echo $i;?>" id="syslabel_<?php echo $j;?>"><?php echo $fields[$i]['syslabel'];?> : </label></th>
					<td><input type="text" name="stat_<?php echo $j;?>" id="stat_<?php echo $j;?>" class="required" style="width:200px;" value="<?php echo $fields[$i]['status'];?>"/>
					</td>
					<td><input type="checkbox" name="sms_<?php echo $j;?>" title="Please Select to send the SMS to Customer" value="1"   <?php if($fields[$i]['sms'] == 1) echo 'checked';?> /></td>
					<td><textarea name="smscontent_<?php echo $j;?>" id="smscontent_<?php echo $j;?>" style="width:200px;height:40px;"><?php echo $fields[$i]['smscontent'];?></textarea><br/><a title='Click to insert Support Ticket Number' class='addVar' rel='@ticketid@'>Ticket Number</a></td>
				</tr>
				<?php 
					}
				?>
				<input type='hidden' name='statcnt' id="statcnt" value='<?php echo $i;?>' />
				<tr>
					<th></th>
					<td><a href="javascript:void(0)" id="addmores">Add More</a></td>
					<td></td>
				</tr>
				</table>
				<input type='hidden'name='hidvals' id="hidvals" value="<?php echo $id.'~'.$modid;?>"/>
			<table><tr><td><center>
			<input id="UpdateSuptktStatus" type="submit" name='UpdateSuptktStatus' class="btn btn-primary" value="Submit" />
</center></td></tr></table>	
		<?php echo form_close();?>
		</div>
				</div>
			</div>
		</div>
	</div>
