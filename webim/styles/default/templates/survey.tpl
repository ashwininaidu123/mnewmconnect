<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>${msg:presurvey.title}</title>
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
	font-weight: bold;
	background:url(${tplroot}/images/wmchat.png) top left no-repeat;
	background-position:0px -25px;
	display:block;
	text-align:center;
	padding-top:2px;
	color:white;
	width:186px;
	height:18px;
	text-decoration:none;
}
</style>

</head>
<body bgcolor="#FFFFFF" text="#000000" link="#C28400" vlink="#C28400" alink="#C28400" style="margin:0px;">
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td valign="top">


<form name="surveyForm" method="post" action="${webimroot}/client.php">
<input type="hidden" name="style" value="${styleid}"/>
<input type="hidden" name="info" value="${form:info}"/>
<input type="hidden" name="referrer" value="${page:referrer}"/>
<input type="hidden" name="survey" value="on"/>
${ifnot:showemail}<input type="hidden" name="email" value="${form:email}"/>${endif:showemail}
${ifnot:groups}${if:formgroupid}<input type="hidden" name="group" value="${form:groupid}"/>${endif:formgroupid}${endif:groups}
${ifnot:showmessage}<input type="hidden" name="message" value="${form:message}"/>${endif:showmessage}
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
				<span class="title_chat">${msg:presurvey.title}</span> 
			</td>
			</tr>
			</table>
			
			<table width="65%" cellspacing="0" cellpadding="0" border="0" align="center">
			<tr>
				<td class="chat_notify">
					${msg:presurvey.intro}
				</td>
			</tr>
			</table>
	</td>
</tr>
<tr>
	<td valign="top" style="padding:0px 24px;">
${if:errors}
		<table width="65%" cellspacing="1" cellpadding="5" border="0" align="center">
		<tr>
	    <td class="text">
		    ${errors}
		</td>
		</tr>
		</table>
${endif:errors}

		<table width="65%" cellspacing="1" cellpadding="5" border="0" align="center">
${if:groups}
			<tr>
				<td class="text">${msg:presurvey.department}</td>
				<td>
				<select name="group" style="min-width:200px;">${page:groups}</select>
				</td>
			</tr>
${endif:groups}

			<tr>
				<td class="text"  style="font-size:11px;">${msg:presurvey.name}</td>
				<td><input type="text" name="name" size="39" value="${form:name}" class="username" ${ifnot:showname}disabled="disabled"${endif:showname}/></td>
			</tr>
			<tr>
				<td class="text">${msg:presurvey.company}</td>
				<td><input type="text" name="company" size="50" value="${form:company}" class="username"/></td>
			</tr>
			<tr>
				<td class="text">${msg:presurvey.phone}</td>
				<td><input type="text" name="phone" size="50" value="${form:phone}" class="username"/></td>
			</tr>

${if:showemail}
			<tr>
				<td class="text">${msg:presurvey.mail}</td>
				<td><input type="text" name="email" size="50" value="${form:email}" class="username"/></td>
			</tr>
${endif:showemail}
			
${if:showmessage}			
			<tr>
				<td class="text"  style="font-size:11px;">${msg:presurvey.question}</td>
				<td valign="top">
					<textarea name="message" tabindex="0" cols="35" rows="3" style="border:1px solid #878787; overflow:auto" class="username">${form:message}</textarea>
				</td>
			</tr>
${endif:showmessage}			
			<tr>
				<td colspan="2" align="right" style="padding-top:20px; padding-right:6px;">
					<table cellspacing="0" cellpadding="0" border="0">
					<tr>
					<td><span class="cht_button"><a href="javascript:document.surveyForm.submit();" id="sndmessagelnk">${msg:presurvey.submit}</a></span></td>
					</tr>
					</table>
				</td>
			</tr>
		</table>

	</td>
</tr>
</table>
</form>
</td>
</tr>
<tr><td height="150"></td></tr>
<tr>
<td>
	<table align="center" style="font-family:arial,helvetica,sans-serif;font-size:10px;color:#CCC;" >
	<tr>
	<td > Powered By <a href="http://mcube.vmc.in">VMC Technologies</a></td>
	</tr>
	</table>
</td>
</tr>
</table>
</body>
</html>
