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
	  $('#i_add').live('click',function() {
	    return !$('#avpris option:selected').remove().appendTo('#sepris');
	});
	$('#i_remove').live('click',function() {
		return !$('#sepris option:selected').remove().appendTo('#avpris');
	});
});

</script>
<style>
ul{list-style:none;}
ul li ul{list-style:none;padding-left:30px;}
</style>
<div id="box">
		<?php
			$attributes = array('class' => 'form', 'id' =>'landingnumber','name'=>'landingnumber');		
			 echo form_open("Masteradmin/Prisettings/".$partner_id,$attributes);
			// print_r($subfeature_list);
		?>
		<fieldset id="priseries">
				<legend><?php echo "Assign to partner";?></legend>
				<table>
					<tr>
						
						<td><select id="avpris" name="avpris[]" multiple>
								<?php
										if(!empty($prilist['data'])){
											for($i=0;$i<sizeof($prilist['data']);$i++){
												$s=$prilist['data'];
												?>
													<option value="<?php echo $s[$i]['prinumber']?>"><?php echo $s[$i]['prinumber']?></option>
												<?php
												
											}
										}
								
								?>
						</select> </td>
						<td valign="center">
							<a href="javasript:void(0);" id="i_add" class="selectButton"><center>Add &gt;&gt;</center></a>
							<a href="javasript:void(0);" id="i_remove" class="selectButton"><center>&lt;&lt; Remove</center></a>
						</td>
						<td><select id="sepris" name="sepris[]" multiple>
								<?php
										if(!empty($selectedlist['data'])){
											for($i=0;$i<sizeof($selectedlist['data']);$i++){
												$ss=$selectedlist['data'];
												?>
													<option value="<?php echo $ss[$i]['prinumber']?>"><?php echo $ss[$i]['prinumber']?></option>
												<?php
												
											}
										}
								
								?>
						</select> </td>
					</tr>
				</table>
				
				
		</fieldset>
		<table><tr><td><center>
<input id="button1" type="submit" name="submit" value="<?=$this->lang->line('submit')?>" /> 
<input id="button2" type="reset" value="<?=$this->lang->line('reset')?>" />
</center></td></tr></table>
		<?php echo form_close();?>
</div>		
