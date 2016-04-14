	<script src="system/application/js/application.js"></script>
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title" id="myModalLabel"><?php echo $module['title'];?></h4>
			</div>
	    <div class="modal-body">
		 <form action="<?=base_url()?><?php echo $form['form_attr']['action'];?>" id="<?php echo $form['form_attr']['id'];?>" name="<?php echo $form['form_attr']['name'];?>" role="form" parsley-validate class="form-horizontal form icon-validation" method ="post" enctype="<?php echo $form['form_attr']['enctype'];?>" >
									<? if(isset($form['hidden'])){ ?>	
									<?php foreach($form['hidden'] as $key => $value){ ?>
									<input type="hidden" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $value; ?>">
								      <? }?>
								      <? }?>
                                
									<?if(isset($form['fields']))
                                        foreach($form['fields'] as $field){ ?>
                                        <div class="form-group">
                                            <?=isset($field['label'])?$field['label']:''?>
                                             <div class="col-sm-6 input-icon right">
                                            <?=isset($field['field'])?$field['field']:''?>
                                            </div>
                                        </div>
                                        <? }?>
                                      <? if(!isset($form['submit'])){ ?>	
                                        <div class="form-group text-center">
											<input id="button1" type="submit" onclick="javascript:$('#<?php echo $form['form_attr']['name'];?>').parsley('validate')" class="btn btn-primary" name="update_system" value="<?=$this->lang->line('submit') ?>" /> 
                                            <input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
      
                                        </div>
                                    	<? } ?>
                                    </form>
			    </div>
		</div>
	</div>
