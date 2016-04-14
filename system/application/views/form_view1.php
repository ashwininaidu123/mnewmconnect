<? if(isset($file) && file_exists($file))require_once($file);?>
<script type="text/javascript">
$(function() {
		$("#login").click(function() {
			if($('#login').attr('checked')){
				$('#username').addClass('required email');
				$('#password').addClass('required');
				$('#cpassword').addClass('required');
				$("#username").val($("#empemail").val());
				//$("#accdetail").toggle( 'blind', {}, 500 );
			}else{
				$('#username').removeClass();
				$('#password').removeClass();
				$('#cpassword').removeClass();
				//$("#accdetail").toggle( 'blind', {}, 500 );
				return true;
				}
		});
	});
$(function() {
	$('#empemail').change(function(){
				
	});
	
});
</script>
<div id="box">
<h3><?
	$js = 'id="parentbid" ';
	echo $module['title'];
	if(isset($form['parentids']) && sizeof($form['parentids'])>1) { 
		echo '&nbsp;&nbsp;&nbsp;'.form_dropdown("parentbid",$form['parentids'],$form['busid'],$js);
	}
	?></h3>
<?=$form['open']?>
<fieldset>
<legend><?=$module['title']?></legend>
<div class="sterror"><?php echo validation_errors(); ?></div>
<table>
<?php
foreach($form['fields'] as $field){?>
	<tr>
		<th><?=$field['label']?></th>
		<td><?=$field['field']?></td>
		<td></td>
	</tr>
<? }?>
</table>
</fieldset>
<fieldset id="accdetail" style="display:none;">
	<legend><?php echo $this->lang->line('level_acc_detail');?></legend>
		<table>
			<?php
			foreach($form['fields1'] as $field1){?>
				<tr>
					<th><?=$field1['label']?></th>
					<td><?=$field1['field']?></td>
					<td></td>
				</tr>
			<? }?>
</table>		
</fieldset>				


<div align="center">
<input id="button1" type="submit" class="btn btn-primary" name="update_system" value="<?=$this->lang->line('submit')?>" /> 
<input id="button2" type="reset" class="btn btn-default"  value="<?=$this->lang->line('reset')?>" />
</div>
<?=$form['close']?>
</div>
