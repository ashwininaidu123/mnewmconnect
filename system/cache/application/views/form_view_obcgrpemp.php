<?if(isset($file) && file_exists($file))require_once($file);?>
<div id="box">
<h3><?php
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
<table class="shortable empListFrm" align="center" id="myTable">
	<thead>
	<tr>
		<th style="width:20%;">#</th>
		<th>Employee Name</th>
		
		<?php
		if($form['gdetail']['group_rule']=='2'){ 
				echo '<th>Employee Weight</th>';
			}
		?>
	</tr>
	</thead><tbody>
		<?php 
		if(!empty($form['gpEmp']))
		{
			 
		?>
		 <tr>
			 <td>#</td>
			 <td><label><?=$form['gpEmp']['empname']?></td>

			 <?php
				
			if($form['gdetail']['group_rule']=='2'){ 
				echo 
				'<td>'.form_input(array('name'=>'weight','id'=>'empweight','value'=>$form['gpEmp']['weight'],'style'=>'width:50px;')).'</td>';
			}	
			
		 
		?>
		 </tr>
		
		<?php
		}
		else
		{
			foreach($form['emplist'] as $key=>$emp){
			if(!in_array($key,$form['empexists'])){
		?>
		 <tr>
			 <td><input type="checkbox" id="<?=$key?>"  name="emp_ids[]" value="<?=$key?>" class="grp_emp"/></td>
			 <td><label for="<?=$key?>"><?=$emp?></label></td>
			 <?php
			 if($form['gdetail']['group_rule']=='2'){ 
				echo 
				'<td>'.form_input(array('name'=>'empweight'.$key,'id'=>'empweight'.$key,'value'=>'','disabled'=>true,'style'=>'width:50px;')).'</td>';
			 }
			 ?>
		</tr>
		<?php
			}
		}
		}
		
		?>
</tbody>
</table>
</fieldset>
<? if(!isset($form['submit'])){ ?>	
<table><tr><td><center>
<input id="button1" type="submit" class="btn btn-primary" name="update_system" value="<?=$this->lang->line('submit')?>" /> 
<input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
</center></td></tr></table>
<? } ?>
<?=$form['close']?>
</div>
