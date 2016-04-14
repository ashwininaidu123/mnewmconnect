<script>
$(function() {
	$('#template').live('change',function(){
		$.get('msg/tmpl/'+$(this).val(), function(data){//alert(data);
			$('textarea#content').val(data);
			$('textarea#content').parent().prev().find('.counter').html( $('textarea#content').val().length + '/' + Math.ceil($('textarea#content').val().length/160) + ' char');
		});
	});
	jQuery.validator.addMethod("mobile", function(value, element) {
	return this.optional(element) || /^[7-9][0-9]{9}$/.test(value);
	}, "Should start with 7 - 9 and 10 digits");
	$("#smsfrm").validate({
		rules: {
			content	: "required",
			senderid: "required",
			scheduleat: "required",
			content	: "required",
			//pbid	: "required",
			to		: {
					//required:true,
					mobile:true
				},
			filename: {
					//required: true,
					accept:'txt|csv'
				}
		}
		,messages: {
			content	: "<?=$this->lang->line('error_required')?>",
			senderid: "<?=$this->lang->line('error_required')?>",
			scheduleat: "<?=$this->lang->line('error_required')?>",
			//pbid	: "<?=$this->lang->line('error_required')?>",
			to		: {
					//required: "<?=$this->lang->line('error_required')?>",
					mobile	: "<?=$this->lang->line('error_mobile')?>"
				},
			filename: {
					//required	: "<?=$this->lang->line('error_required')?>",
					accept		: "<?=$this->lang->line('error_contactfile')?>"
				}			
		}
		,errorPlacement: function(error, element) {
			error.appendTo( element.parent().next() );
			
		}
		,submitHandler: function(form){
			if(!$('#to').val() && !$('#pbid').val() && !$('#filename').val()){
				$('<label class="error"><?=$this->lang->line('error_required')?></label>').appendTo( $('#to').parent().next() );
			}else if($('#content').val().length>160){
				jConfirm('Message length as '+ Math.ceil($('#content').val().length/160) +'<br>To continue click ok','Message Count',function(ret){
					if(ret){
						$('#to').parent().next().html('');
						$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
						form.submit();
					}
				});
			}else{
				$('#to').parent().next().html('');
				$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
				form.submit();
			}
		}
	});
	$("#pbadd").validate({
		rules: {
			pbname	: "required",

			filename: {
					required: true,
					accept:'txt|csv'
				}
		}
		,messages: {
			pbname	: "<?=$this->lang->line('error_required')?>",
			filename: {
					required	: "<?=$this->lang->line('error_required')?>",
					accept		: "<?=$this->lang->line('error_contactfile')?>"
				}			
		}
		,errorPlacement: function(error, element) {
			error.appendTo( element.parent().next() );
			
		}
	});
	$("#voiceadd").validate({
		rules: {
			title	: "required",
			soundfile: {
					required: true,
					accept:'wav'
				}
		}
		,messages: {
			title	: "<?=$this->lang->line('error_required')?>",
			soundfile: {
					required	: "<?=$this->lang->line('error_required')?>",
					accept		: "use .wav file only"
				}			
		}
		,errorPlacement: function(error, element) {
			error.appendTo( element.parent().next() );
			
		}
	});
	$("#smstmplfrm").validate({
		rules: {
			title	: "required",
			content : "required"
		}
		,messages: {
			title	: "<?=$this->lang->line('error_required')?>",
			content: "<?=$this->lang->line('error_required')?>"
		}
		,errorPlacement: function(error, element) {
			error.appendTo( element.parent().next() );
			
		}
	});
	$("#viocebroadcast").validate({
		rules: {
			soundid	: "required",
			scheduleat: "required"
		}
		,messages: {
			soundid	: "<?=$this->lang->line('error_required')?>",
			scheduleat: "<?=$this->lang->line('error_required')?>"
		}
		,errorPlacement: function(error, element) {
			error.appendTo( element.parent().next() );
			
		}
		,submitHandler: function(form){
			if(!$('#to').val() && !$('#pbid').val() && !$('#brfile').val()){
				$('<label class="error"><?=$this->lang->line('error_required')?></label>').appendTo( $('#to').parent().next() );
			}else{
				$('#to').parent().next().html('');
				$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
				form.submit();
			}
		}
	});
});
</script>
