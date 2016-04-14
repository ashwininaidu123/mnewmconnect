<link rel="stylesheet" href="<?=base_url();?>css/validationEngine.jquery.css" type="text/css"/>
<script src="<?=base_url();?>js/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script>
<script src="<?=base_url();?>js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
 <script>
jQuery(document).ready(function(){
	jQuery("#pricing").validationEngine();
	
});
</script>
<?php if(!$this->session->flashdata('smsg')){?>
<script language="javascript" type="text/javascript">
   $(document).ready(function(){
         $(".session_message").show();
         $(".session_message").fadeOut(5000);
   });
</script>
<? }?>
<div style="text-align:center;width:100%;margin-bottom:20px;">
<a href="VMC_Brochure.pdf" class="redlink">Corporate brochure</a>
<a href="VMC_Brochure.pdf">
<img src="images/pdf_vmc.jpg" border="0">
</a>
</div>
<p class="contentsubhead">Need Assistance</p>
<form id="pricing" name="pricing" method="POST" action="user/assistance" autocomplete="off">
<div class="sterror"><?php if(!isset($_POST['sendemail'])) echo validation_errors(); ?></div>
<?php if($this->session->flashdata('msg')){?>
<div <?=($this->session->flashdata('msgt'))?'style="display:inline;"':''?> class="session_message <?php echo $this->session->flashdata('msgt');?>"><span><?php echo $this->session->flashdata('msg');?></span></div>
<? }?>
<table border="0" class="formtablecontactus"> 
<tr><td>First name:</td><td colspan="2"><input type="text" name="firstname" id="firstname" class="formfieldcontactus validate[required]"></td></tr>
<tr><td>Last name:</td><td colspan="2"><input type="text" name="lastname" id="lastname" class="formfieldcontactus validate[required]"></td></tr>
<tr><td>Job title:</td><td colspan="2"><input type="text" name="jtitle" id="jtitle" class="formfieldcontactus validate[required]"></td></tr>
<tr><td>Email:</td><td colspan="2"><input type="text" name="email" id="email" class="formfieldcontactus validate[required,custom[email]]"></td></tr>
<tr><td>Phone:</td><td colspan="2"><input type="text" name="phone" id="phone" class="formfieldcontactus validate[required]"></td></tr>
<tr><td>Company:</td><td colspan="2"><input type="text" name="company" id="company" class="formfieldcontactus validate[required]"></td></tr>
<tr><td>Employees:</td><td colspan="2">
<select name="employee" id="employee" class="formfieldcontactus validate[required]">
<option value="">[--select one--]</option>
<option value="1-100">1 - 100 employees</option>
<option value="101-500">101 - 500 employees</option>
<option value="501-1000">501 - 1000 employees</option>
<option value="1001-1500">1001 - 1500 employees</option>
<option value="1501+">1501 + employees</option>
</select></td></tr>
<tr><td>Zip code:</td><td colspan="2"><input type="text" name="zipcode" id="zipcode" class="formfieldcontactus validate[required]"></td></tr>
<tr><td nowrap>Product Interest</td><td width="28"> <input type="checkbox" name="product[]" id="product1" value="AI Products" class="validate[minCheckbox[1]] checkbox"></td><td><label for="product1">All Products</label></td></tr>
<tr><td>&nbsp; </td><td> <input type="checkbox" name="product[]" id="product2" value="MCube Track" class="validate[minCheckbox[1]] checkbox"></td><td><label for="product2">MCube Track - CallTrack</label></td></tr>
<tr><td>&nbsp; </td><td> <input type="checkbox" name="product[]" id="product3" value="MCube X" class="validate[minCheckbox[1]] checkbox"></td><td><label for="product3">MCube X - PBX</label></td></tr>
<tr><td>&nbsp; </td><td> <input type="checkbox" name="product[]" id="product4" value="MCube IVRS" class="validate[minCheckbox[1]] checkbox"></td><td><label for="product4">MCube IVRS</label></td></tr>
<tr><td colspan="3" valign="top">Questions/Comments </td></tr>
<tr><td colspan="3" valign="top"><textarea cols="26" rows="5" name="Questions" id="Questions" class="validate[required]"></textarea></td></tr>
<tr>
	<td valign="top">Captcha   </td>
	<td colspan="2">
		<input type="text" name="validator" class="validate[required,ajax[ajaxCaptchaCall]]" id="validator" style="width:100px;margin-bottom:3px;" /><br/>
		<img src="captcha.php?width=100&height=30&characters=7" id="cimg" />
	</td>
</tr>
<tr><td colspan="3"><input class="btn" type="submit" value="submit" name="submit" /></td></tr>
<tr><td colspan="3">
<script type="text/javascript">
		mcube_client_id = "03d029891ac9cf52a47a2834ac9c1b78";
		mcube_wiz_width = 560;
		mcube_wiz_height = 430;
		mcube_wiz_btn_id = "ClickToCallBtn";
		</script><script type="text/javascript" src="https://mcube.vmc.in/show.js"></script>
        <img src="https://mcube.vmc.in/images/call_me_button.png" id="ClickToCallBtn" />
</td>

</tr>
</table>
</form>
</div>
