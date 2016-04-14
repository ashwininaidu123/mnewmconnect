<?php //echo script_tag("system/application/js/jquery.validate.js");?>
<script language="javascript" type="text/javascript">
$(document).ready(function(){
	$("#button1").live('click',function(event) {
		$("#form").validate({
			rules: {
				opttext	: "required",
				optorder: {
						required: true,
						number:true,
						max	  :9
					},
				sound	: {
						accept:'wav'
					}
			},
			messages: {
				opttext	: "<?=$this->lang->line('error_required')?>",
				optorder: {
						required	: "<?=$this->lang->line('error_required')?>",
						number		: "<?=$this->lang->line('error_number')?>"
					},
				sound	: {
						accept		: "<?=$this->lang->line('error_soundfile')?>"
					}			
			},
			errorPlacement: function(error, element) {
				error.appendTo( element.parent().next() );
			}
		});
	});
});
</script>
<div id="box">
<h3>IVRS Options
<span>
<a class="btn-danger" data-toggle="modal" data-target="#modal-responsive" href="ivrs/addopt/<?=$this->uri->segment(3).'/'.$this->uri->segment(4)?>"><span title="Add Option" class="glyphicon glyphicon-plus-sign"></span></a>
</span>
</h3>
<table>
	<tr>
		<th width="30">#</th>
		<th width="100"><?=$this->lang->line('ivrs_optorder')?></th>
		<th><?=$this->lang->line('ivrs_opttext')?></th>
		<th><?=$this->lang->line('ivrs_target')?></th>
		<th width="150"><?=$this->lang->line('ivrs_optsound')?></th>
		<th width="100"><?=$this->lang->line('level_Action')?></th>
	</tr>
<?php
	$i = 0;
	foreach($ivrsopt['rec'] as $opt){ ?>
	<tr>
		<td><?=++$i?></td>
		<td><?=$opt['optorder']?></td>
		<td><?=$opt['opttext']?></td>
		<td><?=$opt['targettype']?></td>
		<td>
			<embed src="<?=site_url('sounds/'.$opt['optsound'])?>" 
			volume="100" loop="false" controls="console" height="29"
			wmode="transparent" autostart="FALSE" width="150" hidden="false">
			</embed>
		</td>
		<td align="center">		
			<? if($opt['targettype']=='list'){?>
			<a href="ivrs/addopt/<?=$this->uri->segment(3).'/'.$opt['optid']?>" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"> <span  class="fa fa-plus" title="Add Options" ></span></a>
			<a href="ivrs/options/<?=$this->uri->segment(3).'/'.$opt['optid']?>"><span title="List Option" class="fa fa-list-ul"></span></a><? 
			}else{ ?>
			<span  class="fa fa-plus" title="Terget is Employee" ></span>
			<span title="Terget is Employee" class="fa fa-list-ul"></span>
			<? }?>
			<a href="ivrs/editopt/<?=$this->uri->segment(3).'/'.$opt['optid']?>" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Edit" class="fa fa-edit"></span></a>
			<? if($opt['parentopt']!=0){?>
			<a href="ivrs/deleteopt/<?=$opt['optid']?>" class="deleteClass"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>
			<? }else{?>
	<span title="Delete" class="glyphicon glyphicon-trash"></span>
			<? }?>
		</td>
	</tr>
<? }?>
</table>
</div>
