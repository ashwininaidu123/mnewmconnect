<script>
$(function(){
	$("#scnadd").validate({
		rules: {
			title	: "required",
			prinumber	: {
					required: true,
					number:true
				},
			timeout	: {
					required: true,
					number:true
				},
			filename: {
					required: true,
					accept:'wav'
				}
		}
		,messages: {
			title	: "<?=$this->lang->line('error_required')?>",
			prinumber	: {
					required	: "<?=$this->lang->line('error_required')?>"
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
