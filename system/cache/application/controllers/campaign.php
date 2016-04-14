<?php
class Campaign extends Controller {
	var $data,$roleDetail;
	function Campaign(){	
		parent::controller();
		if(!$this->session->userdata('logged_in'))redirect('/site/login');
		$this->load->model('sysconfmodel');
		$this->data = $this->sysconfmodel->init();
		$this->load->model('systemmodel');
		$this->load->model('groupmodel');
		$this->load->model('configmodel');
		$this->load->model('empmodel');
		$this->load->model('leadsmodel');
		$this->load->model('campaignmodel');
		$this->roleDetail = $this->sysconfmodel->data['roleDetail'];
	}
	function index(){
		$this->manage();
	}
	function manage(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['33']['opt_view']) redirect('Employee/access_denied');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_CampaignManagement');
		$this->sysconfmodel->data['file'] = "system/application/js/ivrs.js.php";
		$this->sysconfmodel->data['links'] = '<a href="campaign/add"><span title="Add Campaign" class="glyphicon glyphicon-plus-sign"></span></a>';
		$ofset = ($this->uri->segment(3)!=null)?$this->uri->segment(3):0;
		$limit = '20';
		$data['itemlist'] = $this->campaignmodel->getCampaignlist($bid,$ofset,$limit);
		$data['module']['title'] = $this->lang->line('label_CampaignManagement'). "[".$data['itemlist']['count']."]";
		$this->pagination->initialize(array(
						 'base_url'=>site_url('campaign/manage')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit						
				));
		$data['paging'] = $this->pagination->create_links();
		$fieldset = $this->configmodel->getFields('33');
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && $field['fieldname']!='filename'){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>form_input(array(
											'name'      => $field['fieldname'],
											'class'		=>($field['fieldname']=="campaign_startdate" || $field['fieldname']=="campaign_enddate")?'datepicker_leads':'',
											'id'        => $field['fieldname']))
											);
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked)$formFields[] = array(
								'label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,'','search'));
			}
		}
		$data['form'] = array(
					'open'=>form_open('campaign/manage',array('name'=>'search','class'=>'form','id'=>'search','method'=>'post')),
					'title'=>'Campaign Search',
					'form_field'=>$formFields,
					'parentids'=>$parentbids,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close()
				);
		
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	
	
	function add(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['33']['opt_add']) redirect('Employee/access_denied');
		$this->sysconfmodel->viewLayout('form_view',$this->addFrm());
	}
	
	function addpopup(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['33']['opt_add']) redirect('Employee/access_denied');
		$this->load->view('form_view',$this->addFrm());
	}
	
	function addFrm(){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['33']['opt_add']) redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			$this->form_validation->set_rules('campaign_name', 'Campaign Name', 'required');
			$this->form_validation->set_rules('campaign_startdate', 'Campaign Startdate', 'required');
			$this->form_validation->set_rules('campaign_enddate', 'Campaign Enddate', 'required');
			$this->form_validation->set_rules('perday_limit', 'Perday Limit', 'required|numeric');
			$this->form_validation->set_rules('perday_lead', 'Perday Lead', 'required|numeric');
			$this->form_validation->set_rules('total_lead', 'Total Leads', 'required|numeric');
			$this->form_validation->set_rules('status', 'Status', 'required');
			$this->form_validation->set_rules('budget', 'Budget', 'required|numeric');
			$this->form_validation->set_rules('campaign_type', 'Campaign Type', 'required');
			$this->form_validation->set_rules('file_id', 'File Name', 'required');
			$this->form_validation->set_rules('action_oncomplete', 'Action', 'required');
			if(!$this->form_validation->run() == FALSE){
				
				if(count($_POST)>0){//print_r($_FILES);exit;
					$get = $this->campaignmodel->addCampaign();
					redirect('campaign');
				}
			}	
		}		
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_campaignconfig');
		$this->sysconfmodel->data['file'] = "system/application/js/campaign.js.php";
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$data['module']['title'] = $this->lang->line('label_campaignconfig');
		$fieldset = $this->configmodel->getFields('33',$bid);
		$formFields = array();
		$itemId = ($this->uri->segment(3)!=null)?$this->uri->segment(3):"";
		$itemDetail = $this->configmodel->getDetail('33',$itemId,'',$bid);
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).'&nbsp;&nbsp;<img src="system/application/img/icons/help.png" title="'.$this->lang->line('TTmod_'.$field['modid'])->$field['fieldname'].'" >&nbsp;&nbsp: </label>',
									'field'=>(
											($field['fieldname']=='filename')?
											form_input(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname'],
											'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'',
											'type'	  => 'file'
											)).((isset($itemDetail[$field['fieldname']]))? 
											'<a target="_blank" href="'.site_url('sounds/'.$itemDetail[$field['fieldname']]).'"><span title="Sound" class="fa fa-volume-up"></span></a>':'')
											
											:(($field['fieldname']=='ivrsnumber')?form_dropdown('prinumber',$this->systemmodel->getPriList(isset($itemDetail['prinumber'])?$itemDetail['prinumber']:set_value($field['fieldname']),'3'),(isset($itemDetail['prinumber'])?$itemDetail['prinumber']:''),"id='prinumber' class='auto'")
											:form_input(array(
											'name'      => $field['fieldname'],
											'id'        => $field['fieldname'],
											'class'		=>($field['fieldname']=="campaign_startdate" || $field['fieldname']=="campaign_enddate")?'datepicker_leads':'',
											'value'     => isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:set_value($field['fieldname']),
											))
											))
											);
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked)$formFields[] = array(
								'label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,isset($itemDetail['custom['.$field['fieldid'].']'])?$itemDetail['custom['.$field['fieldid'].']']:""));
			}
		}
		$data['form'] = array(
					'open'=>form_open_multipart('campaign/add'
								,array('name'=>'form','class'=>'form','id'=>'campaignadd','method'=>'post')
								,array('campaign_id'=>$itemId
								  ,'bid'=>$bid
								  ,'modid'=>'33'
								)),
					'fields'=>$formFields,
					'parentids'=>($itemId=='')?$parentbids:'',
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close()
				);
		return $data;
	}
	
	function details($cid){
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['33']['opt_view']) redirect('Employee/access_denied');
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('label_CampaignManagement');
		$data['module']['title'] = $this->lang->line('label_CampaignManagement');
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$fieldset = $this->configmodel->getFields('33',$bid);
		$formFields = array();
		$itemId = $cid;
		$itemDetail = $this->configmodel->getDetail('33',$itemId,'',$bid);
		foreach($fieldset as $field){$checked = false;
			if($field['type']=='s' && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>isset($itemDetail[$field['fieldname']])?$itemDetail[$field['fieldname']]:'');
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked)$formFields[] = array(
								'label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>isset($itemDetail['custom['.$field['fieldid'].']'])?$itemDetail['custom['.$field['fieldid'].']']:"");
			}
		}
		$data['form'] = array('open'=>'<form class="form">','fields'=>$formFields,'close'=>'</form>');
		$this->load->view('active_view',$data);
	}
	
	function delete($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['33']['opt_delete']) redirect('Employee/access_denied');
		$this->campaignmodel->del_campaign($id,$bid,"2");
		redirect($_SERVER['HTTP_REFERER']);
	}
	function undelete($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['33']['opt_delete']) redirect('Employee/access_denied');
		$this->campaignmodel->del_campaign($id,$bid,"1");
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	function campaignReport($type=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;	
		if($this->input->post('download')){
			$filename = $this->campaignmodel->campcsvreport($bid);
			$dlink =  "<a href='".$this->config->item('reports_path').$filename.".zip"."' target='_blank' style='color:#fff'>Start Download</a>  ";
		}else{
			$dlink = "";
		}
		if(!$roleDetail['modules']['34']['opt_view']) redirect('Employee/access_denied');
		if($this->input->post('submit')){	
			if($this->session->userdata('search')!=""){
				$s=$this->session->unset_userdata('search');
			}
		}
		$this->sysconfmodel->data['file'] = "system/application/js/group.js.php";
		$parentbids=array();
		if($this->session->userdata('eid')==1){
			$parentbids=$this->systemmodel->getChildBusiness();
		}
		$ofset = ($this->uri->segment(4)!=null)?$this->uri->segment(4):0;
		$limit = '20';
		$data['itemlist'] = $this->campaignmodel->getReport($bid,$ofset,$limit,$type);
		$this->pagination->initialize(array(
						 'base_url'=>site_url('campaign/campaignReport/'.$type.'/')
						,'total_rows'=>$data['itemlist']['count']
						,'per_page'=>$limit		
						,'uri_segment'=>4				
				));
		$data['module']['title'] = $this->lang->line('label_CampaignReport'). ' ['.$data['itemlist']['count'].']';
		$links= array();
		//$csv_link=($this->uri->segment(3)!='')?$this->uri->segment(3):'all';
		$links[] =($roleDetail['modules']['6']['opt_download']!=0)?$dlink.'<li><a href="campaign/campaign_csv/" class="btn-danger" data-toggle="modal" data-target="#modal-responsive"><span title="Download" class="glyphicon glyphicon-download-alt">Download All</span></a></li>':'';
		$links[] .= '</li><a href="#" class="blkemail" rel="campaign">&nbsp;<span title="Bulk Mail" class="fa fa-envelope-o">&nbsp; Mail</span></a></li>';
		$links[] .= '</li><a href="Report/blksms" class="blkSMs" data-toggle="modal" data-target="#modal-blksms" rel="campaign">&nbsp;<span title="Bulk SMS" class="glyphicon glyphicon-comment">&nbsp;SMS</span></a></li>';
		$fieldset = $this->configmodel->getFields('34',$bid);
		$formFields = array();
		foreach($fieldset as $field){
			$checked = false;
			if($field['type']=='s' && $field['show'] && !in_array($field['fieldname'],array('status'))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked) $formFields[] = array(
									'label'=>'<label for="'.$field['fieldname'].'">'.(($field['customlabel']!="")
											 ?$field['customlabel']:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']).' : </label>',
									'field'=>($field['fieldname']=='campaign_id')
												?form_dropdown('campaign_id',$this->campaignmodel->getCampaigns(),'',"class='auto'")
												:form_input(array(
													'name'      => $field['fieldname'],
													'id'        => $field['fieldname']
												))
									);
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked)$formFields[] = array(
								'label'=>'<label for="custom_'.$field['fieldid'].'">'.$field['customlabel'].' : </label>',
								'field'=>form_input(array(
													'name'      => 'custom['.$field['fieldid'].']'
													)));
			}
		}
		$data['downlink'] = $dlink;	
		$data['links'] = $links;
		$data['form'] = array(
					'open'=>form_open_multipart(site_url('campaign/campaignReport/'.$type.'/'),array('name'=>'campReport','class'=>'form','id'=>'campReport','method'=>'post')),
					'form_field'=>$formFields,
					'parentids'=>$parentbids,
					'busid'=>$bid,
					'pid'=>$this->session->userdata('pid'),
					'close'=>form_close(),
					'title'=>$this->lang->line('level_search')
					);
							
		$data['paging'] = $this->pagination->create_links();
		$this->sysconfmodel->data['html']['title'] .= " | ".$this->lang->line('level_report');
		$this->sysconfmodel->viewLayout('list_view',$data);
	}
	function report_edit($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['34']['opt_add']) redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			$res=$this->campaignmodel->report_update($id,$bid);
			redirect('campaign/campaignReport');
		}
		$data['module']['title'] = $this->lang->line('label_manageCampaignReport');
		$fieldset = $this->configmodel->getFields('34');
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('34',$id,'',$bid);
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show'] && !in_array($field['fieldname'],array('status'))){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
						$cf = array('label'=>'<label>'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
										).' : </label>',
									'field'=>($field['fieldname']=="campaign_id")?form_dropdown('campaign_id',$this->campaignmodel->getCampaigns(),isset($itemDetail[$field['fieldname']])? $itemDetail[$field['fieldname']]:'',"class='auto'")
												:(($field['fieldname']=="duration" || $field['fieldname']=="call_time") ? $itemDetail[$field['fieldname']]
												:form_input(array( 
												  'name'      => $field['fieldname'],
												  'id'        => $field['fieldname'],
												  'value'     => isset($itemDetail[$field['fieldname']])? $itemDetail[$field['fieldname']]:''
												))
												)
							);
						array_push($formFields,$cf);
				}
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$cf = array('label'=>'<label>'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,
											isset($itemDetail['custom['.$field['fieldid'].']'])?
											$itemDetail['custom['.$field['fieldid'].']']:'')
						);
					array_push($formFields,$cf);
				}
			}
		}
		$cf = array('label'=>'<label>Click To Connect : </label>',
					'field'=>anchor("Report/clicktoconnect/".$itemDetail['callid']."/6", '<span title="click To Connect" class="fa fa-phone"></span>',array('class'=>'clickToConnect'))
					);
		array_push($formFields,$cf);
		$data['form'] = array(
		            'form_attr'=>array('action'=>'campaign/report_edit/'.$id,'name'=>'editreport'),
					'open'=>form_open_multipart('campaign/report_edit/'.$id,array('name'=>'editreport','id'=>'editreport','class'=>'form','method'=>'post')),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->sysconfmodel->viewLayout('form_view',$data);
	}
	function deleteCampReport($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['34']['opt_delete']) redirect('Employee/access_denied');
		$this->campaignmodel->del_camp_report($id,$bid,"2");
		return 1;
	}
	function undeleteCampReport($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="") ? $cbid : $this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['34']['opt_delete']) redirect('Employee/access_denied');
		$this->campaignmodel->del_camp_report($id,$bid,"1");
		$this->session->set_flashdata('msgt', 'success');
		$this->session->set_flashdata('msg',"Record restored Successfully");
		redirect('campaign/campaignReport/del');
	}
	function reportDetails($id){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['34']['opt_add']) redirect('Employee/access_denied');
		$data['module']['title'] = $this->lang->line('label_campreportDetails');
		$fieldset = $this->configmodel->getFields('34');
		$formFields = array();
		$itemDetail = $this->configmodel->getDetail('34',$id,'',$bid);
		foreach($fieldset as $field){
			$checked=false;
			if($field['type']=='s' && !$field['is_hidden'] && $field['show']){
				foreach($roleDetail['system'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
					if($checked){
						$cf = array('label'=>'<label>'.(($field['customlabel']!="")
												?$field['customlabel']
												:$this->lang->line('mod_'.$field['modid'])->$field['fieldname']
												).' : </label>',
									'field'=>($field['fieldname']=="campaign_id")?$itemDetail['campaign_name']: $itemDetail[$field['fieldname']]
							);
						array_push($formFields,$cf);
				}
			}elseif($field['type']=='c' && $field['show']){
				foreach($roleDetail['custom'] as $ret){if($ret['fieldid']==$field['fieldid'])$checked = true;}
				if($checked){
					$cf = array('label'=>'<label>'.$field['customlabel'].' : </label>',
								'field'=>$this->configmodel->createField($field,
											isset($itemDetail['custom['.$field['fieldid'].']'])?
											$itemDetail['custom['.$field['fieldid'].']']:'')
						);
					array_push($formFields,$cf);
				}
			}
		}
		$data['form'] = array(
					'open'=>form_open_multipart('campaign/reportDetails/'.$id,array('name'=>'reportDetails','id'=>'reportDetails','class'=>'form','method'=>'post')),
					'fields'=>$formFields,
					'close'=>form_close()
				);
		$this->load->view('active_view',$data);
	}
	function campaign_csv($eid=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")?$cbid:$this->session->userdata('bid');
		$data=array('systemfields'=>$this->configmodel->getFields('34',$bid),
					'roleDetail'=>$this->roleDetail,
					'eid'=>($eid!="")?$eid:'',
					'attributes' => array('class' => 'form', 'id' =>'campaigncsv','name'=>'campaigncsv'),
					'URL' => "campaign/campaignReport/"
					);
		$this->load->view('campaignreport_csv',$data);
	}
	function convertlead($campid=''){
		$cbid=$this->session->userdata('cbid');
		$bid=(isset($cbid) && $cbid!="")? $cbid : $this->session->userdata('bid');
		$roleDetail = $this->roleDetail;
		if(!$roleDetail['modules']['34']['opt_add']) redirect('Employee/access_denied');
		if($this->input->post('update_system')){
			$campid = $_POST['campid'];
			$res=$this->campaignmodel->convertaslead($campid);
			if($res == 1){
				$this->session->set_flashdata('msgt', 'success');
				$this->session->set_flashdata('msg',"Campaign Converted as lead successfully");
				redirect('campaign/campaignReport/act');
			}
		}
		$data['module']['title'] = $this->lang->line('label_campconvertlead');
		$formFields[] = array(
				'label'=>'<label for="f">'.$this->lang->line('level_groupname').' : </label>',
				'field'=>form_dropdown('groupId',$this->leadsmodel->getGroups(),'','id="groupId"')
						);
		$formFields[] = array(
				'label'=>'<label for="f">'.$this->lang->line('label_campassign').' : </label>',
					'field'=>form_dropdown('employeeId',$this->groupmodel->employee_list(),'','id="employeeId"')
						);
		$formFields[] = array(
				'label'=>'',
				'field'=>form_input(array(
						'name'      => 'campid',
						'id'        => 'campid',
						'type'		=> 'hidden',
						'value'     => $campid,
						))
						);
		$data['form'] = array(
		    'form_attr'=>array('action'=>'campaign/convertlead/','name'=>'convertlead'),
			//~ 'open'=>form_open_multipart('campaign/convertlead/',array('name'=>'convertlead','class'=>'form','id'=>'convertlead','method'=>'post')),
			'fields'=>$formFields,
			'close'=>form_close(),
			'title'=>$this->lang->line('label_campconvertlead')
			);
		$this->load->view('form_view',$data);
	}
}
/* end of Campaign Module controller */
