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
               <li class=""><a href="<?php echo site_url($this->lang->language['menu'][85]);?>">IVRS</a></li>
               <?php } ?>
               <?php if(isset($feature['pbx']) && $feature['pbx']=="1"){?>
               <li class=""><a href="<?php echo site_url($this->lang->language['menu'][86]);?>">MCube X</a></li>
               <?php } ?>
               <?php if(isset($feature['lead']) && $feature['lead']=="1"){?>
               <li class=""><a href="<?php echo site_url($this->lang->language['menu'][87]);?>">Lead</a></li>
               <?php } ?>
               <?php if(isset($feature['support']) && $feature['support']=="1"){?>
               <li class="active"><a href="<?php echo site_url($this->lang->language['menu'][88]);?>">Support</a></li>
               <?php }?>
            </ul>
          </div>
       </div>
          
               <!--- ---------- Dashboard tab starts here --------------- --->
               <table>
                  <tr>
                     <td>
                        <ul>
					<div class="row">
						  <div class="col-md-4">
               <li>
                   <div class="bg-blue panel no-bd">
					 <div class="panel-heading clearfix pos-rel">
                         <div class="panel-heading text-center p-10 p-b-0">
                     <h2 class="panel-title c-white headingclass"><?php echo $this->lang->line('level_Employeewise');?> For Last 7 Days</h2>
                  </div>
                  </div>
                   <div class="panel-body bg-blue p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                           <div class="withScroll" data-height="320">
                              <table class="table tabdes table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                 <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                    <th>Employee</th>
                                    <!--
                                       <th>Employee No</th>
                                       -->
                                    <th>Support Count</th>
                                    <th>Unique Count</th>
                                 </tr>
                                 <?php
                                    for($i=0;$i<sizeof($support_empgroupwise_todaycalls);$i++)
                                    {
                                    	
                                    echo "<tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                    		<td width='60%'><label><a href='TrackReport/emp/".$support_empgroupwise_todaycalls[$i]['eid']."' >".$support_empgroupwise_todaycalls[$i]['employee']."</a></label></td> 
                                    		<td align='right' width='20%'>".$support_empgroupwise_todaycalls[$i]['count']."</td>
                                    		<td align='right' width='20%'>".$support_empgroupwise_todaycalls[$i]['ucount']."</td>
                                    		</tr>";
                                    }
                                    //~ <td align='right' width='20%'>".$support_empgroupwise_todaycalls[$i]['number']."</td>
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
                   <div class="col-md-4">
                <li>
              <div class="bg-green panel no-bd">
								<div class="panel-heading clearfix pos-rel">
                                    <div class="panel-heading text-center p-10 p-b-0">
                     <h2 class="panel-title c-white headingclass">Follow ups For Next 7 Days</h2>
                    </div>
                               </div>
                                  <div class="panel-body bg-green p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                           <div class="withScroll" data-height="320">
                              <table class="table tabdes table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                 <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                    <th><?php echo $this->lang->line('level_callfrom');?></th>
                                    <!--
                                       <th><?php //echo $this->lang->line('level_callername');?></th>
                                       <th><?php// echo "Source";?></th>
                                       <th><?php// echo "Group";?></th>
                                       <th><?php// echo "Employee";?></th>
                                       -->
                                    <th><?php echo $this->lang->line('level_followupdate');?></th>
                                 </tr>
                                 <?php
                                    for($i=0;$i<sizeof($support_followUps);$i++){
                                    	$links='';
                                    	$glink='';
                                    	switch($support_followUps[$i]['source']){
                                    			 case 'support':
                                    			  $link='support/activeSupportTkt/'.$support_followUps[$i]['callid'].'/'.$support_followUps[$i]['gid'];
                                    			 $glink="support/actSupportGrp/".$support_followUps[$i]['gid'];
                                    			 break;
                                    	}
                                    	$flink=($support_followUps[$i]['source']!="leads")?"Report/followup/".$support_followUps[$i]['callid']."/1":"Report/followup/".$support_followUps[$i]['callid']."/1";
                                    	$mlink=($support_followUps[$i]['source']!="pbx" && $support_followUps[$i]['source']!="ivrs")?"<a class='btn-danger' data-toggle='modal' data-target='#modal-responsive' href='".$glink."'>".$support_followUps[$i]['groupname']."</a>":$support_followUps[$i]['groupname'];
                                    	
                                    	echo "<tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                    		<td><a class='btn-danger' data-toggle='modal' data-target='#modal-responsive' href='support/activeSupportTkt/".$support_followUps[$i]['callid']."/".$support_followUps[$i]['gid']."'>".$support_followUps[$i]['callfrom']."</a></td>
                                    		<td><a class='btn-danger' data-toggle='modal' data-target='#modal-responsive' href='".$flink."'>".$support_followUps[$i]['followupdate']."</a></td>
                                    	  </tr>";
                                    }
                                    	//~ <td>".$support_followUps[$i]['source']."</td>
                                    	//~ <td>".$support_followUps[$i]['callername']."</td>
                                    	//~ <td>".$mlink."</td>
                                    	//~ <td><a class='btn-danger' data-toggle='modal' data-target='#modal-responsive' href='Employee/activerecords/".$support_followUps[$i]['eid']."/1'>".$support_followUps[$i]['empname']."</a></td>
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
                     <div class="col-md-4">
                           <li>
                                <div class="panel no-bd bg-red ">
								<div class="panel-heading clearfix pos-rel">
                                    <div class="panel-heading clearfix pos-rel text-center p-10 p-b-0">
                     <h2 class="panel-title c-white headingclass">Assigned Tickets for last 7 Days</h2>
                  </div>
                                </div>
                                  <div class="panel-body bg-red p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                           <div class="withScroll" data-height="320">
                              <table  class="table tabdes table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                 <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                    <th><?php echo "From";?></th>
                                    <th><?php echo "To";?></th>
                                    <!--
                                       <th><?php //echo "Number From";?></th>
                                       <th><?php //echo "Number To"?></th>
                                       -->
                                    <th><?php echo "Assigned Date"?></th>
                                 </tr>
                                 <?php
                                    for($i=0;$i<sizeof($supports_assigned_detail);$i++){
                                    	
                                    	echo "<tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                    		<td>".$supports_assigned_detail[$i]['name']."</td>
                                    		<td>".$supports_assigned_detail[$i]['empname']."</td>
                                    		<td>".$supports_assigned_detail[$i]['createdon']."</td>
                                    		</tr>";
                                    }
                                    		//~ <td>".$supports_assigned_detail[$i]['number']."</td>
                                    		//~ <td>".$supports_assigned_detail[$i]['empnumber']."</td>
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
                                         <div class="row">
						  <div class="col-md-4">
                           <li>
                                <div class=" bg-purple-gradient  panel no-bd">
									<div class="panel-heading clearfix pos-rel">
                                    <div class="panel-heading text-center p-10 p-b-0">
                     <h2 class="panel-title c-white headingclass"><?php echo $this->lang->line('level_supportstatus');?> For Last 7 Days</h2>
                 </div>
                                 </div>
                                  <div class="panel-body bg-purple-gradient p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                           <div class="withScroll" data-height="320">
                              <table class="table tabdes table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                 <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                    <th>Support Status</th>
                                    <th>Total Tickets Count</th>
                                 </tr>
                                 <tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                    <td><label><?php echo "Open";?></label></td>
                                    <td align='right'><?=$open_tickets;?></td>
                                 </tr>
                                 <tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                    <td><label><?php echo "Pending";?></label></td>
                                    <td align='right'><?=$pending_tickets;?></td>
                                 </tr>
                                 <tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                    <td><label><?php echo "Resolved";?></label></td>
                                    <td align='right'><?=$resolved_tickets;?></td>
                                 </tr>
                                 <tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                    <td><label><?php echo "Closed";?></label></td>
                                    <td align='right'><?=$closed_tickets;?></td>
                                 </tr>
                       </table>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                             </div>
                           </li>
                           </div>
                          <div class="col-md-4">
                           <li>
                              <div class="panel-default panel no-bd" >
								  <div class="panel-heading padddef clearfix pos-rel">
                                 <div class="panel-heading text-center p-10 p-b-0">
                     <h2 class="panel-title width-100p c-blue text-center w-500 carrois"><?php echo $this->lang->line('level_Recent_calls_support');?> For Last 7 Days</h2>
                  </div>
                                 </div>
                                   <div class="panel-body panel-default p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                           <div class="withScroll" data-height="320">
                              <?php open_flash_chart_object( 315, 308, 'dashboard/support_groupwisesupports/', false,$url1);?>  
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
