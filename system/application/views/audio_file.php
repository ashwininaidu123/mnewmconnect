
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title" id="myModalLabel"><?php echo $module['title'];?></h4>
			</div>
			<div class="modal-body">
<?=$form['open']?>
<?php $i = 0; ?>
	
<table>
<?php

foreach($form['fields'] as $field){?>
<?php if($field['hdayaudio']){ ?>
	
	<tr style="width: 100%;">
		<th style="width: 1%;"><input  id="hdayaudio" type="radio" name="hdayaudio"  onClick="setMaster()" value=<?=$field['hdayaudio']?>> </th>
		<th style="width: 40%;"><label style="text-align: left;" for="title"><?=$field['groupname']?>  </label></th>
	
		<td style="width: 10%;"> <a target="_blank" href="sounds/<?=$field['hdayaudio']?>"><span title="Sound" class="fa fa-volume-up"></span></a></td>
	</tr>


<? $i=+1; }?>
<? }?>
<?  if($i == 0){ ?>

		<tr>	
					
<th><h3>No files Please upload a new file</h3></th> 
	</tr>

<?  }?>

</table>

<?=$form['close']?>
</div>
				</div>
			</div>
		</div>
	</div>

</div>



