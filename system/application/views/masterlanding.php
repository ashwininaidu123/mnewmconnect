<?php
   $access=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Business',4);
   $access1=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Number',4);
   $access2=$this->mastermodel->get_access_module($this->session->userdata('role_id'),'Creditassign',0);
   ?>
<div id="main-content">
	<h2><?php echo $html['title'];?></h2>
<div class="row">
<table>
<tr>
   <td>
      <ul>
         <div class="row">
            <?php if($access){ ?>
            <div class="col-md-4">
               <li>
                  <div class="bg-blue panel no-bd">
                     <div class="panel-heading clearfix pos-rel">
                        <div class="panel-heading text-center p-10 p-b-0">
                           <h2 class="panel-title c-white headingclass"><?php echo "Landing Numbers";?></h2>
                        </div>
                     </div>
                     <div class="panel-body bg-blue p-t-0 p-b-10">
                        <div class="row">
                           <div class="col-md-12">
                              <div class="row m-b-10">
                                 <div class="withScroll" data-height="320">
                                    <table class="table tabdes table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                       <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                          <th>Landing Number</th>
                                          <th>Business Name</th>
                                       </tr>
                                       <?php
                                          foreach($lastcalls as $last_row){
                                          	?>
                                       <tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                          <td><?=$last_row['landingnumber']?></td>
                                          <td><?=$last_row['businessname']?></td>
                                          <? //=$this->mastermodel->getLastCall($last_row['bid'],$last_row['type'])?>
                                       </tr>
                                       <?php	
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
            <?php } ?>
            <div class="col-md-4">
               <li>
                  <div class="bg-green panel no-bd">
                     <div class="panel-heading clearfix pos-rel">
                        <div class="panel-heading text-center p-10 p-b-0">
                           <h2 class="panel-title c-white headingclass">Demo Users</h2>
                        </div>
                     </div>
                     <div class="panel-body bg-green p-t-0 p-b-10">
                        <div class="row">
                           <div class="col-md-12">
                              <div class="row m-b-10">
                                 <div class="withScroll" data-height="320">
                                    <table class="table tabdes table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                       <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                          <th>Business Name</th>
                                          <th>City</th>
                                          <th>State</th>
                                       </tr>
                                       <?php
                                          foreach($demousers as $demo_rows){
                                          	?>
                                       <tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                          <td><?=$demo_rows['businessname']?></td>
                                          <td><?=$demo_rows['city']?></td>
                                          <td><?=$demo_rows['state']?></td>
                                       </tr>
                                       <?php	
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
            <div class="col-md-4">
               <li>
                  <div class="panel no-bd bg-red ">
                     <div class="panel-heading clearfix pos-rel">
                        <div class="panel-heading clearfix pos-rel text-center p-10 p-b-0">
                           <h2 class="panel-title c-white headingclass">New Users</h2>
                        </div>
                     </div>
                     <div class="panel-body bg-red p-t-0 p-b-10">
                        <div class="row">
                           <div class="col-md-12">
                              <div class="row m-b-10">
                                 <div class="withScroll" data-height="320">
                                    <table class="table tabdes table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                       <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                          <th>Business Name</th>
                                          <th>City</th>
                                          <th>State</th>
                                       </tr>
                                       <?php
                                          foreach($Newusers as $dnew_rows){
                                          	?>
                                       <tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                          <td><?=$dnew_rows['businessname']?></td>
                                          <td><?=$dnew_rows['city']?></td>
                                          <td><?=$dnew_rows['state']?></td>
                                       </tr>
                                       <?php	
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
         <div class="row">
            <div class="col-md-4">
               <li>
                  <div class=" bg-purple-gradient  panel no-bd">
                     <div class="panel-heading clearfix pos-rel">
                        <div class="panel-heading text-center p-10 p-b-0">
                           <h2 class="panel-title c-white headingclass"><?php echo $this->lang->line('level_performancereport');?> Log</h2>
                        </div>
                     </div>
                     <div class="panel-body bg-purple-gradient p-t-0 p-b-10">
                        <div class="row">
                           <div class="col-md-12">
                              <div class="row m-b-10">
                                 <div class="withScroll" data-height="320">
                                    <table class="table tabdes table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                       <tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                          <th>Action</th>
                                          <th>IP Address</th>
                                       </tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                       <?php
                                          foreach($log as $logrow){
                                          ?>	
                                       <tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                          <td><?=str_replace($this->session->userdata('username'),'You',$logrow['action'])?></td>
                                          <td><?=$logrow['Ipaddress']?></td>
                                       </tr>
                                       <?php	
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
            <div class="col-md-4">
               <li>
                  <div class="panel-default panel no-bd" >
                     <div class="panel-heading padddef clearfix pos-rel">
                        <div class="panel-heading text-center p-10 p-b-0">
                           <h2 class="panel-title width-100p c-blue text-center w-500 carrois"><?php echo $this->lang->line('level_qualifylist');?> Black Numbers </h2>
                        </div>
                     </div>
                     <div class="panel-body panel-default p-t-0 p-b-10">
                        <div class="row">
                           <div class="col-md-12">
                              <div class="row m-b-10">
                                 <div class="withScroll" data-height="320">
                                    <table class="table tabdes1 table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                       <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                          <th>Number</th>
                                          <th>Date & Time</th>
                                       </tr>
                                       <?php
                                          foreach($black as $blackrow){
                                          ?>	
                                       <tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                          <td><?=$blackrow['number']?></td>
                                          <td><?=$blackrow['datetime']?></td>
                                       </tr>
                                       <?php	
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
            <?php if($access1){ ?>
            <div class="col-md-4">
               <li>
                  <div class="bg-dark panel no-bd ">
                     <div class="panel-heading clearfix pos-rel">
                        <div class="panel-heading text-center p-10 p-b-0">
                           <h2 class="panel-title c-white headingclass"><?php echo $this->lang->line('level_Employeewise');?>Unassigned Numbers </h2>
                        </div>
                     </div>
                     <div class="panel-body bg-dark p-t-0 p-b-10">
                        <div class="row">
                           <div class="col-md-12">
                              <div class="row m-b-10">
                                 <div class="withScroll" data-height="320">
                                    <table class="table tabdes table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                       <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                          <th>Pri</th>
                                          <th>Landing Number</th>
                                       </tr>
                                       <?php
                                          foreach($unassigned as $unassignedrow){
                                          ?>	
                                       <tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                          <td><?=$unassignedrow['pri']?></td>
                                          <td><?=$unassignedrow['number']?></td>
                                       </tr>
                                       <?php	
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
         <?php } ?>
         <?php if($access2) { ?>
         <div class="row">
            <div class="col-md-4">
               <li>
                  <div class="bg-blue panel no-bd">
                     <div class="panel-heading clearfix pos-rel">
                        <div class="panel-heading text-center p-10 p-b-0">
                           <h2 class="panel-title c-white headingclass"><?php echo $this->lang->line('level_lastcalls');?> Call Credit</h2>
                        </div>
                     </div>
                     <div class="panel-body bg-blue p-t-0 p-b-10">
                        <div class="row">
                           <div class="col-md-12">
                              <div class="row m-b-10">
                                 <div class="withScroll" data-height="320">
                                    <table class="table tabdes table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                       <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                          <th>Business Name</th>
                                          <th>Credit</th>
                                       </tr>
                                       <?php
                                          foreach($callcount['data'] as $call_count){
                                          ?>	
                                       <tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                          <td><?=$call_count['businessname']?></td>
                                          <td><?=$call_count['credit']?></td>
                                       </tr>
                                       <?php	
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
            <?php } ?>
            <?php if($access2) { ?>
            <div class="col-md-4">
               <li>
                  <div class="bg-green panel no-bd">
                     <div class="panel-heading clearfix pos-rel">
                        <div class="panel-heading text-center p-10 p-b-0">
                           <h2 class="panel-title c-white headingclass">SMS Credit</h2>
                        </div>
                     </div>
                     <div class="panel-body bg-green p-t-0 p-b-10">
                        <div class="row">
                           <div class="col-md-12">
                              <div class="row m-b-10">
                                 <div class="withScroll" data-height="320">
                                    <table class="table tabdes table-striped table-hover" cellpadding="0" cellspacing="0" border="0">
                                       <tr class="sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable">
                                          <th>Business Name</th>
                                          <th>Credit</th>
                                       </tr>
                                       <?php
                                          foreach($smscredit['data'] as $smscredit){
                                          ?>	
                                       <tr class='sortable  coldes bd-3 bg-opacity-20  fade in ui-sortable'>
                                          <td><?=$smscredit['businessname']?></td>
                                          <td><?=$smscredit['credit']?></td>
                                       </tr>
                                       <?php	
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
         <?php } ?>
      </ul>
