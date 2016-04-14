$(document).ready(function(){
	 $('#relatedto').live('change',function(event){
		  var r=$('#relatedto').val();
		  $.ajax({  
						type: "POST",  
						url: "Masteradmin/get_relemps/"+r,  
						data:'package=allmodules', 
						success: function(msg){ 	
							$('#emplp option').each(function(i, option){ $(option).remove(); });
							$('#emplp').append(msg);
							
							}
						});
				
        	
        	
        	
			});

	$('#addmodule').validate({
			errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			}
		});
	$('#adminrole').validate({
			errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			}
		});
	$('#feature_id').validate({
			errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			}
		});
	$('#addpackage').validate({
			errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			},submitHandler: function(form){
				var selector_checked = $('input[class="moduleids"]:checked').length; 
				$('#fids').parent().next().html("");
				if(selector_checked==0){
						$('#fids').parent().next().append("<span style='color:red;'>Please select one Module</span>");
						return false;
				}else{
					form.submit();
				}
			}
		});
	
	
	
});
