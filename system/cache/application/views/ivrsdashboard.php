<?php
   include_once 'open_flash_chart_object.php';
   //$url=site_url('system/application/views/tutorial-data-2.php');
   $url=site_url('dashboard/priweekly/');
   $url1=site_url('system/application/');
   
   ?>
<div id="main-content">
   <div class="page-title">
      <i class="icon-custom-left"></i>
      <h3><strong>Dashboard</strong></h3>
   </div>
   <div class="row">
      <div class="col-md-12">
            <ul id="myTab2" class="nav nav-tabs nav-dark">
               <li class=""><a href="<?php echo site_url($this->lang->language['menu'][0]);?>">Dashboard</a></li>
               <?php if(isset($feature['call']) && $feature['call']=="1"){?>
               <li class=""><a href="<?php echo site_url($this->lang->language['menu'][84]);?>" >MCube Track</a></li>
               <?php } ?>
               <?php if(isset($feature['ivrs']) && $feature['ivrs']=="1"){?>
               <li class="active"><a href="<?php echo site_url($this->lang->language['menu'][85]);?>">IVRS</a></li>
               <?php } ?>
               <?php if(isset($feature['pbx']) && $feature['pbx']=="1"){?>
               <li class=""><a href="<?php echo site_url($this->lang->language['menu'][86]);?>">MCube X</a></li>
               <?php } ?>
               <?php if(isset($feature['lead']) && $feature['lead']=="1"){?>
               <li class=""><a href="<?php echo site_url($this->lang->language['menu'][87]);?>">Lead</a></li>
               <?php } ?>
               <?php if(isset($feature['support']) && $feature['support']=="1"){?>
               <li class=""><a href="<?php echo site_url($this->lang->language['menu'][88]);?>">Support</a></li>
               <?php }?>
            </ul>
          </div>
       </div>
          
               <!--- ---------- Dashboard tab starts here --------------- --->
               <table>
                  <tr>
                     <td>
             <?php $j=0; ?>
                           <ul>
					  <div class="row">
						  <?php $j++; ?>
						  <div class="col-md-4">
                           <li>
                            <div class="bg-blue panel no-bd">
								   <div class="panel-heading clearfix pos-rel">
                                    <div class="panel-heading text-center p-10 p-b-0">
                                    <h2 class="panel-title c-white headingclass"><?php echo $this->lang->line('level_lastcalls');?> For Last 7 Days</h2>
                                 </div>
                                </div>
                                  <div class="panel-body bg-blue p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                          <div class="withScroll" data-height="320">
                                             <table class="table tabdes table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                                <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                                   <th><?php echo $this->lang->line('level_from');?></th>
                                                   <!--
                                                      <th><?php //echo $this->lang->line('level_group');?></th>
                                                      -->
                                                   <th><?php echo $this->lang->line('level_empname');?></th>
                                                </tr>
                                                <?php
                                                   for($i=0;$i<sizeof($ivrs_last_calls);$i++){
                                                   	echo "<tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'> 
                                                   			<td>".$ivrs_last_calls[$i]['callfrom']."</td>
                                                   			
                                                   			<td>".$ivrs_last_calls[$i]['empname']."</td>
                                                   		</tr>";
                                                   		//~ <td>".$ivrs_last_calls[$i]['groupname']."</td>
                                                   }
                                                   ?>	
                                             </table>
                                            </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </li>
                         </div>
                        <?php if(($j%3) == 0) {echo $j; ?>
						</div>
                        <div class="row">
							<?php $j++; ?>
							  <div class="col-md-4">
							<?php } else { ?>
						  <div class="col-md-4">
							  <?php } ?> 
                           <li>
                              <div class="bg-green panel no-bd">
								<div class="panel-heading clearfix pos-rel">
                                    <div class="panel-heading text-center p-10 p-b-0">
                                    <h2 class="panel-title c-white headingclass"><?php echo $this->lang->line('level_performancereport');?> For Last 7 Days</h2>
                                 </div>
                                </div>
                                   <div class="panel-body bg-green p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                          <div class="withScroll" data-height="320">
                                             <table class="table tabdes table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                                <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                                   <th>Group</th>
                                                   <th>Call Count</th>
                                                </tr>
                                                <tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                                   <td><label><a href="Report/call"><?php echo $this->lang->line('level_TotalCalls');?></a></label></td>
                                                   <td align="right"><?php foreach($ivrs_total_calls as $totalcall){ foreach($totalcall as $call){ echo $call;}} ?></td>
                                                </tr>
                                                <?php 
                                                   for($i=0;$i<sizeof($ivrs_groupwise_todaycall);$i++){
                                                   	echo "<tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                                   		<td>".$ivrs_groupwise_todaycall[$i]['title']."
                                                   		</td><td align='right'>".$ivrs_groupwise_todaycall[$i]['count']."</td></tr>";
                                                   }
                                                   ?>
                                             </table>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                               </div>
                              </div>
                           </li>
                         </div>
                   <?php if($this->session->userdata('eid')==1){?>
                        <?php if(($j%3) == 0) {echo $j; ?>
						</div>
                        <div class="row">
							<?php $j++; ?>
							  <div class="col-md-4">
							<?php } else { ?>
						  <div class="col-md-4">
							  <?php } ?> 

                           <li>
                              <div class="panel no-bd bg-red ">
								<div class="panel-heading clearfix pos-rel">
                                    <div class="panel-heading clearfix pos-rel text-center p-10 p-b-0">
                                    <h2 class="panel-title c-white headingclass"><?php echo "Landing Number Status";?></h2>
                                 </div>
                                   <div class="panel-body bg-red p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                          <div class="withScroll" data-height="320">
                                             <table  class="table tabdes table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                                <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                                   <th><?php echo $this->lang->line('level_landingnumber');?></th>
                                                   <!--
                                                      <th><?php //echo $this->lang->line('level_type');?></th>
                                                      -->
                                                   <th><?php echo $this->lang->line('level_title');?></th>
                                                   <!--
                                                      <th><?php// echo $this->lang->line('level_lastcall');?></th>
                                                      <th><?php// echo "Credit Limit ";?></th>
                                                      -->
                                                   <th><?php echo "Used ";?></th>
                                                </tr>
                                                <?php
                                                   for($i=0;$i<sizeof($ivrs_Lastcalls);$i++)
                                                   {
                                                   	$Plan=($ivrs_Lastcalls[$i]['climit']=="10000")?'Unlimited Plan':$ivrs_Lastcalls[$i]['climit'];
                                                   echo "<tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                                   		<td>".$ivrs_Lastcalls[$i]['landingnumber']."</td>
                                                   		<td>".$ivrs_Lastcalls[$i]['title']."</td>
                                                   		<td>".$ivrs_Lastcalls[$i]['used']."</td>
                                                   	  </tr>";  
                                                   }
                                                   		//~ <td>".$ivrs_Lastcalls[$i]['lastcall']."</td>
                                                   		//~ <td>".$Plan."</td>
                                                   		//~ <td>".$ivrs_Lastcalls[$i]['type']."</td>
                                                   ?>		
                                             </table>
                                            </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </li>
                        </div>
                        <? }?>
                        <?php if(($j%3) == 0) {echo $j; ?>
						</div>
                        <div class="row">
							<?php $j++; ?>
							  <div class="col-md-4">
							<?php } else { ?>
						  <div class="col-md-4">
							  <?php } ?> 
                           <li>
                              <div class=" bg-purple-gradient  panel no-bd">
								<div class="panel-heading clearfix pos-rel">
                                    <div class="panel-heading text-center p-10 p-b-0">
                                    <h2 class="panel-title c-white headingclass">Follow ups For Next 7 Days</h2>
                                 </div>
                                </div>
                               <div class="panel-body bg-purple-gradient p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                          <div class="withScroll" data-height="320">
                                             <table class="table tabdes table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                                <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                                   <th><?php echo $this->lang->line('level_callfrom');?></th>
                                                   <th><?php echo $this->lang->line('level_followupdate');?></th>
                                                </tr>
                                                <?php
                                                   for($i=0;$i<sizeof($ivrs_followUps);$i++){
                                                   	$links='';
                                                   	$glink='';
                                                   	switch($ivrs_followUps[$i]['source']){
                                                   		case 'calltrack':
                                                   			$link="Report/activerecords/".$ivrs_followUps[$i]['callid']."/1";
                                                   			$glink="group/activerecords/".$ivrs_followUps[$i]['gid']."/1'";
                                                   			
                                                   			break;
                                                   		case 'ivrs':
                                                   			 $link="ivrs/calldetail/".$ivrs_followUps[$i]['callid'];
                                                   			 $glink="group/activerecords/".$ivrs_followUps[$i]['gid']."/1'";
                                                   			 break;
                                                   		case 'pbx':
                                                   			 $link='pbx/detail/'.$ivrs_followUps[$i]['gid'];
                                                   			 $glink="javascript:void(0)";
                                                   			 break;
                                                   		case 'leads':
                                                   			 $link='leads/active_lead/'.$ivrs_followUps[$i]['callid'];
                                                   			 $glink="group/activerecords/".$ivrs_followUps[$i]['gid']."/1'";
                                                   			 break;
                                                   		case 'campaign':
                                                   			 $link='campaign/reportDetails/'.$ivrs_followUps[$i]['callid'];
                                                   			 $glink="campaign/details/".$ivrs_followUps[$i]['gid'];
                                                   			 break;
                                                   	}
                                                   	$flink=($ivrs_followUps[$i]['source']!="leads")?"Report/followup/".$ivrs_followUps[$i]['callid']."/1":"Report/followup/".$ivrs_followUps[$i]['callid']."/1";
                                                   	$mlink=($ivrs_followUps[$i]['source']!="pbx" && $ivrs_followUps[$i]['source']!="ivrs")?"<a class='btn-danger' data-toggle='modal' data-target='#modal-responsive' href='".$glink."'>".$ivrs_followUps[$i]['groupname']."</a>":$ivrs_followUps[$i]['groupname'];
                                                   	
                                                   	echo "<tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                                   		<td><a class='btn-danger' data-toggle='modal' data-target='#modal-responsive' href='".$link."'>".$ivrs_followUps[$i]['callfrom']."</a></td>
                                                   		<td><a class='btn-danger' data-toggle='modal' data-target='#modal-responsive' href='".$flink."'>".$ivrs_followUps[$i]['followupdate']."</a></td>
                                                   	  </tr>";
                                                   }
                                                   //~ <td>".$ivrs_followUps[$i]['source']."</td>
                                                   //~ <td>".$ivrs_followUps[$i]['callername']."</td>
                                                      //~ <td>".$mlink."</td>
                                                   //~ <td><a class='btn-danger' data-toggle='modal' data-target='#modal-responsive' href='Employee/activerecords/".$ivrs_followUps[$i]['eid']."/1'>".$ivrs_followUps[$i]['empname']."</a></td>
                                                   ?>		
                                           </table>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                             </div>
                           </li>
                           </div>
                       <?php if(($j%3) == 0) {echo $j; ?>
						</div>
                        <div class="row">
							<?php $j++; ?>
							  <div class="col-md-4">
							<?php } else { ?>
						  <div class="col-md-4">
							  <?php } ?> 
                           <li>
                               <div class="panel-default panel no-bd" >
								  <div class="panel-heading padddef clearfix pos-rel">
                                    <div class="panel-heading text-center p-10 p-b-0">
                                    <h2 class="panel-title width-100p c-blue text-center w-500 carrois"><?php echo $this->lang->line('level_Recent_calls');?> For Last 7 Days</h2>
                                 </div>
                                 </div>
                                 <div class="panel-body panel-default p-t-0 p-b-10">
								   <div class="row">
									  <div class="col-md-12">
									     <div class="row m-b-10">
                                          <div class="withScroll" data-height="320">
                                             <?php open_flash_chart_object( 315, 308, 'dashboard/ivrs_groupwisecall/', false,$url1);?>  
                                          </div>
                                         </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </li>
                            </div>
						   <?php if(($j%3) == 0) {echo $j; ?>
						</div>
                        <div class="row">
							<?php $j++; ?>
							  <div class="col-md-4">
							<?php } else { ?>
						  <div class="col-md-4">
							  <?php } ?> 
                           <li>
                            <div class="bg-dark panel no-bd ">
							 <div class="panel-heading clearfix pos-rel">
                                 <div class="panel-heading text-center p-10 p-b-0">
                                    <h2 class="panel-title c-white headingclass"><?php echo $this->lang->line('level_weeklycall');?> For Last 7 Days</h2>
                                  </div>
                                </div>
                                <div class="panel-body bg-dark p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                          <div class="withScroll" data-height="320">
                                             <? open_flash_chart_object( 415, 308, 'dashboard/ivrs_priweekly/', false,$url1);?>          
                                          </div>
                                         </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </li>
                          </div>
                     <?php if(($j%3) == 0) {echo $j; ?>
						</div>
                        <div class="row">
							<?php $j++; ?>
							  <div class="col-md-4">
							<?php } else { ?>
						  <div class="col-md-4">
							  <?php } ?> 
                           <li>
                              <div class="bg-blue panel no-bd">
								  <div class="panel-heading clearfix pos-rel">
                                 <div class="panel-heading text-center p-10 p-b-0">
                                    <h2 class="panel-title c-white headingclass"><?php echo "Calls By Time for Last 7 days";?> </h2>
                                   </div>
                                 </div>
                                <div class="panel-body bg-blue p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                          <div class="withScroll" data-height="320">
                                             <? open_flash_chart_object(415, 308, 'Report/ivrs_callbytime/', false,$url1); ?>  
                                          </div>
                                       </div>
                                    </div>
                                  </div>
                                 </div>
                              </div>
                           </li>
                           </div>
                             <?php if(($j%3) == 0) {echo $j; ?>
						</div>
                        <div class="row">
							<?php $j++; ?>
							  <div class="col-md-4">
							<?php } else { ?>
						  <div class="col-md-4">
							  <?php } ?> 
                           <li>
                              <div class="bg-green  panel no-bd">
								   <div class="panel-heading clearfix pos-rel">
                                 <div class="panel-heading text-center p-10 p-b-0">
                                    <h2 class="panel-title c-white headingclass"><?php echo "Calls By Day for Last 7 days";?> </h2>
                                 </div>
                                 </div>
                                 <div class="panel-body bg-green p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                          <div class="withScroll" data-height="320">
                                             <? open_flash_chart_object(415, 308, 'Report/ivrs_callbyweek/', false,$url1); ?>  
                                          </div>
                                       </div>
                                    </div>
                                   </div>
                                 </div>
                               </div>
                              </div>
                           </li>
                        </ul>
                     </td>
                  </tr>
               </table>
               <!--- ---------- IVRS Dashboard tab Ends here --------------- --->
            </div>
         </div>
         <div class="modal fade" id="modal-responsive" aria-hidden="true"></div>
      </div>
   </div>
