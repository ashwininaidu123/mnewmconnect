<?php echo script_tag("system/application/js/jquery.validate.js");?>
<script>
$(function() {
	jQuery.validator.addMethod("mobile", function(value, element) {
	return this.optional(element) || /^[7-9][0-9]{9}$/.test(value);
	}, "Should start with 7 - 9 and 10 digits");
	$("#brfile").change(function() {
		if($( "#brfile" ).val()!=''){
			$("#pbid").attr("disabled",true);
			$("#number").attr("disabled",true);
		}else{
			$("#pbid").attr("disabled",false);
			$("#number").attr("disabled",false);
		}
	});
	$("#pbid").change(function() {
		if($( "#pbid" ).val()!=''){
			$("#brfile").attr("disabled",true);
			$("#number").attr("disabled",true);
		}else{
			$("#brfile").attr("disabled",false);
			$("#number").attr("disabled",false);
		}
	});
	$("#number").change(function() {
		if($( "#number" ).val()!=''){
			$("#pbid").attr("disabled",true);
			$("#brfile").attr("disabled",true);
		}else{
			$("#pbid").attr("disabled",false);
			$("#brfile").attr("disabled",false);
		}
	});

	$("form").validate({
		rules: {
			content	: "required",
			senderid: "required",
			scheduleat: "required",
			content	: "required",
			pbid	: "required",
			number	: {
					required:true,
					mobile:true
				},
			brfile: {
					required: true,
					accept:'txt|csv'
				}
		},
		messages: {
			content	: "<?=$this->lang->line('error_required')?>",
			senderid: "<?=$this->lang->line('error_required')?>",
			scheduleat: "<?=$this->lang->line('error_required')?>",
			pbid	: "<?=$this->lang->line('error_required')?>",
			number	: {
					required: "<?=$this->lang->line('error_required')?>",
					mobile	: "<?=$this->lang->line('error_mobile')?>"
				},
			brfile: {
					required	: "<?=$this->lang->line('error_required')?>",
					accept		: "<?=$this->lang->line('error_contactfile')?>"
				}			
		},
		errorPlacement: function(error, element) {
			error.appendTo( element.parent().next() );
		}
	});
});
</script>
<div id="box">
<h3><?=$this->lang->line('voice_broadcast')?></h3>
<form id="form" action="voice/addbroadcust" method="post" enctype="multipart/form-data">
<input type="hidden" name="soundid" value="1" />
<fieldset id="prinumber">
<legend><?=$this->lang->line('broadcast_add')?></legend>
<table>
	<tr>
		<th><label for="sound"><?=$this->lang->line('voice_file')?> : </label></th>
		<td>
			<select name="soundid">
			<? foreach($list as $opt){?>
				<option value="<?=$opt['soundid']?>"><?=$opt['title']?></option>
			<? }?>
			</select>
		</td>
		<td></td>
	</tr>
	<tr>
		<th valign="top"><label for="brfile"><?=$this->lang->line('broadcast_file')?> : </label></th>
		<td><input name="brfile" id="brfile" type="file" /></td>
		<td></td>
	</tr>
	<tr><td colspan="3"><center><b>OR</b></center></td></tr>
	<tr>
		<th valign="top"><label for="pbid"><?=$this->lang->line('select_phonebook')?> : </label></th>
		<td>
			<select name="pbid" id="pbid">
				<option value="">[<?=$this->lang->line('select_phonebook')?>]</option>
				<? foreach ($pb_list as $pb){?>
				<option value="<?=$pb['pbid']?>"><?=$pb['pbname']?></option>
				<? }?>
			</select>
		</td>
		<td></td>
	</tr>
	<tr><td colspan="3"><center><b>OR</b></center></td></tr>
	<tr>
		<th valign="top"><label for="number"><?=$this->lang->line('broadcast_test')?> : </label></th>
		<td><input name="number" id="number" type="text" /></td>
		<td></td>
	</tr>
	<tr>
		<th valign="top"><label for="scheduleat"><?=$this->lang->line('schedule_time')?> : </label></th>
		<td><input name="scheduleat" id="scheduleat" class="datepicker" type="text" value="<?=date('Y-m-d H:i')?>" readonly="true" /></td>
		<td></td>
	</tr>
</table>
</fieldset>
<div align="center">
<input id="button1" type="submit" value="<?=$this->lang->line('add')?>" /> 
<input id="button2" type="reset" value="<?=$this->lang->line('reset')?>" />
</div>
</form>
</div>
