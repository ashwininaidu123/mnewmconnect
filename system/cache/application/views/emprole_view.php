<div id="main-content">
		  <div class="page-title"> <i class="icon-custom-left"></i>
				<h4><?php echo $module['title'];?></h4>
		  </div>
		  <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
				   <div class="row" >
						<div class="col-md-12">
							<form action="<?=base_url().$form['form_attr']['action'];?>" id="<?php echo $form['form_attr']['id'];?>" name="<?php echo $form['form_attr']['name'];?>" role="form" parsley-validate class="form-horizontal form icon-validation" method ="post" enctype="<?php echo $form['form_attr']['enctype'];?>" >
									<?php if(isset($form['hidden'])){ 
										foreach($form['hidden'] as $key => $value){ ?>
										<input type="hidden" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $value; ?>">
								      <?php }
								      }?>
								<table class="table table-striped">
								<?php foreach($form['fields'] as $field){?>
									<tr>
										<th><?=$field['label']?></th>
										<td><?=$field['field']?></td>
									</tr>
								<? }?>
								</table>
		<div>
		<? foreach($form['modfields'] as $mod){?>
		<div class="rolecls col-md-12"><input type="checkbox" class="modTitle" value="<?=$mod['modid']?>">
		<?=((strlen($this->lang->line('label_'.$mod['modname']))>0)?$this->lang->line('label_'.$mod['modname']):$mod['moddesc'])?>
		<input type="hidden" name="module[<?=$mod['modid']?>][modid]" value="<?=$mod['modid']?>">
		<span><?=form_checkbox(array(
				 'name'=>'module['.$mod['modid'].'][opt_delete]'
				,'value'=>1,'checked'=>$mod['opt_delete']
				,'id'=>'delete_'.$mod['modid']
				,'class'=>'chk_'.$mod['modid']
			))?>&nbsp;<label for="<?='delete_'.$mod['modid']?>"><?=$this->lang->line('level_delete')?></label></span>

		<? if(in_array($mod['modid'],array('6','23','25','26','24','16','32','40','46'))){?>

		<span><?=form_checkbox(array(
				 'name'=>'module['.$mod['modid'].'][opt_download]'
				,'value'=>1,'checked'=>$mod['opt_download']
				,'id'=>'download_'.$mod['modid']
				,'class'=>'chk_'.$mod['modid']
			))?>&nbsp;<label for="<?='view_'.$mod['modid']?>"><?=$this->lang->line('lavel_download')?></label></span>
		<? }?>
		<span><?=form_checkbox(array(
				 'name'=>'module['.$mod['modid'].'][opt_view]'
				,'value'=>1,'checked'=>$mod['opt_view']
				,'id'=>'view_'.$mod['modid']
				,'class'=>'chk_'.$mod['modid']
			))?>&nbsp;<label for="<?='view_'.$mod['modid']?>"><?=$this->lang->line('level_view')?></label></span>
		<span><?=form_checkbox(array(
				 'name'=>'module['.$mod['modid'].'][opt_add]'
				,'value'=>1,'checked'=>$mod['opt_add']
				,'id'=>'add_'.$mod['modid']
				,'class'=>'chk_'.$mod['modid']
			))?>&nbsp;<label for="<?='add_'.$mod['modid']?>"><?=$this->lang->line('level_add')?></label></span>
		</div>
		<div class="row">
         <div class="col-md-12 col-sm-12 col-xs-12">
		<table class="sortable_table">
		<tr>
		<td width="25%">&nbsp;</td>
		<td width="25%">&nbsp;</td>
		<td width="25%">&nbsp;</td>
		<td width="25%">&nbsp;</td>
		</tr>
		<tr>
		<? $i=0;foreach($mod['fields'] as $f){$i++;?>
			<td><?=$f['field'].$f['label']?></td><?=($i%4==0)?"</tr><tr>":""?>
		<? }
		while($i%4==0){
			echo "<td></td>";$i++;
		}
		?>
		</tr>
		</table>
		</div></div>
		<? }?>
		<table><tr><td><center>
		<input id="button1" type="submit" class="btn btn-primary" onclick="javascript:$('#<?php echo $form['form_attr']['name'];?>').parsley('validate')"  name="update_system" value="<?=$this->lang->line('submit')?>" /> 
		<input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
		</center></td></tr></table>
		</div>
		<?=$form['close']?>
		</div>
</div></div>
