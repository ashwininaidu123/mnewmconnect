$(function() {
var client_id=mcube_client_id;
	$.ajax({
				  type: "POST",
				  url: "http://local.vmc.in/api/UserAuthenticate",
				  data: "authKey="+client_id, 
				  success: function(msg){
						alert(msg);
						return false;
				  },
				  error:function(XMLHttpRequest, textStatus, errorThrown) {
					  
					  alert(textStatus);
				  }
			 });
			return false;

	
	var out='';
	out='<table width="'+mcube_wiz_width+'" height="'+mcube_wiz_height+'" cellpadding="0" cellspacing="0" border="1"><tr><td>Dinesh</td></tr></table>';
	document.write(out);
	
	
	
	
});
