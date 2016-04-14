<script>
$(function() {
var cnt=1;
	$('#c_all').live('click',function(event){
		$("input[type=checkbox]").each(function(){
			if((cnt%2) == 0){
				$(this).attr('checked',false);
			}else if((cnt%2) == 1){
				if($('input[type=checkbox]').not(':checked')){
					$(this).attr('checked',true);
				}
			}
		});
		cnt++;
	});
	$('#esetting').validate({ });
	$('.snumbers').live('click',function(event){
		var a = new Array();
		$("input[type=checkbox]").each(function(){
			if($(this).is(":checked")){
				a.push($(this).val());
			}
		});
		if(a.length==0){
			jAlert('Please Select atleast one item to Send Email');
			 return false;
		}else{
			event.preventDefault();
			event.stopPropagation();
			$('embed').hide();
			$.get(this.href, function(data){
				$("#popupDiv").html('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b>' + data);
				$("#popupDiv").css( {
					backgroundColor: '#FFF', 
					borderColor: '#ccc',
					'border-radius': '10px', 
					opacity: .9,
					color: '#000',
					overflow:'auto',
					cursor:'default'
				});
				$('#ids').val(a);
			});
			$.blockUI({ message: $('#popupDiv') });
			return false;
		}
		
	});	
	$('.block').live('click',function(event){
		
		var a = new Array();
		//var nurl=$('.blkAssign').attr('href');
		//alert(nurl);
		$("input[type=checkbox]").each(function(){
			if($(this).is(":checked")){
				a.push($(this).val());
			}
		});
		
		if(a.length==0){
			jAlert('Please Select atleast one item to Block');
			 return false;
		}else{
			event.preventDefault();
			event.stopPropagation();
			$('embed').hide();
			$.get(this.href, function(data){
				$("#popupDiv").html('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b>' + data);
				$("#popupDiv").css( {
					backgroundColor: '#FFF', 
					borderColor: '#ccc',
					'border-radius': '10px', 
					opacity: .9,
					color: '#000',
					overflow:'auto',
					cursor:'default'
				});
				$('#ids').val(a);
			});
			$.blockUI({ message: $('#popupDiv') });
			return false;
		}
	});
	$('.confirmBlock').live('click',function(event){
		
		var a = new Array();
		//var nurl=$('.blkAssign').attr('href');
		//alert(nurl);
		$("input[type=checkbox]").each(function(){
			if($(this).is(":checked")){
				a.push($(this).val());
			}
		});
		
		if(a.length==0){
			jAlert('Please Select atleast one item to Block');
			 return false;
		}else{
			$.ajax({  
				type: "POST",  
				url: "Executive/sessSet/",  
				data:'num='+a+'&update_system=update_system', 
				success: function(msg){ 
				  window.location.href="Executive/cblknumber";
				}
			});
			return false;
		}
		
	});
	$('.unblock').live('click',function(event){
		
		var a = new Array();
		//var nurl=$('.blkAssign').attr('href');
		//alert(nurl);
		$("input[type=checkbox]").each(function(){
			if($(this).is(":checked")){
				a.push($(this).val());
			}
		});
		
		if(a.length==0){
			jAlert('Please Select atleast one item to Block');
			 return false;
		}else{
			event.preventDefault();
			event.stopPropagation();
			$('embed').hide();
			$.get(this.href, function(data){
				$("#popupDiv").html('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b>' + data);
				$("#popupDiv").css( {
					backgroundColor: '#FFF', 
					borderColor: '#ccc',
					'border-radius': '10px', 
					opacity: .9,
					color: '#000',
					overflow:'auto',
					cursor:'default'
				});
				$('#ids').val(a);
			});
			$.blockUI({ message: $('#popupDiv') });
			return false;
		}
	});




});
</script>

