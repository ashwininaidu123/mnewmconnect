$(function() {
	var url="https://mcube.vmc.in/";
	$("#connectcall").submit(function(){
		var err='';
			if($('#number').val()==''){
				err='Please enter Mobile number';
			}
			if($('#number').val()!=''){
				if(!$('#number').val().match(/^[0-9]+$/)){
					err+='Please enter Valid mobile Number';
				}else{
					if(confirm('Are you sure you want to connect the call?')){
						$.ajax({
								  type: "POST",
								 url:url+ "api/click2connect/",
								 data:$("#connectcall").serialize()+'&update_system=update_system', 
								  success: function(msg){
									  if(msg==0){
											err="Your Number is in DND,please try with Non-DND Number";
											$('#error').html(err);
											return false;
										}else if(msg=='2'){
											err="Insufficent Credites to make Call";
											$('#error').html(err);
											return false;
										}else{
											err="You will get a call soon";
											$('#error').html(err);
											window.setTimeout(function() {
												window.close();
											}, 1500);
										}
								  }
							 });
							
						
						
					}
					err='';
					
					
				}
			}
			$('#error').html(err);
			return false;
		
		
		
	});
		/*$('#callme').live('click',function(event){
			
		});*/
	
	
	
});
