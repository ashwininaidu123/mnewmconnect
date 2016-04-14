<? if(isset($file) && file_exists($file))require_once($file);?>
<script type="text/javascript">

$(function(){
	
	$('#polls_single').validate({
		
		
	});
	$('#addb').live('click',function(event){
		var id=$('#st').val();
		id++;
		var htmlid=($('#st').val()==0)?parseInt($('#st').val())+2:parseInt($('#st').val())+2;
		if(htmlid==99){ 
			$('#addb').attr('disabled', 'true');
		}
		var newappend='<tr id="'+id+'"><td>Option'+htmlid+'</td><td><input type="text" name="option'+id+'" id="option'+id+'" class="required number" maxlength="2"/></td><td>Poll Entry'+htmlid+'<br/><input typr="text" name="pollentry'+id+'" id="pollentry'+id+'" class="required"/><br/><a href="javascript:void(0)" rel="'+id+'" class="removerow" >Remove</a></td></tr>';
		$('#st').val(id);
		$('#oldrow').parent().append(newappend);
		
	});
	$('.removerow').live('click',function(event){
		var id=$('#st').val();
		$('#'+$(this).attr('rel')).remove();
		id--;
		$('#st').val(id);
		if(id<=99){
			$('#addb').removeAttr("disabled");
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
<tr>
	<td colpsan="2" align="right"><input type="button" id="addb" name="addb" value="Add Option"/></td>
</tr>
<tr id="oldrow">
	<td>Option1</td>
	<td><input type="text" name="option" id="option" class="required number" maxlength="2"/></td>
	<td>Poll Entry1<br/><input typr="text" name="pollentry" id="pollentry" class="required"/></td>
</tr>
</table>
</fieldset>
<?php 

?>

<table><tr><td><center>
<input id="button1" type="submit" name="submit" value="Submit" /> 
<input id="button2" type="reset" value="<?=$this->lang->line('reset')?>" />
</center></td></tr></table>
<?=$form['close']?>
</div>
