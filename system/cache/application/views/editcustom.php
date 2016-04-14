<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
			<h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('level_edit_custom');?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">	
		<?php
		$url=base_url()."customfield/edit_custom/".$id."/".$modid;
		?>
		<form action="<?=$url;?>" id="customfieldforms" name="customfieldforms" role="form" parsley-validate class="form-horizontal form icon-validation" method ="post" >
		<table class="table table-striped table-hover">
				<tr>
					<th><label class="col-sm-6 text-right" ><?php echo $this->lang->line('level_labelname');?> : </label></th>
					<td><input type="text" name="label_name" id="label_name" class="form-control required" value="<?php echo (isset($fiedls[0]['customlabel']))?$fiedls[0]['customlabel']:'';?>"/></td>
					<td><div id="err1" style="color:red"></div></td>
				</tr>
				<?php
					if($fiedls[0]['fieldtype']!='text' && $fiedls[0]['fieldtype']!='textarea' && $fiedls[0]['fieldtype']!='datetime'){
				?>
					<input type='hidden' name='ftype' value='1'/>
				<tr>
					<th><label class="col-sm-6 text-right" ><?php echo "Field Type";?> : </label></th>
					<td><?php echo (isset($fiedls[0]['fieldtype']))?$fiedls[0]['fieldtype']:'';?></td>
					<td><div id="err1" style="color:red"></div></td>
				</tr>
				<tr>
					<th></th>
					<td><a href="javascript:void(0)" id="moreclick">Add More</a></td>
					<td></td>
					
				</tr>
		</table>
		<table width="100%" id='FromTab1' class="table table-striped table-hover">
				<?php
					$arrs=explode("\n",$fiedls[0]['options']);
					$i=0;
					foreach($arrs as $ar=>$ars){
						$i=$ar+1;
						$newars=explode('|',$ars);
						$opt=(isset($newars[1]))?$newars[0]:$ars;
						$action=(isset($newars[1]))?$newars[1]:'';
						?>
						<tr>
							<th><label class="col-sm-6 text-right" ><?php echo "option".$i;?> : </label><br/></th>
							<td style="width:100px;"><input type='text' class="form-control" readonly='true' name='option[]' value='<?=$opt?>' |</td>
							<td><input name='action[]' placeholder='Action' value="<?php echo $action;?>" class="url form-control" /><br/><div id="err1" style="color:red">
							</td>
						</tr>
						<?php
						$i++;
					}
				 } ?>
			</table>
			<input type='hidden'name='hidvals' id="hidvals" value="<?php echo $id.'~'.$modid;?>"/>
			<table><tr><td><center>
				<input id="Update_CustomField" type="submit" name='Update_CustomField' class="btn btn-primary" value="Submit" />
			</center></td></tr></table>
			<?php echo form_close();?>
					</div>
				</div>
			</div>
		</div>
	</div>
