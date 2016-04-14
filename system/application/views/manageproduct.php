<script>
$(function() {
	$('.DeleteItem').click(function(){
		if(confirm("Do you want to delete this items?")){
			var url="<?php echo site_url();?>"+"Masteradmin/Delete_product/"+this.id
			$.get(url, function(data){
				
				window.parent.location.href = window.parent.location.href;
			});
		}
	});
	$('.ChangeStatus').click(function(){
		if(confirm("Do you want to change the status?")){
			var url="<?php echo site_url();?>"+"Masteradmin/change_productstatus/"+this.id
		
			$.get(url, function(data){
				
				window.parent.location.href = window.parent.location.href;
			});
		}
	});

});


</script>
	<div id="box">
		<h3><?php echo $this->lang->line('level_Manage_products');?></h3>
			<?php
			$attributes = array('class' => 'email', 'id' =>'forms','name'=>'forms');	
			 echo form_open('Masteradmin/manage_product',$attributes);
 	?>
			
			<table width="100%">
				<thead>
				<tr>
					 <th><a href="#"><?php echo $this->lang->line('level_productid');?></a></th>
					 <th><a href="#"><?php echo $this->lang->line('Product_name');?></a></th>
					 <th><a href="#"><?php echo $this->lang->line('Product_rate');?></a></th>
					 <th><a href="#"><?php echo $this->lang->line('Product_ratetype');?></a></th>
					 <th><a href="#"><?php echo $this->lang->line('level_Action');?></a></th>
				</tr>
				</thead>
				<?php
					for($i=0;$i<sizeof($products);$i++){
						?>
							<tr>
									<td><?php echo $products[$i]['product_id'];?></td>
									<td><?php echo $products[$i]['product_name'];?></td>
									<td><?php echo $products[$i]['rate'];?></td>
									<td><?php echo $products[$i]['rate_type'];?></td>
									<td><a href="<?php echo site_url('Masteradmin/add_product/'.$products[$i]['product_id']);?>"><span title="Edit" class="fa fa-edit"></span></a>
									<span class="DeleteItem" id="<?php echo $products[$i]['product_id'];?>" title="Delete" class="glyphicon glyphicon-trash"></span>
										
										
											<?php if($products[$i]['status']=="1"){
												?>
											  <span class="fa fa-unlock ChangeStatus"  id="<?php echo $products[$i]['product_id'];?>" title="Disable"></span>
											<?php
												}
												else{
												?>
												<span class="fa fa-lock ChangeStatus" id="<?php echo $products[$i]['product_id'];?>" title="Enable"></span>
												<?php } ?>
										
							
							
							
							
							</tr>
						<?php
						
					}
				
				?>
				
				



		

			</table>


			<?php echo form_close();?>


	</div>

