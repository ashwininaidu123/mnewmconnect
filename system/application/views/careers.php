<link rel="stylesheet" href="<?=base_url();?>css/validationEngine.jquery.css" type="text/css"/>
<script src="<?=base_url();?>js/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script>
<script src="<?=base_url();?>js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
 <script>
jQuery(document).ready(function(){
	jQuery("#form").validationEngine();
	
});
</script>
<div class="pagecontent">
<div class="columnblock">
<div class="column1">
	<div class="careersbanner"></div>
	<div class="column1block">
		<p class="contenthead">Careers</p>
		<p class="contentsubhead">WHY WORK AT VMC?</p>
		<p  class="contenttext">VMC is a fast-paced environment for motivated people. Do you like technology, innovation and distributed components? If your answer is yes then you have arrived at the right place.</p>
		<p  class="contenttext"><strong>We innovate, build and sell cool products and applications</strong>. We write high performing, scalable and secure web based products and applications to help businesses increase productivity. Using API’s for our products applications can communicate in real time with customers to improve customer engagement. Can it get cooler and better than that? We think not.</p>
		<p  class="contenttext">Our technology is also used by developers innovate and do what no one else has done before. We think this is very cool.</p>
		<p class="contentsubhead">Our People</p>
		<p  class="contenttext">We take a lot of pride in our people – We hand pick people who like to innovate and give value to client.</p>
		<p class="contentsubhead">Our Requirements</p>
		<p  class="contenttext">We are continuously hiring in the area of technology, QA, support and sales across India. If you have desire to join a growing company that is trying to innovate to improve productivity of SMB’s then fill up the following form and we will get in touch with you.</p>
		
		
		<form name="form" id="form" method="post" action="user/careers" enctype="multipart/form-data">

		<table>
			<tr><td colspan="2"><h3>Please fill in the form below</h3></td></tr>
			<tr><td colspan="2"><div class="sterror"><?php echo validation_errors(); ?>
									
									<?php if($this->session->flashdata('msgt')){?>
<div <?=($this->session->flashdata('msgt'))?'style="display:inline;"':''?> class="session_message <?php echo $this->session->flashdata('msgt');?>"><span><?php echo $this->session->flashdata('msg');?></span></div>
<? }?>
						</div></td></tr>
			<tr><td>First Name</td><td><input type="text" size="15" class="formfield validate[required]" name="frstname" id="frstname"></td></tr>
			<tr><td>Last Name</td><td><input type="text" size="15" class="formfield validate[required]" name="lstname" id="lstname"></td></tr>
			<tr><td>Mobile Number</td><td><input type="text" size="15" class="formfield validate[required,custom[phone]]" name="mobnumber" id="mobnumber"></td></tr>
			<tr><td>Land Line Number</td><td><input type="text" size="15" class="formfield" name="landline" id="landline"></td></tr>
			<tr><td>Email</td><td><input type="text" size="15" class="formfield validate[required,custom[email]]" name="cemail" id="cemail"></td></tr>
			<tr><td>Expertise</td><td><input type="text" size="15" class="formfield validate[required]" name="expertise" id="expertise"></td></tr>
			<tr><td>Short Description</td><td><textarea cols="11" rows="3" class="formfield validate[required]" name="messag" id="messag"></textarea></td></tr>
			<tr><td>Upload Resume</td><td><input type="file" name="resume" id="resume" class="validate[required]" ></td></tr>
			<tr><td></td><td><input class="btn" type="submit" value="Submit"></td></tr>
		</table>
		</form>
	</div>
 </div>

<div class="column2"> 
<? $this->load->view('rightform')?>
</div>
</div>
</div>
