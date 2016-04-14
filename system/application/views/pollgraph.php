<?php
	include_once 'open_flash_chart_object.php';
	$url1=site_url('system/application/');
 ?>
 <table align="center" width="800" cellpadding="0" cellspacing="0">
	<tr>
		<td><?php open_flash_chart_object( 800, 400, 'poll/resultpoll/'.$form['poll_id'], false,$url1);?></td>
	</tr>
	<tr>
		<td align="center"><b><?=$form['result']?></b></td>
	</tr>
 </table>

