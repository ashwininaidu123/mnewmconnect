<?php echo doctype('xhtml1-trans'); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $html['title']?></title>
<?php if(isset($html['meta'])) echo meta($html['meta']);?>
<?php foreach($html['links'] as $link)echo link_tag($link);?>
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="system/application/css/ie-sucks.css" />
<![endif]-->
<?php foreach($html['scripts'] as $script) echo script_tag($script);?>


<script type="text/javascript">
 $(document).ready(function(){
	 $('#button1').live('click',function(){
		 var pros=[];
		  var netamount=$('#netamount').val();
		  if(netamount!=0){
			 return true;
		  }else{
			  alert("Please select Atlest one product,Please enter Quantity");
			  return false;
		  }
		 
		 
	 });
	 
	 $('input[type="checkbox"]').live('click',function() {
		  var netamount=$('#netamount').val();
		   var minus=0;
		   var i=0;
		 var val=[];
		 var vals=[];
		 $('input[type="checkbox"]:checked').each(function() {
			 i++;
		   val.push($(this).val());
			$("#"+$(this).val()+"qty").removeAttr("disabled"); 
			$("#itemnumber"+$(this).val()).val($(this).val()); 
			$("#itemname"+$(this).val()).val($("#"+$(this).val()+"name").val()); 
			
			
		 });
		 $('input[type="checkbox"]:unchecked').each(function() {
			
		 
		   $("#"+$(this).val()+"qty").val('');
			  $("#"+$(this).val()+"qty").attr("disabled", "disabled");
			   $('#'+$(this).val()+"div").html(''); 
			   $('#'+$(this).val()+"div1").html(''); 
			   $("#itemnumber"+$(this).val()).val(''); 
			   $("#itemname"+$(this).val()).val(''); 
			  
			minus+=parseFloat($('#'+$(this).val()+"totsprice").val()); 
				   $('#'+$(this).val()+"totsprice").val('0');
		 });
		// alert(minus);
		 $('#netamount').val(parseFloat(netamount-minus));
		  $('#netamt').html(parseFloat(netamount-minus));
		  $('#prodtot').val(val);
		
   });

		$('.amts').live('blur',function(){
			var price=parseInt($(this).val());
			var netamount=$('#netamount').val();
			var s=$(this).attr('id');
			s=s.split("qty")
					var discount=parseFloat($('#'+s[0]+"discount").val()/100);
				 var rate=parseFloat($('#'+s[0]+"rate").val());
						 var totprice=parseFloat(rate*price);
						 var totprice_discount=parseFloat(totprice*discount);
						 $('#'+s[0]+"div").html(totprice);
						 $('#'+s[0]+"div1").html(totprice-totprice_discount);
						 $('#'+s[0]+"totsprice").val(totprice-totprice_discount);
						 netamount=parseFloat(netamount)+parseFloat(totprice-totprice_discount);
						 
						 $('#netamount').val(netamount);
						 $('#itemprice'+s[0]).val(netamount);
						 $('#netamt').html(netamount);
				
				
			
		});
	
	 
	 
	 });
</script>
			
</head>
<body>
	<div id="container">
    	<div id="header">
        	<h2>MQUBE</h2>
        	<br><br><br><br>
      </div>
        <div id="wrapper"><div id="topmenu" class="ddsmoothmenu"><ul></ul></div>
<div id="box">
<div id="regBox">
<div id="box">
<form name="paypal" action='<?=site_url()?>Masteradmin/product_select' id="form" method="post">
<fieldset id="priseries">
<legend>Regsiter</legend>
<div class="session_message <?php echo $this->session->flashdata('msgt');?>"><?php echo $this->session->flashdata('msg');?></div>
	<fieldset id="priseries"><legend>Package Process</legend>
	<table width="100%" border="1">
		<tr>
			<th>Select</th>
			<th>Business Name</th>
			
		</tr>
	<?php
	//print_r($html['business']);
		for($i=0;$i<sizeof($html['business']);$i++)
		{
			
			?>			<tr>
					<td>
					<input type="radio" name="bid" id="<?=$html['business'][$i]['bid'];?>" value="<?=$html['business'][$i]['bid'];?>" class="package" />
					</td>
					<td> <?=$html['business'][$i]['businessname'];?>
						
						
					
					</td>
					
					
					
					</tr>
			<?php
			
		}
	
	
	?>
	
	
</table>
<table><tr><td><center>
<input id="button1" type="submit" name="proceed" value="<?=$this->lang->line('submit')?>" /> 
<input id="button2" type="reset" value="<?=$this->lang->line('reset')?>" />
</center></td></tr></table>
</fieldset>


</fieldset>
<div align="right">

</div>
</form>
</div>
</div>
</div>
<? $this->load->view('footer');?>


