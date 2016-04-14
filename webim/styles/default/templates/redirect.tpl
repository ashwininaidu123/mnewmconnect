<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>${msg:chat.window.title.agent}</title>
<link rel="shortcut icon" href="${webimroot}/images/favicon.ico" type="image/x-icon"/>
<link rel="stylesheet" type="text/css" href="${tplroot}/chat.css" />
<style type="text/css">
#header{
	height:50px;
	background:url(${tplroot}/images/bg_domain.gif) repeat-x top;
	background-color:#5AD66B;
	width:99.6%;
	margin:0px 0px 20px 0px;
}
#header .mmimg{
	background:url(${tplroot}/images/quadrat.gif) bottom left no-repeat;
}
.form td{
	background-color:#f4f4f4;
	color:#525252;
}
.but{
	font-family:Verdana !important;
	font-size:11px;
	background:url(${tplroot}/images/butbg.gif) no-repeat top left;
	display:block;
	text-align:center;
	padding-top:2px;
	color:white;
	width:80px;
	height:18px;
	text-decoration:none;
	position:relative;top:1px;
}
</style>
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#C28400" vlink="#C28400" alink="#C28400" style="margin:0px;">
<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top" height="150" style="padding:5px">
			<table cellspacing="0" cellpadding="0" border="0" style="position:absolute; padding:20px 0px 20px 20px; ">
				<tr>
				 <td>
					${if:ct.company.chatLogoURL}
						${if:webimHost}
							<a onclick="window.open('${page:webimHost}');return false;" href="${page:webimHost}">
								<img src="${page:ct.company.chatLogoURL}" border="0" alt=""/>
							</a>
						${else:webimHost}
							<img src="${page:ct.company.chatLogoURL}" border="0" alt=""/>
						${endif:webimHost}
					${else:ct.company.chatLogoURL}
						${if:webimHost}
							<a onclick="window.open('${page:webimHost}');return false;" href="${page:webimHost}">
								<img src="${webimroot}/images/webimlogo.gif" border="0" alt=""/>
							</a>
						${else:webimHost}
							<img src="${webimroot}/images/webimlogo.gif" border="0" alt=""/>
						${endif:webimHost}
					${endif:ct.company.chatLogoURL}
				</td>  
			</tr>  
			</table>
			 <table cellspacing="0" cellpadding="0" border="0" align="center" style="margin-top:40px;margin-bottom:20px;">
			   <tr>
				<td> 
					<span class="title_chat">${msg:chat.redirect.title}</span> 
			    </td>
			   </tr>
			</table>
			<table width="65%" cellspacing="0" cellpadding="0" border="0" align="center">
				<tr>
					<td class="chat_notify">
						${msg:chat.redirect.choose}
					</td>
					<td align="right" style="padding-right:17px;">
						<table cellspacing="0" cellpadding="0" border="0">
						<tr>
						<td class="button"><a href="javascript:window.back();" title="${msg:chat.redirect.back}">${msg:chat.redirect.back}</a></td>
						</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top" style="padding-left:130px;" align="left">

				<table width="100%" cellpadding="0" align="center">
				<tr>
				<td width="50%" valign="top">
	${if:redirectToAgent}
				
				<ul>
					${page:redirectToAgent}
				</ul>
	${endif:redirectToAgent}
				</td>
				<td width="50%" valign="top">
	${if:redirectToGroup}
				${msg:chat.redirect.group}<br/>
				<ul>
					${page:redirectToGroup}
				</ul>
	${endif:redirectToGroup}
				</td>
				</tr>
				</table>

		</td>
	</tr>
	<tr><td height="50"></td></tr>
	<tr>
		<td valign="top" align="center" style="font-size:11px;color:#999;">
			${pagination}
		</td>
	</tr>
	<tr><td height="190"></td></tr>
<tr>
<td>
	<table align="center" style="font-family:arial,helvetica,sans-serif;font-size:10px;color:#CCC;" >
	<tr>
	<td> Powered By <a href="http://mcube.vmc.in">VMC Technologies</a></td>
	</tr>
	</table>
</td>
</tr>
</table>
</body>
</html>
