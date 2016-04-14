<html>
	<head>
		<title>MCube - Click 2 Connect</title>
		<script src="/system/application/js/jquery-1.5.2.js" language="javascript" type="text/javascript" ></script>
		<script src="/system/application/js/connectcall.js" language="javascript" type="text/javascript" ></script>
	</head>
	<body style="margin:0 auto; padding: 0;width:310px;" bgcolor="#1F211E";>
		<?php echo $form['open'];?>
		<table style="margin-top:10px;width:100%;font-family: 'open sans', Arial, Helvetica, Geneva, sans-serif;font-size:12px; overflow:hidden;" cellpadding="0" cellspacing="0" bgcolor="#FFF">
			<tr>
				<td>
					<table style="margin:0px;width:100%;font-family: 'open sans', Arial, Helvetica, Geneva, sans-serif;font-size:14px;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:100%;height:80px;float:left;overflow:hidden;color:#000;">
								<table cellpadding="0" cellspacing="0"  style="width:100%" border="0">
									<tr>
										<td align="left">
											<img style="margin-left:20px;margin-top:25px;" src="https://mcube.vmc.in/system/application/img/mcubelogoicn.png" >
										</td>
										<td align="right">
											<div style="color:#FF9900;font-size:18px;font-weight: bold;margin-right:30px;margin-top:25px;">Click 2 Call </div>
										</td>		
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="background:#FFF;width:90%;color:#000;vertical-align:top;padding:0px 20px;font-size:13px;">
								<table align="center" cellpadding="0" cellspacing="0" style="margin-top:0px;width:80%;">
									<tr>
										<td>			
											<table align="center" cellpadding="0" cellspacing="0" border="0" style="margin:10px 0px;font-size:14px;">
												<tr style="height:15px;"><td colspan="3" align='right'>
														<span id="error" style='color:#CC0000;font-size:10px;'></span>
													</td>
												</tr>
												<tr>
													<td style="font-size:13px;font-family: 'open sans', Arial, Helvetica, Geneva, sans-serif;">
														
													</td>
													<td>
														<input type="text" name="code" id="code" readonly value="+91" style="width:40px;" />
													</td>
													<td>
														<input type="text" name="number" id="number" value="" size="18" maxlength="10" />
													</td>
												</tr>
												
												<tr>
													<td colspan="2">&nbsp;</td>
													<td style="font-size:9px; padding:3px;color:#333;font-family: 'open sans', Arial, Helvetica, Geneva, sans-serif;" align="right">
														<i>Enter your number without a </br>preceding 0 or country code.</i>
													</td>
												</tr>
												<tr style="height:15px;"><td colspan="3" align='right'></td>
												</tr>
												<tr>
													<td colspan="3" align="right">
															<input type="submit" value="Call Me" name="callme" id="callme" style="border:none;padding:4px 15px;cursor:pointer;margin:0px 2px;font-weight:bold;color:#646466;background:url(http://mcube.vmc.in/system/application/img/button_bg.jpg);border-radius:5px;-moz-border-radius:5px;"/>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
									<?php echo $form['close'];?>
							</td>
						</tr>
						<tr>
							<td bgcolor="#1F211E">
								<table border="0" width="100%" align="center" cellpadding="0" cellspacing="0">
								  <tr>
									<td align="right" style='color: #FFF;font-family: "open sans", Arial, Helvetica, Geneva, sans-serif;font-size:12px;margin-bottom:0px;'>Powered By MCube</td>
								  </tr>	
								</table>
							</td>
						</tr>
				    </table>
				</td>
			</tr>
		</table>
	</body>
</html>
