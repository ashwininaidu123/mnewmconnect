	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title" id="myModalLabel"><?php echo $module['title'];?></h4>
			</div>
			<div class="modal-body">
				<?php foreach($form['fields'] as $field){?>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group" style="height:auto;">
								<?php echo $field['label']?>
								 <div class="col-sm-6 input-icon right" >            
						        <?php echo $field['field']?>
							     </div>
							</div>
						</div>
					</div>
				  <?php } ?>
				  <?php
					if(isset($followups)){
				  ?>
				  <h4><?php echo $followups['module']['title'];?></h4>
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
				    <h4><?php echo $comments['module']['title'];?></h4>
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
				
			</div>
			<? if(isset($form['submit'])){ ?>	
			<div class="modal-footer text-center">
				<button type="submit" class="btn btn-primary" id="button1" name="update_system" data-dismiss="modal">Submit</button>
				<button type="reset" class="btn btn-default" id="button2" data-dismiss="modal">Reset</button>
			</div>
			<? } ?>
		</div>
	</div>
	
