<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">					
					<button aria-hidden="true" data-dismiss="modal" class="close" type="button"><i class="fa fa-times"></i></button>
                      <h4><?php echo $this->lang->line('level_codelist');?></h4>
		<?php
			$attributes = array('class' => 'form', 'id' =>'landingnumber','name'=>'landingnumber');		
			 echo form_open('ivrs/Ivrs_csv',$attributes);
		?>
				<TABLE>
					<TR>
						<Td>Code</Td>
						<Td>Area</Td>
					
					</TR>
					<?php for($i=0;$i<sizeof($codelist);$i++){ ?>
					<tr>
						
						<td><?php echo $codelist[$i]['code'];?></td>
						<td><?php echo $codelist[$i]['area'];?></td>
						
					</tr>	
					<?php } ?>
					
				</TABLE>
		
				<?php echo form_close();?>

</div>				

