<div id="main-content">
	<div class="row">
        <div class="col-md-12">
             <div class="panel panel-default">
                 <div class="panel-body">
					<div class="row">
						<div class="col-md-12">
							<div style="width:100%;margin-bottom:10px;">
								<div style="padding:20px;">
									<label><?php echo $this->lang->line('level_model');?> :</label>
									<select id="modulename" name="modulename" class="form-control required">
										<?php for($i=0;$i<sizeof($module_names);$i++){ ?>
										<option value="<?php echo $module_names[$i]['modid'];?>"><?php echo $module_names[$i]['moddesc'];?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div id="modulePage"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
