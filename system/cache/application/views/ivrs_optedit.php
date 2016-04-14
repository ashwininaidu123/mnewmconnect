<script>
	$(function() {
		if($("#targettype").val() == "employee")
				$("#empid").fadeIn(500);
			else
				$("#empid").fadeOut(500);
		$("#targettype").change(function() {
			if(this.value == "employee")
				$("#empid").fadeIn(500);
			else
				$("#empid").fadeOut(500);
		});
	});
</script>
<div id="box">
<h3><?=$this->lang->line('ivrs_options')?></h3>
<form id="form" action="ivrs/editoptions" method="post" enctype="multipart/form-data">
<input type="hidden" name="ivrsid" value="<?=$this->uri->segment(3)?>" />
<input type="hidden" name="optid" value="<?=$this->uri->segment(4)?>" />
<input type="hidden" name="parentopt" value="<?=$opt['0']['parentopt']?>" />
<fieldset id="prinumber">
<legend><?=$this->lang->line('ivrs_editoptions')?></legend>
<table>
	<tr>
		<th><label for="optorder"><?=$this->lang->line('ivrs_optorder')?> : </label></th>
		<td><input name="optorder" id="optorder" type="text" value="<?=$opt['0']['optorder']?>" /></td>
		<td></td>
	</tr>
	<tr>
		<th><label for="opttext"><?=$this->lang->line('ivrs_opttext')?> : </label></th>
		<td><input name="opttext" id="opttext" type="text" value="<?=$opt['0']['opttext']?>" /></td>
		<td></td>
	</tr>
	<tr>
		<th valign="top"><label for="sound"><?=$this->lang->line('ivrs_optsound')?> : </label></th>
		<td>
			<embed src="<?=site_url('sounds/'.$opt['0']['optsound'])?>" 
			volume="100" loop="false" controls="console" height="29"
			wmode="transparent" autostart="FALSE" width="250" hidden="false">
			</embed><br>
			<input name="sound" id="sound" type="file" />
		</td>
		<td></td>
	</tr>
	<tr>
		<th><label for="targettype"><?=$this->lang->line('ivrs_target')?> : </label></th>
		<td>
			<select name="targettype" id="targettype">
				<option value="list" <?=($opt['0']['targettype']=='list')?'Selected="selected"':""?>><?=$this->lang->line('ivrs_list')?></option>
				<option value="employee" <?=($opt['0']['targettype']=='employee')?'Selected="selected"':""?>><?=$this->lang->line('ivrs_emp')?></option>
				<option value="hangup" <?=($opt['0']['targettype']=='hangup')?'Selected="selected"':""?>><?=$this->lang->line('ivrs_hangup')?></option>
			</select>
		</td>
		<td></td>
	</tr>
	<tr id="empid" style="display:none">
		<th><label for="targeteid"><?=$this->lang->line('ivrs_employee')?> : </label></th>
		<td>
			<select name="targeteid" id="targeteid">
			<? foreach($emplist as $emp)?>
				<option value="<?=$emp['eid']?>" <?=($opt['0']['targeteid']==$emp['eid'])?'Selected="selected"':""?>><?=$emp['empname'] . ' [' .$emp['empnumber'].']'?></option>
			</select>
		</td>
		<td></td>
	</tr>
</table>
</fieldset>
<div align="center">
<input id="button1" type="submit" class="btn btn-primary" value="<?=$this->lang->line('edit')?>" /> 
<input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
</div>
</form>
</div>
