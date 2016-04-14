
    <div class="content-wrapper">
            <section class="content-header">
					<h1>
					 <strong><?php echo $module['title'];?></strong></h1>
            </section>
           <section class="content">
            <div class="row">
                <div class="col-md-12">
             			<div class="box">
                          <div class="box-body">
								<form action="<?=base_url()?><?php echo $form['form_attr']['action'];?>" id="<?php echo $form['form_attr']['name'];?>" role="form" parsley-validate class="form-horizontal icon-validation" method ="post">
								<? if(isset($form['hidden'])){ ?>	
									<?php foreach($form['hidden'] as $key => $value){ ?>
									<input type="hidden" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $value; ?>">
								      <? }?>
								      <? }?>
                                <div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
                                 <table id="example2" class="table table-bordered table-striped ">
                                        <thead>
                                            <tr>
												<th>#</th>
												<th>Employee Name</th>
												<th>Start Time</th>
												<th>End Time</th>
<!--
												<th>Fail over</th>
-->
												<?php
//~ 
													  //~ echo '<th>Employee Weight</th>';
												      //~ echo '<th>Employee Priority</th>';
													  //~ echo '<th>Pincode</th>';			 
												 ?>
                                            </tr>
                                        </thead>
                                        <tbody>		
												<?php
														foreach($form['emplist'] as $key=>$emp){

															$i=0;$i++;
														?>
														 <tr>
															 <td><input type="checkbox" id="<?=$key?>"  name="grp_ids[]" value="<?=$key?>" class="grp_emp"/></td>
															 <td><label for="<?=$key?>"><?=$emp?></label></td>
															 <td><input type="text" name="starttime<?=$key?>" id="starttime<?=$key?>" value="00:00" style="width:60px;" class="timepicker form-control" disabled/></td>
															 <td><input type="text" name="endtime<?=$key?>" id="endtime<?=$key?>" value="23:59" style="width:60px;" class="timepicker form-control" disabled/></td>
<!--
															<td><input type="checkbox" name="isfailover<?=$key?>" id="isfailover<?=$key?>" value="1" disabled /></td>
-->
															 <?php
//~ 
																//~ echo 
																//~ '<td>'.form_input(array('name'=>'empweight'.$key, 'class'=>'form-control', 'id'=>'empweight'.$key,'value'=>'','disabled'=>true,'style'=>'width:50px;')).'</td>';
															//~ 
															//~ 
																//~ echo 
																//~ '<td>'.form_input(array('name'=>'empPriority'.$key,'class'=>'form-control', 'id'=>'empPriority'.$key,'value'=>'','disabled'=>true,'style'=>'width:50px;')).'</td>';
																//~ 
															//~ 
																//~ echo 
																//~ '<td>'.form_textarea(array('name'=>'pcode'.$key,'class'=>'form-control', 'id'=>'pcode'.$key,'value'=>'','disabled'=>true)).'</td>';
														//~ 
														 //~ 
														?>
														 </tr>
														
														<?php
														//}
													//}
														
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
</section>
         

</form>
</div>
