	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title" id="myModalLabel"><?php echo $module['title'];?></h4>
			</div>
			<div class="modal-body">
				<table class="table table-striped table-hover">
					<thead class="no-bd">
						<tr>
							<? foreach($itemlist['header'] as $hd){ ?>
									<th><?=$hd?></th>
							<? }?>
						</tr>
					</thead>
					<tbody class="no-bd-y">
						<?php
							$i=0;
							foreach($itemlist['rec'] as $item){ ?>
							<tr class="<?=($i%2==0)?'':'row2'?>">
							<? foreach($item as $it){?><td><?=$it?></td><? }?>
							</tr>
						<? $i++;}?>
					</tbody>
				</table>
			</div>
			<? if(isset($form['submit'])){ ?>	
			<div class="modal-footer text-center">
				<button type="submit" class="btn btn-primary" id="button1" name="update_system" data-dismiss="modal">Submit</button>
				<button type="reset" class="btn btn-default" id="button2" data-dismiss="modal">Reset</button>
			</div>
			<? } ?>
		</div>
	</div>
	
