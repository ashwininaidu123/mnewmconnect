<?php
   include_once 'open_flash_chart_object.php';
   $url=site_url('dashboard/priweekly/');
   $url1=site_url('system/application/');
   ?>
<div id="main-content" class="dashboard">
   <div class="page-title">
      <i class="icon-custom-left"></i>
      <h3><strong>Dashboard</strong></h3>
   </div>
   <div class="row">
      <div class="col-md-12">
            <ul id="myTab2" class="nav nav-tabs nav-dark">
               <li class="active"><a href="<?php echo site_url($this->lang->language['menu'][0]);?>">Dashboard</a></li>
               <?php if(isset($feature['call']) && $feature['call']=="1"){?>
               <li class=""><a href="<?php echo site_url($this->lang->language['menu'][84]);?>" >MCube Track</a></li>
               <?php } ?>
               <?php if(isset($feature['ivrs']) && $feature['ivrs']=="1"){?>
               <li class=""><a href="<?php echo site_url($this->lang->language['menu'][85]);?>">IVRS</a></li>
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
                                                      <th><?php echo $this->lang->line('level_group');?></th>
                                                      <th><?php echo "Employee";?></th>
                                                   </tr>
                                                   <?php
                                                      for($i=0;$i<sizeof($last_calls);$i++){
                                                      	echo "<tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                                      			<td>".$last_calls[$i]['callfrom']."</td>
                                                      			<td>".$last_calls[$i]['groupname']."</td>
                                                      			<td>".$last_calls[$i]['empname']."</td>
                                                      		  </tr>";
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
                               	 <?php $j++; ?>
                               <div class="col-md-4">
                              <li>
                                 <div class="bg-green panel no-bd">
									 	<div class="panel-heading clearfix pos-rel">
                                    <div class="panel-heading text-center p-10 p-b-0">
                                       <h2 class="panel-title c-white headingclass"><?php echo $this->lang->line('level_returningcustomer');?> For Last 7 Days</h2>
                                    </div>
                                    </div>
                                   <div class="panel-body bg-green p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                             <div class="withScroll" data-height="320">
                                                <table class="table tabdes table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                                   <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                                      <th><?php echo $this->lang->line('level_from');?></th>
                                                      <th><?php echo $this->lang->line('level_group');?></th>
                                                      <th><?php echo "Employee";?></th>
                                                   </tr>
                                                   <?php
                                                      for($i=0;$i<sizeof($returning_custmer);$i++)
                                                      {
                                                      echo "<tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                                      	<td>".$returning_custmer[$i]['callfrom']."</td>
                                                      	<td>".$returning_custmer[$i]['groupname']."</td>
                                                      	<td>".$returning_custmer[$i]['empname']."</td>
                                                      	</tr>";
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
							  <?php $j++;  ?>
                               <div class="col-md-4">
                              <li>
                                 <div class="panel no-bd bg-red ">
									 	<div class="panel-heading clearfix pos-rel">
                                    <div class="panel-heading clearfix pos-rel text-center p-10 p-b-0">
                                       <h2 class="panel-title c-white headingclass"><?php echo "Landing Number Status";?></h2>
                                    </div>
                                    </div>
                                   <div class="panel-body bg-red p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                             <div class="withScroll" data-height="320">
                                                <table class="table tabdes table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                                   <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                                      <th><?php echo $this->lang->line('level_landingnumber');?></th>
                                                      <th><?php echo $this->lang->line('level_title');?></th>
                                                      <!--
                                                         <th><?php// echo $this->lang->line('level_lastcall');?></th>
                                                         <th><?php// echo "Credit Limit ";?></th>
                                                         <th><?php// echo $this->lang->line('level_type');?></th>
                                                         -->
                                                      <th><?php echo "Used ";?></th>
                                                   </tr>
                                                   <?php
                                                      for($i=0;$i<sizeof($lastcalls);$i++)
                                                      {
                                                      	$Plan=($lastcalls[$i]['climit']=="10000")?'Unlimited Plan':$lastcalls[$i]['climit'];
                                                      echo "	<tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                                      		<td>".$lastcalls[$i]['landingnumber']."</td>
                                                      		<td>".$lastcalls[$i]['title']."</td>
                                                      		<td>".$lastcalls[$i]['used']."</td>
                                                      	  </tr>";
                                                      	  /*<td>".$lastcalls[$i]['lastcall']."</td>
                                                      		<td>".$Plan."</td> 
                                                      		<td>".$lastcalls[$i]['type']."</td>*/
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
                               <? }?>
                             
                        <?php if(($j%3) == 0) { ?>
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
                                                      for($i=0;$i<sizeof($followups);$i++){
                                                      	$links='';
                                                      	$glink='';
                                                      	switch($followups[$i]['source']){
                                                      		case 'calltrack':
                                                      			$link="Report/activerecords/".$followups[$i]['callid']."/1";
                                                      			$glink="group/activerecords/".$followups[$i]['gid']."/1'";
                                                      			
                                                      			break;
                                                      		case 'ivrs':
                                                      			 $link="ivrs/calldetail/".$followups[$i]['callid'];
                                                      			 $glink="group/activerecords/".$followups[$i]['gid']."/1'";
                                                      			 break;
                                                      		case 'pbx':
                                                      			 $link='pbx/detail/'.$followups[$i]['gid'];
                                                      			 $glink="javascript:void(0)";
                                                      			 break;
                                                      		case 'leads':
                                                      			 $link='leads/active_lead/'.$followups[$i]['callid'];
                                                      			 $glink="leads/leadgrp_active/".$followups[$i]['gid']."/1'";
                                                      			 break;
                                                      		case 'support':
                                                      			 $link='support/activeSupportTkt/'.$followups[$i]['callid'].'/'.$followups[$i]['gid'];
                                                      			 $glink="support/actSupportGrp/".$followups[$i]['gid'];
                                                      			 break;
                                                      	}
                                                      	$flink=($followups[$i]['source']!="leads")?"Report/followup/".$followups[$i]['callid']."/1":"Report/followup/".$followups[$i]['callid']."/1";
                                                      	$mlink=($followups[$i]['source']!="pbx" && $followups[$i]['source']!="ivrs")?"<a class='btn-danger' data-toggle='modal' data-target='#modal-responsive' href='".$glink."'>".$followups[$i]['groupname']."</a>":$followups[$i]['groupname'];
                                                      	
                                                      	echo "<tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                                      		<td><a class='btn-danger' data-toggle='modal' data-target='#modal-responsive' href='".$link."'>".$followups[$i]['callfrom']."</a></td>
                                                      		<td><a class='btn-danger' data-toggle='modal' data-target='#modal-responsive' href='".$flink."'>".$followups[$i]['followupdate']."</a></td>
                                                      	  </tr>";
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
                                <?php if(($j%3) == 0) { ?>
						</div>
                        <div class="row">
							<?php $j++; ?>
							  <div class="col-md-4">
							<?php } else { ?>
						  <div class="col-md-4">
							  <?php } ?> 
                         
                              <li>
                                 <div class="panel-default  panel no-bd" >
									 	<div class="panel-heading padddef clearfix pos-rel">
                                    <div class="panel-heading  text-center p-10 p-b-0">
                                       <h2 class="panel-title width-100p c-blue text-center w-500 carrois"><?php echo "Account Balance";?></h2>
                                    </div>
                                    </div>
                                     <div class="panel-body panel-default  p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                             <div class="withScroll" data-height="320">
                                                <table class="table tabdes1 table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                                   <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                                      <th><?php echo "Call Balance ";?></th>
                                                      <th><?php echo "SMS Balance ";?></th>
                                                   </tr>
                                                   <?php echo "<tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'><td>".$call_bal."</td>";?>		
                                                   <?php echo "<td>".$sms_bal."</td></tr>";?>		
                                                </table>
                                             </div>
                                          </div>
                                         </div>
                                       </div>
                                    </div>
                                 </div>
                              </li>
                              </div>
                                <?php if(($j%3) == 0) { ?>
						</div>
                        <div class="row">
							<?php $j++; ?>
							  <div class="col-md-4">
							<?php } else { ?>
						  <div class="col-md-4">
							  <?php } ?>
                              <li>
                                 <div class="bg-dark  panel no-bd ">
									 	<div class="panel-heading clearfix pos-rel">
                                    <div class="panel-heading text-center p-10 p-b-0">
                                       <h2 class="panel-title c-white headingclass"><?php echo "Offline Users";?></h2>
                                    </div>
                                    </div>
                                      <div class="panel-body  bg-dark p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                             <div class="withScroll" data-height="320">
                                                <table  class="table tabdes table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                                   <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                                      <th><?php echo "Employee ";?></th>
                                                      <th><?php echo "Break Start Time";?></th>
                                                      <th><?php echo "Make online";?></th>
                                                   </tr>
                                                   <?php
                                                      for($i=0;$i<sizeof($offlineusers);$i++){
                                                      echo "<tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'><td><a class='btn-danger' data-toggle='modal' data-target='#modal-responsive' href='Employee/activerecords/".$offlineusers[$i]['eid']."/1'>".$offlineusers[$i]['empname']."</a></td>";
                                                      echo "<td>".$offlineusers[$i]['start_time']."</td>";
                                                      echo (($roleDetail['modules']['2']['opt_add'] && $offlineusers[$i]['eid']!=1) || ($offlineusers[$i]['eid'] == $this->session->userdata('eid'))) ? "<td><a href='user/selfdisable/1/".$offlineusers[$i]['eid']."'>Make Online</a></td></tr>" : "<td>Make Online</td></tr>";
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
                             </div>
                           </ul>
                        </td>
                     </tr>
                  </table>
               <!-- ---------- Dashboard tab Ends here --------------- --->
            </div>
         <div class="modal fade" id="modal-responsive" aria-hidden="true"></div>
      </div>
   </div>
</div>
</div>
