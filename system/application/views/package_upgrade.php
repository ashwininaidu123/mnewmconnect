<script type="text/javascript">
 $(document).ready(function(){
	 
	 $("#paynow").live('click',function(event) {
		 var s=$('input[name=package]:checked').val();
		 var x=s.split("~");
		 $('#item_name').val(x[1]);
		 $('#item_number').val(x[0]);
		 $('#custom').val(x[0]);
		 $('#amount').val(x[2]);
		 
		
	 });
	
	 
	 
	 });
</script>
<div id="box">
<form name="paypal" action='<?=$this->config->item('pay_url')?>' id="form">
<fieldset id="priseries"><legend>Availble Information</legend>
<table width="100%">
<tr>
	<td>Call Balance :</td>
	<td><?=$call_balance->cr?></td>

</tr>
<tr>
	<td>Sms Balance :</td>
	<td><?=$sms_balance->cr?></td>

</tr>

</table>

</fieldset>






<div class="session_message <?php echo $this->session->flashdata('msgt');?>"><?php echo $this->session->flashdata('msg');?></div>
	<fieldset id="priseries"><legend>Package Process</legend>
<table width="100%">
	<?php
	//print_r($html['packages']);
		for($i=0;$i<sizeof($packages);$i++)
		{
			
			?>
			<tr>
					<td>
						<input type="radio" class='rad' name="package" checked id="<?php echo $packages[$i]['package_id'];?>" value="<?php echo $packages[$i]['package_id']."~".$packages[$i]['package_name']."~".$packages[$i]['amount']; ?>"/><?php echo $packages[$i]['package_name']."   ====>  ".$packages[$i]['amount'];?>
					</td>
			</tr>
			<?php
			
		}
	
	
	?>
	<input type="hidden" id="item_name" name="item_name" value=""/>
	<input type="hidden" id="item_number" name="item_number" value=""/>
	<input type="hidden" id="custom" name="custom" value=""/>
	<input type="hidden" id="amount" name="amount" value=""/>
	
</table>
</fieldset>


<div align="right">
<?=$paypal_form?>
</div>
</div>

