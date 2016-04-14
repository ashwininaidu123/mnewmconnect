<? if(isset($file) && file_exists($file))require_once($file);?>
<script type="text/javascript">
$(function(){
	$('#polls').validate({
		
		
	});
	$('#ptype').live('change',function(event){
		var hides=parseInt("<?php echo ($form['polr']!=0)?'1':'0';?>");
		if(hides>0){
		 if($('#ptype').val()!=1){
				$('#hiderow').hide();
			}else{
				
				$('#hiderow').show();
			}	 	
		}else{
			if($('#ptype').val()==1){
				var newappend='<tr id="approw"><td>Landing Number</td><td><select id="pri" name="pri" class="auto"><?php $arr=array_keys($form['pris']); for($i=0;$i<sizeof($arr);$i++){ ?>	<option value="<?php echo $arr[$i];?>"><?php echo $form['pris'][$arr[$i]];?></option><?php } ?>	</select></td><td></td></tr>';
				$('#oldrow').parent().append(newappend);
			}else{
				
				$('#approw').remove();
			}
		}		
	});
	
});
</script>
<div id="box">
<h3><?=$module['title']?></h3>
<?=$form['open']?>
<fieldset>
<legend><?=$module['title']?></legend>
<div class="sterror"><?php echo validation_errors(); ?></div>
<table>

<? 
	$i=1;
	foreach($form['fields'] as $field){
	
?>
	<tr id="<?php echo ($i==sizeof($form['fields']))?'oldrow':''; ?>">
		<th valign="top"><?=$field['label']?></th>
		<td valign="top"><?=$field['field']?></td>
		<td valign="top"></td>
	</tr>
<? $i++; }
if(!empty($form['fields1'])){
foreach($form['fields1'] as $field){
	?>
	<tr id="hiderow">
		<th valign="top"><?=$field['label']?></th>
		<td valign="top"><?=$field['field']?></td>
		<td valign="top"></td>
	</tr>
	
	<?php
}}else{
		?>
		<tr id="hiderow" style="display:<?=($form['polr']!=1)?'none':''?>"><td>Landing Number</td><td><select id="pri" name="pri" class="auto"><?php $arr=array_keys($form['pris']); for($i=0;$i<sizeof($arr);$i++){ ?>	<option value="<?php echo $arr[$i];?>"><?php echo $form['pris'][$arr[$i]];?></option><?php } ?>	</select></td><td></td></tr>
	<?php
}
?>



</table>
</fieldset>
<table>
	<tr>
		<td><center style="color:red"><?php echo ($form['expiry']!=0)?"Poll Result is Anounced You can't update the poll":''?></center></td>
	</tr>
	<tr><td><center>
	
<input id="button1" type="submit" name="submit" value="Submit" <?php echo ($form['expiry']!=0)?'disabled':''?> /> 
<input id="button2" type="reset" value="<?=$this->lang->line('reset')?>" />
</center></td></tr></table>
<?=$form['close']?>
</div>
