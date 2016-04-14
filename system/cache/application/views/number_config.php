<div id="main-content">
            <div class="page-title"><i class="icon-custom-left"></i>
                <h3>Number Config</h3>
            </div>
           <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
							<div class="row" >
					           <div class="col-md-12 col-sm-12 col-xs-12">

		<?php
			$attributes = array('class' => 'form', 'id' =>'landingnumberFrm','name'=>'landingnumberFrm');		
			 echo form_open($action,$attributes);
		?>
	
						<div class="form-group"><label  class="col-sm-4 text-right">Busniess User :</label>
				<div class="col-sm-6 input-icon right">
							<select name="businessuser" id="businessuser" class="auto form-control">
								<option value="">----Select----</option>
								<?php
									for($i=0;$i<sizeof($businessUsers);$i++)
									{
										?>
										<option value="<?=$businessUsers[$i]['bid']?>"><?=$businessUsers[$i]['businessname']?></option>
										<?php
									}
								?>
							</select>
									 </div>
					</div>
				
						<div class="form-group"><label  class="col-sm-4 text-right">Packages :</label>
					<div class="col-sm-6 input-icon right">
							<select name="package" id="package" class="required form-control">
								<option value="">----Select----</option>
								<?php
									for($i=0;$i<sizeof($packages);$i++)
									{
										?>
										<option value="<?=$packages[$i]['package_id']?>"><?=$packages[$i]['packagename']?></option>
										<?php
									}
								?>
							</select>
										 </div>
					</div>
				
						<div class="form-group"><label  class="col-sm-4 text-right">Module :</label>
	<div class="col-sm-6 input-icon right">
							<select name="module" id="module" class="required form-control">
									<option value="">--select---</option>
							</select>
							 </div>
					</div>
					
						<div id="addonhide" class="form-group"><label  class="col-sm-4 text-right">Addons :</label>
						<div class="col-sm-6 input-icon right" id="addul">
					
									 </div>
					</div>
						<div id="rental" class="form-group"><label  class="col-sm-4 text-right">Rental :</label>
						<div class="col-sm-6 input-icon right">
					<input type='text' name='prental' id='prental' class='form-control' value=''/>
							 </div>
					</div>
						<div id="fmins" class="form-group"><label  class="col-sm-4 text-right">Free Mins :</label>
						<div class="col-sm-6 input-icon right">
					    <input type='text' name='pfmins' id='pfmins' class='form-control'  value=''/>
									 </div>
					</div>
						<div id="climit" class="form-group"><label  class="col-sm-4 text-right">Credit Limit :</label>
							<div class="col-sm-6 input-icon right">
						<input type='text' name='pclimit' id='pclimit' class='form-control'  value=''/>
								 </div>
					</div>
					
						<div id="rate" class="form-group"><label  class="col-sm-4 text-right">Rate:</label>
						<div class="col-sm-6 input-icon right">
					    <input type='text' name='prate' id='prate'  class='form-control'  value=''/>
									 </div>
					</div>
				
						<div class="form-group"><label  class="col-sm-4 text-right">Landing Number :</label>
					<div class="col-sm-6 input-icon right">
							<select name="landingnumber" id="landingnumber" class="required form-control">
								<option value="">----Select----</option>
							<? foreach($landingnumber as $num){?>
								<option value="<?=$num['number']?>" rel="<?=$num['pri']?>" rel1="<?=$num['region']?>"><?=$num['number']?></option>
							<? }?>
							</select>
							<a href="<?php echo base_url().$popaction;?>" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Add" class="glyphicon glyphicon-plus-sign"></span></a>
									 </div>
					</div>
					
						<div class="form-group"><label  class="col-sm-4 text-right">PriNumber :</label>
						<div class="col-sm-6 input-icon right">
						<input type="text" name="pri" value="" id="pri" class="required form-control">
										 </div>
					</div>
					
						<div class="form-group"><label  class="col-sm-4 text-right">Region :</label>
						<div class="col-sm-6 input-icon right">
						<input type="text" name="region" value="" id="region" class="required form-control">	
								 </div>
					</div>
				
						<div class="form-group"><label  class="col-sm-4 text-right">Service Activation Date :</label>
						<div class="col-sm-6 input-icon right">
						<input type="text" name="svdate" value="" id="svdate" class="required datepicker  form-control">
									 </div>
					</div>
					
						<div class="form-group"><label  class="col-sm-4 text-right">Sim OwnerShip:</label>
						<div class="col-sm-6 input-icon right">
						
							<select name="owner" id="owner" class="form-control">
								<option	value="0">Select</option>
								<option	value="vmc">VMC</option>
								<option	value="client">Client</option>
								<option	value="vmcclient">VMC-Client</option>
							</select>
								 </div>
					</div>
			
				
					    <div style="display:none" id="simholder" class="Sform-group"><label  class="col-sm-4 text-right">SIM Holder :</label>
					    <div class="col-sm-6 input-icon right">
						<input type="text" name="simholder" value=" " id="simholder"  class="form-control">
									 </div>
					</div>
					
				     	<div style="display:none" id="tempnumber" class="form-group"><label  class="col-sm-4 text-right">VMC Number :</label>
				     	<div class="col-sm-6 input-icon right">
					    <input type="text" name="tempnumber" value=" " id="tempnumber" class="form-control" >
								 </div>
					</div>
				

			
						<div class="form-group"><label  class="col-sm-4 text-right">Payment Term:</label>
				<div class="col-sm-6 input-icon right">
							<select name="pterm" id="pterm" class="required auto form-control">
								<option value="1">1 Month</option>
								<option value="2">2 Month</option>
								<option value="3">3 Month</option>
								<option value="4">4 Month</option>
								<option value="5">5 Month</option>
								<option value="6">6 Month</option>
								<option value="7">7 Month</option>
								<option value="8">8 Month</option>
								<option value="9">9 Month</option>
								<option value="10">10 Month</option>
								<option value="11">11 Month</option>
								<option value="12">12 Month</option>
							</select>
											 </div>
					</div>
<!--
							<input type="text" name="pterm" value="" id="pterm" class="required">
-->
						
					
						<div class="form-group"><label  class="col-sm-4 text-right">SMS Limit:</label>
						<div class="col-sm-6 input-icon right">
						<input type="text" name="slimit" value="" id="slimit" class="required form-control">
								 </div>
					</div>
					
						<div class="form-group"><label  class="col-sm-4 text-right">Parallel Calls Limit:</label>
						<div class="col-sm-6 input-icon right">
						<input type="text" name="plimit" value="" id="plimit" class="required form-control">	
										 </div>
					</div>
				
					<!--
					<tr>
						<th><label>Missed Call :</label></th>
						<td><input type="checkbox" name="ntype" value="1" id="ntype">
						</td>	
						<td></td>	
					</tr>-->
				
						<div class="form-group"><label  class="col-sm-4 text-right">Support :</label>
						<div class="col-sm-6 input-icon right">
						<input type="checkbox" name="support" value="1" id="support">
								 </div>
					</div>
					<div class="form-group"><label  class="col-sm-4 text-right">Auto Reset :</label>
						<div class="col-sm-6 input-icon right">
						<input type="checkbox" name="autoreset" value="1" id="autoreset">
								 </div>
					</div>
					
			
	           <div class="form-group col-sm-12 text-center">
				   <input id="button1" type="submit" class="btn btn-primary" name="update_system" value="<?=$this->lang->line('submit') ?>" /> 
                   <input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
               </div>
		<?php echo form_close();?>
</div>
