<? if(isset($file) && file_exists($file))require_once($file);?>
<script type="text/javascript">

$(function(){
	
	$('#polls_single').validate({
		
		
	});
	$('#addb').live('click',function(event){
		var ptype=$('#poll_t').val();
		var start=$('#newitems').val();
		var id=($('#newitems').val()!="")?parseInt($('#st').val())+parseInt($('#newitems').val()):parseInt($('#st').val())+0;
		id++;
		start++;
		if(ptype!=2){
			var newappend='<tr id="'+id+'"><td>Option'+id+'</td><td><input type="text" name="newoption'+start+'" id="newoption'+start+'" class="required number" maxlength="2"/></td><td>Poll Entry'+id+'<br/><input typr="text" name="newpollentry'+start+'" id="newpollentry'+start  +'" class="required"/><br/><a href="javascript:void(0)" rel="'+id+'" class="removerow" >Remove</a></td></tr>';
		}else{
			var newappend='<tr id="'+id+'"><td>Landing Number'+id+'</td><td><select id="newpri'+start+'" name="newpri'+start+'" class="required"><?php $arr=array_keys($form['pris']); for($i=0;$i<sizeof($arr);$i++){ ?>	<option value="<?php echo $arr[$i];?>"><?php echo $form['pris'][$arr[$i]];?></option><?php } ?>	</select></td><td>Poll Entry'+id+'<br/><input typr="text" name="newpollentry'+start+'" id="newpollentry'+start+'" class="required"/><br/><a href="javascript:void(0)" rel="'+id+'" class="removerow" >Remove</a></td></tr>';
			
		}
	
		$('#newitems').val(start);
		$('#oldrow').parent().append(newappend);
		
	});
	$('.removerow').live('click',function(event){
		var start=$('#newitems').val(); 
		var id=parseInt($('#st').val())+parseInt($('#newitems').val());
		$('#'+$(this).attr('rel')).remove();
		id--;
		start--;
		
		$('#newitems').val(start);
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
	<td colpsan="2" align="right"><input type="button" id="addb" name="addb" value="Add option"/></td>
</tr>
<?php
	$i=0;
	foreach($form['options'] as $fs){
		$i++;
		?>
		<tr id="oldrow">
		<td><?php echo ($form['pollty']!=2)?'Option'.$i:'Landing Number'.$i;?></td>
		<td><?php 
				$jss1 = 'id="pri'.$i.'" class="required"';
				echo ($form['pollty']!=2)?
					form_input(array(
									  'name'        => 'option'.$fs['option_id'],
										'id'          =>'option'.$fs['option_id'],
										'class'=>'required number',
										'maxlength'=>'2',
										'value'       => $fs['optionkey'])):form_dropdown('pri'.$fs['option_id'],$this->pollmodel->landing_number($fs['optionkey'],'4'),$fs['optionkey'],$jss1); ?></td>
		<td>Poll Entry<?=$i?><br/><input typr="text" name="pollentry<?=$fs['option_id']?>" id="pollentry<?=$i?>" class="required" value="<?php echo $fs['optionval'];?>"/><br/><a href="poll/remove_opt/<?php echo $fs['poll_id']."/".$fs['option_id'];?>">x</a></td>
		</tr>
		
		
		
		
		<?php
		
		
		
		
	}




?>

<input type="hidden" name="newitems" id="newitems" value=""/>


</table>
</fieldset>
<?php 

?>

<table><tr><td><center>
<input id="button1" type="submit" name="submit" value="update" /> 
<input id="button2" type="reset" value="<?=$this->lang->line('reset')?>" />
</center></td></tr></table>
<?=$form['close']?>
</div>
