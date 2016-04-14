<script language="javascript" type="text/javascript">
   $(document).ready(function(){
	   jQuery.validator.addMethod("numeric", function(value, element) {
		return this.optional(element) || value == value.match(/^[0-9]+$/);
		}, "Allows only numeric values"); 
	  $("#landingnumber").validate({
		  rules:{
			 credit:{
			  numeric:true
			}
		  },
		  messages:{
			 
		  },		  
		  errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			}
		  });
	  
   });
   </script>
 <div id="main-content">
            <div class="page-title"><i class="icon-custom-left"></i>
                <h3><?=$credit?></h3>
            </div>
           <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
							<div class="row" >
					           <div class="col-md-12 col-sm-12 col-xs-12">
								   
		<?php
			$attributes = array('class' => 'form', 'id' =>'landingnumber','name'=>'landingnumber');		
			 echo form_open($action,$attributes);
		?>

				<TABLE>
					<tr>
					<div class="form-group"><label class="col-sm-4 text-right">Busniess User :</label>
						<div class="col-sm-6 input-icon right">
							<select name="businessuser" id="businessuser" class="form-control required">
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
					</tr>
					<tr>
					<div class="form-group"><label  class="col-sm-4 text-right"><?=$credit?> :</label>
						<div class="col-sm-6 input-icon right">
							<input type="text" name="credit" id="credit" class="form-control" />
					   </div>
					</div>
					</tr>
					<tr>
						<div class="form-group"><label  class="col-sm-4 text-right">Notes :</label>
						<div class="col-sm-6 input-icon right">
						<textarea name="notes" class="form-control" id="notes"></textarea>
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

