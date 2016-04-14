<script src="system/application/plugins/jquery-ui/jquery-ui-1.10.4.min.js"></script>
<script src="system/application/js/ui/jquery-ui-timepicker-addon.js"></script>
<script src="system/application/js/application.js"></script>
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
		       <section class="content-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h1 class="modal-title" id="myModalLabel">Search</h1>
		     </section>

			  <section class="content">
                <div class="row">
						<div class="col-md-12">
							<?php if(!isset($nosearch)){?>
					<div class="row">
						<!-- Search Start -->
								    <div class="box-body">
							<?=$form['open']?>
							<div  id="nor_search" class="col-md-12 col-sm-12 col-xs-12">
								<?php foreach($form['form_field'] as $field1){ ?>
								<div class="form-group">
									<?=$field1['label']?>
									<div class="col-sm-6 input-icon right"> 
									<?=$field1['field']?>
									</div>
								</div>
								<? }?>
								<div class="form-group col-sm-12 text-center" >
									<input id="button1" class="btn btn-primary" type="submit" name="submit" value="<?=$this->lang->line('level_search')?>" />
								</div>
							</div>
							<?=$form['close']?>
						</div>
						<!-- Search End -->

					</div>
					<? }?>
						</div>
					</div>
			</div>
			<? if(isset($form['submit'])){ ?>	
			<div class="modal-footer text-center">
				<button type="submit" class="btn btn-primary" id="button1" name="update_system" data-dismiss="modal">Submit</button>
				<button type="reset" class="btn btn-default" id="button2" data-dismiss="modal">Reset</button>
			</div>
			<? } ?>
		</div>
	</section>
	
