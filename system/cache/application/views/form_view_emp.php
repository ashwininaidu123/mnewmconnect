   <div id="main-content">
            <div class="page-title"> <i class="icon-custom-left"></i>
                <h4><?php echo $module['title'];?></h4>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
								<form action="<?=base_url()?><?php echo $form['form_attr']['action'];?>" id="<?php echo $form['form_attr']['name'];?>" role="form" parsley-validate class="form-horizontal icon-validation" method ="post">
								<? if(isset($form['hidden'])){ ?>	
									<?php foreach($form['hidden'] as $key => $value){ ?>
									<input type="hidden" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $value; ?>">
								      <? }?>
								      <? }?>
                                <div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="no-bd">
                                            <tr>
												<th>#</th>
												<th>Employee Name</th>
												<th>Start Time</th>
												<th>End Time</th>
												<th>Fail over</th>
												<?php
													 if($form['gdetail']['primary_rule']>'0'){
												     echo '<th>Regional</th>';
													  } 
													  if($form['gdetail']['rule']=='2'){ 
													  echo '<th>Employee Weight</th>';
													  }	
												      if($form['gdetail']['rule']=='3'){ 
												      echo '<th>Employee Priority</th>';
													  }	
													  if($form['gdetail']['pincode']=='1'){
													  echo '<th>Pincode</th>';
													  }	
																 
												 ?>
                                            </tr>
                                        </thead>
                                        <tbody class="no-bd-y">		
												<?php
													if(!empty($form['gpEmp'])){
														 $check=($form['gpEmp']['isfailover']=='1')?'checked':'';
														?>
														 <tr>
															 <td>#</td>
															 <td><label><?=$form['gpEmp']['empname']?></td>
															 <td><input type="text" name="starttime" id="starttime" value="<?=$form['gpEmp']['starttime']?>" class="timepicker" style="width:50px;" /></td>
															 <td><input type="text" name="endtime" id="endtime" value="<?=$form['gpEmp']['endtime']?>"  class="timepicker" style="width:50px;" /></td>
															 <td><input type="checkbox" name="isfailover" id="isfailover" 
															 value="1"  <?=$check?>/></td>
															 <?php
																if($form['gdetail']['primary_rule']>'0'){
															  echo '<td>'.form_dropdown('area_code',$this->empmodel->get_areacodes($form['gdetail']['primary_rule']),'','style="width:150px;" class="form-control"  id="area_code"').'</td>';
															 } 
															if($form['gdetail']['rule']=='2'){ 
																echo 
																'<td>'.form_input(array('name'=>'empweight','id'=>'empweight','value'=>$form['gpEmp']['empweight'],'style'=>'width:50px;')).'</td>';
															}	
															if($form['gdetail']['rule']=='3'){ 
																echo 
																'<td>'.form_input(array('name'=>'empPriority','id'=>'empPriority','value'=>$form['gpEmp']['empPriority'],'style'=>'width:50px;')).'</td>';
															}	
															if($form['gdetail']['pincode']=='1'){
																echo 
																'<td>'.form_textarea(array('name'=>'pcode','id'=>'pcode','value'=>$form['gpEmp']['pincode'])).'</td>';
															}	
														 
														?>
														 </tr>
														
														<?php
														
													}else{
														foreach($form['emplist'] as $key=>$emp){
														//if(sizeof($form['empexists'])>0){
														if(!in_array($key,$form['empexists'])){
															$i=0;$i++;
														?>
														 <tr class="<?=($i%2==0)?'even':''?>">
															 <td><input type="checkbox" id="<?=$key?>"  name="emp_ids[]" value="<?=$key?>" class="grp_emp"/></td>
															 <td><label for="<?=$key?>"><?=$emp?></label></td>
															 <td><input type="text" name="starttime<?=$key?>" id="starttime<?=$key?>" value="00:00" style="width:50px;" class="timepicker" disabled/></td>
															 <td><input type="text" name="endtime<?=$key?>" id="endtime<?=$key?>" value="23:59" style="width:50px;" class="timepicker" disabled/></td>
															<td><input type="checkbox" name="isfailover<?=$key?>" id="isfailover<?=$key?>" value="1" disabled /></td>
															 <?php
																if($form['gdetail']['primary_rule']>'0'){
															  echo '<td>'.form_dropdown('area_code'.$key,$this->empmodel->get_areacodes($form['gdetail']['primary_rule']),'','style="width:150px;" class="form-control" id="area_code'.$key.'" disabled').'</td>';
															 } 
															if($form['gdetail']['rule']=='2'){ 
																echo 
																'<td>'.form_input(array('name'=>'empweight'.$key, 'class'=>'form-control', 'id'=>'empweight'.$key,'value'=>'','disabled'=>true,'style'=>'width:50px;')).'</td>';
															}	
															if($form['gdetail']['rule']=='3'){ 
																echo 
																'<td>'.form_input(array('name'=>'empPriority'.$key,'class'=>'form-control', 'id'=>'empPriority'.$key,'value'=>'','disabled'=>true,'style'=>'width:50px;')).'</td>';
															}	
															if($form['gdetail']['pincode']=='1'){
																echo 
																'<td>'.form_textarea(array('name'=>'pcode'.$key,'class'=>'form-control', 'id'=>'pcode'.$key,'value'=>'','disabled'=>true)).'</td>';
															}	
														 
														?>
														 </tr>
														
														<?php
														}
													//}
														
													}
												}
												?>
												
                                        </tbody>
                                   
                                    </table>
                                          <? if(!isset($form['submit'])){ ?>	
												  <div class="form-group text-center">
														<input id="button1" type="submit" onclick="javascript:$('#<?php echo $form['form_attr']['name'];?>').parsley('validate')" class="btn btn-primary" name="update_system" value="<?=$this->lang->line('submit') ?>" /> 
														<input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
												   </div>
												 <? } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
         

</form>
</div>
