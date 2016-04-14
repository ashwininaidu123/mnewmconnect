<? if(isset($file) && file_exists($file))require_once($file);?>
<div id="box">
<h3><?=$module['title']?></h3>
<?=$form['open']?>
<fieldset>
<legend><?=$module['title']?></legend>
<div class="sterror"><?php echo validation_errors(); ?></div>
<table>

<? foreach($form['fields'] as $field){?>
	<tr>
		<th valign="top"><?=$field['label']?></th>
		<td valign="top"><?=$field['field']?></td>
		<td valign="top"></td>
	</tr>
<? }?>
		<tr>
		<th valign="top"><label>Module Selection :</label></th>	
		<td><ul style="list-style:none;">
					<?php 
							$checked='';
							$checked1='';
							$f=$form['feature_list'];
							$s=$form['subfeature_list'];
							//echo sizeof($subfeature_list);
							for($i=0;$i<sizeof($f);$i++){ 
								if(!empty($form['partnerfeatures'])){
									if(in_array($f[$i]['feature_id'],$form['partnerfeatures'])){
										$checked="checked";
									}else{
										$checked='';
									}
								}
								
								?>
								<li><input type="checkbox" class="parentfeature" name="featureconfig[<?=$f[$i]['feature_id']?>]" id="group<?=$f[$i]['feature_id']?>" value="1" <?=$checked?>/><?=$f[$i]['featurename']?>
								<ul style="list-style:none;margin-left:20px;"><?php
								$checked1='';
							 	for($j=0;$j<sizeof($s);$j++){
									if(!empty($form['partnerfeatures'])){
										if(in_array($s[$j]['feature_id'],$form['partnerfeatures'])){
											$checked1="checked";
										}
									}
								
									if($s[$j]['parent_id']==$f[$i]['feature_id']){ ?>
										<li>
											<input type="checkbox" class="group<?=$f[$i]['feature_id']?>" name="featureconfig[<?=$s[$j]['feature_id']?>]" id="featureconfig[<?=$s[$j]['feature_id']?>]" value="1" <?=$checked1?> /><?=$s[$j]['featurename']?>
										</li>
									<?php }
									
								}
							?></ul>
								</li>
								
								
								<?	
							}
							?>
				</ul></td>
				<td></td>
				</tr>
				
				</TABLE>
</fieldset>
<?php 
/*if(isset($form['clone'])){
	if($form['clone']!=0){
		?>
		<input type="hidden" name="clone" id="clone" value="1" />
		<?php
	}
}*/
?>
<table><tr><td><center>
<input id="button1" type="submit" class="btn btn-primary"  name="update_system" value="<?=$this->lang->line('submit')?>" /> 
<input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
</center></td></tr></table>
<?=$form['close']?>
</div>
