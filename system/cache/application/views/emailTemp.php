<?
$content='
		<html><head>
		</head>
			<body style="margin:0 auto; padding: 0;width:600px;">
			<table style="margin:0px;width:100%;font-family: Helvetica Neue, Arial, Helvetica, Geneva, sans-serif;font-size:14px;" cellpadding="0" cellspacing="0">
			<tr><td style="background:#000; width:100%;height:80px;float:left;
			-webkit-border-top-left-radius: 10px;-webkit-border-top-right-radius: 10px;
			-moz-border-radius-topleft: 10px;-moz-border-radius-topright: 10px;
			border-top-left-radius: 10px;border-top-right-radius: 10px;overflow:hidden;
			color:#000;"><img src="http://mcube.vmc.in/qrcode/company_logos/856265314vmclogo.gif" style="margin-top:-20px;"></td></tr>
			<tr><td style="background:#FFF;width:100%;color:#000;border:1px solid #000;height:300px;vertical-align:top;padding:10px;">
				<p>&nbsp;</p>
				<p style="font-size: 18px; line-height:24px; color: #b0b0b0; font-weight:bold; margin-top:0px; margin-bottom:18px; font-family: Helvetica Neue, Arial, Helvetica, Geneva, sans-serif;text-indent:0.5cm;" align="left"><singleline label="Title">Hi Dinesh</singleline></p>
				<div style="font-size: 13px; line-height: 18px; color: #444444; margin-top: 0px; margin-bottom: 18px; font-family: Helvetica Neue, Arial, Helvetica, Geneva, sans-serif; text-indent:1cm;" align="left">
													
													<multiline label="Description">Your Account has been successfully created with MCube for Dinesh Kumar Pose.<br><br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Please find Login Details<br>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Username: dineshpose@gmail.com<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Password: 123456<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				Note :Please change your password after first login<br>
					</div>
				<p style="font-size: 18px; line-height:24px; color: #0004; font-weight:bold; margin-top:0px; margin-bottom:18px; font-family: Helvetica Neue, Arial, Helvetica, Geneva, sans-serif;text-indent:0.5cm;"><singleline label="Title">Regards, </singleline></p>									
				<p style="font-size: 18px; line-height:24px; color: #0004; font-weight:bold; margin-top:0px; margin-bottom:18px; font-family: Helvetica Neue, Arial, Helvetica, Geneva, sans-serif;text-indent:0.5cm;"><singleline label="Title">Mcube Team </singleline></p>									
												

			</td></tr>
			<tr><td style="background:#000;width:100%;height:40px;
			-webkit-border-bottom-left-radius: 10px;-webkit-border-bottom-right-radius: 10px;
			-moz-border-radius-bottomleft: 10px;-moz-border-radius-bottomright: 10px;
			border-bottom-left-radius: 10px;border-bottom-right-radius: 10px;
			color:#fff;padding:10px;font-size:12px;font-weight:bold;text-align:right;"><a href="http://mcube.vmc.in/" style="color:#FFF;text-decoration:none;">VMC Technologies</a></td></tr>
			</table>
			</body></html>';
$to  = 'dinesh3058@gmail.com'; // note the comma
$subject = 'Account created with MCube';
$from='MCube <noreply@vmc.in>';
echo $message = $content;
$headers = 'MIME-Version: 1.0' . "\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
$headers .= 'From:'.$from. "\n";
mail($to, $subject, $message, $headers);
?>
