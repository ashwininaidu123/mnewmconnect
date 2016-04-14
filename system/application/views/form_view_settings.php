<script>
	$("#showtext").hide();
	</script>
<?=$form['open']?>
<input type='hidden' name="moduleid" id="moduleid" value="<?=$modids?>"/>
<h4><?=$module['title']?></h4>
<?php if($URL != ''){?>
<a data-target="#modal-responsive" data-toggle="modal" class="btn-danger" id="addCus" href="<?=$URL?>" style="color:#0090d9;" >Add Custom Field</a>
<?php } ?>
<table class="table table-striped">
<? foreach($form['fields'] as $field){?>
	<tr>
		<th valign="top"><?=$field['label']?></th>
		<?=$field['field']?>
	</tr>
<? }?>
</table>
<table><tr><td><center>
	<?  $modidArr = array();
		foreach($modlist as $mod){
			$modidArr[] = $mod['modid'];
		}
		$cur = array_search($modids,$modidArr);
		$pre = ($modids>1 && $modids<=end($modidArr)) ? $modidArr[$cur-1] : 0;
		$nxt = ($modids>=1 && $modids<end($modidArr)) ? $modidArr[$cur+1] : $modidArr[$cur];
	?>
	<input type="hidden" id="preV" value="<?=$pre?>">
	<input type="hidden" id="nexT" value="<?=$nxt?>">
<?=($modids>1 && $modids<=47)?'<input id="button4" type="button" name="previous" class="btn btn-primary" value="'.$this->lang->line('level_previous').'" />':''?> 
&nbsp;<?=($modids)?'<input id="button5" type="button" name="saveval" class="btn btn-primary" value="'.$this->lang->line('level_save').'" />':''?>
&nbsp;<?=($modids>=1 && $modids<47)? '<input id="button3" type="button" class="btn btn-primary" name="next" value="'.$this->lang->line('level_next').'" />':''?> 
<?=($modids==47)?'<input id="button1" type="submit" name="update_system" class="btn btn-primary" value="'.$this->lang->line('submit').'" />':''?>
<input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
<input id="button2" type="button" class="btn btn-default skip" value="<?=$this->lang->line('skip')?>" />
</center></td></tr></table>
</div>
<?=$form['close']?>
</div>
 <div class="modal fade" id="modal-responsive" aria-hidden="true">
</div>

