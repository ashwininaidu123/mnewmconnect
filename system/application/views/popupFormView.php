<script src="/system/application/js/ui/jquery-ui-1.8.9.custom.js"></script>
<script src="/system/application/js/ui/jquery.ui.datepicker.js"></script>
<script src="/system/application/js/ui/jquery-ui-timepicker-addon.js"></script>
<script src="system/application/js/application.js"></script>
<script>
$(function() {
	$( ".datetimepicker" ).datetimepicker( {dateFormat: 'yy-mm-dd' } );
});
</script>
<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title" id="myModalLabel"><?php echo $module['title'];?></h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<?php
					if(isset($followups)){
				  ?>
				<table class="table table-striped table-hover">
					<thead class="no-bd">
						<tr>
							<?php foreach($followups['itemlist']['header'] as $hd){ ?>
									<th><?=$hd?></th>
							<?php }?>
						</tr>
					</thead>
					<tbody class="no-bd-y">
						<?php
						$i=0;
						foreach($followups['itemlist']['rec'] as $item){ ?>
							<tr class="<?=($i%2==0)?'':'row2'?>">
							<? foreach($item as $it){?><td><?=$it?></td><? }?>
							</tr>
						<? $i++;}?>
					</tbody>
				</table>
				<?php
				}
				?>
				<?php
					if(isset($comments)){
				  ?>
				<table class="table table-striped table-hover">
					<thead class="no-bd">
						<tr>
							<?php foreach($comments['itemlist']['header'] as $hd){ ?>
									<th><?=$hd?></th>
							<?php }?>
						</tr>
					</thead>
					<tbody class="no-bd-y">
						<?php
						$i=0;
						foreach($comments['itemlist']['rec'] as $item){ ?>
							<tr class="<?=($i%2==0)?'':'row2'?>">
							<? foreach($item as $it){?><td><?=$it?></td><? }?>
							</tr>
						<? $i++;}?>
					</tbody>
				</table>
				<?php
				}
				?>
				<?php
					if(isset($remarks)){
				  ?>
				<table class="table table-striped table-hover">
					<thead class="no-bd">
						<tr>
							<?php foreach($remarks['itemlist']['header'] as $hd){ ?>
									<th><?=$hd?></th>
							<?php }?>
						</tr>
					</thead>
					<tbody class="no-bd-y">
						<?php
						$i=0;
						foreach($remarks['itemlist']['rec'] as $item){ ?>
							<tr class="<?=($i%2==0)?'':'row2'?>">
							<? foreach($item as $it){?><td><?=$it?></td><? }?>
							</tr>
						<? $i++;}?>
					</tbody>
				</table>
				<?php
				}
				?>
					<form action="<?=base_url()?><?php echo $form['form_attr']['action'];?>" id="<?php echo $form['form_attr']['name'];?>" name="<?php echo $form['form_attr']['name'];?>" role="form" parsley-validate class="form-horizontal form icon-validation" method ="post" >
						<? if(isset($form['hidden'])){ ?>	
						<?php foreach($form['hidden'] as $key => $value){ ?>
						<input type="hidden" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $value; ?>">
						  <? }?>
						  <? }?>
						<?if(isset($form['fields']))
							foreach($form['fields'] as $field){ ?>
							<div class="form-group ">
								<?=isset($field['label'])?$field['label']:''?>
								 <div class="col-sm-6 input-icon">
								   <?=isset($field['field'])?$field['field']:''?>
								</div>
							</div>
							<? }?>
						  <? if(!isset($form['submit'])){ ?>	
							<div class="form-group text-center">
								<input id="button1" type="submit" onclick="javascript:$('#<?php echo $form['form_attr']['name'];?>').parsley('validate')" class="btn btn-primary" name="update_system" value="<?=$this->lang->line('submit') ?>" /> 
								<input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
								<br/><br/><span style="color:#CC0000">Note: Followup Mails will be for future Dates and  Mail will be reach starting of the day with all followups together.</span>
							</div>
							<? } ?>
							
						</form>	
					</div>
				</div>
			</div>
		</div>
	</div>
