<!-- START MAIN CONTENT -->
	<div id="main-content">
	<!-- BEGIN ERROR BOX -->
	<?php 
	if($this->session->flashdata('msgt')){ $error1 = $this->session->flashdata('msgt'); }
	$error = validation_errors();
	if((isset($error) && $error != '') || isset($error1)){
		$display = '';
	}else{
		$display = 'hide';
	}
	?>
	<div class="alert <?=($this->session->flashdata('msgt'))?$this->session->flashdata('msgt'):'error'?> <?=$display;?>" >
		<button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
		<?php echo validation_errors(); ?>
		<?php echo $this->session->flashdata('msg');?>
	</div>
	<!-- END ERROR BOX -->
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading bg-red">
					<h3 class="panel-title" style="width:90%;text-align:center;">
					<span style="float:left;font-size:14px;">
						<?php if(!isset($nobulk)){?>
						<div class="btn-group">&nbsp;&nbsp;
							<a class="dropdown-toggle" data-toggle="dropdown" style='color:#FFF;font-weight:bold;'> Actions <span class="caret"></span></a>
							<ul class="dropdown-menu" style="text-align:left;">
								<?php
								for($m=0;$m<count($links);$m++){
									echo $links[$m];
								} ?>
							</ul>
                         </div>
                         <?php } 
                         if(isset($downlink) && $downlink != ''){ echo $downlink;} ?>
					</span>	
						<?php echo $module['title']; 
						if(isset($unique)){ ?>
						<span style="float:right;font-size:13px;color:#000;margin:0px auto;padding:0px;"><form name='uniqueFrm' method='post' action=''>
							<div class="btn-group" style="float:right;">&nbsp;&nbsp;
								<a class="dropdown-toggle" data-toggle="dropdown" style='color:#FFF;font-weight:bold;'>Unique By:</a>
									<?php
									for($m=0;$m<count($unique);$m++){
										echo $unique[$m];
									} 
									?>
								<span  style='font-size:12px;color:#000;'><input type='submit' style="padding:2px 8px;" name='unique' id='unique' value='Set'></span>
							</div></form>
						</span>
					    <?php  } 
					  if(strstr($_SERVER['PHP_SELF'],'ListLead') && !($leadView == 1 || $leadView == 3)){
							$params = @explode('/',$_SERVER['PHP_SELF']);?>
							<div class="btn-group" style="float:right;font-size:13px;">&nbsp;&nbsp;
								<a class="dropdown-toggle" data-toggle="dropdown" style='color:#FFF;font-weight:bold;'> Lead Types <span class="caret"></span></a>
								<ul class="dropdown-menu" style="text-align:left;">
									<?php
									echo "<li><a href='".base_url()."ListLead/all'> All</a></li>";
									for($k=1;$k<=count($leadstatus);$k++){
										echo "<li><a href='".base_url()."ListLead/".$k."'>".$leadstatus[$k]."</a></li>";
									}
									?>
								</ul>
							</div>
							
								
					    <?php  } ?>
					</h3>
					<input type="hidden" value="0" id="mod_Id" name="mod_Id">
					<input type="hidden" value="" id="fsizec" name="fsizec">
				</div>
				<div class="panel-body">
					<div class="row">
						<?php if(!empty($paging)) { ?>	
						<div class="dataTables_paginate paging_bootstrap table-red">
							<ul class="pagination"><?=$paging;?></ul>
						</div>
						<?php } 
						if(isset($tab) && $tab==true){
							$seg = strstr($this->uri->segment(1),'lead') ? $this->uri->segment(3) : $this->uri->segment(2);
							$clss1 = $clss2 = $clss3='';
							if($seg == 'basic')
								$clss1 = 'active';
							elseif($seg == 'contact')
								$clss2 = 'active';
							elseif($seg == 'all' || in_array('all',$this->uri->segments))
								$clss3 = 'active';
						?>
						<div class="stabs">
							<ul id="myTab" class="nav nav-tabs nav-dark">
								   <li class="<?=$clss3;?>"><a href="<?=$form['search_url']?>all">All</a></li>
								<?php if(isset($tab1) && $tab1==true){?>
									<li class="<?=$clss1;?>"><a href="<?=$form['search_url']?>basic">Basic</a></li>
								<?php 
								$tabcls = (isset($form['search_names']) && sizeof($form['search_names'])>0 && ($clss1 != 'active' && $clss3 != 'active')) ? 'active' : $clss2;
								?>
									<li class="dropdown <?=$tabcls;?>">
										<a href="#" id="myTabDrop1" class="dropdown-toggle" data-toggle="dropdown">More<b class="caret"></b></a>
										<ul class="dropdown-menu" role="menu" aria-labelledby="myTabDrop1">
											<li class="<?=$clss2;?>"><a href="<?=$form['search_url']?>contact">Contact</a></li>
									<?php
									foreach($form['search_names'] as $s_name){ 
										if($this->uri->segment(2) == $s_name['search_id']){
											$cls4 = 'active';
										}else{
											$cls4 = '';
										}
									?>
										<li class="<?=$cls4;?>">
						                <div class="leftflot">	
											<a href="<?=$form['search_url'].$s_name['search_id']?>"><?=$s_name['search_name']?></a>
										</div>
										<div class="rightflot">	
											<a href='Report/del_search/<?php echo $s_name['search_id']."/".str_replace("/","~",$form['search_url'].'all');?>'><span title="Remove" class="glyphicon glyphicon-remove"></span></a>
										</div>
										</li>	
									<?php	
									}
									?>
										</ul>
									</li>

						     <?php } ?>
							</ul>
						</div>
						<?php } ?>						
					</div>
					<div class="row">	
						<div class="col-md-12 col-sm-12 col-xs-12 table-responsive ">
							 <table id="listView" class="table table-striped table-hover table-red">
									<thead class="no-bd">
										<tr class="success">
											<?php foreach($itemlist['header'] as $hd){ ?>
													<th><?=$hd?></th>
											<?php }?>
										</tr>
									</thead>
									<tbody class="no-bd-y">
										<?php
										$i=0;
										foreach($itemlist['rec'] as $item){ 
											?>
											<tr class="<?=($i%2==0) ? '' : 'success';?>">
											<?php foreach($item as $it){?><td><?=$it?></td><? } ?>
											</tr>
										
										<?php $i++;} ?>
									</tbody>
							</table>
						</div>
					</div>
					<div class="row">
						<?php if(!empty($paging)) { ?>	
						<div class="dataTables_paginate paging_bootstrap table-red">
							<ul class="pagination"><?=$paging;?></ul>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- END MAIN CONTENT -->
<div class="modal fade" id="modal-responsive" aria-hidden="true"></div>
<div class="modal fade" id="modal-search" aria-hidden="true"></div>
<div class="modal fade" id="modal-advsearch" aria-hidden="true"></div>
</div>
<!-- END WRAPPER -->
