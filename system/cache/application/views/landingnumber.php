<script language="javascript" type="text/javascript">
$(document).ready(function(){
	$('#hiderow').hide();
	$('#hiderow1').hide();
	jQuery.validator.addMethod("numeric", function(value, element) {
		return this.optional(element) || value == value.match(/^[0-9]+$/);
	}, "Allowed only Numeric values"); 
	$('#hiderow').hide();
		$('#demo').live('click',function(){
			var checkeds = $(this).attr("checked");
			if(checkeds){
				$('#hiderow').show();
				$('#hiderow1').show();
			}else{
				$('#hiderow').hide();
				$('#hiderow1').hide();
			}
		
	});
	$("#landingnumberFrm").validate({
		rules:{
			landingnumber:{
				numeric:true
			},
			pri:{
				numeric:true
			}
		},
		errorPlacement: function(error, element) {
			error.appendTo( element.parent().next() );
		}
	});
	$('#button4').live('click',function(event){
		$("#priadd").validate({
			rules:{
				pri:{
					numeric:true
				},
				from:{
					numeric:true,
					min:0,
					max:99
				},
				to:{
					numeric:true,
					min:0,
					max:99
				}
				
			},
			errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			},submitHandler: function(){
				var f1=$('#from').val();
				var f2=$('#to').val();
		    if(f1>0 && f1<f2){
				  
				$.ajax({  
						type: "POST",  
						url: "Masteradmin/AddUassignedPris/",  
						data:$("#priadd").serialize()+'&update_system=update_system', 
						success: function(msg){ 
							if(jQuery.trim(msg)=="redirect"){
								window.location.href="Masteradmin/managePriList";
							}else if(jQuery.trim(msg)=="Error"){
								$('#errors').html("Pri Number is already Listed");
								return false;
								}else{
									
									$('#pri').children().remove().end().append(msg) ;
									$('#popupDiv').html("");
									$.unblockUI();
									}
							}	
						});
				}else{
					$('#from').parent().next().append("From value must be less than<br>To value/From value greater than zero");
					return false;
					
				}
				
			}
			
		});
	
	});
	
});
   </script>
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
			 echo form_open($action.$id,$attributes);
			 $pri='';$number='';$region='';
			 if(!empty($arr)){
					$pri=$arr[0]['pri'];
					$number=$arr[0]['number'];
					$region=$arr[0]['region'];
				}
				//echo $pri."==>".$number."===>".$region;
		?>
		
				<TABLE>
					
					<tr>
					   <div class="form-group"><label  class="col-sm-4 text-right">Landing Number :</label>
						  <div class="col-sm-6 input-icon right">
							<input type="text" name="landingnumber" id="landingnumber" class="required number form-control" value="<?php echo $number;?>"/>
						  </div>
					   </div>
					</tr>
					<tr>
					   <div class="form-group"><label  class="col-sm-4 text-right">Temporary Number :</label>
						  <div class="col-sm-6 input-icon right">
							<input type="text" name="tempnumber" id="tempnumber" class="required number form-control"/>
						  </div>
					   </div>
					</tr>
					<tr>
					   <div class="form-group"><label  class="col-sm-4 text-right">MDN Number :</label>
						  <div class="col-sm-6 input-icon right">
							<input type="text" name="mdnnumber" id="mdnnumber" class="required number form-control"/>
						  </div>
					   </div>
					</tr>
                    <tr>
						<div class="form-group"><label  class="col-sm-4 text-right">Provider :</label>
						<div class="col-sm-6 input-icon right">
							<select name="Provider" id="Provider" class="required form-control">
								<option value="">----Select----</option>
							<? foreach($provider as $key=>$num){?>
								<option value="<?=$num?>" <?=($num==$pri)?'selected':''?>><?=$num?></option>
							<? }?>
							</select>
						 </div>
					</div>
					</tr>
					<tr>
						<div class="form-group"><label  class="col-sm-4 text-right">PriNumber :</label>
						<div class="col-sm-6 input-icon right">
							<select name="pri" id="pri" class="required form-control">
								<option value="">----Select----</option>
							<? foreach($dropdown as $key=>$num){?>
								<option value="<?=$num?>" <?=($num==$pri)?'selected':''?>><?=$num?></option>
							<? }?>
							</select>
							<?php //echo form_dropdown('pri',$dropdown,$pri,' id="pri" class="required"');?>
						<?php if($add){ ?>
						<a href="<?php echo base_url();?>Masteradmin/AddUassignedPris" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Add" class="glyphicon glyphicon-plus-sign"></span></a>
						<?php } ?>
						 </div>
					</div>
					</tr>
						<div class="form-group"><label  class="col-sm-4 text-right">Region :</label>
						<div class="col-sm-6 input-icon right">
						<input type="text" name="region" id="region" class="required form-control" value="<?php echo $region;?>">
						</div>
					</div>
				</TABLE>
		
           <div class="form-group col-sm-12 text-center">
				   <input id="button1" type="submit" class="btn btn-primary" name="update_system" value="<?=$this->lang->line('submit') ?>" /> 
                   <input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
              </div>
		<?php echo form_close();?>
</div>
