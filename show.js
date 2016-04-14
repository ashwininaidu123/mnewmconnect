$(function() {
var url="https://mcube.vmc.in/";
var client_id=mcube_client_id;
var wiz_width=mcube_wiz_width;
var wiz_height=mcube_wiz_height;
var btn_id=mcube_wiz_btn_id;
if(wiz_width=='') {	wiz_width = 300;}
if(wiz_height==''){	wiz_height = 250;}
$("#"+btn_id).css('cursor','pointer');
var out='';
  $.support.cors = true;
		$.ajax({
			  url:url+"api/UserAuthenticate",
			  type: "POST",
			  data: "authKey="+client_id+"&hname="+window.location.hostname, 
				success: function(msg){
					var str=$.trim(msg);
					//alert(str);
					if(str=='n') $("#"+btn_id).css('display','none');
				}
		 });
	$("#"+btn_id).click(function(){
		
		window.open(url+'api/callMe?client_id='+client_id+'&locale=en&amp;url='+escape(document.location.href)+'&amp;referrer='+escape(document.referrer)+'','Click 2 Connect', 'fullscreen=no,toolbar=no,location=no,directories=no,status=no,menubar=no,titlebar=no,scrollbars=no,resizable=no,top=200,left=200,menubar=0,width='+wiz_width+',height='+wiz_height+',resizable=0');
	});
});
