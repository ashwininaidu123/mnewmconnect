
        <!-- BEGIN MAIN CONTENT -->
        <div id="main-content">
            <div class="page-title"> <i class="icon-custom-left"></i>
                <h3><strong><?php echo $module['title']; ?></strong></h3>
            </div>
            <!-- BEGIN ERROR BOX -->
            <?php 
            if($this->session->flashdata('msgt')){ $error1 = $this->session->flashdata('msgt'); }
			$error = validation_errors();
            if((isset($error) &&$error != '') || isset($error1)){
				$display = '';
			}else{
				$display = 'hide';
			}
			?>
			<div class="alert <?=($this->session->flashdata('msgt'))?$this->session->flashdata('msgt'):'error'?> <?=$display;?>" >
				<button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
				<?php echo validation_errors(); ?>
				<?php echo $this->session->flashdata('msg');?>
			</div>
			<!-- END ERROR BOX -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
							<form action="<?=base_url()?><?php echo $form['form_attr']['action'];?>" id="<?php echo $form['form_attr']['name'];?>" role="form" parsley-validate class="form-horizontal icon-validation" method ="post">
                                <div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="no-bd">
                                            <tr>
												<th style="width:20%;">#</th>
												<th>Employee Name</th>
												<?php
												if($form['gdetail']['group_rule']=='2'){ 
														echo '<th>Employee Weight</th>';
													}
												?>
											</tr>
                                        </thead>
                                        <tbody class="no-bd-y">		
												<?php 
												foreach($form['emplist'] as $key=>$emp){
													if(!in_array($key,$form['empexists'])){
												?>
												 <tr>
													 <td><input type="checkbox" id="<?=$key?>"  name="emp_ids[]" value="<?=$key?>" class="grp_emp"/></td>
													 <td><label for="<?=$key?>"><?=$emp?></label></td>
													 <?php
													 if($form['gdetail']['group_rule']=='2'){ 
														echo 
														'<td>'.form_input(array('name'=>'empweight'.$key,'id'=>'empweight'.$key,'value'=>'','disabled'=>true,'style'=>'width:50px;')).'</td>';
													 }
													 ?>
												 </tr>
												<?php
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

