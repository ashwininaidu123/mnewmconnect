<?php echo script_tag("system/application/js/jquery.validate.js");?>
<script type="text/javascript">
function textCounter(textarea,counterID,maxLen) {
	cnt = document.getElementById(counterID);
	
		if (textarea.value.length>maxLen)
		{
			textarea.value=textarea.value.substring(0,maxLen);
		}
		cnt.innerHTML=maxLen-textarea.value.length;
}




</script>
<script type="text/javascript">

$(document).ready(function() {
	jQuery.validator.addMethod("noSpace", function(value, element) { 
	return value.indexOf(" ") < 0 && value != ""; 
	}, "No spaces Allowed");


	 $("#form").validate({
      rules: {
        keyword: {
          noSpace: true
        }
      
      },
     errorPlacement: function(error, element) {
		error.appendTo( element.parent().next() );
		}	 
  });
	
});
$(function() {
	 var i = 2; 

    $('a#add').click(function() { 
		var appends="<table id='tab"+i+"'>" +
					"<tr><th><label><?php echo $this->lang->line('level_subkeyword');?></label></th>" +
					"	<td> :<input type='text' name='subkeyword[]' id='subkeyword' class='required noSpace' size='5'/></td>" +
					"	<td></td></tr>" +
					"<tr><th><label><?php echo $this->lang->line('level_custolevel');?></label></th>" +
					"	<td> :<input type='text' name='customlevel[]' id='customlevel' class='required' size='5'/></td>" +
					"	<td></td></tr>" +
					"<tr><th><label><?php echo $this->lang->line('level_replymsg');?></label></th>" +
					"	<td> :<textarea name='replymsg[]' id='replymsg"+i+"' class='required' onKeyUp=textCounter(this,'count_display"+i+"',160);></textarea><span id='count_display"+i+"'>160</SPAN> characters remaining</td>" +
					"	<td></td></tr>" +
					'<tr><td><a href="javascript:void(0)" rel="tab'+i+'"  id="remove"><?php echo $this->lang->line('level_removesubword');?></a> </td></tr>'+
					"	</table>" ;
		$(appends).appendTo('#shortcodes'); 
		i++;
	});
	 $('a#remove').live('click',function() {
		 $('#'+this.rel).remove();
    });
    
    $('#keyworduse').change(function(){
		if($('#keyworduse').val()==2)
		{
			$('#types').show();
		}else{
			$('#types').hide();
		}
	});
    
    $('#forwardtype').change(function(){
		if($('#forwardtype').val()!="")
		{
			if($('#forwardtype').val()=="1")
			{
				$('#grouplists').show();
				$('#emplist').hide();
			}
			if($('#forwardtype').val()=="2")
			{
				$('#grouplists').hide();
				$('#emplist').show();
			}
		}
		else{
				$('#grouplists').hide();
				$('#emplist').hide();
		}
		
	});
    
	
});

</script>
<div id="box">
<?php $attributes = array('class' => 'email', 'id' =>'form','name'=>'form');
		if(!isset($keywordlist)){ $action='keyword/addkeyword';}else{$action='keyword/editkeyword/'.$keyid;}
		echo form_open($action,$attributes);
		//print_r($keywordlist);
	?>
	<fieldset id="priseries">
				<legend><?php echo $this->lang->line('level_keyword_detail');?></legend>
				<TABLE>
					<tr>
						<th><label><?php echo $this->lang->line('level_shortcode');?></label></th>
						<td> :<?php 
							$js1 = 'id="shortcode" class="required"';	
							if(isset($keywordlist)){$select=$keywordlist[0]['code_id'];}else{$select='';}
							
							echo form_dropdown('shortcode', $shortcode, $select,$js1);?>
						</td>
						<td></td>
					</tr>
					<tr>
						<th><label><?php echo $this->lang->line('level_keyword');?></label></th>
						<?php if(isset($keywordlist)){$keyword=$keywordlist[0]['keyword'];}else{$keyword='';}?>
						<td> :<input type="text" name="keyword" id="keyword" class="required nospace" value="<?=$keyword?>"/>
						</td>
						<td></td>
					</tr>
					<tr>
						<th><label><?php echo $this->lang->line('level_defaultmsg');?></label></th>
						<?php if(isset($keywordlist)){$defaultmsg=$keywordlist[0]['default_msg'];}else{$defaultmsg='';}?>
						<td> :<textarea name="defaultmsg" id="defaultmsg" onKeyUp="textCounter(this,'count_display',160);" onBlur="textCounter(this,'count_display',160);" class="required"><?=$defaultmsg?></textarea>
						<span id="count_display">160</SPAN> characters remaining
						</td>
						<td></td>
					</tr>
					<tr>
						<th><label><?php echo $this->lang->line('level_keyworduse');?></label></th>
							<?php if(isset($keywordlist)){$kuse=$keywordlist[0]['keyword_use'];}else{$kuse='';}?>
						<td> :<?php 
							$js1 = 'id="keyworduse" class="required"';	
							echo form_dropdown('keyworduse', $keyworduse, $kuse,$js1);?>
						</td>
						<td></td>
					</tr>
					
					<tr id="types" <?php if(!isset($keywordlist)){ ?> style="display:none;"<?php }else{ if($kuse!=2){ ?> style="display:none;"<?php } } ?>>
					<?php if(isset($keywordlist)){$forwardt=$keywordlist[0]['fowardto_type'];}else{$forwardt='';}
							if($forwardt=='group'){ $selects=1;}
							else if($forwardt=='employee'){ $selects=2;}
							else{ $selects='';}
							//echo $selects;
							 	
					?>
						<th><label><?php echo $this->lang->line('level_forwardtype');?></label></th>
						<td> :<select id="forwardtype" name="forwardtype">
								<option value="" >select</option>
								<option value="1" <?php if($selects=="1"){ echo "selected";}?>>Group</option>
								<option value="2" <?php if($selects=="2"){ echo "selected";}?>>Employee</option>
							</select>
						</td>
						<td></td>
					</tr>
					<tr id="grouplists"  <?php if(isset($keywordlist)){ if($selects==2){ ?> style="display:none;"<?php } }else{ ?> style="display:none;"<?php } ?>>
						<th><label><?php echo $this->lang->line('level_Group');?></label></th>
						<?php if(isset($keywordlist)){$grp=$keywordlist[0]['forwardto_id'];}else{$grp='';} ?>
						<td> :<?php 
							$js1 = 'id="groups" class="required"';	
							echo form_dropdown('groups', $group_list, $grp,$js1);?>
						</td>
						<td></td>
					</tr>
					<tr id="emplist" <?php if(isset($keywordlist)){ if($selects==1){ ?> style="display:none;"<?php } }else{ ?> style="display:none;"<?php } ?>>
						<th><label><?php echo $this->lang->line('level_Employee');?></label></th>
						<?php if(isset($keywordlist)){$emp=$keywordlist[0]['forwardto_id'];}else{$emp='';} ?>
						<td> :<?php 
							$js1 = 'id="employees" class="required"';	
							echo form_dropdown('employees', $emplist, $emp,$js1);?>
						</td>
						<td></td>
					</tr>
					<?php if(!isset($keywordlist)) {?>
					<tr>
						<td colspan="3">
							<div id="shortcodes">
							</div>
					</td>
					</tr>
					<tr>
							<td colspan="3" id="remv1" align="right"><a href="javascript:void(0)"  id="add"><?php echo $this->lang->line('level_addsubkeyword');?></a> 
							 </td>
					</tr>
					<?php }else{ ?>
					<tr>
						<td colspan="3">
							<?php 
									//print_r($subkeyword);
									if(sizeof($subkeyword)>0){
									
									for($i=0;$i<sizeof($subkeyword);$i++){
								?>
										<table id='tab<?php echo $i;?>'>
											<tr><th><label><?php echo $this->lang->line('level_subkeyword');?></label></th> 
											<td> :<input type='text' name='subkeyword[<?php echo $subkeyword[$i]['subkeyword_id']; ?>]' id='subkeyword' class='required noSpace' size='5' value="<?php echo $subkeyword[$i]['subkeyword']?>"/></td> 
											<td></td></tr> 
											<tr><th><label><?php echo $this->lang->line('level_custolevel');?></label></th> 
											<td> :<input type='text' name='customlevel[<?php echo $subkeyword[$i]['subkeyword_id']; ?>]' id='customlevel' class='required noSpace' size='5' value="<?php echo $subkeyword[$i]['customvalue']?>"/></td> 
											<td></td></tr>
											<tr><th><label><?php echo $this->lang->line('level_replymsg');?></label></th>  
											<td> :<textarea name='replymsg[<?php echo $subkeyword[$i]['subkeyword_id']; ?>]' id='replymsgi' class='required' onKeyUp="textCounter(this,'count_display<?php echo $subkeyword[$i]['subkeyword_id']; ?>',160);"><?=$subkeyword[$i]['replymsg']?></textarea><span id='count_display<?php echo $subkeyword[$i]['subkeyword_id']; ?>'>160</SPAN> characters remaining</td> 
						<td></td></tr>
										</table>
								
								<?php
								}
							}
								?>
						
						
							
						</td>
					</tr>
					
					
					<?php }?>
					
					<tr>
						<td colspan="3" align="center">
						
						<?php if(!isset($keywordlist)) { echo form_submit('AddKeyword', 'AddKeyword');}else{ echo form_submit('Updatekeyword', 'Updatekeyword');}?>
						</td>
					</tr>
					
					
					
					
					
				</TABLE>
				
				
				
				
				
				
				
	</fieldset>			




</div>
	<?php echo form_close();?>
