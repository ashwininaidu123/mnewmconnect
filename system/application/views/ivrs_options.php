<script>
	$(function() {
		$("#targettype").change(function() {
			if(this.value == "employee")
				$("#empid").fadeIn(500);
			else
				$("#empid").fadeOut(500);
		});
		$("#form").validate({
			rules: {
				opttext	: "required",
				optorder: {
						required: true,
						number:true,
						max	  :9
					},
				sound	: {
						required: true,
						accept:'wav'
					}
			},
			messages: {
				opttext	: "<?=$this->lang->line('error_required')?>",
				optorder: {
						required	: "<?=$this->lang->line('error_required')?>",
						number		: "<?=$this->lang->line('error_number')?>"
					},
				sound	: {
						required	: "<?=$this->lang->line('error_required')?>",
						accept		: "<?=$this->lang->line('error_soundfile')?>"
					}			
			},
			errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			}
		});
	});
</script>
<div id="box">
<h3><?=$this->lang->line('ivrs_options')?></h3>
<form id="form" action="ivrs/addoptions" method="post" enctype="multipart/form-data">
<input type="hidden" name="ivrsid" value="<?=$this->uri->segment(3)?>" />
<input type="hidden" name="parentopt" value="<?=$this->uri->segment(4)?>" />
<fieldset id="prinumber">
<legend><?=$this->lang->line('ivrs_addoptions')?></legend>
<table>
	<tr>
		<th><label for="optorder"><?=$this->lang->line('ivrs_optorder')?> : </label></th>
		<td><input name="optorder" id="optorder" type="text" /></td>
		<td></td>
	</tr>
	<tr>
		<th><label for="opttext"><?=$this->lang->line('ivrs_opttext')?> : </label></th>
		<td><input name="opttext" id="opttext" type="text" /></td>
		<td></td>
	</tr>
	<tr>
		<th valign="top"><label for="sound"><?=$this->lang->line('ivrs_optsound')?> : </label></th>
		<td>
			<!--<embed src="http://localhost/cal/sounds/rappin.wav"
				volume="100" loop="true"
				controls="console" height="29"
				autostart="FALSE" width="256" hidden="false">
			</embed><br>-->
			<input name="sound" id="sound" type="file" />
		</td>
		<td></td>
	</tr>
	<tr>
		<th><label for="targettype"><?=$this->lang->line('ivrs_target')?> : </label></th>
		<td>
			<select name="targettype" id="targettype">
				<option value="list"><?=$this->lang->line('ivrs_list')?></option>
				<option value="employee"><?=$this->lang->line('ivrs_emp')?></option>
				<option value="hangup"><?=$this->lang->line('ivrs_hangup')?></option>
			</select>
		</td>
		<td></td>
	</tr>
	<tr id="empid" style="display:none">
		<th><label for="targeteid"><?=$this->lang->line('ivrs_employee')?> : </label></th>
		<td>
			<select name="targeteid" id="targeteid">
			<? foreach($emplist as $emp)?>
				<option value="<?=$emp['eid']?>"><?=$emp['empname'] . ' [' .$emp['empnumber'].']'?></option>
			</select>
		</td>
		<td></td>
	</tr>
</table>
</fieldset>
<div align="center">
<input id="button1" type="submit" class="btn btn-primary" value="<?=$this->lang->line('add')?>" /> 
<input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
</div>
</form>
</div>
<? //$this->load->view('ivrs_optlist');?>
