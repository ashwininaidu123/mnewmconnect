<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php echo doctype('xhtml1-trans'); ?>
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
<title>
	<? if(isset($this->data['html']['title'])) { echo $this->data['html']['title'];} else { echo "MCube"; }
	   if(isset($title)) { echo " | ".$title;}
	?>
</title> 
<meta name="robots" content="no-cache" /> 
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />

<meta name="keywords" content="<? if(isset($keywords)) echo $keywords;?>" />
<meta name="description" content="<? if(isset($description)) echo $description;?>" />

<base href="<?=base_url()?>">
<?php // print_r($this->data['html']);exit;?>  
<link rel="shortcut icon" type="image/x-icon" href="system/application/img/icons/favicon.ico">
<link rel="stylesheet" type="text/css" href="system/application/css/theme5.css" />
<link rel="stylesheet" type="text/css" href="system/application/css/style.css" />
<link rel="stylesheet" type="text/css" href="system/application/css/style1.css" />
<link rel="stylesheet" type="text/css" href="system/application/css/ddsmoothmenu.css" />
<link rel="stylesheet" type="text/css" href="system/application/css/site.css" />
<link rel="stylesheet" type="text/css" href="system/application/css/contactable.css"  />
<link rel="stylesheet" type="text/css" href="system/application/assets/css/style1.css"  />
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-26443929-1']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type =
'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' :
'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0];
s.parentNode.insertBefore(ga, s);
  })();
</script>
	<link href="system/application/assets/css/icons/icons.css" rel="stylesheet">
    <link href="system/application/assets/css/plugins.min.css" rel="stylesheet">
  
	<meta name="google-site-verification" content="ap-4Vcs5Y1bI5OdR6dI5rKITbpnCmtdhojnSVvoZ_-U" />
	<script type="text/javascript" src="system/application/js/jquery-1.5.2.js"></script>
	<script type="text/javascript" src="system/application/js/ddsmoothmenu.js"></script>
	<script type="text/javascript" src="system/application/js/jquery.contactable.js"></script>
	<script type="text/javascript" src="system/application/assets/js/application.js"></script>
    <script src="assets/plugins/jquery-1.11.js"></script>
    <script src="system/application/assets/plugins/jquery-migrate-1.2.1.js"></script>
    <script src="system/application/assets/plugins/jquery-ui/jquery-ui-1.10.4.min.js"></script>
    <script src="system/application/assets/plugins/bootstrap/bootstrap.min.js"></script>
    <script src="system/application/assets/plugins/backstretch/backstretch.min.js"></script>
    <script src="system/application/assets/js/account.js"></script>

<script language="javascript" type="text/javascript">
   $(document).ready(function(){
		$('.signout').live('click',function(event){
			var conf=confirm("Are you sure to signout from here");
			if(conf){
				window.location.href=$('.signout').attr('href');
				return true;
			}else{
				return false;
			}
		}),
		$('#my-contact-div').contactable(
        {
            subject: 'feedback URL:'+location.href,
            url: 'user/feedbackData',
            name: 'Name',
            email: 'Email',
            phone: 'Phone',
            dropdownTitle: 'Category',
            dropdownOptions: [<?php
             echo '"Select",';
			   for($i=0,$j=1;$i<count($this->data['fcategories']);$i++,$j++){
				   echo ($this->data['fcategories'][$i]['category'] != '') ? '"'.$this->data['fcategories'][$i]['category'].'"' : "";
				   echo ($j == count($this->data['fcategories'])) ? '' :',' ;
			   }
			   ?>],
			
            message : 'Message',
            submit : 'SEND',
            recievedMsg : 'Thank you for your message',
            notRecievedMsg : 'Sorry but your message could not be sent, try again later',
           /* disclaimer: 'Please feel free to get in touch, we value your feedback',*/
            disclaimer:'',
            hideOnSubmit: true
        });
	  });
</script>	
<script type="text/javascript">
ddsmoothmenu.init({
	mainmenuid: "topmenu", //menu DIV id
	orientation: 'h', //Horizontal or vertical menu: Set to "h" or "v"
	classname: 'ddsmoothmenu', //class added to menu's outer DIV
	//customtheme: ["#1c5a80", "#18374a"],
	contentsource: "markup" //"markup" or ["container_id", "path_to_menu_file"]
});
<? if($this->session->userdata('logged_in')){ ?>
ddsmoothmenu.init({
	mainmenuid: "topmenu1", //menu DIV id
	orientation: 'h', //Horizontal or vertical menu: Set to "h" or "v"
	classname: 'toplinksM', //class added to menu's outer DIV
	//customtheme: ["#1c5a80", "#18374a"],
	contentsource: "markup" //"markup" or ["container_id", "path_to_menu_file"]
});
<? }?>



</script>
<style>
.ddsmoothmenu ul li a{
padding:0px 13px;
}
</style>
</head> 
<body>
	<!--start contactable -->
<div id="my-contact-div"><!-- contactable html placeholder --></div>

	<div class="pagecontent">
	<div class="header1">
		<table class="headertable">
		
					<tr><td valign="bottom">
										
							<div id="topmenu" class="ddsmoothmenu">
							<ul>
								
							</ul>
						</div> 
					
					</tr>
				</table>
	
		</div>
		
</div>
