<script language="javascript" type="text/javascript">
   $(document).ready(function(){
		
		jQuery.validator.addMethod("alphanumeric", function(value, element) {
		return this.optional(element) || value == value.match(/^[a-z0-9 A-Z]+$/);
		},"Only Characters, Numbers Allowed.");
			 $("#region").validate({
					errorPlacement: function(error, element) {
						error.appendTo( element.parent().next() );
					},
			});
	});	
</script>
          <div id="main-content">
            <div class="page-title"> <i class="icon-custom-left"></i>
                <h4>
			<?php
			if(!empty($regions)){ echo $this->lang->line('level_Edit_region'); $action="AddRegion/".$regions[0]['regionid'];}else{ echo $this->lang->line('label_addregion'); $action='customfield/AddRegion';}
			?>
			</h4>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
							<div class="row" >
								<div class="col-md-12">
			<?php
			$attributes = array('class' => 'form', 'id' =>'region','name'=>'region');		
			 echo form_open($action,$attributes);
			 if(!empty($regions)){ $regionname=$regions[0]['regionname'];}else{ $regionname='';}
			?>
			
			<div class="form-group">
				<label for="groupname" class="col-sm-4 text-right"><?php echo $this->lang->line('level_regionname');?>:</label>   	<div class="col-sm-6 input-icon right">
						<input type="text" id="groupname" parsley-minlength="3" class="form-control required parsley-validated" value="<?=$regionname?>" name="regionname"><img src='system/application/img/icons/help.png' title='Customize regions as per your requirements. Add Region Name here.'>
					</div>
			</div>
			<div class="form-group">
				<label for="groupname" class="col-sm-4 text-right"></label>   	
				<div class="col-sm-6 input-icon right">
					<input id="button1" type="submit" name="update_system" class="btn btn-primary" value="<?=$this->lang->line('submit')?>" /> 
					<input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
				</div>
			</div>
			<?php echo form_close();?>
			</div>
		</div>
	</div>
</div>
</div>

