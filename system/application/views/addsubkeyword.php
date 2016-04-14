
<div id="box">
	<?php 
		$attributes = array('class' => 'email', 'id' =>'form','name'=>'form');
		echo form_open('keyword/addsubkeyword/'.$id,$attributes);
	?>
	<fieldset id="priseries">
				<legend><?php echo "Add".$this->lang->line('level_subkeyword');?></legend>
		<table id='tab'>
			<tr><th><label><?php echo $this->lang->line('level_subkeyword');?></label></th> 
				<td> :<input type='text' name='subkeyword' id='subkeyword' class='required noSpace' size='5' value=""/></td> 
				<td id="err1"></td>
			</tr> 
			<tr><th><label><?php echo $this->lang->line('level_custolevel');?></label></th> 
				<td> :<input type='text' name='customlevel' id='customlevel' class='required noSpace' size='5' value=""/></td> 
				<td id="err2"></td>
			</tr>
			<tr><th><label><?php echo $this->lang->line('level_replymsg');?></label></th>  
				<td> :<textarea name='replymsg' id='replymsg' class='required' onKeyUp="textCounter(this,'count_display',160);"></textarea><span id='count_display'>160</SPAN> characters remaining</td> 
				<td id="err3"></td>
			</tr>
			<tr>
				<td colspan="3" align="center">
					<?php
						$js='id="Addsubkeyword"';
					 echo form_submit('Addsubkeyword','Addsubkeyword',$js);?>
				
				</td>
			
			
			</tr>
		 </table>
	</fieldset>	 
</div>	 
<script type="text/javascript">
$('#Addsubkeyword').click(function(){
	var values=$('#subkeyword').val();
	if($('#subkeyword').val()=='')
	{
		$('#err1').html('<span style="color:red;">The field is required</span>');
		return false;
	}
	if(values!="")
	{
		var str=values.split(" ");
		if(str.length >1){
		$('#err1').html('<span style="color:red;">No spaces Allowed</span>');
		return false;
		}
	}
	if($('#customlevel').val()=='')
	{
		$('#err2').html('<span style="color:red;">The field is required</span>');
		return false;
	}
	if($('#replymsg').val()=='')
	{
		$('#err3').html('<span style="color:red;">The field is required</span>');
		return false;
	}
});
function textCounter(textarea,counterID,maxLen) {
	cnt = document.getElementById(counterID);
	
		if (textarea.value.length>maxLen)
		{
			textarea.value=textarea.value.substring(0,maxLen);
		}
		cnt.innerHTML=maxLen-textarea.value.length;
}



</script>
