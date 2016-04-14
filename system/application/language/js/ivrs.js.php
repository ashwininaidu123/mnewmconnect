<script>
$(function(){
	$("#ivrsadd").validate({
		rules: {
			title	: "required",
			prinumber	: {
					required: true
			},
			timeout	: {
					required: true,
					number:true
				},
			filename: {
					required: true,
					accept:'wav|mp3'
				}
		}
		,messages: {
			title	: "<?=$this->lang->line('error_required')?>",
			prinumber	: {
					required	: "<?=$this->lang->line('error_required')?>",
					min			: "Selecte a landing number",
				},
			timeout	: {
					required: "<?=$this->lang->line('error_required')?>",
				},
			filename: {
					required	: "<?=$this->lang->line('error_required')?>",
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
	
	$("#reftargettype").live('change',function(){
			if($(this).val() == "group"){
				$('#targetid').parent().html('<?=str_replace("\n","",form_dropdown('targetid',$this->systemmodel->get_groups(),'','id="targetid"'))?>');
			}else if($(this).val() == "employee"){
				$('#targetid').parent().html('<?=str_replace("\n","",form_dropdown('targetid',$this->systemmodel->get_emp_list(),'','id="targetid"'))?>');
			}else if($(this).val() == "pbx"){
				$('#targetid').parent().html('<?=str_replace("\n","",form_dropdown('targetid',$this->systemmodel->get_pbx_list(),'','id="targetid"'))?>');
			}else if($(this).val() == "ivrs"){
				$('#targetid').parent().html('<?=str_replace("\n","",form_dropdown('targetid',$this->systemmodel->get_ivrs_list(),'','id="targetid"'))?>');
			}else if($(this).val() == ""){
				$('#targetid').parent().html('<?=str_replace("\n","",form_dropdown('targetid',array(''=>'Target'),'','id="targetid"'))?>');
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
