<?php echo script_tag("system/application/js/jquery.validate.js");?>
<script language="javascript" type="text/javascript">
   $(document).ready(function(){
         $(".session_message").show();
         $(".session_message").fadeOut(5000);
     
   });
</script>
 <script type="text/javascript">
  $(document).ready(function(){
	 $("form").validate({
	 rules: {
	  },
	messages:{
	Module:"<?php echo $this->lang->line('error_moduletype'); ?>", 
	labelname:"<?php echo $this->lang->line('error_labelname'); ?>"
	
	},
// the errorPlacement has to take the table layout into account
	errorPlacement: function(error, element) {
		error.appendTo( element.parent().next() );
	}
});

});
</script>	
<div class="session_message"><?php echo $this->session->flashdata('item');?></div>	
<div id="content">
		<div id="box">
		<h3><?php echo $this->lang->line('level_custom_header');?></h3>
		<?php
		$attributes = array('class' => 'email', 'id' => 'form','name'=>'form');		
		 echo form_open('customfield/add_customfield',$attributes);
		?>	
		<fieldset id="priseries">
			<legend><?php echo $this->lang->line('level_custom_head1');?></legend>
		<table>
			<?php
				foreach($form as $newform)
				{ ?>
			  	 <tr>	
				   <th><label for="number"><?php echo $newform[0].":";?></label></th>	
				    <td><?php echo $newform[1];?> </td>	
				    <td></td>		
				 </tr>	
			<?php
				}
			?>			
			<tr><td colspan="3" align="center"><?php echo form_submit('AddField', 'AddField');?></td></tr>
				


		</table>
		</fieldset>                        
		<?php	
		echo form_close();?>
		</div>
	</div>
	
	
	
	
