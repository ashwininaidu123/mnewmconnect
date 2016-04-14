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
<form name="paypal" action='<?=$this->config->item('pay_url')?>' id="form" method="post">
<fieldset id="priseries">
<legend>Regsiter</legend>
<div class="session_message <?php echo $this->session->flashdata('msgt');?>"><?php echo $this->session->flashdata('msg');?></div>
	<fieldset id="priseries"><legend>Confirmation Process</legend>
	<table width="100%" border="1">
		<tr>
			<th>product ID</th>
			<th>Product Name</th>
			<th>Quantity</th>
			<th>Total Amount</th>
		</tr>
		<?php
		echo "<pre>";
		//print_r($posted);
			$prods=explode(",",$posted['prodtot']);
			for($i=0;$i<sizeof($prods);$i++)
			{
				?>
					<tr>
						<td><?=$prods[$i]?></td>
						<td><?=$posted['itemname'.$prods[$i]]?></td>
						<td><?=$posted[$prods[$i].'qty']?></td>
						<td><?=$posted[$prods[$i].'totsprice']?></td>
						<input type="hidden" name="item_number<?=(($i!=0)?"_".$prods[$i]:'');?>" id="item_number<?=(($i!=0)?"_".$prods[$i]:'');?>" value="<?=$prods[$i]?>"/>
						<input type="hidden" name="item_name<?=(($i!=0)?"_".$prods[$i]:'');?>" id="item_name<?=(($i!=0)?"_".$prods[$i]:'');?>" value="<?=$posted['itemname'.$prods[$i]]?>"/>
						<input type="hidden" name="item_price<?=(($i!=0)?"_".$prods[$i]:'');?>" id="item_price<?=(($i!=0)?"_".$prods[$i]:'');?>" value="<?=$posted[$prods[$i].'totsprice']?>"/>
					</tr>
					
					
				
				<?php
				
			}
		
		?>
	
	<tr>
		<td colspan="3" align="right">Net Total</td>
		<td align="right">
		<input type="hidden" name="amount" id="amount" value="<?=$posted['netamount']?>" />
			<div id="netamt"><?=$posted['netamount']?></div>
		</td>
	
	</tr>
	
</table>
<table><tr><td><center>
<?=$paypal_form?>
<input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
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


