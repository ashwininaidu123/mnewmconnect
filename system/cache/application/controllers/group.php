<?php
class Group extends Controller {
	var $data,$roleDetail,$access;
	function Group(){
		parent::controller();
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('systemmodel');
		$this->load->model('empmodel');
		$this->load->helper('mcube_helper');
		$this->load->model('groupmodel');
		$this->load->model('supportmodel');
		$this->load->model('configmodel');
		$this->load->model('ivrsmodel');
		$this->load->model('pollmodel');
		
		$this->roleDetail=$this->empmodel->getRoledetail($this->session->userdata('roleid'));
	}
	public function __destruct() {
		$this->db->close();
	}
	function index(){
		redirect('ManageGroup/0');
	}
	function Undelte_emplist($id){
		if($this->input->post('update_system')){
			$res=$this->groupmodel->Update_UNdeletegroup($id);
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg',$this->lang->line('undel_msg'));
			redirect("ManageGroup/0");
		}
		$this->sysconfmodel->data['html']['title'] .= " | Select landingNumber";
		$data['module']['title'] = "Select landingNumber";
		$formFields=array();
		$formFields[] = array(
					'label'=>'<label class="col-sm-4 text-right" for="eid">'.$this->lang->line('label_region').' : </label>',
					'field'=>form_dropdown('prinumber',$this->systemmodel->getPriList(),'',"id='prinumber' class='required form-control'"));
		$data['form'] = array(
		            'form_attr'=>array('action'=>'group/Undelte_emplist/'.$id,'name'=>'addgroup','id'=>'addgroup','enctype'=>"multipart/form-data"),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->load->view('form_view2',$data);
	}
	function feature_access($access){
		$show=0;
		$checklist=$this->systemmodel->checked_featuremanage();
		if(in_array($access,$checklist)){
			$show=1;
			}
		return $show;
	}
	function add_group($id='',$clone=''){
		if(!$this->feature_access(1))redirect('Employee/access_denied');
		$this->sysconfmodel->viewLayout('form_view',$this->add_groupfrm($id,$clone));
	}
	function add_grouppopup($id='',$clone=''){
		if(!$this->feature_access(1))redirect('Employee/access_denied');
		$this->load->view('form_view',$this->add_groupfrm($id,$clone));
	}
	function add_groupfrm($id='',$clone=''){
		if(!$this->feature_access(1))redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('groupname', 'Group Name', 'required|min_length[4]|max_length[50]|alpha_numeric|callback_uniquegroup');
			$this->form_validation->set_rules('rules', 'Rule', 'required');
			$this->form_validation->set_rules('prinumber', 'Landing Number', 'required');
			$this->form_validation->set_rules('timeout', 'Time Out to connect Call', 'required');
			if(!$this->form_validation->run() == FALSE){	
				if($id!=""){
					$res=$this->groupmodel->update_group($id);
					if($res){	
						$this->session->set_flashdata('msgt', 'success');
						$this->session->set_flashdata('msg', $this->lang->line('error_groupupdatedsuccmsg'));
						redirect('ManageGroup/0');
					}
				}else{
					$res=$this->groupmodel->create_group();
					if($res){
						$this->session->set_flashdata('msgt', 'success');
						$this->session->set_flashdata('msg', $this->lang->line('error_groupsuccmsg'));
						redirect('AddempGroup/'.$res);
					}
				}
			}	
		}
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_groupfrm');
		$data['module']['title'] = $this->lang->line('label_groupfrm');
		$fieldset = $this->configmodel->getFields('3',$bid,'1');
		$formFields = array();
		$itemDetail = ($id!='') ? $this->configmodel->getDetail('3',$id,'',$bid) : array();
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['3']['opt_add']) redirect('Employee/access_denied');
		$addon='';
		$adds=array();  
		if($id!=''){
			$addons=$this->sysconfmodel->get_baddons_number1($itemDetail['prinumber'],'3');
			foreach($addons as $ads){
				$addon .=$ads['fieldname'].',';
				$adds[]=$ads['fieldname'];
			}
		}
		$leadaccess = $this->sysconfmodel->getfeatureAccess('13');
		$multiSel = @explode(',',$itemDetail['access_reports']);
		foreach($fieldset as $field){
			$eids=$this->empmodel->getEmployee_byname(isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'');
			$checked=false;
			$inArr = array();
			$supChk = ($field['fieldname'] == 'supportgrp') ? $this->groupmodel->supportNumberChk($bid) : ' ' ;
			if($supChk == 0) {
				$inArr = array('supportgrp');
			}
			array_push($inArr,'replytocustomer');
			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],$inArr)){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					$enabled = "";
					if($checked){
						$bday = ($field['fieldname']=='bday' && isset($itemDetail['bday']))?json_decode($itemDetail['bday']):'';
						$enabled=in_array($field['fieldname'],$adds)?'':"'disabled'=>'disabled'";	
						$cf = array('label'=>(($field['fieldname']=='leadaction' ) ? (($leadaccess == 1) ? ('<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" ></label>') :'')
										:'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" > </label>'),
									'field'=>((in_array($field['fieldname'],array('connectowner','replytoexecutive','sameexe','recordnotice','misscall','record','pincode','allgroup','mailalerttowoner')))?
									form_checkbox(array('class'=>"switch",'data-size'=>"mini",
																 'id'=>$field['fieldname']
																 ,'name'=>$field['fieldname']
															,'checked'=>(isset($itemDetail[$field['fieldname']]) && $itemDetail[$field['fieldname']]=='1')? TRUE: false
																,'value'=>'1'))	
												:((in_array($field['fieldname'],array("replymessage","replyattmsg")))?
												form_textarea(array(
											  'name'      => $field['fieldname']
											  ,'id'        => $field['fieldname']
											  ,'class'	  => 'form-control valid'
											  ,'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:$this->input->post($field['fieldname']),
											))."<div class='varlist'><a title='Click to insert Executive Name in Reply Message, count may be differ from actual message' class='addVar' rel='@name@'>Executive Name</a>"
											  ."<a title='Click to insert Executive Number in Reply Message, count may be differ from actual message' class='addVar' rel='@number@'>Executive Number</a>"
											 // ."<a title='Click to insert Executive Email in Reply Message, count may be differ from actual message' class='addVar' rel='@email@'>Executive Email</a>"
											  ."<a title='Click to insert Referance Id in Reply Message, count may be differ from actual message' class='addVar' rel='@refid@'>Reference ID</a>"
											  ."<a title='Click to insert Support Ticket Number' class='addVar' rel='@ticketid@'>Ticket Number</a></div>"
											:(($field['fieldname']=="access_reports")?
											form_multiselect('access_reports[]',$this->groupmodel->employee_list(),$multiSel,"id='access_reports'  class='form-control'")
											:(($field['fieldname']=="eid")?
											form_dropdown('eid',$this->groupmodel->employee_list(),
														 ($eids!="")?$eids->eid:$this->input->post('eid')
														,'id="eid" class="form-control" data-style="input-sm btn-default"') 
											:(($field['fieldname'] == "supportgrp")?
											form_dropdown('supportgrp',$this->supportmodel->getSupportGrps(),(isset($itemDetail['supportgrp'])) ? $itemDetail['supportgrp'] :$this->input->post('supportgrp'),'id="supportgrp" class="form-control" data-style="input-sm btn-default"').'<a href="'.base_url().'support/addSupportGrp" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"></a>'
											:(($field['fieldname'] == "rules")?
												form_dropdown('rules',$this->groupmodel->RuleList(),(isset($itemDetail['rule'])?$itemDetail['rule']:set_value($field['fieldname'])),"id='rules'  class='form-control required' data-style='input-sm btn-default'")
												:(($field['fieldname']=="landingregion")?
														form_dropdown('landingregion',$this->groupmodel->getLanReg(),(isset($itemDetail['landingregion'])?$itemDetail['landingregion']:set_value($field['fieldname'])),"id='landingregion' class='form-control required' data-style='input-sm btn-default'")
												:(($field['fieldname'] == "primary_rule")?
													form_dropdown('primary_rule',$this->groupmodel->PrimaryRuleList(),(isset($itemDetail['primary_rule'])?$itemDetail['primary_rule']:$this->input->post('primary_rule')),"id='primary_rule'  class='form-control required' data-style='input-sm btn-default'")
														
														:(($field['fieldname']=="addnumber")?
														form_dropdown('prinumber',$this->systemmodel->getPriList(isset($itemDetail['prinumber'])?$itemDetail['prinumber']:'','1'),(isset($itemDetail['prinumber']) && $itemDetail['prinumber']>20000)?$itemDetail['prinumber']:(($id!="")?'1':'0'),"id='prinumber' class='form-control required' data-style='input-sm btn-default'")
															:(($field['fieldname']=='bday')?
																'<table class="short"><tr>'
																.'<td>'.form_checkbox(array('name'=>'bday[Mon][day]','value'=>'1','id'=>'mon_day','class'=>"switch",'checked data-size'=>"mini",'checked'=>((isset($bday->Mon->day) || $id=='')?true:''))).'<label class="text-right" for="mon_day">Monday</label>'.'</td>'
																.'<td>'.form_input(array('name'=>'bday[Mon][st]','id'=>'mon_st','class'=>'timepicker form-control','placeholder'=>'Start Time','value'=>(isset($bday->Mon->st)?$bday->Mon->st:'00:00'))).'</td>'
																.'<td>'.form_input(array('name'=>'bday[Mon][et]','id'=>'mon_et','class'=>'timepicker form-control','placeholder'=>'End Time','value'=>(isset($bday->Mon->et)?$bday->Mon->et:'23:59'))).'</td>'
																.'</tr><tr>'
																.'<td>'.form_checkbox(array('name'=>'bday[Tue][day]','value'=>'1','id'=>'tue_day','class'=>"switch",'checked data-size'=>"mini",'checked'=>((isset($bday->Tue->day) || $id=='')?true:''))).'<label class="text-right" for="tue_day">Tuesday</label>'.'</td>'
																.'<td>'.form_input(array('name'=>'bday[Tue][st]','id'=>'tue_st','class'=>'timepicker form-control','placeholder'=>'Start Time','value'=>(isset($bday->Tue->st)?$bday->Tue->st:'00:00'))).'</td>'
																.'<td>'.form_input(array('name'=>'bday[Tue][et]','id'=>'tue_et','class'=>'timepicker form-control','placeholder'=>'End Time','value'=>(isset($bday->Tue->et)?$bday->Tue->et:'23:59'))).'</td>'
																.'</tr><tr>'
																.'<td>'.form_checkbox(array('name'=>'bday[Wed][day]','value'=>'1','id'=>'wed_day','class'=>"switch",'checked data-size'=>"mini",'checked'=>((isset($bday->Wed->day) || $id=='')?true:''))).'<label class="text-right" for="wed_day">Wednesday</label>'.'</td>'
																.'<td>'.form_input(array('name'=>'bday[Wed][st]','id'=>'wed_st','class'=>'timepicker form-control','placeholder'=>'Start Time','value'=>(isset($bday->Wed->st)?$bday->Wed->st:'00:00'))).'</td>'
																.'<td>'.form_input(array('name'=>'bday[Wed][et]','id'=>'wed_et','class'=>'timepicker form-control','placeholder'=>'End Time','value'=>(isset($bday->Wed->et)?$bday->Wed->et:'23:59'))).'</td>'
																.'</tr><tr>'
																.'<td>'.form_checkbox(array('name'=>'bday[Thu][day]','value'=>'1','id'=>'thu_day','class'=>"switch",'checked data-size'=>"mini",'checked'=>((isset($bday->Thu->day) || $id=='')?true:''))).'<label class="text-right" for="thu_day">Thursday</label>'.'</td>'
																.'<td>'.form_input(array('name'=>'bday[Thu][st]','id'=>'thu_st','class'=>'timepicker form-control','placeholder'=>'Start Time','value'=>(isset($bday->Thu->st)?$bday->Thu->st:'00:00'))).'</td>'
																.'<td>'.form_input(array('name'=>'bday[Thu][et]','id'=>'thu_et','class'=>'timepicker form-control','placeholder'=>'End Time','value'=>(isset($bday->Thu->et)?$bday->Thu->et:'23:59'))).'</td>'
																.'</tr><tr>'
																.'<td>'.form_checkbox(array('name'=>'bday[Fri][day]','value'=>'1','id'=>'fri_day','class'=>"switch",'checked data-size'=>"mini",'checked'=>((isset($bday->Fri->day) || $id=='')?true:''))).'<label class="text-right" for="fri_day">Friday</label>'.'</td>'
																.'<td>'.form_input(array('name'=>'bday[Fri][st]','id'=>'fri_st','class'=>'timepicker form-control','placeholder'=>'Start Time','value'=>(isset($bday->Fri->st)?$bday->Fri->st:'00:00'))).'</td>'
																.'<td>'.form_input(array('name'=>'bday[Fri][et]','id'=>'fri_et','class'=>'timepicker form-control','placeholder'=>'End Time','value'=>(isset($bday->Fri->et)?$bday->Fri->et:'23:59'))).'</td>'
																.'</tr><tr>'
																.'<td>'.form_checkbox(array('name'=>'bday[Sat][day]','value'=>'1','id'=>'sat_day','class'=>"switch",'checked data-size'=>"mini",'checked'=>((isset($bday->Sat->day) || $id=='')?true:''))).'<label class="text-right" for="sat_day">Saturday</label>'.'</td>'
																.'<td>'.form_input(array('name'=>'bday[Sat][st]','id'=>'sat_st','class'=>'timepicker form-control','placeholder'=>'Start Time','value'=>(isset($bday->Sat->st)?$bday->Sat->st:'00:00'))).'</td>'
																.'<td>'.form_input(array('name'=>'bday[Sat][et]','id'=>'sat_et','class'=>'timepicker form-control','placeholder'=>'End Time','value'=>(isset($bday->Sat->et)?$bday->Sat->et:'23:59'))).'</td>'
																.'</tr><tr>'
																.'<td>'.form_checkbox(array('name'=>'bday[Sun][day]','value'=>'1','id'=>'sun_day','class'=>"switch",'checked data-size'=>"mini",'checked'=>((isset($bday->Sun->day) || $id=='')?true:''))).'<label class="text-right" for="sun_day">Sunday</label>'.'</td>'
																.'<td>'.form_input(array('name'=>'bday[Sun][st]','id'=>'sun_st','class'=>'timepicker form-control','placeholder'=>'Start Time','value'=>(isset($bday->Sun->st)?$bday->Sun->st:'00:00'))).'</td>'
																.'<td>'.form_input(array('name'=>'bday[Sun][et]','id'=>'sun_et','class'=>'timepicker form-control','placeholder'=>'End Time','value'=>(isset($bday->Sun->et)?$bday->Sun->et:'23:59'))).'</td>'
																.'</tr></table>'
																:((in_array($field['fieldname'],array('hdayaudio')))?
														         (form_input(array(
																	    'name'      => $field['fieldname']
																	   ,'id'        => $field['fieldname']
																       ,'type' => 'file'
																       ,'style' => 'float: left;'
																       ,'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:$this->input->post($field['fieldname'])

																	 ))	)
											                 .((isset($itemDetail[$field['fieldname']]) && $itemDetail[$field['fieldname']]!='' && file_exists('sounds/'.$itemDetail[$field['fieldname']]))? 
											                            '<a target="_blank" href="'.site_url('sounds/'.$itemDetail[$field['fieldname']]).'">
											                            <span id="closeimg" title="Listen audio" class="fa fa-volume-up"></span></a>' 	
											                            .'&nbsp &nbsp <a href="'.base_url().'Employee/delaudio/'.$itemDetail['gid'].'">
											                              <span  id="delimg"  title="Delete Audio file" class="glyphicon glyphicon-trash"></span></a>':'')	 
																	    .'&nbsp &nbsp <a class="btn-danger" data-toggle="modal" data-target="#modal-responsive"  href="'.base_url().'Employee/addaudio"><img  id="addimg"  src="system/application/img/icons/audio-file-add.png" title="Select file of old groups" width="16" height="16" /></a>'
																		.'<input type="text" class="form-control"  id="fileupload1">'	
																		.'<img src="system/application/img/icons/no.png" id="closepng" onClick="deletefile()" title="Select file of old groups" width="16" height="16"  />'					 			 
																	 :(($field['fieldname']=='leadaction' ) ?
																	 (form_input(array(
																	  'class'	  => 'form-control'
																	  ,'name'      => $field['fieldname']
																	  ,'id'        => $field['fieldname']
																	  ,'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:$this->input->post($field['fieldname'])
																	  )))
																	:((in_array($field['fieldname'],array("groupname")))?
																	(form_input(array(
																	  'class'     => "form-control required"
																	  ,'type'     => "text"
																	  ,'parsley-minlength'=> "3"
																	  ,'name'      => $field['fieldname']
																	  ,'id'        => $field['fieldname']
																	  ,'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:$this->input->post($field['fieldname'])
																	)))
                                                                      : ( (in_array($field['fieldname'],array('greetings')))? 
																	(form_input(array(
																	  'name'      => $field['fieldname']
																	  ,'id'        => $field['fieldname']
																      ,'type' => 'file'
																      ,'style' => 'float: left;'
																      ,'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:$this->input->post($field['fieldname'])
																	 ))	) 
											                            .((isset($itemDetail[$field['fieldname']]) && $itemDetail[$field['fieldname']]!='' && file_exists('sounds/'.$itemDetail[$field['fieldname']]))? 
										                              	'<a target="_blank" href="'.site_url('sounds/'.$itemDetail[$field['fieldname']]).'"> <span id="closeimg1" title="Listen audio" class="fa fa-volume-up"></span></a>'
										                              	.'&nbsp &nbsp <a href="'.base_url().'Employee/delgreeting/'.$itemDetail['gid'].'"> <span  id="delimg1"  title="Delete Audio file" class="glyphicon glyphicon-trash"></span></a>':'')
																	    .'&nbsp &nbsp <a class="btn-danger" data-toggle="modal" data-target="#modal-responsive"  href="'.base_url().'Employee/add_greetings" ><img id="addimg1" src="system/application/img/icons/audio-file-add.png" title="Select file of old group" width="16" height="16" /></a>'
																	 	.'<input type="text" class="form-control" id="fileupload">'	
																	 	.'<img src="system/application/img/icons/no.png" onClick="deletefile1()"  id="closepng1" title="Select file of old groups" width="16" height="16"/>' 
                                                                      : ( (in_array($field['fieldname'],array('voicemessage')))? 
																	(form_input(array(
																	  'name'      => $field['fieldname']
																	  ,'id'        => $field['fieldname']
																      ,'type' => 'file'
																      ,'style' => 'float: left;'
																      ,'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:$this->input->post($field['fieldname'])
																	 ))	) 
											                            .((isset($itemDetail[$field['fieldname']]) && $itemDetail[$field['fieldname']]!='' && file_exists('sounds/'.$itemDetail[$field['fieldname']]))? 
										                              	'<a target="_blank" href="'.site_url('sounds/'.$itemDetail[$field['fieldname']]).'"> <span id="closevoice" title="Listen audio" class="fa fa-volume-up"></span></a>'
										                              	.'&nbsp &nbsp <a href="'.base_url().'Employee/delvoicemessage/'.$itemDetail['gid'].'">
										                              	 <span id="delvoice" title="Delete Audio file" class="glyphicon glyphicon-trash"></span></a>':'')
																	    .'&nbsp &nbsp <a  class="btn-danger" data-toggle="modal" data-target="#modal-responsive" href="'.base_url().'Employee/add_voicemessage" ><img id="addvoice" src="system/application/img/icons/audio-file-add.png" title="Select file of old group" width="16" height="16" /></a>'
																	 	.'<input type="text" class="form-control"  id="voiceupload">'	
																	 	.'<img src="system/application/img/icons/no.png" onClick="deletevoice()"  id="closevoice1" title="Select file of old groups" width="16" height="16"/>' 					 			 				 
                                                                     :((in_array($field['fieldname'],array('replytocust_missed','replytocust_attended','replytocust_repcal','replytocust_voice')))? 
																	form_checkbox(array('class'=>"switch",'data-size'=>"mini",
																                         'id'=>$field['fieldname']
																                         ,'name'=>$field['fieldname']
															                             ,'checked'=>isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'checked'
																                         ,'value'=>'1'))	
																	:((in_array($field['fieldname'],array('keyword','hdaytext','supportaction')))? 
																	(form_input(array(
																	  'class'     => "form-control"
																	  ,'name'      => $field['fieldname']
																	  ,'id'        => $field['fieldname']
																	  ,'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:$this->input->post($field['fieldname'])
																	  )))
																	  :((in_array($field['fieldname'],array("replytocust_voitext")))?
																			form_textarea(array(
																		  'name'      => $field['fieldname']
																		  ,'id'        => $field['fieldname']
																		  ,'class'	  => 'form-control valid'
																		  ,'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:$this->input->post($field['fieldname']),
																		))
																	  :(form_input(array(
								                			           'parsley-type' => (($field['fieldname']=="oneditaction" ||$field['fieldname']=="oncallaction" ||$field['fieldname']=="onhangup")? "url":(($field['fieldname']=="timeout")?"number":""))
																	  ,'class'     => ($field['fieldname']=="timeout")? "form-control required" : "form-control"
																	  ,'name'      => $field['fieldname']
																	  ,'id'        => $field['fieldname']
																	  ,'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:$this->input->post($field['fieldname'])
																	  )))))))
																))
												)))))))))))));
						array_push($formFields,$cf);
				}
			}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)$formFields[] = array(
							'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
							'field'=>$this->configmodel->createFieldAdvance($field,isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '',''));
			}
		}								
		$formFields1=array();
		if($id!=''){
			if($this->business_groupcnt()>1){
				$formFields[]= array('label'=>'<label class="col-sm-4 text-right">Apply to All</label>',
									  'field'=>form_checkbox(array('name'=>'atoall','id'=>'atoall','value'=>'1')));	
			}
		}
		$fromAction = ($clone=='')?'group/add_group/'.$id:'group/Clone_Group_Add/'.$id;
		$data['form'] = array(
		            'form_attr'=>array('action'=>$fromAction,'id'=>'addgroup','name'=>'form','enctype'=>"multipart/form-data"),
		            'hidden' => array('gid'=>$id,'clone'=>$clone,'gids'=>'','oldprinumber'=>isset($itemDetail['prinumber'])?$itemDetail['prinumber']:""),
					'fields'=>$formFields,
					'fields1'=>$formFields1,
					'parentids'=>($id=='')?$parentbids:'',
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>'<div id="groupsDiv"></div>'.form_close()
		);
		return $data;
	}
	function miscal_pris(){
		$string='';
		$options=$this->groupmodel->miscal_pris();
		$news=array_keys($options);
		for($i=0;$i<sizeof($news);$i++){
			$string.='<option value="'.$news[$i].'">'.$options[$news[$i]].'</option>';
		}
		echo $string;
	}

	function getPri($val =''){
		$option='';
		$option .='<option value=""> Select </option>';
		$result = $this->groupmodel->getPri($val);
		foreach($result as $res){
			if($re['status'] == 0){
				$option.='<option value="'.$res['number'].'">'.$res['landingnumber'].'</option>';
			}
		}
		echo $option;
	
	}
	function calltrack_pris(){
		$string='';
		$options=$this->systemmodel->getPriList();
		$news=array_keys($options);
		for($i=0;$i<sizeof($news);$i++){
			$string.='<option value="'.$news[$i].'">'.$options[$news[$i]].'</option>';
		}
		echo $string;
	}
	function uniquegroup($str){
		if($this->groupmodel->uniquegroup($str,$id=($this->uri->segment(3)!="")?$this->uri->segment(3):'')!=1){
			$this->form_validation->set_message('uniquegroup', 'The '.$str.' name is  already exists');
			return FALSE;
		}else{
			return true;
		}
	}
	function uniquegroupclone($str){
		if($this->groupmodel->uniquegroupclone($str,$id=($this->uri->segment(3)!="")?$this->uri->segment(3):'')!=1){
			$this->form_validation->set_message('uniquegroupclone', 'The '.$str.' name is  already exists');
			return FALSE;
		}else{
			return true;
		}
	}
	function activerecords($id=''){
		if(!$this->feature_access(1))redirect('Employee/access_denied');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_groupfrm');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('label_groupfrm');
		$fieldset = $this->configmodel->getFields('3',$bid);
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('3',$id,'',$bid);
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['3']['opt_add']) redirect('Employee/access_denied');
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
						$v = '';
						if($field['fieldname']=='eid'){
							$v = '<a href="Employee/activerecords/'.$itemDetail['epid'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.$itemDetail[$field['fieldname']].'</a>';
						}elseif($field['fieldname']=='primary_rule'){
							$v = isset($r['regionname'])?$r['regionname']:"";
						}elseif($field['fieldname']=='rules'){
							$v = ($itemDetail[$field['fieldname']]!='')?'<a title="'.$itemDetail[$field['fieldname']].'">'.$itemDetail[$field['fieldname']].'</a>':$itemDetail[$field['fieldname']];
						}elseif($field['fieldname']=='bday' && $itemDetail[$field['fieldname']]!=''){
							$bday = json_decode($itemDetail[$field['fieldname']]);
							$v = '';
							foreach($bday as $b => $d){ $v .= (isset($d->day) && $d->day=='1')?$b.'='.$d->st.'-'.$d->et.'<br>':'';}
						}elseif($field['fieldname']=='connectowner'){
							$v = isset($itemDetail[$field['fieldname']])?$itemDetail['connectwner']:"";
						}else{
							$v = isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:"";
						}
						$cf = array('label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).' : </label>',
									'field'=>$v
							);
						array_push($formFields,$cf);
						
				}
				}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)$formFields[] = array(
							'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
							'field'=>isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '','');
			}
		}
		$cf = array('label'=>'<label class="col-sm-4 text-right" for="groupkey">Group key For Click To Call : </label>',
						'field'=>isset($itemDetail['groupkey'])?$itemDetail['groupkey']:'');
		array_push($formFields,$cf);
		$data['form'] = array(
					'open'=>form_open('AddGroup/'.$id,array('name'=>'form','class'=>'form','id'=>'addgroup','method'=>'post')),
					'fields'=>$formFields,
					'close'=>form_close()
		);
		$this->load->view('active_view',$data);
		
	}
	function manage_group($bid=''){
		if(!$this->feature_access(1))redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['3']['opt_view']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$data['itemlist'] = $this->groupmodel->getgrouplist($bid,$ofset,$limit);
		//print_r($data['itemlist']);exit;
		$data['module']['title'] = $this->lang->line('label_groupmanage') . "[".$data['itemlist']['count']."]";
		$this->pagination->initialize(array(
						 'base_url'=>site_url('ManageGroup')."/".$this->uri->segment(2)."/"
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_groupmanage');
		$link = array();
		$links[] = '<li><a href="AddGroup"><span title="Add Group" class="glyphicon glyphicon-plus-sign">&nbsp;Add Group</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search"class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$fieldset = $this->configmodel->getFields('3',$bid);
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],
						array(   'filename'
								,'greetings'
								,'hdayaudio'
								,'hdaytext'
								,'bday'
								,'rules'
								,'url'
								,'record'
								,'primary_rule'
								,'connectowner'
								,'pincode'
								,'keyword'
								,'allgroup'
								,'supportgrp'
								//~ ,'voicemessage'
								,'access_reports'
								))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){ $formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>($field['fieldname']=='eid')
												?form_dropdown('empid',$this->groupmodel->employee_list(),''," class='form-control'")
												:form_input(array(
													'name'      => $field['fieldname'],
													'id'        => $field['fieldname'],
													'class'        => 'form-control',
													'value'     => $this->session->userdata($field['fieldname'])))
											);
										 }
			}elseif($field['type']=='c' && $field['show'] ){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)
							$formFields[] = array(
								'label'=>'<label class=" col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createFieldAdvance($field,'','search',"class='form-control'"));
					
			}
		}
		$data['links'] = $links;		
		$data['form'] = array(
			'open'=>form_open_multipart('ManageGroup/0',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'search_url'=>'ManageGroup/0/',
			'parentids'=>$parentbids,
			'busid'=>$bid,
			'pid'=>$this->session->userdata('pid'),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$data['tab'] = false;
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function deletedgroup($num=''){
		if(!$this->feature_access(1))redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['3']['opt_view']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$data['itemlist'] = $this->groupmodel->getdeletedgrouplist($bid,$ofset,$limit);
		$data['module']['title'] = $this->lang->line('label_groupdelete').'['.$data['itemlist']['count'].']';
		$this->pagination->initialize(array(
						 'base_url'=>site_url('DeleteGroup')."/".$this->uri->segment(2)."/"
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_groupdelete');
		$fieldset = $this->configmodel->getFields('3',$bid);
		$links = array();
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],array('keyword','filename','bday','url','rules','primary_rule','connectowner'))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$formFields[] = array(
								'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
										 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
								'field'=>($field['fieldname']=='eid')
											?form_dropdown('eid',$this->groupmodel->employee_list(),''," class='form-control'")
											:form_input(array(
												'name'      => $field['fieldname'],
												'id'        => $field['fieldname'],
												'class'     => 'form-control',
												'value'     => $this->session->userdata($field['fieldname'])))
										);
				}				
		}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$formFields[] = array(
						'label'=>'<label class=" col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
						'field'=>$this->configmodel->createFieldAdvance($field,'','search',"class='form-control'"));
				}
			}
		}
		$data['links']='';
		$data['links'] = $links;
 		$data['form'] = array(
			'open'=>form_open_multipart('DeleteGroup/0',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'close'=>form_close(),
			'busid'=>$bid,
			'title'=>$this->lang->line('level_search')
			);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function addemptogroup($gid='',$eid=''){
		if(!$this->feature_access(1))redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		if($this->input->post('update_system')){
			if($_POST['empid']!=''){
				$res=$this->groupmodel->editemp_group($gid);
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', 'Updated Successfully');
				redirect('ListempGroup/'.$gid);
			}else{
				$array=$this->groupmodel->get_group($gid);
				$get_empgroupC=$this->groupmodel->groupemplist($gid);
				$res=$this->groupmodel->addemp_group($gid);		
				if($res==0){
					$this->session->set_flashdata('msgt', 'error');
					$this->session->set_flashdata('msg', $this->lang->line('error_alreadyexists'));
					redirect('AddempGroup/'.$gid);
				}else{
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', $this->lang->line('error_addempsucss'));
					redirect('ManageGroup/0');
				}
			}
		}
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['3']['opt_add']) redirect('Employee/access_denied');
		$gpDetail = $this->configmodel->getDetail('3',$gid,'',$bid);
		if(count($_POST)>0){
			redirect($_SERVER['HTTP_REFERER']);
		}
		$gpEmp = ($eid!='')?$this->groupmodel->getGroupEmpDetail($gid,$eid):array();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_addemptogroup');
		$data['module']['title'] = $this->lang->line('label_addemptogroup');
		$formFields = array();
		$data['form'] =  array( 'form_attr'=>array('action'=>'/group/addemptogroup/'.$gid.'/'.$eid,'name'=>'addemp','id'=>'addemp',),
										'fields'=>$formFields,
										'hidden' => array('bid'=>$bid,'gid'=>$gid,'empid'=>$eid),
										'close'=>form_close(),
										'emplist'=>$this->systemmodel->get_emp_list(),
										'empexists'=>$this->groupmodel->group_enteremplist($gid),
										'gdetail'=>$gpDetail,
										'gpEmp'=>$gpEmp,
						);
		$this->sysconfmodel->viewLayout('form_view_emp',$data);
	}
	function check_emp_group(){
		$eids=$this->input->post('eids');
		$res=$this->groupmodel->addemp_group();
		if($res!=""){
			echo "The $res Employees are Already Added to Group";
		}else{
			echo "1";
		}
	}
	function check_emp_group1(){
		$eids=$this->input->post('eids');
		$res=$this->groupmodel->addemp_group1();
		if($res!=""){
			echo "The $res Employees are Already Added to Group";
		}else{
			echo "1";
		}
	}
	function group_emp_list($gid){
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('label_groupemp');
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$gpDetail = $this->configmodel->getDetail('3',$gid,'',$bid);
		$limit = '30';
		$header = array($this->lang->line('level_empname')
						,$this->lang->line('level_group')
						);
		if($gpDetail['primary_rule']>0) $header[]=$this->lang->line('label_region');				
		if($gpDetail['rule']=='2') $header[]=$this->lang->line('level_empweight');
		if($gpDetail['rule']=='3') $header[]=$this->lang->line('level_empPriority');
		array_push($header,$this->lang->line('level_starttime')
						,$this->lang->line('level_endtime')
						,$this->lang->line('label_isfailover')
						,"Call Counter"
						,$this->lang->line('level_Action'));
		$data['itemlist']['header'] = $header;
		$emp_list=$this->groupmodel->groupemplist($gid,$ofset,$limit);
		$roleDetail = $this->roleDetail;
		$opt_add=$roleDetail['modules']['3']['opt_add']; 
		$opt_delete=$roleDetail['modules']['3']['opt_delete']; 
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$opt='';
			$s=($item['status']=="1")?'<a href="'.site_url('group/dis_grp_emp/'.$item['eid']."/".$item['gid']).'">
			<span class="fa fa-unlock" title="Disable"></span></a>':'<a href="'.site_url('group/dis_grp_emp/'.$item['eid']."/".$item['gid']).'"><span class="fa fa-lock" title="Enable"></span></a>';
			$opt.=($opt_add)?'<a href="'.site_url('AddempGroup/'.$item['gid']."/".$item['eid']).'"><span title="Edit" class="fa fa-edit"></span></a>':'';
			$opt.=($opt_delete)?'<a href="'.site_url('group/delete_grp_emp/'.$item['eid']."/".$item['gid']).'">
							<span title="Delete Employee from Group" class="glyphicon glyphicon-trash"></span></a>':'';
			$opt.=($opt_add)?$s:'';	 
			 		$r = array(
				'<a href="Employee/activerecords/'.$item['eid'].'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive">'.(($item['status']=="1" && $item['estatus']=='1') ? $item['empname'] : '<font color=red>'.$item['empname'].'</font>').'</a>'
				,(($item['status']=="1" && $item['estatus']=='1') ? $item['groupname'] : '<font color=red>'.$item['groupname'].'</font>'));
				if($gpDetail['primary_rule']>0) $r[]=(($item['status']=="1" && $item['estatus']=='1') ? $item['region'] : '<font color=red>'.$item['region'].'</font>');	
				if($gpDetail['rule']=='2') $r[]=($item['status']=="1" && $item['estatus']=='1') ? $item['empweight'] : '<font color=red>'.$item['empweight'].'</font>';
				if($gpDetail['rule']=='3') $r[]=($item['status']=="1" && $item['estatus']=='1') ? $item['empPriority'] : '<font color=red>'.$item['empPriority'].'</font>';
				$r = array_merge($r,array(
							(($item['status']=="1" && $item['estatus']=='1') ? $item['starttime'] : '<font color=red>'.$item['starttime'].'</font>'),
							(($item['status']=="1" && $item['estatus']=='1') ? $item['endtime'] : '<font color=red>'.$item['endtime'].'</font>'),
							(($item['status']=="1" && $item['estatus']=='1') ? $item['failover'] : '<font color=red>'.$item['failover'].'</font>'),
							(($item['status']=="1" && $item['estatus']=='1') ? $item['callcounter'] : '<font color=red>'.$item['callcounter'].'</font>'),
							$opt));
					$rec[] = $r;
			 }
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('ListempGroup/'.$this->uri->segment(2))
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_groupemp'); 
		$links = array();
		$links[] ='<li><a href="AddempGroup/'.$gid.'"><span title="Add Number" class="glyphicon glyphicon-plus-sign">&nbsp;Add Employee</span></a></li>';
		$links[] = '<li><a href="group/refreshcounter/'.$gid.'"> <span title="Reset Counter" class="fa fa-refresh">&nbsp;Reset Counter</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$formFields = array();
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">Employee Name : </label>',
				  'field'=>form_input(array(
									   'name'         => 'empname',
									   'class'        => 'form-control',
										'id'          => 'empname',
										'value'       => $this->session->userdata('empname'))));
		array_push($formFields,$cf);
		$data['links'] = $links;				
		$data['form'] = array(
			'open'=>form_open_multipart('ListempGroup/'.$gid,array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'adv_search'=>array(),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function delete_grp_emp($eid,$gid){
		$res=$this->groupmodel->delete_grp($eid,$gid);
		redirect('ListempGroup/'.$gid);
	}	
	function Delete_group($id){
		if(!$this->feature_access(1))redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['3']['opt_delete']) redirect('Employee/access_denied');
		$res=$this->groupmodel->delete_group($id);
		echo '1';
	}
	function UNDelete_group($id){
		if(!$this->feature_access(1))redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['3']['opt_delete']) redirect('Employee/access_denied');
		$res=$this->groupmodel->undelete_group($id);
		if($res=="err"){
			echo $this->lang->line('undel_errmsg');
		}else{
			redirect('group/UNDelete_group/'.$id);
		}
	}
	function add_addgroupowner(){
		$res=$this->empmodel->add_emp();
		$group_list=$this->groupmodel->emplist();
		print_r($group_list);
	}
	function confirmundelete($id){
		$res=$this->groupmodel->confirmundelete($id);
		if($res){ 
			redirect('ManageGroup/0'); 
		}else { 
			$this->session->set_flashdata('msgt', 'error');
			$this->session->set_flashdata('msg', $this->lang->line('error_priadjust'));
			redirect('group/deletedgroup'); 
		}
	}
	function autodialler(){
		if(!$this->feature_access(7))redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['3']['opt_view']) redirect('Employee/access_denied');
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('label_autodiallerlist');
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$data['itemlist'] = $this->groupmodel->AutoDailerList($bid,$ofset,$limit);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('group/autodialler')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_autodiallerlist');
		$links = array();
		$links[]='<li><a href="group/autodialleradd"><span title="Add Autodailler" class="glyphicon glyphicon-plus-sign">Add Autodailler</span></a></li>';
		$data['nosearch']=true;
		$data['links'] = $links;
		$this->sysconfmodel->viewLayout('list_view',$data);
	}	
	function getAutoCalls($refer_id){
		if(!$this->feature_access(7))redirect('Employee/access_denied');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['3']['opt_view']) redirect('Employee/access_denied');
		$bid = $this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('label_autodiallerlist');
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '30';
		$data['itemlist'] = $this->groupmodel->getAutodiallerList($refer_id,$ofset,$limit);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('group/getAutoCalls')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_autodiallerlist');
		$data['links']='';
		$data['nosearch']=true;
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function autodialleradd(){
		if(!$this->feature_access(7))redirect('Employee/access_denied');
		if($this->session->userdata('eid')!='1')redirect('group/autodialler');
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('filename', 'Autodialler File', 'required');
			$this->form_validation->set_rules('gid', 'Group', 'required');
			if(!$this->form_validation->run() == FALSE){
				$this->groupmodel->addautodialler();
				redirect('group/autodialleradd');
			}	
		}
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['3']['opt_add']) redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			$this->groupmodel->addautodialler();
			redirect($_SERVER['HTTP_REFERER']);
		}	
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_autodialleradd');
		$data['module']['title'] = $this->lang->line('label_autodialleradd');
		$formFields[] = array('label'=>'<label class="col-sm-4 text-right" for="title">'.$this->lang->line('label_title').'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="Title">&nbsp;&nbsp; : </label>',
							  'field'=>form_input(array(
											'name'      => 'title',
											'id'        => 'title',
											'class'     => 'required'
											))
								);
		$formFields[] = array('label'=>'<label class="col-sm-4 text-right" for="gid">'.$this->lang->line('label_autodiallergroup').'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="Name of the group or campaign this autodialer will be tracked against.">&nbsp;&nbsp; : </label>',
							  'field'=>form_dropdown('gid',$this->systemmodel->get_groups(),'','id="gid" class="required"')
								);
		$formFields[] = array('label'=>'<label class="col-sm-4 text-right" for="filename">'.$this->lang->line('label_autodiallerfile').'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="Upload csv or txt file with the list of numbers to be called and tracked. The format of the file should be Number, Name, Email, Business Name, Remarks">&nbsp;&nbsp;: </label>',
							  'field'=>form_input(array(
											'name'      => 'filename',
											'id'        => 'filename',
											'class'     => 'required',
											'type'	  => 'file')
									)
								);
		$data['form'] = array(
		            'form_attr'=>array('action'=>'group/autodialleradd','name'=>'autoadd'),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function callconnect($did){
		if(!$this->feature_access(7))redirect('Employee/access_denied');
		$this->groupmodel->callconnect($did);
	}
	function autoedit($did){
		if(!$this->feature_access(7))redirect('Employee/access_denied');
		$callid = $this->db->query("SELECT * FROM ".$this->session->userdata('bid')."_autodialler WHERE did='".$did."'")->row()->callid;
		if($callid!=''){
			$this->db->query("UPDATE ".$this->session->userdata('bid')."_autodialler SET
								status='3' WHERE did='".$did."'");
			redirect("Report/edit/".$callid);
		}else{
			echo "<h2>Call is not Complete</h2>";
		}
	}
	function player($sound=''){
		echo '<br><br><br><audio id="audio1" src="sounds/'.$sound.'" controls preload="auto" autobuffer></audio>';
	}
	function blocknumbers(){
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('blacklist', 'blocknumbers', 'required|min_length[10]|max_length[12]|numeric|callback_blocknumbers_check');
			if(!$this->form_validation->run() == FALSE){	
				$res=$this->groupmodel->add_blacklistnumber();
				if($res!=""){
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', $this->lang->line('error_blacksuccmsg'));
					redirect('Blocknumbers');
				}
			}	
		}
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('blocknumbers');
		$data['module']['title'] = $this->lang->line('blocknumbers');
		$formFields = array();$formFields1 = array();
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="gid">'.$this->lang->line('level_blocknumbers').'</label>',
				  'field'=>form_input(array(
									    'name'        		=> 'blacklist',
										'id'          		=> 'blacklist',
										'value'       		=> '',
										'class'		  		=> 'form-control required',
										'parsley-minlength' => '3'
				  				)));
		array_push($formFields,$cf);
		$cf=array('label'=>'<label class="col-sm-4 text-right">'.$this->lang->line('level_blockreason').'</label>',
				  'field'=>form_textarea(array(
										'name'          => 'reason',
										'id'          	=> 'reason',
										'value'       	=> '',
										'class'		  	=> 'form-control'
				  				)));
		array_push($formFields,$cf);
		$data['form'] = array(
				'form_attr'=>array('action'=>'Blocknumbers','name'=>'blocknumbers' ,'id'=>'blocknumbers','enctype'=>"multipart/form-data"),
				'fields'=>$formFields,
				'close'=>form_close()
			);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function blocknumbers_check($str){
		if($this->groupmodel->blacknumberexists($str)=="exists"){
		$this->form_validation->set_message('blocknumbers_check', 'The '.$str.' is already in the list');
			return FALSE;
		}else{
			return TRUE;
		}
	}
	function Delete_blocknumber($id){
		$res=$this->groupmodel->Delete_blocknumber($id);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', $this->lang->line('error_delblacksuccmsg'));
		redirect('BlockList');
	}
	function Clone_Group(){
		$u=explode("/",$this->input->post('urls'));
		$pri=$this->groupmodel->get_PriNumber_Associate($u[5]);
		if($this->input->post('prinumber')==$pri){
			echo "Same";
		}else{
			$res=$this->groupmodel->Insert_Same($u[5]);
			echo "Created";
		}
	}
	function Clone_Group_Add($id){
		$this->form_validation->set_rules('groupname', 'Group Name', 'required|min_length[4]|max_length[32]|alpha_numeric|callback_uniquegroupclone');
		$this->form_validation->set_rules('rules', 'Rule', 'required');
		$this->form_validation->set_rules('prinumber', 'Landing Number', 'required');
		if(!$this->form_validation->run() == FALSE){	
			$res=$this->groupmodel->Insert_Same($id);
			if($_POST['oldprinumber']==$_POST['prinumber']){
				$this->groupmodel->clone_delete($id);
			}
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', $this->lang->line('group_clone_success'));
			redirect('ManageGroup/0');
		}	
		$this->session->set_flashdata('msgt', 'error');
		$this->session->set_flashdata('msg',validation_errors());
		redirect('group/add_group/'.$id.'/clone');
	}
	function blocknumber_status($id){
		$res=$this->groupmodel->blocknumber_status($id);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', $this->lang->line('status_update'));
		redirect('BlockList');
	}
	function blocklist(){
		$data['module']['title'] =$this->lang->line('blocklist');
		$header = array('#',
						'Number',
						'Reason',
						'Request By',
						'Date &time',
						'Action'
						);
		$data['itemlist']['header'] = $header;
		$ofset = ($this->uri->segment(2)!=null)?$this->uri->segment(2):0;
		$limit = '30';
		$credit_info=$this->groupmodel->blocknumber_list($ofset,$limit);
		$rec = array();
		if(count($credit_info['data'])>0)
		$i=1;
		foreach ($credit_info['data'] as $item){
				($item['status']=="0")
				?$s='<a href="'.base_url().'group/blocknumber_status/'.$item['id'].'">
				<span class="fa fa-lock" id="'.$item['id'].'" title="Enable"></span>
				</a>'
				:$s='<a href="'.base_url().'group/blocknumber_status/'.$item['id'].'"> <span class="fa fa-unlock" id="'.$item['id'].'" title="Disable"></span></a>';
			$rec[] = array(
					$item['id'],
					$item['number'],
					$item['reason'],
					$item['requestby'],
					$item['datetime'],
					'<a href="'.base_url().'group/Delete_blocknumber/'.$item['id'].'" class="deleteClass" title="Are you sure to Delete the group"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>'.$s
			);
			$i++;
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $credit_info['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('BlockList/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>'20'	
						,'uri_segment'=>2					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " |".$this->lang->line('blocklist');
		$links = array();
		$links[]='<li><a href="Blocknumbers"><span title="Add Black list number" class="glyphicon glyphicon-plus-sign">&nbsp;Add Number</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
	    $formFields1 = array();
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">'.$this->lang->line('blocknumber').' : </label>',
				  'field'=>form_input(array(
									  'name'        => 'blnumber',
									  'class'       => 'form-control',
									  'id'          => 'blnumber',
									  'value'       => $this->session->userdata('blnumber'))));
		array_push($formFields1,$cf);
		$formFields = array();
		$formFields[] = array();
		$data['links'] = $links; 
		$data['form'] = array(
							'open'=>form_open_multipart('BlockList/',array('name'=>'blocklist','class'=>'form','id'=>'blocklist','method'=>'post')),
							'form_field'=>$formFields1 ,
							'adv_search'=>array(),
							'save_search'=>3,
							'close'=>form_close(),
							'title'=>$this->lang->line('level_search')
							);
		if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function autodetail($did){
		$detail = $this->groupmodel->autodetail($did);
		$out = '';
		if(count($detail)){
			$others = unserialize($detail['others']);
			$out .= "<table style='width:90%'>";
			foreach($others as $key => $val){
				$out .= "<tr align='left'><th>".$key."</th><td>".$val."</td></tr>";
			}
			$out .= "</table>";
			$out .= "<table style='width:90%;margin-top:50px;'><tr>";
			$out .= ($detail['status']=='0')?"<td style='width:50%'><a class='callconnect' href='group/callconnect/".$did."'>Connect</a></td>":"<td style='width:50%'><a class='callconnect' href='group/callconnect/".$did."'>Re-connect</a></td>";
			$out .= "</tr></table>";
		}else{
			$out .= "No Detail found";
		}
		$out .= '';
		echo $out;
	}
	function confirmBynumber($id=''){
		if($this->input->post('update_system')){
			if($id!=""){
				$rest=$this->groupmodel->addConfirmNumber($id);
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg',"Updated Successfully");
				redirect('group/configList');
			}else{
				$rest=$this->groupmodel->addConfirmNumber();
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg',"Insert Successfully");
				redirect('group/configList');
			}
		}
		$rows=$this->groupmodel->get_ConfirmNumber($id);
		$this->sysconfmodel->data['html']['title'] .= " | Confirm By Number";
		$data['module']['title'] = "Confirm By Number";
		$formFields = array();
		$cf=array('label'=>'<label class="col-sm-4 text-right">Landing Number :</label>','field'=>form_dropdown('pri',isset($rows->pri)?$this->pollmodel->landing_number($id,'6'):$this->systemmodel->getPriList(),isset($rows->pri)?$rows->pri:'','id="pri"'));
		array_push($formFields,$cf);
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">Accept url : </label>',
				  'field'=>form_input(array(
										'name' 	  => 'aurl',
										'id'    => 'aurl',
										'class'	=>'required url'
										,'value'=>isset($rows->aurl)?$rows->aurl:'')));
		array_push($formFields,$cf);
		$options=array(""=>"select","number"=>"number");
			$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">Argument1 : </label>',
				  'field'=>form_dropdown('akey1',$options,(!empty($rows)?'number':''),'id="akey1"')." ".form_input(array(
										'name' 	  => 'aopt1',
										'id'    => 'aopt1',
										'class'	=>'required'
										,'value'=>isset($rows->akey1)?$rows->akey1:'')));
		array_push($formFields,$cf);
		$options1=array(""=>"select","status"=>"status");
			$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">Argument2 : </label>',
				  'field'=>form_dropdown('akey2',$options1,(!empty($rows)?'status':''),'id="akey2"')." ".form_input(array(
										'name' 	  => 'aopt2',
										'id'    => 'aopt2',
										'class'	=>'required'
										,'value'=>isset($rows->akey2)?$rows->akey2:'')));
		array_push($formFields,$cf);
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">Denie url : </label>',
				  'field'=>form_input(array(
										'name' 	  => 'durl',
										'id'    => 'durl',
										'class'	=>'required url'
										,'value'=>isset($rows->durl)?$rows->durl:'')));
		array_push($formFields,$cf);
		$option1s=array(""=>"select","number"=>"number");
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">Argument1 : </label>',
				  'field'=>form_dropdown('dkey1',$option1s,(!empty($rows)?'number':''),'id="dkey1"')." ".form_input(array(
										'name' 	  => 'dopt1',
										'id'    => 'dopt1',
										'class'	=>'required'
										,'value'=>isset($rows->dkey1)?$rows->dkey1:'')));
		array_push($formFields,$cf);
		$option1=array(""=>"select","status"=>"status");
			$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">Argument 2 : </label>',
				  'field'=>form_dropdown('dkey2',$option1,(!empty($rows)?'status':''),'id="dkey2"')." ".form_input(array(
										'name' 	  => 'dopt2',
										'id'    => 'dopt2',
										'class'	=>'required'
										,'value'=>isset($rows->dkey2)?$rows->dkey2:'')));
		array_push($formFields,$cf);
		$data['form'] = array(
		            'form_attr'=>array('action'=>'group/confirmBynumber/'.$id,'name'=>'cfrm_number'),
					'fields'=>$formFields,
					'close'=>form_close(),
		);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function configList(){
		$data['module']['title'] ="Numbers List";
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$header = array("#"
						,"Landing Number"
						,"Accept Api Url"
						,"Denied Api Url"
						,"Action"
						);
		$data['itemlist']['header'] = $header;
		$emp_list=$this->groupmodel->configList($ofset,$limit);
		$rec = array();
		if(count($emp_list['data'])>0)
		foreach ($emp_list['data'] as $item){
			$status=($item['stat']=="0")?'<span class="fa fa-lock" title="Enable"></span></a>':'<span class="fa fa-unlock" title="Disable"></span>';
			$rec[] = array(
				$item['cid']
				,$item['landingnumber']
				,$item['aurl']
				,$item['durl']
				,'<a href="group/confirmBynumber/'.$item['cid'].'"><span title="Edit" class="fa fa-edit"></span></a>&nbsp;<a href="group/change_confirmNumber/'.$item['cid'].'">'.$status.'</a>&nbsp;'
			);
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('poll/listpoll/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>3					
				));
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | Poll";
		$data['links']=$data['links']='';
		$this->sysconfmodel->data['links'] = '<a href="poll/addpoll"><span title="Add Number" class="glyphicon glyphicon-plus-sign">&nbsp;Add Number</span></a>';
		$formFields = array();
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">Poll Name : </label>',
				  'field'=>form_input(array(
									  'name'        => 'pollname',
										'id'          => 'pollname',
										'value'       => $this->session->userdata('pollname'))));
		array_push($formFields,$cf);
		$options=array(""=>"select","1"=>"Single Number Poll","2"=>"Multiple Number poll");
		$cf=array('label'=>'<label class="col-sm-4 text-right" for="groupname">Poll type : </label>',
				  'field'=>form_dropdown('polltype',$options,'','id="polltype"'));
		array_push($formFields,$cf);
		$data['form'] = array(
			'open'=>form_open_multipart('poll/listpoll/',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function change_confirmNumber($id){
		$sql=$this->groupmodel->status_updateConfirm($id);
		if($sql!=1){
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg',"status updated Successfully");
			redirect('group/configList');
		}else{
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg',"status updated Successfully");
			redirect('group/configList');
			
		}
	}
	function Moduleaddons_number($lno,$module='',$seg=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$addons=$this->sysconfmodel->get_baddons_number($lno);
		$addon=array();
		foreach($addons as $ads){
			$addon[]=$ads['feature_id'];
		}
		$formFields=array();
		$fieldset = $this->sysconfmodel->getFields_addons($module);
		$output='';
		if($seg!=""){
		$itemDetail = $this->configmodel->getDetail($module,$seg,'',$bid);
		}else{
			$itemDetail =array();
		}
		$arr=array();
		if($lno!=0){
				foreach($fieldset as $field){
					if(!in_array($field['addon'],$addon)){
							$arr[]=$field['fieldname'];	
					}
				}
		}				
		echo implode(",",$arr);
		/*
		 if($lno!=0){
						foreach($fieldset as $field){
							if(in_array($field['addon'],$addon)){
									$output.='<tr class="appended_rows">
												<th>
													';
									$output.='<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
																?$field['customlabel']
																:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
														).'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>';				
									$output.='</th>
												<td>';
									$output.=(in_array($field['fieldname'],array('replytoexecutive','replytocustomer','sameexe','record','misscall','recordnotice')))?form_checkbox(array(
																				 'id'=>$field['fieldname']
																				 ,'name'=>$field['fieldname']
																				,'value'=>'1',
																				'checked'=>(isset($itemDetail[$field['fieldname']]) && $itemDetail[$field['fieldname']]=='1')? TRUE: false
																				)):((in_array($field['fieldname'],array("replymessage","replyattmsg")))?
																form_textarea(array(
															  'name'      => $field['fieldname'],
															  'id'        => $field['fieldname'],
															  'class'	  => 'word_count',
															  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname']),
															))."<div class='varlist'><a title='Click to insert Executive Name in Reply Message, count may be differ from actual message' class='addVar' rel='@name@'>Executive Name</a>"
															  ."<a title='Click to insert Executive Number in Reply Message, count may be differ from actual message' class='addVar' rel='@number@'>Executive Number</a>"
															  ."<a title='Click to insert Executive Email in Reply Message, count may be differ from actual message' class='addVar' rel='@email@'>Executive Email</a>"
															  ."<a title='Click to insert Referance Id in Reply Message, count may be differ from actual message' class='addVar' rel='@refid@'>Referance ID</a></div>"
															  
															  :(in_array($field['fieldname'],array('hdayaudio','greetings'))?
																					form_input(array(
																					  'name'      => $field['fieldname'],
																					  'id'        => $field['fieldname'],
																					  'type'      => 'file',
																					  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname']))).((isset($itemDetail[$field['fieldname']]) && $itemDetail[$field['fieldname']]!='' && file_exists('sounds/'.$itemDetail[$field['fieldname']]))? 
											'<a target="_blank" href="'.site_url('sounds/'.$itemDetail[$field['fieldname']]).'"><img src="system/application/img/icons/sound_high.png" title="Sound" /></a>':'')
																					:form_input(array(
																					  'name'      => $field['fieldname'],
																					  'id'        => $field['fieldname'],
																					  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname']))
																				  )));				
											$output.='</td>
												<td></td>';
															  
								}
						}
				}else{
					foreach($fieldset as $field){
							$output.='<tr class="appended_rows">
										<th>
											';
							$output.='<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
														?$field['customlabel']
														:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
												).'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>';				
							$output.='</th>
										<td>';
							$output.=(in_array($field['fieldname'],array('replytoexecutive','replytocustomer','sameexe','record','misscall','recordnotice')))?form_checkbox(array(
																		 'id'=>$field['fieldname']
																		 ,'name'=>$field['fieldname']
																		,'value'=>'1',
																		'checked'=>(isset($itemDetail[$field['fieldname']]) && $itemDetail[$field['fieldname']]=='1')? TRUE: false)):((in_array($field['fieldname'],array("replymessage","replyattmsg")))?
														form_textarea(array(
													  'name'      => $field['fieldname'],
													  'id'        => $field['fieldname'],
													  'class'	  => 'word_count',
													  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname']),
													))."<div class='varlist'><a title='Click to insert Executive Name in Reply Message, count may be differ from actual message' class='addVar' rel='@name@'>Executive Name</a>"
													  ."<a title='Click to insert Executive Number in Reply Message, count may be differ from actual message' class='addVar' rel='@number@'>Executive Number</a>"
													  ."<a title='Click to insert Executive Email in Reply Message, count may be differ from actual message' class='addVar' rel='@email@'>Executive Email</a>"
													  ."<a title='Click to insert Referance Id in Reply Message, count may be differ from actual message' class='addVar' rel='@refid@'>Referance ID</a></div>"
													  
											  :(in_array($field['fieldname'],array('hdayaudio','greetings'))?
																			form_input(array(
																			  'name'      => $field['fieldname'],
																			  'id'        => $field['fieldname'],
																			  'type'      => 'file',
																			  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname']))).((isset($itemDetail[$field['fieldname']]) && $itemDetail[$field['fieldname']]!='' && file_exists('sounds/'.$itemDetail[$field['fieldname']]))? 
											'<a target="_blank" href="'.site_url('sounds/'.$itemDetail[$field['fieldname']]).'"><img src="system/application/img/icons/sound_high.png" title="Sound" /></a>':'')
																			:form_input(array(
																			  'name'      => $field['fieldname'],
																			  'id'        => $field['fieldname'],
																			  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname']))
																		  )));				
									$output.='</td>
										<td></td>';
															  
								}
				}
		 * */
	}
	function systemOperationset(){
		 $this->session->set_userdata('cbid',$this->input->post('bid'));
		 echo $this->session->userdata('cbid');
	}
	function systemOperationunset(){
		 $this->session->unset_userdata('cbid');
	}
	function addhday(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		if(!$this->feature_access(11))redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			if($_FILES['filename']['size']>0){
				$this->form_validation->set_rules('filename', 'Filename', 'callback_file_extensions');
				if (!$this->form_validation->run() == FALSE){
					$res=$this->groupmodel->addHoliday();
					$this->session->set_flashdata('msgt', 'success');
					 $this->session->set_flashdata('msg', "Holidays added Successfully");
					redirect('ManageHoliday');
				}
			}else{
				$this->form_validation->set_rules('holiday', 'Holiday', 'required');
				$this->form_validation->set_rules('date', 'Date', 'required');
				if (!$this->form_validation->run() == FALSE){
					$res=$this->groupmodel->EditHoliday();
					$this->session->set_flashdata('msgt', 'success');
					$this->session->set_flashdata('msg', "Holiday added Successfully");
					redirect('ManageHoliday');
				}
			}
		}
		$roleDetail = $this->roleDetail;
		$this->sysconfmodel->data['html']['title'] .= " | Add Holidays";
		$data['module']['title'] = "Add Holidays";
		$formFields[] = array('label'=>'<label class="col-sm-4 text-right" for="filename">'.$this->lang->line('label_autodiallerfile').'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="Upload csv or txt file with the list of holidays The format of the file should be Holiday Name,Date">&nbsp;&nbsp;: </label>',
							  'field'=>form_input(array(
										'name'      => 'filename',
										'id'        => 'filename',
										'type'	  => 'file')
										)
								);
		$formFields[] = array('label'=>'',
							  'field'=>'[or]'
								);
		$fieldset = $this->configmodel->getFields('28',$bid);
				foreach($fieldset as $field){
					$checked = false;
					if($field['type']=='s' && $field['show'] && $field['fieldname']!='filename'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked && !in_array($field['fieldname'] ,array('greetings','bday','hdaytext','hdayaudio','operator','prinumber','record','remark','noext'))) 
					$formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>form_input(array(
											'name'      => $field['fieldname'],
											'class'     => 'form-control',
											'id'        => $field['fieldname'],
											'class'		=>($field['fieldname']=='date')?' datepicker_leads form-control':'form-control'))
											);
								}elseif($field['type']=='c' && $field['show']){
					foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked)$formFields[] = array(
							'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
							'field'=>$this->configmodel->createFieldAdvance($field,isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '',''));
			}
			}				
		$data['form'] = array(
		    'form_attr'=>array('action'=>'AddHoliday','name'=>'autoadd','id'=>'autoadd','enctype'=>"multipart/form-data"),
			'hidden'=>array('bid'=>$bid),
			'fields'=>$formFields,
			'busid'=>$bid,
			'pid'=>$this->session->userdata('pid'),
			'parentids'=>$parentbids,
			'close'=>form_close()
		);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function file_extensions($str){
		$allow_types=array("text/csv","csv","application/vnd.ms-excel");
		if($_FILES['filename']['size']>0){
			if(!in_array($_FILES['filename']['type'],$allow_types)){
				$this->form_validation->set_message('file_extensions', 'File Extension Not Allowed');
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return true;
		}
	}
	function hlist(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$roleDetail = $this->roleDetail;
		$ofset = ($this->uri->segment(2)!=null)?$this->uri->segment(2):0;
		$limit = '30';
		$fieldset = $this->configmodel->getFields('28',$bid);
		$keys = array();
		$header = array('#');
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && $field['listing']){
				foreach($roleDetail['system'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					array_push($keys,$field['fieldname']);
					array_push($header,(($field['customlabel']!="")
										?$field['customlabel']
										:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']));
				}
		}elseif($field['type']=='c' && $field['show'] && $field['listing']){
				foreach($roleDetail['custom'] as $f){if($f['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
						array_push($keys,$field['fieldKey']);
					array_push($header,$field['customlabel']);
				}
			}
		}	
		$opt_add 	= $roleDetail['modules']['28']['opt_add'];
		$opt_view 	= $roleDetail['modules']['28']['opt_view'];
		$opt_delete = $roleDetail['modules']['28']['opt_delete'];
		if($opt_add || $opt_view || $opt_delete){
			array_push($header,$this->lang->line('level_Action'));
			array_push($keys,"Action");			
		}
		$data['itemlist']['header'] = $header;
		$emp_list = $this->groupmodel->listholidays($ofset,$limit);
		$links = array();
		$links[]='<li><a href="AddHoliday"><span title="Add Holiday" class="glyphicon glyphicon-plus-sign">&nbsp;Add Holiday</span></a></li>';
		$links[] = '<li class="divider"><a>&nbsp;</a></li>';
		$links[] = '<li><a href="'.$_SERVER['REQUEST_URI'].'" class="btn-search" data-toggle="modal" data-target="#modal-search" ><span title="Search" class="glyphicon glyphicon-search" >&nbsp;Search</span></a></li>';
		$data['module']['title'] ="List Holidays". "[".$emp_list['count']."]";
		$rec = array();
		if(count($emp_list['data'])>0)
		$i = $ofset+1;
		foreach ($emp_list['data'] as $item){
			$arrs = array($i);
			$r = $this->configmodel->getDetail('28',$item['id'],'',$bid);
			foreach($keys as $k){
				$v='';
				if($k=="Action"){
					$v.=($opt_add)? '<a href="EditHoliday/'.$item['id'].'"><span title="Edit" class="fa fa-edit"></span></a>':'';
					$v.=($opt_delete)?'<a href="group/deleteHoliday/'.$item['id'].'"><span title="Delete" class="glyphicon glyphicon-trash"></span></a>':'';
				}else{
						$v = isset($r[$k])?$r[$k]:"";
				}
				array_push($arrs,$v);
			}
			$i++;
			array_push($rec,$arrs);
		}
		$data['itemlist']['rec'] = $rec;
		$data['itemlist']['count'] = $emp_list['count'];
		$this->pagination->initialize(array(
						 'base_url'=>site_url('ManageHoliday/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>2					
				));	
		$data['paging'] = $this->pagination->create_links();
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='filename'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked && !in_array($field['fieldname'] ,array('date'))){ 
					$formFields[] = array(
									'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>form_input(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname'],
											'class'		=>($field['fieldname']=="endtime" ||$field['fieldname']=="datetime")?'datepicker_leads form-control':'form-control'))
											);
					}						
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){$formFields[] = array(
								'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>form_input(array(
													'name'      => 'custom['.$field['fieldid'].']'
													)));
				}						
			}
		}
		$data['links']= $links;
		$data['form'] = array(
			'open'=>form_open_multipart('ManageHoliday/0',array('name'=>'manageemp','class'=>'form','id'=>'manageemp','method'=>'post')),
			'form_field'=>$formFields,
			'parentids'=>$parentbids,
			'busid'=>$bid,
			'pid'=>$this->session->userdata('pid'),
			'close'=>form_close(),
			'title'=>$this->lang->line('level_search')
			);
	    if(isset($_POST['search']) && $_POST['search'] == 'search'){
			$this->load->view('search_view',$data);
			return true;
		}
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function deleteHoliday($contid){
		$res=$this->groupmodel->deleteHoliday($contid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', "Holiday Deleted Successfully");
		redirect('ManageHoliday/0');
	}
	function EditHoliday($contid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if($this->input->post('update_system')){
			$res=$this->groupmodel->EditHoliday($contid);
			if($res!=""){
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg', "Holidays updated Successfully");
				redirect('ManageHoliday/0');
			}else{
				$this->session->set_flashdata('msgt', 'error');
				$this->session->set_flashdata('msg', "Error While Updating Holidays");
				redirect('ManageHoliday/0');
			}
		}
		$this->sysconfmodel->data['html']['title'] .= " | Edit Holiday";
		$data['module']['title'] = "Edit Holiday";
		$fieldset = $this->configmodel->getFields('28',$bid);						
		$itemDetail = $this->configmodel->getDetail('28',$contid,'',$bid);
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show']){
				$formFields[] = array(
							'label'=>'<label class="col-sm-4 text-right" for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
									 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
							'field'=>form_input(array(
									'name'      => $field['fieldname'],
									'class'      => 'form-control',
									'id'        => $field['fieldname'],
									'value'		=> $itemDetail[$field['fieldname']],
									'class'		=> ($field['fieldname']=='date')?'datepicker_leads form-control':'form-control'
									))
									);
				
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked)$formFields[] = array(
						'label'=>'<label class="col-sm-4 text-right" for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
						'field'=>$this->configmodel->createFieldAdvance($field,isset($itemDetail[$field['fieldKey']]) ? $itemDetail[$field['fieldKey']] : '',''));
			}
		}						
		$data['form'] = array(
			   'form_attr'=>array('action'=>'EditHoliday/'.$contid,'name'=>'editcontact','id'=>'editcontact','enctype'=>"multipart/form-data"),
				'fields'=>$formFields,
				'close'=>form_close(),
		);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function dis_grp_emp($eid,$gid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res=$this->groupmodel->dis_grp_employee($eid,$gid,$bid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg', ($res)?'Employee Enabled Succesfully':'Employee Disabled Succesfully');
		redirect('ListempGroup/'.$gid);
	}
	function business_groupcnt(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$gcnt=$this->groupmodel->getbusinessgcnt($bid);
		return $gcnt;
	}
	function glist(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$glist=$this->groupmodel->get_glist($bid);
		$content='<table>
					<tr><td colspan="3"><div id="error" style="color:red;text-align:center;"></div></td></tr>
					<tr>';
		if(!empty($glist)){
			$i=1;
			foreach($glist as $grows){
				if($i%3!=0){
					$content.='<td><label><input type="checkbox" name="gidss[]" value="'.$grows['gid'].'"/>'.$grows['groupname'].'</label></td>';	
				}else{
					$content.='</tr><td><label><input type="checkbox" name="gidss[]" value="'.$grows['gid'].'"/>'.$grows['groupname'].'</label></td>';
				}
				$i++;
			}
		}		
		$content.='</tr>
				</table>';
		$message= '<div id="box">
				<h3>Group List</h3>
				<form action="group/massupdate" class="form" id="glistFrm" name="glistFrm" method="POST">
				<fieldset id="priseries">
						<legend>Group List</legend>
						'.$content.'
				</fieldset>
				<table border="0"><tr><td><center>
				<input id="button1" class="gListSubmit" type="submit" name="submit" value="'.$this->lang->line('submit').'" /> 
				<input id="button2" type="reset" value="'.$this->lang->line('reset').'" />
				</center></td></tr></table>
				'.form_close().'
				</div>';
		echo $message;
	}
	function refreshcounter($gid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$res = $this->groupmodel->refreshcounter($gid,$bid);
		if($res == 1){
			$this->session->set_flashdata('msgt', 'success');
			$this->session->set_flashdata('msg', ' The Call Counter has reset to 0');
		}else{
			$this->session->set_flashdata('msgt', 'error');
			$this->session->set_flashdata('msg', ' Error while reset the counter');
		}
		redirect('ListempGroup/'.$gid);
	}
}
/* end */
?>
