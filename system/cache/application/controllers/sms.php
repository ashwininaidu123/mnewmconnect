<?php
class Pbx extends Controller {
	var $data,$roleDetail;
	function Sms(){
		parent::controller();
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('pbxmodel');
		$this->load->model('configmodel');
		$this->load->model('empmodel');
		$this->load->model('systemmodel');
		$checklist=$this->systemmodel->checked_featuremanage();
		if(!in_array(8,$checklist))redirect('Employee/access_denied');
		$this->roleDetail = $this->empmodel->getRoledetail($this->session->userdata('roleid'));
	}
	
	function index(){
		$this->manage();
	}
	function manage(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['20']['opt_view']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_pbxlist');
		$this->sysconfmodel->data['links'] = '<a href="pbx/configure"><span title="Add Mcube X" class="glyphicon glyphicon-plus-sign">&nbsp;Add MCubeX</span></a>';
		$ofset = ($this->uri->segment(2)!=null)?$this->uri->segment(2):0;
		$limit = '20';
		$data['itemlist'] = $this->pbxmodel->getPBXlist($bid,$ofset,$limit);
		$data['module']['title'] = $this->lang->line('label_pbxlist'). "[".$data['itemlist']['count']."]";
		$this->pagination->initialize(array(
						 'base_url'=>site_url('pbx/manage')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$fieldset = $this->configmodel->getFields('20',$bid);
		$formFields = array();
		$advsearch=array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show']
				&& !in_array($field['fieldname'],array('bday','hdaytext','greetings','hdayaudio','record','remark','noext','dircode','ortcode'))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) { $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>($field['fieldname']=='operator')?
											form_dropdown('operator',$this->groupmodel->employee_list(),'','id="operator" class="auto"')
											:form_input(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname']))
											);
											$advsearch[$field['fieldname']]=(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']);
							}				
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){ $formFields[] = array(
								'label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,'','search'));
								$advsearch['custom['.$field['fieldid'].']']=$field['customlabel'];
							}	
			}
		}
		
		$save_cnt=save_search_count($bid,'20',$this->session->userdata('eid'));
		$data['form'] = array(
					'open'=>form_open('pbx/manage',array('name'=>'search','class'=>'form','id'=>'search','method'=>'post')),
					'title'=>'MCube X Search',
					'form_field'=>$formFields,
					'save_search'=>$save_cnt,
					'adv_search'=>array(),
					'parentids'=>$parentbids,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function addFrm(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['20']['opt_add']) redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('title', 'title', 'required|min_length[4]|max_length[200]|alpha_numeric');
			if(!$this->form_validation->run() == FALSE){				
				if(count($_POST)>0){
					$get = $this->pbxmodel->addpbx();
					redirect('ManagePBX/0');
				}
			}	
		}		
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_pbxconfigure');
		$data['module']['title'] = $this->lang->line('label_pbxconfigure');
		$fieldset = $this->configmodel->getFields('20',$bid,'1');
		$formFields = array();
		$itemId = ($this->uri->segment(3)!=null)?$this->uri->segment(3):"";
		$itemDetail = ($itemId!='')?$this->configmodel->getDetail('20',$itemId,'',$bid):array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				$bday = ($field['fieldname']=='bday' && isset($itemDetail['bday']))?json_decode($itemDetail['bday']):'';
				$empList = $this->groupmodel->employee_list();$empList['0'] = 'Auto';
				if($checked) $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>',
									'field'=>((in_array($field['fieldname'],array('greetings','hdayaudio')))?
											form_input(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname'],
											'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'',
											'type'	  => 'file'
											)).((isset($itemDetail[$field['fieldname']]) && $itemDetail[$field['fieldname']]!='' && file_exists('sounds/'.$itemDetail[$field['fieldname']]))? 
											'<a target="_blank" href="'.site_url('sounds/'.$itemDetail[$field['fieldname']]).'"><span title="Sound" class="fa fa-volume-up"></span></a>':'')
											:(($field['fieldname']=='prinumber')?
											form_dropdown('prinumber',$this->systemmodel->getPriList(isset($itemDetail['prinumber'])?$itemDetail['prinumber']:set_value($field['fieldname']),'2'),(isset($itemDetail['prinumber']) && $itemDetail['prinumber']>0)?$itemDetail['prinumber']:'',"id='prinumber' class=''"):(in_array($field['fieldname'],array('record','noext'))?
											form_checkbox(array(
													 'name'=>$field['fieldname'],'id'=>$field['fieldname']
													,'checked'=>(isset($itemDetail[$field['fieldname']]) && $itemDetail[$field['fieldname']]=='1')? TRUE: false
													,'value'=>'1'))
											:((in_array($field['fieldname'],array('operator','ortcode','dircode')))?
											form_dropdown($field['fieldname'],(($field['fieldname']=='operator')?$empList:array('0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9')),isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:(
													(in_array($field['fieldname'],array('operator','dircode')))?'1':'0'
											),'id="'.$field['fieldname'].'" class="auto"')
											:(($field['fieldname']=='bday')?
																'<table class="short"><tr>'
																.'<td>'.form_checkbox(array('name'=>'bday[Mon][day]','value'=>'1','id'=>'mon_day','checked'=>((isset($bday->Mon->day) || $itemId=='')?true:''))).'<label for="mon_day">Monday</label>'.'</td>'
																.'<td>'.form_input(array('name'=>'bday[Mon][st]','id'=>'mon_st','class'=>'timepicker','placeholder'=>'Start Time','value'=>(isset($bday->Mon->st)?$bday->Mon->st:'00:00'))).'</td>'
																.'<td>'.form_input(array('name'=>'bday[Mon][et]','id'=>'mon_et','class'=>'timepicker','placeholder'=>'End Time','value'=>(isset($bday->Mon->et)?$bday->Mon->et:'23:59'))).'</td>'
																.'</tr><tr>'
																.'<td>'.form_checkbox(array('name'=>'bday[Tue][day]','value'=>'1','id'=>'tue_day','checked'=>((isset($bday->Tue->day) || $itemId=='')?true:''))).'<label for="tue_day">Tuesday</label>'.'</td>'
																.'<td>'.form_input(array('name'=>'bday[Tue][st]','id'=>'tue_st','class'=>'timepicker','placeholder'=>'Start Time','value'=>(isset($bday->Tue->st)?$bday->Tue->st:'00:00'))).'</td>'
																.'<td>'.form_input(array('name'=>'bday[Tue][et]','id'=>'tue_et','class'=>'timepicker','placeholder'=>'End Time','value'=>(isset($bday->Tue->et)?$bday->Tue->et:'23:59'))).'</td>'
																.'</tr><tr>'
																.'<td>'.form_checkbox(array('name'=>'bday[Wed][day]','value'=>'1','id'=>'wed_day','checked'=>((isset($bday->Wed->day) || $itemId=='')?true:''))).'<label for="wed_day">Wednesday</label>'.'</td>'
																.'<td>'.form_input(array('name'=>'bday[Wed][st]','id'=>'wed_st','class'=>'timepicker','placeholder'=>'Start Time','value'=>(isset($bday->Wed->st)?$bday->Wed->st:'00:00'))).'</td>'
																.'<td>'.form_input(array('name'=>'bday[Wed][et]','id'=>'wed_et','class'=>'timepicker','placeholder'=>'End Time','value'=>(isset($bday->Wed->et)?$bday->Wed->et:'23:59'))).'</td>'
																.'</tr><tr>'
																.'<td>'.form_checkbox(array('name'=>'bday[Thu][day]','value'=>'1','id'=>'thu_day','checked'=>((isset($bday->Thu->day) || $itemId=='')?true:''))).'<label for="thu_day">Thursday</label>'.'</td>'
																.'<td>'.form_input(array('name'=>'bday[Thu][st]','id'=>'thu_st','class'=>'timepicker','placeholder'=>'Start Time','value'=>(isset($bday->Thu->st)?$bday->Thu->st:'00:00'))).'</td>'
																.'<td>'.form_input(array('name'=>'bday[Thu][et]','id'=>'thu_et','class'=>'timepicker','placeholder'=>'End Time','value'=>(isset($bday->Thu->et)?$bday->Thu->et:'23:59'))).'</td>'
																.'</tr><tr>'
																.'<td>'.form_checkbox(array('name'=>'bday[Fri][day]','value'=>'1','id'=>'fri_day','checked'=>((isset($bday->Fri->day) || $itemId=='')?true:''))).'<label for="fri_day">Friday</label>'.'</td>'
																.'<td>'.form_input(array('name'=>'bday[Fri][st]','id'=>'fri_st','class'=>'timepicker','placeholder'=>'Start Time','value'=>(isset($bday->Fri->st)?$bday->Fri->st:'00:00'))).'</td>'
																.'<td>'.form_input(array('name'=>'bday[Fri][et]','id'=>'fri_et','class'=>'timepicker','placeholder'=>'End Time','value'=>(isset($bday->Fri->et)?$bday->Fri->et:'23:59'))).'</td>'
																.'</tr><tr>'
																.'<td>'.form_checkbox(array('name'=>'bday[Sat][day]','value'=>'1','id'=>'sat_day','checked'=>((isset($bday->Sat->day) || $itemId=='')?true:''))).'<label for="sat_day">Saturday</label>'.'</td>'
																.'<td>'.form_input(array('name'=>'bday[Sat][st]','id'=>'sat_st','class'=>'timepicker','placeholder'=>'Start Time','value'=>(isset($bday->Sat->st)?$bday->Sat->st:'00:00'))).'</td>'
																.'<td>'.form_input(array('name'=>'bday[Sat][et]','id'=>'sat_et','class'=>'timepicker','placeholder'=>'End Time','value'=>(isset($bday->Sat->et)?$bday->Sat->et:'23:59'))).'</td>'
																.'</tr><tr>'
																.'<td>'.form_checkbox(array('name'=>'bday[Sun][day]','value'=>'1','id'=>'sun_day','checked'=>((isset($bday->Sun->day) || $itemId=='')?true:''))).'<label for="sun_day">Sunday</label>'.'</td>'
																.'<td>'.form_input(array('name'=>'bday[Sun][st]','id'=>'sun_st','class'=>'timepicker','placeholder'=>'Start Time','value'=>(isset($bday->Sun->st)?$bday->Sun->st:'00:00'))).'</td>'
																.'<td>'.form_input(array('name'=>'bday[Sun][et]','id'=>'sun_et','class'=>'timepicker','placeholder'=>'End Time','value'=>(isset($bday->Sun->et)?$bday->Sun->et:'23:59'))).'</td>'
																.'</tr></table>'
											:form_input(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname'],
											'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname']),
											)))
											)
											)))
											);
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked)$formFields[] = array(
								'label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,isset($itemDetail['custom['.$field['fieldid'].']'])?$itemDetail['custom['.$field['fieldid'].']']:""));
			}
		}
		$formFields1=array();
			if($itemId!=""){
			$addons=$this->sysconfmodel->get_baddons_number($itemDetail['prinumber']);
			$addon=array();
			foreach($addons as $ads){
				$addon[]=$ads['feature_id'];
			}
			$fieldset = $this->sysconfmodel->getFields_addons('20');
			foreach($fieldset as $field){
				$con=($itemDetail['prinumber']>20000)?in_array($field['addon'],$addon):'1';
			if($con){
					$cf=array('label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>',
								'field'=>(in_array($field['fieldname'],array('replytoexecutive','replytocustomer','sameexe','record','misscall')))?form_checkbox(array(
																 'id'=>$field['fieldname']
																 ,'name'=>$field['fieldname']
																 ,'checked'=>(isset($itemDetail[$field['fieldname']]) && $itemDetail[$field['fieldname']]=='1')? TRUE: false
																,'value'=>'1')):(($field['fieldname']=="replymessage")?
												form_textarea(array(
											  'name'      => $field['fieldname'],
											  'id'        => $field['fieldname'],
											  'class'	  => 'word_count',
											  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname']),
											))."<a style='display:block;' title='Click to insert Executive Name in Reply Message, count may be differ from actual message' class='addVar' rel='@name@'>Executive Name</a>"
											  ."<a style='display:block;' title='Click to insert Executive Number in Reply Message, count may be differ from actual message' class='addVar' rel='@number@'>Executive Number</a>":(in_array($field['fieldname'],array('hdayaudio','greetings'))?
																	form_input(array(
																	  'name'      => $field['fieldname'],
																	  'id'        => $field['fieldname'],
																	  'type'      => 'file',
																	  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname']))).((isset($itemDetail[$field['fieldname']]) && $itemDetail[$field['fieldname']]!='' && file_exists('sounds/'.$itemDetail[$field['fieldname']]))? 
											'<a target="_blank" href="'.site_url('sounds/'.$itemDetail[$field['fieldname']]).'"><span title="Sound" class="fa fa-volume-up"></span></a>':'')
																	:form_input(array(
																	  'name'      => $field['fieldname'],
																	  'id'        => $field['fieldname'],
																	  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname']))
																  ))));		
																  array_push($formFields1,$cf);		
							
											  
				}
					
		}
		}	
		$data['form'] = array(
					'open'=>form_open_multipart('PBXadd/0'
								,array('name'=>'pbxadd','class'=>'form','id'=>'pbxadd','method'=>'post')
								,array('pbxid'=>$itemId
								  ,'bid'=>$bid
								)),
					'fields'=>$formFields,
					'fields1'=>$formFields1,
					'parentids'=>($itemId=='')?$parentbids:'',
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close()
				);
		return $data;
	}

	function configure(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['20']['opt_add']) redirect('Employee/access_denied');
		$this->sysconfmodel->viewLayout('form_view',$this->addFrm());
	}

	function addpopup(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['20']['opt_add']) redirect('Employee/access_denied');
		$this->load->view('form_view',$this->addFrm());
	}
		
	function delete($pbxid){
		$this->pbxmodel->deletePBX($pbxid);
		redirect('pbx/manage');
	}
	
	function delext($eixid){
		$this->pbxmodel->deletePBXExt($eixid);
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	function undelete($pbxid=''){
		$this->pbxmodel->undeletePBX($pbxid);
		redirect('pbx/deleted');
	}
	
	function deleted(){
		$roleDetail = $this->roleDetail;
		//echo "<pre>";print_r($roleDetail);echo "</pre>";
		if(!$roleDetail['modules']['20']['opt_view']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_pbxlist');
		$this->sysconfmodel->data['file'] = "system/application/js/pbx.js.php";
		$this->sysconfmodel->data['links'] = '<a href="pbx/configure"><span title="Add Mcube X" class="glyphicon glyphicon-plus-sign"></span></a>';
		$ofset = ($this->uri->segment(2)!=null)?$this->uri->segment(2):0;
		$limit = '20';
		$data['itemlist'] = $this->pbxmodel->getDeletedPBXlist($bid,$ofset,$limit);
		$data['module']['title'] = $this->lang->line('label_pbxlist').'['.$data['itemlist']['count'].']';
		$this->pagination->initialize(array(
						 'base_url'=>site_url('pbx/manage')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$fieldset = $this->configmodel->getFields('20');
		$formFields = array();
		$advsearch = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show']
				&& !in_array($field['fieldname'],array('bday','hdaytext','greetings','hdayaudio','record','remark','noext','ortcode','dircode'))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked)  {
								$formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>($field['fieldname']=='operator')?
											form_dropdown('operator',$this->groupmodel->employee_list(),'','id="operator" class=""')
											:form_input(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname']))
											);
								$advsearch[$field['fieldname']]=(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']);				
							}				
			}
		}
	
		$save_cnt=save_search_count($bid,'20',$this->session->userdata('eid'));
		$data['form'] = array(
					'open'=>form_open('pbx/deleted',array('name'=>'search','class'=>'form','id'=>'search','method'=>'post')),
					'title'=>'MCube X Search',
					'form_field'=>$formFields,
					'adv_search'=>array(),
					'save_search'=>$save_cnt,
					'parentids'=>$parentbids,
							'busid'=>$bid,
							'pid'=>$this->session->userdata('pid'),
					'close'=>form_close()
				);
		
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	
	function addext($pbxid='',$extid=''){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['20']['opt_add']) redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			//$this->form_validation->set_rules('targetid', 'Destination', 'required');
			$this->form_validation->set_rules('ext', 'Extension', 'required|numeric');
			if(!$this->form_validation->run() == FALSE){				
				if(count($_POST)>0){
					$this->pbxmodel->addpbxExt();
					redirect($_SERVER['HTTP_REFERER']);
				}
			}else{
				$this->session->set_flashdata(array('msgt' => 'error','msg' => validation_errors()));
				redirect($_SERVER['HTTP_REFERER']);
			}
		}
		$this->sysconfmodel->data['file'] = "system/application/js/pbx.js.php";
		$data['module']['title'] = $this->lang->line('label_pbxext');
		$formFields = array();
		$itemDetail = ($extid)?(array)$this->pbxmodel->getExtDetail($extid):array();
		//print_r($itemDetail);exit;
		
		$formFields[] = array(
					'label'=>'<label for="targettype">'.$this->lang->line('label_pbxtargettype').' : </label>',
					'field'=>form_dropdown('targettype',array(
														''=>'Select Target Type'
														,'employee'=>'Employee'
														,'group'=>'Group'
														,'ivrs'=>'IVRS'
														,'pbx'=>'PBX'
														,'voicemgs'=> 'Voicemgs'
													),isset($itemDetail['targettype'])?$itemDetail['targettype']:'','id="targettype" class="required"'));
	
		if(isset($itemDetail['targettype']) && $itemDetail['targettype']=='employee'){
			$formFields[] = array(
					'label'=>'<label for="targetid">'.$this->lang->line('label_pbxemp').' : </label>',
					'field'=>form_dropdown('targetid',$this->systemmodel->get_emp_list(),isset($itemDetail['targetid'])?$itemDetail['targetid']:'','id="targetid" class="required"'));
		}
		if(isset($itemDetail['targettype']) && $itemDetail['targettype']=='ivrs'){
			$formFields[] = array(
					'label'=>'<label for="targetid">'.$this->lang->line('label_pbxivrs').' : </label>',
					'field'=>form_dropdown('targetid',$this->systemmodel->get_ivrs_list(),isset($itemDetail['targetid'])?$itemDetail['targetid']:'','id="targetid" class="required"'));
		}
		if(isset($itemDetail['targettype']) && $itemDetail['targettype']=='group'){
			$formFields[] = array(
					'label'=>'<label for="targetid">'.$this->lang->line('label_pbxgroup').' : </label>',
					'field'=>form_dropdown('targetid',$this->systemmodel->get_groups(),isset($itemDetail['targetid'])?$itemDetail['targetid']:'','id="targetid" class="required"'));
		}
		if(isset($itemDetail['targettype']) && $itemDetail['targettype']=='pbx'){
			$formFields[] = array(
					'label'=>'<label for="targetid">'.$this->lang->line('label_pbx').' : </label>',
					'field'=>form_dropdown('targetid',$this->systemmodel->get_pbx_list(),isset($itemDetail['targetid'])?$itemDetail['targetid']:'','id="targetid" class="required"'));
		}
			$formFields[] = array(
					'label'=>'<label for="ext">'.$this->lang->line('label_pbxext').' : </label>',
					'field'=>form_input(array(
							'name'      => 'ext',
							'id'        => 'ext',
							'value'     => isset($itemDetail['ext'])?$itemDetail['ext']:'',
							'class'=>'required'
							)));
		
		$data['form'] = array(
		            'form_attr'=>array('action'=>'pbx/addext','name'=>'pbxext'),
					//~ 'open'=>form_open_multipart('pbx/addext'
								//~ ,array('name'=>'form','class'=>'form','id'=>'pbxext','method'=>'post')
								//~ ,array('pbxid'=>$pbxid
									  //~ ,'extid'=>$extid
									  //~ ,'bid'=>$this->session->userdata('bid')
								//~ )),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->load->view('form_view',$data);
	}
	
	function listext($pbxid=''){
		$roleDetail = $this->roleDetail;
		//echo "<pre>";print_r($roleDetail);echo "</pre>";
		if(!$roleDetail['modules']['20']['opt_view']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('label_pbxext');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_pbxext');
		$this->sysconfmodel->data['file'] = "system/application/js/pbx.js.php";
		$this->sysconfmodel->data['links'] = '<a href="pbx/addext/'.$pbxid.'" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Add Mcube X" class="glyphicon glyphicon-plus-sign"></span></a>';
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '30';
		$data['itemlist'] = $this->pbxmodel->getPBXExtlist($pbxid,$ofset,$limit);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Listextn/'.$pbxid)
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
						,'uri_segment'=>4
				));
		$data['paging'] = $this->pagination->create_links();
		$formFields[] = array(
					'label'=>'<label for="ext1">'.$this->lang->line('label_pbxext').' : </label>',
					'field'=>form_input(array(
							'name'      => 'ext',
							'id'        => 'ext1',
							'value'     => isset($itemDetail['ext'])?$itemDetail['ext']:'',
							)));
		
		$data['form'] = array(
					'open'=>form_open('Listextn/'.$pbxid,array('name'=>'search','class'=>'form','id'=>'search','method'=>'post')),
					'title'=>'MCube X Search',
					'form_field'=>$formFields,
					'adv_search'=>array(),
					'close'=>form_close()
				);
		
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	
	function report(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['20']['opt_view']) redirect('Employee/access_denied');
		
		
		$this->sysconfmodel->data['file'] = "system/application/js/pbx.js.php";
		if($this->input->post('download')){
			$filename = $this->pbxmodel->getPrintReport($bid);
			$dlink =  "<a href='".$this->config->item('reports_path').$filename.".zip"."' target='_blank' style='color:#fff;'>Start Download</a>  ";
		}else{
			$dlink = "";
		}
		
		$u3 = ($this->uri->segment(3)!='')?$this->uri->segment(3):'all';
		$this->sysconfmodel->data['links'] = ($roleDetail['modules']['24']['opt_download'])?$dlink.'<a href="pbx/pbx_csv" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Download" class="glyphicon glyphicon-download-alt"></span></a>':'';
		$this->sysconfmodel->data['links'] .= '<a href="#" class="blkemail" rel="pbx">&nbsp;<span title="Bulk Mail" class="fa fa-envelope-o"></span>&nbsp;</a>';
		$this->sysconfmodel->data['links'] .= '<a href="Report/blksms" class="blkSMs" data-toggle="modal" data-target="#modal-blksms" 
		rel="pbx">&nbsp;<span title="Bulk SMS" class="glyphicon glyphicon-comment"></span>&nbsp;</a>';
		//$this->sysconfmodel->data['links'] = '';
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '30';
		$data['itemlist'] = $this->pbxmodel->getPBXReportlist($bid,$ofset,$limit,$u3);
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_pbxreport');
		$data['module']['title'] = $this->lang->line('label_pbxreport'). '['.$data['itemlist']['count'].']';
		$this->pagination->initialize(array(
						 'base_url'=>site_url('pbx/report/'.$u3.'/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit				
						,'uri_segment'=>4			
				));
		$data['paging'] = $this->pagination->create_links();
		$fieldset = $this->configmodel->getFields('24',$bid);
		$formFields = array();
		 
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='filename'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked && !in_array($field['fieldname'] ,array('greetings','bday','hdaytext','hdayaudio','operator','prinumber','record','remark','noext'))) {
					$formFields[] =!in_array($field['fieldname'] ,array('pulse','extensions'))?array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>form_input(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname'],
											'class'		=>($field['fieldname']=="endtime" ||$field['fieldname']=="starttime")?'datepicker_leads':''))
											):(($field['fieldname']=='pulse')?array('label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>form_dropdown('ptype',array(
																				'>'=>' > ',
																				'='=>' = ',
																				'<'=>' < '
																			),'',"style='width:50px;'").' '.
													form_input(array(
													'name'      => $field['fieldname'],
													'id'        => $field['fieldname']
													),'',"style='width:200px;'")):array('label'=>'','field'=>''));
													$advsearch[$field['fieldname']]=(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']);	
						}							
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){ $formFields[] = array(
								'label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>form_input(array(
													'name'      => 'custom['.$field['fieldid'].']'
													)));
								$advsearch['custom['.$field['fieldid'].']']=$field['customlabel'];						
							}						
			}
		}
		unset($advsearch['extensions']);
		$save_cnt=save_search_count($bid,'24',$this->session->userdata('eid'));
		$search_names=get_save_searchnames($bid,'24',$this->session->userdata('eid'));
		$data['form'] = array(
					'open'=>form_open('pbx/report',array('name'=>'search','class'=>'form','id'=>'search','method'=>'post')),
					'title'=>'MCube X Report Search',
					'form_field'=>$formFields,
					'adv_search'=>$advsearch,
					'search_names'=>$search_names,
					'save_search'=>$save_cnt,
					'search_url'=>'pbx/report/',
					'groups'=>$this->systemmodel->get_pbx_list(),
					'employees'=>array(),
					'parentids'=>$parentbids,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close()
				);
		$data['tab'] = true;	
		$this->sysconfmodel->viewLayout('list_view',$data);		
	}
	
	
	
	function pbx_csv(){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data=array('systemfields'=>$this->configmodel->getFields('24',$bid),
					'roleDetail'=>$this->roleDetail,
					 'type'=>'',
					 'eid'=>'',
					 );
		$this->load->view('pbx_csvreport',$data);
	}
	function print_csv($array){
		$filename = "Export_".date("Y-m-d_H-i",time());
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header( "Content-disposition: filename=".$filename.".csv");
		print $array;exit;
	}
	function detail($pbxid=''){
		$roleDetail = $this->roleDetail;
		//echo "<pre>";print_r($roleDetail);echo "</pre>";
		if(!$roleDetail['modules']['20']['opt_view']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data['module']['title'] = $this->lang->line('label_pbxext');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_pbxext');
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '20';
		$data['links'] = '';
		$data['nosearch'] = true;
		$data['form']=array("adv_search"=>array());
		$data['itemlist'] = $this->pbxmodel->getPBXExtlist1($pbxid,$ofset,$limit);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('Listextn/'.$pbxid)
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit	
				));
		$data['paging'] = $this->pagination->create_links();
		
		$this->load->view('list_view',$data);
	}
	function EmpByid($id){
		$this->load->model('groupmodel');
		$res=$this->groupmodel->get_empname($id);
		echo $res->extension;
		
	}
	function PbxeditReport($callid){
		if($this->input->post('update_system')){
			$res=$this->pbxmodel->updatePbxReport($callid);
			redirect('pbx/report');
			
		}
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['24']['opt_view']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_ivrsreport');
		$data['module']['title'] = $this->lang->line('label_ivrsreport');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$fieldset = $this->configmodel->getFields('24',$bid);
		$formFields = array();
		$itemId = $callid;
		$itemDetail = $this->configmodel->getDetail('24',$itemId,'',$bid);
		foreach($fieldset as $field){$checked = false;
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>in_array($field['fieldname'],array('name','email'))
												?form_input(array(
																	  'name'      => $field['fieldname'],
																	  'id'        => $field['fieldname'],
																	  'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname'])))
												:(isset($itemDetail[$field['fieldname']])
													?$itemDetail[$field['fieldname']]
													:''));
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked)$formFields[] = array(
								'label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,isset($itemDetail['custom['.$field['fieldid'].']'])?$itemDetail['custom['.$field['fieldid'].']']:""));
			}
		}
		$data['form'] = array(
		            'form_attr'=>array('action'=>'pbx/PbxeditReport/'.$callid,'name'=>'autoadd'),
					//~ 'open'=>form_open_multipart('pbx/PbxeditReport/'.$callid
								//~ ,array('name'=>'autoadd','class'=>'form','id'=>'autoadd','method'=>'post')
								//~ ,array('bid'=>$bid)
							//~ ),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function AddasOperator($opt,$pbxid){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$Status=$this->pbxmodel->PbxOperator($opt,$pbxid,$bid);
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg','Operator Updated Successfully');
		redirect('Listextn/'.$pbxid);
	}
}
?>
