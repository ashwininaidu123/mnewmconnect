<script>
$(function(){
	$("#pbxadd").validate({
		rules: {
			title	: "required",
			prinumber	: {
					required: true,
					},
			operator	: {
					required: true
				},
			hdayaudio: {
					accept:'wav|mp3'
				},
			greetings: {
					accept:'wav|mp3'
				}			
		}
		
		,errorPlacement: function(error, element) {
			error.appendTo( element.parent().next() );
		}
		,submitHandler: function(form){
			$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
			form.submit();
		}
	});
	$('#button1').live('click',function(event){
		$("#pbxext").validate({
		});
		
	});
	
	
	$("#targettype").live('change',function(){
			if($(this).val() == "group"){
				$('#ext').val('');
				$('#ext').removeAttr('readonly',false);
				$(this).parent().parent().parent().find('#targetid').parent().parent().remove();
				$("#FromTab tr:last").before('<tr><th><label><?=$this->lang->line('label_pbxgroup')?> : </label></th><td><?=str_replace("\n","",form_dropdown('targetid',$this->systemmodel->get_groups(),'','id="targetid" class="required"'))?></td><td></td></tr>');
			}else if($(this).val() == "employee"){
				
				$(this).parent().parent().parent().find('#targetid').parent().parent().remove();
					$("#FromTab tr:last").before('<tr><th><label><?=$this->lang->line('label_pbxemp')?> : </label></th><td><?=str_replace("\n","",form_dropdown('targetid',$this->systemmodel->get_emp_list(),'','id="targetid" class="employees required"'))?></td><td></td></tr>');
				
			}else if($(this).val() == "ivrs"){
				$('#ext').val('');
				$('#ext').removeAttr('readonly',false);
				$(this).parent().parent().parent().find('#targetid').parent().parent().remove();
				$("#FromTab tr:last").before('<tr><th><label><?=$this->lang->line('label_pbxivrs')?> : </label></th><td><?=str_replace("\n","",form_dropdown('targetid',$this->systemmodel->get_ivrs_list(),'','id="targetid" class="required"'))?></td><td></td></tr>');
			}else if($(this).val() == "pbx"){
				$('#ext').val('');
				$('#ext').removeAttr('readonly',false);
				$(this).parent().parent().parent().find('#targetid').parent().parent().remove();
				$("#FromTab tr:last").before('<tr><th><label><?=$this->lang->line('label_pbx')?> : </label></th><td><?=str_replace("\n","",form_dropdown('targetid',$this->systemmodel->get_pbx_list(),'','id="targetid" class="required"'))?></td><td></td></tr>');
			}else{
				$(this).parent().parent().parent().find('#targetid').parent().parent().remove();
			}
	});
	$('.employees').live('change',function(event){
		$('#ext').val('');
		$('#ext').removeAttr('readonly',false);
		$.ajax({  
			type: "POST",  
			url: "pbx/EmpByid/"+$(this).val(),  
			data:'update_system=update_system', 
			success: function(msg){ 
				msg=$.trim(msg);
				if(msg!=''){
					$('#ext').val(msg);
					$('#ext').attr('readonly',true);
				}else{
					$('#ext').val('');
					$('#ext').removeAttr('readonly',false);
				}
				 
			}
		});
	});
	 $('#prinumber').live('change',function(event){
		 var mod="20";
		 var seg="<?php echo $this->uri->segment('3');?>";
		$.ajax({  
			type: "POST",  
			url: "group/Moduleaddons_number/"+$('#prinumber').val()+"/"+mod+"/"+seg,    
			data:'update_system=update_system', 
			success: function(msg){ 
				//alert(msg);return false;
				var str=$.trim(msg);
				if(str!=''){
						var strs=str.split(',');
						for(var i=0;i<strs.length;i++){
							$('#'+$.trim(strs[i])).attr("disabled", "disabled");
						}
						
				}else{
						var form = $("#pbxadd");
						form.find(':disabled').each(function() {
							//alert("dd");
							$(this).removeAttr('disabled');
						});
				}
			}
		});
		
	});
});
</script>
