        <!-- BEGIN MAIN CONTENT -->
    <div class="content-wrapper">
                <section class="content-header">
<!--
                <h1><?php //echo $module['title']; ?></h1>
-->
          

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
		  <section class="content">
            <div class="row">
                <div class="col-md-12">
                        <div class="box box-primary">
                      <div class="box-header with-border">
                         <h3 class="box-title"><?php echo $module['title']; ?></h3>
                </div>
                     
							 <form action="<?=base_url().$form['form_attr']['action'];?>" id="<?php echo $form['form_attr']['id'];?>" name="<?php echo $form['form_attr']['name'];?>" role="form" parsley-validate class="form-horizontal form icon-validation" method ="post" enctype="<?php echo $form['form_attr']['enctype'];?>" >
									    <div class="box-body">
                                         
									<?php if(isset($form['hidden'])){ 
										foreach($form['hidden'] as $key => $value){ ?>
									<input type="hidden" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $value; ?>">
								      <?php }
								      }
								      if(isset($form['fields']))
                                        foreach($form['fields'] as $field){
									  ?>
                                        <div class="form-group <?php if(isset($field['style']) && $field['style'] == 'none')echo 'hidden';?>">
                                            <?=isset($field['label'])?$field['label']:''?>
                                             <div class="col-sm-6 input-icon right">
                                               <?=isset($field['field'])?$field['field']:''?>
                                             </div>
                                        </div>
                                        <?php }
                                        if(isset($form['fields1']))
										foreach($form['fields1'] as $field){?>
										 <div class="form-group">
                                          <?=isset($field['label'])?$field['label']:''?>
                                             <div class="col-sm-6 input-icon right">
                                               <?=isset($field['field'])?$field['field']:''?>
                                            </div>
                                        </div>
									    <?php }
					                    if(!isset($form['submit'])){ ?>	
                                        <div class="form-group text-center">
											<input id="button1" type="submit" onclick="javascript:$('#<?php echo $form['form_attr']['name'];?>').parsley('validate')" class="btn btn-primary" name="update_system" value="<?=$this->lang->line('submit') ?>" /> 
                                            <input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
                                        </div>
                                    	<?php } ?>
                                    	       </div>
                            </div>
                                    </form>
                 
                </div>
            </div>
        </div>
        </section>
      </section>
        <!-- END MAIN CONTENT -->

    <!-- END WRAPPER -->
      <div class="modal fade" id="modal-responsive" aria-hidden="true"></div>