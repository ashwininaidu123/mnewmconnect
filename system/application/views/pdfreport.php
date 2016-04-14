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
						<tr><td><?=$res->businessname?></td></tr>
						<tr><td><?=$res->businessaddress?></td></tr>
						<tr><td><?=$res->state?></td></tr>
						<tr><td><?=$res->city.' - '.$res->zipcode?></td></tr>
							<tr><td style="color:#fff;">********</td></tr>
					</table>
				</td>
				<td width="50%" align="right">
					
						<table width="100%" align="right" cellpadding="0" cellspacing="0" border="0">
							<tr><th align="right">Business Id :</th><td align="right"><?=$res->bid?></td></tr>
							<tr><th align="right">Bill No :</th><td align="right"><?=$res->bill_id?></td></tr>
							<tr><th align="right">Bill date :</th><td align="right"><?=$res->bill_generate_date?></td></tr>
							<tr><th align="right">Billing Period :</th><td align="right"><?=$res->billing_form." to ".$res->billing_to?></td></tr>
							<tr><th align="right">Due date :</th><td align="right"><?=$res->due_date?></td></tr>
						</table>
				</td>
			</tr>
		</table>
	</td></tr>
	<tr><td style="color:red;"><hr/></td></tr>
	<tr><td><b>Your Account Summary</b></td></tr>
	<tr><td>
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td align="center" style="border:1px solid #000000;"><b>previous balanace</b><br/>
						<?=number_format($res->arrear,2)?></td>
						<td align="center" style="border:1px solid #000000;"><b>payments</b>
						<br/><?=number_format($res->gross_amount,2)?></td>
						<td align="center" style="border:1px solid #000000;"><b>adjustments</b>
						<br/><?=number_format($res->discount,2)?></td>
						<td align="center" style="border:1px solid #000000;"><b>this month's charges</b>
						<br/><?=number_format($res->netamount-$res->tax,2)?></td>
						<td align="center" style="border:1px solid #000000;"><b>amount payable </b><br/><?=round($res->netamount,2)?>
						</td>
					</tr>
				</table>
	
		</td>
	</tr>
	<tr>
		<td><b>this month's charges</b></td>
	
	</tr>
	<tr><td> 
			<?php
				
				//$tax=$tax/100*$res->gross_amount;
			
			
			?>
		<table width="100%" cellpadding="0" cellspacing="0" border="1">
					<tr>
						<td width="70%" style="border:1px solid #000000;">&nbsp;&nbsp;&nbsp;Rentals</td>
						<td width="30%" style="border:1px solid #000000;" align="right"><?=$conc->rental?></td>
					</tr>
					<tr>
						<td width="70%" style="border:1px solid #000000;">&nbsp;&nbsp;&nbsp;Call Rate for per pulse</td>
						<td width="30%"  style="border:1px solid #000000;"align="right"><?=$rate->rate?></td>
					</tr>
					<tr>
						<td width="70%" style="border:1px solid #000000;">&nbsp;&nbsp;&nbsp;Pulse</td>
						<td width="30%"  style="border:1px solid #000000;"align="right"><?=$p->pulsecnt?></td>
					</tr>
					<tr>
						<td width="70%" style="border:1px solid #000000;">&nbsp;&nbsp;&nbsp;Gross Charges</td>
						<td width="30%"  style="border:1px solid #000000;"align="right"><?=$res->gross_amount?></td>
					</tr>
					<tr>
						<td width="70%" style="border:1px solid #000000;">&nbsp;&nbsp;&nbsp;Discounts</td>
						<td width="30%"  style="border:1px solid #000000;"align="right"><?=$res->discount?></td>
					</tr>
					<tr>
						<td width="70%" style="border:1px solid #000000;">&nbsp;&nbsp;&nbsp;Service Tax (<?php echo $tax;?>) </td>
						<td width="30%"  style="border:1px solid #000000;"align="right"><?
										
										echo $res->tax;?></td>
					</tr>
					<tr>
						<td width="70%" style="border:1px solid #000000;" align="right">&nbsp;&nbsp;&nbsp;Total</td>
						<td width="30%"  style="border:1px solid #000000;"align="right"><?=number_format($res->netamount,2)?></td>
					</tr>
					
		</table>	
		</td>
		</tr>	
		<tr><td><b>Your Associated Numbers</b></td></tr>
	<tr><td width="100%">
		<table width="100%" cellpadding="0" cellspacing="0" border="1">
			<tr>
				<td style="border:1px solid #000000;"><b><center>Landing Number</center></b></td>
				<td style="border:1px solid #000000;"><b><center>Call Rate</center></b></td>
				<td style="border:1px solid #000000;"><b><center>Pulse</center></b></td>
				<td style="border:1px solid #000000;"><b><center>Amount</center></b></td>
			</tr>
			 <?php foreach($assoc as $newres){
				?>
				<tr>
					<td style="border:1px solid #000000;"><?=$newres['landingnumber']?></td>
					<td style="border:1px solid #000000;"><?=$newres['rate']?></td>
					<td style="border:1px solid #000000;"><?=$newres['pulse']?></td>
					<td style="border:1px solid #000000;"><?=$newres['totalamount']?></td>
				</tr>
				<?php
			 }
			 ?>
		
		</table>
	</td></tr>
	
</table>
</body>
 </html>
