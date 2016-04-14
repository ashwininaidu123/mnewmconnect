<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<style>
body {
  margin: 18pt 18pt 24pt 18pt;
}

* {
  font-family: verdana,arial,georgia,serif,;
  }

p {
  text-align: justify;
  font-size: 1em;
  margin: 0.5em;
  padding: 10px;
}
td,th{
	font-size:10px;
}
</style>
</head>
<body>

<script type="text/php">

if ( isset($pdf) ) {

  $font = Font_Metrics::get_font("verdana");;
  $size = 6;
  $color = array(0,0,0);
  $text_height = Font_Metrics::get_font_height($font, $size);

  $foot = $pdf->open_object();
  
  $w = $pdf->get_width();
  $h = $pdf->get_height();

  // Draw a line along the bottom
  $y = $h - $text_height - 24;
  $pdf->line(16, $y, $w - 16, $y, $color, 0.5);

  $pdf->close_object();
  $pdf->add_object($foot, "all");

  $text = "Page {PAGE_NUM} of {PAGE_COUNT}";  

  // Center the text
  $width = Font_Metrics::get_text_width("Page 1 of 2", $font, $size);
  $pdf->page_text($w / 2 - $width / 2, $y, $text, $font, $size, $color);
  
}
</script>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr><td>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td align="left"><img src="system/application/img/logo.gif" width="120" height="95" border="0" /></td>
				<td align="right" style="color:red;">Your Bill for MCube</td>
			</tr>
		</table>
	</td></tr>
	<tr><td>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr height="200">
				<td width="50%" align="left" >
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr><td><?=$arr['businessname']?></td></tr>
						<tr><td><?=$arr['address']?></td></tr>
						<tr><td><?=$arr['state']?></td></tr>
						<tr><td><?=$arr['city'].' - '.$arr['zipcode']?></td></tr>
						<tr><td style="color:#fff;">********</td></tr>
					</table>
				</td>
				<td width="50%" align="right">
					<table width="100%" align="right" cellpadding="0" cellspacing="0" border="0">
							<tr><th align="right">Business Id :</th><td align="right"><?=$arr['bid']?></td></tr>
							<tr><th align="right">Bill No :</th><td align="right"><?=$arr['billno']?></td></tr>
							<tr><th align="right">Bill date :</th><td align="right"><?=$arr['billgendate']?></td></tr>
							<tr><th align="right">Billing Period :</th><td align="right"><?=$arr['billing_period']?></td></tr>
							<tr><th align="right">Due date :</th><td align="right"><?=$arr['due_date']?></td></tr>
						</table>
				</td>
			</tr>
		 </table>		
	</td></tr>
	<tr><td style="color:red;"><hr/></td></tr>
	<?php
		$bill_detail=$arr['bill_detail']->result_array();
	  // print_r($bill_detail);exit;
	
	
	?>
	<tr><td><b>Your Account Summary</b></td></tr>
	<tr><td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td align="center" style="border:1px solid #000000;"><b>previous balanace</b><br/>
						<?=number_format($bill_detail[0]['arrear'],2)?></td>
						<td align="center" style="border:1px solid #000000;"><b>payments</b>
						<br/><?=number_format($bill_detail[0]['netamount'],2)?></td>
						<td align="center" style="border:1px solid #000000;"><b>adjustments</b>
						<br/>0</td>
						<td align="center" style="border:1px solid #000000;"><b>this month's charges</b>
						<br/><?=number_format($bill_detail[0]['netamount'],2)?></td>
						<td align="center" style="border:1px solid #000000;"><b>amount payable </b><br/><?=round(($bill_detail[0]['netamount']+$bill_detail[0]['arrear']),2)?>
						</td>
					</tr>
				</table>
	
	</td></tr>
	
	<tr><td><b>Your Associated Numbers and charges</b></td></tr>
	<tr><td width="100%">
		<table width="100%" cellpadding="0" cellspacing="0" border="1">
			<tr>
				<td style="border:1px solid #000000;"><b><center>Landing Number</center></b></td>
				<td style="border:1px solid #000000;"><b><center>Package Name</center></b></td>
				<td style="border:1px solid #000000;"><b><center>Call Rate</center></b></td>
				<td style="border:1px solid #000000;"><b><center>Credit Limit</center></b></td>
				<td style="border:1px solid #000000;"><b><center>used</center></b></td>
				<td style="border:1px solid #000000;"><b><center>Rental</center></b></td>
				<td style="border:1px solid #000000;"><b><center>Addons Cost</center></b></td>
				<td style="border:1px solid #000000;"><b><center>Call Cost</center></b></td>
				<td style="border:1px solid #000000;"><b><center>Total Amount</center></b></td>
			</tr>
		 <?php foreach($arr['bill_section']->result_array() as $newres){
					if($newres['old']!=1){
				?>
				<tr>
					<td style="border:1px solid #000000;"><?=$newres['landingnumber']?></td>
					<td style="border:1px solid #000000;"><?=$newres['package_name']?></td>
					<td style="border:1px solid #000000;"><?=$newres['rate']?></td>
					<td style="border:1px solid #000000;"><?=$newres['climit']?></td>
					<td style="border:1px solid #000000;"><?=$newres['used']?></td>
					<td style="border:1px solid #000000;"><?=$newres['rental']?></td>
					<td style="border:1px solid #000000;"><?=$newres['addons_cost']?></td>
					<td style="border:1px solid #000000;"><?=$newres['call_cost']?></td>
					<td style="border:1px solid #000000;"><?=$newres['totalamount']?></td>
				</tr>
				<?php
				}
			 }
			 ?>
		
		</table>
	
	</td></tr>
	<tr><td><b>Previous Package balance</b></td></tr>
	<tr><td width="100%">
		<table width="100%" cellpadding="0" cellspacing="0" border="1">
			<tr>
				<td style="border:1px solid #000000;"><b><center>Landing Number</center></b></td>
				<td style="border:1px solid #000000;"><b><center>Package Name</center></b></td>
				<td style="border:1px solid #000000;"><b><center>Call Rate</center></b></td>
				<td style="border:1px solid #000000;"><b><center>Credit Limit</center></b></td>
				<td style="border:1px solid #000000;"><b><center>used</center></b></td>
				<td style="border:1px solid #000000;"><b><center>Rental</center></b></td>
				<td style="border:1px solid #000000;"><b><center>Addons Cost</center></b></td>
				<td style="border:1px solid #000000;"><b><center>Call Cost</center></b></td>
				<td style="border:1px solid #000000;"><b><center>Total Amount</center></b></td>
			</tr>
		 <?php foreach($arr['bill_section']->result_array() as $newres){
				if($newres['old']!=0){
				?>
				<tr>
					<td style="border:1px solid #000000;"><?=$newres['landingnumber']?></td>
					<td style="border:1px solid #000000;"><?=$newres['package_name']?></td>
					<td style="border:1px solid #000000;"><?=$newres['rate']?></td>
					<td style="border:1px solid #000000;"><?=$newres['climit']?></td>
					<td style="border:1px solid #000000;"><?=$newres['used']?></td>
					<td style="border:1px solid #000000;"><?=$newres['rental']?></td>
					<td style="border:1px solid #000000;"><?=$newres['addons_cost']?></td>
					<td style="border:1px solid #000000;"><?=$newres['call_cost']?></td>
					<td style="border:1px solid #000000;"><?=$newres['totalamount']?></td>
				</tr>
				<?php
			}
			 }
			 ?>
		
		</table>
	
	</td></tr>
	
	
</table>
</body>
 </html>

