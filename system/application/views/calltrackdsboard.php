<?php
   include_once 'open_flash_chart_object.php';
   $url=site_url('dashboard/priweekly/');
   $url1=site_url('system/application/');
   ?>
<div id="main-content">
   <div class="page-title">
      <i class="icon-custom-left"></i>
      <h3><strong>MCube Track Dashboard</strong></h3>
   </div>
   <!--<div class="row">
      <div class="col-md-12">
            <ul id="myTab2" class="nav nav-tabs nav-dark">
               <li class=""><a href="<?php echo site_url($this->lang->language['menu'][0]);?>">Dashboard</a></li>
               <?php if(isset($feature['call']) && $feature['call']=="1"){?>
               <li class="active"><a href="<?php echo site_url($this->lang->language['menu'][84]);?>" >MCube Track</a></li>
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
       </div>-->
          
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
                                                   <th><?php echo $this->lang->line('level_empname');?></th>
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
                             <div class="bg-green panel no-bd">
								<div class="panel-heading clearfix pos-rel">
                                    <div class="panel-heading text-center p-10 p-b-0">
                                    <h2 class="panel-title c-white headingclass"><?php echo "Landing Number Status";?></h2>
                                 </div>
                               </div>
                                  <div class="panel-body bg-green p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                          <div class="withScroll" data-height="320">
                                             <table class="table tabdes table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                                <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                                   <th><?php echo $this->lang->line('level_landingnumber');?></th>
                                                   <th><?php echo $this->lang->line('level_title');?></th>
                                                   <th><?php echo "Used ";?></th>
                                                   <!--
                                                      <th><?php// echo $this->lang->line('level_type');?></th>
                                                      <th><?php//echo "Credit Limit ";?></th>
                                                      <th><?php //echo $this->lang->line('level_lastcall');?></th>
                                                      -->
                                                </tr>
                                                <?php
                                                   for($i=0;$i<sizeof($calltrack_lastcalls);$i++)
                                                   {
                                                   	$Plan=($calltrack_lastcalls[$i]['climit']=="10000")?'Unlimited Plan':$calltrack_lastcalls[$i]['climit'];
                                                   echo "<tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                                   		<td>".$calltrack_lastcalls[$i]['landingnumber']."</td>
                                                           <td>".$calltrack_lastcalls[$i]['title']."</td>
                                                   		<td>".$calltrack_lastcalls[$i]['used']."</td>
                                                   	
                                                   	  </tr>";
                                                   	  	//~ <td>".$calltrack_lastcalls[$i]['type']."</td>
                                                   		//~ <td>".$calltrack_lastcalls[$i]['lastcall']."</td>
                                                   			//~ <td>".$Plan."</td>
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
                                    <h2 class="panel-title c-white headingclass">Follow ups For Next 7 Days</h2>
                                 </div>
                                </div>
                                  <div class="panel-body bg-red p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                            <div class="withScroll" data-height="320">
                                             <table class="table tabdes table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                                <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                                   <th><?php echo $this->lang->line('level_callfrom');?></th>
                                                   <!--
                                                      <th><?php //echo $this->lang->line('level_callername');?></th>
                                                      <th><?php //echo "Group";?></th>
                                                      <th><?php// echo "Employee";?></th>
                                                      <th><?php// echo "Source";?></th>
                                                      -->
                                                   <th><?php echo $this->lang->line('level_followupdate');?></th>
                                                </tr>
                                                <?php
                                                   for($i=0;$i<sizeof($call_followUps);$i++){
                                                   	$links='';
                                                   	$glink='';
                                                   	switch($call_followUps[$i]['source']){
                                                   		case 'calltrack':
                                                   			$link="Report/activerecords/".$call_followUps[$i]['callid']."/1";
                                                   			$glink="group/activerecords/".$call_followUps[$i]['gid']."/1'";
                                                   			
                                                   			break;
                                                   		
                                                   	}
                                                   	$flink=($call_followUps[$i]['source']!="leads")?"Report/followup/".$call_followUps[$i]['callid']."/1":"Report/followup/".$call_followUps[$i]['callid']."/1";
                                                   	$mlink=($call_followUps[$i]['source']!="pbx" && $call_followUps[$i]['source']!="ivrs")?"<a class='btn-danger' data-toggle='modal' data-target='#modal-responsive' href='".$glink."'>".$call_followUps[$i]['groupname']."</a>":$call_followUps[$i]['groupname'];
                                                   	
                                                   	echo "<tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                                   		
                                                   		<td><a class='btn-danger' data-toggle='modal' data-target='#modal-responsive' href='".$link."'>".$call_followUps[$i]['callfrom']."</a></td>
                                                   		
                                                   		<td><a class='btn-danger' data-toggle='modal' data-target='#modal-responsive' href='".$flink."'>".$call_followUps[$i]['followupdate']."</a></td>
                                                   	  </tr>";
                                                   }       
                                                          //~ <td>".$call_followUps[$i]['source']."</td>
                                                           //~ <td>".$call_followUps[$i]['callername']."</td>
                                                   		//~ <td>".$mlink."</td>
                                                   	  //~ <td><a class='btn-danger' data-toggle='modal' data-target='#modal-responsive' href='Employee/activerecords/".$call_followUps[$i]['eid']."/1'>".$call_followUps[$i]['empname']."</a></td>
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
                                <div class=" bg-purple  panel no-bd">
									<div class="panel-heading clearfix pos-rel">
                                    <div class="panel-heading text-center p-10 p-b-0">
                                    <h2 class="panel-title c-white headingclass"><?php echo $this->lang->line('level_performancereport');?> For Last 7 Days</h2>
                                   </div>
                                 </div>
                                  <div class="panel-body bg-purple p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                          <div class="withScroll" data-height="320">
                                             <table class="table tabdes table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                                <tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                                   <th>Group</th>
                                                   <th>Call Count</th>
                                                </tr>
                                                <tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                                   <td><label><a href="TrackReport/all"><?php echo $this->lang->line('level_TotalCalls');?></a></label></td>
                                                   <td align="right"><?php echo $total_calls;?></td>
                                                </tr>
                                                <?php 
                                                   for($i=0;$i<sizeof($groupwise_calls);$i++){
                                                   	echo "<tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                                   		<td><a href='TrackReport/group/".$groupwise_calls[$i]['gid']."'>".$groupwise_calls[$i]['groupname']."</a>
                                                   		</td><td align='right'>".$groupwise_calls[$i]['count']."</td></tr>";
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
                              <div class="panel-default panel no-bd" >
								  <div class="panel-heading padddef clearfix pos-rel">
                                 <div class="panel-heading text-center p-10 p-b-0">
                                    <h2 class="panel-title width-100p c-blue text-center w-500 carrois"><?php echo $this->lang->line('level_qualifylist');?> For Last 7 Days</h2>
                                 </div>
                                 </div>
                                   <div class="panel-body panel-default p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                          <div class="withScroll" data-height="320">
                                             <table class="table tabdes1 table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                                <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                                   <th>Call Type</th>
                                                   <th>Total Call Count</th>
                                                   <th>Unique Call Count</th>
                                                </tr>
                                                <tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                                   <td><label><a href="AttTrackReport/0"><?php echo $this->lang->line('level_AttendCalls');?></a></label></td>
                                                   <td align='right'><?=$attend_calls;?></td>
                                                   <td align='right'><?=$attend_calls1;?></td>
                                                </tr>
                                                <tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                                   <td><label><a href="MissedTrackReport/0"><?php echo $this->lang->line('level_MissedCalls');?></a></label></td>
                                                   <td align='right'><?=$missed_calls;?></td>
                                                   <td align='right'><?=$missed_calls1;?></td>
                                                </tr>
                                                <tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                                   <td><label><a href="QualTrackReport/0"><?php echo $this->lang->line('level_TotalQCalls');?></a></label></td>
                                                   <td align='right'><?=$qualify_calls;?></td>
                                                   <td align='right'><?=$qualify_calls1;?></td>
                                                </tr>
                                                <tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                                   <td><label><a href="UnQualTrackReport/0"><?php echo $this->lang->line('level_TotaluQCalls');?></a></label></td>
                                                   <td align='right'><?=$unqualify_calls;?></td>
                                                   <td align='right'><?=$unqualify_calls1;?></td>
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
                                    <h2 class="panel-title c-white headingclass"><?php echo $this->lang->line('level_Employeewise');?> For Last 7 Days</h2>
                                 </div>
                                 </div>
                                  <div class="panel-body bg-dark p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                          <div class="withScroll" data-height="320">
                                             <table class="table tabdes table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                                <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                                   <th>Employee</th>
                                                   <th>Call Count</th>
                                                   <th>Unique Call Count</th>
                                                </tr>
                                                <?php
                                                   for($i=0;$i<sizeof($empwise_calls);$i++)
                                                   {
                                                   	$style = ($empwise_calls[$i]['status']=='0') ? "style='color:'red'": "";
                                                   echo "<tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                                   		<td width='60%'><label><a href='TrackReport/emp/".$empwise_calls[$i]['eid']."' ".$style.">".$empwise_calls[$i]['empname']."</a></label></td> 
                                                   		<td align='right' width='20%'>".$empwise_calls[$i]['count']."</td>
                                                   		<td align='right' width='20%'>".$empwise_calls[$i]['ucount']."</td>
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
                                    <h2 class="panel-title c-white headingclass"><?php echo $this->lang->line('level_Recent_calls');?> For Last 7 Days</h2>
                                 </div>
                                 </div>
                                  <div class="panel-body bg-blue p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                          <div class="withScroll" data-height="320">
                                             <?php open_flash_chart_object( 315, 308, 'dashboard/groupwisecall/', false,$url1);?> 
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
                                    <h2 class="panel-title c-white headingclass"><?php echo $this->lang->line('level_weeklycall');?> For Last 7 Days</h2>
                                 </div>
                                 </div>
                                  <div class="panel-body bg-green p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                          <div class="withScroll" data-height="320">
									      <div class="thumbnail">
											 <? open_flash_chart_object( 415, 308, 'dashboard/priweekly/', false,$url1);?>   
										   <div class="overlay">
                                            <div class="thumbnail-actions">
                                            <a href="dashboard/weekpopup/calltrack"  class="btn-danger btn btn-default btn-icon btn-rounded magnific" data-toggle="modal" data-target="#modal-responsive" title="Weekly Call"><i class="fa fa-search"></i></a>
                                            </div>
                                           </div>
                                            </div>
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
                              <div class="panel no-bd  bg-red ">
								  <div class="panel-heading clearfix pos-rel">
                                 <div class="panel-heading text-center p-10 p-b-0">
                                    <h2 class="panel-title c-white headingclass"><?php echo $this->lang->line('groupwise_missedcalls');?> For Last 7 Days</h2>
                                 </div>
                                 </div>
                                   <div class="panel-body bg-red p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                          <div class="withScroll" data-height="320">
                                             <?php open_flash_chart_object( 315, 308, 'dashboard/groupwisemisscall/', false,$url1);?>  
                                             
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
                              <div class=" bg-purple panel no-bd">
								  <div class="panel-heading clearfix pos-rel">
                                 <div class="panel-heading text-center p-10 p-b-0">
                                    <h2 class="panel-title c-white headingclass"><?php echo $this->lang->line('groupwise_qualifycalls');?> For Last 7 Days</h2>
                                 </div>
                                 </div>
                                  <div class="panel-body bg-purple p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                          <div class="withScroll" data-height="320">
                                             <?php open_flash_chart_object( 315, 250, 'dashboard/qualifedcalls/', false,$url1);?>  
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
								  <div class="panel-heading  padddef clearfix pos-rel">
                                 <div class="panel-heading text-center p-10 p-b-0">
                                    <h2 class="panel-title width-100p c-blue text-center w-500 carrois"><?php echo "Calls By Time for Last 7 days";?> </h2>
                                 </div>
                                 </div>
                                   <div class="panel-body  panel-default p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                          <div class="withScroll" data-height="320">
										   <div class="thumbnail">
                                             <? open_flash_chart_object(415, 308, 'Report/callbytime/', false,$url1); ?> 
                                       	   <div class="overlay">
                                            <div class="thumbnail-actions">
                                            <a href="dashboard/callbytime/calltrack"  class="btn-danger btn btn-default btn-icon btn-rounded magnific" data-toggle="modal" data-target="#modal-responsive" ><i class="fa fa-search"></i></a>
                                            </div>
                                           </div> 
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
                                    <h2 class="panel-title c-white headingclass"><?php echo "Calls By Day for Last 7 days";?> </h2>
                                 </div>
                                 </div>
                                  <div class="panel-body bg-dark p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                          <div class="withScroll" data-height="320">
											 <div class="thumbnail">
                                             <? open_flash_chart_object(415, 308, 'Report/callbyweek/', false,$url1); ?>
                                              <div class="overlay">
                                            <div class="thumbnail-actions">
                                            <a href="dashboard/callbyweek/calltrack"  class="btn-danger btn btn-default btn-icon btn-rounded magnific" data-toggle="modal" data-target="#modal-responsive" ><i class="fa fa-search"></i></a>
                                            </div>
                                           </div>   
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
                                    <h2 class="panel-title c-white headingclass"><?php echo "Calls By Region for Last 7 days";?> </h2>
                                 </div>
                                 </div>
                                   <div class="panel-body bg-blue p-t-0 p-b-10">
									  <div class="row">
										<div class="col-md-12">
										  <div class="row m-b-10">
                                          <div class="withScroll" data-height="320">
                                             <? open_flash_chart_object(315, 308, 'Report/callbyregion/', false,$url1); ?>  
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
               <!--- ---------- Call Track Dashboard tab Ends here --------------- --->
            </div>
         </div>
         <div class="modal fade" id="modal-responsive" aria-hidden="true"></div>
      </div>
   </div>


