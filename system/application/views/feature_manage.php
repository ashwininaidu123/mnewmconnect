<script type="text/javascript">
$(function() {
	$(".parentfeature").not(':checked').each(function(){
			$("input."+this.id).attr("disabled", true);

		});
  $(".parentfeature").click(function(){
	  if (this.checked) {
			$("input."+this.id).removeAttr("disabled");
		} else {
			$("input."+this.id).removeAttr("checked");
			$("input."+this.id).attr("disabled", true);
		}
	  });
});

</script>
<style>
ul{list-style:none;}
ul li ul{list-style:none;padding-left:30px;}
</style>
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title" id="myModalLabel"><?php echo "Feature Manage";?></h4>
			</div>
			<div class="modal-body">
		<?php
			$attributes = array('class' => 'form', 'id' =>'landingnumber','name'=>'landingnumber');		
			 echo form_open($faction,$attributes);
			// print_r($subfeature_list);
		?>

				<TABLE><tr><td><ul>
					<?php 
							$disabled="";
							for($i=0;$i<sizeof($feature_list);$i++){ 
								 if(!empty($partnerfeatures)) { 
									  if(!in_array($feature_list[$i]['feature_id'],$partnerfeatures)){
											$disabled="disabled";
										}
									  }
								
								?>
								<li><input type="checkbox" class="parentfeature" name="featureconfig[<?=$feature_list[$i]['feature_id']?>]" id="group<?=$feature_list[$i]['feature_id']?>" value="1" <?=(in_array($feature_list[$i]['feature_id'],$checked_list))?'checked':''?> <?=$disabled?> /><?=$feature_list[$i]['featurename']?>
								<ul><?php
							 	for($j=0;$j<sizeof($subfeature_list);$j++){
									if($subfeature_list[$j]['parent_id']==$feature_list[$i]['feature_id']){ ?>
										<li>
											<input type="checkbox" class="group<?=$feature_list[$i]['feature_id']?>" name="featureconfig[<?=$subfeature_list[$j]['feature_id']?>]" id="featureconfig[<?=$subfeature_list[$j]['feature_id']?>]" value="1" <?=(in_array($subfeature_list[$j]['feature_id'],$checked_list))?'checked':''?>/><?=$subfeature_list[$j]['featurename']?>
										</li>
									<?php }
									
								}
							?></ul></li><?	
							}
							?>
				</ul></td></tr></TABLE>
				
				
			
		<table><tr><td><center>
<input id="button1" type="submit" class="btn btn-primary" name="submit" value="<?=$this->lang->line('submit')?>" /> 
<input id="button2" type="reset" class="btn btn-default" value="<?=$this->lang->line('reset')?>" />
</center></td></tr></table>
		<?php echo form_close();?>
</div>		
