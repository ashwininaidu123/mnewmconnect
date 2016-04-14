<link href="http://local.vmc.in/system/application/css/jquery.ui.all.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript">
   jQuery(document).ready(function(){
	jQuery("#supportForm").validationEngine();
	
});
</script>
<?php if($this->session->flashdata('smsg')){?>
<script language="javascript" type="text/javascript">
   $(document).ready(function(){
         $(".session_message").show();
         $(".session_message").fadeOut(5000);
   });
</script>
<? }?>
<div class="pagecontent">
<div class="columnblock">
<div class="column1">
	
	<div class="supportdbanner"></div>
	<div class="column1block">
		<p class="contenthead" style="font-size:22px;">Send us Email at support@vmc.in (or) Submit a Request</p>
		<form enctype="multipart/form-data"  id="supportForm" name="supportForm" method="post" action="site/support">
			<div class="sterror"><?php 
					echo validation_errors(); ?></div>
			<?php if($this->session->flashdata('msgt')){?>
				<div <?=($this->session->flashdata('msgt'))?'style="display:inline;"':''?> class="session_message <?php echo $this->session->flashdata('msgt');?>"><span>&nbsp;<?php echo $this->session->flashdata('smsg');?></span></div>
			<? }?>
			<table border="0" cellpadding="5" cellspacing="0" width="100%" class="formtablecontactus"> 
			<tr>
				<td>Your Name<span class="starrequied">*</span></td>
				<td>: <input type="text" name="name" id="name" class="validate[required]" value="<?=$this->input->post('name')?>"/></td>
				<td></td>
			</tr>
			<tr>
				<td>Your Email Address<span class="starrequied">*</span></td>
				<td>: <input type="text" name="email" id="email" class="validate[required,custom[email]]" value="<?=$this->input->post('email')?>"/></td>
				<td></td>
			</tr>
			<tr>
				<td>Your Mobile Number<span class="starrequied">*</span></td>
				<td>: <input type="text" name="mobile" id="mobile" class="validate[required,custom[phone]]" value="<?=$this->input->post('mobile')?>"/></td>
				<td></td>
			</tr>
			<tr>
				<td>Your Company</td>
				<td>: <input type="text" name="company" id="company" class="required" value="<?=$this->input->post('company')?>"/></td>
				<td></td>
			</tr>
			<tr>
				<td>MCube Group</td>
				<td>: <input type="text" name="mgroup" id="mgroup" class="required" value="<?=$this->input->post('mgroup')?>"/></td>
				<td></td>
			</tr>
			<tr>
				<td>MCube Group Phone Number</td>
				<td>: <input type="text" name="mnumber" id="mnumber" class="validate[custom[phone]]" value="<?=$this->input->post('mnumber')?>"/></td>
				<td></td>
			</tr>
			<tr>
				<td>Subject</td>
				<td>: <input type="text" name="subject" id="subject" class="" value="<?=$this->input->post('subject')?>"/></td>
				<td></td>
			</tr>
			<tr>
				<td>Description</td>
				<td>: <textarea name="description" id="description" rows="8" cols="35"><?=$this->input->post('description')?></textarea></td>
				<td></td>
			</tr>
			<tr>
				<td>Attachment</td>
				<td>: <input type="file" name="attachments" id="attachments" /></td>
				<td></td>
			</tr>
			<tr>
				<td>Captcha</td>
				<td>: <input type="text" name="captchas" class="validate[required,ajax[ajaxCaptchaCall]]" id="captchas" style="width:100px" />
				<img src="captcha.php?width=100&height=30&characters=7" id="cimg" /></td>
				<td></td>
			</tr>
			<tr>
				<td colspan="3" align="center">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3" align="center">
					<input class="btn btn-primary" type="submit" value="Send" name="sendemail" id="sendemail" />
					&nbsp;
					<input class="btn btn-default" type="reset" value="reset" name="reset" />
				</td>
			</tr>
			</table>
		</form>
	</div>
 </div>

<div class="column2"> 
<? $this->load->view('rightform')?>
</div>
</div>
</div>

