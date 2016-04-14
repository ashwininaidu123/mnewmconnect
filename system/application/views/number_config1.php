
 <div id="main-content">
            <div class="page-title"><i class="icon-custom-left"></i>
                <h3><?php echo "Number Config";?> </h3>
            </div>
           <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
							<div class="row" >
					           <div class="col-md-12 col-sm-12 col-xs-12">
								   
		<?php
			$attributes = array('class' => 'form', 'id' =>'landingnumbers','name'=>'landingnumbers');		
			 echo form_open($action.$selectedlist->number,$attributes);
		?>
				
				<div class="form-group">
						<label class="col-sm-4 text-right">Busniess User :</label>
						<div class="col-sm-6 input-icon right">
							<select name="businessuser" id="businessuser" class="form-control">
								<option value="">----Select----</option>
								
								<?php for($i=0;$i<sizeof($businessUsers[$i]['autoreset']);$i++){ ?>
									<option  value="<?=$businessUsers[$i]['bid']?>" <?=(($businessUsers[$i]['bid']==$selectedlist->bid)?"selected":'')?>><?=$businessUsers[$i]['businessname']?></option>
								<?php } ?>
							</select>
							<input type="hidden" class="form-control" name="actualuser" id="actualuser" value="<?php echo $selectedlist->bid;?>"/>
							<input type="hidden" class="form-control" name="mid" id="mid" value="<?php echo $mid;?>"/>
						</div>
					</div>
				
					<?php if($show!=0){ ?>
					
							<div class="form-group"><label class="col-sm-4 text-right">Packages :</label>
							<div class="col-sm-6 input-icon right">
						<select name="package" id="package" class="form-control">
								<?php
									for($i=0;$i<sizeof($packages);$i++)
									{
										?>
										<option  value="<?=$packages[$i]['package_id']?>" <?=(($packages[$i]['package_id']==$selectedlist->package_id)?'selected':'')?>><?=$packages[$i]['packagename']?></option>
										
										<?php
									}
								?>
						</select>	
							</div>
					</div>
				
						<div class="form-group">
						<label class="col-sm-4 text-right">Module :</label>
						<div class="col-sm-6 input-icon right">
							<select name="module" id="module" class="form-control" >
								<?php
									for($i=0;$i<sizeof($modules);$i++)
									{
										?>
										<option value="<?=$modules[$i]['module_id']?>" <?=(($modules[$i]['module_id']==$landing_details)?"selected":'')?>><?=$modules[$i]['module_name']?></option>
										<?php
									}
								?>
							</select>
					</div>
					</div>
					
					<div class="form-group">
					 <label class="col-sm-4 text-right">Addons :</label>
					  <div class="col-sm-6 input-icon right">
									<?php
										$adaray=array();
										foreach($baddons as $badons){
											$adaray[]=$badons['feature_id'];
										}
										foreach($alldons as $bads){
											$checked=(in_array($bads['feature_id'],$adaray))?'checked':'';
											?><input type='checkbox' name='addons[]' id='addons[]'  value="<?=$bads['feature_id']?>"<?=$checked?>/><?=$this->mastermodel->feature_name($bads['feature_id'])?><br/>
										<?
										}
									?>
						</div>
					</div>
					
					<div   id="rental" class="form-group">
						<label class="col-sm-4 text-right">Rental :</label>
						<div class="col-sm-6 input-icon right">
						<input type='text' name='prental' class="form-control" id='prental' value='<?=$selectedlist->rental?>'/>
						</div>
					</div>
				
					<div  id="fmins" class="form-group">
						<label class="col-sm-4 text-right">Free Mins :</label>
						<div class="col-sm-6 input-icon right">
						<input type='text' class="form-control" name='pfmins' id='pfmins' value='<?=$selectedlist->flimit?>'/>
							</div>
					</div>
					
					<div   id="climit" class="form-group">
						<label class="col-sm-4 text-right">Credit Limit :</label>
						<div class="col-sm-6 input-icon right">
						<input type='text' class="form-control" name='pclimit' id='pclimit' value='<?=$selectedlist->climit?>'/>
							</div>
					</div>
					
					<div  id="rate" class="form-group">
						<label class="col-sm-4 text-right">Rate:</label>
						<div class="col-sm-6 input-icon right">
						<input type='text' name='prate' id='prate' value='<?=$selectedlist->rpi?>'  class="form-control"/>
							</div>
					</div>
				
					<div class="form-group">
						<label class="col-sm-4 text-right">Payment Term:</label>
						<div class="col-sm-6 input-icon right">
						<input type="text" name="pterm" value="<?=$selectedlist->payment_term?>" id="pterm" class="form-control">
							</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 text-right">SMS Limit:</label>
						<div class="col-sm-6 input-icon right">
						<input type="text" name="slimit" value="<?=$selectedlist->sms_limit?>" id="slimit" class="form-control">
							</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 text-right">Parallel Calls Limit:</label>
						<div class="col-sm-6 input-icon right">
						<input type="text" name="plimit" value="<?=$selectedlist->parallel_limit?>" id="plimit" class="form-control">
							</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 text-right">Service Activation Date :</label>
						<div class="col-sm-6 input-icon right">
						<input type="text" name="svdate" id="svdate" class="form-control datepicker" value="<?=$selectedlist->svdate?>">
						</div>
					</div>
					<?php } ?>
					<div class="form-group">
						<label class="col-sm-4 text-right">Packages :</label>
						<div class="col-sm-6 input-icon right">
							<select name="package" id="package" class="form-control">
								<option value="">----Select----</option>
								<?php
									for($i=0;$i<sizeof($packages);$i++)
									{
										?>
										<option value="<?=$packages[$i]['package_id']?>" <?=(($packages[$i]['package_id']==$selectedlist->package_id)?"selected":'')?>><?=$packages[$i]['packagename']?></option>
										<?php
									}
								?>
							</select>	
								</div>
					</div>
					<?	//} ?>
					<div class="form-group">
						<label class="col-sm-4 text-right">Landing Number :</label>
					    <div class="col-sm-6 input-icon right">
							<input type="text" name="landingnumber" id="landingnumber" value="<?=$selectedlist->landingnumber?>" class="form-control" />	
						    <div class="errors"></div>
						</div>
					</div>
					<div class="form-group">
				      	<label class="col-sm-4 text-right">PriNumber :</label>
					     <div class="col-sm-6 input-icon right">
							<?php
							$prlist=array($selectedlist->pri=>$selectedlist->pri)+$prlist;	
							echo form_dropdown('pri',$prlist,$selectedlist->pri," 'id'='pri' class=form-control"); 	
							?>
						<input type="hidden"  class="form-control"  name="Opri" id="Opri" value="<?=$selectedlist->pri?>" />
							</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 text-right">Auto Reset :</label>
						<div class="col-sm-6 input-icon right">
						<input type="checkbox" name="autoreset" value="1" <?php if($selectedlist->autoreset == 1 ) echo  'checked';?> id="autoreset" />
							</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 text-right">Support:</label>
						<div class="col-sm-6 input-icon right">
							<th><input type="checkbox" name="support" value="1"  <?php if($selectedlist->support == 1 ) echo  'checked';?> id="support"></th>	
							</div>
					</div>
					<?php if($show==0) { ?>
					<div class="form-group">
						<label class="col-sm-4 text-right">Note :</label>
						<div class="col-sm-6 input-icon right">
						<textarea class="form-control" name="cnote" id="cnote" ></textarea>	
							</div>
					 </div>
						
					<?php } ?>
				 <div class="form-group col-sm-12 text-center">
				   <input id="button1" type="submit" class="btn btn-primary" name="update_system" value="<?=$this->lang->line('submit') ?>" /> 
                   <input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
              </div>
		<?php echo form_close();?>
</div>
