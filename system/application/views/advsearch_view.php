<script src="system/application/plugins/jquery-ui/jquery-ui-1.10.4.min.js"></script>
<script src="system/application/js/ui/jquery-ui-timepicker-addon.js"></script>
<script src="system/application/js/application.js"></script>
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title" id="myModalLabel">Advance Search </h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
						<!-- Advance Search Start -->
						<?php if(!empty($form['adv_search'])) { ?>
						<div id="advance_search">
							<?=$form['open']?>
								<table id="adv_search" class="table table-hover table-striped"> 
								<tbody>
									<tr>
										<td class="small1">
										   <input type="radio" name="timespan" value="all" checked />All
										</td>
										<td class="small1">
										   <input type="radio" name="timespan" value="today"/>Today
										</td>
										<td class="small1">
										  <input type="radio" name="timespan" value="last7"/>Last 7 Days
										</td>
										<td class="small1">
										 <input type="radio" name="timespan" value="month"/>This Month
										</td>
									</tr>
									<tr>
										<th valign="top"><label>Groups : </label></th>
										<td class="small1">
											<select multiple='multiple' class="muliselect multilist" data-style="input-sm btn-default" id="multiselect_gid[]" name="multiselect_gid[]" >
											<?php
												foreach($form['groups'] as $val=>$opt){
													$option = "<option value='".$val."'>".$opt."</option>";
													echo $option;
												}
											?>
											</select> 
										</td>
										<?php if(! empty($form['employees'])) {?>
										<th valign="top"><label>Employees : </label></th>
										<td class="small1">
											<select multiple='multiple' class="muliselect multilist" id="multiselect_eids[]" name="multiselect_eids[]">
											<?php
												foreach($form['employees'] as $val=>$opt){
													$option = "<option value='".$val."'>".$opt."</option>";
													echo $option;
												}
											?>
											</select> 
										</td>
										<?php } ?>
									</tr>
									<tr>
										<th valign="top" id="addmore"><a href="javascript:void(0)" class="add_more_adv">Add More</a></th>
										<td></td>
										<td></td>
										<td></td>
									
									</tr>
									<tr id="searchF">
									   <td class="small1">	
											<label>Condition :</label>	
											<select data-style="input-sm btn-default" class="form-control" name='cond[]' id='cond[]'>
												<option value="and">AND</option>
												<option value="or">OR</option>
											</select>
										</td>
									   <td class="small1">
											<label>Field :</label>
											<select name="field_d[]" class="form-control"  data-style="input-sm btn-default" id="field_d">
											<?php
												foreach($form['adv_search'] as $field=>$field1){?>
													<option value="<?=$field?>"><?=$field1?></option>
											<? }?>
											</select>
										</td>
									   <td class="small1">
											<label>Operator : </label>
											<select data-style="input-sm btn-default" class="form-control" name='equ[]' id='equ[]'>
												<option value="1">Like</option>
												<option value="2">Not Like</option>
												<option value="3"> = </option>
												<option value="4"> != </option>
												<option value="5"> > </option>
												<option value="6"> < </option>
												<option value="7"> >= </option>
												<option value="8"> <= </option>
											</select>
										</td>
									 <td class="small1">
										 <label>Value : </label>
										 <input type="text" name="fval[]" class="form-control" id="fval[]" value=""/> 
									  </td>
									</tr>
									<tr id="sbtn">
										<th >
											<?php if($form['save_search']<6) { ?>
											<td class="small1">
												<input id="button1" type="checkbox" name="sav_search" 
												value="1" /> Save Search
											</td>
											<td class="small1">
												<input type="text" name="searchname" maxlength="12" id="searchname" class="form-control" value="search<?=$form['save_search']+1?>"/>
											</td>
											<?php } ?>
											<td class="small1"><center><input id="button1" type="submit" class="btn btn-success " name="Adv_submit"  value="<?=$this->lang->line('level_search')?>"  /></center></td>
									   </th>
									 </tr>
							</tbody>
						</table>
						<?=$form['close']?>
						</div>
						<?php } ?>
						<!-- Advance Search End -->
							</div>
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
	</div>
	
