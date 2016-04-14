<script>
$(function(){
	$("#campaignadd").validate({
		rules: {
			title	: "required",
			campaign_name	: {
					required: true
			},
			campaign_startdate	: {
					required: true
				},
			campaign_enddate: {
					required: true
				},
			perday_limit	: {
					required: true
			},
			perday_lead	: {
					required: true,
					number:true
				},
			total_lead: {
					required: true,
					number:true
				},
			status	: {
					required: true
			},
			budget	: {
					required: true,
					number:true
				},
			campaign_type: {
					required: true
				},
			file_id	: {
					required: true,
					number:true
				},
			action_oncomplete: {
					required: true
				}
		}
		,messages: {
			title	: "<?=$this->lang->line('error_required')?>",
			campaign_name	: {
					required	: "<?=$this->lang->line('error_required')?>",
				},
			campaign_startdate	: {
					required: "<?=$this->lang->line('error_required')?>",
				},
			campaign_enddate: {
					required	: "<?=$this->lang->line('error_required')?>",
					
				},			
			perday_limit	: {
					required	: "<?=$this->lang->line('error_required')?>",
				},
			perday_lead	: {
					required: "<?=$this->lang->line('error_required')?>",
				},
			total_lead: {
					required	: "<?=$this->lang->line('error_required')?>",
				},			
			status	: {
					required	: "<?=$this->lang->line('error_required')?>",
				},
			budget	: {
					required: "<?=$this->lang->line('error_required')?>",
				},
			campaign_type: {
					required	: "<?=$this->lang->line('error_required')?>",
				},		
			file_id	: {
					required: "<?=$this->lang->line('error_required')?>",
				},
			action_oncomplete: {
					required	: "<?=$this->lang->line('error_required')?>",
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
	$("#targettype").live('change',function(){
			if($(this).val() == "group"){
				$(this).parent().parent().parent().find('.empTr').remove();
				$(this).parent().parent().parent().append('<tr class="empTr"><th><label>Group : </label></th><td><?=str_replace("\n","",form_dropdown('targeteid',$this->systemmodel->get_groups(),'','id="targeteid"'))?></td><td></td></tr>');
			}else if($(this).val() == "employee"){
				$(this).parent().parent().parent().find('.empTr').remove();
				$(this).parent().parent().parent().append('<tr class="empTr"><th><label>Employee : </label></th><td><?=str_replace("\n","",form_dropdown('targeteid',$this->systemmodel->get_emp_list(),'','id="targeteid"'))?></td><td></td></tr>');
			}else if($(this).val() == "pbx"){
				$(this).parent().parent().parent().find('.empTr').remove();
				$(this).parent().parent().parent().append('<tr class="empTr"><th><label>PBX : </label></th><td><?=str_replace("\n","",form_dropdown('targeteid',$this->systemmodel->get_pbx_list(),'','id="targeteid"'))?></td><td></td></tr>');
			}else if($(this).val() == "api"){
				$(this).parent().parent().parent().find('.empTr').remove();
				$(this).parent().parent().parent().append('<tr class="empTr"><th><label>API URL : </label></th><td><?=str_replace("\n","",form_input(array('name' => 'api_url','id'=>'api_url','class'=>'required')))?></td><td></td></tr>');
			}else if($(this).val() == "sms"){
				$(this).parent().parent().parent().find('.empTr').remove();
				$(this).parent().parent().parent().append('<tr class="empTr"><th><label>SMS Text : </label></th><td><?=str_replace("\n","",form_textarea(array('name' => 'sms_text','id'=>'sms_text','class'=>'required','maxlength'=>'140')))?></td><td></td></tr>');
			}else{
				$(this).parent().parent().parent().find('.empTr').remove();
			}
	});
	
	$('textarea[maxlength]').keyup(function(){
		//get the limit from maxlength attribute
		var limit = parseInt($(this).attr('maxlength'));
		//get the current text inside the textarea
		var text = $(this).val();
		//count the number of characters in the text
		var chars = text.length;

		//check if there are more characters then allowed
		if(chars > limit){
			//and if there are use substr to get the text before the limit
			var new_text = text.substr(0, limit);

			//and change the current text with the new text
			$(this).val(new_text);
		}
	});
	$('#ivref').validate({
		
		
	});
	$("#addopt").validate({
		rules: {
			targettype	: {
					required: true
				},
			optorder	: {
					required: true,
					number:true
				},
			opttext	: {
					required: true
				},
			optsound: {
					accept:'wav|mp3'
				}
		}
		,messages: {
			targettype	: {
					required	: "<?=$this->lang->line('error_required')?>"
				},
			ivrsnumber	: {
					required	: "<?=$this->lang->line('error_required')?>"
				},
			opttext	: {
					required: "<?=$this->lang->line('error_required')?>"
				},
			optsound: {
					accept		: "Upload .wav file"
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
});
</script>
