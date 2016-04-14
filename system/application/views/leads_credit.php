 <div id="main-content">
            <div class="page-title"><i class="icon-custom-left"></i>
                <h3>Lead Versions</h3>
            </div>
           <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
							<div class="row" >
					           <div class="col-md-12 col-sm-12 col-xs-12">
		<?php
			$attributes = array('class' => 'form', 'id' =>'leadsversion','name'=>'leadsversion');		
			 echo form_open($action,$attributes);
		?>
				<TABLE>
					<tr>
						<div class="form-group"><label  class="col-sm-4 text-right">Busniess User :</label>
						<div class="col-sm-6 input-icon right">
							<select name="businessuser" id="businessuser" class="required form-control">
								<option value="">----Select----</option>
								<?php
									for($i=0;$i<sizeof($businessUsers);$i++){
										?>
										<option value="<?=$businessUsers[$i]['bid']?>" <?php if($businessUsers[$i]['bid'] == $busdetails['bid']) echo "selected = selected";?>><?=$businessUsers[$i]['businessname']?></option>
										<?php
									}
								?>
							</select>
						 </div>
					</div>
					</tr>
					<tr>
						<div class="form-group"><label  class="col-sm-4 text-right">Lead Version:</label>
						<div class="col-sm-6 input-icon right">
							<select name="leadtype" id="leadtype" class="required form-control">
								<option value="">----Select----</option>
								<?php
									for($i=0;$i<sizeof($leadtype);$i++){
										?>
										<option value="<?=$leadtype[$i]['id']?>" <?php if($leadtype[$i]['id'] == $busdetails['type']) echo "selected = selected";?>><?=$leadtype[$i]['type']?></option>
										<?php
									}
								?>
							</select>
					 </div>
					</div>	
					</tr>
					<tr>
						<div class="form-group"><label  class="col-sm-4 text-right">Lead View: </label>
						<div class="col-sm-6 input-icon right">
							<select name="leaddesign" id="leaddesign" class="required form-control">
								<option value="">----Select----</option>
								<?php
									for($i=0;$i<sizeof($leaddesign);$i++){
										?>
										<option value="<?=$leaddesign[$i]['id']?>" <?php if($leaddesign[$i]['id'] == $busdetails['design']) echo "selected = selected";?>><?=$leaddesign[$i]['design']?></option>
										<?php
									}
								?>
							</select>
						 </div>
					</div>	
					</tr>
				
				</TABLE>
              <div class="form-group col-sm-12 text-center">
				   <input id="button1" type="submit" class="btn btn-primary" name="update_system" value="<?=$this->lang->line('submit') ?>" /> 
                   <input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
              </div>
		<?php echo form_close();?>
</div>

