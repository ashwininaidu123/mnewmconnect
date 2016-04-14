	<?php
   include_once 'open_flash_chart_object.php';
   $url=site_url('dashboard/priweekly/');
   $url1=site_url('system/application/');
   ?>
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title" id="myModalLabel"><?php echo $module['title'];?></h4>
			</div>
			<div class="modal-body">
				 <? if($type == "calltrack") { 
				     open_flash_chart_object( 800, 400, 'dashboard/priweekly/', false,$url1);
				  } elseif($type == "ivrs") {
					open_flash_chart_object(800, 400, 'dashboard/ivrs_priweekly/', false,$url1);
				  } elseif($type == "pbx") {
					 open_flash_chart_object( 800, 400, 'dashboard/pbx_priweekly/', false,$url1);
				  }?>  
			</div>
		
		</div>
	</div>
	
